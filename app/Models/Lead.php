<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class Lead extends Model
{
    use HasFactory, BelongsToBranch;

    protected $fillable = [
        'description',
        'status',
        'priority',
        'source',
        'follow_up_date',
        'notes',
        'estimated_value',
        'expected_close_date',
        'tags',
        'customer_id',
        'assigned_user_id',
        'branch_id',
        'created_by',
        'lead_number',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'expected_close_date' => 'date',
        'estimated_value' => 'decimal:2',
        'tags' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($lead) {
            if (empty($lead->lead_number)) {
                // Get the next value from the counter table in a transaction-safe way
                $nextNumber = DB::transaction(function () {
                    // Lock the counter row for update
                    $counter = DB::table('lead_number_counter')
                        ->where('id', 1)
                        ->lockForUpdate()
                        ->first();
                    
                    if (!$counter) {
                        // Initialize counter if it doesn't exist
                        $nextId = 1;
                        DB::table('lead_number_counter')->insert(['id' => 1, 'next_number' => 2]);
                    } else {
                        $nextId = $counter->next_number;
                        // Increment the counter for next time
                        DB::table('lead_number_counter')
                            ->where('id', 1)
                            ->update(['next_number' => $nextId + 1]);
                    }
                    
                    return $nextId;
                });
                
                $lead->lead_number = 'LD-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                $lead->title = 'Lead ' . $lead->lead_number; // Set default title using lead number
            }
        });
    }

    /**
     * Get the next available lead number
     */
    public static function getNextLeadNumber(): string
    {
        // Use a transaction to ensure we get the next number safely
        return DB::transaction(function () {
            // Lock the counter row for update
            $counter = DB::table('lead_number_counter')
                ->where('id', 1)
                ->lockForUpdate()
                ->first();
            
            if (!$counter) {
                // If no counter exists yet, the next number will be 1
                $nextNumber = 1;
            } else {
                $nextNumber = $counter->next_number;
            }
            
            return 'LD-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function opportunity(): HasOne
    {
        return $this->hasOne(Opportunity::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'related');
    }

    public function convertToOpportunity(array $opportunityData): Opportunity
    {
        $opportunity = $this->opportunity()->create([
            'name' => $opportunityData['name'] ?? $this->title,
            'value' => $opportunityData['value'] ?? $this->estimated_value ?? 0,
            'stage' => 'prospecting',
            'probability' => $opportunityData['probability'] ?? 25,
            'expected_close_date' => $opportunityData['expected_close_date'] ?? $this->expected_close_date,
            'description' => $opportunityData['description'] ?? $this->description,
            'products_services' => $opportunityData['products_services'] ?? null,
            'branch_id' => $this->branch_id,
            'created_by' => auth()->id(),
        ]);

        $this->update(['status' => 'converted']);

        return $opportunity;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'blue',
            'contacted' => 'yellow',
            'qualified' => 'green',
            'converted' => 'purple',
            'lost' => 'red',
            default => 'gray'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->follow_up_date && $this->follow_up_date->isPast() &&
            !in_array($this->status, ['converted', 'lost']);
    }

    public function getDaysUntilFollowUpAttribute(): ?int
    {
        if (!$this->follow_up_date) return null;

        return now()->diffInDays($this->follow_up_date, false);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_user_id', $userId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('follow_up_date', '<', now())
            ->whereNotIn('status', ['converted', 'lost']);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('follow_up_date', today());
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->whereBetween('follow_up_date', [today(), now()->addDays($days)]);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['converted', 'lost']);
    }
}

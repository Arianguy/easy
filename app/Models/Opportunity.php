<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Opportunity extends Model
{
    use HasFactory, BelongsToBranch;

    protected $fillable = [
        'name',
        'value',
        'stage',
        'probability',
        'expected_close_date',
        'actual_close_date',
        'description',
        'close_reason',
        'products_services',
        'lead_id',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'probability' => 'decimal:2',
        'expected_close_date' => 'date',
        'actual_close_date' => 'date',
        'products_services' => 'array',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'related');
    }

    public function markAsWon(string $reason = null): void
    {
        $this->update([
            'stage' => 'won',
            'actual_close_date' => now(),
            'close_reason' => $reason,
            'probability' => 100,
        ]);
    }

    public function markAsLost(string $reason = null): void
    {
        $this->update([
            'stage' => 'lost',
            'actual_close_date' => now(),
            'close_reason' => $reason,
            'probability' => 0,
        ]);
    }

    public function getWeightedValueAttribute(): float
    {
        return ($this->value * $this->probability) / 100;
    }

    public function getStageColorAttribute(): string
    {
        return match ($this->stage) {
            'prospecting' => 'blue',
            'proposal' => 'yellow',
            'negotiation' => 'orange',
            'won' => 'green',
            'lost' => 'red',
            default => 'gray'
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->expected_close_date &&
            $this->expected_close_date->isPast() &&
            !in_array($this->stage, ['won', 'lost']);
    }

    public function getDaysUntilCloseAttribute(): ?int
    {
        if (!$this->expected_close_date) return null;

        return now()->diffInDays($this->expected_close_date, false);
    }

    public function getCustomerAttribute()
    {
        return $this->lead->customer;
    }

    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeWon($query)
    {
        return $query->where('stage', 'won');
    }

    public function scopeLost($query)
    {
        return $query->where('stage', 'lost');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('stage', ['won', 'lost']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('expected_close_date', '<', now())
            ->whereNotIn('stage', ['won', 'lost']);
    }

    public function scopeClosingThisMonth($query)
    {
        return $query->whereBetween('expected_close_date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->whereNotIn('stage', ['won', 'lost']);
    }

    public function scopeHighValue($query, $threshold = 10000)
    {
        return $query->where('value', '>=', $threshold);
    }

    public function scopeHighProbability($query, $threshold = 75)
    {
        return $query->where('probability', '>=', $threshold);
    }
}

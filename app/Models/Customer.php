<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Customer extends Model
{
    use HasFactory, BelongsToBranch;

    protected $fillable = [
        'name',
        'phone',
        'mobile',
        'email',
        'interests',
        'address',
        'company',
        'budget_range',
        'source',
        'status',
        'last_contact_date',
        'notes',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'budget_range' => 'decimal:2',
        'last_contact_date' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'related');
    }

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getPrimaryContactAttribute(): string
    {
        return $this->mobile ?: $this->phone ?: $this->email ?: 'No contact';
    }

    public function getLastActivityAttribute()
    {
        return $this->activities()->latest()->first();
    }

    public function getTotalLeadsAttribute()
    {
        return $this->leads()->count();
    }

    public function getActiveLeadsAttribute()
    {
        return $this->leads()->whereIn('status', ['new', 'contacted', 'qualified'])->count();
    }

    public function getConvertedLeadsAttribute()
    {
        return $this->leads()->where('status', 'converted')->count();
    }

    public function getTotalOpportunitiesValueAttribute()
    {
        return $this->leads()->with('opportunity')->get()
            ->flatMap->opportunity
            ->sum('value');
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeWithBudgetRange($query, $min = null, $max = null)
    {
        if ($min) {
            $query->where('budget_range', '>=', $min);
        }
        if ($max) {
            $query->where('budget_range', '<=', $max);
        }
        return $query;
    }

    public function scopeRecentlyContacted($query, $days = 30)
    {
        return $query->where('last_contact_date', '>=', now()->subDays($days));
    }
}

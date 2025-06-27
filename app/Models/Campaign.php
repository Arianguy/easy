<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campaign extends Model
{
    use HasFactory, BelongsToBranch;

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'start_date',
        'end_date',
        'budget',
        'actual_cost',
        'target_audience',
        'reached_audience',
        'leads_generated',
        'conversions',
        'metrics',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'metrics' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function getReachRateAttribute(): float
    {
        return $this->target_audience > 0
            ? round(($this->reached_audience / $this->target_audience) * 100, 1)
            : 0;
    }

    public function getConversionRateAttribute(): float
    {
        return $this->leads_generated > 0
            ? round(($this->conversions / $this->leads_generated) * 100, 1)
            : 0;
    }

    public function getCostPerLeadAttribute(): float
    {
        return $this->leads_generated > 0 && $this->actual_cost > 0
            ? round($this->actual_cost / $this->leads_generated, 2)
            : 0;
    }

    public function getCostPerConversionAttribute(): float
    {
        return $this->conversions > 0 && $this->actual_cost > 0
            ? round($this->actual_cost / $this->conversions, 2)
            : 0;
    }

    public function getRoiAttribute(): float
    {
        if (!$this->actual_cost || $this->actual_cost == 0) return 0;

        // Assuming average conversion value - this could be calculated from actual opportunities
        $averageConversionValue = 5000; // This should be configurable
        $totalRevenue = $this->conversions * $averageConversionValue;

        return round((($totalRevenue - $this->actual_cost) / $this->actual_cost) * 100, 1);
    }

    public function getBudgetUtilizationAttribute(): float
    {
        return $this->budget > 0
            ? round(($this->actual_cost / $this->budget) * 100, 1)
            : 0;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'active' => 'green',
            'paused' => 'yellow',
            'completed' => 'blue',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' &&
            $this->start_date <= now() &&
            (!$this->end_date || $this->end_date >= now());
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->end_date || $this->end_date->isPast()) {
            return null;
        }

        return now()->diffInDays($this->end_date, false);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')
            ->orWhere(function ($q) {
                $q->where('end_date', '<', now())
                    ->whereIn('status', ['active', 'paused']);
            });
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeHighPerforming($query, $conversionThreshold = 10)
    {
        return $query->where('conversion_rate', '>=', $conversionThreshold);
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    use HasFactory, BelongsToBranch;

    protected $fillable = [
        'type',
        'subject',
        'description',
        'status',
        'scheduled_at',
        'completed_at',
        'duration_minutes',
        'outcome',
        'related_type',
        'related_id',
        'user_id',
        'branch_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function markAsCompleted(string $outcome = null, int $duration = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'outcome' => $outcome,
            'duration_minutes' => $duration,
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update([
            'status' => 'cancelled',
            'completed_at' => now(),
        ]);
    }

    public function reschedule(\DateTime $newDate): void
    {
        $this->update([
            'scheduled_at' => $newDate,
            'outcome' => 'rescheduled',
        ]);
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'call' => 'blue',
            'email' => 'green',
            'meeting' => 'purple',
            'note' => 'yellow',
            'task' => 'orange',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getOutcomeColorAttribute(): string
    {
        return match ($this->outcome) {
            'successful' => 'green',
            'unsuccessful' => 'red',
            'rescheduled' => 'yellow',
            'no_response' => 'gray',
            default => 'gray'
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->scheduled_at &&
            $this->scheduled_at->isPast() &&
            $this->status === 'pending';
    }

    public function getDurationFormattedAttribute(): string
    {
        if (!$this->duration_minutes) return 'N/A';

        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByOutcome($query, $outcome)
    {
        return $query->where('outcome', $outcome);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_at', '<', now())
            ->where('status', 'pending');
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('scheduled_at', today())
            ->where('status', 'pending');
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->whereBetween('scheduled_at', [now(), now()->addDays($days)])
            ->where('status', 'pending');
    }

    public function scopeForRelated($query, $relatedType, $relatedId)
    {
        return $query->where('related_type', $relatedType)
            ->where('related_id', $relatedId);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('outcome', 'successful');
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('scheduled_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('scheduled_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }
}

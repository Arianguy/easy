<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\HasDatabaseNotifications;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasDatabaseNotifications, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'phone',
        'designation',
        'is_active',
        'last_login_at',
        'email_reminders',
        'lead_reminders',
        'activity_reminders',
        'overdue_notifications',
        'upcoming_notifications',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'email_reminders' => 'boolean',
            'lead_reminders' => 'boolean',
            'activity_reminders' => 'boolean',
            'overdue_notifications' => 'boolean',
            'upcoming_notifications' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'created_by');
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_user_id');
    }

    public function createdLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'created_by');
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class, 'created_by');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'created_by');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'related');
    }

    public function userActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'user_id');
    }

    public function isAreaManager(): bool
    {
        return $this->hasRole('Area Manager');
    }

    public function isSalesManager(): bool
    {
        return $this->hasRole('Sales Manager');
    }

    public function isSalesExecutive(): bool
    {
        return $this->hasRole('Sales Executive');
    }

    public function canManageAllBranches(): bool
    {
        return $this->isAreaManager();
    }

    public function getTotalLeadsAttribute()
    {
        return $this->assignedLeads()->count();
    }

    public function getConvertedLeadsAttribute()
    {
        return $this->assignedLeads()->where('status', 'converted')->count();
    }

    public function getConversionRateAttribute(): float
    {
        $totalLeads = $this->assignedLeads()->count();
        $convertedLeads = $this->assignedLeads()->where('status', 'converted')->count();

        return $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get notification preferences for this user
     */
    public function getNotificationPreferences(): array
    {
        return [
            'email_reminders' => $this->email_reminders ?? true,
            'lead_reminders' => $this->lead_reminders ?? true,
            'activity_reminders' => $this->activity_reminders ?? true,
            'overdue_notifications' => $this->overdue_notifications ?? true,
            'upcoming_notifications' => $this->upcoming_notifications ?? true,
        ];
    }

    /**
     * Check if user wants to receive email reminders
     */
    public function wantsEmailReminders(): bool
    {
        return $this->email_reminders ?? true;
    }

    /**
     * Check if user wants to receive lead reminders
     */
    public function wantsLeadReminders(): bool
    {
        return $this->lead_reminders ?? true;
    }

    /**
     * Check if user wants to receive activity reminders
     */
    public function wantsActivityReminders(): bool
    {
        return $this->activity_reminders ?? true;
    }

    /**
     * Check if user wants to receive overdue notifications
     */
    public function wantsOverdueNotifications(): bool
    {
        return $this->overdue_notifications ?? true;
    }

    /**
     * Check if user wants to receive upcoming notifications
     */
    public function wantsUpcomingNotifications(): bool
    {
        return $this->upcoming_notifications ?? true;
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail($notification): array|string|null
    {
        // Only send email notifications if user has email reminders enabled
        if (!$this->wantsEmailReminders()) {
            return null;
        }

        return $this->email;
    }
}

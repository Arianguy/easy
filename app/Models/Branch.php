<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location', // legacy field
        'code',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'phone_no', // legacy field
        'email',
        'manager_name', // legacy field
        'is_active', // legacy field
        'status',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'status' => 'string',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getTotalRevenueAttribute()
    {
        return $this->opportunities()->where('stage', 'won')->sum('value');
    }

    public function getActiveLeadsCountAttribute()
    {
        return $this->leads()->whereIn('status', ['new', 'contacted', 'qualified'])->count();
    }

    public function getConversionRateAttribute()
    {
        $totalLeads = $this->leads()->count();
        $convertedLeads = $this->leads()->where('status', 'converted')->count();

        return $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;
    }
}

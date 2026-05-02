<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'category',
        'note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all maintenance schedules for this asset
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(AssetSchedule::class);
    }

    /**
     * Get all maintenance histories for this asset
     */
    public function histories(): HasMany
    {
        return $this->hasMany(AssetHistory::class);
    }

    /**
     * Get next upcoming maintenance
     */
    public function getNextMaintenanceAttribute()
    {
        return $this->schedules()
            ->orderBy('next_due_at')
            ->first();
    }

    /**
     * Get overdue schedules
     */
    public function getOverdueSchedules()
    {
        return $this->schedules()
            ->where('next_due_at', '<', now())
            ->get();
    }

    /**
     * Get total cost from maintenance history
     */
    public function getTotalMaintenanceCostAttribute()
    {
        return $this->histories()
            ->whereNotNull('cost')
            ->sum('cost');
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetSchedule extends Model
{
    protected $fillable = [
        'asset_id',
        'title',
        'repeat_type',
        'repeat_interval',
        'last_done_at',
        'next_due_at',
        'reminder_before_days',
    ];

    protected $casts = [
        'last_done_at' => 'datetime',
        'next_due_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the asset this schedule belongs to
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get all maintenance history records for this schedule
     */
    public function histories(): HasMany
    {
        return $this->hasMany(AssetHistory::class, 'schedule_id');
    }

    /**
     * Check if schedule is overdue
     */
    public function isOverdue(): bool
    {
        return $this->next_due_at <= now();
    }

    /**
     * Check if reminder should be sent
     */
    public function shouldRemind(): bool
    {
        $reminderDays = $this->reminder_before_days ?? 1;
        $reminderTime = $this->next_due_at->copy()->subDays($reminderDays);
        
        return now()->between($reminderTime, $this->next_due_at);
    }

    /**
     * Calculate next due date based on repeat type
     */
    public function calculateNextDueDate(Carbon $fromDate = null): Carbon
    {
        $fromDate = $fromDate ?? now();

        return match($this->repeat_type) {
            'daily'   => $fromDate->copy()->addDays($this->repeat_interval),
            'weekly'  => $fromDate->copy()->addWeeks($this->repeat_interval),
            'monthly' => $fromDate->copy()->addMonths($this->repeat_interval),
            'yearly'  => $fromDate->copy()->addYears($this->repeat_interval),
            default   => $fromDate,
        };
    }

    /**
     * Mark as completed and update next schedule
     */
    public function markComplete(Carbon|string|null $completedAt = null): void
    {
        if (is_string($completedAt)) {
            $completedAt = Carbon::parse($completedAt);
        }

        $completedAt = $completedAt ?? now();
        
        $this->update([
            'last_done_at'  => $completedAt,
            'next_due_at'   => $this->calculateNextDueDate($completedAt),
        ]);
    }
}

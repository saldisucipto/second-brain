<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetHistory extends Model
{
    protected $fillable = [
        'asset_id',
        'schedule_id',
        'title',
        'description',
        'cost',
        'done_at',
    ];

    protected $casts = [
        'done_at' => 'datetime',
        'cost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the asset this history belongs to
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the schedule this history is related to
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(AssetSchedule::class, 'schedule_id');
    }
}

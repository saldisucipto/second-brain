<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUpAttachment extends Model
{
    protected $fillable = [
        'follow_up_id',
        'file_path',
        'file_name',
    ];

    public function followUp()
    {
        return $this->belongsTo(FollowUp::class);
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . ltrim($this->file_path, '/'));
    }
}

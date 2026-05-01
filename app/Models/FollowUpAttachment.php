<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        return Storage::disk('public')->url($this->file_path);
    }
}

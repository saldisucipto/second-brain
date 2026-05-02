<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mom extends Model
{
    protected $fillable = [
        'title',
        'description',
        'meeting_date',
        'location',
        'created_by',
        'task_group_id',
        'status',
    ];

    protected $casts = [
        'meeting_date' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(MomItem::class);
    }

    public function attachments()
    {
        return $this->hasMany(MomAttachment::class);
    }

    public function taskGroup()
    {
        return $this->belongsTo(TaskGroup::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

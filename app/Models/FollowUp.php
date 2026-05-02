<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    protected $fillable = [
        'task_id',
        'title',
        'note',
        'description',
        'reminder_at',
        'next_follow_up_at',
        'due_at',
        'status'
    ];

    protected $casts = [
        'reminder_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'due_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function attachments()
    {
        return $this->hasMany(FollowUpAttachment::class);
    }
}

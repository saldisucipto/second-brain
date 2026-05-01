<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'task_group_id',
        'description',
        'source',
        'status',
        'priority',
        'due_date',
        'is_recurring',
        'repeat_type',
        'repeat_interval',
        'last_generated_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_recurring' => 'boolean',
        'repeat_interval' => 'integer',
        'last_generated_at' => 'datetime',
    ];

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function group()
    {
        return $this->belongsTo(TaskGroup::class, 'task_group_id');
    }

    public function logs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}

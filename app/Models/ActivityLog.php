<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    //

    protected $guarded = [];
}

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

class MomItem extends Model
{
    protected $fillable = [
        'mom_id',
        'action',
        'pic',
        'status',
        'due_date',
        'note',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function mom()
    {
        return $this->belongsTo(Mom::class);
    }
}

class MomAttachment extends Model
{
    protected $fillable = [
        'mom_id',
        'file_path',
        'file_name',
    ];

    public function mom()
    {
        return $this->belongsTo(Mom::class);
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . ltrim($this->file_path, '/'));
    }
}

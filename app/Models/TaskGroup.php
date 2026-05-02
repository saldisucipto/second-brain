<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mom;

class TaskGroup extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function moms()
    {
        return $this->hasMany(Mom::class);
    }
}

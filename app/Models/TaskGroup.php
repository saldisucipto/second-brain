<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}

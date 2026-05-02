<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

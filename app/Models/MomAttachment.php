<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}

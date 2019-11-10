<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name', 'alias',
    ];

    public function memories()
    {
        return $this->belongsToMany('\App\Models\Memory');
    }
}

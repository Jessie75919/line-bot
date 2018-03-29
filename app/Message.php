<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $memory
 */
class Message extends Model
{
    protected $fillable = [
        'keyword',
        'message'
    ];

    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


    public function memory()
    {
        return $this->belongsTo(Memory::class);
    }

}

<?php

namespace App\Models;

;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property int            $id
 * @property \Carbon\Carbon $updated_at
 * @property mixed          $shop
 */
class SaleChannel extends Model
{
    protected $fillable = [
        'name',
        'shop_id'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}

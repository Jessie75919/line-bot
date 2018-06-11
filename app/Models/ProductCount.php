<?php

namespace App\Models;

;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property int            $id
 * @property \Carbon\Carbon $updated_at
 * @property mixed          $product
 */
class ProductCount extends Model
{
    protected $fillable = [
        'product_id',
        'sales_channel_id',
        'count'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

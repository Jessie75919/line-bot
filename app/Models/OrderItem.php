<?php

namespace App\Models;

;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property int            $id
 * @property \Carbon\Carbon $updated_at
 * @property mixed          $order
 * @property mixed          $product
 */
class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'count',
        'sub_total',
        'price'
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id','product_id');
    }
}

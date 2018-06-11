<?php

namespace App\Models;

;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int            $id
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 * @property mixed          $order_items
 */
class Order extends Model
{
    protected $fillable = [
        'shop_id',
        'total',
        'order_time',
        'sales_channel_id'
    ];


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}

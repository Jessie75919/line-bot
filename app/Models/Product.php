<?php

namespace App\Models;

;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property int            $id
 * @property \Carbon\Carbon $updated_at
 * @property mixed          $product_type
 * @property mixed          $product_count
 * @property mixed          $shop
 * @property mixed          $product_sub_type
 */
class Product extends Model
{
    protected $fillable = [
        'product_type_id',
        'product_sub_type_id',
        'shop_id',
        'name',
        'price',
        'image',
        'description',
        'order',
        'is_launch',
        'is_sold_out',
        'is_hottest',
    ];


    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }


    public function productSubType()
    {
        return $this->belongsTo(ProductSubType::class);
    }


    public function productCount()
    {
        return $this->hasOne(ProductCount::class);
    }


    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }


}

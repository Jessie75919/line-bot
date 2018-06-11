<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property int            $id
 * @property \Carbon\Carbon $updated_at
 * @property mixed          $products
 * @property mixed          $shop
 * @property mixed          $product_sub_type
 */
class ProductType extends Model
{
    protected $fillable = [
        'name',
        'shop_id',
        'order'
    ];


    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productSubType()
    {
        return $this->hasMany(ProductSubType::class);
    }


    public function shop()
    {
        return $this->belongsTo(Shop::class);

    }
}

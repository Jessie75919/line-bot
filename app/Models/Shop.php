<?php

namespace App\Models;

;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property int            $id
 * @property \Carbon\Carbon $updated_at
 * @property mixed          $products
 * @property mixed          $product_types
 * @property mixed          $sale_channels
 * @property mixed          $users
 * @property mixed          $tags
 */
class Shop extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'line_channel'
    ];


    public function products()
    {
        return $this->hasMany(Product::class);
    }


    public function productTypes()
    {
        return $this->hasMany(ProductType::class);
    }


    public function saleChannels()
    {
        return $this->hasMany(SaleChannel::class);
    }


    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }
}

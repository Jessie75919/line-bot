<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property int            $id
 * @property \Carbon\Carbon $updated_at
 * @property mixed          $merchandise
 * @property mixed          $product_type
 */
class ProductSubType extends Model
{
    protected $fillable = [
        'name',
        'shop_id',
        'product_type_id',
        'order'
    ];


    public function products()
    {
        return $this->hasMany(Product::class);
    }


    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

}

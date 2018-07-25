<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed          $product
 * @property \Carbon\Carbon $created_at
 * @property int            $id
 * @property \Carbon\Carbon $updated_at
 */
class ProductImage extends Model
{

    protected $fillable = [
        'file_name',
        'category',
        'status',
        'product_id',
        'image_url',
        'order'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

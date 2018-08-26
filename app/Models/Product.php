<?php

namespace App\Models;

;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property int            $id
 * @property \Carbon\Carbon $updated_at
 * @property mixed          $product_type
 * @property mixed          $product_count
 * @property mixed          $shop
 * @property mixed          $product_sub_type
 * @property mixed          $tags
 * @property mixed          $order_item
 * @property mixed          $product_images
 * @mixin Eloquent
 */
class Product extends Model
{
    protected $fillable = [
        'product_type_id',
        'product_sub_type_id',
        'shop_id',
        'name',
        'price',
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


    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }


    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }


    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }


    public function pathUrl()
    {
        return "/product/content/{$this->id}";
    }


    public function thumbnailUrl($category)
    {
        $image = $this->productImages
            ->where('type', $category)
            ->sortBy('order')
            ->first();

        return isset($image) ? $image->image_url : "";
    }


    public function imagesUrl($category)
    {
        return $this->productImages
            ->where('type', $category)
            ->sortBy('order')
            ->pluck('image_url');
    }


    public function getImages($category)
    {
        return $this->productImages
            ->where('type', $category)
            ->sortBy('order');
    }


}

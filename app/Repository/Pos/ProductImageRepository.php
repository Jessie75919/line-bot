<?php


namespace App\Repository\Pos;


use App\Models\ProductImage;

class ProductImageRepository
{
    public function create($productId, $fileName, $category, $link, $status, $order):ProductImage
    {
        return ProductImage::create([
            'product_id' => $productId,
            'file_name'  => $fileName,
            'category'   => $category,
            'image_url'  => $link,
            'status'     => $status,
            'order'      => $order
        ]);
    }


    public function lastOrder($productId):int
    {
        return ProductImage::where('product_id', $productId)->pluck('order')->max();
    }
}
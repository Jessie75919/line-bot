<?php


namespace App\Repository\Pos;


use App\Models\ProductImage;
use function is_null;

class ProductImageRepository
{
    public function create($productId, $fileName, $category, $link, $status, $order): ProductImage
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


    public function lastOrder($productId): int
    {
        $lastOrder = ProductImage::where('product_id', $productId)->pluck('order')->max();
        return is_null($lastOrder) ? 0 : $lastOrder;
    }
}
<?php


namespace App\Repository\Pos;


use App\Models\ProductCount;

class ProductCountRepository
{
    public static function create($productId, $saleChannelId, $count)
    {
        return ProductCount::create([
            'product_id' => $productId,
            'sales_channel_id' => $saleChannelId,
            'count' => (int)$count
        ]);
    }


    public static function updateCountByProductId($productId, $quantity)
    {
        ProductCount::where('product_id', $productId)
                    ->update(['count' => $quantity]);
    }
}
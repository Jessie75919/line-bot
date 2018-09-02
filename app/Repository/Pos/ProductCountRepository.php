<?php


namespace App\Repository\Pos;


use App\Models\ProductCount;

class ProductCountRepository extends BaseRepository
{
    const entity = ProductCount::class;

    public static function createProductCount($productId, $saleChannelId, $count)
    {
        return self::create(self::entity, [
            'product_id'       => $productId,
            'sales_channel_id' => $saleChannelId,
            'count'            => (int)$count
        ]);
    }


    public static function updateCountByProductId($productId, $quantity)
    {
        return self::updateColumnById(self::entity, 'count', $productId, $quantity);
    }
}
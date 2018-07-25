<?php


namespace App\Repository\Pos;


use App\Models\ProductSubType;

class ProductSubTypeRepository extends BaseRepository
{
    const entity = ProductSubType::class;

    public static function getProductSubTypesByShopId($shopId)
    {
        return self::getCollectionByColName(self::entity, 'shop_id', $shopId);
    }
}
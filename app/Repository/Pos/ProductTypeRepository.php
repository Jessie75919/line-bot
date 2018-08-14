<?php

namespace App\Repository\Pos;

use App\Models\ProductType;

class ProductTypeRepository extends BaseRepository
{
    const entity = ProductType::class;


    public static function getProductTypesByShopId($shopId)
    {
        return self::getCollectionByColName(self::entity, 'shop_id', $shopId);
    }

    public static function getPaginationByShopId($shopId, $paginationNumber)
    {
        return self::getPaginationByColName(self::entity, 'shop_id', $shopId, $paginationNumber);
    }
}
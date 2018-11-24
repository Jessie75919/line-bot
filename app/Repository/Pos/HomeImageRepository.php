<?php


namespace App\Repository\Pos;


use App\Models\HomeImage;

class HomeImageRepository extends BaseRepository
{

    const entity = HomeImage::class;

    public static function getPaginationByShopId($shopId, $paginationNumber)
    {
        return self::getPaginationByColName(self::entity, 'shop_id', $shopId, $paginationNumber);
    }

    public static function getPaginationByShopIdWithSearchQuery($shopId, $paginationNumber, $queryCondition)
    {
        return self::getPaginationWithShopIdByCondition(self::entity, $shopId, $paginationNumber, $queryCondition);
    }

}
<?php

namespace App\Repository\Pos;

use App\Models\ProductType;

/**
 * Class ProductTypeRepository
 * @package App\Repository\Pos
 */
class ProductTypeRepository extends BaseRepository
{

    const entity = ProductType::class;


    /**
     * @param $shopId
     * @return mixed
     */
    public static function getProductTypesByShopId($shopId)
    {
        return self::getCollectionByColName(self::entity, 'shop_id', $shopId);
    }


    /**
     * @param $shopId
     * @param $paginationNumber
     * @return mixed
     */
    public static function getPaginationByShopId($shopId, $paginationNumber)
    {
        return self::getPaginationByColName(self::entity, 'shop_id', $shopId, $paginationNumber);
    }


    /**
     * @param $shopId
     * @return mixed
     */
    public static function getDefaultProductTypesByShopId($shopId)
    {
        return ProductType::where('shop_id', $shopId)
                   ->where('name', '其他商品')
                   ->first();
    }


    /**
     * @param int $productTypeId
     * @return mixed
     */
    public static function getProductTypeById(int $productTypeId)
    {
        return ProductType::find($productTypeId);
    }


    public static function updateProductTypeStatusById($id, $updateVal)
    {
        return self::updateColumnById(self::entity, 'is_launch', $id, $updateVal);
    }


    public static function updateProductTypeOrderById($id, $updateVal)
    {
        return self::updateColumnById(self::entity, 'order', $id, $updateVal);
    }


}
<?php

namespace App\Repository\Pos;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository
{

    const entity = Product::class;


    public static function getProductsByShopId($shopId): Collection
    {
        return self::getCollectionByColName(self::entity, 'shop_id', $shopId);
    }


    public static function getProductsByProductType($productTypeId): Collection
    {
        return self::getCollectionByColName(self::entity, 'product_type_id', $productTypeId);
    }


    public static function getProductsByProductSubType($productSubTypeId): Collection
    {
        return self::getCollectionByColName(self::entity, 'product_sub_type_id', $productSubTypeId);
    }


    public static function getPaginationByShopId($shopId, $paginationNumber)
    {
        return self::getPaginationByColName(self::entity, 'shop_id', $shopId, $paginationNumber);
    }


    public static function getProductById($id)
    {
        return self::getInstanceById(self::entity, $id);
    }


    public static function getPaginationByShopIdWithSearchQuery($shopId, $paginationNumber, $queryCondition)
    {
        return self::getPaginationWithShopIdByCondition(self::entity, $shopId, $paginationNumber, $queryCondition);
    }


    public static function updateProductStatusById($id, $updateVal)
    {
        return self::updateColumnById(self::entity, 'is_launch', $id, $updateVal);
    }


    public static function updateProductOrderById($id, $updateVal)
    {
        return self::updateColumnById(self::entity, 'order', $id, $updateVal);
    }


    public static function deleteProductById($id)
    {
        return self::deleteById(self::entity, $id);
    }


    public static function isTagExist($productId, $tagId)
    {
        $relationShip =
            DB::table('product_tag')
              ->where('product_id', $productId)
              ->where('tag_id', $tagId)
              ->first();

        return isset($relationShip);
    }


}
<?php


namespace App\Repository\Pos;


use Doctrine\DBAL\Query\QueryBuilder;

class BaseRepository
{

    protected static function getCollectionByColName($entity, $colName, $id)
    {
        return $entity::where($colName, $id)
                      ->orderBy('order', 'asc')
                      ->orderBy('updated_at', 'desc')
                      ->get();
    }


    protected static function getPaginationByColName($entity, $colName, $id, $paginationNumber)
    {
        return $entity::where($colName, $id)
                      ->orderBy('order', 'asc')
                      ->orderBy('updated_at', 'desc')
                      ->paginate($paginationNumber);
    }


    public static function updateColumnById($entity, $colName, $id, $updateVal)
    {
        return $entity::find($id)
                      ->update([$colName => $updateVal]);
    }


    protected static function deleteById($entity, $id)
    {
        return $entity::destroy($id);
    }


    public static function getInstanceById($entity, $id)
    {
        return $entity::find($id);
    }


    public static function create($entity, $data)
    {
        return $entity::create($data);
    }


    public static function updateById($entity, $id, $data)
    {
        return $entity::where('id', $id)->update($data);
    }


    protected static function getPaginationWithShopIdByCondition($entity, $shopId, $paginationNumber, $queryCondition)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $entity::where('shop_id', $shopId);

        foreach ($queryCondition as $key => $value) {
            if ($value == "*") {
                continue;
            }

            if ($key === 'keyword') {
                $queryBuilder->where("name", "like", "%{$value}%");
            } else {
                $queryBuilder->where($key, $value);
            }
        }

        return $queryBuilder
            ->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate($paginationNumber);

    }
}
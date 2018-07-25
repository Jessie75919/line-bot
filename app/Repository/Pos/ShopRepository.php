<?php

namespace App\Repository\Pos;

use App\Models\Shop;

class ShopRepository
{
    public static function find(int $shopId): Shop
    {
        return Shop::find($shopId);
    }
}
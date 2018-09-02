<?php

namespace App\Repository\Pos;

use App\Models\Shop;

class ShopRepository
{

    private $shop;


    /**
     * ShopRepository constructor.
     * @param $shop
     */
    public function __construct($shop)
    {
        $this->shop = $shop;
    }


    public static function find(int $shopId): Shop
    {
        return Shop::find($shopId);
    }

    public function saveNotice($notice)
    {
        $this->shop->notice = $notice;
        $this->shop->save();
    }


    public function getNotice()
    {

    }
}
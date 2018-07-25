<?php


namespace App\Services\Pos;


use App\Models\Shop;

class ShopService
{

    private $shop;
    const PRODUCT  = 'product';
    const BLOG     = 'blog';
    const BANNER   = 'banner';
    const CATEGORY = [self::PRODUCT, self::BLOG, self::BANNER];


    /**
     * ShopService constructor.
     * @param $shopId
     * @internal param $id
     */
    public function __construct($shopId)
    {
        $this->shop = Shop::findOrFail($shopId);
    }


    /**
     * @return Shop
     */
    public function getShop()
    {
        return $this->shop;
    }


    /**
     * @return string
     */
    public function getShopSn()
    {
        return $this->shop->shop_sn;
    }
}
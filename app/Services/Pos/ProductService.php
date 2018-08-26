<?php


namespace App\Services\Pos;


use App\Models\Product;
use App\Repository\Pos\ProductImageRepository;
use App\Repository\Pos\ProductRepository;
use const false;
use Illuminate\Support\Facades\App;
use const true;

class ProductService
{
    /**
     * @var Product
     */
    private $product;
    private $ftpStorageService;


    /**
     * ProductService constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product           = $product;
        $shopService             = new ShopService($product->shop->id);
        $this->ftpStorageService = App::make(FTPStorageService::class)
                                      ->setShopService($shopService);
    }


    public function destroyProduct()
    {
        /* delete product_count record */
        $this->product->productCount->delete();

        /* delete product_tag record */
        $this->product->tags()->detach();

        $productImages = $this->product->productImages;
        $isAllImagesDeleted  = false;

        /* delete productImage in DB & Folder */
        if (count($productImages) > 0) {
            foreach ($productImages as $productImage) {
                $isAllImagesDeleted = ProductImageRepository::deleteWithImageFiles($productImage, $this->ftpStorageService);
            }

            /* delete product itself */
//            if ($isAllDeleted) {
            $isProductDeleted = ProductRepository::deleteProductById($this->product->id);
//            }

        } else {
            $isProductDeleted = ProductRepository::deleteProductById($this->product->id);
        }

        if ($isProductDeleted === 1) {
            return true;
        }

        return false;
    }


}
<?php


namespace App\Services\Pos;


use App\Models\ProductType;
use App\Repository\Pos\ProductTypeRepository;
use const false;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use const true;

class ProductTypeService
{

    private $productType;


    /**
     * ProductTypeService constructor.
     * @param $productType
     */
    public function __construct($productType)
    {
        if ($productType instanceof ProductType) {
            $this->productType = $productType;
            return;
        }

        $this->productType = ProductTypeRepository::getProductTypeById($productType);
    }


    public function destroyProductType()
    {
       try {
           DB::beginTransaction();
           if ($this->productType->name === '其他商品') {
               return false;
           }

           $defaultType =
               ProductTypeRepository
                   ::getDefaultProductTypesByShopId($this->productType->shop_id);

           $products = $this->productType->products;

           foreach ($products as $product) {
               $product->product_type_id = $defaultType->id;
               $product->save();
           }

           $this->productType->delete();
           DB::commit();
           return true;

       } catch (\Exception $e) {
           DB::rollBack();
           Log::error($e);
           return false;
       }
    }

    public function productType()
    {
        return $this->productType;
    }

}
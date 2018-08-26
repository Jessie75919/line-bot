<?php


namespace App\Repository\Pos;


use App\Models\ProductImage;
use App\Services\Pos\FTPStorageService;
use App\Services\Pos\ShopService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function is_null;

class ProductImageRepository
{
    public function create($productId, $fileName, $category, $link, $status, $order): ProductImage
    {
        return ProductImage::create([
            'product_id' => $productId,
            'file_name'  => $fileName,
            'type'   => $category,
            'image_url'  => $link,
            'status'     => $status,
            'order'      => $order
        ]);
    }


    public function lastOrder($productId): int
    {
        $lastOrder = ProductImage::where('product_id', $productId)->pluck('order')->max();
        return is_null($lastOrder) ? 0 : $lastOrder;
    }


    public static function deleteWithImageFiles($productImage, FTPStorageService $ftpStorageService)
    {
        try {
            $fileName = $productImage->file_name;

            /** @var ShopService $shopService */
            $shopService = App::makeWith(ShopService::class, ['shopId' => $productImage->shop()->id]);
            $isDelete    = $ftpStorageService
                ->setShopService($shopService)
                ->deleteImageFile($fileName, 'product');

            if ($isDelete) {
                $productImage->delete();
                return true;
            }

        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }
}
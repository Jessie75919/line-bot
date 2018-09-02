<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductImage;
use App\Repository\Pos\ProductImageRepository;
use App\Services\Pos\FTPStorageService;
use App\Services\Pos\ProductImageService;
use App\Services\Pos\ShopService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use function var_dump;

class ProductImageController extends ApiController
{

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request $request
     * @param ProductImageRepository    $productImageRepository
     * @param FTPStorageService         $ftpStorageService
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function storeImages(
        Request $request,
        ProductImageRepository $productImageRepository,
        FTPStorageService $ftpStorageService
    ) {

        $shopId = $request->shopId;
        /** @var ShopService $shopService */
        $shopService = App::makeWith(ShopService::class, ['shopId' => $shopId]);
        $orders      = explode(',', $request->order); // 3,2,1
        var_dump($orders);
        $files         = $request->file;  // baff, logo, jc
        $productId     = (int)$request->productId;
        $startingOrder = $productImageRepository->lastOrder($productId) + 1;


        foreach ($orders as $index => $order) {

            $file = $files[$order];
            $ext  = $file->getClientOriginalExtension();

            $downloadUrl = $ftpStorageService
                ->setShopService($shopService)
                ->setFileExtension($ext)
                ->storeFileAs($file);

            $fileName = $ftpStorageService->getFileName();

            $productImageRepository->createProductImage(
                $productId,
                $fileName,
                ShopService::PRODUCT,
                $downloadUrl,
                1,
                $startingOrder + $index
            );
        }

        return response('images save', 200);
    }


    public function delete(ProductImage $productImage, FTPStorageService $ftpStorageService)
    {

        $id = $productImage->id;

        if (ProductImageRepository::deleteWithImageFiles($productImage, $ftpStorageService)) {
            return $this->respondDeleted("{$id} is deleted");
        } else {
            return $this->setStatusCode(500)
                        ->respondWithError("ProductImage :{$id} Delete Failed!");
        }
    }

}

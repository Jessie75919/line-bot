<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductType;
use App\Repository\Pos\ProductTypeRepository;
use App\Services\Pos\ProductTypeService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApiProductTypeController extends ApiController
{
    public function statusSwitch(ProductType $productType)
    {
        $status = null;

        if ($productType) {
            $status = (int)!($productType->is_launch);
            ProductTypeRepository::updateProductTypeStatusById($productType->id, $status);
        }

        $message = "{$productType->name} => " . $status;
        return $this->respondWithOKMessage($message);
    }


    public function updateOrder(Request $request)
    {
        foreach ($request->data as $item) {
            ProductTypeRepository::updateProductTypeOrderById($item['id'], $item['order']);
        }
        return $this->respondWithOKMessage('ok');
    }


    public function multiDelete(Request $request)
    {
        try {
            DB::beginTransaction();

            foreach ($request->data as $item) {

                $productTypeService = new ProductTypeService($item['id']);
                if (!$productTypeService->destroyProductType()) {
                    DB::rollBack();
                    return $this->respondInternalError("[ERROR] ProductTypeId : {$productTypeService->productType()} failed");
                }
            }

            DB::commit();

            return $this->respondDeleted('ok');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->respondInternalError('Multi delete Failed');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ProductType;
use App\Repository\Pos\ProductTypeRepository;
use App\Services\Pos\ProductTypeService;
use App\Traits\GetShopIdFromUser;
use function compact;
use function dd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use function response;

class ProductTypeController extends Controller
{

    use GetShopIdFromUser;
    private $paginationNumber = 5;


    public function index()
    {
        $shop         = $this->getShop();
        $productTypes = ProductTypeRepository::getPaginationByShopId($shop->id, $this->paginationNumber);

        return view('consoles.products.type.index', compact('productTypes', 'shop'));
    }


    public function destroy(ProductType $productType)
    {
        if ($productType->name === '其他商品') {
            return redirect('/product/type');
        }

        $productTypeService = new ProductTypeService($productType);

        if ($productTypeService->destroyProductType()) {
            return redirect('/product/type');
        }

        return new Exception("Delete ProductType => {$productType->name} Failed");
    }

    public function edit(ProductType $productType)
    {
        $shopId       = $this->getShop('id');
        return view('consoles.products.type.edit', compact('productType','shopId'));
    }

    public function update(ProductType $productType, Request $request)
    {
        try {
            $productType->name = $request->name;
            $productType->save();
            return response()->json('[SUCCESS] Update Successfully', 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json('[ERROR] Update Failed', 500);
        }

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Repository\Pos\ProductImageRepository;
use App\Repository\Pos\ProductRepository;
use App\Repository\Pos\ProductTypeRepository;
use App\Services\Pos\FTPStorageService;
use App\Services\Pos\ShopService;
use App\Traits\GetShopIdFromUser;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use function compact;
use function explode;
use function is_numeric;
use function view;

class ProductConsoleController extends Controller
{
    use GetShopIdFromUser;
    private $paginationNumber = 5;


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shopId = $this->getShopId();
        if (!is_numeric($shopId)) {
            return $shopId;
        }

        /** @var LengthAwarePaginator $products */
        $products     = ProductRepository::getPaginationByShopId($shopId, $this->paginationNumber);
        $productTypes = ProductTypeRepository::getProductTypesByShopId($shopId);


        return view('consoles.products.index', compact('products', 'productTypes'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('consoles.products.create');
    }


    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request $request
     * @param ProductImageRepository    $productImageRepository
     * @param FTPStorageService         $ftpStorageService
     * @return \Illuminate\Http\Response
     */
    public function store(
        Request $request,
        ProductImageRepository $productImageRepository,
        FTPStorageService $ftpStorageService
    ) {
        $shopId = $this->getShopId();
        /** @var ShopService $shopService */
        $shopService   = App::makeWith(ShopService::class, ['shopId' => $shopId]);
        $orders        = explode(',', $request->order); // 3,2,1
        $files         = $request->file;  // baff, logo, jc
        $productId     = 1;
        $startingOrder = $productImageRepository->lastOrder($productId) + 1;

        foreach ($orders as $index => $order) {

            $file = $files[$order - 1];
            $ext  = $file->getClientOriginalExtension();

            $downloadUrl = $ftpStorageService
                ->setShopService($shopService)
                ->setFileExtension($ext)
                ->storeFileAs($file);

            $fileName = $ftpStorageService->getFileName();

            $productImageRepository->create(
                $productId,
                $fileName,
                ShopService::PRODUCT,
                $downloadUrl,
                0,
                $startingOrder + $index
            );
        }
    }


    /**
     * Display the specified resource.
     * @param $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Product $product)
    {
        return view('consoles.products.show', compact('product'));
    }


    /**
     * Show the form for editing the specified resource.
     * @param  int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        return view('consoles.products.index');
    }


    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @internal param int $id
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect('/productsConsole');
    }


    public function clone(Product $product)
    {
        $cloneProduct = $product->replicate();
        $cloneProduct->push();

        return redirect('/productsConsole');
    }


    public function search(Request $request)
    {
        $shopId           = $this->getShopId();
        $lastQueryType    = $request->productType;
        $lastQueryStatus  = $request->onlineStatus;
        $lastQuerykeyword = $request->keyword;

        $queryCondition = $this->getQueryCondition($request);

        $products =
            ProductRepository::getPaginationByShopIdWithSearchQuery($shopId, $this->paginationNumber, $queryCondition);


        $productTypes = ProductTypeRepository::getProductTypesByShopId($shopId);

        $lastQueryString = $this->getQueryString($request);

        return view('consoles.products.index',
            compact(
                'products',
                'productTypes',
                'lastQueryType',
                'lastQueryStatus',
                'lastQueryString',
                'lastQuerykeyword'
            ));
    }


    private function getQueryString($request)
    {
        return "keyword={$request->keyword}&onlineStatus={$request->onlineStatus}&productType={$request->productType}";
    }


    private function getQueryCondition($request)
    {
        return [
            'is_launch'       => $request->onlineStatus,
            'product_type_id' => $request->productType,
            'keyword'         => $request->keyword == "" ? '*' : $request->keyword,
        ];
    }
}

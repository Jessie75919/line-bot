<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Repository\Pos\ProductCountRepository;
use App\Repository\Pos\ProductImageRepository;
use App\Repository\Pos\ProductRepository;
use App\Repository\Pos\ProductTypeRepository;
use App\Repository\Pos\TagRepository;
use App\Services\Pos\FTPStorageService;
use App\Services\Pos\ProductService;
use App\Traits\GetShopIdFromUser;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use function compact;
use function count;
use function explode;
use function redirect;
use function response;
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
        $shop = $this->getShop();
        if (!$shop) {
            throw new Exception('Not Found Shop From this User');
        }

        /** @var LengthAwarePaginator $products */
        $products     = ProductRepository::getPaginationByShopId($shop->id, $this->paginationNumber);
        $productTypes = $shop->productTypes;


        return view('consoles.products.content.index', compact('products', 'productTypes'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $shopId       = $this->getShop('id');
        $productTypes = ProductTypeRepository::getProductTypesByShopId($shopId);

        return view(
            'consoles.products.content.create',
            compact('productTypes', 'shopId')
        );
    }


    public function store(Request $request)
    {
        $productName     = $request->name;
        $productPrice    = $request->price;
        $productTypeId   = $request->productTypeId;
        $is_launched     = $request->is_launched;
        $quantity        = $request->quantity;
        $ckeditorContent = $request->ckeditorContent;
        $tags            = $request->tags;

        try {
            DB::beginTransaction();

            $product = ProductRepository::createProduct([
                'product_type_id' => $productTypeId,
                'shop_id'         => $this->getShop('id'),
                'name'            => $productName,
                'price'           => $productPrice,
                'order'           => 0,
                'description'     => $ckeditorContent,
                'is_launch'       => (int)$is_launched,
                'is_sold_out'     => 0,
                'is_hottest'      => 0,
            ]);

            $saleChannelId = $product->shop->saleChannels->first()->id;
            ProductCountRepository::createProductCount($product->id, $saleChannelId, $quantity);

            if (count($tags) != 0) {
                foreach ($tags as $tag) {
                    $existedTag = TagRepository::getTagsByName($tag);
                    if (!$existedTag) {
                        $existedTag = TagRepository::saveTag($this->getShop('id'), $tag);
                    }
                    $product->tags()->attach($existedTag->id);
                }
            }
            DB::commit();
            return response()->json(['id' => $product->id], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['id' => $product->id], 200);
        }
    }


    /**
     * Show the form for editing the specified resource.
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Product $product)
    {
        $shopId       = $this->getShop('id');
        $productTypes = ProductTypeRepository::getProductTypesByShopId($shopId);
        return view('consoles.products.content.edit', compact('product', 'productTypes', 'shopId'));
    }


    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request $request
     * @param int                       $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $product)
    {
        try {
            DB::beginTransaction();
            ProductRepository::UpdateProductById($product, [
                'name'            => $request->name,
                'price'           => $request->price,
                'product_type_id' => $request->productTypeId,
                'is_launch'       => (int)$request->is_launch,
                'description'     => $request->ckeditorContent
            ]);

            ProductCountRepository::updateCountByProductId($product, $request->quantity);

            foreach ($request->imgOrder as $img) {
                $id                  = explode('_', $img['id'])[1];
                $productImage        = ProductImage::find($id);
                $productImage->order = $img['order'];
                $productImage->save();
            }

            DB::commit();

        } catch (\Exception $e) {
            print $e;
            Log::error($e);
            DB::rollBack();
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param Product           $product
     * @param FTPStorageService $ftpStorageService
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @internal param int $id
     */
    public function destroy(Product $product, FTPStorageService $ftpStorageService)
    {
        try {
            DB::beginTransaction();
            $productService = new ProductService($product);
            if ($productService->destroyProduct()) {
                DB::commit();
                return redirect('/product/content');
            }

            DB::rollBack();
            throw new Exception("Delete ProductId : $product->id falied");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }


    public function clone(Product $product)
    {
        $cloneProduct = $product->replicate();
        $cloneProduct->push();
        $saleChannel = $product->shop->saleChannels->first();
        ProductCountRepository::createProductCount($cloneProduct->id, $saleChannel->id, 1);

        return redirect('/product/content');
    }


    public function search(Request $request)
    {
        $shopId           = $this->getShop('id');
        $lastQueryType    = $request->productType;
        $lastQueryStatus  = $request->onlineStatus;
        $lastQuerykeyword = $request->keyword;

        $queryCondition = $this->getQueryCondition($request);

        $products =
            ProductRepository::getPaginationByShopIdWithSearchQuery($shopId, $this->paginationNumber, $queryCondition);


        $productTypes = ProductTypeRepository::getProductTypesByShopId($shopId);

        $lastQueryString = $this->getQueryString($request);

        return view('consoles.products.content.index',
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

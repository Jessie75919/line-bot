<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Repository\Pos\ProductRepository;
use App\Services\Pos\ProductService;
use App\Transformers\ProductTransformer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class ApiProductController extends ApiController
{

    /** @var  ProductTransformer */
    private $productTransformer;


    /**
     * ProductsController constructor.
     * @param ProductTransformer $productTransformer
     */
    public function __construct(ProductTransformer $productTransformer)
    {
        $this->productTransformer = $productTransformer;
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $limit = Input::get('limit') ? : 3;
        /** @var  LengthAwarePaginator $products */
        $products = Product::paginate($limit);

        return $this->respondWithPagination($products, [
            $this->productTransformer->transformCollection($products->all())
        ]);
    }


    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    /**
     * @param ProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductRequest $request)
    {
        Product::create($request->all());
        return $this->respondCreated('Product successfully created.');
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->respondNotFound("Product doesn't exist");
        }

        return $this->respond([
            'data' => $this->productTransformer->transform($product)
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     * @param ProductRequest|Request $request
     * @param  int                   $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductRequest $request, $id)
    {
        Product::where('id', $id)->update($request->all());
        return $this->respondUpdated('Product successfully update.');
    }


    /**
     * Remove the specified resource from storage.
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Product::destroy($id);
        return $this->respondDeleted('Product successfully delete.');
    }


    public function updateOrder(Request $request)
    {
        foreach ($request->data as $item) {
            ProductRepository::updateProductOrderById($item['id'], $item['order']);
        }
        return $this->respondWithOKMessage('ok');
    }


    public function multiDelete(Request $request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->data as $item) {
                $product = Product::find($item['id']);

                $productService = new ProductService($product);
                if (!$productService->destroyProduct()) {
                    DB::rollBack();
                    return $this->respondInternalError("ProductId : {$product->id} failed");
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

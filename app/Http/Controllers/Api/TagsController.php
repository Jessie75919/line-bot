<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Shop;
use App\Models\Tag;
use App\Repository\Pos\ProductRepository;
use App\Repository\Pos\TagRepository;
use App\Traits\GetShopIdFromUser;
use App\Transformers\TagTransformer;
use Illuminate\Http\Request;

class TagsController extends ApiController
{
    use GetShopIdFromUser;

    protected $tagTransformer;


    /**
     * TagsController constructor.
     * @param $tagTransformer
     */
    public function __construct(TagTransformer $tagTransformer)
    {
        $this->tagTransformer = $tagTransformer;
    }


    /**
     * Display a listing of the resource.
     * @param Shop $shop
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Shop $shop)
    {
        return $this->respond([
            'data' => $this->tagTransformer->transformCollection($shop->tags->all()),
        ]);
    }


    public function getProductTags(Product $product)
    {
        return $this->respond([
            'data' => $this->tagTransformer->transformCollection($product->tags->all()),
        ]);
    }


    public function store(Shop $shop, Product $product, Request $request)
    {
        $tag    = $request->tag;
        $shopId = $shop->id;

        if (!TagRepository::exist($tag, $shopId)) {

            $tag = TagRepository::saveTag($shopId, $tag);
            $product->tags()->attach($tag->id);
            return $this->respondWithOKMessage("{$tag->name} is created");

        } else {

            $tag = TagRepository::getTagsByName($tag);

            if (!ProductRepository::isTagExist($product->id, $tag->id)) {
                $product->tags()->attach($tag->id);
                return $this->respondWithOKMessage("{$tag->name} is attached");
            } else {
                return $this->respondWithOKMessage("{$tag->name} is already attached, action cancelled ");
            }
        }
    }


    public function detachTag(Product $product, Request $request)
    {
        try {
            $tag = TagRepository::getTagsByName($request->tag);
            $product->tags()->detach($tag->id);
            return $this->respondWithOKMessage("{$tag->name} is Detached from {$product->name}");
        } catch (\Exception $e) {
            Log::error($e);
            return $this->respondWithError("Detach Tag is Failed");
        }
    }


    public function destroy(Tag $tag)
    {
        $tagId = $tag->id;
        $tag->products->each(function ($product) use ($tagId) {
            var_dump($product->tags()->detach($tagId));
        });
        $tag->delete();
        return $this->respondWithOKMessage("tagId : {$tagId} is deleted");
    }


    public function update(Tag $tag, Request $request)
    {

        $tag->update($request->all());
        return $this->respondWithOKMessage("tagId : {$tag->id} is Updated");
    }


    /**
     * @param $productId
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    private function getTags($productId)
    {
        return $productId ? Product::findOrFail($productId)->tags : Tag::all();
    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductImage;
use function dd;
use Illuminate\Http\Request;

class ProductImageController extends ApiController
{
    public function delete(ProductImage $productImage)
    {
        $id = $productImage->id;
        $productImage->delete();
        return $this->respondWithOKMessage("{$id} is deleted");
    }
}

<?php

namespace App\Transformers;

class ProductTransformer extends Transformer
{
    public function transform($product)
    {
        return [
            'name'        => $product['name'],
            'desc'        => $product['description'],
            'price'       => $product['price'],
            'is_sold_out' => (Bool)$product['is_sold_out'],
        ];
    }
}
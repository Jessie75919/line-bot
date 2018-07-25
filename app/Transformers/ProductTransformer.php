<?php

namespace App\Transformers;

class ProductTransformer extends Transformer
{
    public function transform($product)
    {
        return [
            'name'                => $product['name'],
            'description'         => $product['description'],
            'price'               => $product['price'],
            'product_type_id'     => $product['product_type_id'],
            'product_sub_type_id' => $product['product_sub_type_id'],
            'shop_id'             => $product['shop_id'],
            'order'               => $product['order'],
            'is_sold_out'         => (Bool)$product['is_sold_out'],
            'is_launch'           => (Bool)$product['is_launch'],
            'is_hottest'          => (Bool)$product['is_hottest'],
        ];
    }
}
<?php

namespace App\Repository\Pos;


use App\Models\Tag;

class TagRepository
{
    public static function getTagsByShopId($shopId)
    {
        return Tag::where('shop_id', $shopId)->get();
    }


    public static function saveTag($shopId, $tag)
    {
        return Tag::create([
            'shop_id' => $shopId,
            'name'    => $tag
        ]);
    }


    public static function exist($tag, $shopId)
    {
        return Tag::where('shop_id', $shopId)->where('name', $tag)->exists();
    }


    public static function getTagsByName($tagName)
    {
        return Tag::where('name', $tagName)->first();
    }
}
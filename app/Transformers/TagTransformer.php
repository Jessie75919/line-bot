<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/6/28星期四
 * Time: 下午10:10
 */

namespace App\Transformers;


class TagTransformer extends Transformer
{
    public function transform($tag)
    {
        return [
            'name' => $tag['name'],
        ];
    }

}
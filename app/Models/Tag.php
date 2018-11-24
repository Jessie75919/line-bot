<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/6/27星期三
 * Time: 下午11:08
 */

namespace App\Models;


use Eloquent;

/**
 * @property mixed $merchandise
 * @property mixed $shops
 */
class Tag extends Eloquent
{
    protected $fillable = ['name', 'shop_id'];

    public function shops()
    {
        return $this->belongsTo(Shop::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
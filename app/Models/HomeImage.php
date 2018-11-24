<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeImage extends Model
{
    protected $fillable = [
        'shop_id',
        'name',
        'is_launch',
        'file_name',
        'image_url',
        'link',
    ];



    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }

    public function pathUrl()
    {
        return "/homeImage/{$this->id}";
    }

}

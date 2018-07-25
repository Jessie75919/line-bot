<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use const true;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_type_id'     => 'required|integer',
            'product_sub_type_id' => 'required|integer',
            'shop_id'             => 'required|integer',
            'name'                => 'required|string',
            'price'               => 'required|integer',
            'image'               => 'required|url',
            'description'         => 'required|string',
            'order'               => 'required|integer',
            'is_launch'           => 'required',
            'is_sold_out'         => 'required',
            'is_hottest'          => 'required',
        ];
    }
}

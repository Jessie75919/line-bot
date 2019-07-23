<?php

namespace App\Http\Controllers\Foody;

use App\Http\Controllers\Api\ApiController;
use App\Services\LineBot\ActionHandler\LineBotActionFoodNearbySearcher;
use Illuminate\Http\Request;
use Validator;

class FoodyMainController extends ApiController
{
    public function index()
    {
        return view('foody.foody_index');
    }

    public function shops(Request $request)
    {
        $v = Validator::make(
            $request->all(),
            [
                'latitude' => 'required',
                'longitude' => 'required',
            ]
        );

        if ($v->fails()) {
            return $this->errorWrongArgs($v->errors());
        }

        $foodSearch = new LineBotActionFoodNearbySearcher([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        $formatShops = $foodSearch->handle();

        return $this->respondWithArray($formatShops->toArray());
    }
}

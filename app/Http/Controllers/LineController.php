<?php

namespace App\Http\Controllers;

use App\Services\Google\GooglePlaceApiService;
use App\Services\LineBot\LineBotMainService;
use Illuminate\Http\Request;

class LineController extends Controller
{
    /* @var LineBotMainService */
    private $lineMainService;

    /**
     * LineController constructor.
     */
    public function __construct()
    {
        \Log::info('Line Bot Starting .... ');
        $this->lineMainService = app(LineBotMainService::class);
    }

    public function index(Request $request)
    {
        $response = $this->lineMainService->handle($request->all());

        \Log::info(__METHOD__.' => '.print_r($response, true));

        return isset($response) ? response()->json($response) : null;
    }

    public function imagePreview(Request $request)
    {
        $placeApi = (app(GooglePlaceApiService::class));

        $photoRef = $request->ref;

        return response()
            ->make($placeApi->getPhotoRefApi($photoRef))
            ->header("Content-Type", 'image/png');
    }
}

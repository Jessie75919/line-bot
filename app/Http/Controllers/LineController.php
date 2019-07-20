<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LineBot\LineBotMainService;

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

        dd($response);


        \Log::info(__METHOD__ . ' => ' . print_r($response, true));

        return isset($response) ? response()->json($response) : null;
    }
}

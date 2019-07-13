<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LineBot\LineBotMainService;
use function app;

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
        $package = $request->all();

        $finalResponse = $this->lineMainService->handle($package);

        return response()->json($finalResponse);
    }
}

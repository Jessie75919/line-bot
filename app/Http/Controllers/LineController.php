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

        \Log::info(__METHOD__ . ' => ' . print_r($package, true));

        $finalResponse = $this->lineMainService->handle($package);

        return isset($finalResponse) ? response()->json($finalResponse) : null;
    }
}

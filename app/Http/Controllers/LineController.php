<?php

namespace App\Http\Controllers;

use function app;
use App\Memory;
use App\Services\LineBotLearnService;
use App\Services\LineBotReceiveMessageService;
use App\Services\LineBotResponseService;
use function env;
use const false;
use Illuminate\Http\Request;
use LINE\LINEBot;
use const true;

class LineController extends Controller
{
    private $lineBot;
    private $lineUserId;
    private $botReceiveMessageService;


    /**
     * LineController constructor.
     * @internal param $lineUserId
     * @internal param $lineBot
     */
    public function __construct()
    {
        \Log::info('Line Bot Starting .... ');
        $this->lineBot    = app(LINEBot::class);
        $this->botReceiveMessageService = app(LineBotReceiveMessageService::class);
        $this->lineUserId = env('LINE_USER_ID');
    }


    public function index(Request $request)
    {
        $package = $request->json()->all();
        $this->botReceiveMessageService->handle($package);

        $replyToken = $this->botReceiveMessageService->getReplyToken();
        $purpose    = $this->botReceiveMessageService->checkPurpose();
        $response   = $this->botReceiveMessageService->dispatch($purpose);
        
        return $this->lineBot->replyText($replyToken, $response);
    }
}

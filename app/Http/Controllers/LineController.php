<?php

namespace App\Http\Controllers;

use function app;
use App\Memory;
use App\Services\LineBotLearnService;
use App\Services\LineBotReceiveMessageService;
use App\Services\LineBotResponseService;
use function dd;
use function env;
use const false;
use Illuminate\Http\Request;
use LINE\LINEBot;
use Psy\Util\Json;
use const true;

class LineController extends Controller
{
    /** @var LINEBot  */
    private $lineBot;
    private $lineUserId;
    /** @var LineBotReceiveMessageService  */
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
    }


    public function index(Request $request)
    {
//        $package = ($request->all())[0]; // for test

        $package = $request->json()->all();
        $this->botReceiveMessageService->handle($package);
//
        /** @var string $replyToken */
        $replyToken = $this->botReceiveMessageService->getReplyToken();

        /** @var string $purpose */
        $purpose = $this->botReceiveMessageService->checkPurpose();

        $responseText = $this->botReceiveMessageService->dispatch($purpose);

        return $this->lineBot->replyText($replyToken, $responseText);
    }
}

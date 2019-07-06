<?php

namespace App\Http\Controllers;

use App\Services\LineBotReceiveMessageService;
use Illuminate\Http\Request;
use LINE\LINEBot;
use function app;

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
        /** @var LineBotReceiveMessageService botReceiveMessageService */
        $this->botReceiveMessageService = app(LineBotReceiveMessageService::class);
    }


    public function index(Request $request)
    {
//        $package = ($request->all())[0]; // for test
        $package = $request->json()->all();
         \Log::info( __METHOD__ . ' => ' . print_r($package , true));
        return response('ok', 200);

        $this->botReceiveMessageService->handle($package);
//
        /** @var string $replyToken */
        $replyToken = $this->botReceiveMessageService->getReplyToken();

        /** @var string $purpose */
        $purpose = $this->botReceiveMessageService->checkPurpose();

        $responseText = $this->botReceiveMessageService->dispatch($purpose);

        $finalResponse = $this->lineBot->replyText($replyToken, $responseText);

        return response()->json($finalResponse);
    }
}

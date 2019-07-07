<?php

namespace App\Http\Controllers;

use LINE\LINEBot;
use Illuminate\Http\Request;
use App\Services\LineBot\LineBotMessageReceiver;
use function app;

class LineController extends Controller
{
    /** @var LINEBot */
    private $lineBot;
    /** @var LineBotMessageReceiver */
    private $lineBotReceiver;


    /**
     * LineController constructor.
     * @internal param $lineUserId
     * @internal param $lineBot
     */
    public function __construct ()
    {
        \Log::info('Line Bot Starting .... ');
        $this->lineBot         = app(LINEBot::class);
        $this->lineBotReceiver = app(LineBotMessageReceiver::class);
    }


    public function index (Request $request)
    {
        $package = $request->all();

        $handler =
            $this->lineBotReceiver->handle($package)
                                  ->checkPurpose()
                                  ->dispatch();

        $responseText = $handler->handle();

        dd($responseText);


        /** @var string $replyToken */
        $replyToken = $this->lineBotReceiver->getReplyToken();

        $finalResponse = $this->lineBot->replyText($replyToken, $responseText);

        return response()->json($finalResponse);
    }
}

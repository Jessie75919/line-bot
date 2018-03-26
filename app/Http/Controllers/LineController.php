<?php

namespace App\Http\Controllers;

use function app;
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
    private $botResponseService;
    private $botReceiveMessageService;


    /**
     * LineController constructor.
     * @internal param $lineUserId
     * @internal param $lineBot
     */
    public function __construct()
    {
        \Log::info('Line Bot Starting .... ');
        $this->lineBot                  = app(LINEBot::class);
        $this->lineUserId               = env('LINE_USER_ID');
        $this->botResponseService       = new LineBotResponseService();
        $this->botReceiveMessageService = new LineBotReceiveMessageService();
    }


    public function index(Request $request)
    {

        $package    = $request->json()->all();
        $replyToken = $this->botReceiveMessageService->getReplyToken($package);
        $userMsg    = $this->botReceiveMessageService->getUserMessage($package);

        \Log::info('replyToken = '.$replyToken);

        $strArr = explode(';', $userMsg);

        if($this->botResponseService->isShutUp()){

            if($this->botResponseService->isNeedTalk($userMsg)){
                $this->botResponseService->setShutUp(false);
                return $this
                    ->lineBot
                    ->replyText($replyToken, "是你要我講話的喔！就別怪我吵喔～");
            }
            return;
        }

        if($this->botResponseService->isNeedShutUp($userMsg)){
            $this->botResponseService->setShutUp(true);
            return $this
                ->lineBot
                ->replyText($replyToken, "好啦～我閉嘴就是了！");
        }



        // check whether is learn command
        if(!$this->botResponseService->isLearningCommand($strArr[0])) {
            $chuCResp = $this->botResponseService->keywordReply($userMsg);
            $response = $this->lineBot->replyText($replyToken, $chuCResp);
            return $response;
        }




        if($this->botResponseService->learnCommand($strArr[1], $strArr[2]) == true) {
            $response = $this
                ->lineBot
                ->replyText($replyToken, "我已經學習了：$strArr[1] = $strArr[2]囉！！ 試試看吧～");
        } else {
            $response = $this
                ->lineBot
                ->replyText($replyToken, "你不要盡教我一些幹話好不好！");
        }


        if($response->isSucceeded()) {
            return;
        }

        return $response;
    }


}

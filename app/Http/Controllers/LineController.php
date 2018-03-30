<?php

namespace App\Http\Controllers;

use function app;
use App\Memory;
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

    }


    public function index(Request $request)
    {

        $package                        = $request->json()->all();
        $this->botReceiveMessageService = new LineBotReceiveMessageService($package);
        $this->botReceiveMessageService->setUserMessage($package);

        $replyToken = $this->botReceiveMessageService->getReplyToken();
        $userMsg    = $this->botReceiveMessageService->getUserMessage();
        $channelId  = $this->botReceiveMessageService->getChannelId();

        $this->init($channelId);
        $this->botResponseService = new LineBotResponseService($channelId);

        \Log::info('channelId = '.$channelId);
        \Log::info('$userMsg = '. $userMsg);

        $strArr = explode('；', $userMsg);

        if($this->botResponseService->isShutUp()){
            if($this->botResponseService->isNeedTalk($userMsg)){
                $this->botResponseService->setShutUp(0);
                return $this->lineBot->replyText($replyToken, "是你要我講話的喔！就別怪我吵喔～");
            }
            return;
        }

        if($this->botResponseService->isNeedShutUp($userMsg)){
            $this->botResponseService->setShutUp(1);
            return $this->lineBot->replyText($replyToken, "好啦～我閉嘴就是了！");
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


    private function init($channelId)
    {
        $memory = Memory::where('channel_id', $channelId)->first();

        if($memory) {
            return ;
        }

        Memory::create([
            'channel_id' => $channelId ,
            'is_talk'  => 1
        ]);
    }


}

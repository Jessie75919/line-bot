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
    private $botResponseService;
    private $botReceiveMessageService;
    private $botLearnService;


    /**
     * LineController constructor.
     * @internal param $lineUserId
     * @internal param $lineBot
     */
    public function __construct()
    {
        \Log::info('Line Bot Starting .... ');
        $this->lineBot    = app(LINEBot::class);
        $this->lineUserId = env('LINE_USER_ID');
    }


    public function index(Request $request)
    {

        $package                        = $request->json()->all();
        $this->botReceiveMessageService = new LineBotReceiveMessageService($package);

        $replyToken = $this->botReceiveMessageService->getReplyToken();
        $userMsg    = $this->botReceiveMessageService->getUserMessage();
        $channelId  = $this->botReceiveMessageService->getChannelId();

        \Log::info('channelId = '.$channelId);
        \Log::info('$userMsg = '. $userMsg);


        $this->init($channelId);
        $this->botResponseService = new LineBotResponseService($channelId);

        if(!$this->botResponseService->isTalk()){
            if($this->botResponseService->isNeed($userMsg,'talk')){
                $this->botResponseService->setTalk(1);
                return $this->lineBot->replyText($replyToken, "好喔！好喔！");
            }
            return false;
        }

        if($this->botResponseService->isNeed($userMsg,'shutUp')){
            $this->botResponseService->setTalk(0);
            return $this->lineBot->replyText($replyToken, "好喔！好喔！");
        }

        $this->botLearnService = new LineBotLearnService($channelId);
        $learnData             = $this->botLearnService->learning($userMsg);

        // not need to learn => response
        if(!$this->botLearnService->isLearningCommand($learnData[0])) {
            $chuCResponseText = $this->botResponseService->keywordReply($userMsg);
            if(!$chuCResponseText == '') {
                $response = $this->lineBot->replyText($replyToken, $chuCResponseText);
                return $response;
            }
        }


        // need to learn
        if($this->botLearnService->learnCommand($learnData[1], $learnData[2]) == true) {
            $response = $this
                ->lineBot
                ->replyText($replyToken, "好喔！好喔！");
        } else {
            $response = $this
                ->lineBot
                ->replyText($replyToken, "你不要盡教我一些幹話好不好！");
        }

        return $response;
    }


    private function init($channelId)
    {
        $memory = Memory::where('channel_id', $channelId)->first();

        if($memory) { return; }

        Memory::create([
            'channel_id' => $channelId ,
            'is_talk'  => 1
        ]);
    }


}

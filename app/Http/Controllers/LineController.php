<?php

namespace App\Http\Controllers;

use function app;
use App\Message;
use App\Services\LineBotResponseService;
use function env;
use const false;
use Illuminate\Http\Request;
use LINE\LINEBot;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use function print_r;
use const true;

class LineController extends Controller
{

    private $lineBot;
    private $lineUserId;
    private $log;
    private $botResponseService;


    /**
     * LineController constructor.
     * @param $lineUserId
     * @internal param $lineBot
     */
    public function __construct()
    {
        $this->log = new Logger('Chu-C ');
        $this->log->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));
        $this->log->addNotice('Line Bot Starting.....');
        $this->lineBot            = app(LINEBot::class);
        $this->lineUserId         = env('LINE_USER_ID');
        $this->botResponseService = new LineBotResponseService();
    }


    public function index(Request $request)
    {
        $package    = $request->json()->all();
        $replyToken = $this->getReplyToken($package);
        $userMsg    = $this->getUserMessage($package);

        $this->log->addDebug('replyToken : ' . $replyToken);

        $strArr = explode(':', $userMsg);
        // check whether is learn command
        $this->log->addDebug('isLearningCmd= ' . $this->botResponseService->isLearningCommand($strArr[0]));


        if(!$this->botResponseService->isLearningCommand($strArr[0])) {
            $chuCResp = $this->botResponseService->keywordReply($userMsg);
            $response = $this->lineBot->replyText($replyToken, $chuCResp);
            \Log::info('response = '. print_r($response,true));
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


    /**
     * @param $package
     * @return string
     */
    private function getReplyToken($package)
    {
        return $package['events']['0']['replyToken'];
    }


    /**
     * @param $package
     * @return mixed | string
     */
    private function getUserMessage($package)
    {
        // only respond text type message
        if($package['events']['0']['message']){
            $userMsg = $package['events']['0']['message'];
            if($userMsg['type'] === 'text' ) {
                return $userMsg['text'];
            }
        }
    }


//    /**
//     * @param $userMsg
//     * @return string
//     */
//    private function keywordReply($userMsg)
//    {
//        $this->log->addDebug('userMsg : ' . $userMsg);
//        $resp = Message::where('keyword','like', '%' . strtolower($userMsg) .'%')->get();
//
//        $this->log->addDebug('reply message : ' . $resp);
//
//        return count($resp) != 0
//            ? $resp->random()->message
//            : '不要再講幹話好嗎！！';
//    }
//
//
//    /**
//     * @param string $learnWord
//     * @return bool
//     */
//    private function isLearningCommand($learnWord)
//    {
//        return trim($learnWord) == '學' ? true : false;
//    }
//
//
//    /**
//     * @param $key
//     * @param $message
//     * @return bool
//     */
//    private function learnCommand($key, $message)
//    {
//        $this->log->addDebug("key = ". $key);
//        $this->log->addDebug("message = ". $message);
//
//        if(strlen($key) <= 0 && strlen($message) <= 0) {
//            return false;
//        }
//
//        $key     = trim($key);
//        $message = trim($message);
//
//        Message::create([
//            'keyword' => $key,
//            'message' => $message
//        ]);
//
//        return true;
//    }

}

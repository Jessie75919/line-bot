<?php

namespace App\Http\Controllers;

use function app;
use App\Message;
use function collect;
use function config;
use function dd;
use function env;
use function explode;
use const false;
use Illuminate\Http\Request;
use function is_null;
use function is_string;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use function response;
use function strlen;
use function strtolower;
use const true;
use function var_dump;

class LineController extends Controller
{

    private $lineBot;
    private $lineUserId;
    private $log;


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

        $this->lineBot    = app(LINEBot::class);
        $this->lineUserId = env('LINE_USER_ID');
    }


    public function index(Request $request)
    {
        $package    = $request->json()->all();
        $replyToken = $this->getReplyToken($package);
        $userMsg    = $this->getUserMessage($package);

        $this->log->addDebug('replyToken : ' . $replyToken);

        $strArr = explode(' ', $userMsg);
        // check whether is learn command
        if(!$this->isLearningCommand($userMsg[0])){
            $chuCResp = $this->keywordReply($userMsg);
            $response = $this->lineBot->replyText($replyToken, $chuCResp);
        }else{
            $this->learnCommand($userMsg[1], $userMsg[2]);
            $response = $this
                            ->lineBot
                            ->replyText($replyToken, "我已經學習了：$userMsg[1] = $userMsg[2])囉！！ 試試看吧～");
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
        if($package['events']['0']['message']){
            $userMsg = $package['events']['0']['message'];
            if($userMsg['type'] === 'text' ) {
                return $userMsg['text'];
            }
        }
    }


    /**
     * @param $userMsg
     * @return string
     */
    private function keywordReply($userMsg)
    {
        $this->log->addDebug('userMsg : ' . $userMsg);
        $resp = Message::where('keyword', strtolower($userMsg))->first()->message;
        $this->log->addDebug('reply message : ' . $resp);

        return is_null($resp)
            ? '不要再講幹話好嗎！！'
            : $resp;
    }


    /**
     * @param string $learnWord
     * @return bool
     */
    private function isLearningCommand($learnWord)
    {
        return $learnWord === '學'
            ? true
            : false;
    }


    /**
     * @param $key
     * @param $message
     * @return bool
     */
    private function learnCommand($key, $message)
    {
        if(strlen($key) <= 0 && strlen($message) <= 0) {
            return false;
        }

        Message::create([
            'keyword' => $key,
            'message' => $message
        ]);

        return true;
    }

}

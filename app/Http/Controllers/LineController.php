<?php

namespace App\Http\Controllers;

use function app;
use function collect;
use function config;
use function dd;
use function env;
use Illuminate\Http\Request;
use function is_null;
use function is_string;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use function response;
use function strtolower;
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
        $this->log->addDebug('userMsg : ' . $userMsg);

        $chuCResp = $this->keywordReply($userMsg);

        $response = $this->lineBot->replyText($replyToken, $chuCResp);


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
     * @return mixed
     */
    private function keywordReply($userMsg)
    {
        $keyword = collect([
            'fb'    => 'https://www.facebook.com/ChuCHandmade/',
            'ig'    => 'chu.c.handmade',
            '蝦皮'    => 'https://shopee.tw/juicekuo1227',
            '吃屎吧'   => '哩假賽！！！',
            '你好'    => '安安幾歲哪人星座血型喜歡的花語單身否?',
            'hi'    => '安安幾歲哪人星座血型喜歡的花語單身否?',
            'hello' => '安安幾歲哪人星座血型喜歡的花語單身否?',
            'yo'    => '安安幾歲哪人星座血型喜歡的花語單身否?'
        ]);


        return is_null($keyword->get(strtolower($userMsg)))
            ? '請講人話好嗎！！'
            : $keyword->get($userMsg);
    }

}

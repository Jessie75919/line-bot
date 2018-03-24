<?php

namespace App\Http\Controllers;

use function app;
use function config;
use function dd;
use function env;
use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use function response;
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

//        $this->lineBot    = app(LINEBot::class);
//        $this->lineUserId = $lineUserId;
    }


    public function index(Request $request)
    {

        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(env('CHANNEL_TOKEN'));
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => env('CHANNEL_SECRET')]);

        $req = $request->json()->all();
        $replyToken = $req['events']['0']['replyToken'];
        $userMsg = $req['events']['0']['message']['text'];
        $this->log->addDebug('replyToken' . $replyToken);
        $this->log->addDebug('userMsg' . $userMsg);

        $response = $bot->replyText($replyToken, $userMsg);


        if($response->isSucceeded()) {
            return;
        }

//        $httpRequestBody = "abc"; // Request body string //        $hash            = hash_hmac('sha256', $httpRequestBody, env('CHANNEL_SECRET'), true); //        $signature       = base64_encode($hash); //
//        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
//
//        $events = $this->lineBot->parseEventRequest($request->getContent(), $signature[0]);
//
//        foreach($events as $event) {
//            if(!($event instanceof MessageEvent)) {
//                continue;
//            }
//            if(!($event instanceof TextMessage)) {
//                continue;
//            }
//
//            $replyText = $event->getText();
//            \Log::info('Reply test = ' . $replyText);
//            $resp = $this->lineBot->replyText($event->getReplyToken(), $replyText);
//            \Log::info($resp->getHTTPStatus() . ': ' . $resp->getRawBody());
//        }

        return $response;
    }

}

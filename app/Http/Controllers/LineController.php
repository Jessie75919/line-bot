<?php

namespace App\Http\Controllers;

use function app;
use function config;
use function dd;
use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use function response;

class LineController extends Controller
{

    private $lineBot;
    private $lineUserId;


    /**
     * LineController constructor.
     * @param $lineUserId
     * @internal param $lineBot
     */
    public function __construct($lineUserId)
    {
        $this->lineBot    = app(LINEBot::class);
        $this->lineUserId = $lineUserId;
    }


    public function index(Request $request)
    {
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);

        $events = $this->lineBot->parseEventRequest($request->getContent(), $signature[0]);

        foreach($events as $event) {
            if(!($event instanceof MessageEvent)) {
                continue;
            }
            if(!($event instanceof TextMessage)) {
                continue;
            }

            $replyText = $event->getText();
            \Log::info('Reply test = ' . $replyText);
            $resp = $this->lineBot->replyText($event->getReplyToken(), $replyText);
            \Log::info($resp->getHTTPStatus() . ': ' . $resp->getRawBody());
        }

        return response('OK');
    }

}

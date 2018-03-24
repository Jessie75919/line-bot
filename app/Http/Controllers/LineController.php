<?php

namespace App\Http\Controllers;

use function config;
use function dd;
use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class LineController extends Controller
{
    public function index () {
        $httpClient = new CurlHTTPClient(config('app.channel_token'));
        $bot        = new LINEBot($httpClient, ['channelSecret' => config('app.channel_secret')]);

        dd($bot);



        $response = $bot->replyText('<reply token>', 'hello!');

    }
}

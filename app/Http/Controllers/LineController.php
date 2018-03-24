<?php

namespace App\Http\Controllers;

use function config;
use Illuminate\Http\Request;

class LineController extends Controller
{
    public function index () {
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('app.channel_token'));
        $bot        = new \LINE\LINEBot($httpClient, ['channelSecret' => config('app.channel_secret')]);



        $response = $bot->replyText('<reply token>', 'hello!');

    }
}

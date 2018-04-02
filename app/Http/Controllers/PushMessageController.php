<?php

namespace App\Http\Controllers;


use function app;
use App\Memory;
use App\Services\LineBotPushService;
use function dd;
use Illuminate\Http\Request;
use LINE\LINEBot;

class PushMessageController extends Controller
{
    private $lineBot;
    /** @var  LineBotPushService */
    private $lineBotPushService;


    /**
     * PushMessageController constructor.
     */
    public function __construct()
    {
        $this->lineBot = app(LINEBot::class);
        $this->lineBotPushService = app(LineBotPushService::class);

    }


    public function index(Request $request)
    {

        $channelIds = $request->channel_id == ''
                        ? Memory::all('channel_id')
                        : $request->channel_id ;

        $message = $request->message;

        \Log::info('message = '.$message);



        foreach($channelIds as $channelId) {
            \Log::info('channel_id = '.$channelId->channel_id);
            $this->lineBotPushService->pushMessage($channelId->channel_id, $message);
       }
       
       return 200;
    }

}

<?php

namespace App\Http\Controllers;


use function app;
use App\Memory;
use App\Services\LineBotPushService;
use function dd;
use Illuminate\Http\Request;
use LINE\LINEBot;
use function redirect;

class PushMessageController extends Controller
{
    /** @var  LineBotPushService */
    private $lineBotPushService;


    /**
     * PushMessageController constructor.
     */
    public function __construct()
    {
        $this->lineBotPushService = app(LineBotPushService::class);
    }


    public function index(Request $request)
    {

        $channelIds = $request->channel_id == ''
                        ? Memory::all('channel_id')
                        : $request->channel_id ;

        $message = $request->message;

        foreach($channelIds as $channelId) {
            $this->lineBotPushService->pushMessage($channelId->channel_id, $message);
       }
       
       return redirect('pushConsole');
    }

}

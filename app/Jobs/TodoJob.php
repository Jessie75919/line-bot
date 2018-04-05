<?php

namespace App\Jobs;

use App\Services\LineBotPushService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TodoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $channelId;
    private $message;
    /** @var  LineBotPushService */
    private $lineBotPushService;


    /**
     * TodoJob constructor.
     * @param $channelId
     * @param $message
     */
    public function __construct($channelId, $message )
    {
        $this->lineBotPushService = app(LineBotPushService::class);
        $this->channelId = $channelId;
        $this->message   = $message;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info("get the job to send message : {$this->message}");
        $this->lineBotPushService->pushMessage($this->channelId, $this->message);
    }
}

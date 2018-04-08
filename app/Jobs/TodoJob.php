<?php

namespace App\Jobs;

use App\Services\LineBotPushService;
use App\TodoList;
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
    private $todoListId;


    /**
     * TodoJob constructor.
     * @param $channelId
     * @param $message
     * @param $todoListId
     */
    public function __construct($channelId, $message , $todoListId)
    {
        $this->lineBotPushService = app(LineBotPushService::class);
        $this->channelId = $channelId;
        $this->message   = $message;
        $this->todoListId = $todoListId;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info("todoListId => {$this->todoListId}");
        $todo = TodoList::find($this->todoListId);
        if($todo) {
            $todo->delete();
            $this->lineBotPushService->pushMessage($this->channelId, $this->message);
            \Log::info("job to send message : {$this->message} was done");
        }
    }
}

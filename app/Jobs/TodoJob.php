<?php

namespace App\Jobs;

use App\Models\TodoList;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\LineBot\LineBotPushService;

class TodoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $channelId;
    private $message;
    /** @var  LineBotPushService */
    private $lineBotPushService;
    private $todoListId;
    private $repeatPeriod;


    /**
     * TodoJob constructor.
     * @param      $channelId
     * @param      $message
     * @param      $todoListId
     * @param null $repeatPeriod
     */
    public function __construct($channelId, $message, $todoListId, $repeatPeriod = null)
    {
        $this->lineBotPushService = app(LineBotPushService::class);
        $this->channelId = $channelId;
        $this->message = $message;
        $this->todoListId = $todoListId;
        $this->repeatPeriod = $repeatPeriod;
    }


    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        \Log::info("todoListId => {$this->todoListId}");

        $todo = TodoList::find($this->todoListId);
        if ($todo) {
            if ($this->repeatPeriod) {
                $this->setNewQueueJob($todo);
            } else {
                $todo->delete();
            }

            $this->lineBotPushService->pushMessage($this->channelId, $this->message);

            \Log::info("job to send message : {$this->message} was done");
        }
    }


    public function setNewQueueJob(TodoList $todo)
    {
        $delayTime = now('Asia/Taipei');

        switch ($this->repeatPeriod['period']) {
            case 'D':
                $delayTime->addDays($this->repeatPeriod['length']);
                break;
            case 'W':
                $delayTime->addWeeks($this->repeatPeriod['length']);
                break;
            case 'M':
                $delayTime->addMinutes($this->repeatPeriod['length']);
                break;
            default:
                $delayTime->addMinutes(0);
        }

        dispatch(
            new TodoJob(
                $todo->send_channel_id,
                $todo->message,
                $todo->id,
                $this->repeatPeriod
            )
        )->delay($delayTime);
    }
}

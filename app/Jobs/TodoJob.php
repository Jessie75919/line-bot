<?php

namespace App\Jobs;

use App\Models\TodoList;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\LineBot\LineBotPushService;
use App\Services\LineBot\LineBotActionReminder;

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
     */
    public function __construct($channelId, $message, $todoListId)
    {
        $this->lineBotPushService = app(LineBotPushService::class);
        $this->channelId = $channelId;
        $this->message = $message;
        $this->todoListId = $todoListId;
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
            if ($todo->repeat_period) {
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
        $repeatPeriodMethodMap = [
            'D' => 'add' . LineBotActionReminder::PERIOD_MAP['D'] . 's',
            'W' => 'add' . LineBotActionReminder::PERIOD_MAP['W'] . 's',
            "M" => 'add' . LineBotActionReminder::PERIOD_MAP['M'] . 's',
        ];

        $repeatPeriod = json_decode($todo->repeat_period, true);

        $period = $repeatPeriod['period'];
        $length = $repeatPeriod['length'];

        if (! array_key_exists($period, $repeatPeriodMethodMap)) {
            return;
        }

        $method = $repeatPeriodMethodMap[$period];
        $delayTime->$method($length);

        $todo->update(['send_time' => $delayTime]);

        dispatch(
            new TodoJob(
                $todo->send_channel_id,
                $todo->message,
                $todo->id
            )
        )->delay($delayTime);
    }
}

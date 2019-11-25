<?php

namespace App\Console\Commands\Line;

use App\Models\WeightSetting;
use App\Services\LineBot\PushHandler\LineBotPushService;
use Illuminate\Console\Command;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class NotifyForSaveWeightRecord extends Command
{

    protected $signature = 'line:notify-for-save-record';
    protected $description = '提醒記錄體重訊息';

    public function handle(LineBotPushService $lineBotPushService)
    {
        \Log::channel('slack')->info("line:notify-for-save-record starting...");

        $now = now('Asia/Taipei');
        $day = $now->dayOfWeek;
        $timeAt = $now->format('H:i:00');

        \Log::channel('slack')->info("day:{$day} / timeAt:{$timeAt}");

        $weightSettings = WeightSetting::with('memory')
            ->where('enable_notification', 1)
            ->where('notify_days', 'like', "%{$day}%")
            ->where('notify_at', $timeAt)
            ->get();

        foreach ($weightSettings as $weightSetting) {
            $channelId = $weightSetting->memory->channel_id;

            \Log::channel('slack')->info("notification to : $channelId");

            $lineBotPushService->pushMessage(
                $channelId,
                $this->getMessagePanel()
            );
        }

        \Log::channel('slack')->info("line:notify-for-save-record done !");
    }

    public function getMessagePanel()
    {
        $target = new ConfirmTemplateBuilder('😉 記得今天要記錄體重喔！', [
            new UriTemplateActionBuilder('記錄今日體重', config('line.link_of_weight_index').'?page=index'),
            new UriTemplateActionBuilder('回顧七天記錄', config('line.link_of_weight_index').'?page=review'),
        ]);
        return new TemplateMessageBuilder('😉 記得今天要記錄體重喔！', $target);
    }
}

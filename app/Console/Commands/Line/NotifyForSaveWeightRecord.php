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
        $now = now('Asia/Taipei');
        $day = $now->dayOfWeek;
        $timeAt = $now->toTimeString();

        $weightSettings = WeightSetting::with('memory')
            ->where('enable_notification', 1)
            ->where('notify_day', $day)
            ->where('notify_at', $timeAt)
            ->get();

        foreach ($weightSettings as $weightSetting) {
            $channelId = $weightSetting->memory->channel_id;
            $lineBotPushService->pushMessage(
                $channelId,
                $this->getMessagePanel()
            );
        }
    }

    public function getMessagePanel()
    {
        $target = new ConfirmTemplateBuilder('😉 記得今天要記錄體重喔！', [
            new UriTemplateActionBuilder('記錄今日體重', config('line.link_of_weight_input')),
            new UriTemplateActionBuilder('調整目標設定', config('line.link_of_weight_setting')),
        ]);
        return new TemplateMessageBuilder('請查看手機的訊息唷！', $target);
    }
}

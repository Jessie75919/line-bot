<?php

namespace App\Console\Commands\Line;

use App\Models\Line\MealReminder;
use App\Services\LineBot\ActionHandler\Meal\LineBotMealMenu;
use App\Services\LineBot\PushHandler\LineBotPushService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class RemindToSaveMeal extends Command
{
    protected $signature = 'line:notify-for-save-meal';
    protected $description = '提醒記錄飲食';

    public function handle(LineBotPushService $lineBotPushService)
    {
        \Log::channel('meal')->info("line:notify-for-save-meal starting...");
        $now = now('Asia/Taipei');
        $timeAt = $now->format('H:i:00');

        /* @var Collection $mealReminders */
        $mealReminders = MealReminder::where('remind_at', $timeAt)
            ->get();

        if ($mealReminders->isEmpty()) {
            \Log::channel('meal')->info('meal reminders are empty, skipped');
            return null;
        }

        foreach ($mealReminders as $mealReminder) {
            $memory = $mealReminder->memory;
            $channelId = $memory->channel_id;

            \Log::channel('meal')->info("notification to : $channelId");

            $message = app(LineBotMealMenu::class)
                ->startMenu($memory->getProcessStatus())
                ->getMealTypeQuickMenus("🙂️ hi，要記得記錄{$mealReminder->mealType->name}喔！ (請用手機查看訊息)");

            $lineBotPushService->pushMessage($channelId, $message);
        }

        \Log::channel('meal')->info("line:notify-for-save-record done !");
    }
}

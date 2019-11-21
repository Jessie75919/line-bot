<?php

namespace App\Console;

use App\Console\Commands\BodyTemperatureDocsGenerator;
use App\Console\Commands\Line\ExchangeRateWatcherNotification;
use App\Console\Commands\Line\LineBotPushMessage;
use App\Console\Commands\Line\NotifyForSaveWeightRecord;
use App\Console\Commands\Line\NotifyToClockInOut;
use App\Console\Commands\MailTest;
use App\Console\Commands\UrlSpider;
use App\Services\LineExchangeRate\ExchangeRateService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     * @var array
     */
    protected $commands = [
        UrlSpider::class,
        MailTest::class,
        BodyTemperatureDocsGenerator::class,
        LineBotPushMessage::class,
        ExchangeRateWatcherNotification::class,
        NotifyToClockInOut::class,
        NotifyForSaveWeightRecord::class,
    ];

    /**
     * Define the application's command schedule.
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->registerScheduleForExchangeRateWatcher($schedule);
        $this->registerScheduleForNotifyClockInOut($schedule);
        $this->registerScheduleForNotifyWeightRecord($schedule);
    }

    /**
     * Register the commands for the application.
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    private function registerScheduleForExchangeRateWatcher(Schedule $schedule)
    {
        $schedule->command('line:currency-watcher')
            ->dailyAt(ExchangeRateService::NOTIFIED_AT)
            ->timezone('Asia/Taipei');
    }

    private function registerScheduleForNotifyWeightRecord(Schedule $schedule)
    {
        $schedule->command('line:notify-for-save-record')
            ->everyThirtyMinutes()
            ->timezone('Asia/Taipei');
    }

    private function registerScheduleForNotifyClockInOut(Schedule $schedule)
    {
        $schedule->command('line:notify-to-clock-in-out')
            ->weekdays()
            ->dailyAt(9)
            ->timezone('Asia/Taipei');
        $schedule->command('line:notify-to-clock-in-out')
            ->weekdays()
            ->dailyAt('18:30')
            ->timezone('Asia/Taipei');
    }
}

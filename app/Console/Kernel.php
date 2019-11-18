<?php

namespace App\Console;

use App\Console\Commands\BodyTemperatureDocsGenerator;
use App\Console\Commands\Line\ExchangeRateWatcherNotification;
use App\Console\Commands\Line\LineBotPushMessage;
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

    private function registerScheduleForNotifyClockInOut(Schedule $schedule)
    {
        $schedule->command('line:notify-to-clock-in-out')
            ->dailyAt(9)
            ->when(function () {
                return now('Asia/Taipei')->isWeekday();
            })
            ->timezone('Asia/Taipei');
        $schedule->command('line:notify-to-clock-in-out')
            ->dailyAt('18:30')
            ->when(function () {
                return now('Asia/Taipei')->isWeekday();
            })
            ->timezone('Asia/Taipei');
    }
}

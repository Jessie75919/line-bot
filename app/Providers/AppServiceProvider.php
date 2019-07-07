<?php

namespace App\Providers;

use LINE\LINEBot;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Services\LineBot\LineBotPushService;
use App\Services\LineBot\LineBotMessageReceiver;
use function env;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->lineBotRegister();
        $this->lineBotServiceRegister();
        $this->lineBotReceiveMessageRegister();
    }

    private function lineBotRegister()
    {
        $this->app->singleton(LINEBot::class, function() {
            $httpClient = new LINEBot\HTTPClient\CurlHTTPClient(env('CHANNEL_TOKEN'));
            return new LINEBot($httpClient, ['channelSecret' => env('CHANNEL_SECRET')]);
        });
    }


    private function lineBotServiceRegister()
    {
        $this->app->singleton(LineBotPushService::class, function() {
            return new LineBotPushService();
        });
    }


    private function lineBotReceiveMessageRegister()
    {
        $this->app->singleton(LineBotMessageReceiver::class, function() {
            return new LineBotMessageReceiver();
        });
    }

}

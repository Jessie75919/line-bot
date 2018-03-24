<?php

namespace App\Providers;

use function config;
use function env;
use Illuminate\Support\ServiceProvider;
use LINE\LINEBot;
use App\Service\LineBotService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
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
        $this->app->singleton(LineBotService::class, function() {
            return new LineBotService(env('LINE_USER_ID'));
        });

    }
}

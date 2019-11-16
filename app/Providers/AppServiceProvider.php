<?php

namespace App\Providers;

use App\Services\Google\GooglePlaceApiService;
use App\Services\LineBot\LineBotMessageReceiver;
use App\Services\LineBot\PushHandler\LineBotPushService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use LINE\LINEBot;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        $this->lineBotRegister();
        $this->lineBotServiceRegister();
        $this->lineBotReceiveMessageRegister();
        $this->googlePlaceServiceRegister();
    }

    private function lineBotRegister()
    {
        $this->app->singleton(LINEBot::class, function () {
            $httpClient = new LINEBot\HTTPClient\CurlHTTPClient(config('line.channel_token'));
            return new LINEBot($httpClient, ['channelSecret' => config('line.channel_secret')]);
        });
    }

    private function googlePlaceServiceRegister()
    {
        $this->app->singleton(GooglePlaceApiService::class, function () {
            return new GooglePlaceApiService(config('credential.google_place_api_key'));
        });
    }

    private function lineBotServiceRegister()
    {
        $this->app->singleton(LineBotPushService::class, function () {
            return new LineBotPushService();
        });
    }

    private function lineBotReceiveMessageRegister()
    {
        $this->app->singleton(LineBotMessageReceiver::class, function () {
            return new LineBotMessageReceiver();
        });
    }
}

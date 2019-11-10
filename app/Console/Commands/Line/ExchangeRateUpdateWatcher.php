<?php

namespace App\Console\Commands\Line;

use App\Models\Currency;
use App\Services\API\GuzzleApi;
use App\Services\ExchangeRate\ExchangeRateService;
use Illuminate\Console\Command;

class ExchangeRateUpdateWatcher extends Command
{
    protected $signature = 'line:currency-watcher';
    protected $description = 'currency watcher';
    /** @var GuzzleApi */
    private $api;

    public function __construct(GuzzleApi $api)
    {
        parent::__construct();
        $this->api = $api;
    }

    public function handle()
    {
        $currencies = Currency::all();

        foreach ($currencies as $currency) {
            $exRate = (new ExchangeRateService($this->api))
                ->setCurrency($currency)
                ->fetchNowCurrencyValue();

            $currency->memories->each(function ($memory) use ($exRate) {
                //                echo $exRate->toFormatCurrencyReportMessage();
                $exRate->notifyLine($memory->channel_id);
            });
        }
    }
}

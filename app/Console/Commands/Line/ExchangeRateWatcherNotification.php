<?php

namespace App\Console\Commands\Line;

use App\Models\Currency;
use App\Services\API\GuzzleApi;
use App\Services\LineExchangeRate\ExchangeRateService;
use Illuminate\Console\Command;

class ExchangeRateWatcherNotification extends Command
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
            foreach (['cash', 'check'] as $type) {
                $memories = $currency->memories()
                    ->wherePivot('type', $type)
                    ->get();

                if ($memories->isEmpty()) {
                    continue;
                }

                $exRate = (new ExchangeRateService($this->api))
                    ->setCurrency($currency)
                    ->setType($type)
                    ->fetchNowCurrencyValue();

                $memories->each(function ($memory) use ($exRate) {
                    //                    echo $exRate->toFormatCurrencyReportMessage();
                    $exRate->notifyLine($memory->channel_id);
                });
            }
        }
    }
}

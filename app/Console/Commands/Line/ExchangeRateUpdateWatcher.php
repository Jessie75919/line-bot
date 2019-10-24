<?php

namespace App\Console\Commands\Line;

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
        $watchList = [
            ['currency' => 'JPY', 'type' => 'cash', 'lessThan' => 0.28,],
            ['currency' => 'USD', 'type' => 'check', 'lessThan' => 30.65,],
        ];

        foreach ($watchList as $watch) {
            $exRate = (new ExchangeRateService($this->api))
                ->setCurrency($watch['currency'])
                ->setType($watch['type'])
                ->updateCurrent();

            if ($exRate->isLessThan($watch['lessThan'])) {
                $exRate->notifyLine('R421b3280799bcde75de0d6c4ddf91d47');
            }
        }
    }
}

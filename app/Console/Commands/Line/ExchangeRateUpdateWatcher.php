<?php

namespace App\Console\Commands\Line;

use App\Services\ExchangeRate\ExchangeRateService;
use App\Services\LineBot\PushHandler\LineBotPushService;
use Illuminate\Console\Command;

class ExchangeRateUpdateWatcher extends Command
{
    protected $signature = 'line:currency-watcher';
    protected $description = 'currency watcher';
    /**
     * @var ExchangeRateService
     */
    private $exRate;
    /**
     * @var LineBotPushService
     */
    private $lineBotPushService;

    public function __construct(
        ExchangeRateService $exRate,
        LineBotPushService $lineBotPushService
    ) {
        parent::__construct();
        $this->exRate = $exRate;
        $this->lineBotPushService = $lineBotPushService;
    }

    public function handle()
    {
        $watchList = [
            ['currency' => 'JPY', 'type' => 'cash', 'lessThan' => 0.275,],
            ['currency' => 'USD', 'type' => 'check', 'lessThan' => 30.65,],
        ];

        foreach ($watchList as $watch) {
            $this->exRate
                ->setCurrency($watch['currency'])
                ->setType($watch['type'])
                ->updateCurrent()
                ->getLowest();

            if ($this->exRate->isLessThan($watch['lessThan'])) {
                $this->exRate->notifyLine('R421b3280799bcde75de0d6c4ddf91d47');
            }
        }
    }
}

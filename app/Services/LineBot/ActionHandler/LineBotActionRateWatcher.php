<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2019-07-07
 * Time: 10:21
 */

namespace App\Services\LineBot\ActionHandler;

use App\Models\Memory;
use App\Services\API\GuzzleApi;
use App\Services\LineExchangeRate\ExchangeRateService;

class LineBotActionRateWatcher extends LineBotActionHandler
{
    private $payload;
    /**
     * @var ExchangeRateService
     */
    private $exRate;
    private $userInput1;
    private $userInput2;

    /**
     * LineBotActionRateWatcher constructor.
     */
    public function __construct()
    {
        $this->exRate = new ExchangeRateService(new GuzzleApi());
    }

    public function preparePayload($rawPayload)
    {
        $msgArr = $this->breakdownMessage($rawPayload);

        /* 日幣＼美金＼澳幣 */
        $this->userInput1 = $msgArr[1];
        /* 現金＼即期 */
        $this->userInput2 = $msgArr[2] ?? '現金';

        return $this;
    }

    public function handle()
    {
        $memory = Memory::getByChannelId($this->channelId);
        return $this->exRate
            ->subscribe($memory, $this->userInput1, $this->userInput2);
    }
}

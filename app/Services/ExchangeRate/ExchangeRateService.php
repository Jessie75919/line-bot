<?php

namespace App\Services\ExchangeRate;

use App\Models\Currency;
use App\Models\Memory;
use App\Services\API\GuzzleApi;
use App\Services\LineBot\PushHandler\LineBotPushService;
use Illuminate\Support\Collection;

class ExchangeRateService
{
    const NOTIFIED_AT = 11;
    /* @var string */
    protected $currency = 'JPY';
    /* @var string */
    private $type = 'cash';
    /** @var GuzzleApi */
    private $api;
    /* @var Collection */
    private $exRates = null;
    private $lowest = null;

    /**
     * ExchangeRateService constructor.
     * @param  GuzzleApi  $api
     */
    public function __construct(GuzzleApi $api)
    {
        $this->api = $api;
    }

    public function updateCurrent()
    {
        $this->api
            ->setUri("https://tw.rter.info/json.php")
            ->get([
                't' => 'currency',
                'q' => $this->type,
                'iso' => $this->currency->alias,
            ]);

        if ($this->api->isSuccessful()) {
            $this->exRates =
                collect($this->api->getContents()->data)
                    ->map(function ($item) {
                        return [
                            'bank' => strip_tags($item[0]),
                            'buy' => $item[1],
                            'sell' => $item[2],
                            'timestamp' => $item[3],
                            'comment' => $item[4],
                        ];
                    });
        }
        return $this;
    }

    public function getLowest()
    {
        if (empty($this->exRates) ||
            $this->exRates->isEmpty()
        ) {
            return [];
        }

        /* @var Collection $collection */
        $collection = $this->exRates->sortBy('sell');

        $this->lowest = $this->type === 'cash'
            ? $collection->first(function ($value) {
                return $value['comment'] === '免手續費';
            })
            : $collection->first();
        return $this->lowest;
    }

    /**
     * @param  mixed  $currency
     * @return ExchangeRateService
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param $currencyStr
     * @return ExchangeRateService
     * @throws \Exception
     */
    public function setChineseCurrency($currencyStr)
    {
        $this->currency = Currency::where('name', $currencyStr)->first();

        if (! $this->currency) {
            throw new \Exception("Currency Not Found");
        }

        return $this;
    }

    /**
     * @param  string  $type
     * @return ExchangeRateService
     */
    public function setType(string $type): ExchangeRateService
    {
        $this->type = $type;
        return $this;
    }

    public function toFormatCurrencyReportMessage(array $lowest): string
    {
        if (empty($lowest)) {
            return '';
        }

        return "[{$this->getTypeStr()}] 訊息
  - 幣別：{$this->currency->name}
  - 銀行：{$lowest['bank']}
  - 買入：{$lowest['buy']}
  - 賣出：{$lowest['sell']}
  - 時間點：{$lowest['timestamp']}
  - 備註：{$lowest['comment']}
        ";
    }

    public function isLessThan($num): bool
    {
        if (! $this->lowest) {
            $this->getLowest();
        }

        return $num >= $this->lowest['sell'];
    }

    public function notifyLine(string $channelId)
    {
        $lineBotPushService = app(LineBotPushService::class);
        $lineBotPushService->pushMessage(
            $channelId,
            $this->toFormatCurrencyReportMessage($this->lowest)
        );
    }

    public function subscribe(Memory $memory, string $currencyName)
    {
        if (! $this->currency = Currency::where('name', $currencyName)->first()) {
            throw new \Exception("Currency Not Found");
        }
        $memory->currencies()->attach($this->currency->id);
        return $this;
    }

    public function toSubscribeSuccessMessage(): string
    {
        $hour = self::NOTIFIED_AT;
        return <<<EOD
已經爲您開啓了 [{$this->currency->name}] 的訂閱，
會在每天的 {$hour} 點提醒你哦！
EOD;
    }

    private function getTypeStr(): string
    {
        return $this->type === 'cash' ? '現金匯率' : '即期匯率';
    }
}
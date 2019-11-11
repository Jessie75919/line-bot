<?php

namespace App\Services\LineExchangeRate;

use App\Models\Currency;
use App\Models\Memory;
use App\Services\API\GuzzleApi;
use App\Services\LineBot\PushHandler\LineBotPushService;
use Illuminate\Support\Collection;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class ExchangeRateService
{
    const TYPE_MAP = [
        'cash' => '現金',
        'check' => '即期',
    ];

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

    public function fetchNowCurrencyValue()
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
     * @throws NotFoundResourceException
     */
    public function setChineseCurrency($currencyStr)
    {
        if (! $this->currency = Currency::where('name', $currencyStr)->first()) {
            throw new NotFoundResourceException("Currency Not Found");
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

    public function toFormatCurrencyReportMessage(?array $lowest = null): string
    {
        if (empty($lowest)) {
            $lowest = $this->getLowest();
        }

        return "[{$this->typeEngToChinese($this->type)}] 訊息
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

    public function subscribe(Memory $memory, string $currencyName, string $ChineseType)
    {
        if (! $this->currency = Currency::where('name', $currencyName)->first()) {
            return $this->toCurrencyNotFoundReplyMessage();
        }

        $type = $this->typeChineseToEng($ChineseType);

        if (! $memory->currencies->contains($this->currency->id)) {
            $memory->currencies()->attach($this->currency->id);
            return $this->toSubscribeSuccessReplyMessage();
        }

        return $this->toSubscribeRepeatReplyMessage();
    }

    public function toCurrencyNotFoundReplyMessage(): string
    {
        $currencies = Currency::all();
        $currencyNames = $currencies->implode('name', '、');

        return <<<EOD
hihi, 

找不到此貨幣的資料！

目前只有支援這個 [{$currencyNames}] 貨幣的匯率喔！
EOD;
    }

    public function toSubscribeSuccessReplyMessage(): string
    {
        $hour = self::NOTIFIED_AT;
        return <<<EOD
hihi!

已經爲您開啓了 [{$this->currencyWithTypeStr()}] 的訂閱，
會在每天的 {$hour} 點通知你今日最低的匯率哦！
EOD;
    }

    public function toSubscribeRepeatReplyMessage(): string
    {
        $hour = self::NOTIFIED_AT;
        return <<<EOD
hihi!

您已經訂閱過了 [{$this->currencyWithTypeStr()}] 囉，
一樣會在每天的 {$hour} 點通知你今日最低的匯率哦！
EOD;
    }

    public function typeEngToChinese(string $englishType): string
    {
        return self::TYPE_MAP[$englishType].'匯率';
    }

    public function typeChineseToEng(string $chineseType): ？string
    {
        return array_search($chineseType, self::TYPE_MAP);
    }

    private function currencyWithTypeStr(): string
    {
        return "{$this->currency->name} {$this->typeEngToChinese($this->type)}";
    }
}
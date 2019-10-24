<?php

namespace App\Services\ExchangeRate;

use App\Services\API\GuzzleApi;
use App\Services\LineBot\PushHandler\LineBotPushService;
use Illuminate\Support\Collection;

class ExchangeRateService
{
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
                'iso' => $this->currency,
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
     * @param  string  $type
     * @return ExchangeRateService
     */
    public function setType(string $type): ExchangeRateService
    {
        $this->type = $type;
        return $this;
    }

    public function formatMessage(array $lowest): string
    {
        if (empty($lowest)) {
            return '';
        }
        return "[{$this->getTypeStr()}] 訊息
  - 幣別：{$this->currency}
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
            $this->formatMessage($this->lowest)
        );
    }

    private function getTypeStr(): string
    {
        return $this->type === 'cash' ? '現金匯率' : '即期匯率';
    }
}
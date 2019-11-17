<?php

namespace App\Services\LineBot\ActionHandler;

use App\Models\Weight;
use App\Models\WeightSetting;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class LineBotActionWeightHelper extends LineBotActionHandler
{
    private $input;
    private $inputUrl;

    public function __construct()
    {
        parent::__construct();
        $this->inputUrl = config('line.link_of_weight_input');
    }

    public function preparePayload($rawPayload)
    {
        $breakdownMessage = $this->breakdownMessage($rawPayload);

        $this->input = $breakdownMessage[1] ?? null;

        return $this;
    }

    public function handle()
    {
        if (! $this->input) {
            return null;
        }

        if ($this->input === '記錄') {
            return $this->buildOpenPanel();
        }

        try {
            $weightInputs = json_decode($this->input, true);
            if (! is_array($weightInputs)) {
                return null;
            }

            /* 目標設定 */
            if ($this->purpose === 'weight_goal') {
                return $this->saveGoal($weightInputs);
            }

            /* 每日記錄 */
            return $this->saveDailyRecord($weightInputs);

        } catch (\Exception $e) {
            \Log::error(__METHOD__.' => '.$e);
            return null;
        }
    }

    public function reply(string $replyToken, $replyMessage)
    {
        if (is_string($replyMessage)) {
            return $this->LINEBot->replyText($replyToken, $replyMessage);
        }
        return $this->LINEBot->replyMessage($replyToken, $replyMessage);
    }

    public function getMoreOrLessStr($diff): string
    {
        return $diff >= 0 ? '增加' : '減少';
    }

    /**
     * @return TemplateMessageBuilder
     */
    private function buildOpenPanel(): TemplateMessageBuilder
    {
        $target = new ConfirmTemplateBuilder('減重小幫手來囉！', [
            new UriTemplateActionBuilder('記錄今日體重', config('line.link_of_weight_input')),
            new UriTemplateActionBuilder('目標設定', config('line.link_of_weight_setting')),
        ]);

        return new TemplateMessageBuilder('請查看手機的訊息唷！', $target);
    }

    private function replySaveMessage($todayWeight)
    {
        $yesterdayWeight = Weight::getYesterdayRecord($this->getMemory());
        if ($yesterdayWeight) {
            return $this->messageWithYesterday($yesterdayWeight, $todayWeight);
        }

        return $this->messageForToday($todayWeight);
    }

    private function messageWithYesterday(Weight $yesterdayWeight, $todayWeight)
    {
        $diffWeight = $todayWeight->weight - $yesterdayWeight->weight;
        $diffFat = $todayWeight->fat - $yesterdayWeight->fat;
        $finalWords = $this->getFinalWords($diffWeight, $diffFat);

        return <<<EOD
您昨天的記錄是：
體重： {$yesterdayWeight->weight} kg
體脂： {$yesterdayWeight->fat} %

今天的記錄是：
體重： {$todayWeight->weight} kg
體脂： {$todayWeight->fat} %

體重比昨天{$this->getMoreOrLessStr($diffWeight)}了 {$diffWeight} kg
體脂比昨天{$this->getMoreOrLessStr($diffFat)}了 {$diffFat} % 

{$finalWords}
EOD;
    }

    private function messageForToday($todayWeight)
    {
        return <<<EOD
找不到昨日的記錄。

今日體重記錄：
體重： {$todayWeight->weight} kg
體脂： {$todayWeight->fat} %

{$this->getFinalWords(0, 0)}

EOD;
    }

    private function getFinalWords(float $diffWeight, float $diffFat)
    {
        if ($diffWeight > 0 && $diffFat > 0) {
            return '記得要運動，飲食要均衡喔！';
        }

        if ($diffWeight < 0 && $diffFat < 0) {
            return '棒棒喔！要繼續保持唷！';
        }

        return '加油！要記得每天記錄喔，我會在提醒你的！';
    }

    private function saveGoal(array $weightInputs): string
    {
        if (count($weightInputs) !== 3) {
            return $this->errorMessage();
        }

        if (! isset($weightInputs['height']) ||
            ! isset($weightInputs['goal_fat']) ||
            ! isset($weightInputs['goal_weight'])
        ) {
            return $this->errorMessage();
        }

        WeightSetting::updateOrCreate(
            ['memory_id' => $this->getMemory()->id],
            [
                'height' => $weightInputs['height'],
                'goal_fat' => $weightInputs['goal_fat'],
                'goal_weight' => $weightInputs['goal_weight'],
            ]
        );

        return $this->replySaveGoalMessage($weightInputs);
    }

    private function saveDailyRecord(array $weightInputs): string
    {
        if (count($weightInputs) !== 2) {
            return $this->errorMessage();
        }

        if (! isset($weightInputs['weight']) || ! isset($weightInputs['fat'])) {
            return $this->errorMessage();
        }

        $todayWeight = Weight::saveRecordForToday(
            $this->getMemory(),
            $weightInputs['weight'],
            $weightInputs['fat']
        );

        return $this->replySaveMessage($todayWeight);
    }

    private function errorMessage(): string
    {
        return "噢噢！輸入好像有點問題喔 Q_Q";
    }

    private function replySaveGoalMessage(array $weightInputs)
    {
        return <<<EOD
已經幫你設定好以下的目標：
體重：{$weightInputs['goal_weight']} kg
體脂：{$weightInputs['goal_fat']} %
EOD;
    }
}
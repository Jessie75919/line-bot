<?php

namespace App\Services\LineBot\ActionHandler\Weight;

use App\Models\Memory;
use App\Models\Weight;
use App\Models\WeightSetting;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class LineBotActionWeightHelper extends LineBotActionHandler
{
    const INT_TO_DAY = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六',];
    private $inputUrl;
    /**
     * @var Memory
     */
    private $memory;
    private $text;

    public function __construct(Memory $memory, $text)
    {
        $this->memory = $memory;
        $this->text = $text;
        $this->inputUrl = config('line.link_of_weight_index');
    }

    public function paresMessage($message)
    {
        $breakdownMessage = $this->parseMessage($message);
        return [$breakdownMessage[0], $breakdownMessage[1] ?? null];
    }

    public function handle()
    {
        [$page, $input] = $this->paresMessage($this->text);
        \Log::info(__METHOD__."[".__LINE__."] =>".print_r($page, true));
        \Log::info(__METHOD__."[".__LINE__."] =>".print_r($input, true));
        if (! $input) {
            return null;
        }

        if ($input === 'show') {
            return $this->buildOpenPanel();
        }

        try {
            $weightInputs = json_decode($input, true);
            if (! is_array($weightInputs)) {
                return null;
            }

            if ($page === 'weight-setting') {
                /* 目標設定 */
                $message = $this->saveGoal($weightInputs);
            } else {
                /* 每日記錄 */
                $message = $this->saveDailyRecord($weightInputs);
            }

            return $this->reply($message);

        } catch (\Exception $e) {
            \Log::error(__METHOD__.' => '.$e);
            return null;
        }
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
        $weightSetting = $this->memory->weightSetting()->exists();
        if (! $weightSetting) {
            $target = new ConfirmTemplateBuilder('請先輸入目標設定', [
                new UriTemplateActionBuilder('點我進行設定', config('line.link_of_weight_index').'?page=setting'),
                new UriTemplateActionBuilder('記錄今日體重', config('line.link_of_weight_index').'?page=index'),
            ]);
        } else {
            $target = new ConfirmTemplateBuilder('減重小幫手來囉！', [
                new UriTemplateActionBuilder('記錄今日體重', config('line.link_of_weight_index').'?page=index'),
                new UriTemplateActionBuilder('調整目標設定', config('line.link_of_weight_index').'?page=setting'),
            ]);
        }
        return new TemplateMessageBuilder('請查看手機的訊息唷！', $target);
    }

    private function replySaveMessage($todayWeight)
    {
        $lastTimeWeight = Weight::getLastTimeRecord($this->memory);
        if ($lastTimeWeight) {
            return $this->messageForLastTime($lastTimeWeight, $todayWeight);
        }

        return $this->messageForToday($todayWeight);
    }

    private function messageForLastTime(Weight $lastTimeWeight, $todayWeight)
    {
        $diffWeight = round($todayWeight->weight - $lastTimeWeight->weight, 2);
        $diffFat = round($todayWeight->fat - $lastTimeWeight->fat, 2);
        $finalWords = $this->getFinalWords($diffWeight, $diffFat);

        return <<<EOD
👉 上次記錄 ({$lastTimeWeight->created_at->toDateString()})：
{$this->getRecordWording($lastTimeWeight)}

📅 今日記錄：
{$this->getRecordWording($todayWeight)}

{$this->compareWithLastTime($diffWeight, $diffFat)}


{$this->getDiffWithGoal($todayWeight)}

{$finalWords}
EOD;
    }

    private function messageForToday($todayWeight)
    {
        return <<<EOD
😐️ 找不到上次的記錄。

📅 今日記錄：
{$this->getRecordWording($todayWeight)}

{$this->getDiffWithGoal($todayWeight)}

{$this->getFinalWords(0, 0)}

EOD;
    }

    private function getRecordWording(Weight $weight): string
    {
        return <<<EOD
☆ 體重： {$weight->weight} kg
★ 體脂： {$weight->fat} %
☆ BMI： {$weight->bmi} %
EOD;
    }

    private function getFinalWords(float $diffWeight, float $diffFat)
    {
        if ($diffWeight > 0 && $diffFat > 0) {
            return '😮️ 記得要運動，飲食要均衡喔！';
        }

        if ($diffWeight < 0 && $diffFat < 0) {
            return '👍 棒棒喔！要繼續保持唷！';
        }

        return '😃 加油！要記得定時記錄喔，我會在提醒你的！';
    }

    private function saveGoal(array $weightInputs): string
    {
        if (count($weightInputs) !== 6) {
            return $this->errorMessage();
        }

        if (! isset($weightInputs['height']) ||
            ! isset($weightInputs['goal_fat']) ||
            ! isset($weightInputs['goal_weight']) ||
            ! (bool) ($weightInputs['enable_notification'])
        ) {
            return $this->errorMessage();
        }

        $notifyDaysStr = collect($weightInputs['notify_days'])
            ->sort()
            ->implode(',');

        if (! $this->memory->weightSetting()->exists()) {
            \Log::channel('slack')->info("Hi, 有新用戶新增設定囉！");
            \Log::info(__METHOD__."[".__LINE__."] => ".'Hi, 有新用戶設定囉！ channel_id:'.$this->memory->channel_id);
        }

        WeightSetting::updateOrCreate(
            ['memory_id' => $this->memory->id],
            [
                'height' => $weightInputs['height'],
                'goal_fat' => $weightInputs['goal_fat'],
                'goal_weight' => $weightInputs['goal_weight'],
                'enable_notification' => $weightInputs['enable_notification'],
                'notify_days' => $notifyDaysStr,
                'notify_at' => $weightInputs['notify_at'],
            ]
        );

        return $this->replySaveGoalMessage($weightInputs);
    }

    private function saveDailyRecord(array $weightInputs): string
    {
        if (count($weightInputs) !== 3) {
            return $this->errorMessage();
        }

        if (! isset($weightInputs['weight']) || ! isset($weightInputs['fat'])) {
            return $this->errorMessage();
        }

        $todayWeight = Weight::saveRecordForToday(
            $this->memory,
            $weightInputs['weight'],
            $weightInputs['fat'],
            $weightInputs['bmi'] ?? null
        );

        return $this->replySaveMessage($todayWeight);
    }

    private function errorMessage(): string
    {
        return "噢噢！輸入好像有點問題喔 😱";
    }

    private function replySaveGoalMessage(array $weightInputs)
    {
        return <<<EOD
😉 已經幫你設定好以下：

🏁 目標設定
☆ 體重：{$weightInputs['goal_weight']} kg
★ 體脂：{$weightInputs['goal_fat']} %
 
⚙️ 個人資料
★ 身高：{$weightInputs['height']} cm

{$this->settingText($weightInputs)}
EOD;
    }

    private function settingText($weightInputs): string
    {
        if ($weightInputs['enable_notification'] === 0) {
            return <<<EOD
🔕 紀錄提醒：關閉
EOD;
        }

        $notifyDaysStr = collect($weightInputs['notify_days'])
            ->sort()
            ->map(function ($day) {
                return self::INT_TO_DAY[$day];
            })
            ->implode('、');

        return <<<EOD
🔔 紀錄提醒：開啓
📆 提醒日：每週{$notifyDaysStr}
⏰ 提醒時間：{$weightInputs['notify_at']}
EOD;
    }

    private function getDiffWithGoal($todayWeight)
    {
        $setting = $this->memory->weightSetting;
        if (! $setting) {
            return '';
        }
        $diffWeight = round($todayWeight->weight - $setting->goal_weight, 2);
        $diffFat = round($todayWeight->fat - $setting->goal_fat, 2);

        return <<<EOD
💪 與目標差距：
☆ 體重：相差 {$diffWeight} kg
★ 體脂：相差 {$diffFat} %
EOD;
    }

    private function compareWithLastTime(float $diffWeight, float $diffFat)
    {
        $absDiffWeight = abs($diffWeight);
        $absDiffFat = abs($diffFat);
        return <<<EOD
* 體重比上次{$this->getMoreOrLessStr($diffWeight)}了 {$absDiffWeight} kg
* 體脂比上次{$this->getMoreOrLessStr($diffFat)}了 {$absDiffFat} % 
EOD;
    }
}
<?php

namespace App\Services\LineBot\ActionHandler\Meal;

use App\Jobs\Line\Meal\DeleteProcessStatus;
use App\Jobs\Line\Meal\UploadMealImage;
use App\Models\Line\Meal;
use App\Models\Line\MealReminder;
use App\Models\Line\MealType;
use App\Models\Line\ProcessStatus;
use App\Models\Memory;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\CameraRollTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\CameraTemplateActionBuilder;

class LineBotMealHelper extends LineBotActionHandler
{
    /** @var ProcessStatus */
    private $processStatus;

    /**
     * LineBotActionKeywordReplier constructor.
     * @param $memory
     * @param $message
     */
    public function __construct(Memory $memory, $message)
    {
        parent::__construct($memory, $message);
    }

    public function handle()
    {
        /* 文字輸入 */
        [$meal, $command] = $this->parseMessage($this->message);
        $this->processStatus = $this->memory->getProcessStatus();
        $message = null;

        if ($command === 'start') {
            $message = $this->showMealType();
        }

        if ($command === 'select') {
            $message = $this->askWayOfRecord();
        }

        if ($command === 'setting') {
            $message = $this->setSetting();
        }

        if ($command === 'image-upload') {
            $message = $this->handleForImageUpload();
        }

        if ($message) {
            $this->reply($message);
        }
    }

    /**
     * @param  string  $text
     * @return TextMessageBuilder
     */
    public function getMealTypeQuickMenus(string $text): TextMessageBuilder
    {
        $mealsBtns = MealType::getMealQuickReplyButtons();
        return new TextMessageBuilder(
            $text,
            new QuickReplyMessageBuilder($mealsBtns)
        );
    }

    protected function reply($message)
    {
        /* @var LINEBot bot */
        $bot = app(LINEBot::class);
        return $bot->replyMessage($this->replyToken, $message);
    }

    protected function showMealType()
    {
        $this->processStatus->mealStart();
        return $this->getMealTypeQuickMenus('hi～hi，請問要記錄哪一餐呢？');
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function askWayOfRecord()
    {
        [$meal, $command, $mealTypeId] = $this->parseMessage($this->message);

        /* @var Meal $meal */
        $meal = $this->memory->getTodayMealByType($mealTypeId);
        $this->processStatus->mealSelectMealType($meal->meal_type_id);

        dispatch(new DeleteProcessStatus($this->processStatus))
            ->delay(now()->addMinutes(5));

        $quickReply = new QuickReplyMessageBuilder([
            new QuickReplyButtonBuilder(new CameraTemplateActionBuilder('拍照記錄')),
            new QuickReplyButtonBuilder(new CameraRollTemplateActionBuilder('使用相簿')),
        ]);

        return new TextMessageBuilder('好哦！請問要用什麼方式記錄呢？', $quickReply);
    }

    private function handleForImageUpload()
    {
        [$meal, $command, $messageId] = $this->parseMessage($this->message);
        /* @var ProcessStatus $processStatus */
        $processStatus = $this->memory->processStatus;
        if (is_null($processStatus)) {
            return null;
        }

        if (! $processStatus->isOnSelectMealType()) {
            return null;
        }

        dispatch(new UploadMealImage($this->memory, $messageId));

        $mealType = $processStatus->getMealType();

        return new TextMessageBuilder("🙂️ 已經幫你記錄好{$mealType->name}囉！");
    }

    private function setSetting()
    {
        [, , $data] = $this->parseMessage($this->message);
        $notifyTimes = json_decode($data, true);

        $this->memory->mealReminders()->delete();

        if (count($notifyTimes) === 0) {
            return new TextMessageBuilder("已經幫你關閉所有提醒囉！你要記得自己提醒自己喔 😥️");
        }

        foreach ($notifyTimes as $time) {
            MealReminder::create([
                'memory_id' => $this->memory->id,
                'meal_type_id' => $time['meal_type_id'],
                'remind_at' => $time['time'],
            ]);
        }

        $mealsStr = $this->getMealsStr();

        $messageStr = <<<EOD
🙂️ 已經幫您設定好以下提醒時間囉！

{$mealsStr}
EOD;

        return new TextMessageBuilder($messageStr);
    }

    /**
     * @return mixed
     */
    private function getMealsStr()
    {
        return $this->memory->mealReminders()
            ->orderBy('meal_type_id')
            ->get()
            ->map(function ($reminder) {
                $mealType = MealType::find($reminder->meal_type_id);
                [$hour, $minute,] = explode(':', $reminder->remind_at);
                return "{$mealType->name} : {$hour}:{$minute}";
            })->implode("\n");
    }
}

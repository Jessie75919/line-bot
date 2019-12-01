<?php

namespace App\Services\LineBot\ActionHandler\Meal;

use App\Models\Line\Meal;
use App\Models\Line\MealType;
use App\Models\Memory;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\CameraRollTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\CameraTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class LineBotMealHelper extends LineBotActionHandler
{
    private $memory;
    private $text;
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    private $processStatus;

    /**
     * LineBotActionKeywordReplier constructor.
     * @param $memory
     * @param $text
     */
    public function __construct(Memory $memory, $text)
    {
        $this->memory = $memory;
        $this->text = $text;
    }

    public function handle()
    {
        [$meal, $command] = $this->parseMessage($this->text);
        $this->processStatus = $this->memory->processStatus()->firstOrCreate([]);

        if ($command === 'start') {
            return $this->showMealType();
        }

        if ($command === 'select') {
            return $this->askWayOfRecord();
        }

        if ($command === 'ready_add') {
            return $this->readySaveTextRecord();
        }

        $processStatus = $this->memory->processStatus;
        if (is_null($processStatus)) {
            return null;
        }

        if ($processStatus->isOnReadyAdd()) {

        }
    }

    public function reply($message)
    {
        /* @var LINEBot bot */
        $bot = app(LINEBot::class);
        return $bot->replyMessage($this->replyToken, $message);
    }

    /**
     * @return mixed
     */
    protected function showMealType()
    {
        $mealsBtns = $this->getMealQuickReplyButtons();
        $quickReply = new QuickReplyMessageBuilder($mealsBtns);

        $message = new TextMessageBuilder('請問要記錄哪一餐呢？', $quickReply);
        $this->processStatus->mealStart();
        return $this->reply($message);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function askWayOfRecord()
    {
        [$diet, $command, $mealTypeId] = $this->parseMessage($this->text);
        $quickReply = new QuickReplyMessageBuilder([
            new QuickReplyButtonBuilder(new CameraTemplateActionBuilder('拍照記錄')),
            new QuickReplyButtonBuilder(new CameraRollTemplateActionBuilder('使用相簿')),
            new QuickReplyButtonBuilder(
                new UriTemplateActionBuilder(
                    '文字記錄',
                    config('line.link_of_meal_index').'?page=index',
                    null
                )
            ),
        ]);
        $message = new TextMessageBuilder('請問要用什麼方式記錄呢？', $quickReply);
        /* @var Meal $meal */
        $meal = $this->memory->getTodayMealByType($mealTypeId);
        $this->processStatus->mealSelectMealType($meal->meal_type_id);
        return $this->reply($message);
    }

    private function getMealQuickReplyButtons()
    {
        return MealType::all()->map(function ($mealType) {
            return new QuickReplyButtonBuilder(
                new PostbackTemplateActionBuilder(
                    $mealType->name,
                    "diet，select，{$mealType->id}",
                    "hi，我想要記錄{$mealType->name} 🙂️"
                )
            );
        })->all();
    }

    private function readySaveTextRecord()
    {
        $mealTypeId = $this->memory->processStatus->data['meal_type_id'];
        $this->processStatus->mealReadySaveTextRecord($mealTypeId);
    }
}

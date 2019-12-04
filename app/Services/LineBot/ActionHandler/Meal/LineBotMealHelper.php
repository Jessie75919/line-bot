<?php

namespace App\Services\LineBot\ActionHandler\Meal;

use App\Models\Line\Meal;
use App\Models\Line\MealType;
use App\Models\Line\ProcessStatus;
use App\Models\Memory;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use Illuminate\Database\Eloquent\Model;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\Response;
use LINE\LINEBot\TemplateActionBuilder\CameraRollTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\CameraTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use Storage;

class LineBotMealHelper extends LineBotActionHandler
{
    /**
     * @var Model
     */
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
        $this->processStatus = $this->memory->processStatus()->firstOrCreate([]);

        if ($command === 'start') {
            return $this->showMealType();
        }

        if ($command === 'select') {
            return $this->askWayOfRecord();
        }

        if ($command === 'add-image') {
            return $this->handleForImageUpload();
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

        $message = new TextMessageBuilder('hi～hi，請問要記錄哪一餐呢？', $quickReply);
        $this->processStatus->mealStart();
        return $this->reply($message);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function askWayOfRecord()
    {
        [$meal, $command, $mealTypeId] = $this->parseMessage($this->message);
        $quickReply = new QuickReplyMessageBuilder([
            new QuickReplyButtonBuilder(new CameraTemplateActionBuilder('拍照記錄')),
            new QuickReplyButtonBuilder(new CameraRollTemplateActionBuilder('使用相簿')),
        ]);
        $message = new TextMessageBuilder('好哦！請問要用什麼方式記錄呢？', $quickReply);
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
                    "meal，select，{$mealType->id}",
                    "hi，我想要記錄{$mealType->name} 🙂️"
                )
            );
        })->all();
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

        /* @var Response $resp */
        $resp = $this->lineBot->getMessageContent($messageId);

        Storage::disk('line-meal')->put('/line/test.jpg', $resp->getRawBody());
    }
}

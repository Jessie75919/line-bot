<?php

namespace App\Services\LineBot\ActionHandler\Meal;

use App\Jobs\Line\Meal\DeleteProcessStatus;
use App\Models\Line\ProcessStatus;
use Illuminate\Database\Eloquent\Model;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;

class LineBotMealMenu
{
    public function getMealQuickReplyButtons()
    {
        return Model::all()->map(function ($mealType) {
            return new QuickReplyButtonBuilder(
                new PostbackTemplateActionBuilder(
                    $mealType->name,
                    "meal，select，{$mealType->id}",
                    "hi，我想要記錄{$mealType->name} 🙂️"
                )
            );
        })->all();
    }

    public function startMenu(ProcessStatus $processStatus)
    {
        $processStatus->mealStart();
        dispatch(new DeleteProcessStatus($processStatus))
            ->delay(now()->addMinutes(5));
        return $this;
    }

    /**
     * @param  string  $text
     * @return TextMessageBuilder
     */
    public function getMealTypeQuickMenus(string $text): TextMessageBuilder
    {
        $mealsBtns = $this->getMealQuickReplyButtons();
        return new TextMessageBuilder(
            $text,
            new QuickReplyMessageBuilder($mealsBtns)
        );
    }
}
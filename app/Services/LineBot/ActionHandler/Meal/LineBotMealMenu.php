<?php

namespace App\Services\LineBot\ActionHandler\Meal;

use App\Jobs\Line\Meal\DeleteProcessStatus;
use App\Models\Line\MealType;
use App\Models\Line\ProcessStatus;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;

class LineBotMealMenu
{
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
        $mealsBtns = MealType::getMealQuickReplyButtons();
        return new TextMessageBuilder(
            $text,
            new QuickReplyMessageBuilder($mealsBtns)
        );
    }
}
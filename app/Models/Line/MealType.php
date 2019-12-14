<?php

namespace App\Models\Line;

use Illuminate\Database\Eloquent\Model;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;

class MealType extends Model
{
    protected $fillable = ['key', 'name', 'time'];

    public static function getMealQuickReplyButtons()
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

    public function mealReminders()
    {
        return $this->hasMany(MealReminder::class);
    }
}

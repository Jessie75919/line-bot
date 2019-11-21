<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Models\Memory;
use App\Models\Weight;

class LiffWeightController extends Controller
{
    public function input()
    {
        return view(
            'line.input',
            [
                'liffToken' => Weight::getPageToken(config('line.link_of_weight_input')),
                'today' => now('Asia/Taipei')->toDateString(),
            ]
        );
    }

    public function setting()
    {
        return view(
            'line.setting',
            [
                'liffToken' => Weight::getPageToken(config('line.link_of_weight_setting')),
                'today' => now('Asia/Taipei')->toDateString(),
            ]
        );
    }

    public function mySetting(string $userId)
    {
        $memory = Memory::getByChannelId($userId);
        $weightSetting = $memory->weightSetting;

        return response()->json([
            'setting' => [
                'height' => $weightSetting->height,
                'goal_fat' => $weightSetting->goal_fat,
                'goal_weight' => $weightSetting->goal_weight,
                'enable_notification' => $weightSetting->enable_notification,
                'notify_day' => $weightSetting->notify_day,
                'notify_at' => $weightSetting->notify_at,
            ],
        ]);
    }
}
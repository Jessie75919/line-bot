<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Models\Memory;
use App\Models\Weight;
use Illuminate\Http\Request;

class LiffWeightController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->all()['page'] ?? 'index';
        return view(
            "line.weight.{$page}",
            [
                'liffToken' => Weight::getPageToken(config('line.link_of_weight_index')),
                'today' => now('Asia/Taipei')->toDateString(),
                'page' => $page,
            ]
        );
    }

    public function mySetting(string $userId)
    {
        $memory = Memory::getByChannelId($userId);
        $weightSetting = $memory->weightSetting;

        return response()->json([
            'setting' => isset($weightSetting)
                ? [
                    'height' => $weightSetting->height,
                    'goal_fat' => $weightSetting->goal_fat,
                    'goal_weight' => $weightSetting->goal_weight,
                    'enable_notification' => $weightSetting->enable_notification,
                    'notify_day' => $weightSetting->notify_day,
                    'notify_at' => $weightSetting->notify_at,
                ] : null,
        ]);
    }
}

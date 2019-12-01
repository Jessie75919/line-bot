<?php

namespace App\Http\Controllers\Line\Meal;

use App\Http\Controllers\Controller;
use App\Services\LineBot\Liff\LiffService;
use Illuminate\Http\Request;

class LiffMealController extends Controller
{
    public function index(Request $request)
    {
        return view(
            "line.meal.index",
            [
                'liffToken' => LiffService::parsePageToken(config('line.link_of_weight_index')),
                'today' => now('Asia/Taipei')->toDateString(),
                'page' => $request->all()['page'] ?? 'index',
            ]
        );
    }
}

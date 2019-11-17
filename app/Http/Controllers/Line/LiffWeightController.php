<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;

class LiffWeightController extends Controller
{
    public function input()
    {
        return view(
            'line.line-liff',
            [
                'liffToken' => config('line.liff_token'),
                'today' => now('Asia/Taipei')->toDateString(),
            ]
        );
    }
}

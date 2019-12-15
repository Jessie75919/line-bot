<?php

namespace App\Http\Controllers\Line\Weight;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Weight as WeightCollection;
use App\Http\Resources\WeightSetting as WeightSettingResource;
use App\Models\Memory;
use App\Repository\LineBot\Weight\WeightRepo;
use App\Services\LineBot\Liff\LiffService;
use Illuminate\Http\Request;

class LiffWeightController extends ApiController
{
    public function index(Request $request, LiffService $liffService)
    {
        return view(
            "line.weight.index",
            [
                'liffToken' => $liffService->parsePageToken(config('line.link_of_weight_index')),
                'today' => now('Asia/Taipei')->toDateString(),
                'page' => $request->all()['page'] ?? 'index',
            ]
        );
    }

    public function setting(Memory $memory)
    {
        $weightSetting = $memory->weightSetting;

        return isset($weightSetting)
            ? new WeightSettingResource($weightSetting)
            : response()->json();
    }

    public function weightRecords(Memory $memory, WeightRepo $weightRepo)
    {
        $weightRecords = $weightRepo->setMemory($memory)
            ->getWeightRecords();

        return new WeightCollection($weightRecords);
    }

    public function weeklyData(Memory $memory, WeightRepo $weightRepo)
    {
        $weightRecords = $weightRepo->setMemory($memory)
            ->getWeightRecordsBeforeDays(7);

        return new WeightCollection($weightRecords);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\BodyTemperature\BodyTemperature;
use App\Models\User;
use App\Repository\BodyTemperature\BodyTemperatureRepo;
use App\Services\DocsGeneration\Documents\BodyTemperature\Generator\BodyTemperatureGenerator;
use App\Utilities\DateTools;
use Illuminate\Http\Request;
use function explode;
use function storage_path;
use function tap;

class ApiBodyTemperatureController extends ApiController
{
    public function query(Request $request)
    {
        $date    = $request->date;
        $dateArr = explode('-', $request->date);
        $user_id = $request->user_id;

        $bodyTemperature = BodyTemperatureRepo::getModelByDate($date, $user_id);
        if (!$bodyTemperature) {
            $bodyTemperature = BodyTemperature::create([
                'month'       => $dateArr[1],
                'day'         => $dateArr[2],
                'temperature' => 0,
                'user_id'     => $user_id,
                'is_period'   => 0
            ]);
        }

        return $this->respondWithArray($bodyTemperature->toArray());
    }


    public function update(Request $request)
    {
        $date             = $request->date;
        $dateArr          = explode('-', $request->date);
        $body_temperature = $request->body_temperature;
        $is_period        = $request->is_period;
        $user_id          = $request->user_id;


        $bodyTemperature = BodyTemperatureRepo::getModelByDate($date, $user_id);
        if (!$bodyTemperature) {
            BodyTemperature::create([
                'month'       => $dateArr[1],
                'day'         => $dateArr[2],
                'temperature' => $body_temperature,
                'user_id'     => $user_id,
                'is_period'   => $is_period
            ]);

            return $this->respondWithOKMessage("Done");
        }

        tap($bodyTemperature)->update([
            'temperature' => $body_temperature,
            'is_period'   => $is_period
        ]);

        return $this->respondWithOKMessage("Done");
    }


    public function generateImage(Request $request)
    {

        $begin = DateTools::createCarbonByDateStr($request->begin, '/');
        $end   = DateTools::createCarbonByDateStr($request->end, '/');

        $userId = $request->user_id;
        $user   = User::find($userId);

        $generator = BodyTemperatureGenerator::init($begin, $end, $userId);

        $filename  = "{$begin}_{$end}_{$user->name}_body_temperature.png";

        $generator->setFilename($filename)
                  ->printData()
                  ->save();

        return response()
            ->download(storage_path("app/public/{$filename}"), $filename);
//            ->deleteFileAfterSend(true);
    }
}

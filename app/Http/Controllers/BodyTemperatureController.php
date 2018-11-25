<?php

namespace App\Http\Controllers;

use App\Repository\BodyTemperature\BodyTemperatureRepo;
use App\Utilities\DateTools;
use Auth;

class BodyTemperatureController extends Controller
{
    public function index()
    {
        $today           = DateTools::today()->toDateString();
        $bodyTemperature = BodyTemperatureRepo::getModelByDate($today);
        $user            = Auth::user();
        $payload         = [];


        $payload['today'] = $today;
        $payload['user_id']  = $user->id;

        if ($bodyTemperature) {
            $payload['temperature'] = $bodyTemperature->temperature;
            $payload['is_period']   = $bodyTemperature->is_period;
        }


        return view("body_temperature.index", $payload);


    }
}

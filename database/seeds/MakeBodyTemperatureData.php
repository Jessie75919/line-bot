<?php

use App\Models\BodyTemperature\BodyTemperature;
use Illuminate\Database\Seeder;

class MakeBodyTemperatureData extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        foreach (range(1, 30) as $item) {
            factory(BodyTemperature::class)->create(['month'=> 9 ,'day' => $item]);
        }
        foreach (range(1, 31) as $item) {
            factory(BodyTemperature::class)->create(['month'=> 10 ,'day' => $item]);
        }
        foreach (range(1, 30) as $item) {
            factory(BodyTemperature::class)->create(['day' => $item]);
        }
    }
}

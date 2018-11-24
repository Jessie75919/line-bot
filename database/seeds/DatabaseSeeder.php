<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//         $this->call(CreateRandomDatabase::class);
         $this->call(MakeBodyTemperatureData::class);
    }
}

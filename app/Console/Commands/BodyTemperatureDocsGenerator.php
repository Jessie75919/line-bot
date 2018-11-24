<?php

namespace App\Console\Commands;

use App\Services\DocsGeneration\Documents\BodyTemperature\Generator\BodyTemperatureGenerator;
use Illuminate\Console\Command;

class BodyTemperatureDocsGenerator extends Command
{
    protected $signature = 'body-temperature:gen';

    protected $description = '測試產出體溫表指令';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {

        $generator = BodyTemperatureGenerator::init('107-10-15', '107-11-30');
        $generator->printData()
                  ->save();

    }
}

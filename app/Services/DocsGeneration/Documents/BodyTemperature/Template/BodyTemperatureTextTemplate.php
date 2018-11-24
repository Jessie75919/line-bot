<?php

namespace App\Services\DocsGeneration\Documents\BodyTemperature\Template;

use App\Services\DocsGeneration\Printer\Template\TextPrintTemplate;
use App\Utilities\DateTools;

/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/11/24星期六
 * Time: 下午12:02
 */
class BodyTemperatureTextTemplate extends TextPrintTemplate
{


    /**
     * BodyTemperatureTemplate constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->handle();
    }


    private function handle()
    {
        $this->printYear();
        $this->printData();
    }


    private function printYear()
    {
        $this->addSections(36, 998, 154, DateTools::thisLunarYear());

    }


    private function printData()
    {
        $spacing = 50;
        $monthY  = 214;
        $dayY    = 263;
        $periodY = 1210;

        $this->data->each(function ($item, $key) use (
            $spacing,
            $monthY,
            $dayY,
            $periodY
        ) {
            $x = 874 + $key * $spacing;
            $this->addSections(30, $x, $monthY, $item->month);
            $this->addSections(30, $x, $dayY, $item->day);
            if ($item->is_period) {
                $this->addSections(30, $x, $periodY, "○");
                return;
            }
        });

    }
}
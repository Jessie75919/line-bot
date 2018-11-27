<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/11/24星期六
 * Time: 下午5:02
 */

namespace App\Services\DocsGeneration\Documents\BodyTemperature\Template;


use App\Services\DocsGeneration\Printer\Template\DrawPrintTemplate;
use function count;

class BodyTemperatureDrawTemplate extends DrawPrintTemplate
{

    const LOWEST_TEMPERATURE = 36;
    protected $firstX  = 890;
    protected $spacing = 50;
    protected $bottomY = 1121;
    protected $topY    = 323;
    private   $distance;
    private   $singleDiv;
    private   $miniSingleDiv;


    /**
     * BodyTemperatureTemplate constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data          = $data;
        $this->distance      = $this->bottomY - $this->topY;
        $this->singleDiv     = $this->distance / 16;
        $this->miniSingleDiv = $this->singleDiv / 10;
        $this->handle();
    }


    private function handle()
    {
        $this->parseDataToCord();
    }


    private function parseDataToCord()
    {

        $cords = [];
        $count = 0;

        foreach ($this->data as $item) {
            $x       = $this->firstX + $count * $this->spacing;
            $y       = $this->getY($item->temperature);
            $cords[] = ['x' => $x, 'y' => $y, 'temperature' => $item->temperature];
            $count++;
        }

        for ($i = 1 ; $i < count($cords) ; $i++) {

            $this->addSections(
                $cords[$i - 1]['x'], $cords[$i - 1]['y'], $cords[$i]['x'], $cords[$i]['y'], 4);
        }
    }


    private function getY($temperature)
    {
        $temperature = $temperature <= 36 ? 36 : $temperature;
        $diff         = $temperature - self::LOWEST_TEMPERATURE;
        $diffTimesTen = $diff * 10;
        $divCount     = $diffTimesTen % 10;
        $miniDivCount = ($diffTimesTen - $divCount) * 10;

        return $this->bottomY - ($divCount * $this->singleDiv + $miniDivCount * $this->miniSingleDiv);
    }
}
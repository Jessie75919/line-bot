<?php

namespace App\Services\DocsGeneration\Documents\BodyTemperature\Generator;

use App\Repository\BodyTemperature\BodyTemperatureRepo;
use App\Services\DocsGeneration\Documents\BodyTemperature\Template\BodyTemperatureDrawTemplate;
use App\Services\DocsGeneration\Documents\BodyTemperature\Template\BodyTemperatureTextTemplate;
use App\Services\DocsGeneration\Printer\DrawPrinterService;
use App\Services\DocsGeneration\Printer\TextPrinterService;

/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/11/24星期六
 * Time: 上午11:14
 */
class BodyTemperatureGenerator
{

    public $image;

    private $savePath;
    /** @var TextPrinterService */
    private $textPrinter;
    /** @var DrawPrinterService */
    private $drawPrinter;

    private $data;


    public function __construct($data)
    {
        $this->image    = imagecreatefrompng(storage_path('') . "/template/body_temperature/body_temperature.png");
        $this->savePath = storage_path('') . "/app/file/body_temperature.png";

        $this->textPrinter = new TextPrinterService();
        $this->drawPrinter = new DrawPrinterService();

        $this->data = $data;
    }


    public static function init($begin, $end)
    {
        $data = BodyTemperatureRepo::getRangeData($begin, $end);
        return new static($data);
    }


    public function printData()
    {

        $this->textPrinter
            ->setTemplate(new BodyTemperatureTextTemplate($this->data))
            ->setImage($this->image)
            ->printTemplate();

        $this->drawPrinter
            ->setTemplate(new BodyTemperatureDrawTemplate($this->data))
            ->setImage($this->image)
            ->printTemplate();

        return $this;
    }


    public function save()
    {
        ImageJPEG($this->image, $this->savePath);
        imagedestroy($this->image);
    }
}
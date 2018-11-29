<?php

namespace App\Services\DocsGeneration\Printer;


use function storage_path;

class TextPrinterService extends PrinterService
{
    private $font;



    public function __construct()
    {
        $this->font = storage_path('.') . "/fonts/kaiu.ttf";

    }


    public static function init()
    {


        return (new TextPrinterService());
    }


    public function printTemplate()
    {
        $this->color = ImageColorAllocate($this->image, 52, 115, 195);

        if(is_null($this->template->sections)){
            throw new \Exception("No Data !!");
        }

        foreach ($this->template->sections as $section) {
            $this->imagettftextSp(
                $this->image, $section->fontSize, 0, $section->x, $section->y, $this->color, $this->font,
                $section->text, $section->spacing
            );
        }

        return $this;
    }


    public function save($storedPath)
    {
        ImageJPEG($this->image, $storedPath);
        imagedestroy($this->image);
        return $this;
    }


    public function initImage($imagePath)
    {
        $this->image = imagecreatefrompng($imagePath);
        // hard-code color is black
        $this->color = ImageColorAllocate($this->image, 52, 115, 195);
        return $this;
    }


    private function imagettftextSp($image, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0)
    {
        if ($spacing == 0) {
            imagettftext($image, $size, $angle, $x, $y, $color, $font, $text);
        } else {
            $temp_x = $x;
            for ($i = 0 ; $i < strlen($text) ; $i++) {
                $bbox   = imagettftext($image, $size, $angle, $temp_x, $y, $color, $font, $text[$i]);
                $temp_x += $spacing + ($bbox[2] - $bbox[0]);
            }
        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/11/24星期六
 * Time: 上午11:32
 */

namespace App\Services\DocsGeneration\Printer;


use function is_null;

class DrawPrinterService extends PrinterService
{


    public function imageLineThick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
    {
        if ($thick == 1) {
            return imageline($image, $x1, $y1, $x2, $y2, $color);
        }
        $t = $thick / 2 - 0.5;
        if ($x1 == $x2 || $y1 == $y2) {
            return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t),
                round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
        }
        $k      = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
        $a      = $t / sqrt(1 + pow($k, 2));
        $points = [
            round($x1 - (1 + $k) * $a),
            round($y1 + (1 - $k) * $a),
            round($x1 - (1 - $k) * $a),
            round($y1 - (1 + $k) * $a),
            round($x2 + (1 + $k) * $a),
            round($y2 - (1 - $k) * $a),
            round($x2 + (1 - $k) * $a),
            round($y2 + (1 + $k) * $a),
        ];
        imagefilledpolygon($image, $points, 4, $color);
        return imagepolygon($image, $points, 4, $color);
    }


    public function printTemplate()
    {
        $this->color = ImageColorAllocate($this->image, 52, 115, 195);

        if(is_null($this->template->sections)){
            throw new \Exception("No Data !!");
        }

        foreach ($this->template->sections as $section) {
            $this->imageLineThick(
                $this->image,
                $section->x1, $section->y1,
                $section->x2, $section->y2,
                $this->color, $section->thick);
        }

        return $this;
    }
}
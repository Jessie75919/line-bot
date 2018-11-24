<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/11/24星期六
 * Time: 下午5:02
 */

namespace App\Services\DocsGeneration\Printer\Template;


use App\Services\DocsGeneration\Printer\Sections\DrawPrintSection;

class DrawPrintTemplate extends Template
{


    protected function addSections($x1, $y1, $x2, $y2, $thick)
    {
        $this->sections[] = new DrawPrintSection($x1, $y1, $x2, $y2, $thick);
        return $this;
    }

}
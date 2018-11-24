<?php

namespace App\Services\DocsGeneration\Printer\Template;

use App\Services\DocsGeneration\Printer\Sections\TextPrintSection;

/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/11/24星期六
 * Time: 上午11:57
 */
class TextPrintTemplate extends Template
{


    protected function addSections($fontSize, $x, $y, $text, $spacing = 0)
    {
        $this->sections[] = new TextPrintSection($fontSize, $x, $y, $text, $spacing);
        return $this;
    }
}
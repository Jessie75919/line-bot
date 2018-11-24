<?php

namespace App\Services\DocsGeneration\Printer\Sections;

class TextPrintSection
{
    /** * @var array */
    public $x;
    /** * @var int */
    public $y;
    /** * @var int */
    public $text;
    /** * @var int */
    public $spacing;
    /** * @var int */
    public $fontSize;


    /**
     * PrintSection constructor.
     * @param int    $fontSize
     * @param int    $x pixel
     * @param int    $y pixel
     * @param string $text
     * @param int    $spacing
     */
    public function __construct(int $fontSize, int $x, int $y, ?string $text, int $spacing = 0)
    {
        $this->fontSize = $fontSize;
        $this->x        = $x;
        $this->y        = $y;
        $this->text     = $text;
        $this->spacing  = $spacing;
    }


}

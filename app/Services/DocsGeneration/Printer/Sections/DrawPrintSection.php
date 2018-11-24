<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/11/24星期六
 * Time: 下午5:12
 */

namespace App\Services\DocsGeneration\Printer\Sections;


class DrawPrintSection
{
    /** * @var int */
    public $x1;
    /** * @var int */
    public $y1;
    /** * @var int */
    public $x2;
    /** * @var int */
    public $y2;
    /** * @var int */
    public $thick;


    /**
     * DrawPrintSection constructor.
     * @param     $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param int $thick
     */
    public function __construct($x1, $y1, $x2, $y2, $thick)
    {
        $this->x1    = $x1;
        $this->y1    = $y1;
        $this->x2    = $x2;
        $this->y2    = $y2;
        $this->thick = $thick;
    }
}
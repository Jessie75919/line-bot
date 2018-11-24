<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/11/24星期六
 * Time: 上午11:38
 */

namespace App\Services\DocsGeneration\Printer;


abstract class PrinterService
{
    public    $image;
    protected $template;
    protected $color;


    /**
     * @param mixed $image
     * @return PrinterService
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }


    abstract public function printTemplate();


    /**
     * @param mixed $template
     * @return PrinterService
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }
}
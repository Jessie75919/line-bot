<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/11/24星期六
 * Time: 下午12:00
 */

namespace App\Services\DocsGeneration\Printer\Template;


class Template
{
    protected $data;
    public $sections;


    /**
     * @param mixed $data
     * @return Template
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

}
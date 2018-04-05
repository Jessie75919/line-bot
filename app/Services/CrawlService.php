<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/4/2星期一
 * Time: 下午10:32
 */

namespace App\Services;


use Symfony\Component\DomCrawler\Crawler;

class CrawlService
{
    private $crawler;


    /**
     * CrawlService constructor.
     */
    public function __construct()
    {
        $this->crawler = new Crawler();
    }
}
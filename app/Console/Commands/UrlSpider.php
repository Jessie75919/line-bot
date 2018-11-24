<?php

namespace App\Console\Commands;

use Goutte\Client as GoutteClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Pool;
use Illuminate\Console\Command;
use function trim;

class UrlSpider extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'line-bot:crawl {occu} {keyWords*}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Crawl First Time';


    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        //
        $concurrency = $this->argument('occu');  //并发数
        $keyWords    = $this->argument('keyWords');    //查询关键词
        /** @var GuzzleClient $guzzleClent */
        $guzzleClent = new GuzzleClient();
        /** @var GoutteClient $client */
        $client      = new GoutteClient();
        $client->setClient($guzzleClent);

        $request = function($total) use ($client, $keyWords) {
            foreach($keyWords as $key) {
//                $url = 'https://laravel-china.org/search?q=' . $key;
                $url = 'http://rate.bot.com.tw/xrt?Lang=zh-TW';
                yield function() use ($client, $url) {
                    return $client->request('GET', $url);
                };
            }
        };


        $pool = new Pool($guzzleClent, $request(count($keyWords)), [
            'concurrency' => $concurrency,
            'fulfilled'   => function($response, $index) use ($client) {
                $response->filter(' tr > td')->reduce(function($node) use ($client) {

//                    print_r($node);

//                    if(strlen($node->attr('title')) == 0) {
                        $title   = $node->text();             //文章标题

                        $this->info(trim($title));

//                        $link    = $node->attr('href');        //文章链接
//                        $this->info($link);
//                        $carwler = $client->request('GET', $link);       //进入文章

//                        $this->comment($carwler->filter('public')->first()->text());
//                        $product = $carwler->filter('#emojify')->first()->text();     //获取内容
//                        Storage::disk('local')->put($title, $product);           //储存在本地
//                    }
                });
            },
            'rejected'    => function($reason, $index) {
                $this->error("Error is " . $reason);
            }
        ]);
        //开始爬取
        $promise = $pool->promise();
        $promise->wait();
    }
}

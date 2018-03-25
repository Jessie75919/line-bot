<?php

use App\Message;
use Illuminate\Database\Seeder;

class KeywordGenerate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Message::create([
            'keyword' => 'hi',
            'message' => 'Hello~',
        ]);
        Message::create([
            'keyword' => 'fb',
            'message' => 'https://www.facebook.com/ChuCHandmade/',
        ]);
        Message::create([
            'keyword' => 'ig',
            'message' => 'chu.c.handmade',
        ]);
        Message::create([
            'keyword' => '蝦皮',
            'message' => 'https://shopee.tw/juicekuo1227',
        ]);
        Message::create([
            'keyword' => '吃屎吧',
            'message' => '你才吃屎！！',
        ]);
        Message::create([
            'keyword' => '你好',
            'message' => '蹦啾',
        ]);
        Message::create([
            'keyword' => 'hello',
            'message' => '哩賀～',
        ]);
    }
}

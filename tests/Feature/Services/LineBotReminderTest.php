<?php

namespace Tests\Feature\Services;

use function dd;
use LINE\LINEBot\Response;
use Tests\TestCase;

class LineBotReminderTest extends TestCase
{


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testForTodayMorning()
    {

        $cmd = '提醒;今天 早上 8:45;泡咖啡';
        $package = $this->getPackage($cmd);
        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }


    public function testForWeekday()
    {
        $cmd      = '提醒;星期四 早上 8:45;泡咖啡';
        $package  = $this->getPackage($cmd);
        $response = $this->call('post', 'webhook', [$package]);

        $this->assertTrue($response->isSuccessful());

    }

    public function testForNextWeekday()
    {
        $cmd      = '提醒;下星期四 早上 8:45;泡咖啡';
        $package  = $this->getPackage($cmd);
        $response = $this->call('post', 'webhook', [$package]);

        $this->assertTrue($response->isSuccessful());

    }


    public function testForNextNextWeekday()
    {
        $cmd      = '提醒;下下星期四 早上 8:45;泡咖啡';
        $package  = $this->getPackage($cmd);
        $response = $this->call('post', 'webhook', [$package]);

        $this->assertTrue($response->isSuccessful());

    }


    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function testForGetState()
    {

        $cmd = '提醒;all';

        $package = $this->getPackage($cmd);

        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }



    /**
     * A basic test example.
     *
     * @return void
     */
    public function testForRandomString()
    {

        $cmd = '提醒;feifjojaeofjo;泡咖啡';

        $package = $this->getPackage($cmd);

        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }


    /**
     * @test
     */
    public function testForTodayMorningChineseTime()
    {

        $cmd = '提醒;今天 晚上 8點30分;大便便';

        $package = $this->getPackage($cmd);

        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }

    /**
     * @test
     */
    public function testForDeleteReminder()
    {

        $cmd = '提醒;del;55';

        $package = $this->getPackage($cmd);

        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testForNoWithTodayAlias()
    {

        $cmd = '提醒;早上 8點37分;別賴床囉！';

        $package = $this->getPackage($cmd);

        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }


    /**
     * @test
     */
    public function testForSpecificDateTime()
    {

        $cmd = '提醒;2018-04-08 10:00;休息一下囉';

        $package = $this->getPackage($cmd);

        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }

    /**
     * @test
     */
    public function testForTimeWithBigColon()
    {

        $cmd = '提醒;明天 下午 2：10;記得測試一下喔';

        $package = $this->getPackage($cmd);


        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }


    private function getPackage($cmd)
    {
        return [
            'events' => [
                [
                    'type'       => 'message',
                    'replyToken' => '73425dfaf88644ada8a43de2cb3d37fd',
                    'timestamp'  => '1522892959409',
                    'source'     => [
                        'groupId' => 'C31f1bf838bea0857e40b13c7ea3e94a8',
                        'userId'  => 'Ub3cb8c2273727454ec52e48cf2388158',
                        'type'    => 'group'
                    ],
                    'message'    => [
                        'id'   => '7742096355881',
                        'type' => 'text',
                        'text' =>  $cmd
                    ]
                ],
                [
                    "replyToken" => "73425dfaf88644ada8a43de2cb3d37fd",
                    "type"       => "follow",
                    "timestamp"  => '1462629479859',
                    "source"     => [
                        "type"   => "user",
                        "userId" => "U4af4980629..."
                    ]
                ]
            ]
        ];
    }
}

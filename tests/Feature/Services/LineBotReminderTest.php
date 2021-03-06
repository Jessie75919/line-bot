<?php

namespace Tests\Feature\Services;

use Tests\TestCase;

class LineBotReminderTest extends TestCase
{

    /* @test */
    public function testForTodayMorning()
    {
        $this->withoutExceptionHandling();

        $cmd = '提醒;今天 早上 8:45;泡咖啡';
        $package = $this->getPackage($cmd);
        $response = $this->post('webhook', [$package]);

        $this->assertTrue($response->isSuccessful());

    }

    /* @test */
    public function testHelpCommand()
    {
        $cmd = 'help';
        $package = $this->getPackage($cmd);
        $response = $this->post('webhook', $package);

        $this->assertTrue($response->isSuccessful());
    }

    public function ForWeekday()
    {
        $cmd = '提醒;星期四 早上 8:45;泡咖啡';
        $package = $this->getPackage($cmd);
        $response = $this->call('post', 'webhook', [$package]);

        $this->assertTrue($response->isSuccessful());

    }

    public function ForNextWeekday()
    {
        $cmd = '提醒;下星期三 早上 8:45;泡咖啡';
        $package = $this->getPackage($cmd);
        $response = $this->call('post', 'webhook', [$package]);

        $this->assertTrue($response->isSuccessful());

    }

    public function ForNextNextWeekday()
    {
        $cmd = '提醒;下下星期三 早上 8:45;泡咖啡';
        $package = $this->getPackage($cmd);
        $response = $this->call('post', 'webhook', [$package]);

        $this->assertTrue($response->isSuccessful());

    }

    public function ForGetState()
    {

        $cmd = '提醒;all';

        $package = $this->getPackage($cmd);

        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }

    /**
     * A basic test example.
     * @return void
     */
    public function ForRandomString()
    {

        $cmd = '提醒;feifjojaeofjo;泡咖啡';

        $package = $this->getPackage($cmd);

        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }

    /**
     */
    public function ForTodayMorningChineseTime()
    {

        $cmd = '提醒;今天 晚上 8點30分;大便便';

        $package = $this->getPackage($cmd);

        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }

    /**
     *
     */
    public function ForDeleteReminder()
    {

        $cmd = '提醒;del;55';

        $package = $this->getPackage($cmd);

        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }

    /**
     * A basic test example.
     * @return void
     */
    public function ForNoWithTodayAlias()
    {

        $cmd = '提醒;早上 8點37分;別賴床囉！';

        $package = $this->getPackage($cmd);

        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }

    /**
     *
     */
    public function ForSpecificDateTime()
    {

        $cmd = '提醒;2018-04-08 10:00;休息一下囉';

        $package = $this->getPackage($cmd);

        $response = $this->call('post', 'webhook', [$package]
        );

        $this->assertTrue($response->isSuccessful());

    }

    /**
     */
    public function ForTimeWithBigColon()
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
                    'type' => 'message',
                    'replyToken' => '73425dfaf88644ada8a43de2cb3d37fd',
                    'timestamp' => '1522892959409',
                    'source' => [
                        'groupId' => 'C31f1bf838bea0857e40b13c7ea3e94a8',
                        'userId' => 'Ub3cb8c2273727454ec52e48cf2388158',
                        'type' => 'group',
                    ],
                    'message' => [
                        'id' => '7742096355881',
                        'type' => 'text',
                        'text' => $cmd,
                    ],
                ],
                [
                    "replyToken" => "73425dfaf88644ada8a43de2cb3d37fd",
                    "type" => "follow",
                    "timestamp" => '1462629479859',
                    "source" => [
                        "type" => "user",
                        "userId" => "U4af4980629...",
                    ],
                ],
            ],
        ];
    }
}

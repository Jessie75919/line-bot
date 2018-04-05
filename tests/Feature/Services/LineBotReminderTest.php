<?php

namespace Tests\Feature\Services;

use LINE\LINEBot;
use Psy\Util\Json;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LineBotReminderTest extends TestCase
{

    private $cmd = '提醒;2018-04-05 18:17;DEF';

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $package = [
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
                            'text' =>  $this->cmd
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

        $response = $this->call('post', 'webhook', [$package]
        );

        $response->assertStatus(200);


    }
}

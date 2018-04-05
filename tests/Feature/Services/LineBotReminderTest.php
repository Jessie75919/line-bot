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

        /*
                                                                  [type] => message
2018-04-05T01:49:20.173024+00:00 app[web.1]:                     [replyToken] => 73425dfaf88644ada8a43de2cb3d37fd
2018-04-05T01:49:20.174692+00:00 app[web.1]:                     [source] => Array
2018-04-05T01:49:20.175017+00:00 app[web.1]:                         (
2018-04-05T01:49:20.176570+00:00 app[web.1]:                             [groupId] => C31f1bf838bea0857e40b13c7ea3e94a8
2018-04-05T01:49:20.176997+00:00 app[web.1]:                             [userId] => Ub3cb8c2273727454ec52e48cf2388158
2018-04-05T01:49:20.177196+00:00 app[web.1]:                             [type] => group
2018-04-05T01:49:20.177371+00:00 app[web.1]:                         )
2018-04-05T01:49:20.180581+00:00 app[web.1]:
2018-04-05T01:49:20.180841+00:00 app[web.1]:                     [timestamp] => 1522892959409
2018-04-05T01:49:20.181071+00:00 app[web.1]:                     [message] => Array
2018-04-05T01:49:20.181271+00:00 app[web.1]:                         (
2018-04-05T01:49:20.184730+00:00 app[web.1]:                             [id] => 7742096355881
2018-04-05T01:49:20.184509+00:00 app[web.1]:                             [type] => text
2018-04-05T01:49:20.184938+00:00 app[web.1]:                             [text] => ？
2018-04-05T01:49:20.185820+00:00 app[web.1]:                         )
2018-04-05T01:49:20.189687+00:00 app[web.1]:
2018-04-05T01:49:20.189862+00:00 app[web.1]:                 )



         */


    }
}

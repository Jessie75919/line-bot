<?php

namespace App\Console\Commands\Line;

use App\Services\LineBot\PushHandler\LineBotPushService;
use Illuminate\Console\Command;

class LineBotPushMessage extends Command
{
    protected $signature = 'line:push';
    protected $description = 'Line Bot Push Message';
    /**
     * @var LineBotPushService
     */
    private $lineBotPush;

    /**
     * Create a new command instance.
     * @param  LineBotPushService  $lineBotPush
     */
    public function __construct(LineBotPushService $lineBotPush)
    {
        parent::__construct();
        $this->lineBotPush = $lineBotPush;
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $lineBot = $this->lineBotPush->getLineBot();

        $resp = $this->lineBotPush->pushMessage(
            'R421b3280799bcde75de0d6c4ddf91d47',
            'hihihihi~ Nice To Meet You !!!!!!!!!!!'
        );
    }
}

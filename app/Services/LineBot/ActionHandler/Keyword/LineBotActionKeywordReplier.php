<?php

namespace App\Services\LineBot\ActionHandler\Keyword;

use App\Models\Message;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use Illuminate\Support\Collection;

class LineBotActionKeywordReplier extends LineBotActionHandler
{
    private $memory;
    private $message;

    /**
     * LineBotActionKeywordReplier constructor.
     * @param $memory
     * @param $message
     */
    public function __construct($memory, $message)
    {
        $this->memory = $memory;
        $this->message = $message;
    }

    public function handle()
    {
        /* @var Collection $keywords */
        $keywords = Message::where('keyword', $this->message)
            ->where('channel_id', $this->memory->channel_id)->get();

        $message = count($keywords) > 0 ? $keywords->random()->message : null;

        return $this->reply($message);
    }
}

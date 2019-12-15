<?php

namespace App\Services\LineBot\ActionHandler\Keyword;

use App\Models\Message;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use Illuminate\Support\Collection;

class LineBotActionKeywordReplier extends LineBotActionHandler
{
    /**
     * LineBotActionKeywordReplier constructor.
     * @param $memory
     * @param $message
     */
    public function __construct($memory, $message)
    {
        parent::__construct($memory, $message);
    }

    public function handle()
    {
        /* @var Collection $keywords */
        $keywords = Message::where('keyword', $this->message)
            ->where('channel_id', $this->memory->channel_id)->get();

        if (count($keywords) == 0) {
            return null;
        }

        $message = $keywords->random()->message;

        return $this->reply($message);
    }
}

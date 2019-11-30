<?php

namespace App\Services\LineBot\ActionHandler\Keyword;

use App\Models\Message;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;
use Illuminate\Support\Collection;

class LineBotActionKeywordReplier extends LineBotActionHandler
{
    private $memory;
    private $text;

    /**
     * LineBotActionKeywordReplier constructor.
     * @param $memory
     * @param $text
     */
    public function __construct($memory, $text)
    {
        $this->memory = $memory;
        $this->text = $text;
    }

    public function handle()
    {
        /* @var Collection $keywords */
        $keywords = Message::where('keyword', $this->text)
            ->where('channel_id', $this->memory->channel_id)->get();

        return count($keywords) > 0 ? $keywords->random()->message : null;
    }
}

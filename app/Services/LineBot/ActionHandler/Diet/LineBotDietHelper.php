<?php

namespace App\Services\LineBot\ActionHandler\Diet;

use App\Services\LineBot\ActionHandler\LineBotActionHandler;

class LineBotDietHelper extends LineBotActionHandler
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
        return '';
    }
}

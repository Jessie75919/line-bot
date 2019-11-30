<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/3/31星期六
 * Time: 下午6:11
 */

namespace App\Services\LineBot\ActionHandler\Learner;

use App\Models\Memory;
use App\Models\Message;
use App\Services\LineBot\ActionHandler\LineBotActionHandler;

class LineBotActionLearner extends LineBotActionHandler
{
    /**
     * @var Memory
     */
    private $memory;
    private $text;

    /**
     * LineBotActionLearner constructor.
     * @param  Memory  $memory
     * @param $text
     */
    public function __construct(Memory $memory, $text)
    {
        $this->memory = $memory;
        $this->text = $text;
    }

    public function handle()
    {
        [$key, $value] = $this->getKeyAndValue($this->text);

        if (strlen($key) <= 0 || strlen($value) <= 0) {
            return null;
        }

        try {
            Message::create([
                'keyword' => $key,
                'message' => $value,
                'channel_id' => $this->memory->channel_id,
            ]);

            $message = <<<EOD
🙂️ 我已經記起來 {$key} 等於 {$value} 的意思囉！
EOD;

        } catch (\Exception $e) {
            \Log::error(__METHOD__.' => '.$e);
            $message = "😭️ 好像哪裏發生錯誤惹！";
        }

        $this->reply($message);
    }

    private function getKeyAndValue($message)
    {
        $messageArr = $this->parseMessage($message);
        return [$messageArr[1], $messageArr[2]];
    }
}

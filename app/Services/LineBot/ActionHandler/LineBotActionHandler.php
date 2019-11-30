<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2019-07-07
 * Time: 09:47
 */

namespace App\Services\LineBot\ActionHandler;

use App\Services\LineBot\Router\LineBotRouter;

abstract class LineBotActionHandler
{
    /** Dissect the Message if it is a learning command with <學;key;value>
     * @param $userMessage
     * @return array
     */
    public function parseMessage($userMessage): array
    {
        return collect(
            preg_split(
                '/'.LineBotRouter::DELIMITER.'/',
                $userMessage
            )
        )->map(function ($item) {
            return trim($item);
        })->toArray();
    }

    abstract public function handle();
}

<?php

namespace App\Services\LineBot\TypePayloadHandler;

use LINE\LINEBot\Event\MessageEvent;

interface TypePayloadHandlerInterface
{
    public function checkPurpose(MessageEvent $message);

    public function dispatch();
}
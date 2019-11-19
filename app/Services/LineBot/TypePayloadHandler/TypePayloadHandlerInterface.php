<?php

namespace App\Services\LineBot\TypePayloadHandler;

use LINE\LINEBot\Event\BaseEvent;

interface TypePayloadHandlerInterface
{
    public function checkPurpose(BaseEvent $message);

    public function dispatch();
}
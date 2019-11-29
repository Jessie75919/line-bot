<?php

namespace App\Services\LineBot\TypePayloadHandler;

use LINE\LINEBot\Event\BaseEvent;

interface TypePayloadHandlerInterface
{
    public function route(BaseEvent $message);

}
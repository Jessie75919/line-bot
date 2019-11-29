<?php

namespace App\Services\LineBot\TypePayloadHandler;

use LINE\LINEBot\Event\BaseEvent;

interface TypePayloadHandlerInterface
{
    public function checkRoute(BaseEvent $message);

}
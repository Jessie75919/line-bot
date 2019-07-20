<?php

namespace App\Services\LineBot\TypePayloadHandler;

interface TypePayloadHandlerInterface
{
    public function checkPurpose($payload);

    public function preparePayload();

    public function dispatch();
}
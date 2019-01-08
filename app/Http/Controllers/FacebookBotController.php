<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function info;
use function response;

class FacebookBotController extends Controller
{
    public function post(Request $request)
    {
        $body = (object)$request->toArray();

        if ($body->object !== 'page') {
            return response('', 404);
        }

        $entries = $body->entry;
        foreach ($entries as $entry) {
            $webhookEvt = $entry['messaging'][0];
            info($webhookEvt);
        }
        return response('EVENT_RECEIVED', 200);
    }


    public function get(Request $request)
    {

        $verifyToken = 'Npkb5h9pY95fn5v3PmWRPSAFM';

        $mode = $request->hub_mode;
        $token = $request->hub_verify_token;
        $challenge = $request->hub_challenge;

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200);
        } else {
            return response('', 403);
        }
    }
}

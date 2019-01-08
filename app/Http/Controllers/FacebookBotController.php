<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use function env;
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

            $senderId   = $webhookEvt['sender']['id'];
            $message    = $webhookEvt['message']['text'];

            $this->sendMessage($senderId, $message);

        }
        return response('EVENT_RECEIVED', 200);
    }


    public function get(Request $request)
    {

        $verifyToken = env('FACEBOOK_CUSTOM_TOKEN');

        $mode      = $request->hub_mode;
        $token     = $request->hub_verify_token;
        $challenge = $request->hub_challenge;

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200);
        } else {
            return response('', 403);
        }
    }


    private function sendMessage($senderId, $message)
    {
        $facebookToken = env('FACEBOOK_TOKEN');
        $client        = new Client(['timeout' => 2.0]);
        $client->post("https://graph.facebook.com/v2.6/me/messages?access_token={$facebookToken}",
            [
                'form_params' => [
                    "recipient" => ["id" => $senderId],
                    "message"   => ["text" => $message]
                ]
            ]);
    }
}

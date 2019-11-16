<?php

namespace App\Http\Controllers;

use App\Http\Requests\Line\LineMessageApiRequest;
use App\Services\Google\GooglePlaceApiService;
use App\Services\LineBot\LineBotMainService;
use Illuminate\Http\Request;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;

class LineController extends Controller
{
    /* @var LineBotMainService $lineMainService */
    private $lineMainService;

    /**
     * LineController constructor.
     * @param  LineBotMainService  $lineBotMainService
     */
    public function __construct(LineBotMainService $lineBotMainService)
    {
        \Log::info('Line Bot Starting .... ');
        $this->lineMainService = $lineBotMainService;
    }

    public function index(LineMessageApiRequest $request)
    {
        $body = $request->getContent();

        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);

        /* @var MessageEvent $messageEvt */
        $messageEvt = $this->lineMainService->parseEventRequest($body, $signature);

        \Log::info(__METHOD__.' => '.print_r($messageEvt, true));

        $response = $this->lineMainService->handle($messageEvt[0]);

        \Log::info(__METHOD__.' => '.print_r($response, true));

        return response(200);
    }

    public function imagePreview(Request $request)
    {
        $placeApi = (app(GooglePlaceApiService::class));

        $photoRef = $request->ref;

        return response()
            ->make($placeApi->getPhotoRefApi($photoRef))
            ->header("Content-Type", 'image/png');
    }

    public function liffEndpoint()
    {
        return view(
            'line.line-liff',
            [
                'liffToken' => config('line.liff_token'),
                'today' => now('Asia/Taipei')->toDateString(),
            ]
        );
    }
}

<?php

namespace App\Http\Controllers\Line;

use App\Http\Controllers\Controller;
use App\Http\Requests\Line\LineMessageApiRequest;
use App\Services\Google\GooglePlaceApiService;
use App\Services\LineBot\LineBotIndexService;
use Illuminate\Http\Request;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;

class LineController extends Controller
{
    /* @var LineBotIndexService $lineIndex */
    private $lineIndex;

    /**
     * LineController constructor.
     * @param  LineBotIndexService  $lineBotMainService
     */
    public function __construct(LineBotIndexService $lineBotMainService)
    {
        \Log::info('Line Bot Starting .... ');
        $this->lineIndex = $lineBotMainService;
    }

    public function index(LineMessageApiRequest $request)
    {
        $body = $request->getContent();
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);

        /* @var MessageEvent $messageEvt */
        $messageEvt = $this->lineIndex->parseEventRequest($body, $signature);

        \Log::info(__METHOD__.' => '.print_r($messageEvt, true));

        $response = $this->lineIndex->handle($messageEvt[0]);

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
}

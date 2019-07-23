<?php


namespace App\Services\LineBot\ActionHandler;

use App\Services\Google\GooglePlaceApiService;
use Illuminate\Support\Collection;

class LineBotActionFoodNearbySearcher implements LineBotActionHandlerInterface
{
    private $payload;
    /* @var GooglePlaceApiService */
    private $placeApi;


    /**
     * LineBotActionFoodNearbySearcher constructor.
     * @param $payload
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
        $this->placeApi = (app(GooglePlaceApiService::class))
            ->setPayload($payload);
    }


    public function handle()
    {
        $rawData = $this->getDataFromGooglePlaceAPI();

        $shops = $this->filterFoodTypeShops($rawData);

        $formatShops = $this->formatShops($shops);

        return $formatShops;
    }


    private function getDataFromGooglePlaceAPI()
    {
        return $this->placeApi->nearBySearchApi()->results;
    }


    private function filterFoodTypeShops($rawData)
    {
        return collect($rawData)->filter(function ($item) {
            return in_array('food', $item->types);
        });
    }


    private function formatShops(Collection $shops)
    {
        return $shops->map(function ($item) {
            $detail = $this->placeApi->getShopDetailApi($item->place_id)->result;
            $isOpenNow = null;
            //            $photoUrl = null;

            try {
                $photoUrl = $this->placeApi->getPhotoRefUrl($item->photos[0]->photo_reference);
                $isOpenNow = $detail->opening_hours ? $detail->opening_hours->open_now : null;
            } catch (\Exception $e) {
                \Log::info(__METHOD__." => ".$e);
                //                dd($item, $detail, $e);
            }

            return (object) [
                'photo_url' => $photoUrl ?? url('images/shop.png'),
                'website' => $detail->website ?? '',
                'is_opening' => $isOpenNow ? '還在營業中！' : '已休息囉！',
                'label' => mb_substr($item->name, 0, 40, "utf-8"),
                'url' => "http://maps.google.com/?q=".
                    "{$item->geometry->location->lat},{$item->geometry->location->lng}",
            ];
        });
    }
}

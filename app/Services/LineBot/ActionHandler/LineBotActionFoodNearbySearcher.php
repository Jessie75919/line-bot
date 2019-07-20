<?php


namespace App\Services\LineBot\ActionHandler;

use Illuminate\Support\Collection;
use App\Services\Google\GooglePlaceApiService;

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
            ->setPayload($payload)
            ->setMaxprice(1000);
    }


    public function handle()
    {
        $rawData = $this->getDataFromGooglePlaceAPI();

        $shops = $this->filterFoodTypeShops($rawData);

        $formatShops = $this->formatShops($shops);

        return $formatShops->take(10);
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

            try {
                $isOpenNow = $detail->opening_hours ? $detail->opening_hours->open_now : null;
            } catch (\Exception $e) {
            }

            return [
                'photo_url'  => $this->placeApi->getPhotoRefUrl($item->photos[0]->photo_reference),
                'website'    => $detail->website ?? '',
                'is_opening' => $isOpenNow ? '還在營業中！' : '已休息囉！',
                'label'      => mb_substr($item->name, 0, 40, "utf-8"),
                'url'        => 'http://maps.google.com/?q=' .
                    "{$item->geometry->location->lat},{$item->geometry->location->lng}",
            ];
        });
    }
}

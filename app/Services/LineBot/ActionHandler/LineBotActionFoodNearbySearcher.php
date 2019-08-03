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
        $shops = collect([]);

        $nextPageToken = null;

        do {
            $response = $this->getDataFromGooglePlaceAPI($nextPageToken);

            $result = $response->results;

            $nextPageToken = isset($response->next_page_token)
                ? $response->next_page_token
                : null;

            $shops = $shops->merge($this->filterFoodTypeShops($result));
        } while (isset($nextPageToken) && count($shops) < env('GOOGLE_SEARCH_SHOPS_COUNT'));

        return array_key_exists('channelId', $this->payload)
            ? $this->formatShops($shops)->take(10)
            : $this->formatShops($shops);
    }

    private function getDataFromGooglePlaceAPI($nextPageToken)
    {
        return $this->placeApi->nearBySearchApi($nextPageToken);
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
            $shopDetailApi = $this->placeApi->getShopDetailApi($item->place_id);

            if ($shopDetailApi) {
                $detail = $shopDetailApi->result;
                $isOpenNow = null;
                $photoUrl = null;

                try {
                    if (isset($item->photos)) {
                        $photoUrl = $this->placeApi->getPhotoRefUrl($item->photos[0]->photo_reference);
                    } else {
                        $photoUrl = url('images/shop.png');
                    }

                    $isOpenNow = isset($detail->opening_hours)
                        ? $detail->opening_hours->open_now
                            ? '營業中！' : '休息囉！'
                        : '---';
                } catch (\Exception $e) {
                    \Log::info(__METHOD__." => ".$e);
                }

                return (object) [
                    'photo_url' => $photoUrl,
                    'website' => $detail->website ?? '',
                    'is_opening' => $isOpenNow,
                    'rating' => $item->rating,
                    'label' => mb_substr($item->name, 0, 40, "utf-8"),
                    'url' => "http://maps.google.com/?q=".
                        "{$item->geometry->location->lat},{$item->geometry->location->lng}",
                ];
            }
        });
    }
}

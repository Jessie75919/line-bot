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
        $nextPageTokenOrigin = 'abc';
        $shops = collect([]);

        while (isset($nextPageTokenOrigin) && count($shops) < 50) {
            [$result, $nextPageToken] = $this->getDataFromGooglePlaceAPI();
            $nextPageTokenOrigin = $nextPageToken;
            $shops = $shops->merge($this->filterFoodTypeShops($result));
        }

        return $this->formatShops($shops);
    }


    private function getDataFromGooglePlaceAPI()
    {
        $data = $this->placeApi->nearBySearchApi();

        return [
            $data->results,
            isset($data->next_page_token) ? $data->next_page_token : null,
        ];
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
            $photoUrl = null;

            try {
                if (isset($item->photos)) {
                    $photoUrl = $this->placeApi->getPhotoRefUrl($item->photos[0]->photo_reference);
                } else {
                    $photoUrl = url('images/shop.png');
                }

                $isOpenNow = isset($detail->opening_hours)
                    ? $detail->opening_hours->open_now
                        ? '還在營業中！' : '已休息囉！'
                    : '---';

            } catch (\Exception $e) {
                \Log::info(__METHOD__." => ".$e);
            }

            return (object) [
                'photo_url' => $photoUrl,
                'website' => $detail->website ?? '',
                'is_opening' => $isOpenNow,
                'label' => mb_substr($item->name, 0, 40, "utf-8"),
                'url' => "http://maps.google.com/?q=".
                    "{$item->geometry->location->lat},{$item->geometry->location->lng}",
            ];
        });
    }
}

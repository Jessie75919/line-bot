<?php

namespace App\Services\Google;

use App\Utilities\CurlTools;

class GooglePlaceApiService
{
    const PLACE_API_URL = 'https://maps.googleapis.com/maps/api/place';
    private $placeApiKey;
    private $payload;
    private $curlHelper;
    /** * @var string */
    private $maxprice = 300;

    public function __construct($placeApiKey)
    {
        $this->placeApiKey = $placeApiKey;
        $this->curlHelper = new CurlTools();
    }

    public function getShopDetailApi($placeId)
    {
        $url = self::PLACE_API_URL.'/details/json?';
        $payload = http_build_query([
            'placeid' => $placeId,
            'key' => $this->placeApiKey,
            'language' => config('google_api.place_api.language'),
        ]);

        $curlHelper = $this->curlHelper->get($url, $payload);
        if ($curlHelper->isSuccessful()) {
            return $curlHelper->getContents();
        }

        return null;
    }

    public function getPhotoRefUrl($photoRef)
    {
        $url = self::PLACE_API_URL.'/photo?';
        $payload = http_build_query([
            'maxwidth' => 300,
            'sensor' => false,
            'photoreference' => $photoRef,
            'key' => $this->placeApiKey,
        ]);
        return $url.$payload;
    }

    public function getPhotoRefApi(string $photoRef)
    {
        $url = self::PLACE_API_URL.'/photo?';
        $payload = http_build_query([
            'maxwidth' => 400,
            'sensor' => false,
            'photoreference' => $photoRef,
            'key' => $this->placeApiKey,
        ]);

        $curlHelper = $this->curlHelper->get($url, $payload);
        if ($curlHelper->isSuccessful()) {
            return $curlHelper->getContents();
        }

        return null;
    }

    /**
     * @param  null  $pageToken
     * @return mixed
     */
    public function nearBySearchApi($pageToken = null)
    {
        $url = self::PLACE_API_URL.'/nearbysearch/json?';
        $payload = http_build_query([
            'radius' => config('google_api.place_api.radius'),
            'language' => config('google_api.place_api.language'),
            'types' => config('google_api.place_api.types'),
            'key' => $this->placeApiKey,
            'location' => "{$this->payload['latitude']},{$this->payload['longitude']}",
            'pagetoken' => $pageToken,
        ]);

        $curlHelper = $this->curlHelper->get($url, $payload);

        if ($curlHelper->isSuccessful()) {
            return $curlHelper->getContents();
        }

        return null;
    }

    /**
     * @param  mixed  $payload
     * @return GooglePlaceApiService
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @param  mixed  $maxprice
     * @return GooglePlaceApiService
     */
    public function setMaxprice($maxprice)
    {
        $this->maxprice = $maxprice;
        return $this;
    }
}

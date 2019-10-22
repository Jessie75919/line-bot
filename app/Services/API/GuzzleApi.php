<?php

namespace App\Services\API;

use GuzzleHttp\Client;

class GuzzleApi
{
    /** @var Client */
    private $client;
    private $response;
    private $uri;

    /**
     * CurlTools constructor.
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        $this->client = new Client(['timeout' => 20.0]);
    }

    public function post($payload, $headers = [])
    {
        $payloadType = 'form_params';

        if (array_key_exists('Content-Type', $headers) &&
            $headers['Content-Type'] === 'application/json') {
            $payloadType = 'json';
        }

        $this->response =
            $this->client->post(
                $this->uri,
                [$payloadType => $payload, 'headers' => $headers]
            );
        return $this;
    }

    public function get($payload, $headers = [])
    {
        $this->response =
            $this->client->get($this->uri, ['query' => $payload, 'headers' => $headers]);
        return $this;
    }

    public function put($payload)
    {
        $this->response =
            $this->client->put($this->uri, ['form_params' => $payload]);
        return $this;
    }

    public function isSuccessful()
    {
        return isset($this->response) && $this->getStatusCode() === 200;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function getHeaders(): array
    {
        return $this->getResponse()->getHeaders();
    }

    public function getStatusCode()
    {
        return $this->getResponse()->getStatusCode();
    }

    public function getContents()
    {
        $contentType = $this->getHeaders()['Content-Type'][0];
        $contents = $this->getResponse()->getBody()->getContents();
        return strpos($contentType, 'application/json') !== false
            ? json_decode($contents)
            : $contents;
    }

    /**
     * @param  mixed  $uri
     * @return GuzzleApi
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }
}

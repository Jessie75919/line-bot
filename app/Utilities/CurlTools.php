<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018-12-25
 * Time: 12:53
 */

namespace App\Utilities;

use GuzzleHttp\Client;

class CurlTools
{
    /** @var Client */
    private $client;
    /** @var GuzzleHttp\Psr7\Response */
    private $response;
    private $uri;


    /**
     * CurlTools constructor.
     * @param $uri
     * @throws \InvalidArgumentException
     */
    public function __construct($uri = '')
    {
        $this->client = new Client(['timeout' => 10.0]);
        $this->uri = $uri;
    }


    public function post($payload, $headers = [])
    {
        $payloadType = 'form_params';

        if (array_key_exists('Content-Type', $headers) &&
            $headers['Content-Type'] === 'application/json') {
            $payloadType = 'json';
        }

        $this->response =
            $this->client->post($this->uri, [$payloadType => $payload, 'headers' => $headers]);
        return $this;
    }


    public function uploadPost($payload, $headers = [])
    {
        $this->response =
            $this->client->post($this->uri, ['body' => $payload, 'headers' => $headers]);
        return $this;
    }


    public function get($uri, $payload, $headers = [])
    {
        $this->response =
            $this->client->get($uri, ['query' => $payload, 'headers' => $headers]);
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
        return isset($this->response) && $this->response->getStatusCode() === 200;
    }


    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }


    public function getContents()
    {
        $contentType = $this->getResponse()->getHeaders()['Content-Type'][0];
        $contents = $this->getResponse()->getBody()->getContents();
        return \strpos($contentType, 'application/json') !== false
            ? json_decode($contents)
            : $contents;
    }
}

<?php


namespace App\Services\Pos;


use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use function array_merge;
use function config;
use function env;
use function now;

class JWTService
{
    private $code;
    private $token;


    /**
     * JWTService constructor.
     */
    public function __construct()
    {
        $this->token = [
            'iss' => env('API_URL'),
            'iat' => now()->timestamp,
        ];
    }


    public function encode()
    {
        if (!$this->token['exp']) {
            throw new Exception('Need To Add Expired Time First');
        }

        $this->code = JWT::encode($this->token, config('app.jwt_secret_key'));
        return $this;
    }


    public function setValidTime($expTime)
    {

        $this->token['exp'] = now()->addMinute($expTime)->timestamp;
        return $this;
    }


    public function setEncodeData($encodeData)
    {
        $this->token = array_merge($this->token, $encodeData);
        return $this;
    }


    public function decode($jwtCode)
    {
        try {
            $decoded = JWT::decode($jwtCode, config('app.jwt_secret_key'), ['HS256']);
            return $decoded;
        } catch (ExpiredException $e) {
            \Log::error($e);
            return $e;
        }
    }


    public function generateLink($path)
    {
        if (!$this->code) {
            throw new Exception('Need To encode First');
        }

        return config('app.url') . "/{$path}?code={$this->code}";
    }

}
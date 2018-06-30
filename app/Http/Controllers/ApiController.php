<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/6/26星期二
 * Time: 下午11:25
 */

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;

class ApiController extends Controller
{

    protected $statusCode = 200;


    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }


    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }


    /**
     * @param string $message
     * @return JsonResponse
     */
    public function respondNotFound($message = "Not Found!")
    {
        return $this->setStatusCode(HttpResponse::HTTP_NOT_FOUND)->respondWithError($message);
    }


    /**
     * @param string $message
     * @return JsonResponse
     */
    public function respondInternalError($message = "Internal Error!")
    {
        return $this->setStatusCode(HttpResponse::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);
    }


    /**
     * @param       $data
     * @param array $headers
     * @return JsonResponse
     */
    public function respond($data, $headers = [])
    {
        return Response::json($data, $this->getStatusCode(), $headers);
    }


    /**
     * @param $message
     * @return JsonResponse
     */
    public function respondWithError($message)
    {
        return $this->respond([
            'error' => [
                'message'     => $message,
                'status_code' => $this->getStatusCode()
            ]
        ]);
    }


    /**
     * @param $message
     * @return JsonResponse
     */
    protected function respondCreated($message): JsonResponse
    {
        return $this->setStatusCode(HttpResponse::HTTP_ACCEPTED)
                    ->respond([
                        'message' => $message
                    ]);
    }
}
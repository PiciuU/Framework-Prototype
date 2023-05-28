<?php

namespace Services\Support;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class Response {

    public static function success($message, $data = [], $status = 200) {
        $response = new JsonResponse(
            [
                'success' => true,
                'data' => $data,
                'message' => $message,
            ],
            $status
        );
        self::send($response);
    }

    public static function failure($message, $status = 422) {
        $response = new JsonResponse(
            [
                'success' => false,
                'message' => $message,
            ],
            $status
        );
        self::send($response);
    }

    protected static function send(JsonResponse $response) {
        $response->sendHeaders();
        $response->sendContent();
        exit();
    }
}
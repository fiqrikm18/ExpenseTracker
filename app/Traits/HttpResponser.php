<?php

namespace App\Traits;

trait HttpResponser
{

    protected function successResponse($data, $message = '', $code = 200) {
        return response()->json([
            'code' => $code,
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse($message, $code = 400) {
        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => $message,
            'data' => [],
        ], $code);
    }

}

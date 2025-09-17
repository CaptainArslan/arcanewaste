<?php

namespace App\Http\Controllers;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class Controller
{
    public function sendSuccessResponse(array|Arrayable|JsonSerializable|null $data, string $message = '', int $code = 200, $headers = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code, $headers);
    }

    public function sendErrorResponse(string $message = '', int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}

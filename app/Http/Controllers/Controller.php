<?php

namespace App\Http\Controllers;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class Controller
{
    public function sendSuccessResponse(
        JsonResource|array|Arrayable|JsonSerializable|null $data,
        string $message = '',
        int $code = 200,
        array $headers = []
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data instanceof JsonResource ? $data->response()->getData(true) : $data,
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

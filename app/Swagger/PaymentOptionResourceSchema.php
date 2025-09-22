<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="PaymentOptionResource",
 *     type="object",
 *     title="Payment Option",
 *     description="Payment option resource response",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Payment Option"),
 *     @OA\Property(property="type", type="string", example="type"),
 *     @OA\Property(property="percentage", type="number", example=100)
 * )
 */

class PaymentOptionResourceSchema
{
    // This class can remain empty; it's just for Swagger annotations
}
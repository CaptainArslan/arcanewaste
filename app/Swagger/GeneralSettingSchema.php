<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="GeneralSettingResource",
 *     type="object",
 *     title="General Setting",
 *     description="General setting resource response",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="key", type="string", example="key"),
 *     @OA\Property(property="value", type="string", example="value"),
 *     @OA\Property(property="type", type="string", example="type")
 * )
 */
class GeneralSettingSchema
{
    // This class can remain empty; it's just for Swagger annotations
}

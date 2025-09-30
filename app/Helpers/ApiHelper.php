<?php

namespace App\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApiHelper
{
    public static function successResponse($paginate, $data, $message = 'Success', $status = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        // If data is paginated (LengthAwarePaginator)
        if ($paginate) {
            $response['data'] = $data->items(); // raw items (models/arrays)
            $response['meta'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'links' => [
                    'first' => $data->url(1),
                    'last' => $data->url($data->lastPage()),
                    'prev' => $data->previousPageUrl(),
                    'next' => $data->nextPageUrl(),
                ],
            ];
        } else {
            // For collections, resources, or single items
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }
}

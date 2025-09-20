<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckJsonHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $errors = [];

        // Validate Content-Type header for methods with body
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            if (!$request->hasHeader('Content-Type')) {
                $errors[] = 'Missing Content-Type header';
            } elseif (stripos($request->header('Content-Type'), 'application/json') === false) {
                $errors[] = 'Invalid Content-Type. Expected application/json';
            }
        }

        // Validate Accept header for all requests
        if (!$request->hasHeader('Accept')) {
            $errors[] = 'Missing Accept header';
        } elseif (stripos($request->header('Accept'), 'application/json') === false) {
            $errors[] = 'Invalid Accept header. Expected application/json';
        }

        // If any errors, return JSON response as single string
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => implode('. ', $errors) . '.',
            ], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}

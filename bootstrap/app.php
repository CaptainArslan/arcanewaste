<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            // CheckJsonHeaders::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        // Customize JSON response
        $exceptions->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = match (true) {
                    $e instanceof \Exception => Response::HTTP_BAD_REQUEST,
                    $e instanceof ValidationException => Response::HTTP_UNPROCESSABLE_ENTITY,
                    $e instanceof NotFoundHttpException => Response::HTTP_NOT_FOUND,
                    $e instanceof MethodNotAllowedHttpException => Response::HTTP_METHOD_NOT_ALLOWED,
                    $e instanceof AuthenticationException => Response::HTTP_UNAUTHORIZED,
                    $e instanceof AuthorizationException => Response::HTTP_FORBIDDEN,
                    $e instanceof BadRequestHttpException => Response::HTTP_BAD_REQUEST,
                    default => Response::HTTP_INTERNAL_SERVER_ERROR,
                };

                $message = match (true) {
                    $e instanceof \Exception => $e->getMessage(),
                    $e instanceof ValidationException => implode(', ', $e->errors()),
                    $e instanceof NotFoundHttpException => 'Resource not found',
                    $e instanceof MethodNotAllowedHttpException => 'Method not allowed',
                    $e instanceof AuthenticationException => 'Unauthorized',
                    $e instanceof AuthorizationException => 'Forbidden',
                    $e instanceof BadRequestHttpException => 'Bad request',
                    default => 'Something went wrong',
                };

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => [
                        $e->getMessage(),
                    ],
                ], $statusCode);
            }
        });
    })->create();

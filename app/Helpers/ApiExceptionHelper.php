<?php

namespace App\Helpers;

use App\Contracts\Exceptions\CustomExceptionInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class ApiExceptionHelper
{
    public function __construct(
        protected Throwable $throwable,
        protected Request $request,
    ) {}

    public function handleAPIException(): JsonResponse
    {
        return match (true) {
            app()->hasDebugModeEnabled() && $this->throwable instanceof ModelNotFoundException => $this->modelNotFoundError(),
            app()->hasDebugModeEnabled() && $this->throwable instanceof MethodNotAllowedHttpException => $this->methodNotAllowedError(),
            app()->hasDebugModeEnabled() && $this->throwable instanceof RouteNotFoundException => $this->routeNotFoundError(),
            app()->hasDebugModeEnabled() && $this->throwable instanceof NotFoundHttpException => $this->dataNotFoundError(),
            $this->throwable instanceof ValidationException => $this->validationError(),
            $this->throwable instanceof AuthenticationException => $this->authenticationError(),
            $this->throwable instanceof AuthorizationException => $this->authorizationError(),
            $this->throwable instanceof CustomExceptionInterface => $this->customError($this->throwable),
            default => $this->serverError(),
        };
    }

    protected function modelNotFoundError(): JsonResponse
    {
        return response()->apiError(
            messages: 'Model not found.',
            responseCode: Response::HTTP_NOT_FOUND,
        );
    }

    protected function methodNotAllowedError(): JsonResponse
    {
        return response()->apiError(
            messages: 'The request method is not allowed.',
            responseCode: Response::HTTP_METHOD_NOT_ALLOWED,
        );
    }

    protected function routeNotFoundError(): JsonResponse
    {
        return response()->apiError(
            messages: 'Route not found.',
            responseCode: Response::HTTP_NOT_FOUND,
        );
    }

    protected function dataNotFoundError(): JsonResponse
    {
        return response()->apiError(
            messages: 'Data not found.',
            responseCode: Response::HTTP_NOT_FOUND,
        );
    }

    protected function validationError(): JsonResponse
    {
        /** @var ValidationException $e */
        $e = $this->throwable;

        return response()->apiError(
            messages: array_reduce($e->errors(), function (mixed $a, mixed $b) {
                return array_merge($a, $b);
            }, []),
            responseCode: Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    protected function authenticationError(): JsonResponse
    {
        return response()->apiError(
            responseCode: Response::HTTP_UNAUTHORIZED,
        );
    }

    protected function authorizationError(): JsonResponse
    {
        return response()->apiError(
            messages: 'Unauthorized access.',
            responseCode: Response::HTTP_FORBIDDEN,
        );
    }

    protected function serverError(): JsonResponse
    {
        return response()->apiError(
            messages: 'Unknown error.',
            responseCode: Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }

    protected function customError(Throwable $throwable): JsonResponse
    {
        return response()->apiError(
            messages: $throwable->getMessage(),
            responseCode: $throwable->getCode(),
        );
    }
}

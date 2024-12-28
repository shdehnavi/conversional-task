<?php

namespace App\Providers;

use App\Http\Resources\PaginationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ApiResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Response::macro('apiSuccess', [$this, 'successResponse']);
        Response::macro('apiError', [$this, 'errorResponse']);
    }

    public static function successResponse(
        ResourceCollection | JsonResource | Collection | array | null $data = null,
        array | string $messages = [],
        bool $hasPagination = false,
        int $responseCode = HttpResponse::HTTP_OK,
    ): JsonResponse {
        $response = [
            'success' => true,
            'messages' => is_array($messages) ? $messages : [$messages],
            'data' => $data,
        ];

        if ($hasPagination) {
            $pagination = new PaginationResource($data);

            $response['pagination'] = $pagination;
        }

        return response()->json($response, $responseCode);
    }

    public static function errorResponse(
        array | string $messages = [],
        int $responseCode = HttpResponse::HTTP_BAD_REQUEST
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'messages' => is_array($messages) ? $messages : [$messages],
        ], $responseCode);
    }
}

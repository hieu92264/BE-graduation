<?php

namespace App\Common\Traits;

use App\Common\Constants\HttpStatus;
use App\Common\Constants\ResponseStatus;
use App\Common\Helpers\TranslationHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponseTrait
{
    protected function DataResponse(
        bool $success = false,
        string $message = 'messages.common.success',
        int $code = HttpStatus::OK,
        ?array $data = []
    ): JsonResponse {
        $payload = [
            'status' => $success ? ResponseStatus::SUCCESS : ResponseStatus::ERROR,
            'message' => TranslationHelper::translate($message),
            'statusCode' => $code,
            'path' => request()->path(),
            'timestamp' => now(),
        ];

        if ($success) {
            $payload['data'] = $data ?? [];
        } else {
            $payload['errors'] = $data ?? null;
        }

        return response()->json($payload, $code);
    }

    protected function successResponse(
        array $data = [],
        string $message = 'messages.common.success',
        int $code = HttpStatus::OK
    ): JsonResponse {
        return response()->json([
            'status' => ResponseStatus::SUCCESS,
            'data' => $data,
            'message' => TranslationHelper::translate($message),
            'statusCode' => $code,
            'path' => request()->path(),
            'timestamp' => now(),
        ], $code);
    }

    protected function failedResponse(
        string $message = 'messages.common.error',
        int $code = HttpStatus::BAD_REQUEST,
        mixed $errors = null
    ): JsonResponse {
        return response()->json([
            'status' => ResponseStatus::ERROR,
            'errors' => $errors,
            'message' => TranslationHelper::translate($message),
            'statusCode' => $code,
            'path' => request()->path(),
            'timestamp' => now(),
        ], $code);
    }

    protected function paginate(
        LengthAwarePaginator $paginator,
        string $message = 'messages.common.success',
        int $code = HttpStatus::OK,
        array $extraMeta = []
    ): JsonResponse {
        return response()->json([
            'status' => ResponseStatus::SUCCESS,
            'message' => TranslationHelper::translate($message),
            'statusCode' => $code,
            'path' => request()->path(),
            'timestamp' => now(),
            'data' => $paginator->items(),
            'meta' => array_merge([
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ], $extraMeta),
        ], $code);
    }
}

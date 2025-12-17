<?php

namespace App\Common\Traits;

use App\Common\Constants\HttpStatus;
use App\Common\Constants\ResponseStatus;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function DataResponse(
        string $status,
        string $message,
        mixed $data = null,
        mixed $errors = null,
        int $statusCode = HttpStatus::OK
    ): JsonResponse {
        $metaData = [
            'status' => $status,
            'message' => $message
        ];

        if ($status === ResponseStatus::SUCCESS && $data !== null) {
            $metaData['data'] = $data;
        }

        if ($status === 'error' && $errors !== null) {
            $responseStructure['errors'] = $errors;
        }

        if ($status === 'error' && !isset($responseStructure['errors'])) {
            $responseStructure['errors'] = null;
        }

        return response()->json($metaData, $statusCode);
    }
}

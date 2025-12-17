<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        return $this->DataResponse(
            status: '200',
            message: 'hello',
            statusCode: HttpStatus::OK
        );
    }
}

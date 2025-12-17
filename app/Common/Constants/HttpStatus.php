<?php

namespace App\Common\Constants;

use Symfony\Component\HttpFoundation\Response;

class HttpStatus
{
    public const OK = Response::HTTP_OK; //200
    public const CREATED = Response::HTTP_CREATED; // 201

    public const BAD_REQUEST = Response::HTTP_BAD_REQUEST; // 400
    public const UNAUTHORIZED = Response::HTTP_UNAUTHORIZED; // 401
    public const FORBIDDEN = Response::HTTP_FORBIDDEN; // 403
    public const NOT_FOUND = Response::HTTP_NOT_FOUND; // 404
    public const UNPROCESSABLE_ENTITY = Response::HTTP_UNPROCESSABLE_ENTITY; // 422
    public const TOO_MANY_REQUESTS = Response::HTTP_TOO_MANY_REQUESTS; // 429

    // 5xx Server Error
    public const INTERNAL_SERVER_ERROR = Response::HTTP_INTERNAL_SERVER_ERROR; // 500
}

<?php

namespace App\Http\Controllers;

use App\Common\Traits\ApiResponseTrait;

abstract class Controller
{
    use ApiResponseTrait;

    protected function getUserName()
    {
        return auth()->user()?->username;
    }
}

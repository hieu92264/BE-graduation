<?php

namespace App\Http\Interfaces;

use App\Http\_base\BaseServiceInterface;

interface PermissionServiceInterface extends BaseServiceInterface
{
    public function getPermissionOptions(): array;
}

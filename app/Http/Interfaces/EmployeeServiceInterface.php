<?php

namespace App\Http\Interfaces;

use App\Http\_base\BaseServiceInterface;

interface  EmployeeServiceInterface extends BaseServiceInterface
{
    public function getUserOptions(?int $userId): array;
}

<?php

namespace App\Http\Services;

use App\Http\_base\BaseService;
use App\Http\Interfaces\EmployeeServiceInterface;
use App\Models\Employee;

class EmployeeService extends BaseService implements EmployeeServiceInterface
{

    protected function getModel(): string
    {
        // TODO: Implement getModel() method.
        return Employee::class;
    }
}

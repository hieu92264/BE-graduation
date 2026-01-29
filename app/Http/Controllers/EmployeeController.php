<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Http\Interfaces\EmployeeServiceInterface;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function __construct(protected EmployeeServiceInterface $employeeService)
    {
    }

    public function getAll(): JsonResponse
    {
        $result = $this->employeeService->getAll()->toArray();

        return $this->DataResponse(true, 'success', HttpStatus::OK, $result);
    }

    public function create()
    {
    
    }
}

<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Http\Interfaces\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserServiceInterface $userService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->userService->getAll();
        return $this->successResponse(
            $data->toArray(),
            'Success',
            HttpStatus::OK
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->userService->create($request->all());
        return $this->successResponse($data->toArray(), 'Success', HttpStatus::CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $this->userService->update($id, $request->all());
        return $this->successResponse($data->toArray(), 'Success', HttpStatus::OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->userService->delete($id);
        return $this->successResponse([], 'Deleted successfully', HttpStatus::OK);
    }

    public function getPermissions(string $id): JsonResponse
    {
        $data = $this->userService->getPermissions($id);
        return $this->successResponse($data, 'Success', HttpStatus::OK);
    }

    public function syncPermissions(Request $request, string $id): JsonResponse
    {
        $data = $this->userService->syncPermissions($id, $request->input('permission_ids', []));
        return $this->successResponse($data, 'Success', HttpStatus::OK);
    }
}

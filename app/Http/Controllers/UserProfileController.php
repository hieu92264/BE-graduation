<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Http\Interfaces\UserProfileServiceInterface;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function __construct(protected UserProfileServiceInterface $userProfileService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->userProfileService->getAll();
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
        $data = $this->userProfileService->create($request->all());
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
        $data = $this->userProfileService->update($id, $request->all());
        return $this->successResponse($data->toArray(), 'Success', HttpStatus::OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->userProfileService->delete($id);
        return $this->successResponse([], 'Deleted successfully', HttpStatus::NO_CONTENT);
    }
}

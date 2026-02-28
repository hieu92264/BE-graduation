<?php

namespace App\Http\Controllers;

use App\Common\Traits\ApiResponseTrait;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Room::all()->toArray();
        return $this->successResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $room = Room::create($data);
        return $this->successResponse($room);
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
        $data = $request->all();
        $room = Room::findOrFail($id);
        $room->update($data);
        return $this->successResponse($room);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $room = Room::findOrFail($id);
        $room->delete();
        return $this->successResponse([], 'Room deleted successfully');
    }

    public function featured(): JsonResponse
    {
        $rooms = Room::with(['postType', 'photos'])
            ->whereHas('postType', function ($query) {
                $query->where('priority', '>', 0);
            })
            ->join('post_types', 'rooms.post_type_id', '=', 'post_types.id')
            ->orderByDesc('post_types.priority')
            ->orderByDesc('rooms.created_at')
            ->select('rooms.*')
            ->take(10)
            ->get();

        return $this->successResponse($rooms->toArray());
    }
}

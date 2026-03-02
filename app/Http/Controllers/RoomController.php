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

    public function roomDetail(string $id): JsonResponse
    {
        $room = Room::query()
            ->with([
                'category:id,code,name,slug,sort_order,remark,isactive,created_at,updated_at',
                'postType:id,code,name,priority,default_days,price,remark,isactive,created_at,updated_at',
                'city:id,code,name,sort_order,isactive,created_at,updated_at',
                'district:id,city_id,code,name,sort_order,isactive,created_at,updated_at',
                'ward:id,district_id,code,name,sort_order,isactive,created_at,updated_at',
                'photos' => function ($q) {
                    $q->orderByDesc('is_cover')->orderBy('sort_order')->orderBy('id');
                },
                'owner:id,username,email',
                'owner.profile:id,user_id,full_name,phone_number,avatar_url,address,zalo,facebook,user_type,remark,isactive,created_at,updated_at',
            ])
            ->whereKey((int)$id)
            ->firstOrFail();
        return $this->successResponse($room->toArray());
    }

    public function createContact()
    {

    }
}

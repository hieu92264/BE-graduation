<?php

namespace App\Http\Controllers;

use App\Common\Traits\ApiResponseTrait;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        $data = Room::query()
            ->with([
                'category:id,code,name,slug,sort_order,remark,isactive,created_at,updated_at',
                'postType:id,code,name,priority,default_days,price,remark,isactive,created_at,updated_at',
                'city:id,code,name,sort_order,isactive,created_at,updated_at',
                'district:id,city_id,code,name,sort_order,isactive,created_at,updated_at',
                'ward:id,district_id,code,name,sort_order,isactive,created_at,updated_at',
                'photos' => function ($q) {
                    $q->orderByDesc('is_cover')->orderBy('sort_order')->orderBy('id');
                },
            ])
            ->latest('id')
            ->get()
            ->toArray();

        return $this->successResponse($data);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $room = Room::create($data);

        return $this->successResponse($room->toArray());
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->all();
        $room = Room::findOrFail($id);
        $room->update($data);

        return $this->successResponse($room->toArray());
    }

    public function destroy(string $id): JsonResponse
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return $this->successResponse([], 'Room deleted successfully');
    }

    public function featured(): JsonResponse
    {
        $rooms = Room::query()
            ->with([
                'postType:id,code,name,priority,default_days,price,remark,isactive,created_at,updated_at',
                'photos' => function ($q) {
                    $q->orderByDesc('is_cover')->orderBy('sort_order')->orderBy('id');
                },
                'city:id,code,name,sort_order,isactive,created_at,updated_at',
                'district:id,city_id,code,name,sort_order,isactive,created_at,updated_at',
                'ward:id,district_id,code,name,sort_order,isactive,created_at,updated_at',
            ])
            ->whereHas('postType', function ($query) {
                $query->where('priority', '>', 0);
            })
            ->join('post_types', 'rooms.post_type_id', '=', 'post_types.id')
            ->orderByDesc('post_types.priority')
            ->orderByDesc('rooms.created_at')
            ->select('rooms.*')
            ->take(10)
            ->get()
            ->toArray();

        return $this->successResponse($rooms);
    }

    public function search(Request $request): JsonResponse
    {
        $query = Room::query()
            ->with([
                'category:id,code,name,slug,sort_order,remark,isactive,created_at,updated_at',
                'postType:id,code,name,priority,default_days,price,remark,isactive,created_at,updated_at',
                'city:id,code,name,sort_order,isactive,created_at,updated_at',
                'district:id,city_id,code,name,sort_order,isactive,created_at,updated_at',
                'ward:id,district_id,code,name,sort_order,isactive,created_at,updated_at',
                'photos' => function ($q) {
                    $q->orderByDesc('is_cover')->orderBy('sort_order')->orderBy('id');
                },
            ]);

        if ($request->filled('keyword')) {
            $keyword = trim((string) $request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->category_id);
        }

        if ($request->filled('post_type_id')) {
            $query->where('post_type_id', (int) $request->post_type_id);
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', (int) $request->city_id);
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', (int) $request->district_id);
        }

        if ($request->filled('ward_id')) {
            $query->where('ward_id', (int) $request->ward_id);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        if ($request->filled('min_area')) {
            $query->where('area', '>=', (float) $request->min_area);
        }

        if ($request->filled('max_area')) {
            $query->where('area', '<=', (float) $request->max_area);
        }

        $query
            ->leftJoin('post_types', 'rooms.post_type_id', '=', 'post_types.id')
            ->orderByDesc('post_types.priority')
            ->orderByDesc('rooms.created_at')
            ->select('rooms.*');

        $perPage = (int) ($request->get('per_page', 12));
        $rooms = $query->paginate($perPage);

        return $this->paginate($rooms, 'Search rooms successfully');
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
            ->whereKey((int) $id)
            ->firstOrFail();

        return $this->successResponse($room->toArray());
    }

    public function createContact()
    {
    }
}

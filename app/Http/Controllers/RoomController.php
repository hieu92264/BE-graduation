<?php

namespace App\Http\Controllers;

use App\Common\Traits\ApiResponseTrait;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
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
            ])
            ->where('rooms.isactive', 'Y')
            ->where('post_status', 'approved');

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

        $sort = (string) $request->get('sort', 'latest');

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'price_desc':
                $query->orderByDesc('price');
                break;
            case 'area_asc':
                $query->orderBy('area');
                break;
            case 'area_desc':
                $query->orderByDesc('area');
                break;
            default:
                $query
                    ->leftJoin('post_types', 'rooms.post_type_id', '=', 'post_types.id')
                    ->orderByDesc('post_types.priority')
                    ->orderByDesc('rooms.created_at')
                    ->select('rooms.*');
                break;
        }

        $perPage = max(1, min((int) $request->get('per_page', 12), 50));
        $rooms = $query->paginate($perPage)->through(function ($room) {
            return $this->transformRoom($room);
        });

        return $this->paginate($rooms, 'Search rooms successfully');
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
            ->where('rooms.isactive', 'Y')
            ->where('rooms.post_status', 'approved')
            ->join('post_types', 'rooms.post_type_id', '=', 'post_types.id')
            ->orderByDesc('post_types.priority')
            ->orderByDesc('rooms.created_at')
            ->select('rooms.*')
            ->take(10)
            ->get()
            ->map(fn($room) => $this->transformRoom($room))
            ->toArray();

        return $this->successResponse($rooms);
    }

    public function showPublic(string $slugOrId): JsonResponse
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
            ->where(function ($q) use ($slugOrId) {
                if (is_numeric($slugOrId)) {
                    $q->whereKey((int) $slugOrId);
                }
                $q->orWhere('slug', $slugOrId);
            })
            ->where('isactive', 'Y')
            ->where('post_status', 'approved')
            ->firstOrFail();

        return $this->successResponse($this->transformRoom($room));
    }

    private function transformRoom(Room $room): array
    {
        $data = $room->toArray();

        $data['post_type'] = $data['postType'] ?? null;

        if (! empty($data['photos'])) {
            $data['photos'] = collect($data['photos'])->map(function ($photo) {
                if (! empty($photo['photo_url']) && ! str_starts_with($photo['photo_url'], 'http')) {
                    $photo['photo_url'] = asset('storage/' . ltrim($photo['photo_url'], '/'));
                }

                return $photo;
            })->values()->toArray();
        }

        if (! empty($data['owner']['profile']['avatar_url']) && ! str_starts_with($data['owner']['profile']['avatar_url'], 'http')) {
            $data['owner']['profile']['avatar_url'] = asset('storage/' . ltrim($data['owner']['profile']['avatar_url'], '/'));
        }

        return $data;
    }
}

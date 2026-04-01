<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Common\Traits\ApiResponseTrait;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LandlordRoomController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = Room::query()
            ->withoutGlobalScopes()
            ->where('owner_user_id', $user->id)
            ->with([
                'category:id,code,name,slug,sort_order,remark,isactive,created_at,updated_at',
                'postType:id,code,name,priority,default_days,price,remark,isactive,created_at,updated_at',
                'city:id,code,name,sort_order,isactive,created_at,updated_at',
                'district:id,city_id,code,name,sort_order,isactive,created_at,updated_at',
                'ward:id,district_id,code,name,sort_order,isactive,created_at,updated_at',
                'photos' => function ($q) {
                    $q->orderByDesc('is_cover')
                        ->orderBy('sort_order')
                        ->orderBy('id');
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

        if ($request->filled('booking_status')) {
            $query->where('booking_status', (string) $request->booking_status);
        }

        if ($request->filled('isactive')) {
            $query->where('isactive', (string) $request->isactive);
        }

        $sort = (string) $request->get('sort', 'latest');

        switch ($sort) {
            case 'oldest':
                $query->oldest('id');
                break;
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
                $query->latest('id');
                break;
        }

        $perPage = max(1, min((int) $request->get('per_page', 10), 50));

        $rooms = $query->paginate($perPage)->through(function (Room $room) {
            return $this->transformRoom($room);
        });

        return $this->paginate($rooms, 'Lấy danh sách phòng của chủ trọ thành công');
    }

    public function show(int $id): JsonResponse
    {
        $room = $this->findOwnedRoom($id);

        $room->load([
            'category:id,code,name,slug,sort_order,remark,isactive,created_at,updated_at',
            'postType:id,code,name,priority,default_days,price,remark,isactive,created_at,updated_at',
            'city:id,code,name,sort_order,isactive,created_at,updated_at',
            'district:id,city_id,code,name,sort_order,isactive,created_at,updated_at',
            'ward:id,district_id,code,name,sort_order,isactive,created_at,updated_at',
            'photos' => function ($q) {
                $q->orderByDesc('is_cover')
                    ->orderBy('sort_order')
                    ->orderBy('id');
            },
        ]);

        return $this->successResponse(
            $this->transformRoom($room),
            'Lấy chi tiết phòng thành công'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePayload($request);

        DB::beginTransaction();

        try {
            $user = auth()->user();
            $slug = $this->makeUniqueSlug($validated['slug'] ?? $validated['title']);

            $room = Room::query()
                ->withoutGlobalScopes()
                ->create([
                    'isactive' => $validated['isactive'] ?? 'Y',
                    'owner_user_id' => $user->id,
                    'category_id' => $validated['category_id'] ?? null,
                    'post_type_id' => $validated['post_type_id'] ?? null,
                    'city_id' => $validated['city_id'] ?? null,
                    'district_id' => $validated['district_id'] ?? null,
                    'ward_id' => $validated['ward_id'] ?? null,
                    'title' => $validated['title'],
                    'slug' => $slug,
                    'address' => $validated['address'] ?? null,
                    'price' => $validated['price'] ?? 0,
                    'area' => $validated['area'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'booking_status' => $validated['booking_status'] ?? 'pending',
                    'post_status' => 'pending',
                    'moderated_by' => null,
                    'moderated_at' => null,
                    'moderation_note' => null,
                ]);

            DB::commit();

            $room->load([
                'category',
                'postType',
                'city',
                'district',
                'ward',
                'photos' => function ($q) {
                    $q->orderByDesc('is_cover')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ]);

            return $this->successResponse(
                $this->transformRoom($room),
                'Tạo phòng thành công',
                HttpStatus::CREATED
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failedResponse(
                'Failed to create room',
                HttpStatus::BAD_REQUEST,
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $room = $this->findOwnedRoom($id);
        $validated = $this->validatePayload($request, $room->id);

        DB::beginTransaction();

        try {
            $slug = $validated['slug'] ?? $room->slug;

            if ($slug !== $room->slug) {
                $slug = $this->makeUniqueSlug($slug, $room->id);
            }

            $room->update([
                'isactive' => $validated['isactive'] ?? $room->isactive,
                'category_id' => $validated['category_id'] ?? null,
                'post_type_id' => $validated['post_type_id'] ?? null,
                'city_id' => $validated['city_id'] ?? null,
                'district_id' => $validated['district_id'] ?? null,
                'ward_id' => $validated['ward_id'] ?? null,
                'title' => $validated['title'],
                'slug' => $slug,
                'address' => $validated['address'] ?? null,
                'price' => $validated['price'] ?? 0,
                'area' => $validated['area'] ?? null,
                'description' => $validated['description'] ?? null,
                'booking_status' => $validated['booking_status'] ?? $room->booking_status,

                'post_status' => 'pending',
                'moderated_by' => null,
                'moderated_at' => null,
                'moderation_note' => null,
            ]);

            DB::commit();

            $room->load([
                'category',
                'postType',
                'city',
                'district',
                'ward',
                'photos' => function ($q) {
                    $q->orderByDesc('is_cover')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ]);

            return $this->successResponse(
                $this->transformRoom($room),
                'Cập nhật phòng thành công'
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failedResponse(
                'Failed to update room',
                HttpStatus::BAD_REQUEST,
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $room = $this->findOwnedRoom($id);

        DB::beginTransaction();

        try {
            $room->delete();

            DB::commit();

            return $this->successResponse([], 'Xóa phòng thành công');
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failedResponse(
                'Failed to delete room',
                HttpStatus::BAD_REQUEST,
                [
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

    private function findOwnedRoom(int $id): Room
    {
        return Room::query()
            ->withoutGlobalScopes()
            ->where('owner_user_id', auth()->id())
            ->findOrFail($id);
    }

    private function validatePayload(Request $request, ?int $roomId = null): array
    {
        return $request->validate([
            'isactive' => ['nullable', 'in:Y,N'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'post_type_id' => ['nullable', 'integer', 'exists:post_types,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'ward_id' => ['nullable', 'integer', 'exists:wards,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:300',
                Rule::unique('rooms', 'slug')->ignore($roomId),
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'area' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'booking_status' => ['nullable', 'in:pending,confirmed,available,occupied'],
        ]);
    }

    private function makeUniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        $base = $base !== '' ? $base : 'room';

        $slug = $base;
        $counter = 1;

        while (
            Room::query()
                ->withoutGlobalScopes()
                ->when($ignoreId, function ($q) use ($ignoreId) {
                    $q->where('id', '!=', $ignoreId);
                })
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function transformRoom(Room $room): array
    {
        $data = $room->toArray();

        $data['post_type'] = $data['postType'] ?? null;

        if (! empty($data['photos'])) {
            $data['photos'] = collect($data['photos'])
                ->map(function ($photo) {
                    $photo['photo_path'] = $photo['photo_url'] ?? null;

                    if (! empty($photo['photo_url']) && ! str_starts_with($photo['photo_url'], 'http')) {
                        $photo['photo_url'] = asset('storage/'.ltrim($photo['photo_url'], '/'));
                    }

                    return $photo;
                })
                ->values()
                ->toArray();
        }

        return $data;
    }
}

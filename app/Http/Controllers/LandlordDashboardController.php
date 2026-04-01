<?php

namespace App\Http\Controllers;

use App\Common\Traits\ApiResponseTrait;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LandlordDashboardController extends Controller
{
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        $user = auth()->user();

        $baseQuery = Room::query()
            ->withoutGlobalScopes()
            ->where('owner_user_id', $user->id);

        $totalRooms = (clone $baseQuery)->count();

        $activeRooms = (clone $baseQuery)
            ->where('isactive', 'Y')
            ->count();

        $inactiveRooms = (clone $baseQuery)
            ->where('isactive', 'N')
            ->count();

        $availableRooms = (clone $baseQuery)
            ->where('booking_status', 'available')
            ->count();

        $occupiedRooms = (clone $baseQuery)
            ->where('booking_status', 'occupied')
            ->count();

        $pendingRooms = (clone $baseQuery)
            ->where('booking_status', 'pending')
            ->count();

        $totalValue = (clone $baseQuery)->sum('price');

        $roomsByStatus = (clone $baseQuery)
            ->select('booking_status', DB::raw('COUNT(*) as total'))
            ->groupBy('booking_status')
            ->get()
            ->map(fn ($item) => [
                'status' => $item->booking_status,
                'total' => (int) $item->total,
            ])
            ->values()
            ->toArray();

        $latestRooms = (clone $baseQuery)
            ->with([
                'postType:id,code,name,priority,default_days,price,remark,isactive,created_at,updated_at',
                'photos' => function ($q) {
                    $q->orderByDesc('is_cover')->orderBy('sort_order')->orderBy('id');
                },
                'city:id,code,name,sort_order,isactive,created_at,updated_at',
                'district:id,city_id,code,name,sort_order,isactive,created_at,updated_at',
                'ward:id,district_id,code,name,sort_order,isactive,created_at,updated_at',
            ])
            ->latest('id')
            ->take(5)
            ->get()
            ->map(fn (Room $room) => $this->transformRoom($room))
            ->toArray();

        return $this->successResponse([
            'summary' => [
                'total_rooms' => $totalRooms,
                'active_rooms' => $activeRooms,
                'inactive_rooms' => $inactiveRooms,
                'available_rooms' => $availableRooms,
                'occupied_rooms' => $occupiedRooms,
                'pending_rooms' => $pendingRooms,
                'total_value' => (float) $totalValue,
            ],
            'rooms_by_status' => $roomsByStatus,
            'latest_rooms' => $latestRooms,
        ], 'Lấy dữ liệu dashboard chủ trọ thành công');
    }

    private function transformRoom(Room $room): array
    {
        $data = $room->toArray();

        $data['post_type'] = $data['postType'] ?? null;

        if (!empty($data['photos'])) {
            $data['photos'] = collect($data['photos'])->map(function ($photo) {
                if (!empty($photo['photo_url']) && !str_starts_with($photo['photo_url'], 'http')) {
                    $photo['photo_url'] = asset('storage/' . ltrim($photo['photo_url'], '/'));
                }
                return $photo;
            })->values()->toArray();
        }

        return $data;
    }
}

<?php

namespace App\Http\Controllers;

use App\Common\Traits\ApiResponseTrait;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminRoomModerationController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Room::query()
            ->withoutGlobalScopes()
            ->with([
                'owner:id,username,email',
                'owner.profile:id,user_id,full_name,phone_number',
                'category:id,name',
                'postType:id,name,priority',
                'city:id,name',
                'district:id,name',
                'ward:id,name',
                'photos' => function ($q) {
                    $q->orderByDesc('is_cover')->orderBy('sort_order')->orderBy('id');
                },
            ]);

        if ($request->filled('keyword')) {
            $keyword = trim((string) $request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('post_status')) {
            $query->where('post_status', (string) $request->post_status);
        }

        if ($request->filled('owner_user_id')) {
            $query->where('owner_user_id', (int) $request->owner_user_id);
        }

        $perPage = max(1, min((int) $request->get('per_page', 10), 50));

        $rooms = $query
            ->latest('id')
            ->paginate($perPage)
            ->through(fn (Room $room) => $this->transformRoom($room));

        return $this->paginate($rooms, 'Fetched moderation rooms successfully');
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'post_status' => ['required', 'in:approved,rejected,hidden,pending'],
            'moderation_note' => ['nullable', 'string'],
        ]);

        $room = Room::query()
            ->withoutGlobalScopes()
            ->findOrFail($id);

        $room->update([
            'post_status' => $validated['post_status'],
            'moderation_note' => $validated['moderation_note'] ?? null,
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
        ]);

        $room->load([
            'owner:id,username,email',
            'owner.profile:id,user_id,full_name,phone_number',
            'photos' => function ($q) {
                $q->orderByDesc('is_cover')->orderBy('sort_order')->orderBy('id');
            },
        ]);

        return $this->successResponse(
            $this->transformRoom($room),
            'Updated room moderation successfully'
        );
    }

    private function transformRoom(Room $room): array
    {
        $data = $room->toArray();
        $data['post_type'] = $data['postType'] ?? null;

        if (! empty($data['photos'])) {
            $data['photos'] = collect($data['photos'])->map(function ($photo) {
                $photo['photo_path'] = $photo['photo_url'] ?? null;

                if (! empty($photo['photo_url']) && ! str_starts_with($photo['photo_url'], 'http')) {
                    $photo['photo_url'] = asset('storage/'.ltrim($photo['photo_url'], '/'));
                }

                return $photo;
            })->values()->toArray();
        }

        return $data;
    }
}

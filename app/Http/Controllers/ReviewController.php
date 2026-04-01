<?php

namespace App\Http\Controllers;

use App\Common\Traits\ApiResponseTrait;
use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request, int $roomId): JsonResponse
    {
        Room::query()->whereKey($roomId)->firstOrFail();

        $query = Comment::query()
            ->with([
                'user:id,username,email',
                'user.profile:id,user_id,full_name,avatar_url',
                'replies' => function ($q) {
                    $q->where('status', 'visible')
                        ->with([
                            'user:id,username,email',
                            'user.profile:id,user_id,full_name,avatar_url',
                        ])
                        ->latest('id');
                },
            ])
            ->where('room_id', $roomId)
            ->where('status', 'visible')
            ->latest('id');

        $perPage = max(1, min((int) $request->get('per_page', 10), 50));

        $rows = $query
            ->paginate($perPage)
            ->through(fn(Comment $comment) => $this->transformComment($comment));

        $summary = [
            'total_reviews' => Comment::query()
                ->where('room_id', $roomId)
                ->where('status', 'visible')
                ->count(),
            'avg_rating' => round((float) Comment::query()
                ->where('room_id', $roomId)
                ->where('status', 'visible')
                ->avg('rating'), 1),
        ];

        return $this->paginate($rows, 'Lấy danh sách đánh giá thành công', 200, $summary);
    }

    public function store(Request $request, int $roomId): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:3000'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $user = auth('api')->user();
        $room = Room::query()->where('post_status', 'approved')->findOrFail($roomId);

        $comment = Comment::query()->create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'content' => $validated['content'],
            'rating' => $validated['rating'] ?? null,
            'status' => 'pending',
        ]);

        $comment->load([
            'user:id,username,email',
            'user.profile:id,user_id,full_name,avatar_url',
            'replies.user:id,username,email',
            'replies.user.profile:id,user_id,full_name,avatar_url',
        ]);

        return $this->successResponse(
            $this->transformComment($comment),
            'Đánh giá của bạn đã được gửi và đang chờ duyệt',
            201
        );
    }

    public function reply(Request $request, int $commentId): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:3000'],
        ]);

        $user = auth('api')->user();

        $comment = Comment::query()
            ->with('room:id,owner_user_id')
            ->findOrFail($commentId);

        $canReply = $user->username === 'admin'
            || (int) $comment->room?->owner_user_id === (int) $user->id;

        if (! $canReply) {
            abort(403, 'Bạn không có quyền phản hồi review này');
        }

        $reply = CommentReply::query()->create([
            'comment_id' => $comment->id,
            'user_id' => $user->id,
            'content' => $validated['content'],
            'status' => 'visible',
        ]);

        $reply->load([
            'user:id,username,email',
            'user.profile:id,user_id,full_name,avatar_url',
        ]);

        return $this->successResponse(
            $this->transformReply($reply),
            'Tạo phản hồi thành công',
            201
        );
    }

    private function transformComment(Comment $comment): array
    {
        $data = $comment->toArray();
        $data['user_name'] = $comment->user?->profile?->full_name ?: $comment->user?->username;
        $data['user_avatar'] = $comment->user?->profile?->avatar_url;

        $data['replies'] = collect($comment->replies ?? [])->map(
            fn(CommentReply $reply) => $this->transformReply($reply)
        )->values()->toArray();

        return $data;
    }

    private function transformReply(CommentReply $reply): array
    {
        $data = $reply->toArray();
        $data['user_name'] = $reply->user?->profile?->full_name ?: $reply->user?->username;
        $data['user_avatar'] = $reply->user?->profile?->avatar_url;

        return $data;
    }
}

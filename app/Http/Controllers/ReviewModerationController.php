<?php

namespace App\Http\Controllers;

use App\Common\Traits\ApiResponseTrait;
use App\Models\Comment;
use App\Models\CommentReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewModerationController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $query = Comment::query()
            ->with([
                'room:id,title,slug,address,owner_user_id',
                'user:id,username,email',
                'user.profile:id,user_id,full_name,avatar_url,phone_number',
                'replies.user:id,username,email',
                'replies.user.profile:id,user_id,full_name,avatar_url',
            ])
            ->latest('id');

        if ($request->filled('keyword')) {
            $keyword = trim((string) $request->keyword);

            $query->where(function ($q) use ($keyword) {
                $q->where('content', 'like', "%{$keyword}%")
                    ->orWhereHas('room', function ($roomQ) use ($keyword) {
                        $roomQ->where('title', 'like', "%{$keyword}%")
                            ->orWhere('slug', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('user', function ($userQ) use ($keyword) {
                        $userQ->where('username', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->status);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', (int) $request->room_id);
        }

        $perPage = max(1, min((int) $request->get('per_page', 10), 50));

        $rows = $query
            ->paginate($perPage)
            ->through(fn(Comment $comment) => $this->transformComment($comment));

        return $this->paginate($rows, 'Fetched review moderation list successfully');
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,visible,hidden'],
        ]);

        $comment = Comment::query()->findOrFail($id);

        $comment->update([
            'status' => $validated['status'],
        ]);

        if ($validated['status'] === 'hidden') {
            CommentReply::query()
                ->where('comment_id', $comment->id)
                ->update(['status' => 'hidden']);
        }

        $comment->refresh()->load([
            'room:id,title,slug,address,owner_user_id',
            'user:id,username,email',
            'user.profile:id,user_id,full_name,avatar_url,phone_number',
            'replies.user:id,username,email',
            'replies.user.profile:id,user_id,full_name,avatar_url',
        ]);

        return $this->successResponse(
            $this->transformComment($comment),
            'Updated review status successfully'
        );
    }

    private function transformComment(Comment $comment): array
    {
        $data = $comment->toArray();
        $data['user_name'] = $comment->user?->profile?->full_name ?: $comment->user?->username;
        $data['room_title'] = $comment->room?->title;
        $data['room_slug'] = $comment->room?->slug;

        return $data;
    }
}

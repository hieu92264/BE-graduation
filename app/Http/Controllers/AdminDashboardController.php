<?php

namespace App\Http\Controllers;

use App\Common\Traits\ApiResponseTrait;
use App\Models\Booking;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        $summary = [
            'total_users' => User::query()->count(),
            'total_rooms' => Room::query()->withoutGlobalScopes()->count(),
            'published_rooms' => Room::query()->withoutGlobalScopes()->where('post_status', 'approved')->count(),
            'pending_rooms' => Room::query()->withoutGlobalScopes()->where('post_status', 'pending')->count(),
            'total_contacts' => Contact::query()->count(),
            'new_contacts' => Contact::query()->where('status', 'new')->count(),
            'total_bookings' => Booking::query()->count(),
            'won_bookings' => Booking::query()->where('status', 'completed')->count(),
            'total_reviews' => Comment::query()->count(),
            'pending_reviews' => Comment::query()->where('status', 'pending')->count(),
        ];

        $roomStatusStats = Room::query()
            ->withoutGlobalScopes()
            ->select('post_status', DB::raw('COUNT(*) as total'))
            ->groupBy('post_status')
            ->get()
            ->map(fn ($item) => [
                'status' => $item->post_status ?: 'unknown',
                'total' => (int) $item->total,
            ])
            ->values();

        $contactStatusStats = Contact::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(fn ($item) => [
                'status' => $item->status ?: 'unknown',
                'total' => (int) $item->total,
            ])
            ->values();

        $bookingStatusStats = Booking::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(fn ($item) => [
                'status' => $item->status ?: 'unknown',
                'total' => (int) $item->total,
            ])
            ->values();

        $latestContacts = Contact::query()
            ->with(['room:id,title,slug', 'owner:id,username'])
            ->latest('id')
            ->take(5)
            ->get();

        $latestRooms = Room::query()
            ->withoutGlobalScopes()
            ->with(['owner:id,username', 'photos'])
            ->latest('id')
            ->take(5)
            ->get();

        return $this->successResponse([
            'summary' => $summary,
            'room_status_stats' => $roomStatusStats,
            'contact_status_stats' => $contactStatusStats,
            'booking_status_stats' => $bookingStatusStats,
            'latest_contacts' => $latestContacts,
            'latest_rooms' => $latestRooms,
        ], 'messages.dashboard.admin_success');
    }
}

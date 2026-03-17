<?php

namespace App\Http\Controllers;

use App\Common\Enums\DealStatus;
use App\Common\Traits\ApiResponseTrait;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        $query = Booking::query()
            ->with([
                'room:id,title,slug,address,price,owner_user_id,availability_status',
                'contact:id,name,phone,email,status,room_id,owner_user_id',
                'tenant:id,username,email',
                'tenant.profile:id,user_id,full_name,phone_number',
                'landlord:id,username,email',
                'landlord.profile:id,user_id,full_name,phone_number',
            ])
            ->latest('id');

        if ($request->filled('keyword')) {
            $keyword = trim((string) $request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('room', function ($roomQ) use ($keyword) {
                    $roomQ->where('title', 'like', "%{$keyword}%")
                        ->orWhere('slug', 'like', "%{$keyword}%")
                        ->orWhere('address', 'like', "%{$keyword}%");
                })->orWhere('tenant_name', 'like', "%{$keyword}%")
                    ->orWhere('tenant_phone', 'like', "%{$keyword}%")
                    ->orWhere('tenant_email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->status);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', (int) $request->room_id);
        }

        if ($request->boolean('mine')) {
            $query->where('landlord_user_id', $user->id);
        }

        $perPage = max(1, min((int) $request->get('per_page', 10), 50));

        $rows = $query
            ->paginate($perPage)
            ->through(fn(Booking $booking) => $this->transformBooking($booking));

        return $this->paginate($rows, 'Fetched deals successfully');
    }

    public function show(int $id): JsonResponse
    {
        $booking = Booking::query()
            ->with([
                'room:id,title,slug,address,price,owner_user_id,availability_status',
                'contact:id,name,phone,email,status,room_id,owner_user_id',
                'tenant:id,username,email',
                'tenant.profile:id,user_id,full_name,phone_number',
                'landlord:id,username,email',
                'landlord.profile:id,user_id,full_name,phone_number',
            ])
            ->findOrFail($id);

        return $this->successResponse(
            $this->transformBooking($booking),
            'Fetched deal detail successfully'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
            'tenant_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'tenant_name' => ['nullable', 'string', 'max:255'],
            'tenant_phone' => ['nullable', 'string', 'max:50'],
            'tenant_email' => ['nullable', 'email', 'max:255'],
            'landlord_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'agreed_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'commission_percent' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(DealStatus::values())],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        $room = Room::query()->withoutGlobalScopes()->findOrFail((int) $validated['room_id']);
        $landlordUserId = (int) ($validated['landlord_user_id'] ?? $room->owner_user_id);

        if ($landlordUserId !== (int) $room->owner_user_id) {
            return $this->failedResponse('Landlord không khớp với chủ của phòng.', 422);
        }

        $contact = null;

        if (! empty($validated['contact_id'])) {
            $contact = Contact::query()->findOrFail((int) $validated['contact_id']);

            if ($contact->status->value !== 'won') {
                return $this->failedResponse('Chỉ được tạo deal từ lead đã won.', 422);
            }

            if ((int) $contact->room_id !== (int) $room->id) {
                return $this->failedResponse('Lead không thuộc phòng này.', 422);
            }

            if ((int) $contact->owner_user_id !== (int) $landlordUserId) {
                return $this->failedResponse('Lead không khớp với chủ phòng.', 422);
            }
        }

        $activeDealExists = Booking::query()
            ->where('room_id', $room->id)
            ->whereIn('status', ['draft', 'reserved', 'confirmed'])
            ->exists();

        if ($activeDealExists) {
            return $this->failedResponse('Phòng này đang có deal active.', 422);
        }

        $tenant = null;
        if (! empty($validated['tenant_user_id'])) {
            $tenant = User::query()->findOrFail((int) $validated['tenant_user_id']);
            if ($tenant->id === $landlordUserId) {
                return $this->failedResponse('Tenant và landlord không được trùng nhau.', 422);
            }
        }

        $commissionPercent = (float) ($validated['commission_percent'] ?? 0);
        $agreedPrice = (float) $validated['agreed_price'];
        $commissionAmount = round(($agreedPrice * $commissionPercent) / 100, 2);
        $status = $validated['status'] ?? DealStatus::DRAFT->value;

        $booking = Booking::query()->create([
            'room_id' => $room->id,
            'contact_id' => $validated['contact_id'] ?? null,
            'tenant_user_id' => $validated['tenant_user_id'] ?? null,
            'tenant_name' => $validated['tenant_name'] ?? $contact?->name ?? $tenant?->profile?->full_name ?? $tenant?->username,
            'tenant_phone' => $validated['tenant_phone'] ?? $contact?->phone ?? $tenant?->profile?->phone_number,
            'tenant_email' => $validated['tenant_email'] ?? $contact?->email ?? $tenant?->email,
            'landlord_user_id' => $landlordUserId,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'agreed_price' => $agreedPrice,
            'currency' => $validated['currency'] ?? 'VND',
            'commission_percent' => $commissionPercent,
            'commission_amount' => $commissionAmount,
            'status' => $status,
            'note' => $validated['note'] ?? null,
            'reserved_at' => $status === DealStatus::RESERVED->value ? now() : null,
            'confirmed_at' => $status === DealStatus::CONFIRMED->value ? now() : null,
            'completed_at' => $status === DealStatus::COMPLETED->value ? now() : null,
        ]);

        $this->syncRoomAvailability($booking);

        $booking->load([
            'room:id,title,slug,address,price,owner_user_id,availability_status',
            'contact:id,name,phone,email,status,room_id,owner_user_id',
            'tenant:id,username,email',
            'tenant.profile:id,user_id,full_name,phone_number',
            'landlord:id,username,email',
            'landlord.profile:id,user_id,full_name,phone_number',
        ]);

        return $this->successResponse(
            $this->transformBooking($booking),
            'Created deal successfully',
            201
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'agreed_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'commission_percent' => ['nullable', 'numeric', 'min:0'],
            'tenant_name' => ['nullable', 'string', 'max:255'],
            'tenant_phone' => ['nullable', 'string', 'max:50'],
            'tenant_email' => ['nullable', 'email', 'max:255'],
            'status' => ['nullable', Rule::in(DealStatus::values())],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        $booking = Booking::query()->with('room')->findOrFail($id);

        $commissionPercent = array_key_exists('commission_percent', $validated)
            ? (float) $validated['commission_percent']
            : (float) $booking->commission_percent;

        $agreedPrice = array_key_exists('agreed_price', $validated)
            ? (float) $validated['agreed_price']
            : (float) $booking->agreed_price;

        $payload = $validated;
        $payload['commission_percent'] = $commissionPercent;
        $payload['commission_amount'] = round(($agreedPrice * $commissionPercent) / 100, 2);

        if (! empty($validated['status'])) {
            if ($validated['status'] === DealStatus::RESERVED->value && empty($booking->reserved_at)) {
                $payload['reserved_at'] = now();
            }

            if ($validated['status'] === DealStatus::CONFIRMED->value && empty($booking->confirmed_at)) {
                $payload['confirmed_at'] = now();
            }

            if ($validated['status'] === DealStatus::CANCELLED->value && empty($booking->cancelled_at)) {
                $payload['cancelled_at'] = now();
            }

            if ($validated['status'] === DealStatus::COMPLETED->value && empty($booking->completed_at)) {
                $payload['completed_at'] = now();
            }
        }

        $booking->update($payload);

        $this->syncRoomAvailability($booking->fresh('room'));

        $booking->refresh()->load([
            'room:id,title,slug,address,price,owner_user_id,availability_status',
            'contact:id,name,phone,email,status,room_id,owner_user_id',
            'tenant:id,username,email',
            'tenant.profile:id,user_id,full_name,phone_number',
            'landlord:id,username,email',
            'landlord.profile:id,user_id,full_name,phone_number',
        ]);

        return $this->successResponse(
            $this->transformBooking($booking),
            'Updated deal successfully'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $booking = Booking::query()->findOrFail($id);
        $room = $booking->room;

        $booking->delete();

        if ($room) {
            $hasActiveDeal = Booking::query()
                ->where('room_id', $room->id)
                ->whereIn('status', ['draft', 'reserved', 'confirmed'])
                ->exists();

            if (! $hasActiveDeal) {
                $room->update(['availability_status' => 'available']);
            }
        }

        return $this->successResponse([], 'Deleted deal successfully');
    }

    private function syncRoomAvailability(Booking $booking): void
    {
        $room = $booking->room;
        if (! $room) {
            return;
        }

        $status = $booking->status->value;

        if ($status === DealStatus::RESERVED->value) {
            $room->update(['availability_status' => 'reserved']);
            return;
        }

        if (in_array($status, [DealStatus::CONFIRMED->value, DealStatus::COMPLETED->value], true)) {
            $room->update(['availability_status' => 'occupied']);
            return;
        }

        if ($status === DealStatus::CANCELLED->value) {
            $hasActiveDeal = Booking::query()
                ->where('room_id', $room->id)
                ->whereIn('status', ['draft', 'reserved', 'confirmed'])
                ->where('id', '!=', $booking->id)
                ->exists();

            if (! $hasActiveDeal) {
                $room->update(['availability_status' => 'available']);
            }
        }
    }

    private function transformBooking(Booking $booking): array
    {
        $data = $booking->toArray();

        $data['room_title'] = $booking->room?->title;
        $data['room_slug'] = $booking->room?->slug;
        $data['room_address'] = $booking->room?->address;
        $data['room_availability_status'] = $booking->room?->availability_status?->value ?? $booking->room?->availability_status;

        $data['contact_name'] = $booking->contact?->name;
        $data['contact_phone'] = $booking->contact?->phone;
        $data['contact_email'] = $booking->contact?->email;

        $data['tenant_name'] = $booking->tenant_name
            ?: ($booking->tenant?->profile?->full_name ?: $booking->tenant?->username);

        $data['tenant_phone'] = $booking->tenant_phone
            ?: $booking->tenant?->profile?->phone_number;

        $data['tenant_email'] = $booking->tenant_email
            ?: $booking->tenant?->email;

        $data['landlord_name'] = $booking->landlord?->profile?->full_name ?: $booking->landlord?->username;
        $data['landlord_phone'] = $booking->landlord?->profile?->phone_number;

        return $data;
    }
}

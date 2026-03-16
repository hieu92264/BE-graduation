<?php

namespace App\Http\Controllers;

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
        $user = auth()->user();

        $query = Booking::query()
            ->with([
                'room:id,title,slug,address,price,owner_user_id',
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
                })->orWhereHas('tenant', function ($tenantQ) use ($keyword) {
                    $tenantQ->where('username', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                })->orWhereHas('landlord', function ($landlordQ) use ($keyword) {
                    $landlordQ->where('username', 'like', "%{$keyword}%")
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

        if ($request->boolean('mine')) {
            $query->where(function ($q) use ($user) {
                $q->where('tenant_user_id', $user->id)
                    ->orWhere('landlord_user_id', $user->id);
            });
        }

        $perPage = max(1, min((int) $request->get('per_page', 10), 50));

        $rows = $query
            ->paginate($perPage)
            ->through(fn (Booking $booking) => $this->transformBooking($booking));

        return $this->paginate($rows, 'Fetched bookings successfully');
    }

    public function show(int $id): JsonResponse
    {
        $booking = Booking::query()
            ->with([
                'room:id,title,slug,address,price,owner_user_id',
                'tenant:id,username,email',
                'tenant.profile:id,user_id,full_name,phone_number',
                'landlord:id,username,email',
                'landlord.profile:id,user_id,full_name,phone_number',
            ])
            ->findOrFail($id);

        return $this->successResponse(
            $this->transformBooking($booking),
            'Fetched booking detail successfully'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'tenant_user_id' => ['required', 'integer', 'exists:users,id'],
            'landlord_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'agreed_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'commission_percent' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'available', 'occupied'])],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        $room = Room::query()->withoutGlobalScopes()->findOrFail((int) $validated['room_id']);
        $tenant = User::query()->findOrFail((int) $validated['tenant_user_id']);

        $landlordUserId = (int) ($validated['landlord_user_id'] ?? $room->owner_user_id);

        if ($landlordUserId !== (int) $room->owner_user_id) {
            return $this->failedResponse('Landlord không khớp với chủ của phòng.', 422);
        }

        if ($tenant->id === $landlordUserId) {
            return $this->failedResponse('Tenant và landlord không được trùng nhau.', 422);
        }

        if (! empty($validated['contact_id'])) {
            $contact = Contact::query()->findOrFail((int) $validated['contact_id']);

            if ($contact->status !== 'successful') {
                return $this->failedResponse('Chỉ được tạo booking từ contact đã successful.', 422);
            }

            if ((int) $contact->room_id !== (int) $room->id) {
                return $this->failedResponse('Contact không thuộc phòng này.', 422);
            }

            if ((int) $contact->owner_user_id !== (int) $landlordUserId) {
                return $this->failedResponse('Contact không khớp với chủ phòng.', 422);
            }
        }

        $commissionPercent = (float) ($validated['commission_percent'] ?? 0);
        $agreedPrice = (float) $validated['agreed_price'];
        $commissionAmount = round(($agreedPrice * $commissionPercent) / 100, 2);

        $booking = Booking::query()->create([
            'room_id' => $room->id,
            'tenant_user_id' => $tenant->id,
            'landlord_user_id' => $landlordUserId,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'agreed_price' => $agreedPrice,
            'currency' => $validated['currency'] ?? 'VND',
            'commission_percent' => $commissionPercent,
            'commission_amount' => $commissionAmount,
            'status' => $validated['status'] ?? 'pending',
            'note' => $validated['note'] ?? null,
        ]);

        if (($validated['status'] ?? 'pending') === 'occupied') {
            $room->update([
                'booking_status' => 'occupied',
            ]);
        }

        $booking->load([
            'room:id,title,slug,address,price,owner_user_id',
            'tenant:id,username,email',
            'tenant.profile:id,user_id,full_name,phone_number',
            'landlord:id,username,email',
            'landlord.profile:id,user_id,full_name,phone_number',
        ]);

        return $this->successResponse(
            $this->transformBooking($booking),
            'Created booking successfully',
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
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'available', 'occupied'])],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        $booking = Booking::query()->findOrFail($id);

        $commissionPercent = array_key_exists('commission_percent', $validated)
            ? (float) $validated['commission_percent']
            : (float) $booking->commission_percent;

        $agreedPrice = array_key_exists('agreed_price', $validated)
            ? (float) $validated['agreed_price']
            : (float) $booking->agreed_price;

        $payload = $validated;
        $payload['commission_percent'] = $commissionPercent;
        $payload['commission_amount'] = round(($agreedPrice * $commissionPercent) / 100, 2);

        $booking->update($payload);

        if (! empty($validated['status'])) {
            if ($validated['status'] === 'occupied') {
                $booking->room?->update(['booking_status' => 'occupied']);
            }

            if (in_array($validated['status'], ['available', 'pending'], true)) {
                $booking->room?->update(['booking_status' => $validated['status']]);
            }
        }

        $booking->refresh()->load([
            'room:id,title,slug,address,price,owner_user_id',
            'tenant:id,username,email',
            'tenant.profile:id,user_id,full_name,phone_number',
            'landlord:id,username,email',
            'landlord.profile:id,user_id,full_name,phone_number',
        ]);

        return $this->successResponse(
            $this->transformBooking($booking),
            'Updated booking successfully'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $booking = Booking::query()->findOrFail($id);
        $booking->delete();

        return $this->successResponse(null, 'Deleted booking successfully');
    }

    private function transformBooking(Booking $booking): array
    {
        $data = $booking->toArray();

        $data['room_title'] = $booking->room?->title;
        $data['room_slug'] = $booking->room?->slug;
        $data['room_address'] = $booking->room?->address;

        $data['tenant_name'] = $booking->tenant?->profile?->full_name ?: $booking->tenant?->username;
        $data['tenant_phone'] = $booking->tenant?->profile?->phone_number;

        $data['landlord_name'] = $booking->landlord?->profile?->full_name ?: $booking->landlord?->username;
        $data['landlord_phone'] = $booking->landlord?->profile?->phone_number;

        return $data;
    }
}

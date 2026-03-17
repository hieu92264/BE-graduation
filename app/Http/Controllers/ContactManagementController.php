<?php

namespace App\Http\Controllers;

use App\Common\Enums\LeadStatus;
use App\Common\Traits\ApiResponseTrait;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactManagementController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        $query = Contact::query()
            ->with([
                'room:id,title,slug,address,price,owner_user_id',
                'owner:id,username,email',
                'owner.profile:id,user_id,full_name,phone_number',
                'handledByUser:id,username,email',
                'handledByUser.profile:id,user_id,full_name',
            ])
            ->latest('id');

        $scope = $request->string('scope')->toString();

        if ($scope === 'landlord') {
            $query->where('owner_user_id', $user->id);
        }

        if ($keyword = trim((string) $request->input('keyword'))) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%")
                    ->orWhere('subject', 'like', "%{$keyword}%")
                    ->orWhere('message', 'like', "%{$keyword}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($roomId = $request->input('room_id')) {
            $query->where('room_id', $roomId);
        }

        $perPage = max(1, min((int) $request->input('per_page', 10), 50));

        $contacts = $query
            ->paginate($perPage)
            ->through(fn(Contact $contact) => $this->transformContact($contact));

        return $this->paginate($contacts, 'Fetched contacts successfully');
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = auth('api')->user();

        $contact = Contact::query()
            ->with([
                'room:id,title,slug,address,price,owner_user_id',
                'owner:id,username,email',
                'owner.profile:id,user_id,full_name,phone_number',
                'handledByUser:id,username,email',
                'handledByUser.profile:id,user_id,full_name',
            ])
            ->findOrFail($id);

        $scope = $request->string('scope')->toString();

        if ($scope === 'landlord' && (int) $contact->owner_user_id !== (int) $user->id) {
            abort(403, 'Bạn không có quyền xem lead này');
        }

        return $this->successResponse(
            $this->transformContact($contact),
            'Fetched contact detail successfully'
        );
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(LeadStatus::values())],
            'status_note' => ['nullable', 'string', 'max:3000'],
            'lost_reason' => ['nullable', 'string', 'max:3000'],
            'next_follow_up_at' => ['nullable', 'date'],
            'viewing_at' => ['nullable', 'date'],
        ]);

        $user = auth('api')->user();

        $contact = Contact::query()
            ->with([
                'room:id,title,slug,address,price,owner_user_id',
                'owner:id,username,email',
                'owner.profile:id,user_id,full_name,phone_number',
                'handledByUser:id,username,email',
                'handledByUser.profile:id,user_id,full_name',
            ])
            ->findOrFail($id);

        $scope = $request->string('scope')->toString();

        if ($scope === 'landlord' && (int) $contact->owner_user_id !== (int) $user->id) {
            abort(403, 'Bạn không có quyền cập nhật lead này');
        }

        if ($validated['status'] === LeadStatus::VIEWING_SCHEDULED->value && empty($validated['viewing_at'])) {
            return $this->failedResponse('Vui lòng truyền viewing_at khi hẹn xem phòng.', 422);
        }

        $payload = [
            'status' => $validated['status'],
            'status_note' => $validated['status_note'] ?? null,
            'lost_reason' => $validated['lost_reason'] ?? null,
            'next_follow_up_at' => $validated['next_follow_up_at'] ?? null,
            'handled_by' => $user->id,
            'handled_at' => now(),
        ];

        if ($validated['status'] === LeadStatus::CONTACTED->value) {
            $payload['last_contacted_at'] = now();
        }

        if ($validated['status'] === LeadStatus::VIEWING_SCHEDULED->value) {
            $payload['viewing_at'] = $validated['viewing_at'];
        }

        if ($validated['status'] !== LeadStatus::LOST->value) {
            $payload['lost_reason'] = null;
        }

        $contact->update($payload);

        $contact->refresh()->load([
            'room:id,title,slug,address,price,owner_user_id',
            'owner:id,username,email',
            'owner.profile:id,user_id,full_name,phone_number',
            'handledByUser:id,username,email',
            'handledByUser.profile:id,user_id,full_name',
        ]);

        return $this->successResponse(
            $this->transformContact($contact),
            'Updated contact status successfully'
        );
    }

    private function transformContact(Contact $contact): array
    {
        $data = $contact->toArray();

        $data['room_title'] = $contact->room?->title;
        $data['room_slug'] = $contact->room?->slug;
        $data['room_address'] = $contact->room?->address;
        $data['room_price'] = $contact->room?->price;

        $data['owner_name'] = $contact->owner?->profile?->full_name ?: $contact->owner?->username;
        $data['owner_phone'] = $contact->owner?->profile?->phone_number;
        $data['handled_by_name'] = $contact->handledByUser?->profile?->full_name ?: $contact->handledByUser?->username;

        return $data;
    }

    public function landlordIndex(Request $request): JsonResponse
    {
        $request->merge(['scope' => 'landlord']);
        return $this->index($request);
    }

    public function landlordShow(Request $request, int $id): JsonResponse
    {
        $request->merge(['scope' => 'landlord']);
        return $this->show($request, $id);
    }

    public function landlordUpdateStatus(Request $request, int $id): JsonResponse
    {
        $request->merge(['scope' => 'landlord']);
        return $this->updateStatus($request, $id);
    }
}

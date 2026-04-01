<?php

namespace App\Http\Controllers;

use App\Common\Enums\LeadStatus;
use App\Common\Traits\ApiResponseTrait;
use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactToLandlordMail;
use App\Models\Contact;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    use ApiResponseTrait;

    public function store(StoreContactRequest $request, string $id): JsonResponse
    {
        $room = Room::with(['owner:id,email,username', 'owner.profile:user_id,full_name,phone_number'])
            ->withoutGlobalScopes()
            ->whereKey((int) $id)
            ->firstOrFail();

        if ($room->post_status !== 'approved') {
            return $this->failedResponse('Phòng chưa sẵn sàng nhận liên hệ.', 422);
        }

        $landlordEmail = $room->owner?->email;
        if (! $landlordEmail) {
            return $this->failedResponse('Không tìm thấy email của chủ trọ.', 404);
        }

        $contact = Contact::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'subject' => $request->input('subject') ?: ("Liên hệ phòng #{$room->id}"),
            'message' => $request->input('message'),
            'move_in_date' => $request->input('move_in_date'),
            'preferred_viewing_time' => $request->input('preferred_viewing_time'),
            'status' => LeadStatus::NEW->value,
            'source' => 'room_detail_form',
            'room_id' => $room->id,
            'owner_user_id' => $room->owner_user_id,
        ]);

        Mail::to($landlordEmail)->send(new ContactToLandlordMail($contact, $room));

        return $this->successResponse(
            $this->transformContact($contact->fresh(['room', 'owner.profile'])),
            'Gửi liên hệ thành công',
            201
        );
    }

    private function transformContact(Contact $contact): array
    {
        $data = $contact->toArray();
        $data['room_title'] = $contact->room?->title;
        $data['owner_name'] = $contact->owner?->profile?->full_name ?: $contact->owner?->username;

        return $data;
    }
}

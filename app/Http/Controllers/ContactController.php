<?php

namespace App\Http\Controllers;

use App\Common\Traits\ApiResponseTrait;
use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactToLandlordMail;
use App\Models\Contact;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request, string $id): JsonResponse
    {
        $room = Room::with(['owner:id,email,username', 'owner.profile:user_id,full_name,phone_number'])
            ->whereKey((int)$id)
            ->firstOrFail();

        $landlordEmail = $room->owner?->email;
        if (!$landlordEmail) {
            return $this->failedResponse(
                'Landlord email not found.',
                404
            );
        }

        $contact = Contact::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'subject' => $request->input('subject') ?: ("Liên hệ phòng #{$room->id}"),
            'message' => $request->input('message'),
            'status' => 'new',
            'room_id' => $room->id,
            'owner_user_id' => $room->owner_user_id,
        ]);

        Mail::to($landlordEmail)->send(new ContactToLandlordMail($contact, $room));

        return $this->successResponse(
            [], 'Success', 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

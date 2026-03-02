<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactToLandlordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contact $contact,
        public ?Room   $room = null
    )
    {
    }

    public function build(): ContactToLandlordMail
    {
        $subject = $this->contact->subject ?: 'Yêu cầu liên hệ phòng';

        $roomUrl = null;
        if ($this->room) {
            // Nếu bạn có FE: config('app.frontend_url') . "/rooms/{$this->room->id}"
            $roomUrl = url("/rooms/{$this->room->id}");
        }

        return $this->subject($subject)
            ->view('emails.contact-to-landlord', [
                'contact' => $this->contact,
                'room' => $this->room,
                'roomUrl' => $roomUrl,
                'appName' => config('app.name'),
            ]);
    }
}

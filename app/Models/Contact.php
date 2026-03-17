<?php

namespace App\Models;

use App\Common\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'move_in_date',
        'preferred_viewing_time',
        'status',
        'status_note',
        'lost_reason',
        'next_follow_up_at',
        'last_contacted_at',
        'viewing_at',
        'source',
        'room_id',
        'owner_user_id',
        'handled_by',
        'handled_at',
    ];

    protected function casts(): array
    {
        return [
            'move_in_date' => 'date',
            'next_follow_up_at' => 'datetime',
            'last_contacted_at' => 'datetime',
            'viewing_at' => 'datetime',
            'handled_at' => 'datetime',
            'status' => LeadStatus::class,
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function handledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}

<?php

namespace App\Models;

use App\Common\Enums\BookingStatus;
use App\Common\Enums\RoomAvailabilityStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends BaseModel
{
    protected $fillable = [
        'isactive',
        'owner_user_id',
        'category_id',
        'post_type_id',
        'city_id',
        'district_id',
        'ward_id',
        'title',
        'slug',
        'address',
        'price',
        'area',
        'description',
        'booking_status',
        'availability_status',
        'post_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',
    ];

    public function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'area' => 'decimal:2',
            'booking_status' => BookingStatus::class,
            'availability_status' => RoomAvailabilityStatus::class,
            'moderated_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function postType(): BelongsTo
    {
        return $this->belongsTo(PostType::class, 'post_type_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(RoomPhoto::class, 'room_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'room_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'room_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}

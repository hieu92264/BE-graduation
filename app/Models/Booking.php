<?php

namespace App\Models;

use App\Common\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking query()
 * @property BookingStatus $status
 * @property-read \App\Models\User|null $landlord
 * @property-read \App\Models\Room|null $room
 * @property-read \App\Models\User|null $tenant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking withoutTrashed()
 * @mixin \Eloquent
 */
class Booking extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'room_id',
        'tenant_user_id',
        'landlord_user_id',
        'start_date',
        'end_date',
        'agreed_price',
        'currency',
        'commission_percent',
        'commission_amount',
        'status',
        'note'
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'agreed_price' => 'decimal:2',
            'commission_percent' => 'decimal:2',
            'status' => BookingStatus::class
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_user_id');
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_user_id');
    }
}

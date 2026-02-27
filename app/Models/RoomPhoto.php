<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomPhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomPhoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomPhoto query()
 * @property-read \App\Models\Room|null $room
 * @mixin \Eloquent
 */
class RoomPhoto extends Model
{
    protected $fillable = [
        'room_id',
        'photo_url',
        'is_cover',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_cover' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}

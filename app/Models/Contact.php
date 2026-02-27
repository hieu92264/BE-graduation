<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact query()
 * @mixin \Eloquent
 */
class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
    ];
}

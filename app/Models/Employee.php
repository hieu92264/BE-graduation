<?php

namespace App\Models;

use App\Common\Enums\WorkStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'isactive',
        'user_id',
        'employee_code',
        'full_name',
        'phone',
        'email',
        'dob',
        'avatar_url',
        'status',
        'join_date',
        'terminate_date',
        'remark',
        'user_name_created',
        'user_name_updated'
    ];

    protected function casts(): array
    {
        return [
            'status' => WorkStatus::class,
            'dob' => 'date',
            'join_date' => 'date',
            'terminate_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

<?php

namespace App\Models;

use App\Common\Enums\WorkStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// có property docs khi gõ sẽ chuẩn là có type hint, ví dụ $employee->full_name. thử tự gõ đi
/**
 * @property int $id
 * @property bool $isactive
 * @property int|null $user_id
 * @property string $employee_code
 * @property string $full_name
 * @property string|null $phone
 * @property string|null $email
 * @property \Carbon\CarbonImmutable|null $dob
 * @property string|null $avatar_url
 * @property WorkStatus $status
 * @property \Carbon\CarbonImmutable|null $join_date
 * @property \Carbon\CarbonImmutable|null $terminate_date
 * @property string|null $remark
 * @property string|null $user_name_created
 * @property string|null $user_name_updated
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\User|null $user
 */
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
        'user_name_updated',
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

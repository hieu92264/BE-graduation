<?php

namespace App\Http\Services;

use App\Common\Constants\RecordStatus;
use App\Http\_base\BaseService;
use App\Http\Interfaces\EmployeeServiceInterface;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;

class EmployeeService extends BaseService implements EmployeeServiceInterface
{

    protected function getModel(): string
    {
        // TODO: Implement getModel() method.
        return Employee::class;
    }

    protected function generateEmployeeCode(): string
    {
        $prefix = Carbon::now()->format('Ymd');
        $latestEmployeeCode = Employee::query()
            ->where('employees.employee_code', 'like', $prefix . '%')
            ->orderBy('employees.created_at', 'desc')
            ->value('employee_code');

        if ($latestEmployeeCode) {
            $lastSequence = (int)substr($latestEmployeeCode, 8);
            $newSequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '0001';
        }

        return $prefix . $newSequence;
    }

    public function create(array $attributes): Employee
    {
        $attributes['isactive'] = RecordStatus::ACTIVE;
        $attributes['employee_code'] = $this->generateEmployeeCode();
        $attributes['join_date'] = $attributes['join_date'] ?? Carbon::now()->toDateString();

        return parent::create($attributes);
    }

    public function getUserOptions(?int $userId): array
    {
        try {
            return User::query()
                ->where(function ($query) use ($userId) {
                    $query->whereDoesntHave('employee');

                    if ($userId) {
                        $query->orWhere('users.id', $userId);
                    }
                })
                ->select(['users.id', 'users.username', 'users.email'])
                ->get()
                ->map(function (User $user) use ($userId) {
                    return [
                        'value' => $user->id,
                        'text' => $user->username . ' (' . $user->email . ')',
                        'selected' => $userId !== null && $user->id === $userId
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {

            return [];
        }
    }
}

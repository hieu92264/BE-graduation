<?php

namespace App\Http\Requests;

use App\Common\Enums\WorkStatus;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Exists;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Tự google search vì sao mảng nó validation nó tốt hơn là pipe string ,mà có khi nhìn ngay cũng thấy
        return [
            'user_id' => ['nullable', new Exists(User::class, 'id')],
            'full_name' => ['required', 'string', 'max:255'],
            'status' => ['required', new Enum(WorkStatus::class)],
            'join_date' => ['nullable', 'date'],
            'email' => ['nullable', 'email'],
            'dob' => ['nullable', 'date'],
            'phone' => ['nullable', 'string'],
            'terminate_date' => ['nullable', 'date'],
            'remark' => ['nullable', 'string'],
        ];
    }
}

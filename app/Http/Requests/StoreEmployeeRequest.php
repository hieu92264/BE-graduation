<?php

namespace App\Http\Requests;

use App\Common\Enums\WorkStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'full_name' => 'required|string|max:255',
            'status' => ['required', new Enum(WorkStatus::class)],
            'join_date' => 'nullable|date|string',
            'email' => 'email|nullable',
            'dob' => 'nullable|date|string',
            'phone' => 'nullable|string',
            'terminate_date' => 'nullable|date|string',
            'remark' => 'nullable|string',
        ];
    }
}

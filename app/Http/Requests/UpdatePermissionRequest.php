<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'string|max:255|min:1|unique:permissions,code',
            'name' => 'string|max:255|min:1',
            'parent_id' => 'nullable|integer|exists:permissions,id',
            'url' => 'nullable|string|max:255',
        ];
    }
}

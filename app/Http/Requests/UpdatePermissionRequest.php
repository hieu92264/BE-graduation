<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $id = $this->route('id');
        return [
            'code' => [
                'required',
                'string',
                'min:1',
                'max:255',
                Rule::unique('permissions', 'code')->ignore($id),
            ],
            'name' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:permissions,id',
            ],
            'url' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}

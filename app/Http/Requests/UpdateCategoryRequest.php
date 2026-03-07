<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'isactive' => 'required|string|in:Y,N',
            'code' => [
                'required',
                'string',
                'min:1',
                'max:50',
                Rule::unique('categories', 'code')->ignore($id),
            ],
            'name' => [
                'required',
                'string',
                'min:1',
                'max:150',
            ],
            'slug' => [
                'required',
                'string',
                'min:1',
                'max:180',
                Rule::unique('categories', 'slug')->ignore($id),
            ],
            'sort_order' => [
                'nullable',
                'integer',
            ],
            'remark' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }
}

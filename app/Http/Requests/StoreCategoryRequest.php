<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
        return [
            'isactive' => 'required|string|in:Y,N',
            'code' => 'required|string|max:50|min:1|unique:categories,code',
            'name' => 'required|string|max:150|min:1',
            'slug' => 'required|string|max:180|min:1|unique:categories,slug',
            'sort_order' => 'nullable|integer',
            'remark' => 'nullable|string|max:500',
        ];
    }
}

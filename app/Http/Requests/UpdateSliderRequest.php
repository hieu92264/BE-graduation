<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'isactive' => filter_var($this->isactive, FILTER_VALIDATE_BOOLEAN) ? 'Y' : 'N',
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'isactive' => 'required|string|in:Y,N',
            'remark' => 'nullable|string|max:1000',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,avif|max:5120',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Chuẩn bị dữ liệu trước khi validate.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Chuyển đổi 1/true thành 'Y' và 0/false/khác thành 'N'
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
            'image' => 'required|file|mimes:jpg,jpeg,png,webp,avif|max:5120',
        ];
    }
}

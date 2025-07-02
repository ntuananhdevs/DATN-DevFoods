<?php

namespace App\Http\Requests\Admin\Discount_codes;

use Illuminate\Foundation\Http\FormRequest;

class BulkDiscountCodeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:discount_codes,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ids.required' => 'Vui lòng chọn ít nhất một mã giảm giá.',
            'ids.array' => 'Dữ liệu không hợp lệ.',
            'ids.*.integer' => 'ID mã giảm giá không hợp lệ.',
            'ids.*.exists' => 'Mã giảm giá không tồn tại.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'ids' => 'mã giảm giá',
        ];
    }
} 
<?php

namespace App\Http\Requests\Admin\Discount_codes;

use Illuminate\Foundation\Http\FormRequest;

class BulkStatusDiscountCodeRequest extends FormRequest
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
            'is_active' => 'required|boolean',
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
            'is_active.required' => 'Trạng thái hoạt động là bắt buộc.',
            'is_active.boolean' => 'Trạng thái hoạt động không hợp lệ.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'ids' => 'mã giảm giá',
            'is_active' => 'trạng thái hoạt động',
        ];
    }
} 
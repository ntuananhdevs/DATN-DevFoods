<?php

namespace App\Http\Requests\Admin\Discount_codes;

use Illuminate\Foundation\Http\FormRequest;

class GetUsersByRankRequest extends FormRequest
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
            'ranks' => 'required|array',
            'ranks.*' => 'integer|between:1,5',
            'discount_code_id' => 'nullable|exists:discount_codes,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ranks.required' => 'Vui lòng chọn ít nhất một hạng thành viên.',
            'ranks.array' => 'Danh sách hạng thành viên không hợp lệ.',
            'ranks.*.integer' => 'ID hạng thành viên không hợp lệ.',
            'ranks.*.between' => 'ID hạng thành viên phải từ 1 đến 5.',
            'discount_code_id.exists' => 'Mã giảm giá không tồn tại.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'ranks' => 'hạng thành viên',
            'discount_code_id' => 'mã giảm giá',
        ];
    }
} 
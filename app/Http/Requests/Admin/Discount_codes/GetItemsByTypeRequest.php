<?php

namespace App\Http\Requests\Admin\Discount_codes;

use Illuminate\Foundation\Http\FormRequest;

class GetItemsByTypeRequest extends FormRequest
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
            'type' => 'required|string|in:products,categories,combos,variants',
            'search' => 'nullable|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Loại item là bắt buộc.',
            'type.string' => 'Loại item không hợp lệ.',
            'type.in' => 'Loại item phải là một trong: products, categories, combos, variants.',
            'search.string' => 'Từ khóa tìm kiếm không hợp lệ.',
            'search.max' => 'Từ khóa tìm kiếm không được vượt quá 255 ký tự.',
            'limit.integer' => 'Giới hạn số lượng phải là số nguyên.',
            'limit.min' => 'Giới hạn số lượng phải lớn hơn 0.',
            'limit.max' => 'Giới hạn số lượng không được vượt quá 100.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'type' => 'loại item',
            'search' => 'từ khóa tìm kiếm',
            'limit' => 'giới hạn số lượng',
        ];
    }
} 
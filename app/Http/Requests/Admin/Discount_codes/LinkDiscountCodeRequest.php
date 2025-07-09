<?php

namespace App\Http\Requests\Admin\Discount_codes;

use Illuminate\Foundation\Http\FormRequest;

class LinkDiscountCodeRequest extends FormRequest
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
            'branch_id' => 'nullable|exists:branches,id',
            'product_id' => 'nullable|exists:products,id',
            'category_id' => 'nullable|exists:categories,id',
            'combo_id' => 'nullable|exists:combos,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'branch_id.exists' => 'Chi nhánh không tồn tại.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'category_id.exists' => 'Danh mục không tồn tại.',
            'combo_id.exists' => 'Combo không tồn tại.',
            'variant_id.exists' => 'Biến thể không tồn tại.',
            'user_ids.array' => 'Danh sách người dùng không hợp lệ.',
            'user_ids.*.exists' => 'Người dùng không tồn tại.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'branch_id' => 'chi nhánh',
            'product_id' => 'sản phẩm',
            'category_id' => 'danh mục',
            'combo_id' => 'combo',
            'variant_id' => 'biến thể',
            'user_ids' => 'người dùng',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Ensure at least one linking field is provided
            $hasLink = $this->branch_id || $this->product_id || $this->category_id || 
                      $this->combo_id || $this->variant_id || $this->user_ids;
            
            if (!$hasLink) {
                $validator->errors()->add('general', 'Vui lòng chọn ít nhất một item để liên kết.');
            }
        });
    }
} 
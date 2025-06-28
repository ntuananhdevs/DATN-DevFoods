<?php

namespace App\Http\Requests\Admin\Discount_codes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiscountCodeRequest extends FormRequest
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
        $discountCodeId = $this->route('id');
        
        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('discount_codes', 'code')->ignore($discountCodeId),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed_amount,free_shipping',
            'discount_value' => 'required|numeric|min:0',
            'min_requirement_type' => 'required|string|max:255|in:order_amount,product_price',
            'min_requirement_value' => 'required|numeric|min:0.01',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'applicable_items' => 'nullable|string|in:all_items,specific_products,specific_categories,specific_combos,specific_variants',
            'applicable_scope' => 'nullable|string|in:all_branches,specific_branches',
            'applicable_ranks' => 'nullable|array',
            'applicable_ranks.*' => 'integer|between:1,5',
            'rank_exclusive' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'valid_days_of_week' => 'nullable|array',
            'valid_days_of_week.*' => 'integer|between:0,6',
            'valid_from_time' => 'nullable|date_format:H:i',
            'valid_to_time' => 'nullable|date_format:H:i|after_or_equal:valid_from_time',
            'usage_type' => 'required|in:public,personal',
            'max_total_usage' => 'required|integer|min:1',
            'max_usage_per_user' => 'required|integer|min:1',
            'assigned_users' => 'nullable|array',
            'assigned_users.*' => 'exists:users,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
            
            // Branch related validations
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            
            // Product related validations
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            
            // Category related validations
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            
            // Combo related validations
            'combo_ids' => 'nullable|array',
            'combo_ids.*' => 'exists:combos,id',
            
            // Variant related validations
            'variant_ids' => 'nullable|array',
            'variant_ids.*' => 'exists:product_variants,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Mã giảm giá là bắt buộc.',
            'code.unique' => 'Mã giảm giá đã tồn tại.',
            'name.required' => 'Tên mã giảm giá là bắt buộc.',
            'discount_type.required' => 'Loại giảm giá là bắt buộc.',
            'discount_type.in' => 'Loại giảm giá không hợp lệ.',
            'discount_value.required' => 'Giá trị giảm giá là bắt buộc.',
            'discount_value.numeric' => 'Giá trị giảm giá phải là số.',
            'discount_value.min' => 'Giá trị giảm giá phải lớn hơn hoặc bằng 0.',
            'min_requirement_type.required' => 'Loại điều kiện tối thiểu là bắt buộc.',
            'min_requirement_type.in' => 'Loại điều kiện tối thiểu không hợp lệ.',
            'min_requirement_value.required' => 'Giá trị điều kiện tối thiểu là bắt buộc.',
            'min_requirement_value.numeric' => 'Giá trị điều kiện tối thiểu phải là số.',
            'min_requirement_value.min' => 'Giá trị điều kiện tối thiểu phải lớn hơn 0.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ.',
            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'end_date.date' => 'Ngày kết thúc không hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'usage_type.required' => 'Loại sử dụng là bắt buộc.',
            'usage_type.in' => 'Loại sử dụng không hợp lệ.',
            'max_total_usage.required' => 'Số lần sử dụng tối đa là bắt buộc.',
            'max_total_usage.integer' => 'Số lần sử dụng tối đa phải là số nguyên.',
            'max_total_usage.min' => 'Số lần sử dụng tối đa phải lớn hơn 0.',
            'max_usage_per_user.required' => 'Số lần sử dụng tối đa mỗi người dùng là bắt buộc.',
            'max_usage_per_user.integer' => 'Số lần sử dụng tối đa mỗi người dùng phải là số nguyên.',
            'max_usage_per_user.min' => 'Số lần sử dụng tối đa mỗi người dùng phải lớn hơn 0.',
            'assigned_users.*.exists' => 'Người dùng được chọn không tồn tại.',
            'branch_ids.*.exists' => 'Chi nhánh được chọn không tồn tại.',
            'product_ids.*.exists' => 'Sản phẩm được chọn không tồn tại.',
            'category_ids.*.exists' => 'Danh mục được chọn không tồn tại.',
            'combo_ids.*.exists' => 'Combo được chọn không tồn tại.',
            'variant_ids.*.exists' => 'Biến thể được chọn không tồn tại.',
            'valid_to_time.after_or_equal' => 'Thời gian kết thúc phải sau hoặc bằng thời gian bắt đầu.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'code' => 'mã giảm giá',
            'name' => 'tên mã giảm giá',
            'description' => 'mô tả',
            'discount_type' => 'loại giảm giá',
            'discount_value' => 'giá trị giảm giá',
            'min_requirement_type' => 'loại yêu cầu tối thiểu',
            'min_requirement_value' => 'giá trị yêu cầu tối thiểu',
            'max_discount_amount' => 'số tiền giảm tối đa',
            'applicable_items' => 'sản phẩm áp dụng',
            'applicable_scope' => 'phạm vi áp dụng',
            'applicable_ranks' => 'hạng thành viên áp dụng',
            'start_date' => 'ngày bắt đầu',
            'end_date' => 'ngày kết thúc',
            'valid_days_of_week' => 'ngày trong tuần hợp lệ',
            'valid_from_time' => 'thời gian bắt đầu',
            'valid_to_time' => 'thời gian kết thúc',
            'usage_type' => 'loại sử dụng',
            'max_total_usage' => 'số lần sử dụng tối đa',
            'max_usage_per_user' => 'số lần sử dụng tối đa mỗi người dùng',
            'assigned_users' => 'người dùng được gán',
            'is_active' => 'trạng thái hoạt động',
            'is_featured' => 'trạng thái nổi bật',
            'display_order' => 'thứ tự hiển thị',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate discount value based on discount type
            if ($this->discount_type === 'percentage' && $this->discount_value > 100) {
                $validator->errors()->add('discount_value', 'Phần trăm giảm giá không được vượt quá 100%.');
            }

            // Validate: max_discount_amount là bắt buộc khi loại giảm giá là percentage
            if ($this->discount_type === 'percentage' && !$this->filled('max_discount_amount')) {
                $validator->errors()->add('max_discount_amount', 'Số tiền giảm tối đa là bắt buộc khi loại giảm giá là phần trăm.');
            }

            // Validate that at least one item type is selected when applicable_items is specific
            if (in_array($this->applicable_items, ['specific_products', 'specific_categories', 'specific_combos', 'specific_variants'])) {
                $hasItems = false;
                
                switch ($this->applicable_items) {
                    case 'specific_products':
                        $hasItems = !empty($this->product_ids);
                        break;
                    case 'specific_categories':
                        $hasItems = !empty($this->category_ids);
                        break;
                    case 'specific_combos':
                        $hasItems = !empty($this->combo_ids);
                        break;
                    case 'specific_variants':
                        $hasItems = !empty($this->variant_ids);
                        break;
                }
                
                if (!$hasItems) {
                    $validator->errors()->add('applicable_items', 'Vui lòng chọn ít nhất một ' . $this->getItemTypeName($this->applicable_items) . ' khi áp dụng cho sản phẩm cụ thể.');
                }
            }

            // Validate that branches are selected when applicable_scope is specific_branches
            if ($this->applicable_scope === 'specific_branches' && empty($this->branch_ids)) {
                $validator->errors()->add('branch_ids', 'Vui lòng chọn ít nhất một chi nhánh khi áp dụng cho chi nhánh cụ thể.');
            }

            // Validate that users are assigned when usage_type is personal
            if ($this->usage_type === 'personal' && empty($this->assigned_users)) {
                $validator->errors()->add('assigned_users', 'Vui lòng gán ít nhất một người dùng khi mã giảm giá là cá nhân.');
            }

            // Validate: Nếu chỉ nhập 1 trong 2 trường valid_from_time hoặc valid_to_time thì báo lỗi
            if (($this->filled('valid_from_time') && !$this->filled('valid_to_time')) || (!$this->filled('valid_from_time') && $this->filled('valid_to_time'))) {
                $validator->errors()->add('valid_from_time', 'Vui lòng nhập cả hai trường thời gian bắt đầu và kết thúc trong ngày hoặc bỏ trống cả hai.');
                $validator->errors()->add('valid_to_time', 'Vui lòng nhập cả hai trường thời gian bắt đầu và kết thúc trong ngày hoặc bỏ trống cả hai.');
            }

            // Validate: Nếu có valid_days_of_week thì phải chọn ít nhất 1 ngày
            if ($this->has('valid_days_of_week') && is_array($this->valid_days_of_week) && count($this->valid_days_of_week) === 0) {
                $validator->errors()->add('valid_days_of_week', 'Vui lòng chọn ít nhất một ngày trong tuần áp dụng.');
            }

            // Validate: Nếu có thời gian áp dụng trong ngày thì phải chọn ít nhất 1 ngày trong tuần
            if (($this->filled('valid_from_time') && $this->filled('valid_to_time')) && 
                (!$this->has('valid_days_of_week') || !is_array($this->valid_days_of_week) || count($this->valid_days_of_week) === 0)) {
                $validator->errors()->add('valid_days_of_week', 'Khi có giới hạn thời gian trong ngày, vui lòng chọn ít nhất một ngày trong tuần áp dụng.');
            }

            // Validate: Phải có ít nhất một điều kiện thời gian (ngày trong tuần hoặc giờ trong ngày)
            $hasDaysOfWeek = $this->has('valid_days_of_week') && is_array($this->valid_days_of_week) && count($this->valid_days_of_week) > 0;
            $hasTimeRange = $this->filled('valid_from_time') && $this->filled('valid_to_time');
            
            if (!$hasDaysOfWeek && !$hasTimeRange) {
                $validator->errors()->add('valid_days_of_week', 'Vui lòng chọn ít nhất một ngày trong tuần hoặc nhập thời gian áp dụng trong ngày.');
                $validator->errors()->add('valid_from_time', 'Vui lòng chọn ít nhất một ngày trong tuần hoặc nhập thời gian áp dụng trong ngày.');
                $validator->errors()->add('valid_to_time', 'Vui lòng chọn ít nhất một ngày trong tuần hoặc nhập thời gian áp dụng trong ngày.');
            }

            // Validate: Nếu có min_requirement_type thì phải có min_requirement_value
            if ($this->filled('min_requirement_type') && !$this->filled('min_requirement_value')) {
                $validator->errors()->add('min_requirement_value', 'Vui lòng nhập giá trị điều kiện tối thiểu khi đã chọn loại điều kiện.');
            }

            // Validate: Nếu có min_requirement_value thì phải có min_requirement_type
            if ($this->filled('min_requirement_value') && !$this->filled('min_requirement_type')) {
                $validator->errors()->add('min_requirement_type', 'Vui lòng chọn loại điều kiện tối thiểu khi đã nhập giá trị.');
            }

            // Validate: max_discount_amount phải lớn hơn discount_value khi loại giảm giá là percentage
            if ($this->discount_type === 'percentage' && 
                $this->filled('max_discount_amount') && 
                $this->filled('discount_value') && 
                $this->max_discount_amount <= $this->discount_value) {
                $validator->errors()->add('max_discount_amount', 'Số tiền giảm tối đa phải lớn hơn giá trị giảm giá khi loại giảm giá là phần trăm.');
            }

            // Validate: min_requirement_value phải lớn hơn 0 khi có giá trị
            if ($this->filled('min_requirement_value') && $this->min_requirement_value <= 0) {
                $validator->errors()->add('min_requirement_value', 'Giá trị điều kiện tối thiểu phải lớn hơn 0.');
            }
        });
    }

    /**
     * Get the item type name for validation messages.
     */
    private function getItemTypeName($type): string
    {
        return match($type) {
            'specific_products' => 'sản phẩm',
            'specific_categories' => 'danh mục',
            'specific_combos' => 'combo',
            'specific_variants' => 'biến thể',
            default => 'item'
        };
    }
}

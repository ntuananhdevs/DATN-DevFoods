<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class ToppingRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $toppingId = $this->route('id') ?? $this->route('topping');
        
        return [
            'name' => 'required|string|max:255|unique:toppings,name,' . $toppingId,
            'price' => 'required|numeric|min:0|max:999999999',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:active,inactive,discontinued'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên topping là bắt buộc.',
            'name.string' => 'Tên topping phải là chuỗi ký tự.',
            'name.max' => 'Tên topping không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên topping này đã tồn tại.',
            
            'price.required' => 'Giá topping là bắt buộc.',
            'price.numeric' => 'Giá topping phải là số.',
            'price.min' => 'Giá topping không được nhỏ hơn 0.',
            'price.max' => 'Giá topping không được vượt quá 999,999,999.',
            
            'description.string' => 'Mô tả phải là chuỗi ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            
            'image.image' => 'File phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
            
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái phải là một trong các giá trị: đang bán, tạm ngưng, chưa bán nữa.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'tên topping',
            'price' => 'giá',
            'description' => 'mô tả',
            'image' => 'hình ảnh',
            'status' => 'trạng thái'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert active checkbox to boolean
        $this->merge([
            'active' => $this->has('active') ? true : false,
        ]);

        // Clean price input
        if ($this->has('price')) {
            $price = str_replace([',', '.', ' '], '', $this->price);
            $this->merge([
                'price' => is_numeric($price) ? (float) $price : $this->price,
            ]);
        }
    }
}
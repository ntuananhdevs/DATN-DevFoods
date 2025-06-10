<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'base_price' => 'required|numeric|min:0',
            'preparation_time' => 'required|integer|min:0',
            'short_description' => 'required|string|max:500',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'ingredients_json' => 'nullable|string',
            'attributes' => 'required|array',
            'attributes.*.name' => 'required_with:attributes|string|max:255',
            'attributes.*.values' => 'required_with:attributes|array',
            'attributes.*.values.*.value' => 'required_with:attributes.*.values|string|max:255',
            'attributes.*.values.*.price_adjustment' => 'nullable|numeric',
            'toppings' => 'nullable|array',
            'toppings.*.name' => 'required_with:toppings|string|max:255',
            'toppings.*.price' => 'required_with:toppings|numeric|min:0',
            'toppings.*.available' => 'nullable|boolean',
            'toppings.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'primary_image' => $isUpdate ? 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:coming_soon,selling,discontinued',
            'release_at' => 'nullable|date',
            'is_featured' => 'nullable|boolean',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->input('ingredients')) && empty($this->input('ingredients_json'))) {
                $validator->errors()->add('ingredients', 'Nguyên liệu là bắt buộc.');
            }
        });
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.string' => 'Tên sản phẩm phải là chuỗi ký tự.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'category_id.required' => 'Danh mục là bắt buộc.',
            'category_id.exists' => 'Danh mục đã chọn không hợp lệ.',
            'base_price.required' => 'Giá cơ bản là bắt buộc.',
            'short_description.required' => 'Mô tả ngắn là bắt buộc.',
            'base_price.numeric' => 'Giá cơ bản phải là số.',
            'ingredients.string' => 'Nguyên liệu phải là chuỗi ký tự.',
            'ingredients_json.string' => 'Dữ liệu nguyên liệu phải là chuỗi ký tự.',
            'base_price.min' => 'Giá cơ bản phải lớn hơn hoặc bằng 0.',
            'preparation_time.required' => 'Thời gian chuẩn bị là bắt buộc.',
            'preparation_time.integer' => 'Thời gian chuẩn bị phải là số nguyên.',
            'preparation_time.min' => 'Thời gian chuẩn bị phải lớn hơn hoặc bằng 0.',
            'short_description.string' => 'Mô tả ngắn phải là chuỗi ký tự.',
            'short_description.max' => 'Mô tả ngắn không được vượt quá 500 ký tự.',
            'description.string' => 'Mô tả chi tiết phải là chuỗi ký tự.',
            'attributes.required' => 'Thuộc tính là bắt buộc.',
            'attributes.array' => 'Thuộc tính phải là một mảng.',
            'attributes.*.name.required_with' => 'Tên thuộc tính là bắt buộc khi có thuộc tính.',
            'attributes.*.name.string' => 'Tên thuộc tính phải là chuỗi.',
            'attributes.*.name.max' => 'Tên thuộc tính không được vượt quá 255 ký tự.',
            'attributes.*.values.required_with' => 'Giá trị thuộc tính là bắt buộc khi có thuộc tính.',
            'attributes.*.values.array' => 'Giá trị thuộc tính phải là một mảng.',
            'attributes.*.values.*.value.required_with' => 'Tên giá trị là bắt buộc cho mỗi giá trị thuộc tính.',
            'attributes.*.values.*.value.string' => 'Tên giá trị phải là chuỗi.',
            'attributes.*.values.*.value.max' => 'Tên giá trị không được vượt quá 255 ký tự.',
            'attributes.*.values.*.price_adjustment.numeric' => 'Giá điều chỉnh phải là số.',
            'toppings.array' => 'Topping phải là một mảng.',
            'toppings.*.name.required_with' => 'Tên topping là bắt buộc khi có topping.',
            'toppings.*.name.string' => 'Tên topping phải là chuỗi.',
            'toppings.*.name.max' => 'Tên topping không được vượt quá 255 ký tự.',
            'toppings.*.price.required_with' => 'Giá topping là bắt buộc khi có topping.',
            'toppings.*.price.numeric' => 'Giá topping phải là số.',
            'toppings.*.price.min' => 'Giá topping phải lớn hơn hoặc bằng 0.',
            'toppings.*.available.boolean' => 'Trạng thái có sẵn của topping phải là boolean.',
            'toppings.*.image.image' => 'Ảnh topping phải là hình ảnh.',
            'toppings.*.image.mimes' => 'Ảnh topping phải có định dạng: jpeg, png, jpg, gif, svg.',
            'toppings.*.image.max' => 'Ảnh topping không được vượt quá 2MB.',
            'primary_image.required' => 'Hình ảnh chính là bắt buộc.',
            'primary_image.image' => 'Tệp tải lên phải là hình ảnh.',
            'primary_image.mimes' => 'Hình ảnh chính phải có định dạng: jpeg, png, jpg, gif, svg.',
            'primary_image.max' => 'Hình ảnh chính không được vượt quá 2MB.',
            'images.*.image' => 'Tệp tải lên phải là hình ảnh.',
            'images.*.mimes' => 'Hình ảnh bổ sung phải có định dạng: jpeg, png, jpg, gif, svg.',
            'images.*.max' => 'Hình ảnh bổ sung không được vượt quá 2MB.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ. Chỉ chấp nhận: sắp ra mắt, đang bán, ngừng bán.',
            'release_at.date' => 'Ngày phát hành phải là định dạng ngày hợp lệ.',
            'is_featured.boolean' => 'Trường nổi bật phải là đúng hoặc sai.',
        ];
    }
}
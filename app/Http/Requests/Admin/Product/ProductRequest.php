<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Models\Category;
use App\Models\Topping;

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
        $productId = $this->route('id');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        
        return [
            // Basic product information
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    $category = Category::find($value);
                    if ($category && !$category->status) {
                        $fail('Danh mục đã chọn không khả dụng.');
                    }
                }
            ],
            'base_price' => [
                'required',
                'numeric',
                'min:1000',
                'max:10000000'
            ],
            'preparation_time' => [
                'required',
                'integer',
                'min:1',
                'max:180'
            ],
            'short_description' => [
                'required',
                'string',
                'min:10',
                'max:500'
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000'
            ],
            
            // Ingredients validation
            'ingredients' => 'nullable|string|max:2000',
            'ingredients_json' => [
                'nullable',
                'string',
                'max:2000',
                function ($attribute, $value, $fail) {
                    if ($value && !$this->isValidJson($value)) {
                        $fail('Dữ liệu nguyên liệu không đúng định dạng JSON.');
                    }
                }
            ],
            
            // Attributes validation
            'attributes' => 'required|array|min:1',
            'attributes.*.name' => [
                'required_with:attributes',
                'string',
                'min:2',
                'max:255'
            ],
            'attributes.*.values' => [
                'required_with:attributes',
                'array',
                'min:1',
                'max:20'
            ],
            'attributes.*.values.*.value' => [
                'required_with:attributes.*.values',
                'string',
                'min:1',
                'max:255'
            ],
            'attributes.*.values.*.price_adjustment' => [
                'nullable',
                'numeric',
                'min:-1000000',
                'max:1000000'
            ],
            
            // Toppings validation
            'toppings' => 'nullable|array|max:50',
            'toppings.*' => [
                'exists:toppings,id',
                function ($attribute, $value, $fail) {
                    $topping = Topping::find($value);
                    if ($topping && !$topping->available) {
                        $fail('Topping đã chọn không khả dụng.');
                    }
                }
            ],
            
            // Image validation
            'primary_image' => $isUpdate 
                ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120|dimensions:min_width=300,min_height=300'
                : 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120|dimensions:min_width=300,min_height=300',
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120|dimensions:min_width=300,min_height=300',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:product_images,id',
            
            // Status and availability
            'status' => [
                'required',
                'in:coming_soon,selling,discontinued'
            ],
            'available' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'release_at' => [
                'nullable',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    if ($value && $this->input('status') !== 'coming_soon') {
                        $fail('Ngày phát hành chỉ áp dụng cho sản phẩm sắp ra mắt.');
                    }
                }
            ],
            
            // Variant stocks validation
            'variant_stocks' => 'nullable|array',
            'variant_stocks.*' => 'nullable|array',
            'variant_stocks.*.*' => 'nullable|integer|min:0|max:999999',
        ];
    }
    
    /**
     * Check if string is valid JSON
     */
    private function isValidJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
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
            // Validate ingredients requirement
            if (empty($this->input('ingredients')) && empty($this->input('ingredients_json'))) {
                $validator->errors()->add('ingredients', 'Nguyên liệu là bắt buộc.');
            }
            
            // Validate attribute uniqueness within the same product
            $attributes = $this->input('attributes', []);
            $attributeNames = [];
            foreach ($attributes as $index => $attribute) {
                if (isset($attribute['name'])) {
                    $name = strtolower(trim($attribute['name']));
                    if (in_array($name, $attributeNames)) {
                        $validator->errors()->add("attributes.{$index}.name", 'Tên thuộc tính đã tồn tại trong sản phẩm này.');
                    }
                    $attributeNames[] = $name;
                }
                
                // Validate attribute values uniqueness
                if (isset($attribute['values']) && is_array($attribute['values'])) {
                    $valueNames = [];
                    foreach ($attribute['values'] as $valueIndex => $value) {
                        if (isset($value['value'])) {
                            $valueName = strtolower(trim($value['value']));
                            if (in_array($valueName, $valueNames)) {
                                $validator->errors()->add("attributes.{$index}.values.{$valueIndex}.value", 'Giá trị thuộc tính đã tồn tại.');
                            }
                            $valueNames[] = $valueName;
                        }
                    }
                }
            }
            
            // Validate topping uniqueness
            $toppings = $this->input('toppings', []);
            if (count($toppings) !== count(array_unique($toppings))) {
                $validator->errors()->add('toppings', 'Không được chọn trùng lặp topping.');
            }
            
            // Validate release date logic
            $status = $this->input('status');
            $releaseAt = $this->input('release_at');
            
            if ($status === 'coming_soon' && empty($releaseAt)) {
                $validator->errors()->add('release_at', 'Ngày phát hành là bắt buộc cho sản phẩm sắp ra mắt.');
            }
            
            if ($status !== 'coming_soon' && !empty($releaseAt)) {
                $validator->errors()->add('release_at', 'Chỉ sản phẩm sắp ra mắt mới có ngày phát hành.');
            }
            
            // Validate image count for update
            if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
                $deleteImages = $this->input('delete_images', []);
                $newImages = $this->file('images', []);
                $hasNewPrimaryImage = $this->hasFile('primary_image');
                
                // Get current product to check existing images
                $productId = $this->route('id');
                $product = $productId ? Product::find($productId) : null;
                if ($product) {
                    $currentImageCount = $product->images()->count();
                    $remainingImages = $currentImageCount - count($deleteImages);
                    $totalAfterUpdate = $remainingImages + count($newImages) + ($hasNewPrimaryImage ? 1 : 0);
                    
                    if ($remainingImages <= 0 && !$hasNewPrimaryImage && empty($newImages)) {
                        $validator->errors()->add('primary_image', 'Sản phẩm phải có ít nhất một hình ảnh.');
                    }
                }
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
            // Basic product information messages
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.string' => 'Tên sản phẩm phải là chuỗi ký tự.',
            'name.min' => 'Tên sản phẩm phải có ít nhất 3 ký tự.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
            
            'category_id.required' => 'Danh mục là bắt buộc.',
            'category_id.exists' => 'Danh mục đã chọn không hợp lệ.',
            
            'base_price.required' => 'Giá cơ bản là bắt buộc.',
            'base_price.numeric' => 'Giá cơ bản phải là số.',
            'base_price.min' => 'Giá cơ bản phải ít nhất 1,000 VNĐ.',
            'base_price.max' => 'Giá cơ bản không được vượt quá 10,000,000 VNĐ.',
            
            'preparation_time.required' => 'Thời gian chuẩn bị là bắt buộc.',
            'preparation_time.integer' => 'Thời gian chuẩn bị phải là số nguyên.',
            'preparation_time.min' => 'Thời gian chuẩn bị phải ít nhất 1 phút.',
            'preparation_time.max' => 'Thời gian chuẩn bị không được vượt quá 180 phút.',
            
            'short_description.required' => 'Mô tả ngắn là bắt buộc.',
            'short_description.string' => 'Mô tả ngắn phải là chuỗi ký tự.',
            'short_description.min' => 'Mô tả ngắn phải có ít nhất 10 ký tự.',
            'short_description.max' => 'Mô tả ngắn không được vượt quá 500 ký tự.',
            
            'description.string' => 'Mô tả chi tiết phải là chuỗi ký tự.',
            'description.max' => 'Mô tả chi tiết không được vượt quá 5,000 ký tự.',
            
            // Ingredients messages
            'ingredients.string' => 'Nguyên liệu phải là chuỗi ký tự.',
            'ingredients.max' => 'Nguyên liệu không được vượt quá 2,000 ký tự.',
            'ingredients_json.string' => 'Dữ liệu nguyên liệu phải là chuỗi ký tự.',
            'ingredients_json.max' => 'Dữ liệu nguyên liệu không được vượt quá 2,000 ký tự.',
            
            // Attributes messages
            'attributes.required' => 'Thuộc tính là bắt buộc.',
            'attributes.array' => 'Thuộc tính phải là một mảng.',
            'attributes.min' => 'Sản phẩm phải có ít nhất một thuộc tính.',
            'attributes.*.name.required_with' => 'Tên thuộc tính là bắt buộc khi có thuộc tính.',
            'attributes.*.name.string' => 'Tên thuộc tính phải là chuỗi.',
            'attributes.*.name.min' => 'Tên thuộc tính phải có ít nhất 2 ký tự.',
            'attributes.*.name.max' => 'Tên thuộc tính không được vượt quá 255 ký tự.',
            'attributes.*.values.required_with' => 'Giá trị thuộc tính là bắt buộc khi có thuộc tính.',
            'attributes.*.values.array' => 'Giá trị thuộc tính phải là một mảng.',
            'attributes.*.values.min' => 'Thuộc tính phải có ít nhất một giá trị.',
            'attributes.*.values.max' => 'Thuộc tính không được có quá 20 giá trị.',
            'attributes.*.values.*.value.required_with' => 'Tên giá trị là bắt buộc cho mỗi giá trị thuộc tính.',
            'attributes.*.values.*.value.string' => 'Tên giá trị phải là chuỗi.',
            'attributes.*.values.*.value.min' => 'Tên giá trị phải có ít nhất 1 ký tự.',
            'attributes.*.values.*.value.max' => 'Tên giá trị không được vượt quá 255 ký tự.',
            'attributes.*.values.*.price_adjustment.numeric' => 'Giá điều chỉnh phải là số.',
            'attributes.*.values.*.price_adjustment.min' => 'Giá điều chỉnh không được nhỏ hơn -1,000,000.',
            'attributes.*.values.*.price_adjustment.max' => 'Giá điều chỉnh không được lớn hơn 1,000,000.',
            
            // Toppings messages
            'toppings.array' => 'Topping phải là một mảng.',
            'toppings.max' => 'Không được chọn quá 50 topping.',
            'toppings.*.exists' => 'Topping đã chọn không hợp lệ.',
            
            // Image messages
            'primary_image.required' => 'Hình ảnh chính là bắt buộc.',
            'primary_image.image' => 'Tệp tải lên phải là hình ảnh.',
            'primary_image.mimes' => 'Hình ảnh chính phải có định dạng: jpeg, png, jpg, gif, webp.',
            'primary_image.max' => 'Hình ảnh chính không được vượt quá 5MB.',
            'primary_image.dimensions' => 'Hình ảnh chính phải có kích thước tối thiểu 300x300 pixel.',
            
            'images.array' => 'Danh sách hình ảnh phải là một mảng.',
            'images.max' => 'Không được tải lên quá 10 hình ảnh.',
            'images.*.image' => 'Tệp tải lên phải là hình ảnh.',
            'images.*.mimes' => 'Hình ảnh bổ sung phải có định dạng: jpeg, png, jpg, gif, webp.',
            'images.*.max' => 'Hình ảnh bổ sung không được vượt quá 5MB.',
            'images.*.dimensions' => 'Hình ảnh bổ sung phải có kích thước tối thiểu 300x300 pixel.',
            
            'delete_images.array' => 'Danh sách hình ảnh xóa phải là một mảng.',
            'delete_images.*.integer' => 'ID hình ảnh xóa phải là số nguyên.',
            'delete_images.*.exists' => 'Hình ảnh cần xóa không tồn tại.',
            
            // Status and availability messages
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ. Chỉ chấp nhận: sắp ra mắt, đang bán, ngừng bán.',
            'available.boolean' => 'Trạng thái khả dụng phải là đúng hoặc sai.',
            'is_featured.boolean' => 'Trường nổi bật phải là đúng hoặc sai.',
            'release_at.date' => 'Ngày phát hành phải là định dạng ngày hợp lệ.',
            'release_at.after_or_equal' => 'Ngày phát hành phải từ hôm nay trở đi.',
        ];
    }
}
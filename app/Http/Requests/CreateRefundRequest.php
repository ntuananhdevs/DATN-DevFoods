<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRefundRequest extends FormRequest
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
        return [
            'order_id' => [
                'required',
                'integer',
                'exists:orders,id',
                function ($attribute, $value, $fail) {
                    $order = \App\Models\Order::find($value);
                    if (!$order) {
                        $fail('Đơn hàng không tồn tại.');
                        return;
                    }
                    
                    // Check if order belongs to authenticated user
                    if ($order->customer_id !== auth()->id()) {
                        $fail('Bạn không có quyền yêu cầu hoàn tiền cho đơn hàng này.');
                        return;
                    }
                    
                    // Check if order status allows refund
                    if (!in_array($order->status, ['delivered', 'completed'])) {
                        $fail('Chỉ có thể yêu cầu hoàn tiền cho đơn hàng đã giao thành công.');
                        return;
                    }
                    
                    // Check if refund request already exists
                    $existingRefund = \App\Models\RefundRequest::where('order_id', $value)
                        ->whereNotIn('status', ['cancelled', 'rejected'])
                        ->first();
                    if ($existingRefund) {
                        $fail('Đã có yêu cầu hoàn tiền cho đơn hàng này.');
                    }
                }
            ],
            'refund_amount' => [
                'required',
                'numeric',
                'min:1000',
                function ($attribute, $value, $fail) {
                    $orderId = $this->input('order_id');
                    if ($orderId) {
                        $order = \App\Models\Order::find($orderId);
                        if ($order && $value > $order->total_amount) {
                            $fail('Số tiền hoàn không được vượt quá tổng giá trị đơn hàng.');
                        }
                    }
                }
            ],
            'refund_type' => [
                'required',
                'string',
                Rule::in(['full', 'partial'])
            ],
            'reason' => [
                'required',
                'string',
                'max:500',
                'min:10'
            ],
            'customer_message' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'attachments' => [
                'nullable',
                'array',
                'max:5'
            ],
            'attachments.*' => [
                'file',
                'mimes:jpeg,jpg,png,pdf,doc,docx',
                'max:5120' // 5MB
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'order_id.required' => 'Vui lòng chọn đơn hàng.',
            'order_id.exists' => 'Đơn hàng không tồn tại.',
            'refund_amount.required' => 'Vui lòng nhập số tiền hoàn.',
            'refund_amount.numeric' => 'Số tiền hoàn phải là số.',
            'refund_amount.min' => 'Số tiền hoàn tối thiểu là 1,000 VNĐ.',
            'refund_type.required' => 'Vui lòng chọn loại hoàn tiền.',
            'refund_type.in' => 'Loại hoàn tiền không hợp lệ.',
            'reason.required' => 'Vui lòng nhập lý do hoàn tiền.',
            'reason.max' => 'Lý do hoàn tiền không được vượt quá 500 ký tự.',
            'reason.min' => 'Lý do hoàn tiền phải có ít nhất 10 ký tự.',
            'customer_message.max' => 'Tin nhắn không được vượt quá 1000 ký tự.',
            'attachments.max' => 'Chỉ được đính kèm tối đa 5 file.',
            'attachments.*.file' => 'File đính kèm không hợp lệ.',
            'attachments.*.mimes' => 'File đính kèm phải có định dạng: jpeg, jpg, png, pdf, doc, docx.',
            'attachments.*.max' => 'Kích thước file không được vượt quá 5MB.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'order_id' => 'đơn hàng',
            'refund_amount' => 'số tiền hoàn',
            'refund_type' => 'loại hoàn tiền',
            'reason' => 'lý do',
            'customer_message' => 'tin nhắn',
            'attachments' => 'file đính kèm'
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\RefundRequest;

class UpdateRefundStatusRequest extends FormRequest
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
        $refundRequest = $this->route('refund') ?? $this->route('id');
        
        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    RefundRequest::STATUS_PENDING,
                    RefundRequest::STATUS_UNDER_REVIEW,
                    RefundRequest::STATUS_APPROVED,
                    RefundRequest::STATUS_REJECTED,
                    RefundRequest::STATUS_PROCESSING,
                    RefundRequest::STATUS_COMPLETED,
                    RefundRequest::STATUS_CANCELLED
                ]),
                function ($attribute, $value, $fail) use ($refundRequest) {
                    if ($refundRequest) {
                        $refund = is_object($refundRequest) ? $refundRequest : RefundRequest::find($refundRequest);
                        if ($refund && !$this->isValidStatusTransition($refund->status, $value)) {
                            $fail('Không thể chuyển từ trạng thái "' . $this->getStatusLabel($refund->status) . '" sang "' . $this->getStatusLabel($value) . '".');
                        }
                    }
                }
            ],
            'admin_note' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'reason' => [
                'required_if:status,' . RefundRequest::STATUS_REJECTED,
                'nullable',
                'string',
                'max:500',
                'min:10'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'admin_note.max' => 'Ghi chú không được vượt quá 1000 ký tự.',
            'reason.required_if' => 'Vui lòng nhập lý do từ chối.',
            'reason.max' => 'Lý do không được vượt quá 500 ký tự.',
            'reason.min' => 'Lý do phải có ít nhất 10 ký tự.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'status' => 'trạng thái',
            'admin_note' => 'ghi chú',
            'reason' => 'lý do'
        ];
    }

    /**
     * Check if status transition is valid
     */
    private function isValidStatusTransition(string $currentStatus, string $newStatus): bool
    {
        $validTransitions = [
            RefundRequest::STATUS_PENDING => [
                RefundRequest::STATUS_UNDER_REVIEW,
                RefundRequest::STATUS_CANCELLED,
                RefundRequest::STATUS_REJECTED
            ],
            RefundRequest::STATUS_UNDER_REVIEW => [
                RefundRequest::STATUS_APPROVED,
                RefundRequest::STATUS_REJECTED,
                RefundRequest::STATUS_PENDING
            ],
            RefundRequest::STATUS_APPROVED => [
                RefundRequest::STATUS_PROCESSING,
                RefundRequest::STATUS_REJECTED
            ],
            RefundRequest::STATUS_PROCESSING => [
                RefundRequest::STATUS_COMPLETED,
                RefundRequest::STATUS_REJECTED
            ],
            RefundRequest::STATUS_REJECTED => [],
            RefundRequest::STATUS_COMPLETED => [],
            RefundRequest::STATUS_CANCELLED => []
        ];

        return in_array($newStatus, $validTransitions[$currentStatus] ?? []);
    }

    /**
     * Get status label in Vietnamese
     */
    private function getStatusLabel(string $status): string
    {
        $labels = [
            RefundRequest::STATUS_PENDING => 'Chờ xử lý',
            RefundRequest::STATUS_UNDER_REVIEW => 'Đang xem xét',
            RefundRequest::STATUS_APPROVED => 'Đã duyệt',
            RefundRequest::STATUS_REJECTED => 'Đã từ chối',
            RefundRequest::STATUS_PROCESSING => 'Đang xử lý',
            RefundRequest::STATUS_COMPLETED => 'Hoàn thành',
            RefundRequest::STATUS_CANCELLED => 'Đã hủy'
        ];

        return $labels[$status] ?? $status;
    }
}
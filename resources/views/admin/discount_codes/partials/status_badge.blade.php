@php
    $now = now();
    if (!$discountCode->is_active) {
        $status = 'inactive';
        $statusText = 'Không hoạt động';
    } elseif ($now->gt($discountCode->end_date)) {
        $status = 'expired';
        $statusText = 'Đã hết hạn';
    } else {
        $status = 'active';
        $statusText = 'Hoạt động';
    }
@endphp
<span class="status-badge {{ $status }}">{{ $statusText }}</span> 
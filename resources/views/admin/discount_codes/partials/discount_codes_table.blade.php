<table class="w-full">
    <thead>
        <tr class="border-b bg-muted/50">
            <th class="py-3 px-4 text-left font-medium">
                <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300">
            </th>
            <th class="py-3 px-4 text-left font-medium">Mã</th>
            <th class="py-3 px-4 text-left font-medium">Tên</th>
            <th class="py-3 px-4 text-left font-medium">Loại giảm giá</th>
            <th class="py-3 px-4 text-center font-medium">Giá trị</th>
            <th class="py-3 px-4 text-center font-medium">Hiệu lực</th>
            <th class="py-3 px-4 text-center font-medium">Loại sử dụng</th>
            <th class="py-3 px-4 text-left font-medium">Trạng thái</th>
            <th class="py-3 px-4 text-center font-medium">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @forelse($discountCodes as $code)
        <tr class="border-b hover:bg-muted/20">
            <td class="py-3 px-4">
                <input type="checkbox" name="ids[]" value="{{ $code->id }}" class="discount-checkbox rounded border-gray-300">
            </td>
            <td class="py-3 px-4">
                <div class="font-mono font-medium">{{ $code->code }}</div>
            </td>
            <td class="py-3 px-4">
                <div>
                    <div class="font-medium">{{ $code->name }}</div>
                    <div class="text-sm text-muted-foreground">{{ Str::limit($code->description ?? '', 50) }}</div>
                </div>
            </td>
            <td class="py-3 px-4">
                @php
                    $typeClass = 'percentage';
                    $typeText = 'Phần trăm';
                    switch($code->discount_type) {
                        case 'fixed_amount':
                            $typeClass = 'fixed-amount';
                            $typeText = 'Số tiền cố định';
                            break;
                        case 'free_shipping':
                            $typeClass = 'free-shipping';
                            $typeText = 'Miễn phí vận chuyển';
                            break;
                    }
                @endphp
                <span class="discount-type {{ $typeClass }}">{{ $typeText }}</span>
            </td>
            <td class="py-3 px-4 text-center">
                @if($code->discount_type == 'percentage')
                    <span class="value-display percentage">{{ $code->discount_value }}%</span>
                @elseif($code->discount_type == 'fixed_amount')
                    <span class="value-display amount">{{ number_format($code->discount_value) }} đ</span>
                @else
                    <span class="value-display">Miễn phí ship</span>
                @endif
            </td>
            <td class="py-3 px-4 text-center">
                <div class="date-range">
                    <div class="start-date">{{ $code->start_date->format('d/m/Y') }}</div>
                    <div>đến {{ $code->end_date->format('d/m/Y') }}</div>
                </div>
            </td>
            <td class="py-3 px-4 text-center">
                <span class="px-2 py-1 rounded-full text-xs {{ $code->usage_type === 'public' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                    {{ $code->usage_type === 'public' ? 'Công khai' : 'Cá nhân' }}
                </span>
            </td>
            <td class="py-3 px-4">
                @php
                    $now = now();
                    if (!$code->is_active) {
                        $status = 'inactive';
                        $statusText = 'Không hoạt động';
                    } elseif ($now->gt($code->end_date)) {
                        $status = 'expired';
                        $statusText = 'Đã hết hạn';
                    } else {
                        $status = 'active';
                        $statusText = 'Hoạt động';
                    }
                @endphp
                <span class="status-badge {{ $status }}">{{ $statusText }}</span>
            </td>
            <td class="py-3 px-4">
                <div class="flex justify-center space-x-1">
                    <a href="{{ route('admin.discount_codes.show', $code->id) }}"
                        class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                        title="Xem chi tiết">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </a>
                    <a href="{{ route('admin.discount_codes.edit', $code->id) }}"
                        class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                        title="Chỉnh sửa">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </a>
                    <form action="{{ route('admin.discount_codes.destroy', $code->id) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent delete-btn"
                            data-code="{{ $code->code }}"
                            title="Xóa">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr class="empty-row">
            <td colspan="9" class="text-center py-4">
                <div class="flex flex-col items-center justify-center text-muted-foreground py-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mb-2">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2Z"></path>
                        <path d="M13 5v2"></path>
                        <path d="M13 17v2"></path>
                        <path d="M13 11v2"></path>
                    </svg>
                    <h3 class="text-lg font-medium">Không có mã giảm giá nào</h3>
                    <p class="text-sm">Hãy tạo mã giảm giá mới để bắt đầu</p>
                    <a href="{{ route('admin.discount_codes.create') }}" class="btn btn-primary mt-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                            <path d="M5 12h14"></path>
                            <path d="M12 5v14"></path>
                        </svg>
                        Tạo mã giảm giá mới
                    </a>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table> 
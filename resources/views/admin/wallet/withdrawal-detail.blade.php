<div class="space-y-6">
    <!-- Withdrawal Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Basic Info -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-semibold text-gray-900 mb-3">Thông Tin Giao Dịch</h4>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">ID:</span>
                    <span class="font-mono font-semibold">#{{ $withdrawal->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Mã giao dịch:</span>
                    <span class="font-mono text-sm">{{ $withdrawal->transaction_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Số tiền:</span>
                    <span class="font-bold text-red-600">{{ number_format($withdrawal->amount, 0, ',', '.') }} VND</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Trạng thái:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        <i class="fas fa-clock mr-1"></i>
                        Chờ duyệt
                    </span>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-semibold text-gray-900 mb-3">Thông Tin Khách Hàng</h4>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tên:</span>
                    <span class="font-medium">{{ $withdrawal->user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span class="text-sm">{{ $withdrawal->user->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Số dư hiện tại:</span>
                    <span class="font-bold {{ $withdrawal->user->balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($withdrawal->user->balance, 0, ',', '.') }} VND
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Information -->
    @php
        $bankInfo = is_string($withdrawal->metadata) ? json_decode($withdrawal->metadata, true) : $withdrawal->metadata;
    @endphp
    <div class="bg-blue-50 p-4 rounded-lg">
        <h4 class="font-semibold text-gray-900 mb-3">Thông Tin Ngân Hàng</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <span class="text-gray-600 text-sm">Ngân hàng:</span>
                <div class="font-medium">{{ $bankInfo['bank_name'] ?? 'N/A' }}</div>
            </div>
            <div>
                <span class="text-gray-600 text-sm">Số tài khoản:</span>
                <div class="font-mono font-medium">{{ $bankInfo['bank_account'] ?? 'N/A' }}</div>
            </div>
            <div>
                <span class="text-gray-600 text-sm">Chủ tài khoản:</span>
                <div class="font-medium">{{ $bankInfo['account_holder'] ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-semibold text-gray-900 mb-3">Thời Gian</h4>
        <div class="space-y-3">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mr-3"></div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">Tạo yêu cầu</div>
                    <div class="text-sm text-gray-500">{{ $withdrawal->created_at->format('d/m/Y H:i:s') }}</div>
                    <div class="text-xs text-gray-400">{{ $withdrawal->created_at->diffForHumans() }}</div>
                </div>
            </div>
            
            @if($withdrawal->processed_at)
            <div class="flex items-center">
                <div class="flex-shrink-0 w-2 h-2 bg-green-600 rounded-full mr-3"></div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">Đã xử lý</div>
                    <div class="text-sm text-gray-500">{{ $withdrawal->processed_at->format('d/m/Y H:i:s') }}</div>
                </div>
            </div>
            @endif

            @if($withdrawal->expires_at)
            <div class="flex items-center">
                <div class="flex-shrink-0 w-2 h-2 {{ $withdrawal->expires_at < now() ? 'bg-red-600' : 'bg-yellow-600' }} rounded-full mr-3"></div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">{{ $withdrawal->expires_at < now() ? 'Đã hết hạn' : 'Hết hạn' }}</div>
                    <div class="text-sm {{ $withdrawal->expires_at < now() ? 'text-red-500' : 'text-gray-500' }}">
                        {{ $withdrawal->expires_at->format('d/m/Y H:i:s') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Additional Information -->
    @if($withdrawal->description || $withdrawal->admin_notes)
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-semibold text-gray-900 mb-3">Ghi Chú</h4>
        
        @if($withdrawal->description)
        <div class="mb-3">
            <span class="text-gray-600 text-sm">Mô tả:</span>
            <div class="mt-1 text-gray-900">{{ $withdrawal->description }}</div>
        </div>
        @endif
        
        @if($withdrawal->admin_notes)
        <div>
            <span class="text-gray-600 text-sm">Ghi chú admin:</span>
            <div class="mt-1 text-gray-900">{{ $withdrawal->admin_notes }}</div>
        </div>
        @endif
    </div>
    @endif

    <!-- Risk Assessment -->
    <div class="bg-yellow-50 p-4 rounded-lg">
        <h4 class="font-semibold text-gray-900 mb-3">Đánh Giá Rủi Ro</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 text-sm">Số tiền:</span>
                    @if($withdrawal->amount < 50000)
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">
                            <i class="fas fa-exclamation mr-1"></i>
                            Dưới giới hạn tối thiểu
                        </span>
                    @elseif($withdrawal->amount > 5000000)
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Vượt giới hạn tối đa
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                            <i class="fas fa-check mr-1"></i>
                            Trong giới hạn (50K - 5M)
                        </span>
                    @endif
                </div>
            </div>
            
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 text-sm">Số dư sau rút:</span>
                    @php
                        $balanceAfter = $withdrawal->user->balance + $withdrawal->amount; // Vì số dư hiện tại đã trừ rồi
                    @endphp
                    <span class="font-medium {{ $balanceAfter >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($balanceAfter, 0, ',', '.') }} VND
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Notice -->
    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-lg"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800">Thông Tin</h4>
                <p class="text-sm text-blue-700">Đây là trang xem thông tin chi tiết. Để thực hiện thao tác duyệt/từ chối, vui lòng truy cập trang quản lý đơn rút tiền.</p>
            </div>
        </div>
    </div>
</div>

<style>
.transition-colors {
    transition-property: color, background-color, border-color;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>

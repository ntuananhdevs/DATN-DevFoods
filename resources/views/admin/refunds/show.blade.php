@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chi tiết yêu cầu hoàn tiền')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Chi tiết yêu cầu hoàn tiền</h1>
                <p class="text-gray-600 mt-1">Mã yêu cầu: <span class="font-medium">#{{ $refundRequest->refund_code }}</span></p>
            </div>
            <a href="{{ route('admin.refunds.index') }}" 
               class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
        </div>
        
        <div class="flex items-center">
            @if($refundRequest->status === 'pending')
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                    <i class="fas fa-clock mr-2"></i>Chờ xử lý
                </span>
            @elseif($refundRequest->status === 'processing')
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                    <i class="fas fa-spinner mr-2"></i>Đang xử lý
                </span>
            @elseif($refundRequest->status === 'approved')
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                    <i class="fas fa-check mr-2"></i>Đã duyệt
                </span>
            @elseif($refundRequest->status === 'completed')
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                    <i class="fas fa-check-double mr-2"></i>Hoàn thành
                </span>
            @elseif($refundRequest->status === 'rejected')
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                    <i class="fas fa-times mr-2"></i>Từ chối
                </span>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-6">
            <!-- Refund Request Information -->
            <div class="bg-white p-6 rounded border">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Thông tin yêu cầu hoàn tiền
                </h2>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Mã yêu cầu</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $refundRequest->refund_code }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Số tiền hoàn</label>
                                <p class="mt-1 text-sm font-semibold text-green-600">{{ number_format($refundRequest->refund_amount, 0, ',', '.') }}đ</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Ngày tạo</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $refundRequest->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            
                            @if($refundRequest->processed_at)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Ngày xử lý</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $refundRequest->processed_at->format('d/m/Y H:i') }}</p>
                            </div>
                            @endif
                            
                            @if($refundRequest->completed_at)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Ngày hoàn thành</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $refundRequest->completed_at->format('d/m/Y H:i') }}</p>
                            </div>
                            @endif
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Chi nhánh</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $refundRequest->branch->name ?? 'N/A' }}</p>
                            </div>
                            
                            @if($refundRequest->processedBy)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Người xử lý</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $refundRequest->processedBy->full_name }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($refundRequest->reason)
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                        <h4 class="font-medium text-gray-800 mb-2">Lý do hoàn tiền:</h4>
                        <p class="text-sm text-gray-700">{{ $refundRequest->reason }}</p>
                    </div>
                    @endif
                    
                    @if($refundRequest->admin_note)
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded">
                        <h4 class="font-medium text-gray-800 mb-2">Ghi chú admin:</h4>
                        <p class="text-sm text-gray-700">{{ $refundRequest->admin_note }}</p>
                    </div>
                    @endif
                        
                    @if($refundRequest->attachments && count($refundRequest->attachments) > 0)
                    <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded">
                        <h4 class="font-medium text-gray-800 mb-3">
                            <i class="fas fa-paperclip mr-2"></i>
                            File đính kèm ({{ count($refundRequest->attachments) }}):
                        </h4>
                        <div class="space-y-3">
                            @foreach($refundRequest->attachments as $attachment)
                                @php
                                    $fileExtension = strtolower(pathinfo($attachment['path'], PATHINFO_EXTENSION));
                                    $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    $isPdf = $fileExtension === 'pdf';
                                    $isVideo = in_array($fileExtension, ['mp4', 'avi', 'mov', 'wmv']);
                                    $fileSize = isset($attachment['size']) ? round($attachment['size'] / 1024, 1) . ' KB' : 'N/A';
                                @endphp
                                
                                <div class="flex items-center p-3 bg-white border rounded">
                                    @if($isImage)
                                        <img src="{{ asset('storage/' . $attachment['path']) }}" 
                                             alt="{{ $attachment['original_name'] ?? 'Attachment' }}"
                                             class="w-12 h-12 object-cover rounded cursor-pointer"
                                             onclick="openImageModal('{{ asset('storage/' . $attachment['path']) }}', '{{ $attachment['original_name'] ?? 'Image' }}')">
                                    @elseif($isPdf)
                                        <div class="w-12 h-12 bg-red-100 rounded flex items-center justify-center">
                                            <i class="fas fa-file-pdf text-red-600"></i>
                                        </div>
                                    @elseif($isVideo)
                                        <div class="w-12 h-12 bg-purple-100 rounded flex items-center justify-center">
                                            <i class="fas fa-file-video text-purple-600"></i>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                            <i class="fas fa-file text-gray-600"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $attachment['original_name'] ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500">{{ $fileSize }}</p>
                                    </div>
                                    
                                    <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                       download="{{ $attachment['original_name'] ?? 'download' }}"
                                       class="ml-3 px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                        <i class="fas fa-download mr-1"></i>
                                        Tải
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    </div>
                </div>

            <!-- Order Info -->
            @if($refundRequest->order)
            <div class="bg-white p-6 rounded border">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-shopping-cart mr-2 text-green-500"></i>
                    Thông tin đơn hàng
                </h2>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Mã đơn hàng</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $refundRequest->order->order_code }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Ngày đặt</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $refundRequest->order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Tổng tiền</label>
                                <p class="mt-1 text-sm font-semibold text-green-600">{{ number_format($refundRequest->order->total_amount, 0, ',', '.') }}đ</p>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $refundRequest->order->status }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Phương thức thanh toán</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $refundRequest->order->payment_method }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Customer Info & Actions -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Customer Info -->
            @if($refundRequest->customer)
            <div class="bg-white rounded-lg shadow border">
                <div class="bg-gray-50 px-4 py-3 border-b">
                    <h2 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-user mr-2"></i>
                        Thông tin khách hàng
                    </h2>
                </div>
                <div class="p-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Họ tên</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $refundRequest->customer->full_name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900 break-all">{{ $refundRequest->customer->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $refundRequest->customer->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            @if(in_array($refundRequest->status, ['pending', 'processing', 'approved']))
            <div class="bg-white p-6 rounded border">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-cogs mr-2 text-orange-500"></i>
                    Hành động
                </h2>
                <div class="space-y-3">
                        @if(in_array($refundRequest->status, ['pending', 'processing']))
                        <form action="{{ route('admin.refunds.approve', $refundRequest->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <i class="fas fa-check mr-2"></i> Duyệt yêu cầu
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.refunds.reject', $refundRequest->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                <i class="fas fa-times mr-2"></i> Từ chối
                            </button>
                        </form>
                        @endif
                        
                        @if($refundRequest->status === 'approved')
                        <form action="{{ route('admin.refunds.complete', $refundRequest->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-check-double mr-2"></i> Hoàn thành
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeImageModal()"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="imageModalTitle">Xem hình ảnh</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeImageModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="text-center">
                    <img id="modalImage" src="" alt="" class="max-w-full max-h-96 mx-auto rounded-lg">
                </div>
            </div>
        </div>
    </div>
</div>
 @endsection

@push('scripts')
<script>
function openImageModal(imageSrc, imageName) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModalTitle').textContent = imageName;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('modalImage').src = '';
}

// Close modal when pressing Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endpush
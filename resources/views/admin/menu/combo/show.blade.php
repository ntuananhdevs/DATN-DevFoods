@extends('layouts/admin/contentLayoutMaster')
@section('title', 'Chi Tiết Combo')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto p-6 space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4 justify-end">
            <div class="flex-1">
                <h1 class="text-3xl font-bold">Chi Tiết Combo: {{ $combo->name }}</h1>
                <p class="text-gray-600">Thông tin chi tiết về combo</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.combos.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    <i class="fas fa-arrow-left w-4 h-4 mr-2"></i>
                    Quay lại
                </a>
                <a href="{{ route('admin.combos.edit', $combo) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-md">
                    <i class="fas fa-edit w-4 h-4 mr-2"></i>
                    Chỉnh sửa
                </a>

            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Hình ảnh và thông tin chính -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                    <!-- Hình ảnh combo -->
                    <div class="relative">
                        @if($combo->image)
                            <img src="{{ $combo->image_url }}" alt="{{ $combo->name }}" class="w-full h-80 object-cover">
                        @else
                            <div class="w-full h-80 bg-gray-100 flex items-center justify-center">
                                <div class="text-center">
                                    <i class="fas fa-image fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-gray-500">Không có hình ảnh</p>
                                </div>
                            </div>
                        @endif
                        <div class="absolute top-4 right-4 flex gap-2">
                            @if($combo->status === 'selling')
                                <span class="px-3 py-1 text-sm font-medium bg-green-100 text-green-800 rounded-full">Đang bán</span>
                            @elseif($combo->status === 'coming_soon')
                                <span class="px-3 py-1 text-sm font-medium bg-yellow-100 text-yellow-800 rounded-full">Sắp bán</span>
                            @elseif($combo->status === 'discontinued')
                                <span class="px-3 py-1 text-sm font-medium bg-gray-200 text-gray-700 rounded-full">Dừng bán</span>
                            @endif
                            @if($combo->original_price && $combo->original_price > $combo->price)
                                <span class="px-3 py-1 text-sm font-medium bg-orange-500 text-white rounded-full">
                                    -{{ round((($combo->original_price - $combo->price) / $combo->original_price) * 100) }}%
                                </span>
                            @endif
                        </div>
                        <div class="absolute bottom-4 left-4 bg-black bg-opacity-50 text-white px-4 py-2 rounded-lg">
                            <div class="flex items-center gap-4">
                                <div>
                                    <span class="text-2xl font-bold">{{ number_format($combo->price, 0, ',', '.') }} VNĐ</span>
                                    @if($combo->original_price && $combo->original_price > $combo->price)
                                        <span class="text-sm line-through ml-2 opacity-75">{{ number_format($combo->original_price, 0, ',', '.') }} VNĐ</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin combo -->
                    <div class="p-6">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold mb-2">{{ $combo->name }}</h2>
                            <p class="text-gray-600 text-lg">{{ $combo->short_description ?: $combo->description ?: 'Không có mô tả' }}</p>
                        </div>

                        <!-- Số lượng combo tại từng chi nhánh -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">Số lượng tại các chi nhánh</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border rounded">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left">Chi nhánh</th>
                                            <th class="px-4 py-2 text-left">Số lượng tồn</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($combo->comboBranchStocks as $stock)
                                            <tr>
                                                <td class="px-4 py-2">{{ $stock->branch->name ?? 'N/A' }}</td>
                                                <td class="px-4 py-2">{{ $stock->quantity }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="px-4 py-2 text-gray-500">Chưa có tồn kho tại chi nhánh nào</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Thông tin chi tiết -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                                    @if($combo->category)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">{{ $combo->category->name }}</span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Chưa phân loại</span>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                                    @if($combo->status === 'selling')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Đang bán</span>
                                    @elseif($combo->status === 'coming_soon')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Sắp bán</span>
                                    @elseif($combo->status === 'discontinued')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-700">Dừng bán</span>
                                    @endif
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày tạo</label>
                                    <span class="text-gray-900">{{ $combo->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng có sẵn</label>
                                    <span class="text-gray-900 font-medium">
                                        @if($combo->quantity !== null)
                                            {{ number_format($combo->quantity) }} combo

                                        @else
                                            <span class="text-gray-500">Không giới hạn</span>
                                        @endif
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tổng sản phẩm</label>
                                    <span class="text-gray-900 font-medium">{{ $combo->productVariants->count() }} loại</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sản phẩm trong combo -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Sản phẩm trong combo</h3>
                            @if($combo->productVariants->count() > 0)
                                 @foreach($combo->productVariants as $productVariant)
                                     <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                         <div class="flex items-center gap-4">
                                             <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                                                 @if($productVariant->product->image)
                                                     <img src="{{ $productVariant->product->image_url }}" alt="{{ $productVariant->product->name }}" class="w-full h-full object-cover">
                                                 @else
                                                     <span class="text-2xl">
                                                         @if(str_contains(strtolower($productVariant->product->name), 'burger'))
                                                             🍔
                                                         @elseif(str_contains(strtolower($productVariant->product->name), 'gà'))
                                                             🍗
                                                         @elseif(str_contains(strtolower($productVariant->product->name), 'pizza'))
                                                             🍕
                                                         @elseif(str_contains(strtolower($productVariant->product->name), 'khoai'))
                                                             🍟
                                                         @elseif(str_contains(strtolower($productVariant->product->name), 'salad'))
                                                             🥗
                                                         @elseif(str_contains(strtolower($productVariant->product->name), 'nước'))
                                                             🥤
                                                         @else
                                                             🍽️
                                                         @endif
                                                     </span>
                                                 @endif
                                             </div>
                                             <div>
                                                 <h4 class="font-semibold">
                                                     <a href="{{ route('admin.products.show', $productVariant->product->id) }}" class="text-blue-600 hover:text-blue-800">
                                                         {{ $productVariant->product->name }}
                                                     </a>
                                                 </h4>
                                                 @if($productVariant->variantValues->count() > 0)
                                                     <p class="text-sm text-gray-600">
                                                         @foreach($productVariant->variantValues as $variantValue)
                                                             {{ $variantValue->attribute->name }}: {{ $variantValue->value }}@if(!$loop->last), @endif
                                                         @endforeach
                                                     </p>
                                                 @endif
                                                 <p class="text-sm text-orange-600 font-medium">{{ number_format($productVariant->price, 0, ',', '.') }} VNĐ</p>
                                             </div>
                                         </div>
                                         <div class="text-right">
                                             <div class="text-lg font-semibold">x{{ $productVariant->pivot->quantity ?? 1 }}</div>
                                             <div class="text-sm text-gray-600">{{ number_format($productVariant->price * ($productVariant->pivot->quantity ?? 1), 0, ',', '.') }} VNĐ</div>
                                         </div>
                                     </div>
                                 @endforeach
                             @else
                                 <div class="text-center py-8">
                                     <i class="fas fa-box-open fa-3x text-gray-400 mb-3"></i>
                                     <p class="text-gray-500 mb-4">Combo này chưa có sản phẩm nào</p>
                                     <a href="{{ route('admin.combos.edit', $combo) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-md">
                                         <i class="fas fa-plus w-4 h-4 mr-2"></i>
                                         Thêm sản phẩm
                                     </a>
                                 </div>
                             @endif
                         </div>
                     </div>
                 </div>
             </div>

             <!-- Sidebar thông tin -->
             <div class="lg:col-span-1">
                 <div class="space-y-6">
                     <!-- Thống kê giá -->
                     <div class="bg-white rounded-lg shadow-sm border p-6">
                         <h3 class="text-lg font-semibold mb-4">Thông tin giá</h3>
                         <div class="space-y-4">
                             <div class="flex justify-between items-center">
                                 <span class="text-gray-600">Giá bán:</span>
                                 <span class="font-semibold text-lg text-orange-600">{{ number_format($combo->price, 0, ',', '.') }} VNĐ</span>
                             </div>
                             <div class="flex justify-between items-center">
                                 <span class="text-gray-600">Giá gốc:</span>
                                 <span class="font-medium text-gray-900">
                                     @if($combo->original_price)
                                         {{ number_format($combo->original_price, 0, ',', '.') }} VNĐ
                                     @else
                                         Không có
                                     @endif
                                 </span>
                             </div>
                             @if($combo->original_price && $combo->original_price > $combo->price)
                                 <div class="flex justify-between items-center pt-2 border-t">
                                     <span class="text-gray-600">Tiết kiệm:</span>
                                     <span class="font-semibold text-green-600">{{ number_format($combo->original_price - $combo->price, 0, ',', '.') }} VNĐ</span>
                                 </div>
                                 <div class="flex justify-between items-center">
                                     <span class="text-gray-600">Giảm giá:</span>
                                     <span class="font-semibold text-orange-600">{{ round((($combo->original_price - $combo->price) / $combo->original_price) * 100) }}%</span>
                                 </div>
                             @endif
                         </div>
                     </div>

                     <!-- Thống kê sản phẩm -->
                     <div class="bg-white rounded-lg shadow-sm border p-6">
                         <h3 class="text-lg font-semibold mb-4">Thống kê sản phẩm</h3>
                         <div class="space-y-3">
                             <div class="flex justify-between items-center">
                                 <span class="text-gray-600">Tổng sản phẩm:</span>
                                 <span class="font-medium">{{ $combo->productVariants->count() }} loại</span>
                             </div>
                             <div class="flex justify-between items-center">
                                 <span class="text-gray-600">Tổng số lượng:</span>
                                 <span class="font-medium">{{ $combo->productVariants->sum('pivot.quantity') }} món</span>
                             </div>
                             @if($combo->productVariants->count() > 0)
                                 <div class="pt-2 border-t">
                                     @php
                                         $categoryBreakdown = [];
                                         foreach($combo->productVariants as $variant) {
                                             $categoryName = $variant->product->category->name ?? 'Khác';
                                             if (!isset($categoryBreakdown[$categoryName])) {
                                                 $categoryBreakdown[$categoryName] = ['count' => 0, 'quantity' => 0];
                                             }
                                             $categoryBreakdown[$categoryName]['count']++;
                                             $categoryBreakdown[$categoryName]['quantity'] += $variant->pivot->quantity ?? 1;
                                         }
                                     @endphp
                                     @foreach($categoryBreakdown as $category => $data)
                                         <div class="flex justify-between items-center py-1">
                                             <span class="text-sm text-gray-600">{{ $category }}:</span>
                                             <span class="text-sm font-medium">{{ $data['count'] }} loại ({{ $data['quantity'] }} món)</span>
                                         </div>
                                     @endforeach
                                 </div>
                             @endif
                         </div>
                     </div>

    <!-- Thao tác -->
                     <div class="bg-white rounded-lg shadow-sm border p-6">
                         <h3 class="text-lg font-semibold mb-4">Thao tác</h3>
                         <div class="space-y-3">
                             <button class="w-full flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-md transition-colors">
                                 <i class="fas fa-copy w-4 h-4 mr-2"></i>
                                 Sao chép combo
                             </button>
                             <button class="w-full flex items-center justify-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-md transition-colors">
                                 <i class="fas fa-file-pdf w-4 h-4 mr-2"></i>
                                 Xuất PDF
                             </button>
                             <button class="w-full flex items-center justify-center px-4 py-2 {{ $combo->is_active ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white font-medium rounded-md transition-colors">
                                 <i class="fas {{ $combo->is_active ? 'fa-pause' : 'fa-play' }} w-4 h-4 mr-2"></i>
                                 {{ $combo->is_active ? 'Tạm ngưng' : 'Kích hoạt' }}
                             </button>
                         </div>
                     </div>

                     <!-- Lịch sử thay đổi -->
                     <div class="bg-white rounded-lg shadow-sm border p-6">
                         <h3 class="text-lg font-semibold mb-4">Lịch sử thay đổi</h3>
                         <div class="space-y-3">
                             <div class="flex items-start space-x-3">
                                 <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                 <div class="flex-1">
                                     <p class="text-sm font-medium">Tạo combo</p>
                                     <p class="text-xs text-gray-500">{{ $combo->created_at->format('d/m/Y H:i') }}</p>
                                 </div>
                             </div>
                             @if($combo->updated_at != $combo->created_at)
                                 <div class="flex items-start space-x-3">
                                     <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                     <div class="flex-1">
                                         <p class="text-sm font-medium">Cập nhật gần nhất</p>
                                         <p class="text-xs text-gray-500">{{ $combo->updated_at->format('d/m/Y H:i') }}</p>
                                     </div>
                                 </div>
                             @endif
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>

 <!-- Delete Modal -->
 <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
     <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
         <div class="mt-3 text-center">
             <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                 <i class="fas fa-exclamation-triangle text-red-600"></i>
             </div>
             <h3 class="text-lg font-medium text-gray-900 mt-4">Xác nhận xóa combo</h3>
             <div class="mt-2 px-7 py-3">
                 <p class="text-sm text-gray-500">
                     Bạn có chắc chắn muốn xóa combo "{{ $combo->name }}" không? Hành động này không thể hoàn tác.
                 </p>
             </div>
             <div class="flex justify-center space-x-3 mt-4">
                 <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                     Hủy
                 </button>
                 <form action="{{ route('admin.combos.destroy', $combo) }}" method="POST" style="display: inline;">
                     @csrf
                     @method('DELETE')
                     <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                         Xóa
                     </button>
                 </form>
             </div>
         </div>
     </div>
 </div>

 <script>
 function openDeleteModal() {
     document.getElementById('deleteModal').classList.remove('hidden');
 }

 function closeDeleteModal() {
     document.getElementById('deleteModal').classList.add('hidden');
 }

 // Cập nhật nút xóa để sử dụng modal mới
 document.addEventListener('DOMContentLoaded', function() {
     const deleteButton = document.querySelector('[onclick="openDeleteModal()"]');
     if (deleteButton) {
         deleteButton.addEventListener('click', openDeleteModal);
     }
 });
 </script>
@endsection

@push('styles')
<style>
.form-control-static {
    padding-top: 7px;
    padding-bottom: 7px;
    margin-bottom: 0;
    min-height: 34px;
}

.no-image-placeholder {
    padding: 50px;
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
}

.no-image-small {
    padding: 20px;
    background-color: #f8f9fa;
    border: 1px dashed #dee2e6;
    border-radius: 4px;
    text-align: center;
}

.product-item {
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.product-item:hover {
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0,123,255,0.25);
}

.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}

.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}

.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 13px;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}
</style>
@endpush

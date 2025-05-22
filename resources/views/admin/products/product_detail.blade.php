@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chi tiết sản phẩm')
@section('description', 'Xem thông tin chi tiết sản phẩm')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
  <!-- Page header -->
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Chi tiết sản phẩm</h1>
      <p class="text-gray-500 dark:text-gray-400">Xem thông tin chi tiết và quản lý sản phẩm</p>
    </div>
    <div class="flex items-center space-x-3">
      <a href="{{ route('admin.products.edit', $product->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
          <path d="m15 5 4 4"></path>
        </svg>
        Chỉnh sửa
      </a>
      <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m12 19-7-7 7-7"></path>
          <path d="M19 12H5"></path>
        </svg>
        Quay lại
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Thông tin cơ bản -->
    <div class="md:col-span-2 space-y-6">
      <!-- Thông tin chính -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="flex justify-between items-center p-5 border-b border-gray-200 dark:border-gray-700">
          <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Thông tin sản phẩm</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Thông tin chi tiết về sản phẩm</p>
          </div>
          <div class="flex items-center">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->stock > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
              {{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}
            </span>
          </div>
        </div>
        <div class="p-5">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tên sản phẩm</h3>
              <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Mã sản phẩm</h3>
              <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $product->sku ?? 'N/A' }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Danh mục</h3>
              <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $product->category->name ?? 'N/A' }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Giá bán</h3>
              <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ number_format($product->price, 0, ',', '.') }} đ</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tồn kho</h3>
              <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $product->stock }} sản phẩm</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Thời gian chuẩn bị</h3>
              <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $product->preparation_time ?? 'N/A' }} phút</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Trạng thái</h3>
              <p class="text-base font-medium text-gray-900 dark:text-gray-100">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->status == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                  {{ $product->status == 'active' ? 'Đang bán' : 'Ngừng bán' }}
                </span>
              </p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Sản phẩm nổi bật</h3>
              <p class="text-base font-medium text-gray-900 dark:text-gray-100">
                <span class="inline-flex items-center">
                  @if($product->featured)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 24 24" fill="currentColor">
                      <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                    </svg>
                  @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                  @endif
                  <span class="ml-1">{{ $product->featured ? 'Có' : 'Không' }}</span>
                </span>
              </p>
            </div>
          </div>
          
          <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Mô tả sản phẩm</h3>
            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
              {!! $product->description ?? 'Không có mô tả' !!}
            </div>
          </div>
        </div>
      </div>

      <!-- Biến thể sản phẩm -->
      @if(isset($product->variants) && count($product->variants) > 0)
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="flex justify-between items-center p-5 border-b border-gray-200 dark:border-gray-700">
          <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Biến thể sản phẩm</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Danh sách các biến thể của sản phẩm</p>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">SKU</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Thuộc tính</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Giá</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tồn kho</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Trạng thái</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              @foreach($product->variants as $variant)
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $variant->sku }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $variant->attributes }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ number_format($variant->price, 0, ',', '.') }} đ</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $variant->stock }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $variant->status == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                    {{ $variant->status == 'active' ? 'Đang bán' : 'Ngừng bán' }}
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
      <!-- Hình ảnh sản phẩm -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Hình ảnh sản phẩm</h2>
        </div>
        <div class="p-5">
          @if(isset($product->images) && count($product->images) > 0)
            <div class="grid grid-cols-2 gap-2">
              @foreach($product->images as $image)
                <div class="aspect-square rounded-md overflow-hidden">
                  <img src="{{ asset($image->path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
              @endforeach
            </div>
          @else
            <div class="aspect-square rounded-md overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
          @endif
        </div>
      </div>

      <!-- Thông tin bổ sung -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Thông tin bổ sung</h2>
        </div>
        <div class="p-5 space-y-4">
          <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Ngày tạo</h3>
            <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $product->created_at->format('d/m/Y H:i') }}</p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Cập nhật lần cuối</h3>
            <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $product->updated_at->format('d/m/Y H:i') }}</p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Người tạo</h3>
            <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $product->creator->name ?? 'N/A' }}</p>
          </div>
        </div>
      </div>

      <!-- Hành động -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Hành động</h2>
        </div>
        <div class="p-5 space-y-3">
          <a href="{{ route('admin.products.edit', $product->id) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
              <path d="m15 5 4 4"></path>
            </svg>
            Chỉnh sửa sản phẩm
          </a>
          
          <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Xuất thông tin
          </button>
          
          <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="mt-3">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 dark:border-red-700 shadow-sm text-sm font-medium rounded-md text-red-700 dark:text-red-400 bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18"></path>
                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
              </svg>
              Xóa sản phẩm
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

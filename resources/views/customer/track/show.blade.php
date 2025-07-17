@extends('layouts.customer.fullLayoutMaster')

@section('content')
<div class="container mx-auto my-10 p-5">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md">
        <div class="p-5 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">Theo Dõi Đơn Hàng</h1>
            <p class="text-gray-600">Cập nhật thông tin chi tiết về hành trình đơn hàng của bạn.</p>
        </div>

        @if (isset($error))
            <div class="p-5">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Lỗi!</strong>
                    <span class="block sm:inline">{{ $error }}</span>
                </div>
                <div class="mt-4 text-center">
                     <a href="{{ route('home') }}" class="text-blue-500 hover:underline">Quay lại trang chủ</a>
                </div>
            </div>
        @else
            <div class="p-5">
                {{-- Order Summary --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-700">Thông tin đơn hàng</h3>
                        <div class="mt-2 space-y-2 text-gray-600">
                           <p><strong>Mã đơn:</strong> {{ $order->order_code }}</p>
                           <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('H:i d/m/Y') }}</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg text-gray-700">Thông tin giao hàng</h3>
                        <div class="mt-2 space-y-2 text-gray-600">
                            <p><strong>Người nhận:</strong> {{ $order->customer_name_short }}</p>
                            <p><strong>Địa chỉ:</strong> {{ $order->delivery_address_short }}</p>
                        </div>
                    </div>
                </div>

                {{-- Status Timeline --}}
                <div>
                    <h3 class="font-semibold text-lg text-gray-700 mb-4">Trạng thái đơn hàng</h3>
                     <div class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex-shrink-0">
                             <svg class="h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-xl font-bold text-gray-800">{{ $currentStatus }}</h4>
                            <p class="text-sm text-gray-500">Cập nhật lúc: {{ $lastUpdateTime->format('H:i d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                 <div class="mt-8 text-center">
                    <a href="{{ route('home') }}" class="text-blue-500 hover:underline">Quay lại trang chủ</a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    /* You can add custom styles here if needed */
</style>
@endpush 
@extends('layouts.driver.masterLayout')

@section('title', 'Thu nhập')
@section('page-title', 'Thu nhập')

@section('content')
<div class="pt-4 p-4 space-y-4">
    <!-- Period Selector -->
    <div class="flex space-x-2 mb-4">
        <button class="px-4 py-2 bg-orange-100 text-orange-600 rounded-full text-sm font-medium">Hôm nay</button>
        <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-full text-sm">Tuần này</button>
        <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-full text-sm">Tháng này</button>
    </div>

    <!-- Total Earnings -->
    <div class="bg-white rounded-lg p-6 shadow-sm text-center">
        <div class="text-3xl font-bold text-green-600 mb-2">250.000 đ</div>
        <div class="text-gray-500">Tổng thu nhập hôm nay</div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-list text-blue-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold">5</div>
                    <div class="text-sm text-gray-500">Đơn hàng</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold">50.000 đ</div>
                    <div class="text-sm text-gray-500">Tips</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-purple-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold">8h</div>
                    <div class="text-sm text-gray-500">Giờ làm</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-orange-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold">31.250 đ</div>
                    <div class="text-sm text-gray-500">Theo giờ</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <h3 class="font-semibold mb-3">Tóm tắt</h3>
        
        <div class="space-y-3">
            <div class="flex justify-between">
                <span>Trung bình mỗi đơn:</span>
                <span class="font-medium">50.000 đ</span>
            </div>
            <div class="flex justify-between">
                <span>Hiệu suất:</span>
                <span class="font-medium text-green-600">Tốt</span>
            </div>
            <div class="flex justify-between">
                <span>Xếp hạng:</span>
                <span class="font-medium text-blue-600">Top 20%</span>
            </div>
        </div>
    </div>
</div>
@endsection

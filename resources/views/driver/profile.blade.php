@extends('layouts.driver.masterLayout')

@section('title', 'Hồ sơ')
@section('page-title', 'Hồ sơ')

@section('content')
<div class="pt-4 p-4 space-y-4">
    <!-- User Info -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-gray-600 text-xl"></i>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-semibold">Nguyễn Văn A</h2>
                <p class="text-gray-500">0987654321</p>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Online</span>
                    <span class="text-xs text-gray-400">ID: TH2345</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-lg p-4 shadow-sm text-center">
            <div class="text-2xl font-bold text-blue-600">150</div>
            <div class="text-sm text-gray-500">Đơn giao</div>
        </div>
        <div class="bg-white rounded-lg p-4 shadow-sm text-center">
            <div class="text-2xl font-bold text-green-600">98%</div>
            <div class="text-sm text-gray-500">Thành công</div>
        </div>
        <div class="bg-white rounded-lg p-4 shadow-sm text-center">
            <div class="text-2xl font-bold text-orange-600">6</div>
            <div class="text-sm text-gray-500">Tháng</div>
        </div>
    </div>

    <!-- Menu Options -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <a href="#" class="flex items-center justify-between p-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <i class="fas fa-edit text-gray-400"></i>
                <div>
                    <div class="font-medium">Chỉnh sửa hồ sơ</div>
                    <div class="text-sm text-gray-500">Cập nhật thông tin cá nhân</div>
                </div>
            </div>
            <i class="fas fa-chevron-right text-gray-400"></i>
        </a>
        
        <a href="#" class="flex items-center justify-between p-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <i class="fas fa-calendar text-gray-400"></i>
                <div>
                    <div class="font-medium">Lịch làm việc</div>
                    <div class="text-sm text-gray-500">Xem và đăng ký ca làm</div>
                </div>
            </div>
            <i class="fas fa-chevron-right text-gray-400"></i>
        </a>
        
        <a href="#" class="flex items-center justify-between p-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <i class="fas fa-star text-gray-400"></i>
                <div>
                    <div class="font-medium">Thành tích</div>
                    <div class="text-sm text-gray-500">Xem huy hiệu và thành tích</div>
                </div>
            </div>
            <i class="fas fa-chevron-right text-gray-400"></i>
        </a>
        
        <a href="#" class="flex items-center justify-between p-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <i class="fas fa-cog text-gray-400"></i>
                <div>
                    <div class="font-medium">Cài đặt</div>
                    <div class="text-sm text-gray-500">Thông báo, bảo mật</div>
                </div>
            </div>
            <i class="fas fa-chevron-right text-gray-400"></i>
        </a>
        
        <a href="#" class="flex items-center justify-between p-4 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <i class="fas fa-question-circle text-gray-400"></i>
                <div>
                    <div class="font-medium">Hỗ trợ</div>
                    <div class="text-sm text-gray-500">Liên hệ hỗ trợ khách hàng</div>
                </div>
            </div>
            <i class="fas fa-chevron-right text-gray-400"></i>
        </a>
        
        <a href="#" class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-3">
                <i class="fas fa-shield-alt text-gray-400"></i>
                <div>
                    <div class="font-medium">Chính sách</div>
                    <div class="text-sm text-gray-500">Điều khoản và bảo mật</div>
                </div>
            </div>
            <i class="fas fa-chevron-right text-gray-400"></i>
        </a>
    </div>

    <!-- App Info -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <h3 class="font-semibold mb-3">Thông tin ứng dụng</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span>Phiên bản:</span>
                <span>1.0.0</span>
            </div>
            <div class="flex justify-between">
                <span>Cập nhật cuối:</span>
                <span>15/01/2024</span>
            </div>
        </div>
    </div>

    <!-- Logout Button -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <form action="{{ route('driver.logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" class="w-full text-red-600 font-medium py-2 hover:bg-red-50 rounded transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i>
                Đăng xuất
            </button>
        </form>
    </div>
</div>
@endsection

@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - ' . $user->full_name)

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
   }
</style>
<div class="bg-gradient-to-r from-orange-500 to-red-500 py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center">
            <div class="relative mb-6 md:mb-0 md:mr-8">
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-white p-1 shadow-lg">
                    {{-- DYNAMIC AVATAR --}}
                    <img src="{{ $user->avatar ? Storage::disk('s3')->url($user->avatar) : asset('images/default-avatar.png') }}" alt="Ảnh đại diện" class="w-full h-full rounded-full object-cover">
                </div>
                <a href="{{ route('customer.profile.edit') }}" class="absolute bottom-0 right-0 bg-orange-600 hover:bg-orange-700 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-md transition-colors">
                    <i class="fas fa-camera"></i>
                </a>
            </div>
            <div class="text-center md:text-left text-white">
                {{-- DYNAMIC USER INFO --}}
                <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $user->full_name }}</h1>
                <p class="text-white/80 mb-4">Thành viên từ {{ $user->created_at->isoFormat('MMMM, YYYY') }}</p>
                <div class="flex flex-wrap justify-center md:justify-start gap-3">
                    @if($currentRank)
                    <div class="bg-white/20 backdrop-blur-sm px-4 py-1 rounded-full flex items-center">
                        <i class="fas fa-star mr-2" style="color: {{ $currentRank->color ?? '#FFD700' }};"></i>
                        <span>Thành viên {{ $currentRank->name }}</span>
                    </div>
                    @endif
                    <div class="bg-white/20 backdrop-blur-sm px-4 py-1 rounded-full flex items-center">
                        <i class="fas fa-medal text-yellow-300 mr-2"></i>
                        <span>{{ number_format($currentPoints, 0, ',', '.') }} điểm</span>
                    </div>
                </div>
            </div>
            <div class="mt-6 md:mt-0 md:ml-auto">
                <a href="{{ route('customer.profile.edit') }}" class="bg-white text-orange-500 hover:bg-orange-50 px-6 py-2 rounded-lg shadow-md transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Chỉnh sửa hồ sơ
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <div class="lg:w-1/4">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-24">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="font-bold text-lg">Tài khoản của tôi</h2>
                </div>
                <nav class="p-2">
                    <ul class="space-y-1">
                        <li><a href="#overview" class="flex items-center px-4 py-3 rounded-lg bg-orange-50 text-orange-500 font-medium"><i class="fas fa-home mr-3 w-5 text-center"></i>Tổng quan</a></li>
                        <li><a href="#orders" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-shopping-bag mr-3 w-5 text-center"></i>Đơn hàng của tôi</a></li>
                        <li><a href="#addresses" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-map-marker-alt mr-3 w-5 text-center"></i>Địa chỉ đã lưu</a></li>
                        <li><a href="#favorites" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-heart mr-3 w-5 text-center"></i>Món ăn yêu thích</a></li>
                        <li><a href="#rewards" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-gift mr-3 w-5 text-center"></i>Điểm thưởng & Ưu đãi</a></li>
                        <li><a href="{{ route('customer.profile.setting') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i class="fas fa-cog mr-3 w-5 text-center"></i>Cài đặt tài khoản</a></li>
                        <li class="border-t border-gray-100 mt-2 pt-2">
                            <form method="POST" action="{{ route('customer.logout') }}" id="logout-form">
                                @csrf
                                <a href="{{ route('customer.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center px-4 py-3 rounded-lg text-red-500 hover:bg-red-50 transition-colors">
                                    <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>Đăng xuất
                                </a>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="lg:w-3/4">
            <section id="overview" class="mb-10">
                <h2 class="text-2xl font-bold mb-6">Tổng Quan</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    {{-- DYNAMIC STATS --}}
                    <div class="bg-white rounded-xl shadow-sm p-6 text-center"><div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-shopping-bag text-blue-500"></i></div><h3 class="text-3xl font-bold mb-1">{{ $user->total_orders }}</h3><p class="text-gray-500 text-sm">Đơn hàng</p></div>
                    <div class="bg-white rounded-xl shadow-sm p-6 text-center"><div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-medal text-green-500"></i></div><h3 class="text-3xl font-bold mb-1">{{ number_format($currentPoints, 0, ',', '.') }}</h3><p class="text-gray-500 text-sm">Điểm thưởng</p></div>
                    <div class="bg-white rounded-xl shadow-sm p-6 text-center"><div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-ticket-alt text-purple-500"></i></div><h3 class="text-3xl font-bold mb-1">{{ $vouchers->count() }}</h3><p class="text-gray-500 text-sm">Voucher</p></div>
                    <div class="bg-white rounded-xl shadow-sm p-6 text-center"><div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-heart text-red-500"></i></div><h3 class="text-3xl font-bold mb-1">{{ $user->favorites->count() }}</h3><p class="text-gray-500 text-sm">Yêu thích</p></div>
                </div>
                
                @if($currentRank)
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    {{-- DYNAMIC RANK PROGRESS --}}
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                        <div><h3 class="text-lg font-bold mb-1">Thành viên {{ $currentRank->name }}</h3>
                            @if($nextRank)<p class="text-gray-500 text-sm">Còn {{ number_format(max(0, $nextRank->min_spending - $currentPoints), 0, ',', '.') }} điểm nữa để lên hạng {{ $nextRank->name }}</p>
                            @else<p class="text-gray-500 text-sm">Bạn đã đạt hạng cao nhất!</p>@endif
                        </div>
                        <div class="mt-2 md:mt-0">
                            @if($nextRank)<span class="text-sm font-medium">{{ number_format($currentPoints, 0, ',', '.') }}/{{ number_format($nextRank->min_spending, 0, ',', '.') }} điểm</span>
                            @else<span class="text-sm font-medium">{{ number_format($currentPoints, 0, ',', '.') }} điểm</span>@endif
                        </div>
                    </div>
                    <div class="relative h-4 bg-gray-100 rounded-full overflow-hidden mb-2"><div class="absolute top-0 left-0 h-full bg-gradient-to-r from-yellow-400 to-yellow-500" style="width: {{ $progressPercent }}%"></div></div>
                    <div class="flex justify-between text-xs text-gray-500">
                        @foreach($allRanks as $rank)
                        <div class="flex flex-col items-center"><div class="w-4 h-4 rounded-full mb-1 flex items-center justify-center" style="background-color: {{ $rank->id === $currentRank->id ? ($rank->color ?? '#CCCCCC').'40' : '#E5E7EB' }};"><div class="w-2 h-2 rounded-full" style="background-color: {{ $rank->color ?? '#9CA3AF' }};"></div></div><span>{{ $rank->name }}</span><span>{{ number_format($rank->min_spending, 0, ',', '.') }}</span></div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div id="orders" class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6"><h3 class="text-lg font-bold">Đơn Hàng Gần Đây</h3><a href="#" class="text-orange-500 hover:underline text-sm font-medium">Xem tất cả</a></div>
                    <div class="space-y-4">
                        {{-- DYNAMIC RECENT ORDERS --}}
                        @forelse($recentOrders as $order)
                        <div class="border border-gray-100 rounded-lg p-4 hover:shadow-sm transition-shadow">
                            <div class="flex justify-between items-start mb-3">
                                <div><span class="text-sm text-gray-500">Mã đơn hàng: #{{ $order->order_code ?? $order->id }}</span><h4 class="font-medium">{{ $order->items->pluck('product.name')->implode(', ') }}</h4></div>
                                <span class="text-xs font-medium px-2 py-1 rounded-full capitalize" style="background-color: {{ $order->status_color['bg'] ?? '#f3f4f6' }}; color: {{ $order->status_color['text'] ?? '#374151' }};">{{ $order->status_text }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="text-gray-500 text-sm"><i class="far fa-calendar-alt mr-1"></i> {{ $order->created_at->format('d/m/Y') }}</div>
                                <div class="font-medium">{{ number_format($order->total_amount, 0, ',', '.') }}đ</div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">Bạn chưa có đơn hàng nào gần đây.</p>
                        @endforelse
                    </div>
                </div>
            </section>
            
            <section id="personal-info" class="mb-10">
                {{-- DYNAMIC PERSONAL INFO --}}
                <h2 class="text-2xl font-bold mb-6">Thông Tin Cá Nhân</h2>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6"><h3 class="text-lg font-bold">Thông tin chi tiết</h3><a href="{{ route('customer.profile.edit') }}" class="text-orange-500 hover:underline text-sm font-medium flex items-center"><i class="fas fa-edit mr-1"></i> Chỉnh sửa</a></div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div><h4 class="text-sm text-gray-500 mb-1">Họ và tên</h4><p class="font-medium">{{ $user->full_name }}</p></div>
                        <div><h4 class="text-sm text-gray-500 mb-1">Email</h4><p class="font-medium">{{ $user->email }}</p></div>
                        <div><h4 class="text-sm text-gray-500 mb-1">Số điện thoại</h4><p class="font-medium">{{ $user->phone }}</p></div>
                        <div><h4 class="text-sm text-gray-500 mb-1">Ngày sinh</h4><p class="font-medium">{{ $user->birthday ? $user->birthday->format('d/m/Y') : 'Chưa cập nhật' }}</p></div>
                        <div><h4 class="text-sm text-gray-500 mb-1">Giới tính</h4><p class="font-medium">{{ $user->gender ? ucfirst($user->gender) : 'Chưa cập nhật' }}</p></div>
                    </div>
                </div>
            </section>
            
            <section id="addresses" class="mb-10">
                <div class="flex justify-between items-center mb-6"><h2 class="text-2xl font-bold">Địa Chỉ Đã Lưu</h2><button class="bg-orange-500 hover:bg-orange-600 text-white text-sm px-4 py-2 rounded-lg transition-colors flex items-center"><i class="fas fa-plus mr-2"></i> Thêm địa chỉ mới</button></div>
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- DYNAMIC ADDRESSES --}}
                    @forelse($user->addresses as $address)
                    <div class="bg-white rounded-xl shadow-sm p-6 @if($address->is_default) border-l-4 border-orange-500 @endif">
                        <div class="flex justify-between items-start mb-4">
                            <div><h3 class="font-bold mb-1">{{ $address->type === 'home' ? 'Nhà riêng' : ($address->type === 'office' ? 'Văn phòng' : 'Khác') }}</h3>@if($address->is_default)<p class="text-gray-500 text-sm">Mặc định</p>@endif</div>
                            <div class="flex space-x-2"><button class="text-gray-500 hover:text-orange-500 transition-colors"><i class="fas fa-edit"></i></button><button class="text-gray-500 hover:text-red-500 transition-colors"><i class="fas fa-trash-alt"></i></button></div>
                        </div>
                        <div class="space-y-2"><p class="font-medium">{{ $address->recipient_name ?? $user->full_name }}</p><p class="text-gray-600">{{ $address->recipient_phone ?? $user->phone }}</p><p class="text-gray-600">{{ $address->full_address }}</p></div>
                    </div>
                    @empty
                    <p class="md:col-span-2 text-gray-500 text-center py-4">Bạn chưa có địa chỉ nào được lưu.</p>
                    @endforelse
                </div>
            </section>
            
            <section id="favorites" class="mb-10">
                <h2 class="text-2xl font-bold mb-6">Món Ăn Yêu Thích</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- DYNAMIC FAVORITES --}}
                    @forelse($favoriteProducts as $favorite)
                    @if($favorite->product) {{-- Ensure product exists --}}
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden group">
                        <a href="{{ route('products.show', $favorite->product->id) }}" class="block relative h-48"><img src="{{ $favorite->product->primaryImage ? Storage::disk('s3')->url($favorite->product->primaryImage->img) : asset('images/default-product.png') }}" alt="{{ $favorite->product->name }}" class="w-full h-full object-cover"></a>
                        <div class="p-4">
                            <h3 class="font-bold mb-1"><a href="{{ route('products.show', $favorite->product->id) }}" class="hover:text-orange-500">{{ $favorite->product->name }}</a></h3>
                            <p class="text-gray-500 text-sm mb-2 h-10">{{ Str::limit($favorite->product->short_description, 60) }}</p>
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-orange-500">{{ number_format($favorite->product->base_price, 0, ',', '.') }}đ</span>
                                <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-lg text-sm transition-colors">Thêm vào giỏ</button>
                            </div>
                        </div>
                    </div>
                    @endif
                    @empty
                    <p class="md:col-span-2 lg:col-span-3 text-gray-500 text-center py-4">Bạn chưa yêu thích món ăn nào.</p>
                    @endforelse
                </div>
            </section>
            
            <section id="rewards" class="mb-10">
                <h2 class="text-2xl font-bold mb-6">Điểm Thưởng & Ưu Đãi</h2>
                {{-- DYNAMIC REWARDS --}}
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                        <div><h3 class="text-lg font-bold mb-1">Điểm thưởng của bạn</h3><p class="text-gray-500">Sử dụng điểm để đổi lấy ưu đãi hấp dẫn</p></div>
                        <div class="mt-4 md:mt-0"><div class="flex items-center bg-orange-50 px-4 py-2 rounded-lg"><i class="fas fa-medal text-orange-500 mr-2"></i><span class="text-2xl font-bold text-orange-500">{{ number_format($currentPoints, 0, ',', '.') }}</span><span class="text-gray-500 ml-2">điểm</span></div></div>
                    </div>
                    <div class="border-t border-gray-100 pt-6">
                        <h4 class="font-bold mb-4">Lịch sử điểm thưởng</h4>
                        <div class="space-y-4">
                            @forelse($pointHistory as $history)
                            <div class="flex justify-between items-center">
                                <div><p class="font-medium">{{ $history->reason }}</p><p class="text-sm text-gray-500">{{ $history->created_at->format('d/m/Y') }}</p></div>
                                @if($history->points > 0)
                                <span class="text-green-500 font-medium">+{{ $history->points }} điểm</span>
                                @else
                                <span class="text-red-500 font-medium">{{ $history->points }} điểm</span>
                                @endif
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-2">Chưa có lịch sử điểm thưởng.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold mb-6">Voucher của bạn</h3>
                    <div class="space-y-4">
                        @forelse($vouchers as $voucher)
                        <div class="border border-dashed border-orange-200 rounded-lg p-4 bg-orange-50">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <h4 class="font-bold text-lg mb-1">{{ $voucher->name }}</h4>
                                    <p class="text-gray-600 text-sm">{{ $voucher->description }}</p>
                                    @if($voucher->end_date)<p class="text-gray-500 text-xs mt-2">Hết hạn: {{ $voucher->end_date->format('d/m/Y') }}</p>@endif
                                </div>
                                <div class="flex flex-col items-center"><span class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full mb-2">{{ $voucher->code }}</span><button class="text-orange-500 border border-orange-500 hover:bg-orange-50 px-4 py-1 rounded-lg text-sm transition-colors">Sử dụng ngay</button></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">Bạn không có voucher nào khả dụng.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit profile button
    const editProfileButtons = document.querySelectorAll('button:contains("Chỉnh sửa hồ sơ")');
    const editProfileModal = document.getElementById('edit-profile-modal');
    const closeModalButton = document.getElementById('close-modal');
    const editProfileForm = document.getElementById('edit-profile-form');
    
    if (editProfileButtons.length > 0) {
        editProfileButtons.forEach(button => {
            button.addEventListener('click', function() {
                editProfileModal.classList.remove('hidden');
            });
        });
    }
    
    // Close modal
    if (closeModalButton) {
        closeModalButton.addEventListener('click', function() {
            editProfileModal.classList.add('hidden');
        });
    }
    
    // Close modal when clicking outside
    if (editProfileModal) {
        editProfileModal.addEventListener('click', function(e) {
            if (e.target === editProfileModal) {
                editProfileModal.classList.add('hidden');
            }
        });
    }
    
    // Form submission
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would normally send the data to the server
            // For now, we'll just close the modal
            editProfileModal.classList.add('hidden');
            
            // Show success message
            showToast('Thông tin hồ sơ đã được cập nhật');
        });
    }
    
    // Simple toast notification function
    function showToast(message) {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0';
        toast.textContent = message;
        
        // Add to DOM
        document.body.appendChild(toast);
        
        // Show toast
        setTimeout(() => {
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
        }, 10);
        
        // Hide and remove toast after 3 seconds
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
            
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
    
    // Fix for :contains selector which is not standard
    if (!Element.prototype.matches) {
        Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
    }
    
    if (!NodeList.prototype.forEach) {
        NodeList.prototype.forEach = Array.prototype.forEach;
    }
    
    // Helper function to find elements containing text
    function getElementsContainingText(selector, text) {
        const elements = document.querySelectorAll(selector);
        return Array.from(elements).filter(element => element.textContent.includes(text));
    }
    
    // Use this instead of the :contains selector
    const editButtons = getElementsContainingText('button', 'Chỉnh sửa hồ sơ');
    if (editButtons.length > 0) {
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (editProfileModal) {
                    editProfileModal.classList.remove('hidden');
                }
            });
        });
    }
});
</script>
@endsection
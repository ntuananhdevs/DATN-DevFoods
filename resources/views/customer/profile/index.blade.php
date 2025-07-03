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
                        <img src="{{ $user->avatar_url ?? asset('images/default-avatar.png') }}" alt="Ảnh đại diện"
                            class="w-full h-full rounded-full object-cover">
                    </div>
                    <a href="{{ route('customer.profile.edit') }}"
                        class="absolute bottom-0 right-0 bg-orange-600 hover:bg-orange-700 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-md transition-colors">
                        <i class="fas fa-camera"></i>
                    </a>
                </div>
                <div class="text-center md:text-left text-white">
                    {{-- DYNAMIC USER INFO --}}
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $user->full_name }}</h1>
                    <p class="text-white/80 mb-4">Thành viên từ {{ $user->created_at->isoFormat('MMMM, YYYY') }}</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-3">
                        @if ($currentRank)
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
                    <a href="{{ route('customer.profile.edit') }}"
                        class="bg-white text-orange-500 hover:bg-orange-50 px-6 py-2 rounded-lg shadow-md transition-colors">
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
                            <li><a href="#overview"
                                    class="flex items-center px-4 py-3 rounded-lg bg-orange-50 text-orange-500 font-medium"><i
                                        class="fas fa-home mr-3 w-5 text-center"></i>Tổng quan</a></li>
                            <li><a href="{{ route('customer.orders.index') }}"
                                    class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                                        class="fas fa-shopping-bag mr-3 w-5 text-center"></i>Đơn hàng của tôi</a></li>
                            <li><a href="#addresses"
                                    class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                                        class="fas fa-map-marker-alt mr-3 w-5 text-center"></i>Địa chỉ đã lưu</a></li>
                            <li><a href="#favorites"
                                    class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                                        class="fas fa-heart mr-3 w-5 text-center"></i>Món ăn yêu thích</a></li>
                            <li><a href="#rewards"
                                    class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                                        class="fas fa-gift mr-3 w-5 text-center"></i>Điểm thưởng & Ưu đãi</a></li>
                            <li><a href="{{ route('customer.profile.setting') }}"
                                    class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                                        class="fas fa-cog mr-3 w-5 text-center"></i>Cài đặt tài khoản</a></li>
                            <li class="border-t border-gray-100 mt-2 pt-2">
                                <form method="POST" action="{{ route('customer.logout') }}" id="logout-form">
                                    @csrf
                                    <a href="{{ route('customer.logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                        class="flex items-center px-4 py-3 rounded-lg text-red-500 hover:bg-red-50 transition-colors">
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
                        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3"><i
                                    class="fas fa-shopping-bag text-blue-500"></i></div>
                            <h3 class="text-3xl font-bold mb-1">{{ $user->total_orders }}</h3>
                            <p class="text-gray-500 text-sm">Đơn hàng</p>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-medal text-green-500"></i></div>
                            <h3 class="text-3xl font-bold mb-1">{{ number_format($currentPoints, 0, ',', '.') }}</h3>
                            <p class="text-gray-500 text-sm">Điểm thưởng</p>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-ticket-alt text-purple-500"></i></div>
                            <h3 class="text-3xl font-bold mb-1">{{ $vouchers->count() }}</h3>
                            <p class="text-gray-500 text-sm">Voucher</p>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3"><i
                                    class="fas fa-heart text-red-500"></i></div>
                            <h3 class="text-3xl font-bold mb-1">{{ $user->favorites->count() }}</h3>
                            <p class="text-gray-500 text-sm">Yêu thích</p>
                        </div>
                    </div>

                    @if ($currentRank)
                        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                            {{-- DYNAMIC RANK PROGRESS --}}
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-bold mb-1">Thành viên {{ $currentRank->name }}</h3>
                                    @if ($nextRank)
                                        <p class="text-gray-500 text-sm">Còn
                                            {{ number_format(max(0, $nextRank->min_spending - $currentPoints), 0, ',', '.') }}
                                            điểm nữa để lên hạng {{ $nextRank->name }}</p>
                                    @else<p class="text-gray-500 text-sm">Bạn đã đạt hạng cao nhất!</p>
                                    @endif
                                </div>
                                <div class="mt-2 md:mt-0">
                                    @if ($nextRank)
                                        <span
                                            class="text-sm font-medium">{{ number_format($currentPoints, 0, ',', '.') }}/{{ number_format($nextRank->min_spending, 0, ',', '.') }}
                                            điểm</span>
                                    @else<span
                                            class="text-sm font-medium">{{ number_format($currentPoints, 0, ',', '.') }}
                                            điểm</span>
                                    @endif
                                </div>
                            </div>
                            <div class="relative h-4 bg-gray-100 rounded-full overflow-hidden mb-2">
                                <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-yellow-400 to-yellow-500"
                                    style="width: {{ $progressPercent }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                @foreach ($allRanks as $rank)
                                    <div class="flex flex-col items-center">
                                        <div class="w-4 h-4 rounded-full mb-1 flex items-center justify-center"
                                            style="background-color: {{ $rank->id === $currentRank->id ? ($rank->color ?? '#CCCCCC') . '40' : '#E5E7EB' }};">
                                            <div class="w-2 h-2 rounded-full"
                                                style="background-color: {{ $rank->color ?? '#9CA3AF' }};"></div>
                                        </div>
                                        <span>{{ $rank->name }}</span><span>{{ number_format($rank->min_spending, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <section id="orders" class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold">Đơn Hàng Gần Đây</h3>
                            <a href="{{ route('customer.orders.index') }}"
                                class="text-orange-500 hover:underline text-sm font-medium">Xem tất cả</a>
                        </div>

                        <div class="space-y-4">
                            @forelse($recentOrders as $order)
                                <div class="border rounded-lg p-4 transition-shadow hover:shadow-md">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-semibold text-gray-800">
                                                #{{ $order->order_code ?? $order->id }}</h4>
                                            <p class="text-sm text-gray-500">{{ $order->order_date->diffForHumans() }}</p>
                                        </div>
                                        <span class="text-xs font-medium px-2.5 py-1 rounded-full"
                                            style="background-color: {{ $order->status_color['bg'] }}; color: {{ $order->status_color['text'] }};">
                                            {{ $order->status_text }}
                                        </span>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-sm text-gray-500 mb-1 font-medium">
                                            {{ optional($order->branch)->name ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-700">
                                            {{-- Sử dụng optional() để đảm bảo an toàn --}}
                                            {{ $order->orderItems->map(fn($item) => (optional(optional($item->productVariant)->product)->name ?? 'Sản phẩm') . ' x' . $item->quantity)->implode(', ') }}
                                        </p>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span
                                            class="font-semibold text-lg text-orange-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('customer.orders.show', $order) }}"
                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200">Chi
                                                tiết</a>

                                            {{-- ====== NEW BUTTON LOGIC ====== --}}
                                            @if ($order->status == 'awaiting_confirmation')
                                                <form action="{{ route('customer.orders.updateStatus', $order) }}"
                                                    method="POST" class="cancel-order-form">
                                                    @csrf
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-md text-sm font-medium h-9 px-4 py-2 border border-red-500 text-red-600 hover:bg-red-50">Hủy
                                                        đơn</button>
                                                </form>

                                                {{-- Case 2: Order has been delivered by the driver --}}
                                            @elseif($order->status == 'delivered')
                                                <a href="{{ route('customer.orders.updateStatus', $order) }}"
                                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200">Chưa
                                                    nhận được hàng</a>
                                                <form class="receive-order-form"
                                                    action="{{ route('customer.orders.updateStatus', $order) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="item_received">
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white h-10 px-4 py-2 bg-orange-500 hover:bg-orange-600">Xác
                                                        nhận đã nhận hàng</button>
                                                </form>

                                                {{-- Case 3: Customer has confirmed they received the item --}}
                                            @elseif($order->status == 'item_received')
                                                <a href="#"
                                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium text-white h-10 px-4 py-2 bg-yellow-500 hover:bg-yellow-600">
                                                    <i class="fas fa-star mr-2"></i>Đánh giá
                                                </a>
                                            @endif
                                            {{-- ====== END NEW BUTTON LOGIC ====== --}}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-8">Bạn chưa có đơn hàng nào.</p>
                            @endforelse
                        </div>
                    </section>
                </section>

                <section id="personal-info" class="mb-10">
                    {{-- DYNAMIC PERSONAL INFO --}}
                    <h2 class="text-2xl font-bold mb-6">Thông Tin Cá Nhân</h2>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold">Thông tin chi tiết</h3><a
                                href="{{ route('customer.profile.edit') }}"
                                class="text-orange-500 hover:underline text-sm font-medium flex items-center"><i
                                    class="fas fa-edit mr-1"></i> Chỉnh sửa</a>
                        </div>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm text-gray-500 mb-1">Họ và tên</h4>
                                <p class="font-medium">{{ $user->full_name }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm text-gray-500 mb-1">Email</h4>
                                <p class="font-medium">{{ $user->email }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm text-gray-500 mb-1">Số điện thoại</h4>
                                <p class="font-medium">{{ $user->phone }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm text-gray-500 mb-1">Ngày sinh</h4>
                                <p class="font-medium">
                                    {{ $user->birthday ? $user->birthday->format('d/m/Y') : 'Chưa cập nhật' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm text-gray-500 mb-1">Giới tính</h4>
                                <p class="font-medium">{{ $user->gender ? ucfirst($user->gender) : 'Chưa cập nhật' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="addresses" class="mb-10">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Địa Chỉ Đã Lưu</h2><button
                            class="bg-orange-500 hover:bg-orange-600 text-white text-sm px-4 py-2 rounded-lg transition-colors flex items-center"><i
                                class="fas fa-plus mr-2"></i> Thêm địa chỉ mới</button>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        {{-- DYNAMIC ADDRESSES --}}
                        @forelse($user->addresses as $address)
                            <div
                                class="bg-white rounded-xl shadow-sm p-6 @if ($address->is_default) border-l-4 border-orange-500 @endif">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-bold mb-1">
                                            {{ $address->type === 'home' ? 'Nhà riêng' : ($address->type === 'office' ? 'Văn phòng' : 'Khác') }}
                                        </h3>
                                        @if ($address->is_default)
                                            <p class="text-gray-500 text-sm">Mặc định</p>
                                        @endif
                                    </div>
                                    <div class="flex space-x-2"><button
                                            class="text-gray-500 hover:text-orange-500 transition-colors"><i
                                                class="fas fa-edit"></i></button><button
                                            class="text-gray-500 hover:text-red-500 transition-colors"><i
                                                class="fas fa-trash-alt"></i></button></div>
                                </div>
                                <div class="space-y-2">
                                    <p class="font-medium">{{ $address->recipient_name ?? $user->full_name }}</p>
                                    <p class="text-gray-600">{{ $address->recipient_phone ?? $user->phone }}</p>
                                    <p class="text-gray-600">{{ $address->full_address }}</p>
                                </div>
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
                            @if ($favorite->product)
                                {{-- Ensure product exists --}}
                                <div class="bg-white rounded-xl shadow-sm overflow-hidden group">
                                    <a href="{{ route('products.show', $favorite->product->id) }}"
                                        class="block relative h-48"><img
                                            src="{{ $favorite->product->primaryImage ? Storage::disk('s3')->url($favorite->product->primaryImage->img) : asset('images/default-product.png') }}"
                                            alt="{{ $favorite->product->name }}" class="w-full h-full object-cover"></a>
                                    <div class="p-4">
                                        <h3 class="font-bold mb-1"><a
                                                href="{{ route('products.show', $favorite->product->id) }}"
                                                class="hover:text-orange-500">{{ $favorite->product->name }}</a></h3>
                                        <p class="text-gray-500 text-sm mb-2 h-10">
                                            {{ Str::limit($favorite->product->short_description, 60) }}</p>
                                        <div class="flex justify-between items-center">
                                            <span
                                                class="font-bold text-orange-500">{{ number_format($favorite->product->base_price, 0, ',', '.') }}đ</span>
                                            <button
                                                class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-lg text-sm transition-colors">Thêm
                                                vào giỏ</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <p class="md:col-span-2 lg:col-span-3 text-gray-500 text-center py-4">Bạn chưa yêu thích món ăn
                                nào.</p>
                        @endforelse
                    </div>
                </section>

                <section id="rewards" class="mb-10">
                    <h2 class="text-2xl font-bold mb-6">Điểm Thưởng & Ưu Đãi</h2>
                    {{-- DYNAMIC REWARDS --}}
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold mb-1">Điểm thưởng của bạn</h3>
                                <p class="text-gray-500">Sử dụng điểm để đổi lấy ưu đãi hấp dẫn</p>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <div class="flex items-center bg-orange-50 px-4 py-2 rounded-lg"><i
                                        class="fas fa-medal text-orange-500 mr-2"></i><span
                                        class="text-2xl font-bold text-orange-500">{{ number_format($currentPoints, 0, ',', '.') }}</span><span
                                        class="text-gray-500 ml-2">điểm</span></div>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-6">
                            <h4 class="font-bold mb-4">Lịch sử điểm thưởng</h4>
                            <div class="space-y-4">
                                @forelse($pointHistory as $history)
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-medium">{{ $history->reason }}</p>
                                            <p class="text-sm text-gray-500">{{ $history->created_at->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        @if ($history->points > 0)
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
                                            @if ($voucher->end_date)
                                                <p class="text-gray-500 text-xs mt-2">Hết hạn:
                                                    {{ $voucher->end_date->format('d/m/Y') }}</p>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-center"><span
                                                class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full mb-2">{{ $voucher->code }}</span><button
                                                class="text-orange-500 border border-orange-500 hover:bg-orange-50 px-4 py-1 rounded-lg text-sm transition-colors">Sử
                                                dụng ngay</button></div>
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
    {{-- THÊM ĐOẠN HTML NÀY VÀO CUỐI FILE BLADE CỦA BẠN --}}
    <div id="action-confirmation-modal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div id="modal-icon-container"
                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i id="modal-icon" class="fas fa-times text-red-600 text-xl"></i>
                </div>
                <h3 id="action-modal-title" class="text-lg leading-6 font-medium text-gray-900 mt-4">Xác nhận hành động
                </h3>
                <div class="mt-2 px-7 py-3">
                    <p id="action-modal-message" class="text-sm text-gray-500">
                        Bạn có chắc chắn thực hiện thao tác này không?
                    </p>
                </div>
                <div class="items-center px-4 py-3 flex gap-3">
                    <button id="action-abort-btn"
                        class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Không
                    </button>
                    <button id="action-confirm-btn"
                        class="w-full px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                        Đồng ý
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <div id="toast-message" class="fixed top-6 right-6 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50 hidden">
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /**
             * ===================================================================
             * HÀM TIỆN ÍCH CHUNG
             * ===================================================================
             */
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
                toast.className =
                    `fixed top-5 right-4 text-white px-4 py-2 rounded-lg shadow-lg z-[101] transition-opacity duration-300 opacity-0 ${bgColor}`;
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => toast.classList.remove('opacity-0'), 10);
                setTimeout(() => {
                    toast.classList.add('opacity-0');
                    setTimeout(() => document.body.removeChild(toast), 300);
                }, 3000);
            }

            /**
             * ===================================================================
             * MODULE 1: LOGIC XỬ LÝ CHỈNH SỬA HỒ SƠ
             * ===================================================================
             */
            const ProfilePageLogic = {
                elements: {
                    modal: document.getElementById('edit-profile-modal'),
                    closeButton: document.getElementById('close-modal'),
                    form: document.getElementById('edit-profile-form'),
                    editButtons: []
                },

                init() {
                    // Helper để tìm nút bấm bằng text, vì :contains không hoạt động
                    this.elements.editButtons = Array.from(document.querySelectorAll('a, button')).filter(el =>
                        el.textContent.includes('Chỉnh sửa hồ sơ'));

                    if (!this.elements.modal || this.elements.editButtons.length === 0) {
                        console.log('Không tìm thấy modal hoặc nút chỉnh sửa hồ sơ.');
                        return;
                    }
                    this.setupEventListeners();
                    console.log('[ProfilePageLogic] Initialized successfully.');
                },

                setupEventListeners() {
                    this.elements.editButtons.forEach(button => {
                        button.addEventListener('click', (e) => {
                            e.preventDefault(); // Dùng cho cả thẻ <a> và <button>
                            this.openModal();
                        });
                    });

                    this.elements.closeButton?.addEventListener('click', () => this.closeModal());
                    this.elements.modal?.addEventListener('click', (e) => {
                        if (e.target === this.elements.modal) this.closeModal();
                    });
                    this.elements.form?.addEventListener('submit', (e) => this.handleFormSubmit(e));
                    document.addEventListener('keydown', (e) => {
                        if (e.key === "Escape" && !this.elements.modal.classList.contains('hidden')) {
                            this.closeModal();
                        }
                    });
                },

                openModal() {
                    this.elements.modal.classList.remove('hidden');
                },

                closeModal() {
                    this.elements.modal.classList.add('hidden');
                },

                handleFormSubmit(event) {
                    event.preventDefault();
                    // Nơi để bạn thêm logic gửi dữ liệu form bằng AJAX sau này
                    console.log('Form submitted!');
                    this.closeModal();
                    showToast('Thông tin hồ sơ đã được cập nhật!');
                }
            };

            /**
             * ===================================================================
             * MODULE 2: LOGIC XỬ LÝ HỦY ĐƠN HÀNG (Giữ nguyên, đã ổn)
             * ===================================================================
             */
            let formToSubmit = null;
            let modalAction = 'cancel'; // hoặc 'receive'

            const modal = document.getElementById('action-confirmation-modal');
            const modalIcon = document.getElementById('modal-icon');
            const modalIconContainer = document.getElementById('modal-icon-container');
            const modalTitle = document.getElementById('action-modal-title');
            const modalMessage = document.getElementById('action-modal-message');
            const confirmBtn = document.getElementById('action-confirm-btn');
            const abortBtn = document.getElementById('action-abort-btn');

            function openActionModal(form, actionType) {
                formToSubmit = form;
                modalAction = actionType;

                if (actionType === 'cancel') {
                    modalIcon.className = "fas fa-times text-red-600 text-xl";
                    modalIconContainer.className =
                        "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100";
                    modalTitle.textContent = "Xác nhận hủy đơn hàng";
                    modalMessage.textContent =
                        "Bạn có chắc chắn muốn hủy đơn hàng này không? Hành động này không thể hoàn tác.";
                    confirmBtn.className = "w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700";
                    confirmBtn.textContent = "Đồng ý hủy";
                } else if (actionType === 'receive') {
                    modalIcon.className = "fas fa-check text-green-600 text-xl";
                    modalIconContainer.className =
                        "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100";
                    modalTitle.textContent = "Xác nhận đã nhận hàng";
                    modalMessage.textContent =
                        "Bạn xác nhận đã nhận được hàng? Vui lòng kiểm tra kỹ trước khi xác nhận.";
                    confirmBtn.className = "w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700";
                    confirmBtn.textContent = "Đã nhận";
                }
                modal.classList.remove('hidden');
            }

            function closeActionModal() {
                formToSubmit = null;
                modal.classList.add('hidden');
            }

            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    if (formToSubmit) {
                        const form = formToSubmit;
                        const action = form.getAttribute('action');
                        const methodInput = form.querySelector('input[name="_method"]');
                        const csrf = form.querySelector('input[name="_token"]').value;
                        const status = form.querySelector('input[name="status"]').value;
                        const method = methodInput ? methodInput.value : form.method;
                        const formData = new FormData();
                        formData.append('_token', csrf);
                        formData.append('status', status);
                        if (methodInput) formData.append('_method', method);

                        fetch(action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                closeActionModal();
                                if (data.success) {
                                    showToast(
                                        modalAction === 'cancel' ? 'Hủy đơn hàng thành công!' :
                                        'Đã nhận hàng thành công!'
                                    );
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1300);
                                } else {
                                    showToast(data.message || 'Có lỗi xảy ra!', "bg-red-600");
                                }
                            })
                            .catch(() => {
                                closeActionModal();
                                showToast('Có lỗi khi kết nối!', "bg-red-600");
                            });
                    } else {
                        closeActionModal();
                    }
                });
            }
            if (abortBtn) {
                abortBtn.addEventListener('click', function() {
                    closeActionModal();
                });
            }

            // Nút hủy đơn hàng
            document.querySelectorAll('.cancel-order-form button[type="submit"]').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const form = this.closest('form');
                    openActionModal(form, 'cancel');
                });
            });

            // Nút xác nhận đã nhận hàng
            document.querySelectorAll('.receive-order-form button[type="submit"]').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const form = this.closest('form');
                    openActionModal(form, 'receive');
                });
            });

            // ===================================================================
            // KHỞI CHẠY CÁC MODULE
            // ===================================================================
            ProfilePageLogic.init();

        });
    </script>
@endsection

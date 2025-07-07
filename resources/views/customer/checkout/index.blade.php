@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Thanh Toán')

@section('head')
    <style>
        /* Styling for autocomplete dropdown */
        .autocomplete-items {
            position: absolute;
            border: 1px solid #d4d4d4;
            border-bottom: none;
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
            max-height: 200px;
            overflow-y: auto;
            border-radius: 0 0 0.25rem 0.25rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            background-color: #fff;
            border-bottom: 1px solid #d4d4d4;
        }

        .autocomplete-items div:hover {
            background-color: #FEF3C7;
        }

        .autocomplete-active {
            background-color: #FDBA74 !important;
            color: #fff;
        }
    </style>
@endsection

@section('content')
    <style>
        .container {
            max-width: 1280px;
            margin: 0 auto;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: #F97316;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(249, 115, 22, 0.25);
        }

        .form-label {
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .required::after {
            content: "*";
            color: #dc3545;
            margin-left: 0.25rem;
        }
    </style>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-2">Thanh Toán</h1>
        <p class="text-gray-500 mb-8">Hoàn tất đơn hàng của bạn</p>

        <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
            @csrf

            <!-- Địa chỉ nhận hàng (giống Shopee) -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex items-center border border-orange-200 relative">
                <span class="text-orange-500 mr-3 text-xl">
                    <i class="fas fa-map-marker-alt"></i>
                </span>
                <div class="flex-1">
                    <div class="font-semibold text-base mb-1">
                        <span class="font-bold">Bùi Đức Dương</span>
                        <span class="ml-2">(+84) 355032605</span>
                        <span class="ml-2 align-middle">
                            <span class="border border-orange-500 text-orange-500 px-2 py-0.5 rounded text-xs font-medium bg-white">Mặc Định</span>
                        </span>
                    </div>
                    <div class="text-gray-800 text-sm">
                        Số Nhà 26, Ngách 66 Ngõ 250 Kim Giang, Phường Đại Kim, Quận Hoàng Mai, Hà Nội
                    </div>
                </div>
                <a href="#" id="changeAddressBtn" class="ml-4 text-blue-600 hover:underline font-medium text-sm">Thay Đổi</a>
            </div>

            

            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <!-- Thông tin khách hàng -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Thông Tin Giao Hàng</h2>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label for="full_name" class="form-label required">Họ và tên</label>
                                <input type="text" id="full_name" name="full_name" class="form-control"
                                    value="{{ old('full_name', auth()->user()->full_name ?? '') }}" required>
                                @error('full_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="form-label required">Số điện thoại</label>
                                <input type="tel" id="phone" name="phone" class="form-control"
                                    value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                                @error('phone')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="email" class="form-label required">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="{{ old('email', auth()->user()->email ?? '') }}" required>
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            @auth
                                @if($userAddresses && $userAddresses->count() > 0)
                                    <div class="md:col-span-2 mb-4">
                                        <label for="saved_address" class="form-label">Chọn địa chỉ đã lưu</label>
                                        <select id="saved_address" name="address_id" class="form-control">
                                            <option value="">-- Chọn địa chỉ hoặc nhập mới --</option>
                                            @foreach($userAddresses as $savedAddress)
                                                <option value="{{ $savedAddress->id }}" 
                                                    data-address="{{ $savedAddress->address_line }}"
                                                    data-city="{{ $savedAddress->city }}"
                                                    data-district="{{ $savedAddress->district }}"
                                                    data-ward="{{ $savedAddress->ward }}"
                                                    data-phone="{{ $savedAddress->phone_number }}"
                                                    {{ $savedAddress->is_default ? 'selected' : '' }}>
                                                    {{ $savedAddress->address_line }}, {{ $savedAddress->ward }}, {{ $savedAddress->district }}, {{ $savedAddress->city }}
                                                    @if($savedAddress->is_default)
                                                        <span class="text-green-600">(Mặc định)</span>
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Chọn địa chỉ có sẵn hoặc để trống để nhập địa chỉ mới
                                        </div>
                                        @error('address_id')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            @endauth

                            <div>
                                <label for="city" class="form-label required">Tỉnh/Thành phố</label>
                                <select id="city" name="city" class="form-control" required>
                                    <option value="Hà Nội" selected>Hà Nội</option>
                                </select>
                                @error('city')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="district" class="form-label required">Quận/Huyện</label>
                                <select id="district" name="district" class="form-control" required>
                                    <option value="">-- Chọn Quận/Huyện --</option>
                                </select>
                                @error('district')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="ward" class="form-label required">Xã/Phường</label>
                                <select id="ward" name="ward" class="form-control" required>
                                    <option value="">-- Chọn Xã/Phường --</option>
                                </select>
                                @error('ward')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-2 relative">
                                <label for="address" class="form-label required">Số nhà, đường</label>
                                <input type="text" id="address" name="address" class="form-control"
                                    value="{{ old('address', auth()->user()->address ?? '') }}" autocomplete="off"
                                    required>
                                <div id="address-autocomplete" class="autocomplete-items" style="display: none;"></div>
                                <div class="text-xs text-gray-500 mt-1">Nhập địa chỉ sau khi chọn Quận/Huyện và Phường/Xã
                                </div>
                                @error('address')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="form-label">Ghi chú đơn hàng</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3"
                                placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn.">{{ old('notes') }}</textarea>
                        </div>
                    </div>



                    <!-- Phương thức thanh toán -->
                    <div class="mt-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Phương thức thanh toán</h3>
                        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                            <div class="space-y-4">
                                <!-- COD -->
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="cod" class="h-5 w-5 text-orange-500" checked>
                                    <div class="ml-3">
                                        <span class="block font-medium">COD</span>
                                        <span class="block text-sm text-gray-500">Thanh toán trực tiếp cho tài xế</span>
                                    </div>
                                    <span class="ml-auto">
                                        <i class="fas fa-money-bill-wave text-green-500 text-xl"></i>
                                    </span>
                                </label>
                                
                                <!-- VNPAY -->
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="vnpay" class="h-5 w-5 text-orange-500">
                                    <div class="ml-3">
                                        <span class="block font-medium">VNPAY</span>
                                        <span class="block text-sm text-gray-500">Thanh toán tiện lợi thông qua VNPAY</span>
                                    </div>
                                    <span class="ml-auto">
                                        <i class="fas fa-credit-card text-blue-600 text-xl"></i>
                                    </span>
                                </label>

                                <!-- Dành cho người dùng đã đăng nhập -->
                                @auth
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="payment_method" value="balance" class="h-5 w-5 text-orange-500">
                                        <div class="ml-3">
                                            <span class="block font-medium">Số dư</span>
                                            <span class="block text-sm text-gray-500">
                                                Thanh toán bằng số dư tài khoản của bạn: 
                                                <strong>{{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }} đ</strong>
                                            </span>
                                        </div>
                                        <span class="ml-auto">
                                            <i class="fas fa-wallet text-purple-500 text-xl"></i>
                                        </span>
                                    </label>
                                @endauth
                                
                                <!-- Dành cho khách -->
                                @guest
                                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-center">
                                        <p class="text-sm text-blue-700">
                                            <a href="{{ route('customer.login') }}" class="font-bold underline hover:text-blue-800">Đăng nhập</a> để sử dụng số dư và nhận nhiều ưu đãi hơn!
                                        </p>
                                    </div>
                                @endguest
                            </div>
                            @error('payment_method')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Tóm tắt đơn hàng -->
                <div>
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                        <h2 class="text-xl font-bold mb-4">Đơn Hàng Của Bạn</h2>

                        <div class="space-y-4">
                            @foreach ($cartItems as $item)
                                <div class="flex items-center gap-4">
                                    <div class="relative h-16 w-16 flex-shrink-0 rounded overflow-hidden">
                                        @if ($item->variant->product->primary_image)
                                            <img src="{{ Storage::disk('s3')->url($item->variant->product->primary_image->img) }}"
                                                alt="{{ $item->variant->product->name }}"
                                                class="object-cover w-full h-full">
                                        @else
                                            <div class="h-full w-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-sm truncate">{{ $item->variant->product->name }}</h3>
                                        <p class="text-xs text-gray-500">
                                            @if ($item->variant->variant_description)
                                                {{ $item->variant->variant_description }}
                                            @else
                                                {{ implode(', ', $item->variant->variantValues->pluck('value')->toArray()) }}
                                            @endif
                                        </p>
                                        @if ($item->toppings && $item->toppings->count() > 0)
                                            <p class="text-xs text-orange-600 mt-1">
                                                +{{ $item->toppings->count() }} topping
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium">
                                            @php
                                                $itemPrice = $item->variant->price;
                                                foreach ($item->toppings as $topping) {
                                                    $itemPrice += $topping->price;
                                                }
                                                $itemTotal = $itemPrice * $item->quantity;
                                            @endphp
                                            {{ number_format($itemTotal) }}đ
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            SL: {{ $item->quantity }}
                                        </div>
                                    </div>
                                </div>
                                <hr class="border-t border-gray-200 my-6">
                            @endforeach
                        </div>


                        <div class="space-y-4 mb-6">
                            @php
                                $subtotal = 0;
                                $shipping = 0;
                                $discount = request()->query('discount', session('discount', 0));

                                // Calculate subtotal from cart items
                                foreach ($cartItems as $item) {
                                    $itemPrice = $item->variant->price;
                                    foreach ($item->toppings as $topping) {
                                        $itemPrice += $topping->price;
                                    }
                                    $subtotal += $itemPrice * $item->quantity;
                                }

                                // Calculate shipping
                                $shipping = $subtotal > 100000 ? 0 : 15000;

                                // Calculate total
                                $total = $subtotal + $shipping - $discount;
                            @endphp

                            <div class="flex justify-between">
                                <span class="text-gray-600">Tạm tính</span>
                                <span>{{ number_format($subtotal) }}đ</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Phí giao hàng</span>
                                <span>{{ $shipping > 0 ? number_format($shipping) . 'đ' : 'Miễn phí' }}</span>
                            </div>

                            @if ($discount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Giảm giá</span>
                                    <span>-{{ number_format($discount) }}đ</span>
                                </div>
                            @endif

                            <hr class="border-t border-gray-200">

                            <div class="flex justify-between font-bold text-lg">
                                <span>Tổng cộng</span>
                                <span>{{ number_format($total) }}đ</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="terms" name="terms" class="h-4 w-4 text-orange-500"
                                    required>
                                <label for="terms" class="ml-2 text-sm text-gray-600">
                                    Tôi đã đọc và đồng ý với <a href="/terms"
                                        class="text-orange-500 hover:underline">điều khoản và điều kiện</a> của website
                                </label>
                            </div>

                            <button type="submit"
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white text-center px-6 py-3 rounded-md font-medium transition-colors">
                                Đặt Hàng
                            </button>

                            <div class="text-center">
                                <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-orange-500">
                                    <i class="fas fa-arrow-left mr-2"></i> Quay lại giỏ hàng
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Modal chọn địa chỉ nhận hàng (tĩnh, giống Shopee) -->
        <div id="addressModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
                <div class="px-6 py-4 border-b">
                    <span class="text-lg font-semibold">Địa Chỉ Của Tôi</span>
                </div>
                <div class="px-6 py-4 max-h-96 overflow-y-auto divide-y">
                    <div class="flex items-start py-3">
                        <input type="radio" name="address" class="mt-1 mr-3 h-5 w-5 text-orange-500" checked>
                        <div class="flex-1">
                            <div class="font-semibold">Nhữ Thị Minh <span class="font-normal text-gray-600">(+84) 975 154 746</span></div>
                            <div class="text-sm text-gray-700">Hẻm số 4,Hẻm 144 Đồ Lương<br>Phường 11, Thành Phố Vũng Tàu, Bà Rịa - Vũng Tàu</div>
                        </div>
                        <a href="#" class="ml-3 text-blue-600 hover:underline text-sm font-medium">Cập nhật</a>
                    </div>
                    <div class="flex items-start py-3">
                        <input type="radio" name="address" class="mt-1 mr-3 h-5 w-5 text-orange-500">
                        <div class="flex-1">
                            <div class="font-semibold">Nguyễn Thị Huê <span class="font-normal text-gray-600">(+84) 966 189 711</span></div>
                            <div class="text-sm text-gray-700">Cổng chợ mền Thanh Khê<br>Xã Thanh Hải, Huyện Thanh Liêm, Hà Nam</div>
                        </div>
                        <a href="#" class="ml-3 text-blue-600 hover:underline text-sm font-medium">Cập nhật</a>
                    </div>
                    <div class="flex items-start py-3">
                        <input type="radio" name="address" class="mt-1 mr-3 h-5 w-5 text-orange-500">
                        <div class="flex-1">
                            <div class="font-semibold">Mai Xuân Cường <span class="font-normal text-gray-600">(+84) 977 312 936</span></div>
                            <div class="text-sm text-gray-700">Nhà Nghỉ Thế Cường, Thanh Khê<br>Xã Thanh Hải, Huyện Thanh Liêm, Hà Nam</div>
                        </div>
                        <a href="#" class="ml-3 text-blue-600 hover:underline text-sm font-medium">Cập nhật</a>
                    </div>
                </div>
                <div class="px-6 py-3">
                    <button class="flex items-center text-orange-600 border border-orange-500 rounded px-3 py-1 text-sm font-medium hover:bg-orange-50 mb-3">
                        <i class="fas fa-plus mr-2"></i> Thêm Địa Chỉ Mới
                    </button>
                    <div class="flex justify-end gap-3">
                        <button type="button" id="addressModalCancel" class="px-5 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">Hủy</button>
                        <button type="button" class="px-5 py-2 rounded bg-orange-500 text-white font-semibold hover:bg-orange-600">Xác nhận</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal cập nhật địa chỉ (tĩnh, giống Shopee) -->
        <div id="updateAddressModal" class="fixed inset-0 z-60 flex items-center justify-center bg-black bg-opacity-40 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
                <div class="max-h-[80vh] overflow-y-auto scrollbar-none" style="scrollbar-width: none; -ms-overflow-style: none;">
                    <div class="px-6 py-4 border-b">
                        <span class="text-lg font-semibold">Cập nhật địa chỉ</span>
                    </div>
                    <form class="px-6 py-4">
                        <div class="flex gap-3 mb-3">
                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 mb-1">Họ và tên</label>
                                <input type="text" class="w-full border rounded px-3 py-2" value="Bùi Đức Dương">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 mb-1">Số điện thoại</label>
                                <input type="text" class="w-full border rounded px-3 py-2" value="(+84) 355 032 605">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Tỉnh/Thành phố</label>
                            <select class="w-full border rounded px-3 py-2 mb-2">
                                <option>Hà Nội</option>
                            </select>
                            <label class="block text-xs text-gray-500 mb-1">Quận/Huyện</label>
                            <select class="w-full border rounded px-3 py-2 mb-2">
                                <option>Quận Hoàng Mai</option>
                            </select>
                            <label class="block text-xs text-gray-500 mb-1">Phường/Xã</label>
                            <select class="w-full border rounded px-3 py-2">
                                <option>Phường Đại Kim</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Địa chỉ cụ thể</label>
                            <textarea class="w-full border rounded px-3 py-2" rows="2">Số Nhà 26, Ngách 66 Ngõ 250 Kim Giang</textarea>
                        </div>
                        <div class="mb-3">
                            <img src="https://maps.googleapis.com/maps/api/staticmap?center=21.002,105.825&zoom=16&size=400x120&markers=color:red%7C21.002,105.825&key=AIzaSyDUMMYKEY" alt="Google Map" class="w-full rounded border" style="height:120px;object-fit:cover;">
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Loại địa chỉ:</label>
                            <div class="flex gap-2">
                                <button type="button" class="border border-orange-500 text-orange-600 bg-orange-50 px-4 py-1 rounded font-medium">Nhà Riêng</button>
                                <button type="button" class="border border-gray-300 text-gray-700 bg-white px-4 py-1 rounded font-medium">Văn Phòng</button>
                            </div>
                        </div>
                        <div class="mb-3 flex items-center">
                            <input type="checkbox" id="setDefaultAddress" class="mr-2" checked disabled>
                            <label for="setDefaultAddress" class="text-xs text-gray-400 select-none">Đặt làm địa chỉ mặc định</label>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" id="updateAddressBack" class="px-5 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">Trở Lại</button>
                            <button type="button" class="px-5 py-2 rounded bg-orange-500 text-white font-semibold hover:bg-orange-600">Hoàn thành</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal thêm địa chỉ mới (tĩnh, giống modal cập nhật) -->
        <div id="addAddressModal" class="fixed inset-0 z-60 flex items-center justify-center bg-black bg-opacity-40 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
                <div class="max-h-[80vh] overflow-y-auto scrollbar-none" style="scrollbar-width: none; -ms-overflow-style: none;">
                    <div class="px-6 py-4 border-b">
                        <span class="text-lg font-semibold">Thêm địa chỉ mới</span>
                    </div>
                    <form class="px-6 py-4">
                        <div class="flex gap-3 mb-3">
                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 mb-1">Họ và tên</label>
                                <input type="text" class="w-full border rounded px-3 py-2" value="">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 mb-1">Số điện thoại</label>
                                <input type="text" class="w-full border rounded px-3 py-2" value="">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Tỉnh/Thành phố</label>
                            <select class="w-full border rounded px-3 py-2 mb-2">
                                <option>Hà Nội</option>
                            </select>
                            <label class="block text-xs text-gray-500 mb-1">Quận/Huyện</label>
                            <select class="w-full border rounded px-3 py-2 mb-2">
                                <option>Quận Hoàng Mai</option>
                            </select>
                            <label class="block text-xs text-gray-500 mb-1">Phường/Xã</label>
                            <select class="w-full border rounded px-3 py-2">
                                <option>Phường Đại Kim</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Địa chỉ cụ thể</label>
                            <textarea class="w-full border rounded px-3 py-2" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <img src="https://maps.googleapis.com/maps/api/staticmap?center=21.002,105.825&zoom=16&size=400x120&markers=color:red%7C21.002,105.825&key=AIzaSyDUMMYKEY" alt="Google Map" class="w-full rounded border" style="height:120px;object-fit:cover;">
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500 mb-1">Loại địa chỉ:</label>
                            <div class="flex gap-2">
                                <button type="button" class="border border-orange-500 text-orange-600 bg-orange-50 px-4 py-1 rounded font-medium">Nhà Riêng</button>
                                <button type="button" class="border border-gray-300 text-gray-700 bg-white px-4 py-1 rounded font-medium">Văn Phòng</button>
                            </div>
                        </div>
                        <div class="mb-3 flex items-center">
                            <input type="checkbox" id="setDefaultAddressAdd" class="mr-2">
                            <label for="setDefaultAddressAdd" class="text-xs text-gray-500 select-none">Đặt làm địa chỉ mặc định</label>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" id="addAddressBack" class="px-5 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">Trở Lại</button>
                            <button type="button" class="px-5 py-2 rounded bg-orange-500 text-white font-semibold hover:bg-orange-600">Hoàn thành</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Constants
            const HANOI_PROVINCE_CODE = 1;

            // Elements
            const districtSelect = document.getElementById('district');
            const wardSelect = document.getElementById('ward');
            const addressInput = document.getElementById('address');
            const addressAutocomplete = document.getElementById('address-autocomplete');
            const checkoutForm = document.getElementById('checkout-form');
            const savedAddressSelect = document.getElementById('saved_address');
            const phoneInput = document.getElementById('phone');

            console.log('Script loaded, elements:', {
                district: districtSelect,
                ward: wardSelect,
                address: addressInput,
                autocomplete: addressAutocomplete
            });

            // Common street naming patterns in Vietnam
            const streetPrefixes = ['Đường', 'Phố', 'Ngõ', 'Ngách', 'Hẻm', 'Tổ', 'Xóm', 'Làng', 'Thôn', 'Đại lộ',
                'Quốc lộ'
            ];

            // Popular streets in Hanoi by district
            const districtStreets = {
                'Ba Đình': ['Kim Mã', 'Liễu Giai', 'Nguyễn Thái Học', 'Phan Đình Phùng', 'Quán Thánh',
                    'Trần Phú', 'Hoàng Diệu', 'Đội Cấn', 'Châu Long', 'Đặng Dung'
                ],
                'Hoàn Kiếm': ['Hàng Bông', 'Tràng Tiền', 'Hàng Đào', 'Bà Triệu', 'Lý Thái Tổ',
                    'Đinh Tiên Hoàng', 'Hàng Bạc', 'Lê Thái Tổ', 'Hàng Gai', 'Trần Hưng Đạo'
                ],
                'Hai Bà Trưng': ['Bạch Mai', 'Lò Đúc', 'Bà Triệu', 'Nguyễn Du', 'Trần Xuân Soạn', 'Trương Định',
                    'Minh Khai', 'Đại Cồ Việt', 'Kim Ngưu', 'Thanh Nhàn'
                ],
                'Đống Đa': ['Tây Sơn', 'Khâm Thiên', 'Xã Đàn', 'Nguyễn Lương Bằng', 'Phạm Ngọc Thạch',
                    'Thái Hà', 'Chùa Bộc', 'Tôn Đức Thắng', 'Láng Hạ', 'Nguyễn Chí Thanh'
                ],
                'Tây Hồ': ['Lạc Long Quân', 'Âu Cơ', 'Xuân Diệu', 'Quảng An', 'Thụy Khuê', 'Tứ Liên',
                    'Quảng Khánh', 'Từ Hoa', 'Nhật Chiêu', 'An Dương Vương'
                ],
                'Cầu Giấy': ['Xuân Thủy', 'Trần Thái Tông', 'Phạm Hùng', 'Dương Đình Nghệ', 'Nguyễn Khang',
                    'Nguyễn Phong Sắc', 'Trần Duy Hưng', 'Cầu Giấy', 'Phan Văn Trường', 'Trung Kính'
                ],
                'Thanh Xuân': ['Nguyễn Trãi', 'Nguyễn Tuân', 'Lê Văn Lương', 'Vũ Trọng Phụng', 'Khuất Duy Tiến',
                    'Nguyễn Huy Tưởng', 'Lê Trọng Tấn', 'Định Công', 'Triều Khúc', 'Nguyễn Xiển'
                ],
                'Hoàng Mai': ['Giải Phóng', 'Trương Định', 'Định Công', 'Lĩnh Nam', 'Tân Mai', 'Linh Đàm',
                    'Đền Lừ', 'Hoàng Liệt', 'Yên Sở', 'Vĩnh Hưng'
                ],
                'Long Biên': ['Nguyễn Văn Cừ', 'Ngọc Lâm', 'Ngọc Thụy', 'Gia Thụy', 'Việt Hưng', 'Bồ Đề',
                    'Long Biên', 'Cổ Linh', 'Thượng Thanh', 'Đức Giang'
                ],
                'Hà Đông': ['Quang Trung', 'Tô Hiệu', 'Nguyễn Trãi', 'Mộ Lao', 'La Khê', 'Hà Cầu', 'Văn Quán',
                    'Yên Nghĩa', 'Kiến Hưng', 'Phúc La'
                ]
            };

            // Current autocomplete selection
            let currentFocus = -1;

            // Fetch districts of Hanoi on page load
            fetchDistricts();

            // Handle saved address selection
            if (savedAddressSelect) {
                savedAddressSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    
                    if (selectedOption.value) {
                        // Auto-fill form fields with saved address data
                        const addressData = {
                            address: selectedOption.dataset.address,
                            city: selectedOption.dataset.city,
                            district: selectedOption.dataset.district,
                            ward: selectedOption.dataset.ward,
                            phone: selectedOption.dataset.phone
                        };

                        // Fill address fields
                        addressInput.value = addressData.address;
                        if (phoneInput && addressData.phone) {
                            phoneInput.value = addressData.phone;
                        }

                        // Set city (should be Hà Nội by default)
                        document.getElementById('city').value = addressData.city;

                        // Trigger district fetch and set district value
                        fetchDistricts().then(() => {
                            setTimeout(() => {
                                districtSelect.value = addressData.district;
                                
                                // Trigger ward fetch and set ward value
                                fetchWards().then(() => {
                                    setTimeout(() => {
                                        wardSelect.value = addressData.ward;
                                    }, 100);
                                });
                            }, 100);
                        });

                        showToast('Đã điền thông tin từ địa chỉ đã lưu');
                    } else {
                        // Clear form fields when no address is selected
                        addressInput.value = '';
                        districtSelect.selectedIndex = 0;
                        wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
                    }
                });
            }

            // Add event listeners
            districtSelect.addEventListener('change', function() {
                console.log('District changed to:', districtSelect.value);
                fetchWards();
                // Reset address input
                addressInput.value = '';
            });

            wardSelect.addEventListener('change', function() {
                console.log('Ward changed to:', wardSelect.value);
                const isWardSelected = wardSelect.value !== '';

                if (isWardSelected) {
                    // Add small delay to allow the user to see the selection
                    setTimeout(() => {
                        addressInput.focus();
                        // Update placeholder to guide user
                        addressInput.placeholder =
                            `Nhập địa chỉ tại ${wardSelect.value}, ${districtSelect.value}`;
                    }, 300);
                }
            });

            // Set up autocomplete for address input
            addressInput.addEventListener('input', function() {
                const val = this.value;
                closeAllLists();

                if (!val || val.length < 2) return;

                // Make sure district and ward are selected
                if (!districtSelect.value || !wardSelect.value) {
                    showToast("Vui lòng chọn Quận/Huyện và Phường/Xã trước");
                    return;
                }

                // Generate suggestions
                const suggestions = generateAddressSuggestions(val);
                displayAddressSuggestions(suggestions);
            });

            // Handle keyboard navigation in autocomplete
            addressInput.addEventListener('keydown', function(e) {
                let items = document.getElementsByClassName('autocomplete-item');
                if (!items || items.length === 0) return;

                // Down arrow
                if (e.keyCode === 40) {
                    currentFocus++;
                    addActive(items);
                }
                // Up arrow
                else if (e.keyCode === 38) {
                    currentFocus--;
                    addActive(items);
                }
                // Enter key
                else if (e.keyCode === 13 && currentFocus > -1) {
                    e.preventDefault();
                    if (items) items[currentFocus].click();
                }
            });

            // Close dropdown when clicking elsewhere
            document.addEventListener('click', function(e) {
                closeAllLists(e.target);
            });

            // Generate address suggestions based on input and selected district
            function generateAddressSuggestions(input) {
                const district = districtSelect.value;
                const ward = wardSelect.value;
                let suggestions = [];

                // Get streets for the selected district
                const streets = districtStreets[district] || [];

                // Add streets that match the input
                streets.forEach(street => {
                    // Check if input matches street name
                    if (street.toLowerCase().includes(input.toLowerCase())) {
                        suggestions.push(`${street}, ${ward}, ${district}`);

                        // Add variation with number if input has no number
                        if (!/\d/.test(input)) {
                            const houseNumber = Math.floor(Math.random() * 100) + 1;
                            suggestions.push(`${houseNumber} ${street}, ${ward}, ${district}`);
                        }
                    }
                });

                // Check if input starts with a street prefix
                streetPrefixes.forEach(prefix => {
                    if (input.toLowerCase().startsWith(prefix.toLowerCase())) {
                        // Add matching streets with this prefix
                        streets.forEach(street => {
                            suggestions.push(`${prefix} ${street}, ${ward}, ${district}`);
                        });
                    }
                });

                // Check if input has number
                if (/\d+/.test(input)) {
                    const number = input.match(/\d+/)[0];
                    const textPart = input.replace(/\d+/g, '').trim();

                    streets.forEach(street => {
                        if (street.toLowerCase().includes(textPart.toLowerCase()) || textPart.length < 2) {
                            suggestions.push(`${number} ${street}, ${ward}, ${district}`);
                        }
                    });
                }

                // If no suggestions based on exact match, add some based on common prefixes
                if (suggestions.length === 0) {
                    streetPrefixes.forEach(prefix => {
                        if (input.toLowerCase().includes(prefix.toLowerCase())) {
                            const afterPrefix = input.toLowerCase().split(prefix.toLowerCase())[1].trim();

                            streets.forEach(street => {
                                if (street.toLowerCase().startsWith(afterPrefix) || afterPrefix
                                    .length < 2) {
                                    suggestions.push(`${prefix} ${street}, ${ward}, ${district}`);
                                }
                            });
                        }
                    });
                }

                // Add generic suggestions if still no matches
                if (suggestions.length === 0) {
                    const matchingStreetPrefixes = streetPrefixes.filter(prefix =>
                        input.toLowerCase().includes(prefix.toLowerCase()));

                    if (matchingStreetPrefixes.length > 0) {
                        const prefix = matchingStreetPrefixes[0];
                        streets.slice(0, 5).forEach(street => {
                            suggestions.push(`${prefix} ${street}, ${ward}, ${district}`);
                        });
                    } else {
                        // Completely generic suggestions
                        streets.slice(0, 5).forEach(street => {
                            suggestions.push(`${street}, ${ward}, ${district}`);
                        });
                    }
                }

                // Limit number of suggestions
                return [...new Set(suggestions)].slice(0, 7);
            }

            // Display address suggestions
            function displayAddressSuggestions(suggestions) {
                // Reset current focus
                currentFocus = -1;

                // Create heading
                const heading = document.createElement('div');
                heading.className = 'p-2 bg-gray-100 font-medium text-gray-800 border-b';
                heading.textContent = 'Gợi ý địa chỉ:';
                addressAutocomplete.appendChild(heading);

                suggestions.forEach(suggestion => {
                    const item = document.createElement('div');
                    item.className = 'autocomplete-item';
                    item.innerHTML = suggestion;

                    // Add click handler
                    item.addEventListener('click', function() {
                        addressInput.value = suggestion;
                        closeAllLists();
                    });

                    addressAutocomplete.appendChild(item);
                });

                // Show the autocomplete container
                addressAutocomplete.style.display = 'block';
            }

            // Add active class to current focused item
            function addActive(items) {
                if (!items) return;

                // Remove active from all items
                removeActive(items);

                // Ensure currentFocus stays within bounds
                if (currentFocus >= items.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = items.length - 1;

                // Add active class
                items[currentFocus].classList.add('autocomplete-active');

                // Scroll to the active item if needed
                items[currentFocus].scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }

            // Remove active class from items
            function removeActive(items) {
                for (let i = 0; i < items.length; i++) {
                    items[i].classList.remove('autocomplete-active');
                }
            }

            // Close all autocomplete lists
            function closeAllLists(elmnt) {
                // Don't close if clicked on the input field
                if (elmnt === addressInput) return;

                // Clear autocomplete content and hide
                addressAutocomplete.innerHTML = '';
                addressAutocomplete.style.display = 'none';
            }

            // Fetch districts from API
            function fetchDistricts() {
                // Show loading state
                districtSelect.innerHTML = '<option value="">Đang tải...</option>';

                // Fetch districts from API
                return fetch(`https://provinces.open-api.vn/api/p/${HANOI_PROVINCE_CODE}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Districts data:', data);
                        // Clear loading state
                        districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';

                        // Add district options
                        if (data && data.districts) {
                            data.districts.forEach(district => {
                                const option = document.createElement('option');
                                option.value = district.name;
                                option.textContent = district.name;
                                option.dataset.code = district.code;
                                districtSelect.appendChild(option);
                            });
                        }
                        return data;
                    })
                    .catch(error => {
                        console.error('Error fetching districts:', error);
                        districtSelect.innerHTML = '<option value="">Không thể tải dữ liệu</option>';
                        throw error;
                    });
            }

            // Fetch wards based on selected district
            function fetchWards() {
                // Reset ward select
                wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';

                const selectedDistrict = districtSelect.options[districtSelect.selectedIndex];
                if (!selectedDistrict || !selectedDistrict.dataset.code) {
                    return Promise.resolve();
                }

                const districtCode = selectedDistrict.dataset.code;

                // Show loading state
                wardSelect.innerHTML = '<option value="">Đang tải...</option>';

                // Fetch wards from API
                return fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Wards data:', data);
                        // Clear loading state
                        wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';

                        // Add ward options
                        if (data && data.wards) {
                            data.wards.forEach(ward => {
                                const option = document.createElement('option');
                                option.value = ward.name;
                                option.textContent = ward.name;
                                wardSelect.appendChild(option);
                            });
                        }
                        return data;
                    })
                    .catch(error => {
                        console.error('Error fetching wards:', error);
                        wardSelect.innerHTML = '<option value="">Không thể tải dữ liệu</option>';
                        throw error;
                    });
            }

            // Form validation
            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate form
                const requiredFields = document.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('border-red-500');
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });

                if (!isValid) {
                    showToast('Vui lòng điền đầy đủ thông tin bắt buộc');
                    return;
                }

                // Submit form
                checkoutForm.submit();
            });

            // Simple toast notification function
            function showToast(message) {
                // Create toast element
                const toast = document.createElement('div');
                toast.className =
                    'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0';
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

            // Khai báo các modal ngoài form
            const addressModal = document.getElementById('addressModal');
            const updateAddressModal = document.getElementById('updateAddressModal');
            const addAddressModal = document.getElementById('addAddressModal');

            // Nút mở modal chọn địa chỉ
            const changeAddressBtn = document.getElementById('changeAddressBtn');
            if (changeAddressBtn && addressModal) {
                changeAddressBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    addressModal.classList.remove('hidden');
                });
            }
            // Nút đóng modal chọn địa chỉ
            const addressModalCancel = document.getElementById('addressModalCancel');
            if (addressModalCancel && addressModal) {
                addressModalCancel.addEventListener('click', function() {
                    addressModal.classList.add('hidden');
                });
            }
            // Nút cập nhật trong modal chọn địa chỉ (mở modal cập nhật)
            document.querySelectorAll('#addressModal a.text-blue-600').forEach(function(updateBtn) {
                updateBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    addressModal.classList.add('hidden');
                    updateAddressModal.classList.remove('hidden');
                });
            });
            // Nút Trở Lại trong modal cập nhật địa chỉ
            const updateAddressBack = document.getElementById('updateAddressBack');
            if (updateAddressBack && updateAddressModal && addressModal) {
                updateAddressBack.addEventListener('click', function() {
                    updateAddressModal.classList.add('hidden');
                    addressModal.classList.remove('hidden');
                });
            }
            // Nút Thêm Địa Chỉ Mới trong modal chọn địa chỉ (mở modal thêm địa chỉ)
            const addAddressBtn = document.querySelector('#addressModal button.flex.items-center');
            if (addAddressBtn && addAddressModal && addressModal) {
                addAddressBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    addressModal.classList.add('hidden');
                    addAddressModal.classList.remove('hidden');
                });
            }
            // Nút Trở Lại trong modal thêm địa chỉ mới
            const addAddressBack = document.getElementById('addAddressBack');
            if (addAddressBack && addAddressModal && addressModal) {
                addAddressBack.addEventListener('click', function() {
                    addAddressModal.classList.add('hidden');
                    addressModal.classList.remove('hidden');
                });
            }
        });
    </script>
@endsection

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

        /* === MODAL CSS FIXES === */
        #addressModal, #addAddressModal {
            position: fixed !important;
            z-index: 99999 !important; /* Ensure modal is on top */
            pointer-events: auto !important; /* Ensure modal container is interactive */
        }
        
        /* Ensure modal content and all buttons are clickable */
        .modal-content,
        #addressModal button, 
        #changeAddressBtn, 
        #addFirstAddressBtn {
            pointer-events: auto !important;
            cursor: pointer !important;
        }

        /* Ensure modal is properly shown when flex class is added */
        #addressModal.flex {
            display: flex !important;
        }


    </style>
@endsection

@section('content')
    <style>
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

        .::after {
            content: "*";
            color: #dc3545;
            margin-left: 0.25rem;
        }
    </style>
<div class="max-w-[1240px] mx-auto w-full">

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-2">Thanh Toán</h1>
        <p class="text-gray-500 mb-8">Hoàn tất đơn hàng của bạn</p>

        <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
            @csrf
            @foreach(request('cart_item_ids', []) as $cartItemId)
                <input type="hidden" name="cart_item_ids[]" value="{{ $cartItemId }}">
            @endforeach
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- ========= CỘT BÊN TRÁI ========= -->
                <div class="lg:col-span-2">
            @if($userAddresses && $userAddresses->count() > 0)
                @auth
                    <!-- Address Component -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-200" id="address-component">
                        <h2 class="text-xl font-bold mb-1">Địa chỉ giao hàng</h2>
                        <p class="text-sm text-gray-500 mb-4">Chọn hoặc thêm địa chỉ nhận hàng của bạn.</p>
                        <hr class="mb-4">
                        <!-- View 1: Hiển thị địa chỉ được chọn -->
                        <div id="address-summary-view">
                            @php
                                $selectedAddressId = request('address_id');
                                $selectedAddress = $selectedAddressId ? $userAddresses->firstWhere('id', $selectedAddressId) : null;
                                if (!$selectedAddress) {
                                    $selectedAddress = $userAddresses->where('is_default', true)->first() ?? $userAddresses->first();
                                }
                            @endphp
                            <div class="border border-orange-300 bg-orange-50 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start">
                                        <span class="text-orange-500 mr-4 mt-1"><i class="fas fa-map-marker-alt"></i></span>
                                        <div>
                                            <div class="font-semibold">
                                                <span id="summary-name">{{ $selectedAddress->full_name ?? auth()->user()->full_name }}</span>
                                                <span class="font-normal" id="summary-phone">({{ $selectedAddress->phone_number }})</span>
                                                <span id="summary-default-badge" class="ml-2 border border-orange-500 text-orange-500 px-2 py-0.5 rounded text-xs font-medium bg-white {{ $selectedAddress->is_default ? '' : 'hidden' }}">Mặc Định</span>
                                            </div>
                                            <div class="text-sm text-gray-700" id="summary-address">{{ $selectedAddress->full_address }}</div>
                                        </div>
                                    </div>
                                    <button type="button" id="show-address-list-btn" class="ml-4 text-blue-600 hover:underline font-medium text-sm px-3 py-1 rounded flex-shrink-0">
                                        Thay đổi
                                    </button>
                                </div>
                            </div>
                            <!-- Hidden fields for submission -->
                            <input type="hidden" id="hidden_address_id" name="address_id" value="{{ $selectedAddress->id }}">
                            <input type="hidden" id="hidden_full_name" name="full_name" value="{{ $selectedAddress->full_name ?? auth()->user()->full_name }}">
                            <input type="hidden" id="hidden_phone" name="phone" value="{{ $selectedAddress->phone_number }}">
                            <input type="hidden" id="hidden_email" name="email" value="{{ auth()->user()->email }}">
                            <input type="hidden" id="hidden_address" name="address" value="{{ $selectedAddress->address_line }}">
                            <input type="hidden" id="hidden_city" name="city" value="{{ $selectedAddress->city }}">
                            <input type="hidden" id="hidden_district" name="district" value="{{ $selectedAddress->district }}">
                            <input type="hidden" id="hidden_ward" name="ward" value="{{ $selectedAddress->ward }}">
                        </div>
                        <!-- View 2: Danh sách địa chỉ để chọn -->
                        <div id="address-list-view" class="hidden">
                            <div class="space-y-3 max-h-72 overflow-y-auto pr-2" id="address-list-container">
                                @foreach($userAddresses as $address)
                                    <label for="address-radio-{{ $address->id }}" class="address-option-label flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-all"
                                        data-address-id="{{ $address->id }}"
                                        data-full-name="{{ $address->full_name ?? auth()->user()->full_name }}"
                                        data-phone-number="{{ $address->phone_number }}"
                                        data-full-address="{{ $address->full_address }}"
                                        data-is-default="{{ $address->is_default ? 'true' : 'false' }}"
                                        data-city="{{ $address->city }}"
                                        data-district="{{ $address->district }}"
                                        data-ward="{{ $address->ward }}"
                                        data-address-line="{{ $address->address_line }}"
                                        data-latitude="{{ $address->latitude }}"
                                        data-longitude="{{ $address->longitude }}">
                                        <span class="text-gray-400 mr-4 mt-1"><i class="fas fa-map-marker-alt"></i></span>
                                        <div class="flex-grow">
                                            <div class="font-semibold">
                                                <span>{{ $address->full_name ?? auth()->user()->full_name }}</span>
                                                <span class="font-normal">({{ $address->phone_number }})</span>
                                                @if($address->is_default)
                                                    <span class="ml-2 border border-orange-500 text-orange-500 px-2 py-0.5 rounded text-xs font-medium bg-white">Mặc Định</span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-700">{{ $address->full_address }}</div>
                                            <!-- NEW: Distance and warning placeholders -->
                                            <div class="address-meta mt-2 text-sm">
                                                <span class="distance-info text-blue-600 font-medium hidden"></span>
                                                <span class="warning-info text-red-600 font-medium hidden"></span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end gap-2 ml-4">
                                            <input type="radio" name="selected_address_option" id="address-radio-{{ $address->id }}" value="{{ $address->id }}" class="form-radio h-5 w-5 text-orange-600" {{ ($selectedAddress->id ?? -1) == $address->id ? 'checked' : '' }}>
                                            <!-- Edit button for this address -->
                                            <button type="button" class="edit-address-btn text-blue-600 hover:text-blue-800 text-xs font-medium" 
                                                    data-address-id="{{ $address->id }}"
                                                    data-full-name="{{ $address->full_name ?? auth()->user()->full_name }}"
                                                    data-phone-number="{{ $address->phone_number }}"
                                                    data-city="{{ $address->city }}"
                                                    data-district="{{ $address->district }}"
                                                    data-ward="{{ $address->ward }}"
                                                    data-address-line="{{ $address->address_line }}"
                                                    data-latitude="{{ $address->latitude }}"
                                                    data-longitude="{{ $address->longitude }}"
                                                    data-is-default="{{ $address->is_default ? '1' : '0' }}">
                                                <i class="fas fa-edit mr-1"></i>Sửa
                                            </button>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <div class="mt-4 pt-4 border-t">
                                <button type="button" id="openAddAddressModalBtn" class="text-orange-600 border border-orange-500 rounded px-3 py-1 text-sm font-medium hover:bg-orange-50">
                                    <i class="fas fa-plus mr-2"></i>Thêm địa chỉ mới
                                </button>
                                <div class="flex justify-end gap-3 mt-3">
                                    <button type="button" id="cancel-change-address-btn" class="px-5 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 text-sm">Hủy</button>
                                    <button type="button" id="confirm-address-btn" class="px-5 py-2 rounded bg-orange-500 text-white font-semibold hover:bg-orange-600 text-sm">Xác nhận</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth
            @endif
            @if(!$userAddresses || $userAddresses->count() === 0)
                @php
                    $user = Auth::user();
                @endphp
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Thông Tin Giao Hàng</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                            <input type="text" id="full_name" name="full_name" 
                                class="w-full px-3 py-2 border rounded-lg @error('full_name') border-red-500 @enderror"
                                value="{{ old('full_name', $user ? $user->full_name : '') }}"
                                placeholder="Nhập họ và tên">
                            @error('full_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                            <input type="tel" id="phone" name="phone" 
                                class="w-full px-3 py-2 border rounded-lg @error('phone') border-red-500 @enderror"
                                value="{{ old('phone', $user ? $user->phone : '') }}"
                                placeholder="Nhập số điện thoại">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" 
                                class="w-full px-3 py-2 border rounded-lg @error('email') border-red-500 @enderror"
                                value="{{ old('email', $user ? $user->email : '') }}"
                                placeholder="Nhập email">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Tỉnh/Thành phố</label>
                            <select id="city" name="city" class="w-full px-3 py-2 border rounded-lg @error('city') border-red-500 @enderror">
                                <option value="Hà Nội" selected>Hà Nội</option>
                            </select>
                            @error('city')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="district" class="block text-sm font-medium text-gray-700 mb-1">Quận/Huyện</label>
                            <select id="district" name="district" class="w-full px-3 py-2 border rounded-lg @error('district') border-red-500 @enderror">
                                <option value="">-- Chọn Quận/Huyện --</option>
                            </select>
                            @error('district')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-1">
                            <label for="ward" class="block text-sm font-medium text-gray-700 mb-1">Xã/Phường</label>
                            <select id="ward" name="ward" class="w-full px-3 py-2 border rounded-lg @error('ward') border-red-500 @enderror">
                                <option value="">-- Chọn Xã/Phường --</option>
                            </select>
                            @error('ward')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-1 relative">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Số nhà, đường</label>
                            <input type="text" id="address" name="address" 
                                class="w-full px-3 py-2 border rounded-lg @error('address') border-red-500 @enderror"
                                value="{{ old('address') }}" autocomplete="off"
                                placeholder="Nhập số nhà, tên đường">
                            <div class="text-xs text-gray-500 mt-1">Nhập địa chỉ sau khi chọn Quận/Huyện và Phường/Xã</div>
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- MAP PICKER -->
                        <div class="md:col-span-2 relative mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chọn vị trí trên bản đồ <span class="text-xs text-gray-500">(bắt buộc để giao hàng)</span></label>
                            <div id="checkout-map" style="height: 300px; border-radius: 8px; margin-bottom: 8px; z-index: 1000; position: relative;"></div>
                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                            <div class="text-xs text-gray-500">Nhấn vào bản đồ để chọn vị trí giao hàng chính xác.</div>
                            @error('latitude')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            @error('longitude')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

                    <!-- Order Notes -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Ghi Chú Đơn Hàng</h2>
                        <div>
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3"
                                placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn.">{{ old('notes') }}</textarea>
                            <div class="text-xs text-gray-500 mt-1">
                                Bạn có thể thêm ghi chú đặc biệt cho đơn hàng như thời gian giao hàng mong muốn, hướng dẫn tìm địa chỉ, v.v.
                            </div>
                        </div>
                    </div>

                    <!-- NEW: Payment Methods Moved Here -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Phương thức thanh toán</h2>
                        <div id="payment-method-options" class="space-y-4">
                            <label class="payment-option flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-all">
                                <input type="radio" name="payment_method" value="cod" class="h-5 w-5 text-orange-500" checked>
                                <div class="ml-4 flex-grow">
                                    <span class="block font-medium">Thanh toán khi nhận hàng (COD)</span>
                                    <span class="text-sm text-gray-500">Trả tiền mặt trực tiếp cho tài xế.</span>
                                </div>
                                <i class="fas fa-money-bill-wave text-green-500 text-2xl"></i>
                            </label>
                            <label class="payment-option flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-all">
                                <input type="radio" name="payment_method" value="vnpay" class="h-5 w-5 text-orange-500">
                                <div class="ml-4 flex-grow">
                                    <span class="block font-medium">Thanh toán qua VNPAY</span>
                                    <span class="text-sm text-gray-500">An toàn & nhanh chóng qua cổng VNPAY.</span>
                                </div>
                                <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Icon-VNPAY-QR.png" alt="VNPAY" class="h-8 object-contain">
                            </label>
                            @auth
                            <label class="payment-option flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-all">
                                <input type="radio" name="payment_method" value="balance" class="h-5 w-5 text-orange-500">
                                <div class="ml-4 flex-grow">
                                    <span class="block font-medium">Thanh toán bằng số dư</span>
                                    <span class="text-sm text-gray-500">Sử dụng số dư: <strong>{{ number_format(Auth::user()->balance ?? 0) }}đ</strong></span>
                                </div>
                                 <i class="fas fa-wallet text-purple-500 text-2xl"></i>
                            </label>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- ========= CỘT BÊN PHẢI ========= -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                        
                        <!-- NEW: Voucher Form -->
                        <div class="mb-6">
                             <h2 class="text-xl font-bold mb-4">Mã giảm giá</h2>
                             <div id="coupon-area">
                                <div class="flex">
                                <input type="text" name="coupon_code" id="coupon-code-input" class="form-control flex-grow rounded-r-none focus:ring-0" placeholder="Nhập mã giảm giá..." {{ session('coupon_code') ? 'disabled' : '' }} value="{{ session('coupon_code') }}">
                                <button type="button" id="apply-coupon-btn" class="bg-orange-500 text-white px-5 rounded-l-none rounded-r-lg hover:bg-orange-600 font-semibold text-sm transition-colors border border-orange-500" {{ session('coupon_code') ? 'disabled' : '' }}>
                                    Áp dụng
                                </button>
                            </div>
                                <div id="coupon-feedback" class="mt-2 text-sm"></div>
                             </div>
                        </div>

                        <h2 class="text-xl font-bold mb-4">Đơn Hàng Của Bạn</h2>
                        <!-- Order Items -->
                        <div class="space-y-4">
                            @foreach ($cartItems as $item)
                                @php
                                    $isCombo = isset($item->combo) && $item->combo;
                                    $combo = $isCombo ? $item->combo : null;
                                    $variant = !$isCombo ? ($item->variant ?? null) : null;
                                    $product = $variant ? ($variant->product ?? null) : null;
                                @endphp
                                <div class="flex items-center gap-4">
                                    <div class="relative h-16 w-16 flex-shrink-0 rounded overflow-hidden">
                                        @if ($isCombo && $combo && $combo->image)
                                            <img src="{{ Storage::disk('s3')->url($combo->image) }}"
                                                 alt="{{ $combo->name }}"
                                                 class="object-cover w-full h-full">
                                        @elseif ($product && $product->primary_image)
                                            <img src="{{ Storage::disk('s3')->url($product->primary_image->img) }}"
                                                 alt="{{ $product->name }}"
                                                 class="object-cover w-full h-full">
                                        @else
                                            <div class="h-full w-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-sm truncate">
                                            {{ $isCombo ? $combo->name : ($product ? $product->name : 'Sản phẩm không xác định') }}
                                        </h3>
                                        <p class="text-xs text-gray-500">
                                            @if ($isCombo)
                                                Combo
                                            @elseif ($variant && $variant->variantValues && $variant->variantValues->count() > 0)
                                                @foreach ($variant->variantValues as $variantValue)
                                                    {{ $variantValue->attribute->name }}: {{ $variantValue->value }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            @elseif ($variant && $variant->variant_description)
                                                {{ $variant->variant_description }}
                                            @else
                                                &mdash;
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
                                                if ($isCombo) {
                                                    $itemPrice = $item->final_price ?? ($combo->price ?? 0);
                                                } else {
                                                    $itemPrice = $item->final_price ?? ($variant ? ($variant->price ?? 0) : 0) + ($item->toppings ? $item->toppings->sum('price') : 0);
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
                        <!-- Order Totals -->
                        <div class="space-y-4 my-6">
                            @php
                                // Subtotal đã được tính sẵn trong controller ($subtotal)
                                $discount = session('coupon_discount_amount', 0);
                                // Phí vận chuyển ban đầu sẽ được tính bằng JS
                                $shipping = 0; 
                                $total = $subtotal + $shipping - $discount;
                            @endphp

                            <div class="flex justify-between">
                                <span class="text-gray-600">Tạm tính</span>
                                <span id="subtotal-display" data-value="{{ $subtotal }}">{{ number_format($subtotal) }}đ</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Phí giao hàng</span>
                                <span id="shipping-fee-display" data-value="{{ $shipping }}">Đang tính...</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 flex items-center">
                                    Thời gian giao hàng dự kiến
                                    <span id="delivery-time-indicator" class="ml-1 text-xs text-green-500 opacity-0 transition-opacity duration-300">●</span>
                                </span>
                                <span id="delivery-time-display" class="text-sm font-medium text-orange-600">Nhập địa chỉ để tính toán</span>
                            </div>

                            <div id="coupon-discount-row" class="flex justify-between text-green-600 font-semibold {{ $discount > 0 ? '' : 'hidden' }}">
                                <span>Giảm giá (voucher)</span>
                                <span id="coupon-discount-display" data-value="{{ $discount }}">-{{ number_format($discount) }}đ</span>
                            </div>


                            <hr class="border-t border-gray-200">

                            <div class="flex justify-between font-bold text-lg">
                                <span>Tổng cộng</span>
                                <span id="total-amount-display">{{ number_format($total) }}đ</span>
                            </div>
                        </div>

                        <!-- Payment Methods (MOVED) -->

                        <!-- Terms and Place Order Button -->
                        <div class="space-y-4 mt-6">
                            <div class="flex items-center">
                                <input type="checkbox" id="terms" name="terms" class="h-4 w-4 text-orange-500"
                                    >
                                <label for="terms" class="ml-2 text-sm text-gray-600">
                                    Tôi đã đọc và đồng ý với <a href="/terms"
                                        class="text-orange-500 hover:underline">điều khoản và điều kiện</a> của website
                                </label>
                            </div>
                            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 rounded-lg">
                                Đặt Hàng
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />
<script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>
<script src='https://npmcdn.com/@turf/turf/turf.min.js'></script>
<style>
.custom-marker {
    cursor: pointer;
}

.custom-marker:hover {
    transform: scale(1.1);
}


@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(249, 115, 22, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(249, 115, 22, 0);
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}

@keyframes slideIn {
    from { 
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
    }
    to { 
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}
</style>
<script>
// --- MAPBOX CONFIGURATION ---
mapboxgl.accessToken = '{{ config('services.mapbox.access_token') }}';

// --- MAP INITIALIZATION ---
let checkoutMap = null;
let mapMarker = null;

function initializeMap() {
    const mapContainer = document.getElementById('checkout-map');
    if (!mapContainer) return;

    try {
        checkoutMap = new mapboxgl.Map({
            container: 'checkout-map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [105.8194, 21.0227], // Hanoi center
            zoom: 13
        });

        checkoutMap.on('load', function() {
            console.log('Map loaded successfully');
            
            // Add default marker at map center
            const defaultLng = 105.8194;
            const defaultLat = 21.0227;
            
            // Create custom marker element
            const markerElement = document.createElement('div');
            markerElement.className = 'custom-marker';
            markerElement.innerHTML = `
                <div class="relative">
                    <div class="w-8 h-8 bg-orange-500 rounded-full border-4  shadow-lg flex items-center justify-center marker-pulse">
                        <i class="fas fa-map-marker-alt text-white text-lg"></i>
                    </div>
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-orange-500"></div>
                </div>
            `;
            
            // Add default marker with drag functionality
            mapMarker = new mapboxgl.Marker({
                element: markerElement,
                draggable: true
            })
            .setLngLat([defaultLng, defaultLat])
            .setPopup(new mapboxgl.Popup().setHTML(`
                <div class="text-sm">
                    <p class="font-semibold">Vị trí mặc định</p>
                    <p>Kéo thả hoặc nhấn vào bản đồ để chọn vị trí khác</p>
                </div>
            `))
            .addTo(checkoutMap);
            
            // Handle marker drag event
            mapMarker.on('dragend', function() {
                const lngLat = mapMarker.getLngLat();
                document.getElementById('latitude').value = lngLat.lat;
                document.getElementById('longitude').value = lngLat.lng;
                
                // Check delivery distance for guest checkout
                checkGuestDeliveryDistance(lngLat.lat, lngLat.lng);
                
                // Update popup with new coordinates
                mapMarker.getPopup().setHTML(`
                    <div class="text-sm">
                        <p class="font-semibold">Vị trí đã chọn</p>
                        <p>Lat: ${lngLat.lat.toFixed(6)}</p>
                        <p>Lng: ${lngLat.lng.toFixed(6)}</p>
                        <p class="text-xs text-gray-500 mt-1">Có thể kéo thả để điều chỉnh</p>
                    </div>
                `);
                
                console.log('Marker dragged to:', { lat: lngLat.lat, lng: lngLat.lng });
            });
            
            // Update default coordinates
            document.getElementById('latitude').value = defaultLat;
            document.getElementById('longitude').value = defaultLng;
            
            // Check delivery distance for guest checkout with default coordinates
            checkGuestDeliveryDistance(defaultLat, defaultLng);
        });

        // Add click event to get coordinates
        checkoutMap.on('click', function(e) {
            const lng = e.lngLat.lng;
            const lat = e.lngLat.lat;
            
            // Update hidden inputs
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            // Check delivery distance for guest checkout
            checkGuestDeliveryDistance(lat, lng);
            
            // Remove existing marker
            if (mapMarker) {
                mapMarker.remove();
            }
            
            // Create custom marker element
            const markerElement = document.createElement('div');
            markerElement.className = 'custom-marker';
            markerElement.innerHTML = `
                 <div class="relative">
                     <div class="w-8 h-8 bg-orange-500 rounded-full border-4 border-white shadow-lg flex items-center justify-center marker-pulse">
                         <i class="fas fa-map-marker-alt text-white text-lg"></i>
                     </div>
                     <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-orange-500"></div>
                 </div>
             `;
            
            // Add new marker with custom icon and drag functionality
            mapMarker = new mapboxgl.Marker({
                element: markerElement,
                draggable: true
            })
            .setLngLat([lng, lat])
            .setPopup(new mapboxgl.Popup().setHTML(`
                <div class="text-sm">
                    <p class="font-semibold">Vị trí đã chọn</p>
                    <p>Lat: ${lat.toFixed(6)}</p>
                    <p>Lng: ${lng.toFixed(6)}</p>
                    <p class="text-xs text-gray-500 mt-1">Có thể kéo thả để điều chỉnh</p>
                </div>
            `))
            .addTo(checkoutMap);
            
            // Handle marker drag event
            mapMarker.on('dragend', function() {
                const lngLat = mapMarker.getLngLat();
                document.getElementById('latitude').value = lngLat.lat;
                document.getElementById('longitude').value = lngLat.lng;
                
                // Check delivery distance for guest checkout
                checkGuestDeliveryDistance(lngLat.lat, lngLat.lng);
                
                // Update popup with new coordinates
                mapMarker.getPopup().setHTML(`
                    <div class="text-sm">
                        <p class="font-semibold">Vị trí đã chọn</p>
                        <p>Lat: ${lngLat.lat.toFixed(6)}</p>
                        <p>Lng: ${lngLat.lng.toFixed(6)}</p>
                        <p class="text-xs text-gray-500 mt-1">Có thể kéo thả để điều chỉnh</p>
                    </div>
                `);
                
                console.log('Marker dragged to:', { lat: lngLat.lat, lng: lngLat.lng });
            });
            
            console.log('Selected coordinates:', { lat, lng });
        });

        checkoutMap.on('error', function(e) {
            // Silent error handling
        });

    } catch (error) {
        document.getElementById('checkout-map').innerHTML = '<div class="flex items-center justify-center h-full bg-gray-100 text-gray-500">Không thể tải bản đồ. Vui lòng thử lại.</div>';
    }
}

// --- ĐỊA CHỈ: TỰ ĐỘNG LOAD QUẬN/HUYỆN, XÃ/PHƯỜNG ---
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map first
    initializeMap();
    const citySelect = document.getElementById('city');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');

    // Chỉ hiển thị Hà Nội
    citySelect.innerHTML = '<option value="Hà Nội" data-code="1" selected>Hà Nội</option>';

    // Khi chọn tỉnh/thành phố, load quận/huyện
    citySelect.addEventListener('change', function() {
        districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
        wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
        
        // Sử dụng file JSON thay vì API
        fetch('/data/hanoi-districts.json')
            .then(res => res.json())
            .then(data => {
                if (!data.districts || !Array.isArray(data.districts)) {
                    console.error('Invalid districts data format');
                    return;
                }
                
                data.districts.forEach(d => {
                    districtSelect.innerHTML += `<option value="${d.name}" data-code="${d.code}">${d.name}</option>`;
                });
            })
            .catch(error => {
                console.error('Error loading districts:', error);
            });
    });

    // Trigger change event after setting up the listener to load districts
    citySelect.dispatchEvent(new Event('change'));

    // Khi chọn quận/huyện, load xã/phường và geocode
    districtSelect.addEventListener('change', function() {
        const districtName = this.value;
        wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
        if (!districtName) return;
        
        // Sử dụng file JSON thay vì API
        fetch('/data/hanoi-districts.json')
            .then(res => res.json())
            .then(data => {
                if (!data.districts || !Array.isArray(data.districts)) {
                    console.error('Invalid districts data format');
                    return;
                }
                
                const district = data.districts.find(d => d.name === districtName);
                if (!district || !district.wards || !Array.isArray(district.wards)) {
                    console.error('District not found or has no wards');
                    return;
                }
                
                district.wards.forEach(w => {
                    wardSelect.innerHTML += `<option value="${w.name}">${w.name}</option>`;
                });
            })
            .catch(error => {
                console.error('Error loading wards:', error);
            });
        
        // Also trigger geocoding after district selection
        geocodeAndUpdateMap();
    });

    // Function to geocode address and update map
    function geocodeAndUpdateMap() {
        const city = citySelect.value;
        const district = districtSelect.value;
        const ward = wardSelect.value;
        const address = document.getElementById('address').value;
        
        if (!district || !ward) return;
        
        let fullAddress = '';
        if (address) fullAddress += address + ', ';
        fullAddress += ward + ', ' + district + ', ' + city;
        
        // Use Mapbox Geocoding API
        const geocodeUrl = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(fullAddress)}.json?access_token=${mapboxgl.accessToken}&country=VN&limit=1`;
        
        fetch(geocodeUrl)
            .then(response => response.json())
            .then(data => {
                if (data.features && data.features.length > 0) {
                    const [lng, lat] = data.features[0].center;
                    
                    // Update map center
                    if (checkoutMap) {
                        checkoutMap.flyTo({
                            center: [lng, lat],
                            zoom: 15,
                            duration: 1000
                        });
                        
                        // Remove existing marker
                        if (mapMarker) {
                            mapMarker.remove();
                        }
                        
                        // Create custom marker element
                        const markerElement = document.createElement('div');
                        markerElement.className = 'custom-marker';
                        markerElement.innerHTML = `
                             <div class="relative">
                                 <div class="w-8 h-8 bg-orange-500 rounded-full border-4 border-white shadow-lg flex items-center justify-center marker-pulse">
                                     <i class="fas fa-map-marker-alt text-white text-lg"></i>
                                 </div>
                                 <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-orange-500"></div>
                             </div>
                         `;
                        
                        // Add new marker with custom icon and drag functionality
                        mapMarker = new mapboxgl.Marker({
                            element: markerElement,
                            draggable: true
                        })
                        .setLngLat([lng, lat])
                        .setPopup(new mapboxgl.Popup().setHTML(`
                            <div class="text-sm">
                                <p class="font-semibold">Địa chỉ tìm thấy</p>
                                <p>${fullAddress}</p>
                                <p class="text-xs text-gray-500 mt-1">Kéo thả hoặc nhấn vào bản đồ để điều chỉnh</p>
                            </div>
                        `))
                        .addTo(checkoutMap);
                        
                        // Handle marker drag event
                        mapMarker.on('dragend', function() {
                            const lngLat = mapMarker.getLngLat();
                            document.getElementById('latitude').value = lngLat.lat;
                            document.getElementById('longitude').value = lngLat.lng;
                            
                            // Check delivery distance for guest checkout
                            checkGuestDeliveryDistance(lngLat.lat, lngLat.lng);
                            
                            // Update popup with new coordinates
                            mapMarker.getPopup().setHTML(`
                                <div class="text-sm">
                                    <p class="font-semibold">Vị trí đã điều chỉnh</p>
                                    <p>Lat: ${lngLat.lat.toFixed(6)}</p>
                                    <p>Lng: ${lngLat.lng.toFixed(6)}</p>
                                    <p class="text-xs text-gray-500 mt-1">Có thể kéo thả để điều chỉnh</p>
                                </div>
                            `);
                            
                            console.log('Marker dragged to:', { lat: lngLat.lat, lng: lngLat.lng });
                        });
                        
                        // Update coordinates
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                        
                        // Check delivery distance for guest checkout
                        checkGuestDeliveryDistance(lat, lng);
                    }
                }
            })
            .catch(error => {
                // Silent error handling
            });
    }
    
    // Add event listeners for address changes (district already handled above)
    wardSelect.addEventListener('change', geocodeAndUpdateMap);
    document.getElementById('address').addEventListener('blur', geocodeAndUpdateMap);
});
        // --- SHIPPING CONFIG ---
        const shippingConfig = {
            freeShippingThreshold: {{ \App\Models\GeneralSetting::getFreeShippingThreshold() }},
            baseFee: {{ \App\Models\GeneralSetting::getShippingBaseFee() }},
            feePerKm: {{ \App\Models\GeneralSetting::getShippingFeePerKm() }},
            maxDeliveryDistance: {{ \App\Models\GeneralSetting::getMaxDeliveryDistance() }}
        };

        // --- DELIVERY TIME CONFIG ---
        const deliveryConfig = {
            defaultPreparationTime: {{ $deliveryConfig['defaultPreparationTime'] }},
            averageSpeedKmh: {{ $deliveryConfig['averageSpeedKmh'] }},
            bufferTime: {{ $deliveryConfig['bufferTime'] }}
        };
        
        // Cart items preparation times
        const itemPreparationTimes = [
            @foreach($cartItems as $item)
                @if($item->variant && $item->variant->product)
                    {{ $item->variant->product->preparation_time_minutes ?? 0 }},
                @elseif($item->combo)
                    {{ $item->combo->preparation_time_minutes ?? 0 }},
                @else
                    0,
                @endif
            @endforeach
        ];

        // Debug: Log shipping config values
        console.log('Shipping Config:', shippingConfig);
        console.log('Delivery Config:', deliveryConfig);
        
        // Global variables for delivery time tracking
        let currentDeliveryDistance = 0;
        let deliveryTimeInterval = null;
        
        // Khởi động timer cập nhật thời gian giao hàng mỗi phút
        document.addEventListener('DOMContentLoaded', function() {
            startDeliveryTimeUpdater();
            console.log('Delivery time updater started - will update every minute');
        });
        
        // Dừng timer khi người dùng rời khỏi trang để tránh memory leak
        window.addEventListener('beforeunload', function() {
            stopDeliveryTimeUpdater();
        });
        
        // Dừng timer khi trang bị ẩn (tab switching)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopDeliveryTimeUpdater();
            } else {
                startDeliveryTimeUpdater();
                // Cập nhật ngay lập tức khi quay lại tab
                if (currentDeliveryDistance > 0) {
                    updateDeliveryTimeDisplay(currentDeliveryDistance, true); // Show indicator when returning to tab
                }
            }
        });

        /**
         * Tính thời gian giao hàng dự kiến
         * @param {number} distance - Khoảng cách tính bằng km
         * @returns {number} Thời gian dự kiến tính bằng phút
         */
        function calculateEstimatedDeliveryTime(distance) {
            // 1. Tìm thời gian chuẩn bị lâu nhất
            let maxPreparationTime = Math.max(...itemPreparationTimes);
            
            // Nếu không có sản phẩm nào có thời gian, sử dụng giá trị mặc định
            if (maxPreparationTime === 0 || maxPreparationTime === -Infinity) {
                maxPreparationTime = deliveryConfig.defaultPreparationTime;
            }
            
            // 2. Tính thời gian di chuyển
            let travelTime = 0;
            if (deliveryConfig.averageSpeedKmh > 0 && distance > 0) {
                travelTime = (distance / deliveryConfig.averageSpeedKmh) * 60; // Đổi sang phút
            }
            
            // 3. Cộng tổng với thời gian dự phòng và làm tròn lên
            const totalMinutes = maxPreparationTime + travelTime + deliveryConfig.bufferTime;
            
            return Math.ceil(totalMinutes);
        }
        
        /**
         * Cập nhật hiển thị thời gian giao hàng dự kiến
         * @param {number} distance - Khoảng cách tính bằng km
         * @param {boolean} showIndicator - Có hiển thị indicator cập nhật không
         */
        function updateDeliveryTimeDisplay(distance, showIndicator = false) {
            const deliveryTimeEl = document.getElementById('delivery-time-display');
            const indicatorEl = document.getElementById('delivery-time-indicator');
            
            if (distance <= 0) {
                deliveryTimeEl.textContent = 'Nhập địa chỉ để tính toán';
                deliveryTimeEl.classList.remove('text-red-500');
                deliveryTimeEl.classList.add('text-orange-600');
                return;
            }
            
            if (distance > shippingConfig.maxDeliveryDistance) {
                deliveryTimeEl.textContent = 'Không khả dụng';
                deliveryTimeEl.classList.remove('text-orange-600');
                deliveryTimeEl.classList.add('text-red-500');
                return;
            }
            
            // Tính thời gian giao hàng dự kiến
            const estimatedMinutes = calculateEstimatedDeliveryTime(distance);
            const now = new Date();
            const estimatedTime = new Date(now.getTime() + estimatedMinutes * 60000);
            
            // Format thời gian hiển thị
            const startTime = estimatedTime.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
            const endTime = new Date(estimatedTime.getTime() + 15 * 60000).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
            
            // Kiểm tra xem có phải hôm nay không
            const isToday = now.toDateString() === estimatedTime.toDateString();
            const dateDisplay = isToday ? 'Hôm nay' : estimatedTime.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
            
            deliveryTimeEl.textContent = `${dateDisplay}, ${startTime} - ${endTime}`;
            deliveryTimeEl.classList.remove('text-red-500');
            deliveryTimeEl.classList.add('text-orange-600');
            
            // Hiển thị indicator khi cập nhật tự động
            if (showIndicator && indicatorEl) {
                indicatorEl.classList.remove('opacity-0');
                indicatorEl.classList.add('opacity-100');
                
                // Ẩn indicator sau 2 giây
                setTimeout(() => {
                    indicatorEl.classList.remove('opacity-100');
                    indicatorEl.classList.add('opacity-0');
                }, 2000);
            }
        }
        
        /**
         * Khởi tạo timer cập nhật thời gian giao hàng mỗi phút
         */
        function startDeliveryTimeUpdater() {
            // Clear existing interval if any
            if (deliveryTimeInterval) {
                clearInterval(deliveryTimeInterval);
            }
            
            // Update every minute (60000ms)
            deliveryTimeInterval = setInterval(() => {
                if (currentDeliveryDistance > 0 && currentDeliveryDistance <= shippingConfig.maxDeliveryDistance) {
                    updateDeliveryTimeDisplay(currentDeliveryDistance, true); // Show indicator for auto-update
                    console.log('Delivery time updated automatically at', new Date().toLocaleTimeString());
                }
            }, 60000);
        }
        
        /**
         * Dừng timer cập nhật thời gian giao hàng
         */
        function stopDeliveryTimeUpdater() {
            if (deliveryTimeInterval) {
                clearInterval(deliveryTimeInterval);
                deliveryTimeInterval = null;
                console.log('Delivery time updater stopped');
            }
        }
        
        /**
         * Tính phí vận chuyển ở phía client.
         * @param {number} distance - Khoảng cách tính bằng km.
         * @param {number} subtotal - Tổng phụ của đơn hàng.
         * @returns {number} Phí vận chuyển. Trả về -1 nếu ngoài vùng phục vụ.
         */
        function calculateShippingFee(distance, subtotal) {
            // Kiểm tra giới hạn khoảng cách giao hàng
            if (distance > shippingConfig.maxDeliveryDistance) {
                return -1; // Ngoài vùng phục vụ
            }
            
            if (subtotal >= shippingConfig.freeShippingThreshold) {
                return 0;
            }
            if (distance < 0) {
                return -1; // Đánh dấu không hợp lệ
            }
            if (distance === 0) {
                return 0;
            }
            if (distance <= 1) {
                return shippingConfig.baseFee;
            }
            const additionalKms = Math.ceil(distance) - 1;
            return shippingConfig.baseFee + (additionalKms * shippingConfig.feePerKm);
        }
        
        function formatCurrency(number) {
            if (isNaN(number)) return '0đ';
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number);
        }

        /**
         * Kiểm tra khoảng cách giao hàng cho khách vãng lai
         * @param {number} lat - Vĩ độ
         * @param {number} lng - Kinh độ
         */
        function checkGuestDeliveryDistance(lat, lng) {
            // Kiểm tra nếu không phải guest checkout
            if ({{ Auth::check() ? 'true' : 'false' }}) {
                return;
            }

            const branchLat = {{ $currentBranch->latitude ?? 'null' }};
            const branchLng = {{ $currentBranch->longitude ?? 'null' }};

            if (!branchLat || !branchLng || typeof turf === 'undefined') {
                console.warn('Branch coordinates or turf.js not available');
                return;
            }

            // Tính khoảng cách
            const branchPoint = turf.point([branchLng, branchLat]);
            const guestPoint = turf.point([lng, lat]);
            const distance = turf.distance(branchPoint, guestPoint);

            // Debug: Log distance calculation
            console.log('=== GUEST DISTANCE CHECK ===');
            console.log('Branch coordinates:', { lat: branchLat, lng: branchLng });
            console.log('Guest coordinates:', { lat, lng });
            console.log('Calculated distance:', distance.toFixed(2) + 'km');

            // Cập nhật UI phí vận chuyển
            const subtotal = parseFloat(document.getElementById('subtotal-display')?.dataset?.value || 0);
            const shippingFee = calculateShippingFee(distance, subtotal);
            const shippingFeeEl = document.getElementById('shipping-fee-display');
            const deliveryTimeEl = document.getElementById('delivery-time-display');

            if (shippingFeeEl) {
                if (shippingFee >= 0) {
                    shippingFeeEl.dataset.value = shippingFee;
                    shippingFeeEl.textContent = shippingFee > 0 ? formatCurrency(shippingFee) : 'Miễn phí';
                    shippingFeeEl.classList.remove('text-red-500', 'font-semibold');
                    
                    // Cập nhật thời gian giao hàng khi có địa chỉ hợp lệ
                    updateDeliveryTimeDisplay(distance);
                } else {
                    shippingFeeEl.dataset.value = 0;
                    shippingFeeEl.textContent = 'Ngoài vùng phục vụ';
                    shippingFeeEl.classList.add('text-red-500', 'font-semibold');
                    
                    // Cập nhật thời gian giao hàng khi ngoài vùng phục vụ
                    if (deliveryTimeEl) {
                        deliveryTimeEl.textContent = 'Không khả dụng';
                        deliveryTimeEl.classList.remove('text-orange-600');
                        deliveryTimeEl.classList.add('text-red-500');
                    }
                }
            }

            // Xóa cảnh báo cũ nếu có
            const existingWarning = document.getElementById('guest-distance-warning');
            if (existingWarning) {
                existingWarning.remove();
            }
            
            // Hiển thị cảnh báo nếu ngoài vùng phục vụ
            if (shippingFee < 0) {
                const warningHtml = `
                    <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md" id="guest-distance-warning">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <div>
                                <p class="font-bold">Địa chỉ nằm ngoài vùng phục vụ</p>
                                <p class="text-sm">Khoảng cách: ${distance.toFixed(1)}km (tối đa: ${shippingConfig.maxDeliveryDistance}km)</p>
                                <p class="text-sm">Vui lòng chọn địa chỉ khác hoặc liên hệ cửa hàng.</p>
                            </div>
                        </div>
                    </div>`;
                
                // Thêm cảnh báo vào form
                const guestForm = document.querySelector('.guest-checkout-form');
                if (guestForm) {
                    guestForm.insertAdjacentHTML('beforeend', warningHtml);
                }
                
                // Vô hiệu hóa nút đặt hàng
                const placeOrderBtn = document.querySelector('#checkout-form button[type="submit"]');
                if (placeOrderBtn) {
                    placeOrderBtn.disabled = true;
                    placeOrderBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    placeOrderBtn.title = 'Địa chỉ ngoài vùng phục vụ';
                }
            } else {
                // Kích hoạt lại nút đặt hàng
                const placeOrderBtn = document.querySelector('#checkout-form button[type="submit"]');
                if (placeOrderBtn) {
                    placeOrderBtn.disabled = false;
                    placeOrderBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    placeOrderBtn.title = '';
                }
            }

            // Cập nhật tổng tiền
            const totalEl = document.getElementById('total-amount-display');
            if (totalEl) {
                const couponDiscountEl = document.getElementById('coupon-discount-display');
                const couponDiscount = parseFloat(couponDiscountEl ? couponDiscountEl.dataset.value : 0);
                const finalShippingFee = shippingFee >= 0 ? shippingFee : 0;
                const total = Math.max(0, subtotal + finalShippingFee - couponDiscount);
                totalEl.textContent = formatCurrency(total);
            }

            console.log(`Guest delivery distance: ${distance.toFixed(1)}km, shipping fee: ${shippingFee >= 0 ? formatCurrency(shippingFee) : 'Error'}`);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // --- ELEMENT SELECTORS ---
            const addressComponent = document.getElementById('address-component');
            if (!addressComponent) return;

            const summaryView = document.getElementById('address-summary-view');
            const listView = document.getElementById('address-list-view');
            const showListBtn = document.getElementById('show-address-list-btn');
            const confirmBtn = document.getElementById('confirm-address-btn');
            const cancelChangeBtn = document.getElementById('cancel-change-address-btn');
            const placeOrderBtn = document.querySelector('#checkout-form button[type="submit"]');

            const branchLat = {{ $currentBranch->latitude ?? 'null' }};
            const branchLng = {{ $currentBranch->longitude ?? 'null' }};
            const summaryAddressId = document.getElementById('hidden_address_id')?.value;
            const addressLabels = document.querySelectorAll('.address-option-label');

            // --- UI UPDATE FUNCTIONS ---
            
            function updateTotalsDisplay() {
                const subtotal = parseFloat(document.getElementById('subtotal-display').dataset.value || 0);
                const shippingFee = parseFloat(document.getElementById('shipping-fee-display').dataset.value || 0);
                const couponDiscountEl = document.getElementById('coupon-discount-display');
                const couponDiscount = parseFloat(couponDiscountEl ? couponDiscountEl.dataset.value : 0);

                const total = Math.max(0, subtotal + shippingFee - couponDiscount);
                document.getElementById('total-amount-display').textContent = formatCurrency(total);
            }

            function updateShippingFeeUI(distance) {
                const subtotal = parseFloat(document.getElementById('subtotal-display').dataset.value || 0);
                const shippingFee = calculateShippingFee(distance, subtotal);
                const shippingFeeEl = document.getElementById('shipping-fee-display');

                // Cập nhật biến global và thời gian giao hàng
                currentDeliveryDistance = distance;
                updateDeliveryTimeDisplay(distance);

                if (shippingFee >= 0) {
                    shippingFeeEl.dataset.value = shippingFee;
                    shippingFeeEl.textContent = shippingFee > 0 ? formatCurrency(shippingFee) : 'Miễn phí';
                    shippingFeeEl.classList.remove('text-red-500', 'font-semibold');
                } else {
                    shippingFeeEl.dataset.value = 0;
                    shippingFeeEl.textContent = 'Ngoài vùng';
                    shippingFeeEl.classList.add('text-red-500', 'font-semibold');
                }
                updateTotalsDisplay();
            }

            function displaySummaryWarning() {
                const summaryViewDiv = document.querySelector('#address-summary-view .border');
                const existingWarning = document.getElementById('summary-warning');
                if (summaryViewDiv && !existingWarning) {
                    const warningHtml = `
                        <div class="mt-2 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-md" id="summary-warning">
                            <p class="font-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Địa chỉ nằm ngoài vùng phục vụ</p>
                            <p>Khoảng cách vượt quá giới hạn ${shippingConfig.maxDeliveryDistance}km. Vui lòng chọn một địa chỉ khác để tiếp tục.</p>
                        </div>`;
                    summaryViewDiv.insertAdjacentHTML('beforeend', warningHtml);
                }
            }

            function removeSummaryWarning() {
                document.getElementById('summary-warning')?.remove();
            }

            function toggleCheckoutButton(enable, title = '') {
                if (placeOrderBtn) {
                    placeOrderBtn.disabled = !enable;
                    placeOrderBtn.classList.toggle('opacity-50', !enable);
                    placeOrderBtn.classList.toggle('cursor-not-allowed', !enable);
                    placeOrderBtn.title = title;
                }
            }

            // --- CORE LOGIC ---
            
            function calculateAllAddressDistances(branchPoint) {
                addressLabels.forEach(label => {
                    const distanceInfoEl = label.querySelector('.distance-info');
                    if (distanceInfoEl && distanceInfoEl.dataset.distance) return; // Skip if already calculated

                    const lat = parseFloat(label.dataset.latitude);
                    const lng = parseFloat(label.dataset.longitude);
                    const radioInput = label.querySelector('input[type="radio"]');
                    const warningInfoEl = label.querySelector('.warning-info');

                    if (isNaN(lat) || isNaN(lng) || !radioInput || !distanceInfoEl || !warningInfoEl) return;

                    const addressPoint = turf.point([lng, lat]);
                    const distance = turf.distance(branchPoint, addressPoint);

                    distanceInfoEl.textContent = `📍 ${distance.toFixed(1)}km từ chi nhánh`;
                    distanceInfoEl.dataset.distance = distance;
                    distanceInfoEl.classList.remove('hidden');

                    // Kiểm tra nếu địa chỉ ngoài vùng phục vụ
                    if (distance > shippingConfig.maxDeliveryDistance) {
                        // Hiển thị cảnh báo cho địa chỉ ngoài vùng
                        warningInfoEl.innerHTML = `
                            <div class="text-red-600 text-xs mt-1">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Ngoài vùng phục vụ (tối đa: ${shippingConfig.maxDeliveryDistance}km)
                            </div>`;
                        warningInfoEl.classList.remove('hidden');
                        
                        // Vô hiệu hóa địa chỉ này
                        radioInput.disabled = true;
                        label.classList.add('opacity-60', 'cursor-not-allowed');
                        label.classList.remove('hover:bg-gray-50');
                        label.style.borderColor = '#fca5a5'; // red-300
                    } else {
                        // Địa chỉ hợp lệ
                        radioInput.disabled = false;
                        label.classList.remove('opacity-60', 'cursor-not-allowed');
                        label.classList.add('hover:bg-gray-50');
                        warningInfoEl.classList.add('hidden');
                        label.style.borderColor = '';
                    }
                });
            }

            function initializeCheckoutPage() {
                if (typeof turf === 'undefined' || !branchLat || !branchLng) {
                    document.getElementById('shipping-fee-display').textContent = 'Lỗi cấu hình';
                    document.getElementById('delivery-time-display').textContent = 'Lỗi cấu hình';
                    toggleCheckoutButton(false, 'Lỗi cấu hình chi nhánh, không thể đặt hàng.');
                    return;
                }
                
                const branchPoint = turf.point([branchLng, branchLat]);
                
                // 1. Calculate for the selected address IMMEDIATELY
                if (summaryAddressId) {
                    const summaryLabel = document.querySelector(`.address-option-label[data-address-id="${summaryAddressId}"]`);
                    if (summaryLabel) {
                        const lat = parseFloat(summaryLabel.dataset.latitude);
                        const lng = parseFloat(summaryLabel.dataset.longitude);
                        if (!isNaN(lat) && !isNaN(lng)) {
                            const point = turf.point([lng, lat]);
                            const distance = turf.distance(branchPoint, point);
                            
                            const distanceEl = summaryLabel.querySelector('.distance-info');
                            if(distanceEl) distanceEl.dataset.distance = distance; // Store for later
                            
                            updateShippingFeeUI(distance);
                            
                            // Kiểm tra và hiển thị cảnh báo nếu ngoài vùng phục vụ
                            if (distance > shippingConfig.maxDeliveryDistance) {
                                displaySummaryWarning();
                                toggleCheckoutButton(false, 'Địa chỉ ngoài vùng phục vụ');
                            } else {
                                removeSummaryWarning();
                                toggleCheckoutButton(true);
                            }
                        }
                    } else if (addressLabels.length === 0) {
                        updateShippingFeeUI(-1); // No addresses, so invalid
                        document.getElementById('delivery-time-display').textContent = 'Nhập địa chỉ để tính toán';
                    }
                } else if ({{ Auth::check() ? 'true' : 'false' }} && addressLabels.length === 0) {
                    updateShippingFeeUI(-1); // Logged in but no addresses
                    document.getElementById('delivery-time-display').textContent = 'Nhập địa chỉ để tính toán';
                } else if (!{{ Auth::check() ? 'true' : 'false' }}) {
                    document.getElementById('shipping-fee-display').textContent = 'Nhập địa chỉ';
                    document.getElementById('delivery-time-display').textContent = 'Nhập địa chỉ để tính toán';
                }

                // 2. Defer calculation for the rest of the addresses
                setTimeout(() => calculateAllAddressDistances(branchPoint), 50);
            }

            // --- EVENT LISTENERS ---

            if (showListBtn) {
                showListBtn.addEventListener('click', () => {
                    summaryView.classList.add('hidden');
                    listView.classList.remove('hidden');
                });
            }

            if (cancelChangeBtn) {
                cancelChangeBtn.addEventListener('click', () => {
                    listView.classList.add('hidden');
                    summaryView.classList.remove('hidden');
                });
            }

            if (confirmBtn) {
                confirmBtn.addEventListener('click', () => {
                    const selectedRadio = document.querySelector('input[name="selected_address_option"]:checked');
                    if (!selectedRadio) {
                        showToast('Vui lòng chọn một địa chỉ.', 'error'); return;
                    }

                    const selectedLabel = selectedRadio.closest('.address-option-label');
                    const data = selectedLabel.dataset;
                    const distance = parseFloat(selectedLabel.querySelector('.distance-info').dataset.distance);

                    // Update summary view
                    document.getElementById('summary-name').textContent = data.fullName;
                    document.getElementById('summary-phone').textContent = `(${data.phoneNumber})`;
                    document.getElementById('summary-address').textContent = data.fullAddress;
                    document.getElementById('summary-default-badge').classList.toggle('hidden', data.isDefault !== 'true');
                    
                    // Update hidden fields
                    document.getElementById('hidden_address_id').value = data.addressId;
                    document.getElementById('hidden_full_name').value = data.fullName;
                    document.getElementById('hidden_phone').value = data.phoneNumber;
                    document.getElementById('hidden_address').value = data.addressLine;
                    document.getElementById('hidden_city').value = data.city;
                    document.getElementById('hidden_district').value = data.district;
                    document.getElementById('hidden_ward').value = data.ward;
                    
                    updateShippingFeeUI(distance); // Recalculate and display the fee
                    
                    // Kiểm tra và cập nhật UI state dựa trên khoảng cách
                    if (distance > shippingConfig.maxDeliveryDistance) {
                        displaySummaryWarning();
                        toggleCheckoutButton(false, 'Địa chỉ ngoài vùng phục vụ');
                    } else {
                        removeSummaryWarning();
                        toggleCheckoutButton(true);
                    }

                    listView.classList.add('hidden');
                    summaryView.classList.remove('hidden');
                    showToast('Đã cập nhật địa chỉ giao hàng.');
                });
            }

            addressLabels.forEach(label => {
                if (label.querySelector('input').checked && !label.querySelector('input').disabled) {
                    label.classList.add('border-orange-300', 'bg-orange-50');
                }
                label.addEventListener('click', (e) => {
                    const radio = label.querySelector('input[type="radio"]');
                    
                    // Remove border styling from all addresses
                    addressLabels.forEach(l => {
                        l.classList.remove('border-orange-300', 'bg-orange-50');
                        l.classList.add('border-gray-200');
                    });
                    
                    // Add border styling to selected address
                    label.classList.remove('border-gray-200');
                    label.classList.add('border-orange-300', 'bg-orange-50');
                });
            });

            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                let bgColor = 'bg-green-500';
                if (type === 'error') bgColor = 'bg-red-500';
                
                toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow-lg text-white ${bgColor}`;
                toast.textContent = message; document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }
            
            // Start The Process
            initializeCheckoutPage();
        });
    </script>

    <!-- Add Address Modal -->
    <script>
        // Simple form validation and utilities
        document.addEventListener('DOMContentLoaded', function() {
            // Simple toast notification function
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                let bgColor = 'bg-green-500';
                if (type === 'error') bgColor = 'bg-red-500';
                
                toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow-lg text-white z-50 ${bgColor}`;
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            }
            
            // Make showToast available globally
            window.showToast = showToast;
        });
    </script>

    <script>
        // Toast notification function
        function showToast(message, type = 'success') {
            // Create toast element
            const toast = document.createElement('div');
            let bgColor = 'bg-green-500';
            if (type === 'error') bgColor = 'bg-red-500';
            
            toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${bgColor}`;
            toast.textContent = message;
            
            // Add to page
            document.body.appendChild(toast);
            
            // Remove after 3 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 3000);
        }
    </script>

    <!-- Add Address Modal -->
    <div id="addAddressModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center" style="z-index: 9999 !important;">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold" id="modalTitle">Thêm địa chỉ mới</h3>
                    <button type="button" id="closeAddAddressModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                
                <form id="addAddressForm">
                    @csrf
                    <input type="hidden" id="edit_address_id" name="address_id" value="">
                    <input type="hidden" id="form_method" name="_method" value="POST">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="new_recipient_name" class="block text-sm font-medium text-gray-700 mb-1">Họ và tên người nhận <span class="text-red-500">*</span></label>
                            <input type="text" id="new_recipient_name" name="recipient_name" 
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('recipient_name') border-red-500 @enderror"
                                placeholder="Nhập họ và tên" value="{{ old('recipient_name') }}">
                            @error('recipient_name')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                            <div class="text-red-500 text-xs mt-1 hidden" id="error_recipient_name"></div>
                        </div>
                        <div>
                            <label for="new_phone_number" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="tel" id="new_phone_number" name="phone_number" 
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('phone_number') border-red-500 @enderror"
                                placeholder="Nhập số điện thoại" value="{{ old('phone_number') }}">
                            @error('phone_number')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                            <div class="text-red-500 text-xs mt-1 hidden" id="error_phone_number"></div>
                        </div>
                        <div>
                            <label for="new_city" class="block text-sm font-medium text-gray-700 mb-1">Tỉnh/Thành phố <span class="text-red-500">*</span></label>
                            <select id="new_city" name="city" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('city') border-red-500 @enderror">
                                <option value="Hà Nội" selected>Hà Nội</option>
                            </select>
                            @error('city')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                            <div class="text-red-500 text-xs mt-1 hidden" id="error_city"></div>
                        </div>
                        <div>
                            <label for="new_district" class="block text-sm font-medium text-gray-700 mb-1">Quận/Huyện <span class="text-red-500">*</span></label>
                            <select id="new_district" name="district" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('district') border-red-500 @enderror">
                                <option value="">-- Chọn Quận/Huyện --</option>
                            </select>
                            @error('district')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                            <div class="text-red-500 text-xs mt-1 hidden" id="error_district"></div>
                        </div>
                        <div class="md:col-span-1">
                            <label for="new_ward" class="block text-sm font-medium text-gray-700 mb-1">Xã/Phường <span class="text-red-500">*</span></label>
                            <select id="new_ward" name="ward" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('ward') border-red-500 @enderror">
                                <option value="">-- Chọn Xã/Phường --</option>
                            </select>
                            @error('ward')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                            <div class="text-red-500 text-xs mt-1 hidden" id="error_ward"></div>
                        </div>
                        <div class="md:col-span-1 relative">
                            <label for="new_address_line" class="block text-sm font-medium text-gray-700 mb-1">Số nhà, đường <span class="text-red-500">*</span></label>
                            <input type="text" id="new_address_line" name="address_line" 
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('address_line') border-red-500 @enderror"
                                placeholder="Nhập số nhà, tên đường" value="{{ old('address_line') }}" autocomplete="off">
                            <div class="text-xs text-gray-500 mt-1">Nhập địa chỉ sau khi chọn Quận/Huyện và Phường/Xã</div>
                            @error('address_line')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                            <div class="text-red-500 text-xs mt-1 hidden" id="error_address_line"></div>
                        </div>
                        
                        <!-- Map Picker for New Address -->
                        <div class="md:col-span-2 relative mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chọn vị trí trên bản đồ</label>
                            <div id="new-address-map" style="height: 300px; border-radius: 8px; margin-bottom: 8px; z-index: 1000; position: relative;"></div>
                            <input type="hidden" id="new_latitude" name="latitude">
                            <input type="hidden" id="new_longitude" name="longitude">
                            <div class="text-xs text-gray-500">Nhấn vào bản đồ để chọn vị trí giao hàng chính xác (tùy chọn).</div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" id="new_is_default" name="is_default" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Đặt làm địa chỉ mặc định</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                        <button type="button" id="cancelAddAddress" class="px-5 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">
                            Hủy
                        </button>
                        <button type="submit" id="saveAddressBtn" class="px-5 py-2 rounded bg-orange-500 text-white font-semibold hover:bg-orange-600">
                            <span id="saveAddressText">Lưu địa chỉ</span>
                            <span id="saveAddressLoading" class="hidden">
                                <i class="fas fa-spinner fa-spin mr-2"></i><span id="saveLoadingText">Đang lưu...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Address Modal JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addAddressModal = document.getElementById('addAddressModal');
            const openAddAddressModalBtn = document.getElementById('openAddAddressModalBtn');
            const closeAddAddressModal = document.getElementById('closeAddAddressModal');
            const cancelAddAddress = document.getElementById('cancelAddAddress');
            const addAddressForm = document.getElementById('addAddressForm');
            const saveAddressBtn = document.getElementById('saveAddressBtn');
            const saveAddressText = document.getElementById('saveAddressText');
            const saveAddressLoading = document.getElementById('saveAddressLoading');
            
            let newAddressMap = null;
            let newAddressMarker = null;
            
            // Initialize new address form dropdowns
            function initializeNewAddressDropdowns() {
                const newCitySelect = document.getElementById('new_city');
                const newDistrictSelect = document.getElementById('new_district');
                const newWardSelect = document.getElementById('new_ward');
                
                // Chỉ hiển thị Hà Nội
                newCitySelect.innerHTML = '<option value="Hà Nội" data-code="1" selected>Hà Nội</option>';

                // When city changes, load districts
                newCitySelect.addEventListener('change', function() {
                    newDistrictSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                    newWardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
                    
                    // Sử dụng file JSON thay vì API
                    fetch('/data/hanoi-districts.json')
                        .then(res => res.json())
                        .then(data => {
                            if (!data.districts || !Array.isArray(data.districts)) {
                                console.error('Invalid districts data format');
                                return;
                            }
                            
                            data.districts.forEach(d => {
                                newDistrictSelect.innerHTML += `<option value="${d.name}" data-code="${d.code}">${d.name}</option>`;
                            });
                        })
                        .catch(error => {
                            console.error('Error loading districts:', error);
                        });
                });
                
                // Load districts initially for Hà Nội (only if not in edit mode)
                const editAddressIdInput = document.getElementById('edit_address_id');
                if (!editAddressIdInput || !editAddressIdInput.value) {
                    // Only trigger change event if we're in add mode
                    newCitySelect.dispatchEvent(new Event('change'));
                }

                // When district changes, load wards and geocode
                newDistrictSelect.addEventListener('change', function() {
                    const districtName = this.value;
                    newWardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
                    if (!districtName) return;
                    
                    // Sử dụng file JSON thay vì API
                    fetch('/data/hanoi-districts.json')
                        .then(res => res.json())
                        .then(data => {
                            if (!data.districts || !Array.isArray(data.districts)) {
                                console.error('Invalid districts data format');
                                return;
                            }
                            
                            const district = data.districts.find(d => d.name === districtName);
                            if (!district || !district.wards || !Array.isArray(district.wards)) {
                                console.error('District not found or has no wards');
                                return;
                            }
                            
                            district.wards.forEach(w => {
                                newWardSelect.innerHTML += `<option value="${w.name}">${w.name}</option>`;
                            });
                        })
                        .catch(error => {
                            console.error('Error loading wards:', error);
                        });
                    
                    // Also trigger geocoding after district selection
                    geocodeNewAddress();
                });
                newWardSelect.addEventListener('change', geocodeNewAddress);
                document.getElementById('new_address_line').addEventListener('blur', geocodeNewAddress);
            }
            
            // Function to update map location with marker
            function updateMapLocation(lat, lng) {
                if (!newAddressMap) {
                    console.warn('Map not initialized yet');
                    return;
                }
                
                try {
                    // Center map on the address location
                    newAddressMap.setCenter([lng, lat]);
                    newAddressMap.setZoom(16);
                    
                    // Remove existing marker if any
                    if (newAddressMarker) {
                        newAddressMarker.remove();
                    }
                    
                    // Create new marker
                    const markerElement = document.createElement('div');
                    markerElement.className = 'custom-marker';
                    markerElement.style.cssText = `
                        background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23ef4444"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>');
                        width: 30px;
                        height: 30px;
                        background-size: 100%;
                        cursor: pointer;
                    `;
                    
                    newAddressMarker = new mapboxgl.Marker({
                        element: markerElement,
                        draggable: true
                    })
                    .setLngLat([lng, lat])
                    .setPopup(new mapboxgl.Popup().setHTML(`
                        <div class="text-sm">
                            <p class="font-semibold">Vị trí hiện tại</p>
                            <p>Lat: ${lat.toFixed(6)}</p>
                            <p>Lng: ${lng.toFixed(6)}</p>
                            <p class="text-xs text-gray-500 mt-1">Có thể kéo thả để điều chỉnh</p>
                        </div>
                    `))
                    .addTo(newAddressMap);
                    
                    // Handle marker drag event
                    newAddressMarker.on('dragend', function() {
                        const lngLat = newAddressMarker.getLngLat();
                        const latitudeInput = document.getElementById('new_latitude');
                        const longitudeInput = document.getElementById('new_longitude');
                        
                        if (latitudeInput) latitudeInput.value = lngLat.lat;
                        if (longitudeInput) longitudeInput.value = lngLat.lng;
                        
                        // Update popup with new coordinates
                        newAddressMarker.getPopup().setHTML(`
                            <div class="text-sm">
                                <p class="font-semibold">Vị trí đã điều chỉnh</p>
                                <p>Lat: ${lngLat.lat.toFixed(6)}</p>
                                <p>Lng: ${lngLat.lng.toFixed(6)}</p>
                                <p class="text-xs text-gray-500 mt-1">Có thể kéo thả để điều chỉnh</p>
                            </div>
                        `);
                    });
                    
                    // Update hidden fields
                    const latitudeInput = document.getElementById('new_latitude');
                    const longitudeInput = document.getElementById('new_longitude');
                    if (latitudeInput) latitudeInput.value = lat;
                    if (longitudeInput) longitudeInput.value = lng;
                } catch (error) {
                    console.error('Error updating map location:', error);
                }
            }
            
            // Function to load districts and wards for edit mode
            async function loadDistrictsAndWards(targetDistrict, targetWard) {
                const newDistrictSelect = document.getElementById('new_district');
                const newWardSelect = document.getElementById('new_ward');
                
                try {
                    // Load districts for Hà Nội from JSON file
                    const districtResponse = await fetch('/data/hanoi-districts.json');
                    const districtData = await districtResponse.json();
                    
                    // Populate districts
                    newDistrictSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                    if (!districtData.districts || !Array.isArray(districtData.districts)) {
                        console.error('Invalid districts data format');
                        return;
                    }
                    
                    districtData.districts.forEach(d => {
                        const selected = d.name === targetDistrict ? 'selected' : '';
                        newDistrictSelect.innerHTML += `<option value="${d.name}" data-code="${d.code}" ${selected}>${d.name}</option>`;
                    });
                    
                    // If we have a target district, load its wards
                    if (targetDistrict) {
                        const selectedDistrict = districtData.districts.find(d => d.name === targetDistrict);
                        if (selectedDistrict) {
                            // Populate wards from the same JSON data
                            newWardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
                            if (!selectedDistrict.wards || !Array.isArray(selectedDistrict.wards)) {
                                console.error('District has no wards');
                                return;
                            }
                            
                            selectedDistrict.wards.forEach(w => {
                                const selected = w.name === targetWard ? 'selected' : '';
                                newWardSelect.innerHTML += `<option value="${w.name}" ${selected}>${w.name}</option>`;
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error loading districts and wards:', error);
                }
            }
            
            // Geocode new address and update map
            function geocodeNewAddress() {
                const city = document.getElementById('new_city').value;
                const district = document.getElementById('new_district').value;
                const ward = document.getElementById('new_ward').value;
                const address = document.getElementById('new_address_line').value;
                
                if (!district || !ward) return;
                
                let fullAddress = '';
                if (address) fullAddress += address + ', ';
                fullAddress += ward + ', ' + district + ', ' + city;
                
                // Use Mapbox Geocoding API
                const geocodeUrl = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(fullAddress)}.json?access_token=${mapboxgl.accessToken}&country=VN&limit=1`;
                
                fetch(geocodeUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.features && data.features.length > 0) {
                            const [lng, lat] = data.features[0].center;
                            
                            // Update map center
                            if (newAddressMap) {
                                newAddressMap.flyTo({
                                    center: [lng, lat],
                                    zoom: 15,
                                    duration: 1000
                                });
                                
                                // Remove existing marker
                                if (newAddressMarker) {
                                    newAddressMarker.remove();
                                }
                                
                                // Create custom marker element
                                const markerElement = document.createElement('div');
                                markerElement.className = 'custom-marker';
                                markerElement.innerHTML = `
                                     <div class="relative">
                                         <div class="w-8 h-8 bg-orange-500 rounded-full border-4 border-white shadow-lg flex items-center justify-center marker-pulse">
                                             <i class="fas fa-map-marker-alt text-white text-lg"></i>
                                         </div>
                                         <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-orange-500"></div>
                                     </div>
                                 `;
                                
                                // Add new marker with custom icon and drag functionality
                                newAddressMarker = new mapboxgl.Marker({
                                    element: markerElement,
                                    draggable: true
                                })
                                .setLngLat([lng, lat])
                                .setPopup(new mapboxgl.Popup().setHTML(`
                                    <div class="text-sm">
                                        <p class="font-semibold">Địa chỉ tìm thấy</p>
                                        <p>${fullAddress}</p>
                                        <p class="text-xs text-gray-500 mt-1">Kéo thả hoặc nhấn vào bản đồ để điều chỉnh</p>
                                    </div>
                                `))
                                .addTo(newAddressMap);
                                
                                // Handle marker drag event
                                newAddressMarker.on('dragend', function() {
                                    const lngLat = newAddressMarker.getLngLat();
                                    document.getElementById('new_latitude').value = lngLat.lat;
                                    document.getElementById('new_longitude').value = lngLat.lng;
                                    
                                    // Update popup with new coordinates
                                    newAddressMarker.getPopup().setHTML(`
                                        <div class="text-sm">
                                            <p class="font-semibold">Vị trí đã điều chỉnh</p>
                                            <p>Lat: ${lngLat.lat.toFixed(6)}</p>
                                            <p>Lng: ${lngLat.lng.toFixed(6)}</p>
                                            <p class="text-xs text-gray-500 mt-1">Có thể kéo thả để điều chỉnh</p>
                                        </div>
                                    `);
                                });
                                
                                // Update coordinates
                                document.getElementById('new_latitude').value = lat;
                                document.getElementById('new_longitude').value = lng;
                                
                                // Hide coordinate error
                                document.getElementById('error_coordinates').classList.add('hidden');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Geocoding error:', error);
                    });
            }
            
            // Check if new address is within service area
            
            // Initialize map for new address
            function initializeNewAddressMap() {
                if (newAddressMap) {
                    newAddressMap.remove();
                }
                
                // Default to Hanoi center
                const defaultLat = 21.0285;
                const defaultLng = 105.8542;
                
                newAddressMap = new mapboxgl.Map({
                    container: 'new-address-map',
                    style: 'mapbox://styles/mapbox/streets-v11',
                    center: [defaultLng, defaultLat],
                    zoom: 13
                });
                
                // Add click event to map
                newAddressMap.on('click', function(e) {
                    const lat = e.lngLat.lat;
                    const lng = e.lngLat.lng;
                    
                    // Remove existing marker
                    if (newAddressMarker) {
                        newAddressMarker.remove();
                    }
                    
                    // Create custom marker element
                    const markerElement = document.createElement('div');
                    markerElement.className = 'custom-marker';
                    markerElement.innerHTML = `
                         <div class="relative">
                             <div class="w-8 h-8 bg-orange-500 rounded-full border-4 border-white shadow-lg flex items-center justify-center marker-pulse">
                                 <i class="fas fa-map-marker-alt text-white text-lg"></i>
                             </div>
                             <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-orange-500"></div>
                         </div>
                     `;
                    
                    // Add new marker
                    newAddressMarker = new mapboxgl.Marker({
                        element: markerElement,
                        draggable: true
                    })
                    .setLngLat([lng, lat])
                    .setPopup(new mapboxgl.Popup().setHTML(`
                        <div class="text-sm">
                            <p class="font-semibold">Vị trí đã chọn</p>
                            <p>Lat: ${lat.toFixed(6)}</p>
                            <p>Lng: ${lng.toFixed(6)}</p>
                            <p class="text-xs text-gray-500 mt-1">Có thể kéo thả để điều chỉnh</p>
                        </div>
                    `))
                    .addTo(newAddressMap);
                    
                    // Handle marker drag event
                    newAddressMarker.on('dragend', function() {
                        const lngLat = newAddressMarker.getLngLat();
                        document.getElementById('new_latitude').value = lngLat.lat;
                        document.getElementById('new_longitude').value = lngLat.lng;
                        
                        // Update popup with new coordinates
                        newAddressMarker.getPopup().setHTML(`
                            <div class="text-sm">
                                <p class="font-semibold">Vị trí đã điều chỉnh</p>
                                <p>Lat: ${lngLat.lat.toFixed(6)}</p>
                                <p>Lng: ${lngLat.lng.toFixed(6)}</p>
                                <p class="text-xs text-gray-500 mt-1">Có thể kéo thả để điều chỉnh</p>
                            </div>
                        `);
                        
                        // Validate delivery distance after dragging
                        validateDeliveryDistanceByCoordinates(lngLat.lat, lngLat.lng);
                    });
                    
                    // Update hidden fields
                    document.getElementById('new_latitude').value = lat;
                    document.getElementById('new_longitude').value = lng;
                    
                    // Validate delivery distance for the selected location
                    validateDeliveryDistanceByCoordinates(lat, lng);
                });
            }
            
            // Open modal for adding new address
            if (openAddAddressModalBtn) {
                openAddAddressModalBtn.addEventListener('click', function() {
                    openAddressModal('add');
                });
            }

            // Enhanced function to open modal in add or edit mode with animations
            function openAddressModal(mode, addressData = null) {
                const modalTitle = document.getElementById('modalTitle');
                const saveAddressText = document.getElementById('saveAddressText');
                const saveLoadingText = document.getElementById('saveLoadingText');
                const editAddressIdInput = document.getElementById('edit_address_id');
                const formMethodInput = document.getElementById('form_method');
                const modal = addAddressModal;
                const modalContent = modal.querySelector('.bg-white');
                
                // Clear previous validation states
                const inputs = modal.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.classList.remove('border-red-500', 'border-green-500', 'ring-2', 'ring-orange-200');
                    const icons = input.parentNode.querySelectorAll('.success-icon, .error-icon');
                    icons.forEach(icon => icon.style.display = 'none');
                });
                
                // Show modal with enhanced animations first
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.style.animation = 'fadeIn 0.3s ease-out';
                modalContent.style.animation = 'slideIn 0.3s ease-out';
                
                // Initialize map and dropdowns first
                setTimeout(() => {
                    initializeNewAddressMap();
                    initializeNewAddressDropdowns();
                    
                    // Then handle mode-specific logic
                    if (mode === 'edit' && addressData) {
                        // Edit mode
                        modalTitle.textContent = 'Chỉnh sửa địa chỉ';
                        saveAddressText.textContent = 'Cập nhật địa chỉ';
                        saveLoadingText.textContent = 'Đang cập nhật...';
                        editAddressIdInput.value = addressData.id;
                        formMethodInput.value = 'PUT';
                        
                        // Populate basic form fields immediately
                        const recipientNameInput = document.getElementById('new_recipient_name');
                        const phoneNumberInput = document.getElementById('new_phone_number');
                        const addressLineInput = document.getElementById('new_address_line');
                        const latitudeInput = document.getElementById('new_latitude');
                        const longitudeInput = document.getElementById('new_longitude');
                        const isDefaultInput = document.getElementById('new_is_default');
                        
                        if (recipientNameInput) recipientNameInput.value = addressData.fullName || '';
                        if (phoneNumberInput) phoneNumberInput.value = addressData.phoneNumber || '';
                        if (addressLineInput) addressLineInput.value = addressData.addressLine || '';
                        if (latitudeInput) latitudeInput.value = addressData.latitude || '';
                        if (longitudeInput) longitudeInput.value = addressData.longitude || '';
                        if (isDefaultInput) isDefaultInput.checked = addressData.isDefault === '1' || addressData.isDefault === 'true';
                        
                        // Set dropdowns using the new loadDistrictsAndWards function
                        if (addressData.district || addressData.ward) {
                            loadDistrictsAndWards(addressData.district, addressData.ward).then(() => {
                                // Update map with coordinates after dropdowns are loaded
                                if (addressData.latitude && addressData.longitude) {
                                    setTimeout(() => {
                                        updateMapLocation(parseFloat(addressData.latitude), parseFloat(addressData.longitude));
                                    }, 500);
                                }
                            }).catch(error => {
                                console.error('Error loading districts and wards:', error);
                                
                                // Still try to update map even if dropdowns fail
                                if (addressData.latitude && addressData.longitude) {
                                    setTimeout(() => {
                                        updateMapLocation(parseFloat(addressData.latitude), parseFloat(addressData.longitude));
                                    }, 500);
                                }
                            });
                        } else {
                            // No district/ward data, just update map
                            if (addressData.latitude && addressData.longitude) {
                                setTimeout(() => {
                                    updateMapLocation(parseFloat(addressData.latitude), parseFloat(addressData.longitude));
                                }, 500);
                            }
                        }
                        
                    } else {
                        // Add mode - Reset everything completely
                        modalTitle.textContent = 'Thêm địa chỉ mới';
                        saveAddressText.textContent = 'Lưu địa chỉ';
                        saveLoadingText.textContent = 'Đang lưu...';
                        editAddressIdInput.value = '';
                        formMethodInput.value = 'POST';
                        
                        // Reset form completely
                        addAddressForm.reset();
                        
                        // Clear all hidden fields
                        document.getElementById('new_latitude').value = '';
                        document.getElementById('new_longitude').value = '';
                        
                        // Reset dropdowns to default state
                        const citySelect = document.getElementById('new_city');
                        const districtSelect = document.getElementById('new_district');
                        const wardSelect = document.getElementById('new_ward');
                        
                        if (districtSelect) {
                            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                        }
                        if (wardSelect) {
                            wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
                        }
                        
                        // Trigger district loading for Hà Nội
                        if (citySelect) {
                            setTimeout(() => {
                                citySelect.dispatchEvent(new Event('change'));
                            }, 100);
                        }
                        
                        // Clear validation states
                        const inputs = modal.querySelectorAll('input, select');
                        inputs.forEach(input => {
                            input.classList.remove('border-red-500', 'border-green-500', 'ring-2', 'ring-orange-200');
                            const icons = input.parentNode.querySelectorAll('.success-icon, .error-icon');
                            icons.forEach(icon => icon.style.display = 'none');
                        });
                        
                        // Reset map to default location (Hanoi center)
                        setTimeout(() => {
                            if (newAddressMap) {
                                const defaultLat = 21.0285;
                                const defaultLng = 105.8542;
                                newAddressMap.setView([defaultLat, defaultLng], 13);
                                
                                if (newAddressMarker) {
                                    newAddressMarker.remove();
                                    newAddressMarker = null;
                                }
                            }
                        }, 500);
                    }
                    
                    // Focus on first input for better UX
                    setTimeout(() => {
                        const firstInput = document.getElementById('new_recipient_name');
                        if (firstInput) {
                            firstInput.focus();
                            firstInput.select();
                        }
                    }, 100);
                    
                }, 300);
            }

            // Field validation function
            function validateField(fieldName, value) {
                switch (fieldName) {
                    case 'recipient_name':
                        return value.trim().length >= 2 && value.trim().length <= 50;
                    case 'phone_number':
                        const phoneRegex = /^(0[3|5|7|8|9])+([0-9]{8})$/;
                        return phoneRegex.test(value.trim());
                    case 'address_line':
                        return value.trim().length >= 5 && value.trim().length <= 200;
                    default:
                        return true;
                }
            }

            // Enhanced real-time validation for input fields
            const inputFields = ['recipient_name', 'phone_number', 'address_line'];
            inputFields.forEach(fieldName => {
                const inputElement = document.getElementById(`new_${fieldName}`);
                if (inputElement) {
                    // Add input event listener for real-time validation
                    inputElement.addEventListener('input', function() {
                        const isValid = validateField(fieldName, this.value);
                        
                        // Visual feedback for validation (only border colors, no icons)
                        if (this.value.trim() !== '') {
                            if (isValid) {
                                this.classList.remove('border-red-500');
                                this.classList.add('border-green-500');
                            } else {
                                this.classList.remove('border-green-500');
                                this.classList.add('border-red-500');
                            }
                        } else {
                            // Reset styling for empty fields
                            this.classList.remove('border-red-500', 'border-green-500');
                        }
                    });
                    
                    // Add focus and blur effects
                    inputElement.addEventListener('focus', function() {
                        this.classList.add('ring-2', 'ring-orange-200');
                    });
                    
                    inputElement.addEventListener('blur', function() {
                        this.classList.remove('ring-2', 'ring-orange-200');
                    });
                }
            });

            // Enhanced edit address functionality with loading state
            document.addEventListener('click', function(e) {
                // Check if clicked element or its parent has edit-address-btn class
                let editButton = null;
                if (e.target.classList.contains('edit-address-btn')) {
                    editButton = e.target;
                } else if (e.target.closest('.edit-address-btn')) {
                    editButton = e.target.closest('.edit-address-btn');
                }
                
                if (editButton) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Add loading state to edit button
                    const originalText = editButton.innerHTML;
                    editButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Đang tải...';
                    editButton.disabled = true;
                    
                    setTimeout(() => {
                        const addressData = {
                            id: editButton.dataset.addressId,
                            fullName: editButton.dataset.fullName,
                            phoneNumber: editButton.dataset.phoneNumber,
                            city: editButton.dataset.city,
                            district: editButton.dataset.district,
                            ward: editButton.dataset.ward,
                            addressLine: editButton.dataset.addressLine,
                            latitude: editButton.dataset.latitude,
                            longitude: editButton.dataset.longitude,
                            isDefault: editButton.dataset.isDefault
                        };
                        
                        // Validate that we have required data
                        if (!addressData.id) {
                            console.error('Missing address ID!');
                            alert('Lỗi: Không tìm thấy ID địa chỉ. Vui lòng tải lại trang.');
                            editButton.innerHTML = originalText;
                            editButton.disabled = false;
                            return;
                        }
                        
                        openAddressModal('edit', addressData);
                        
                        // Reset button state
                        editButton.innerHTML = originalText;
                        editButton.disabled = false;
                    }, 500);
                }
            });
            
            // Enhanced close modal function with animation
            function closeModal() {
                const modal = addAddressModal;
                const modalContent = modal.querySelector('.bg-white');
                
                // Add closing animation
                modalContent.style.animation = 'fadeOut 0.2s ease-in';
                modal.style.animation = 'fadeOut 0.2s ease-in';
                
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    modal.style.animation = '';
                    modalContent.style.animation = '';
                    
                    addAddressForm.reset();
                    clearErrors();
                    
                    // Clear validation states
                    const inputs = modal.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        input.classList.remove('border-red-500', 'border-green-500', 'ring-2', 'ring-orange-200');
                    });
                    
                    // Re-enable submit button
                    const submitBtn = document.querySelector('#add-address-modal button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        submitBtn.title = '';
                    }
                    
                    // Clean up map
                    if (newAddressMap) {
                        newAddressMap.remove();
                        newAddressMap = null;
                    }
                    if (newAddressMarker) {
                        newAddressMarker = null;
                    }
                }, 200);
            }
            
            closeAddAddressModal.addEventListener('click', closeModal);
            cancelAddAddress.addEventListener('click', closeModal);
            
            // Close modal when clicking outside
            addAddressModal.addEventListener('click', function(e) {
                if (e.target === addAddressModal) {
                    closeModal();
                }
            });
            
            // Clear error messages
            function clearErrors() {
                const errorElements = addAddressModal.querySelectorAll('[id^="error_"]');
                errorElements.forEach(el => {
                    el.classList.add('hidden');
                    el.textContent = '';
                });
            }
            
            // Show error messages
            function showErrors(errors) {
                clearErrors();
                for (const [field, messages] of Object.entries(errors)) {
                    const errorElement = document.getElementById(`error_${field}`);
                    if (errorElement) {
                        errorElement.textContent = messages[0];
                        errorElement.classList.remove('hidden');
                        
                        // Add red border to input field
                        const inputElement = document.getElementById(`new_${field}`);
                        if (inputElement) {
                            inputElement.classList.add('border-red-500');
                            inputElement.classList.remove('border-gray-300');
                        }
                    }
                }
            }
            
            // Show single field error
            function showFieldError(fieldName, message) {
                const errorElement = document.getElementById(`error_${fieldName}`);
                const inputElement = document.getElementById(`new_${fieldName}`);
                
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.classList.remove('hidden');
                }
                
                if (inputElement) {
                    inputElement.classList.add('border-red-500');
                    inputElement.classList.remove('border-gray-300');
                }
            }
            
            // Clear single field error
            function clearFieldError(fieldName) {
                const errorElement = document.getElementById(`error_${fieldName}`);
                const inputElement = document.getElementById(`new_${fieldName}`);
                
                if (errorElement) {
                    errorElement.textContent = '';
                    errorElement.classList.add('hidden');
                }
                
                if (inputElement) {
                    inputElement.classList.remove('border-red-500');
                    inputElement.classList.add('border-gray-300');
                }
            }
            
            // Validate delivery distance function
            async function validateDeliveryDistance(addressText) {
                if (!addressText || addressText.trim() === '') {
                    // showFieldError('address_line', 'Vui lòng nhập địa chỉ để kiểm tra khoảng cách giao hàng');
                    return false;
                }

                try {
                    // Check if we already have coordinates
                    const latInput = document.getElementById('new_latitude');
                    const lngInput = document.getElementById('new_longitude');
                    
                    let lat, lng;
                    
                    if (latInput && lngInput && latInput.value && lngInput.value) {
                        // Use existing coordinates
                        lat = parseFloat(latInput.value);
                        lng = parseFloat(lngInput.value);
                    } else {
                        // Geocode the address to get coordinates
                        const citySelect = document.getElementById('new_city');
                        const districtSelect = document.getElementById('new_district');
                        const wardSelect = document.getElementById('new_ward');
                        
                        const city = citySelect ? citySelect.options[citySelect.selectedIndex]?.text || '' : '';
                        const district = districtSelect ? districtSelect.options[districtSelect.selectedIndex]?.text || '' : '';
                        const ward = wardSelect ? wardSelect.options[wardSelect.selectedIndex]?.text || '' : '';
                        
                        const fullAddress = `${addressText}, ${ward}, ${district}, ${city}, Vietnam`.replace(/,\s*,/g, ',').replace(/^,\s*|,\s*$/g, '');
                        
                        const geocodeUrl = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(fullAddress)}.json?access_token=${mapboxgl.accessToken}&country=VN&limit=1`;
                        
                        const response = await fetch(geocodeUrl);
                        const data = await response.json();
                        
                        if (!data.features || data.features.length === 0) {
                            // showFieldError('address_line', 'Không thể xác định vị trí của địa chỉ này. Vui lòng chọn vị trí trên bản đồ.');
                            return false;
                        }
                        
                        const coordinates = data.features[0].center;
                        lng = coordinates[0];
                        lat = coordinates[1];
                        
                        // Update hidden fields with geocoded coordinates
                        if (latInput) latInput.value = lat;
                        if (lngInput) lngInput.value = lng;
                    }

                    // Get branch coordinates
                    const branchLat = {{ $selectedBranch->latitude ?? 21.0285 }};
                    const branchLng = {{ $selectedBranch->longitude ?? 105.8542 }};
                    
                    // Calculate distance using Turf.js
                    const branchPoint = turf.point([branchLng, branchLat]);
                    const addressPoint = turf.point([lng, lat]);
                    const distance = turf.distance(branchPoint, addressPoint);
                    
                    // Check if distance exceeds maximum delivery distance
                    const maxDistance = shippingConfig.maxDeliveryDistance;
                    if (distance > maxDistance) {
                        showFieldError('address_line', `Khoảng cách giao hàng vượt quá giới hạn ${maxDistance}km (hiện tại: ${distance.toFixed(1)}km). Vui lòng chọn địa chỉ khác.`);
                        
                        // Show warning toast - REMOVED
                        // showEnhancedToast(
                        //     `Địa chỉ nằm ngoài vùng phục vụ. Khoảng cách tối đa: ${maxDistance}km`, 
                        //     'warning', 
                        //     { 
                        //         icon: 'fas fa-exclamation-triangle',
                        //         duration: 6000 
                        //     }
                        // );
                        return false;
                    }
                    
                    // Show success message for valid distance
                    clearFieldError('address_line');
                    // showEnhancedToast(
                    //     `Địa chỉ hợp lệ. Khoảng cách: ${distance.toFixed(1)}km`, 
                    //     'success', 
                    //     { 
                    //         icon: 'fas fa-check-circle',
                    //         duration: 3000 
                    //     }
                    // );
                    
                    return true;
                    
                } catch (error) {
                    console.error('Error validating delivery distance:', error);
                    // showFieldError('address_line', 'Có lỗi xảy ra khi kiểm tra khoảng cách giao hàng. Vui lòng thử lại.');
                    return false;
                }
            }

            // Validate delivery distance by coordinates (for map interactions)
            function validateDeliveryDistanceByCoordinates(lat, lng) {
                if (!lat || !lng || isNaN(lat) || isNaN(lng)) {
                    // showEnhancedToast('Vị trí không hợp lệ', 'error', {
                    //     icon: 'fas fa-exclamation-triangle',
                    //     duration: 3000
                    // });
                    return false;
                }

                // Get branch coordinates
                const branchLat = {{ $selectedBranch->latitude ?? 21.0285 }};
                const branchLng = {{ $selectedBranch->longitude ?? 105.8542 }};
                
                // Calculate distance using Turf.js
                const branchPoint = turf.point([branchLng, branchLat]);
                const addressPoint = turf.point([lng, lat]);
                const distance = turf.distance(branchPoint, addressPoint);
                
                // Check if distance exceeds maximum delivery distance
                const maxDistance = shippingConfig.maxDeliveryDistance;
                if (distance > maxDistance) {
                    // Show error message below map
                    showFieldError('address_line', `Vị trí này nằm ngoài vùng phục vụ (${distance.toFixed(1)}km > ${maxDistance}km). Vui lòng chọn vị trí khác.`);
                    
                    // Show warning toast for map selection - REMOVED
                    // showEnhancedToast(
                    //     `Vị trí này nằm ngoài vùng phục vụ (${distance.toFixed(1)}km > ${maxDistance}km)`, 
                    //     'warning', 
                    //     { 
                    //         icon: 'fas fa-map-marker-alt',
                    //         duration: 6000 
                    //     }
                    // );
                    
                    // Update marker color to indicate invalid location
                    if (newAddressMarker) {
                        const markerElement = newAddressMarker.getElement();
                        const markerIcon = markerElement.querySelector('.bg-orange-500');
                        if (markerIcon) {
                            markerIcon.classList.remove('bg-orange-500');
                            markerIcon.classList.add('bg-red-500');
                        }
                    }
                    
                    return false;
                }
                
                // Clear any previous error messages
                clearFieldError('address_line');
                
                // Show success message for valid distance - REMOVED
                // showEnhancedToast(
                //     `Vị trí hợp lệ. Khoảng cách: ${distance.toFixed(1)}km`, 
                //     'success', 
                //     { 
                //         icon: 'fas fa-check-circle',
                //         duration: 3000 
                //     }
                // );
                
                // Update marker color to indicate valid location
                if (newAddressMarker) {
                    const markerElement = newAddressMarker.getElement();
                    const markerIcon = markerElement.querySelector('.bg-red-500, .bg-orange-500');
                    if (markerIcon) {
                        markerIcon.classList.remove('bg-red-500');
                        markerIcon.classList.add('bg-orange-500');
                    }
                }
                
                return true;
            }

            // Real-time validation function
            function validateField(fieldName, value) {
                switch(fieldName) {
                    case 'recipient_name':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, 'Họ và tên người nhận là bắt buộc');
                            return false;
                        }
                        break;
                    case 'phone_number':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, 'Số điện thoại là bắt buộc');
                            return false;
                        }
                        if (!/^[0-9]{10,11}$/.test(value.replace(/\s/g, ''))) {
                            showFieldError(fieldName, 'Số điện thoại không hợp lệ');
                            return false;
                        }
                        break;
                    case 'city':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, 'Tỉnh/Thành phố là bắt buộc');
                            return false;
                        }
                        break;
                    case 'district':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, 'Quận/Huyện là bắt buộc');
                            return false;
                        }
                        break;
                    case 'ward':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, 'Xã/Phường là bắt buộc');
                            return false;
                        }
                        break;
                    case 'address_line':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, 'Số nhà, đường là bắt buộc');
                            return false;
                        }
                        // Note: Delivery distance validation is handled separately in form submission
                        break;
                }
                
                clearFieldError(fieldName);
                return true;
            }
            
            // Enhanced confirmation dialog function
            function showConfirmDialog(title, message, confirmText = 'Xác nhận', cancelText = 'Hủy') {
                return new Promise((resolve) => {
                    // Create modal backdrop
                    const backdrop = document.createElement('div');
                    backdrop.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center';
                    backdrop.style.zIndex = '10000';
                    backdrop.style.animation = 'fadeIn 0.3s ease-out';
                    
                    // Create modal content
                    const modal = document.createElement('div');
                    modal.className = 'bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all';
                    modal.style.animation = 'slideIn 0.3s ease-out';
                    
                    modal.innerHTML = `
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-question-circle text-orange-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">${title}</h3>
                                </div>
                            </div>
                            <div class="mb-6">
                                <p class="text-sm text-gray-500">${message}</p>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" class="cancel-btn px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                    ${cancelText}
                                </button>
                                <button type="button" class="confirm-btn px-4 py-2 text-sm font-medium text-white bg-orange-600 border border-transparent rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                                    ${confirmText}
                                </button>
                            </div>
                        </div>
                    `;
                    
                    backdrop.appendChild(modal);
                    document.body.appendChild(backdrop);
                    
                    // Add event listeners
                    const confirmBtn = modal.querySelector('.confirm-btn');
                    const cancelBtn = modal.querySelector('.cancel-btn');
                    
                    const cleanup = () => {
                        backdrop.style.animation = 'fadeOut 0.3s ease-out';
                        setTimeout(() => {
                            if (backdrop.parentNode) {
                                backdrop.parentNode.removeChild(backdrop);
                            }
                        }, 300);
                    };
                    
                    confirmBtn.addEventListener('click', () => {
                        cleanup();
                        resolve(true);
                    });
                    
                    cancelBtn.addEventListener('click', () => {
                        cleanup();
                        resolve(false);
                    });
                    
                    backdrop.addEventListener('click', (e) => {
                        if (e.target === backdrop) {
                            cleanup();
                            resolve(false);
                        }
                    });
                });
            }
            
            // Enhanced toast notification function
            function showEnhancedToast(message, type = 'info', options = {}) {
                const {
                    icon = type === 'success' ? 'fas fa-check-circle' : 
                          type === 'error' ? 'fas fa-times-circle' : 
                          type === 'warning' ? 'fas fa-exclamation-triangle' : 'fas fa-info-circle',
                    duration = 4000,
                    position = 'top-right'
                } = options;
                
                const colors = {
                    success: 'bg-green-500 border-green-600',
                    error: 'bg-red-500 border-red-600',
                    warning: 'bg-yellow-500 border-yellow-600',
                    info: 'bg-blue-500 border-blue-600'
                };
                
                const toast = document.createElement('div');
                toast.className = `fixed ${position === 'top-right' ? 'top-4 right-4' : 'bottom-4 right-4'} ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg border-l-4 max-w-sm z-50 transform transition-all duration-300 translate-x-full opacity-0`;
                
                toast.innerHTML = `
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="${icon} text-lg"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button class="close-toast text-white hover:text-gray-200 focus:outline-none">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(toast);
                
                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                    toast.classList.add('translate-x-0', 'opacity-100');
                }, 100);
                
                // Auto remove
                const removeToast = () => {
                    toast.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 300);
                };
                
                // Close button
                toast.querySelector('.close-toast').addEventListener('click', removeToast);
                
                // Auto remove after duration
                setTimeout(removeToast, duration);
                
                return toast;
            }
            
            // Enhanced error display function
            function showEnhancedErrors(errors) {
                Object.keys(errors).forEach(fieldName => {
                    const inputElement = document.getElementById(`new_${fieldName}`);
                    if (inputElement) {
                        // Add shake animation
                        inputElement.classList.add('animate-shake', 'border-red-500');
                        setTimeout(() => {
                            inputElement.classList.remove('animate-shake');
                        }, 500);
                        
                        // Show error message
                        showFieldError(fieldName, errors[fieldName][0]);
                    }
                });
                
                // Show general error toast - REMOVED
                // showEnhancedToast('Vui lòng kiểm tra lại thông tin đã nhập', 'error', {
                //     icon: 'fas fa-exclamation-triangle',
                //     duration: 5000
                // });
            }

            // Handle form submission
            addAddressForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Enhanced validation with visual feedback
                let hasErrors = false;
                const requiredFields = ['recipient_name', 'phone_number', 'city', 'district', 'ward', 'address_line'];
                
                requiredFields.forEach(fieldName => {
                    const inputElement = document.getElementById(`new_${fieldName}`);
                    if (inputElement && !validateField(fieldName, inputElement.value)) {
                        hasErrors = true;
                        // Add shake animation for invalid fields
                        inputElement.classList.add('animate-shake');
                        setTimeout(() => inputElement.classList.remove('animate-shake'), 500);
                    }
                });
                
                // Validate delivery distance before submitting
                const addressLineInput = document.getElementById('new_address_line');
                if (addressLineInput && addressLineInput.value.trim()) {
                    const distanceValid = await validateDeliveryDistance(addressLineInput.value.trim());
                    if (!distanceValid) {
                        hasErrors = true;
                        addressLineInput.classList.add('animate-shake', 'border-red-500');
                        setTimeout(() => {
                            addressLineInput.classList.remove('animate-shake');
                        }, 500);
                    }
                }
                
                if (hasErrors) {
                    // showEnhancedToast('Vui lòng kiểm tra lại thông tin đã nhập', 'error');
                    return;
                }
                
                const formMethod = document.getElementById('form_method').value;
                const editAddressId = document.getElementById('edit_address_id').value;
                const isEdit = formMethod === 'PUT' && editAddressId;
                
                // Show confirmation dialog for edits
                if (isEdit) {
                    const confirmed = await showConfirmDialog(
                        'Xác nhận cập nhật',
                        'Bạn có chắc chắn muốn cập nhật địa chỉ này không?',
                        'Cập nhật',
                        'Hủy'
                    );
                    
                    if (!confirmed) {
                        return;
                    }
                }
                
                // Enhanced loading state
                saveAddressBtn.disabled = true;
                saveAddressText.classList.add('hidden');
                saveAddressLoading.classList.remove('hidden');
                
                try {
                    const formData = new FormData(addAddressForm);
                    const formMethod = document.getElementById('form_method').value;
                    const editAddressId = document.getElementById('edit_address_id').value;
                    
                    // Explicitly handle checkbox value
                    const isDefaultCheckbox = document.getElementById('new_is_default');
                    if (isDefaultCheckbox.checked) {
                        formData.set('is_default', '1');
                    } else {
                        formData.delete('is_default'); // Remove if not checked
                    }
                    
                    // Determine URL and method based on operation
                    let url, method;
                    if (formMethod === 'PUT' && editAddressId) {
                        // Edit operation
                        url = `{{ route("customer.profile.addresses.index") }}/${editAddressId}`;
                        method = 'POST'; // Laravel uses POST with _method for PUT
                        formData.append('_method', 'PUT');
                    } else {
                        // Add operation
                        url = '{{ route("customer.profile.addresses.store") }}';
                        method = 'POST';
                    }
                    
                    const response = await fetch(url, {
                        method: method,
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        const successMessage = isEdit ? 'Địa chỉ đã được cập nhật thành công!' : 'Địa chỉ đã được thêm thành công!';
                        
                        // Enhanced success feedback
                        showEnhancedToast(successMessage, 'success', {
                            icon: isEdit ? 'fas fa-edit' : 'fas fa-plus',
                            duration: 3000
                        });
                        
                        if (isEdit) {
                            updateAddressInList(result.data);
                        } else {
                            addNewAddressToList(result.data);
                        }
                        
                        // Smooth modal close with delay
                        setTimeout(() => {
                            closeModal();
                        }, 500);
                        
                        // Handle first address scenario
                        const addressComponent = document.getElementById('address-component');
                        if (addressComponent && addressComponent.style.display === 'none') {
                            addressComponent.style.display = 'block';
                            const manualForm = document.querySelector('.bg-white.rounded-lg.shadow-sm.p-6.mb-6:has(#full_name)');
                            if (manualForm) {
                                manualForm.style.display = 'none';
                            }
                        }
                        
                    } else {
                        if (result.errors) {
                            showEnhancedErrors(result.errors);
                        } else {
                            const errorMessage = result.message || (isEdit ? 'Có lỗi xảy ra khi cập nhật địa chỉ' : 'Có lỗi xảy ra khi thêm địa chỉ');
                            showEnhancedToast(errorMessage, 'error', {
                                icon: 'fas fa-exclamation-triangle',
                                duration: 5000
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    const errorMessage = isEdit ? 'Có lỗi xảy ra khi cập nhật địa chỉ' : 'Có lỗi xảy ra khi thêm địa chỉ';
                    // showEnhancedToast(errorMessage, 'error', {
                    //     icon: 'fas fa-times-circle',
                    //     duration: 5000
                    // });
                } finally {
                    // Reset button state
                    saveAddressBtn.disabled = false;
                    saveAddressText.classList.remove('hidden');
                    saveAddressLoading.classList.add('hidden');
                }
            });

            // Function to add new address to the list
            function addNewAddressToList(address) {
                const addressListContainer = document.getElementById('address-list-container');
                const addressSummaryView = document.getElementById('address-summary-view');
                
                if (!addressListContainer) return;
                
                // Create full address string
                const fullAddress = `${address.address_line}, ${address.ward}, ${address.district}, ${address.city}`;
                
                // Always use default styling for new address since it will be selected
                const borderClass = 'border-orange-300 bg-orange-50';
                
                // Create new address HTML
                const newAddressHTML = `
                    <label for="address-radio-${address.id}" class="address-option-label flex items-start p-3 ${borderClass} rounded-lg cursor-pointer hover:bg-gray-50 transition-all"
                        data-address-id="${address.id}"
                        data-full-name="${address.recipient_name}"
                        data-phone-number="${address.phone_number}"
                        data-full-address="${fullAddress}"
                        data-is-default="${address.is_default ? 'true' : 'false'}"
                        data-city="${address.city}"
                        data-district="${address.district}"
                        data-ward="${address.ward}"
                        data-address-line="${address.address_line}"
                        data-latitude="${address.latitude || ''}"
                        data-longitude="${address.longitude || ''}">
                        <span class="text-gray-400 mr-4 mt-1"><i class="fas fa-map-marker-alt"></i></span>
                        <div class="flex-grow">
                            <div class="font-semibold">
                                <span>${address.recipient_name}</span>
                                <span class="font-normal">(${address.phone_number})</span>
                                ${address.is_default ? '<span class="ml-2 border border-orange-500 text-orange-500 px-2 py-0.5 rounded text-xs font-medium bg-white">Mặc Định</span>' : ''}
                            </div>
                            <div class="text-sm text-gray-700">${fullAddress}</div>
                            <div class="address-meta mt-2 text-sm">
                                <span class="distance-info text-blue-600 font-medium hidden"></span>
                                <span class="warning-info text-red-600 font-medium hidden"></span>
                            </div>
                        </div>
                        <input type="radio" name="selected_address_option" id="address-radio-${address.id}" value="${address.id}" class="form-radio h-5 w-5 text-orange-600 ml-4 mt-1" checked>
                    </label>
                `;
                
                // Remove default styling and uncheck all existing addresses
                const existingAddresses = addressListContainer.querySelectorAll('.address-option-label');
                console.log('Found existing addresses:', existingAddresses.length);
                
                existingAddresses.forEach(addressLabel => {
                    console.log('Processing existing address:', addressLabel);
                    
                    // Remove default badge - use a more specific approach
                    const defaultBadges = addressLabel.querySelectorAll('span');
                    defaultBadges.forEach(badge => {
                        if (badge.textContent.trim() === 'Mặc Định') {
                            console.log('Removing default badge:', badge);
                            badge.remove();
                        }
                    });
                    
                    // Remove default border styling
                    console.log('Before removing classes:', addressLabel.className);
                    addressLabel.classList.remove('border-orange-300', 'bg-orange-50');
                    addressLabel.classList.add('border-gray-200');
                    console.log('After removing classes:', addressLabel.className);
                    
                    // Update data attribute
                    addressLabel.setAttribute('data-is-default', 'false');
                });
                
                // Uncheck all existing radio buttons
                const existingRadios = addressListContainer.querySelectorAll('input[type="radio"]');
                existingRadios.forEach(radio => radio.checked = false);
                
                // Always add new address at the top
                addressListContainer.insertAdjacentHTML('afterbegin', newAddressHTML);
                console.log('Added new address at the top and selected it');
                
                // Always update summary view with new address
                updateAddressSummary(address, fullAddress);
                
                // Calculate distance for the new address if coordinates are available
                const branchLat = {{ $currentBranch->latitude ?? 'null' }};
                const branchLng = {{ $currentBranch->longitude ?? 'null' }};
                if (branchLat && branchLng && address.latitude && address.longitude && typeof turf !== 'undefined') {
                    const branchPoint = turf.point([branchLng, branchLat]);
                    const addressPoint = turf.point([parseFloat(address.longitude), parseFloat(address.latitude)]);
                    const distance = turf.distance(branchPoint, addressPoint);
                    
                    // Update distance info for the new address
                    const newAddressLabel = addressListContainer.querySelector(`label[data-address-id="${address.id}"]`);
                    if (newAddressLabel) {
                        const distanceInfoEl = newAddressLabel.querySelector('.distance-info');
                        const warningInfoEl = newAddressLabel.querySelector('.warning-info');
                        const radioInput = newAddressLabel.querySelector('input[type="radio"]');
                        
                        if (distanceInfoEl) {
                            distanceInfoEl.textContent = `📍 ${distance.toFixed(1)}km từ chi nhánh`;
                            distanceInfoEl.dataset.distance = distance;
                            distanceInfoEl.classList.remove('hidden');
                        }
                        
                        // Check if address is outside service area
                        const shippingConfig = {
                            maxDeliveryDistance: {{ $currentBranch->max_delivery_distance ?? 15 }}
                        };
                        
                        if (distance > shippingConfig.maxDeliveryDistance) {
                            // Show warning for address outside service area
                            if (warningInfoEl) {
                                warningInfoEl.innerHTML = `
                                    <div class="text-red-600 text-xs mt-1">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Ngoài vùng phục vụ (tối đa: ${shippingConfig.maxDeliveryDistance}km)
                                    </div>`;
                                warningInfoEl.classList.remove('hidden');
                            }
                            
                            // Disable this address
                            if (radioInput) {
                                radioInput.disabled = true;
                                radioInput.checked = false;
                            }
                            newAddressLabel.classList.add('opacity-60', 'cursor-not-allowed');
                            newAddressLabel.classList.remove('hover:bg-gray-50', 'border-orange-300', 'bg-orange-50');
                            newAddressLabel.classList.add('border-gray-200');
                            newAddressLabel.style.borderColor = '#fca5a5'; // red-300
                            
                            // If this was the selected address, we need to find another valid one
                            const validAddresses = addressListContainer.querySelectorAll('.address-option-label input[type="radio"]:not([disabled])');
                            if (validAddresses.length > 0) {
                                validAddresses[0].checked = true;
                                const validLabel = validAddresses[0].closest('.address-option-label');
                                validLabel.classList.remove('border-gray-200');
                                validLabel.classList.add('border-orange-300', 'bg-orange-50');
                                
                                // Update summary with valid address
                                const validData = validLabel.dataset;
                                const validFullAddress = `${validData.addressLine}, ${validData.ward}, ${validData.district}, ${validData.city}`;
                                const validAddress = {
                                    id: validData.addressId,
                                    recipient_name: validData.fullName,
                                    phone_number: validData.phoneNumber,
                                    address_line: validData.addressLine,
                                    city: validData.city,
                                    district: validData.district,
                                    ward: validData.ward,
                                    latitude: validData.latitude,
                                    longitude: validData.longitude,
                                    is_default: validData.isDefault === 'true'
                                };
                                updateAddressSummary(validAddress, validFullAddress);
                            }
                        } else {
                            // Address is valid
                            if (radioInput) {
                                radioInput.disabled = false;
                            }
                            newAddressLabel.classList.remove('opacity-60', 'cursor-not-allowed');
                            newAddressLabel.classList.add('hover:bg-gray-50');
                            if (warningInfoEl) {
                                warningInfoEl.classList.add('hidden');
                            }
                            newAddressLabel.style.borderColor = '';
                        }
                        
                        // Update shipping fee UI
                        if (radioInput && radioInput.checked && !radioInput.disabled) {
                            // Update shipping fee for the selected address
                            const updateShippingFeeUI = window.updateShippingFeeUI;
                            if (typeof updateShippingFeeUI === 'function') {
                                updateShippingFeeUI(distance);
                            }
                        }
                    }
                }
                
                // Add event listener to the new address option
                const newAddressLabel = addressListContainer.querySelector(`label[data-address-id="${address.id}"]`);
                if (newAddressLabel) {
                    newAddressLabel.addEventListener('click', function(e) {
                        const radio = this.querySelector('input[type="radio"]');
                        if (radio && radio.disabled) { 
                            e.preventDefault(); 
                            return; 
                        }
                        
                        // Remove border styling from all addresses
                        const allAddressLabels = addressListContainer.querySelectorAll('.address-option-label');
                        allAddressLabels.forEach(l => {
                            l.classList.remove('border-orange-300', 'bg-orange-50');
                            l.classList.add('border-gray-200');
                        });
                        
                        // Add border styling to selected address
                        this.classList.remove('border-gray-200');
                        this.classList.add('border-orange-300', 'bg-orange-50');
                        
                        // Update radio button selection
                        if (radio) {
                            radio.checked = true;
                        }
                    });
                }
            }
            
            // Function to update existing address in the list
            function updateAddressInList(address) {
                const addressListContainer = document.getElementById('address-list-container');
                const existingAddressLabel = addressListContainer.querySelector(`label[data-address-id="${address.id}"]`);
                
                if (!existingAddressLabel) return;
                
                // Create full address string
                const fullAddress = `${address.address_line}, ${address.ward}, ${address.district}, ${address.city}`;
                
                // Remove default styling and uncheck all existing addresses
                const allAddressLabels = addressListContainer.querySelectorAll('.address-option-label');
                allAddressLabels.forEach(label => {
                    label.classList.remove('border-orange-300', 'bg-orange-50');
                    label.classList.add('border-gray-200');
                    label.setAttribute('data-is-default', 'false');
                    
                    // Remove default badges from other addresses
                    const defaultBadges = label.querySelectorAll('span');
                    defaultBadges.forEach(badge => {
                        if (badge.textContent.trim() === 'Mặc Định') {
                            badge.remove();
                        }
                    });
                    
                    // Uncheck radio buttons
                    const radio = label.querySelector('input[type="radio"]');
                    if (radio) radio.checked = false;
                });
                
                // Update data attributes
                existingAddressLabel.setAttribute('data-full-name', address.recipient_name);
                existingAddressLabel.setAttribute('data-phone-number', address.phone_number);
                existingAddressLabel.setAttribute('data-full-address', fullAddress);
                existingAddressLabel.setAttribute('data-is-default', address.is_default ? 'true' : 'false');
                existingAddressLabel.setAttribute('data-city', address.city);
                existingAddressLabel.setAttribute('data-district', address.district);
                existingAddressLabel.setAttribute('data-ward', address.ward);
                existingAddressLabel.setAttribute('data-address-line', address.address_line);
                existingAddressLabel.setAttribute('data-latitude', address.latitude || '');
                existingAddressLabel.setAttribute('data-longitude', address.longitude || '');
                
                // Update the content
                const contentDiv = existingAddressLabel.querySelector('.flex-grow');
                if (contentDiv) {
                    contentDiv.innerHTML = `
                        <div class="font-semibold">
                            <span>${address.recipient_name}</span>
                            <span class="font-normal">(${address.phone_number})</span>
                            ${address.is_default ? '<span class="ml-2 border border-orange-500 text-orange-500 px-2 py-0.5 rounded text-xs font-medium bg-white">Mặc Định</span>' : ''}
                        </div>
                        <div class="text-sm text-gray-700">${fullAddress}</div>
                        <div class="address-meta mt-2 text-sm">
                            <span class="distance-info text-blue-600 font-medium hidden"></span>
                            <span class="warning-info text-red-600 font-medium hidden"></span>
                        </div>
                    `;
                }
                
                // Update edit button data attributes
                const editBtn = existingAddressLabel.querySelector('.edit-address-btn');
                if (editBtn) {
                    editBtn.setAttribute('data-address-id', address.id);
                    editBtn.setAttribute('data-full-name', address.recipient_name);
                    editBtn.setAttribute('data-phone-number', address.phone_number);
                    editBtn.setAttribute('data-city', address.city);
                    editBtn.setAttribute('data-district', address.district);
                    editBtn.setAttribute('data-ward', address.ward);
                    editBtn.setAttribute('data-address-line', address.address_line);
                    editBtn.setAttribute('data-latitude', address.latitude || '');
                    editBtn.setAttribute('data-longitude', address.longitude || '');
                    editBtn.setAttribute('data-is-default', address.is_default ? 'true' : 'false');
                }
                
                // Move the updated address to the top of the list
                existingAddressLabel.remove();
                addressListContainer.insertAdjacentElement('afterbegin', existingAddressLabel);
                
                // Apply selected styling to the updated address
                existingAddressLabel.classList.remove('border-gray-200');
                existingAddressLabel.classList.add('border-orange-300', 'bg-orange-50');
                
                // Check the radio button for the updated address
                const thisRadio = existingAddressLabel.querySelector('input[type="radio"]');
                if (thisRadio) thisRadio.checked = true;
                
                // Update summary view with the updated address
                updateAddressSummary(address, fullAddress);
                
                // Calculate distance for the updated address if coordinates are available
                const branchLat = {{ $currentBranch->latitude ?? 'null' }};
                const branchLng = {{ $currentBranch->longitude ?? 'null' }};
                if (branchLat && branchLng && address.latitude && address.longitude && typeof turf !== 'undefined') {
                    const branchPoint = turf.point([branchLng, branchLat]);
                    const addressPoint = turf.point([parseFloat(address.longitude), parseFloat(address.latitude)]);
                    const distance = turf.distance(branchPoint, addressPoint);
                    
                    // Update distance info for the updated address
                    const distanceInfoEl = existingAddressLabel.querySelector('.distance-info');
                    const warningInfoEl = existingAddressLabel.querySelector('.warning-info');
                    const radioInput = existingAddressLabel.querySelector('input[type="radio"]');
                    
                    if (distanceInfoEl) {
                        distanceInfoEl.textContent = `📍 ${distance.toFixed(1)}km từ chi nhánh`;
                        distanceInfoEl.dataset.distance = distance;
                        distanceInfoEl.classList.remove('hidden');
                    }
                    
                    // Check if address is outside service area
                    const shippingConfig = {
                        maxDeliveryDistance: {{ $currentBranch->max_delivery_distance ?? 15 }}
                    };
                    
                    if (distance > shippingConfig.maxDeliveryDistance) {
                        // Show warning for address outside service area
                        if (warningInfoEl) {
                            warningInfoEl.innerHTML = `
                                <div class="text-red-600 text-xs mt-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Ngoài vùng phục vụ (tối đa: ${shippingConfig.maxDeliveryDistance}km)
                                </div>`;
                            warningInfoEl.classList.remove('hidden');
                        }
                        
                        // Disable this address
                        if (radioInput) {
                            radioInput.disabled = true;
                            radioInput.checked = false;
                        }
                        existingAddressLabel.classList.add('opacity-60', 'cursor-not-allowed');
                        existingAddressLabel.classList.remove('hover:bg-gray-50', 'border-orange-300', 'bg-orange-50');
                        existingAddressLabel.classList.add('border-gray-200');
                        existingAddressLabel.style.borderColor = '#fca5a5'; // red-300
                        
                        // If this was the selected address, we need to find another valid one
                        const validAddresses = addressListContainer.querySelectorAll('.address-option-label input[type="radio"]:not([disabled])');
                        if (validAddresses.length > 0) {
                            validAddresses[0].checked = true;
                            const validLabel = validAddresses[0].closest('.address-option-label');
                            validLabel.classList.remove('border-gray-200');
                            validLabel.classList.add('border-orange-300', 'bg-orange-50');
                            
                            // Update summary with valid address
                            const validData = validLabel.dataset;
                            const validFullAddress = `${validData.addressLine}, ${validData.ward}, ${validData.district}, ${validData.city}`;
                            const validAddress = {
                                id: validData.addressId,
                                recipient_name: validData.fullName,
                                phone_number: validData.phoneNumber,
                                address_line: validData.addressLine,
                                city: validData.city,
                                district: validData.district,
                                ward: validData.ward,
                                latitude: validData.latitude,
                                longitude: validData.longitude,
                                is_default: validData.isDefault === 'true'
                            };
                            updateAddressSummary(validAddress, validFullAddress);
                        }
                    } else {
                        // Address is valid
                        if (radioInput) {
                            radioInput.disabled = false;
                        }
                        existingAddressLabel.classList.remove('opacity-60', 'cursor-not-allowed');
                        existingAddressLabel.classList.add('hover:bg-gray-50');
                        if (warningInfoEl) {
                            warningInfoEl.classList.add('hidden');
                        }
                        existingAddressLabel.style.borderColor = '';
                        
                        // Update shipping fee UI
                        const updateShippingFeeUI = window.updateShippingFeeUI;
                        if (typeof updateShippingFeeUI === 'function') {
                            updateShippingFeeUI(distance);
                        }
                    }
                }
            }
                        
            
            // Function to update address summary
            function updateAddressSummary(address, fullAddress) {
                const summaryName = document.getElementById('summary-name');
                const summaryPhone = document.getElementById('summary-phone');
                const summaryAddress = document.getElementById('summary-address');
                const summaryDefaultBadge = document.getElementById('summary-default-badge');
                
                // Update hidden fields
                document.getElementById('hidden_address_id').value = address.id;
                document.getElementById('hidden_full_name').value = address.recipient_name;
                document.getElementById('hidden_phone').value = address.phone_number;
                document.getElementById('hidden_address').value = address.address_line;
                document.getElementById('hidden_city').value = address.city;
                document.getElementById('hidden_district').value = address.district;
                document.getElementById('hidden_ward').value = address.ward;
                
                // Update summary display
                if (summaryName) summaryName.textContent = address.recipient_name;
                if (summaryPhone) summaryPhone.textContent = `(${address.phone_number})`;
                if (summaryAddress) summaryAddress.textContent = fullAddress;
                if (summaryDefaultBadge) {
                    if (address.is_default) {
                        summaryDefaultBadge.classList.remove('hidden');
                    } else {
                        summaryDefaultBadge.classList.add('hidden');
                    }
                }
            }
            

        });
    </script>

    <!-- Disable Pusher on Checkout Page -->
    <script>
        // Completely disable Pusher on checkout page to prevent connection errors
        (function() {
            // Override Pusher constructor to prevent initialization
            if (typeof window.Pusher !== 'undefined') {
                const OriginalPusher = window.Pusher;
                window.Pusher = function() {
                    // Return a mock Pusher object that does nothing
                    return {
                        subscribe: function() {
                            return {
                                bind: function() {},
                                unbind: function() {},
                                trigger: function() {}
                            };
                        },
                        unsubscribe: function() {},
                        disconnect: function() {},
                        connection: {
                            bind: function() {},
                            unbind: function() {},
                            state: 'disconnected'
                        }
                    };
                };
                // Copy static properties
                Object.keys(OriginalPusher).forEach(key => {
                    window.Pusher[key] = OriginalPusher[key];
                });
                window.Pusher.logToConsole = false;
            }
            
            // Override console methods to filter Pusher errors
            const originalConsoleError = console.error;
            const originalConsoleWarn = console.warn;
            const originalConsoleLog = console.log;
            
            function filterPusherMessages(...args) {
                const message = args.join(' ').toLowerCase();
                return message.includes('pusher') || 
                       message.includes('sockjs') || 
                       message.includes('websocket') || 
                       message.includes('net::err_failed') ||
                       message.includes('cors policy') ||
                       message.includes('access-control-allow-origin') ||
                       message.includes('xhr') ||
                       message.includes('http_request.ts') ||
                       message.includes('transport_connection') ||
                       message.includes('strategy.ts');
            }
            
            console.error = function(...args) {
                if (!filterPusherMessages(...args)) {
                    originalConsoleError.apply(console, args);
                }
            };
            
            console.warn = function(...args) {
                if (!filterPusherMessages(...args)) {
                    originalConsoleWarn.apply(console, args);
                }
            };
            
            console.log = function(...args) {
                if (!filterPusherMessages(...args)) {
                    originalConsoleLog.apply(console, args);
                }
            };
            
            // Handle unhandled promise rejections
            window.addEventListener('unhandledrejection', function(event) {
                const reason = event.reason ? event.reason.toString().toLowerCase() : '';
                if (reason.includes('pusher') || 
                    reason.includes('sockjs') || 
                    reason.includes('websocket') ||
                    reason.includes('cors') ||
                    reason.includes('net::err_failed')) {
                    event.preventDefault();
                    return;
                }
            });
            
            // Handle regular errors
            window.addEventListener('error', function(event) {
                const message = event.message ? event.message.toLowerCase() : '';
                const filename = event.filename ? event.filename.toLowerCase() : '';
                if (message.includes('pusher') || 
                    message.includes('sockjs') || 
                    message.includes('websocket') ||
                    filename.includes('pusher') ||
                    filename.includes('sockjs')) {
                    event.preventDefault();
                    return;
                }
            });
        })();
    </script>
@endsection

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
        #addAddressModal button,
        #changeAddressBtn, 
        #addFirstAddressBtn, 
        #openAddAddressModalBtn {
            pointer-events: auto !important;
            cursor: pointer !important;
        }

        /* Ensure modal is properly shown when flex class is added */
        #addressModal.flex, 
        #addAddressModal.flex {
            display: flex !important;
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
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- ========= CỘT BÊN TRÁI ========= -->
                <div class="lg:col-span-2">
            @auth
                        <!-- Address Component -->
                        <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-200" id="address-component">
                            <h2 class="text-xl font-bold mb-1">Địa chỉ giao hàng</h2>
                            <p class="text-sm text-gray-500 mb-4">Chọn hoặc thêm địa chỉ nhận hàng của bạn.</p>
                            <hr class="mb-4">

                            <!-- View 1: Hiển thị địa chỉ được chọn -->
                            <div id="address-summary-view">
                @if($userAddresses && $userAddresses->count() > 0)
                    @php
                                        // Xác định địa chỉ được chọn (từ query param hoặc mặc định)
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
                @else
                                    <div class="text-center py-4">
                                        <p class="text-gray-600 mb-3">Bạn chưa có địa chỉ nào được lưu.</p>
                                        <button type="button" id="show-add-form-btn-initial" class="bg-orange-500 text-white px-4 py-2 rounded-md hover:bg-orange-600 text-sm font-semibold">
                                            <i class="fas fa-plus mr-2"></i>Thêm địa chỉ đầu tiên
                            </button>
                        </div>
                                @endif
                    </div>

                            <!-- View 2: Danh sách địa chỉ để chọn -->
                            <div id="address-list-view" class="hidden">
                                <div class="space-y-3 max-h-72 overflow-y-auto pr-2" id="address-list-container">
                                    @if($userAddresses && $userAddresses->count() > 0)
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
                                                <input type="radio" name="selected_address_option" id="address-radio-{{ $address->id }}" value="{{ $address->id }}" class="form-radio h-5 w-5 text-orange-600 ml-4 mt-1" {{ ($selectedAddress->id ?? -1) == $address->id ? 'checked' : '' }}>
                                            </label>
                                        @endforeach
                                    @endif
                    </div>
                                <div class="mt-4 pt-4 border-t">
                                     <a href="{{ route('customer.profile') }}#addresses" class="text-orange-600 border border-orange-500 rounded px-3 py-1 text-sm font-medium hover:bg-orange-50">
                                        <i class="fas fa-plus mr-2"></i>Thêm địa chỉ mới
                                    </a>
                                    <div class="flex justify-end gap-3 mt-3">
                                        <button type="button" id="cancel-change-address-btn" class="px-5 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 text-sm">Hủy</button>
                                        <button type="button" id="confirm-address-btn" class="px-5 py-2 rounded bg-orange-500 text-white font-semibold hover:bg-orange-600 text-sm">Xác nhận</button>
                                    </div>
                                </div>
                            </div>

                            <!-- View 3: Form thêm địa chỉ (ĐÃ XÓA) -->
                        </div>
                    @else
                        <!-- Guest form -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Thông Tin Giao Hàng</h2>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label for="full_name" class="form-label required">Họ và tên</label>
                                <input type="text" id="full_name" name="full_name" class="form-control"
                                    value="{{ old('full_name') }}" required>
                                @error('full_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="form-label required">Số điện thoại</label>
                                <input type="tel" id="phone" name="phone" class="form-control"
                                    value="{{ old('phone') }}" required>
                                @error('phone')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="email" class="form-label required">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

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
                                    value="{{ old('address') }}" autocomplete="off" required>
                                <div id="address-autocomplete" class="autocomplete-items" style="display: none;"></div>
                                <div class="text-xs text-gray-500 mt-1">Nhập địa chỉ sau khi chọn Quận/Huyện và Phường/Xã</div>
                                @error('address')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endauth

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
                                    <button type="button" id="apply-coupon-btn" class="bg-orange-500 text-white px-5 rounded-l-none rounded-r-lg hover:bg-orange-600 font-semibold text-sm transition-colors border border-orange-500" {{ session('coupon_code') ? 'disabled' : '' }}>Áp dụng</button>
                                </div>
                                <div id="coupon-feedback" class="mt-2 text-sm"></div>
                             </div>
                        </div>

                        <h2 class="text-xl font-bold mb-4">Đơn Hàng Của Bạn</h2>
                        <!-- Order Items -->
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
                                                $itemPrice = $item->final_price ?? ($item->variant->price + $item->toppings->sum('price'));
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
                                    required>
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
<script src='https://npmcdn.com/@turf/turf/turf.min.js'></script>
    <script>
        // --- SHIPPING CONFIG ---
        const shippingConfig = {
            freeShippingThreshold: {{ config('shipping.free_shipping_threshold', 200000) }},
            baseFee: {{ config('shipping.base_fee', 10000) }},
            feePerKm: {{ config('shipping.fee_per_km', 5000) }},
            maxDistance: {{ config('shipping.max_delivery_distance', 7) }}
        };

        /**
         * Tính phí vận chuyển ở phía client.
         * @param {number} distance - Khoảng cách tính bằng km.
         * @param {number} subtotal - Tổng phụ của đơn hàng.
         * @returns {number} Phí vận chuyển.
         */
        function calculateShippingFee(distance, subtotal) {
            if (subtotal >= shippingConfig.freeShippingThreshold) {
                return 0;
            }
            if (distance > shippingConfig.maxDistance || distance < 0) {
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
                            <p>Địa chỉ này ở quá xa chi nhánh. Vui lòng chọn một địa chỉ khác để tiếp tục.</p>
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
                const maxDistance = shippingConfig.maxDistance;
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

                    if (distance > maxDistance) {
                        radioInput.disabled = true;
                        label.classList.add('opacity-60', 'cursor-not-allowed');
                        label.classList.remove('hover:bg-gray-50');
                        warningInfoEl.textContent = `Địa chỉ vượt quá ${maxDistance}km.`;
                        warningInfoEl.classList.remove('hidden');
                        label.style.borderColor = '#e5e7eb';
                    }
                });
            }

            function initializeCheckoutPage() {
                if (typeof turf === 'undefined' || !branchLat || !branchLng) {
                    console.error('Turf.js or branch coordinates are not available.');
                    document.getElementById('shipping-fee-display').textContent = 'Lỗi cấu hình';
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
                            
                            if (distance > shippingConfig.maxDistance) {
                                displaySummaryWarning();
                                toggleCheckoutButton(false, 'Địa chỉ đã chọn nằm ngoài vùng phục vụ.');
                            }
                        }
                    } else if (addressLabels.length === 0) {
                        updateShippingFeeUI(-1); // No addresses, so invalid
                    }
                } else if ({{ Auth::check() ? 'true' : 'false' }} && addressLabels.length === 0) {
                    updateShippingFeeUI(-1); // Logged in but no addresses
                } else if (!{{ Auth::check() ? 'true' : 'false' }}) {
                    document.getElementById('shipping-fee-display').textContent = 'Nhập địa chỉ';
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
                    if (selectedRadio.disabled) {
                        showToast('Địa chỉ này ở quá xa, vui lòng chọn địa chỉ khác.', 'error'); return;
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
                    
                    // Update UI state
                    if (distance > shippingConfig.maxDistance) {
                        displaySummaryWarning();
                        toggleCheckoutButton(false, 'Địa chỉ đã chọn nằm ngoài vùng phục vụ.');
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
                    label.classList.add('border-orange-500', 'bg-orange-50');
                }
                label.addEventListener('click', (e) => {
                    const radio = label.querySelector('input[type="radio"]');
                    if (radio.disabled) { e.preventDefault(); return; }
                    addressLabels.forEach(l => l.classList.remove('border-orange-500', 'bg-orange-50'));
                    label.classList.add('border-orange-500', 'bg-orange-50');
                });
            });

            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-lg shadow-lg text-white ${type === 'error' ? 'bg-red-500' : 'bg-green-500'}`;
                toast.textContent = message; document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }
            
            // Start The Process
            initializeCheckoutPage();

            // --- COUPON LOGIC (No changes needed) ---
            // ...
        
            // --- PAYMENT METHOD HIGHLIGHT (No changes needed) ---
            // ...
        });
    </script>
@endsection

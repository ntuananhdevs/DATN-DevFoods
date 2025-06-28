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
            <div>
                <div>
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
                        <a href="#" class="ml-4 text-blue-600 hover:underline font-medium text-sm">Thay Đổi</a>
                    </div>

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
                                    value="{{ old('phone', auth()->user()->phone ?? '') }}">
                                @error('phone')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="email" class="form-label required">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="{{ old('email', auth()->user()->email ?? '') }}">
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="city" class="form-label required">Tỉnh/Thành phố</label>
                                <select id="city" name="city" class="form-control">
                                    <option value="Hà Nội" selected>Hà Nội</option>
                                </select>
                                @error('city')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="district" class="form-label required">Quận/Huyện</label>
                                <select id="district" name="district" class="form-control">
                                    <option value="">-- Chọn Quận/Huyện --</option>
                                </select>
                                @error('district')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="ward" class="form-label required">Xã/Phường</label>
                                <select id="ward" name="ward" class="form-control">
                                    <option value="">-- Chọn Xã/Phường --</option>
                                </select>
                                @error('ward')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="md:col-span-2 relative">
                                <label for="address" class="form-label">Số nhà, đường</label>
                                <input type="text" id="address" name="address" class="form-control"
                                    value="{{ old('address', auth()->user()->address ?? '') }}" autocomplete="off">
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

                    <!-- Sản phẩm, voucher, vận chuyển, lời nhắn (giống Shopee) -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <div class="mb-4">
                            <div class="flex items-center mb-2">
                                <span class="text-base font-semibold">Sản phẩm</span>
                                <span class="ml-auto text-gray-400 text-sm">Đơn giá</span>
                                <span class="w-20 text-gray-400 text-sm text-center">Số lượng</span>
                                <span class="w-24 text-gray-400 text-sm text-right">Thành tiền</span>
                            </div>
                            <div class="flex items-center border-b py-3">
                                <img src="https://product.hstatic.net/200000605103/product/bo_pho_mai_35d14a20f2c34b938cf95f984dece2e0_master.jpg" alt="sp" class="w-14 h-14 rounded border mr-4">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium truncate">Burger Bò Phô Mai</div>
                                    <div class="text-xs text-gray-500 mt-1">Nhỏ, Ít đường</div>
                                    <p class="text-xs text-orange-600 mt-1">+1 topping</p>
                                </div>
                                <div class="text-right w-24">₫445.000</div>
                                <div class="w-20 text-center">1</div>
                                <div class="w-24 text-right font-semibold">₫445.000</div>
                            </div>
                            <div class="flex items-center py-3 border-b">
                                <input type="checkbox" class="mr-2">
                                <span class="text-sm">Bảo hiểm Thiết bị điện tử</span>
                                <span class="ml-auto text-gray-500 text-sm">₫13.999</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b relative">
                            <div class="flex items-center">
                                <span class="text-red-500 mr-2"><i class="fas fa-ticket-alt"></i></span>
                                <span class="text-sm font-medium">Voucher của Shop</span>
                                <span class="ml-2 bg-red-100 text-red-500 px-2 py-0.5 rounded text-xs font-semibold">-₫45k</span>
                            </div>
                            <button type="button" id="toggleVoucherBox" class="text-blue-600 text-sm hover:underline">Chọn Voucher Khác</button>
                            <!-- Box chọn voucher (ẩn/hiện) -->
                            <div id="voucherBox" class="absolute right-0 top-full mt-2 bg-white rounded-lg shadow-lg border border-gray-200 z-40 hidden">
                                <div class="absolute -top-2.5 right-0 w-0 h-0 bg-white" style="left:90%;transform:translateX(-50%);">
                                    <div class="bg-white" style="width:0;height:0;border-left:10px solid transparent;border-right:10px solid transparent;border-bottom:10px solid #e5e7eb;"></div>
                                </div>
                                <div class="px-6 pt-4 pb-2 border-b flex items-center gap-2">
                                    <span class="font-semibold text-base">XSmart Store Voucher</span>
                                    <span class="ml-auto text-xs text-gray-500">Mã Voucher</span>
                                    <input type="text" class="border rounded px-2 py-1 text-xs w-40" placeholder="Nhập mã voucher của Shop">
                                    <button class="bg-gray-200 text-gray-500 px-3 py-1 rounded text-xs font-semibold cursor-not-allowed" disabled>ÁP DỤNG</button>
                                </div>
                                <div class="max-h-72 overflow-y-auto px-6 py-4 space-y-4">
                                    <!-- Voucher 1 -->
                                    <div class="relative bg-white rounded-lg border border-orange-400 p-4 flex gap-4 items-start shadow-sm">
                                        <div class="flex flex-col items-center mr-2">
                                            <img src="https://cf.shopee.vn/file/sg-11134201-7quk2-ljv3v7w7w7w7a2" alt="logo" class="w-10 h-10 object-contain mb-1">
                                            <span class="bg-green-500 text-white text-xs px-2 py-0.5 rounded">Lựa chọn tốt nhất</span>
                                            <span class="bg-red-100 text-red-500 text-xs px-2 py-0.5 rounded mt-1">Shop Yêu Thích</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-sm">Giảm 10% Giảm tối đa ₫60k</div>
                                            <div class="text-xs text-gray-600">Đơn Tối Thiểu ₫300k</div>
                                            <span class="inline-block border border-red-400 text-red-500 text-xs px-2 py-0.5 rounded mt-1">Sản phẩm nhất định</span>
                                            <div class="text-xs text-gray-500 mt-1">HSD: 30.06.2025 <a href="#" class="text-blue-500 underline">Điều Kiện</a></div>
                                        </div>
                                        <span class="absolute top-2 right-2 text-orange-500"><i class="fas fa-check-circle fa-lg"></i></span>
                                    </div>
                                    <!-- Voucher 2 -->
                                    <div class="relative bg-white rounded-lg border p-4 flex gap-4 items-start shadow-sm">
                                        <div class="flex flex-col items-center mr-2">
                                            <img src="https://cf.shopee.vn/file/sg-11134201-7quk2-ljv3v7w7w7w7a2" alt="logo" class="w-10 h-10 object-contain mb-1">
                                            <span class="bg-red-100 text-red-500 text-xs px-2 py-0.5 rounded">Shop Yêu Thích</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-sm">Giảm 9% Giảm tối đa ₫50k</div>
                                            <div class="text-xs text-gray-600">Đơn Tối Thiểu ₫200k</div>
                                            <span class="inline-block border border-red-400 text-red-500 text-xs px-2 py-0.5 rounded mt-1">Sản phẩm nhất định</span>
                                            <div class="w-full h-1 bg-orange-100 rounded mt-2"><div class="h-1 bg-orange-400 rounded" style="width:99%"></div></div>
                                            <div class="text-xs text-gray-500 mt-1">Đã dùng 99%, HSD: 30.06.2025</div>
                                        </div>
                                        <button class="absolute top-2 right-2 bg-red-100 text-red-500 px-3 py-1 rounded text-xs font-semibold">Lưu</button>
                                    </div>
                                    <!-- Voucher 3 -->
                                    <div class="relative bg-gray-100 rounded-lg border p-4 flex gap-4 items-start opacity-60">
                                        <div class="flex flex-col items-center mr-2">
                                            <img src="https://cf.shopee.vn/file/sg-11134201-7quk2-ljv3v7w7w7w7a2" alt="logo" class="w-10 h-10 object-contain mb-1">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-sm">Giảm ₫150k</div>
                                            <div class="text-xs text-gray-600">Đơn Tối Thiểu ₫2,5tr</div>
                                            <div class="text-xs text-gray-500 mt-1">HSD: 08.08.2025</div>
                                        </div>
                                        <button class="absolute top-2 right-2 bg-red-100 text-red-500 px-3 py-1 rounded text-xs font-semibold">Lưu</button>
                                    </div>
                                </div>
                                <div class="px-6 py-2 text-xs text-orange-600 bg-orange-50 border-t border-orange-100">Mua thêm ₫2,055tr để sử dụng Voucher</div>
                                <div class="px-6 py-2 text-xs text-gray-600 border-t">1 Voucher đã được chọn <span class="text-red-500 font-semibold">Tiết kiệm ₫44,5k</span></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-6 py-6 border-b">
                            <div>
                                <label class="block text-sm text-gray-600 mb-2">Lời nhắn:</label>
                                <input type="text" class="w-full border rounded px-3 py-2 text-sm" placeholder="Lưu ý cho Người bán...">
                            </div>
                            <div>
                                <div class="flex items-center mb-2">
                                    <span class="text-sm text-gray-600 font-medium">Phương thức vận chuyển:</span>
                                    <button type="button" class="ml-2 text-blue-600 text-sm hover:underline shipping-change-btn">Thay Đổi</button>
                                    <span class="ml-auto text-gray-700 font-semibold">₫18.300</span>
                                </div>
                                <div class="text-xs text-gray-500 mb-1">Đảm bảo nhận hàng từ 28 Tháng 6 - 30 Tháng 6</div>
                                <div class="text-xs text-gray-500 mb-1">Nhận Voucher trị giá ₫15.000 nếu đơn hàng được giao đến bạn sau ngày 30 Tháng 6 2025.</div>
                                <div class="flex items-center mt-2">
                                    <span class="text-xs text-gray-400">Hoặc chọn Tủ Nhận Hàng để nhận</span>
                                    <a href="#" class="ml-2 text-green-600 text-xs font-semibold flex items-center"><i class="fas fa-shipping-fast mr-1"></i>Giao Trong Ngày Mai</a>
                                </div>
                                <div class="mt-2 text-xs text-gray-400 flex items-center"><i class="fas fa-info-circle mr-1"></i>Được đồng kiểm.</div>
                            </div>
                        </div>
                        <div class="flex justify-end items-center py-4">
                            <span class="text-base text-gray-600 mr-4">Tổng số tiền (1 sản phẩm):</span>
                            <span class="text-2xl text-red-500 font-bold">₫418.800</span>
                        </div>
                    </div>

                    <!-- Tổng kết đơn hàng & phương thức thanh toán (giống Shopee) -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                        <div class="mb-4">
                            <span class="font-semibold text-lg">Phương thức thanh toán</span>
                            <div class="flex gap-2 mt-3">
                                <button type="button" class="px-4 py-2 border border-gray-300 rounded bg-white text-gray-700 font-medium">Momo</button>
                                <button type="button" class="px-4 py-2 border border-gray-300 rounded bg-white text-gray-700 font-medium">Bank</button>
                                <button type="button" class="px-4 py-2 border-2 border-orange-500 rounded bg-orange-50 text-orange-600 font-semibold relative">Thanh toán khi nhận hàng <span class="absolute -right-2 -top-2 text-orange-500"><i class="fas fa-check"></i></span></button>
                            </div>
                        </div>
                        <!-- Box nội dung phương thức thanh toán, sẽ thay đổi theo lựa chọn -->
                        <div id="payment-cod-box">
                            <div class="border-t border-b py-4 flex items-center justify-between text-sm text-gray-700">
                                <div>
                                    <span class="font-medium">Thanh toán khi nhận hàng</span>
                                    <span class="ml-4 text-gray-500">Phí thu hộ: <span class="text-black font-semibold">₫0 VNĐ</span>. Ưu đãi về phí vận chuyển (nếu có) áp dụng cả với phí thu hộ.</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-start py-6">
                                <div class="flex-1"></div>
                                <div class="w-full max-w-xs">
                                    <div class="flex justify-between mb-2 text-gray-700 text-base">
                                        <span>Tổng tiền hàng</span>
                                        <span>₫553.000</span>
                                    </div>
                                    <div class="flex justify-between mb-2 text-gray-700 text-base">
                                        <span>Tổng tiền phí vận chuyển</span>
                                        <span>₫38.400</span>
                                    </div>
                                    <div class="flex justify-between mb-2 text-gray-700 text-base">
                                        <span>Tổng cộng Voucher giảm giá</span>
                                        <span class="text-red-500">-₫96.565</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="flex justify-between items-center text-lg font-bold">
                                        <span class="text-gray-700">Tổng thanh toán</span>
                                        <span class="text-2xl text-orange-500">₫494.835</span>
                                    </div>
                                    <button class="w-full mt-6 bg-orange-500 hover:bg-orange-600 text-white text-center px-6 py-3 rounded-md font-medium transition-colors text-lg">Đặt hàng</button>
                                </div>
                            </div>
                            <div class="border-t pt-4 text-sm text-gray-400">
                                Nhấn "Đặt hàng" đồng nghĩa với việc bạn đồng ý tuân theo <a href="#" class="text-blue-500 hover:underline">Điều khoản Shopee</a>
                            </div>
                        </div>
                        <!-- Box Momo -->
                        <div id="payment-momo-box" class="hidden">
                            <div class="border-t border-b py-4 flex items-center text-sm text-gray-700">
                                <span class="font-medium text-pink-600"><i class="fab fa-cc-visa mr-2"></i>Thanh toán qua ví Momo</span>
                                <span class="ml-4 text-gray-500">Vui lòng quét mã QR bên dưới bằng app Momo để thanh toán.</span>
                            </div>
                            <div class="flex flex-col items-center py-6">
                                <img src="https://img.vietqr.io/image/970422-123456789-compact2.png" alt="QR Momo" class="w-48 h-48 border rounded mb-4">
                                <div class="text-gray-600 text-sm mb-2">Số tiền: <span class="text-orange-500 font-bold">₫494.835</span></div>
                                <div class="text-xs text-gray-400">Sau khi thanh toán thành công, đơn hàng sẽ được xác nhận tự động.</div>
                            </div>
                        </div>
                        <!-- Box Bank -->
                        <div id="payment-vnpay-box" class="hidden">
                            <div class="border-t border-b py-4 flex items-center text-sm text-gray-700">
                                <span class="font-medium text-blue-600"><i class="fas fa-university mr-2"></i>Thanh toán qua Bank</span>
                                <span class="ml-4 text-gray-500">Chọn ngân hàng để thanh toán qua cổng Bank.</span>
                            </div>
                            <div class="py-6">
                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <button class="border border-gray-300 rounded p-3 flex flex-col items-center hover:border-blue-500">
                                        <img src="https://play-lh.googleusercontent.com/KBIgU6nz3hzia77BUj4FyVdL2azYvnttVkreRmc6c-asHof7ErHsY79G_yHdFkI83w" alt="VCB" class="w-10 h-10 mb-1">
                                        <span class="text-xs">Vietcombank</span>
                                    </button>
                                    <button class="border border-gray-300 rounded p-3 flex flex-col items-center hover:border-blue-500">
                                        <img src="https://yt3.googleusercontent.com/xsKeDcgwn9tAvfRSUcDC7rVvmolj8dUb1YAcrM2qT9E9IgYKuFvRjEj9Xe94XytbKzTRMU78=s900-c-k-c0x00ffffff-no-rj" alt="Agribank" class="w-10 h-10 mb-1">
                                        <span class="text-xs">Agribank</span>
                                    </button>
                                    <button class="border border-gray-300 rounded p-3 flex flex-col items-center hover:border-blue-500">
                                        <img src="https://diadiembank.com/wp-content/uploads/2024/11/icon-bidv-smartbanking.svg" alt="BIDV" class="w-10 h-10 mb-1">
                                        <span class="text-xs">BIDV</span>
                                    </button>
                                </div>
                                <div class="text-gray-600 text-sm mb-2">Số tiền: <span class="text-orange-500 font-bold">₫494.835</span></div>
                                <div class="text-xs text-gray-400">Sau khi thanh toán thành công, đơn hàng sẽ được xác nhận tự động.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

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
                        <label class="block text-xs text-gray-500 mb-1">Tỉnh/Thành phố, Quận/Huyện, Phường/Xã</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>Hà Nội, Quận Hoàng Mai, Phường Đại Kim</option>
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
                        <label class="block text-xs text-gray-500 mb-1">Tỉnh/Thành phố, Quận/Huyện, Phường/Xã</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>Hà Nội, Quận Hoàng Mai, Phường Đại Kim</option>
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

    <!-- Modal chọn phương thức vận chuyển (giống Shopee, đẹp) -->
    <div id="shippingModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
            <div class="px-8 pt-6 pb-2 border-b">
                <span class="text-xl font-semibold">Chọn phương thức vận chuyển</span>
            </div>
            <div class="px-8 pt-6 pb-2">
                <div class="flex gap-4 mb-4">
                    <button type="button" class="flex flex-col items-center justify-center border-2 border-orange-500 bg-orange-50 text-orange-600 rounded-lg px-6 py-3 font-semibold text-base focus:outline-none">
                        <i class="fas fa-shipping-fast fa-lg mb-1"></i>
                        Giao hàng tận nơi
                        <span class="text-xs font-normal text-gray-500 mt-1">Từ ₫14.850</span>
                    </button>
                    <button type="button" class="flex flex-col items-center justify-center border border-gray-300 text-gray-500 rounded-lg px-6 py-3 font-semibold text-base focus:outline-none">
                        <i class="fas fa-box-open fa-lg mb-1"></i>
                        Giao tới tủ giao nhận
                        <span class="text-xs font-normal text-gray-500 mt-1">Từ ₫18.300</span>
                    </button>
                </div>
                <div class="uppercase text-xs text-gray-400 font-semibold mb-2 tracking-wide flex items-center gap-2">
                    Phương thức vận chuyển liên kết với Shopee
                    <i class="fas fa-shield-alt text-orange-400"></i>
                </div>
                <div class="max-h-[50vh] overflow-y-auto space-y-3 pr-2">
                    <!-- Option 1 -->
                    <div class="relative bg-orange-50 border border-orange-400 rounded-lg px-6 py-4 flex items-center group">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-base text-gray-800">Nhanh</span>
                                <span class="text-gray-700 font-semibold">₫18.300</span>
                            </div>
                            <div class="text-xs text-gray-600 mt-1">Đảm bảo nhận hàng từ 28 Tháng 6 - 30 Tháng 6</div>
                            <div class="text-xs text-gray-400 mt-1">Nhận Voucher trị giá ₫15.000 nếu đơn hàng được giao đến bạn sau ngày 30 Tháng 6 2025.</div>
                        </div>
                        <span class="absolute top-1/2 right-6 -translate-y-1/2 text-orange-500"><i class="fas fa-check fa-lg"></i></span>
                    </div>
                    <!-- Option 2 -->
                    <div class="relative bg-white border border-gray-200 rounded-lg px-6 py-4 flex items-center group">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-base text-gray-800">Tiết kiệm</span>
                                <span class="text-gray-700 font-semibold">₫14.850</span>
                            </div>
                            <div class="text-xs text-gray-600 mt-1">Đảm bảo nhận hàng từ 28 Tháng 6 - 30 Tháng 6</div>
                            <div class="text-xs text-gray-400 mt-1">Nhận Voucher trị giá ₫15.000 nếu đơn hàng được giao đến bạn sau ngày 30 Tháng 6 2025.</div>
                        </div>
                    </div>
                    <!-- Option 3 (disabled) -->
                    <div class="relative bg-gray-100 border border-gray-200 rounded-lg px-6 py-4 flex items-center opacity-60 cursor-not-allowed">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-base text-gray-400">Hàng Cồng Kềnh</span>
                                <span class="text-gray-400 font-semibold">₫0</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-1">Dưới giới hạn kích thước tối thiểu</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-8 py-4 flex justify-end gap-3">
                <button type="button" id="shippingModalCancel" class="px-5 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">Trở Lại</button>
                <button type="button" class="px-5 py-2 rounded bg-orange-500 text-white font-semibold hover:bg-orange-600">Xác Nhận</button>
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
                fetch(`https://provinces.open-api.vn/api/p/${HANOI_PROVINCE_CODE}?depth=2`)
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
                    })
                    .catch(error => {
                        console.error('Error fetching districts:', error);
                        districtSelect.innerHTML = '<option value="">Không thể tải dữ liệu</option>';
                    });
            }

            // Fetch wards based on selected district
            function fetchWards() {
                // Reset ward select
                wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';

                const selectedDistrict = districtSelect.options[districtSelect.selectedIndex];
                if (!selectedDistrict || !selectedDistrict.dataset.code) {
                    return;
                }

                const districtCode = selectedDistrict.dataset.code;

                // Show loading state
                wardSelect.innerHTML = '<option value="">Đang tải...</option>';

                // Fetch wards from API
                fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
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
                    })
                    .catch(error => {
                        console.error('Error fetching wards:', error);
                        wardSelect.innerHTML = '<option value="">Không thể tải dữ liệu</option>';
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

            // Modal open/close logic cho chọn địa chỉ, cập nhật địa chỉ, thêm địa chỉ mới
            const addressModal = document.getElementById('addressModal');
            const openModalBtn = document.querySelector('a.text-blue-600');
            const closeModalBtn = document.getElementById('addressModalCancel');
            const updateBtns = addressModal ? addressModal.querySelectorAll('a.text-blue-600.font-medium') : [];
            const updateAddressModal = document.getElementById('updateAddressModal');
            const updateAddressBack = document.getElementById('updateAddressBack');
            const addAddressModal = document.getElementById('addAddressModal');
            const addAddressBtn = addressModal ? addressModal.querySelector('button.flex.items-center') : null;
            const addAddressBack = document.getElementById('addAddressBack');

            if (openModalBtn && addressModal) {
                openModalBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    addressModal.classList.remove('hidden');
                });
            }
            if (closeModalBtn && addressModal) {
                closeModalBtn.addEventListener('click', function() {
                    addressModal.classList.add('hidden');
                });
            }
            // Sự kiện mở modal cập nhật địa chỉ
            updateBtns.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    addressModal.classList.add('hidden');
                    updateAddressModal.classList.remove('hidden');
                });
            });
            // Sự kiện trở lại modal chọn địa chỉ
            if (updateAddressBack && updateAddressModal && addressModal) {
                updateAddressBack.addEventListener('click', function() {
                    updateAddressModal.classList.add('hidden');
                    addressModal.classList.remove('hidden');
                });
            }
            // Sự kiện mở modal thêm địa chỉ mới
            if (addAddressBtn && addAddressModal && addressModal) {
                addAddressBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    addressModal.classList.add('hidden');
                    addAddressModal.classList.remove('hidden');
                });
            }
            // Sự kiện trở lại modal chọn địa chỉ từ modal thêm mới
            if (addAddressBack && addAddressModal && addressModal) {
                addAddressBack.addEventListener('click', function() {
                    addAddressModal.classList.add('hidden');
                    addressModal.classList.remove('hidden');
                });
            }

            // Toggle voucher box
            const toggleVoucherBoxBtn = document.getElementById('toggleVoucherBox');
            const voucherBox = document.getElementById('voucherBox');
            if (toggleVoucherBoxBtn && voucherBox) {
                toggleVoucherBoxBtn.addEventListener('click', function() {
                    voucherBox.classList.toggle('hidden');
                });
            }

            // Modal open/close logic cho chọn phương thức vận chuyển
            const shippingModal = document.getElementById('shippingModal');
            const openShippingBtn = document.querySelector('button.shipping-change-btn');
            const closeShippingBtn = document.getElementById('shippingModalCancel');
            if (openShippingBtn && shippingModal) {
                openShippingBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    shippingModal.classList.remove('hidden');
                });
            }
            if (closeShippingBtn && shippingModal) {
                closeShippingBtn.addEventListener('click', function() {
                    shippingModal.classList.add('hidden');
                });
            }

            // Xử lý chuyển đổi giao diện phương thức thanh toán
            const paymentBtns = document.querySelectorAll('.flex.gap-2.mt-3 button');
            const momoBox = document.getElementById('payment-momo-box');
            const vnpayBox = document.getElementById('payment-vnpay-box');
            const codBox = document.getElementById('payment-cod-box');
            function hideAllPaymentBox() {
                momoBox.classList.add('hidden');
                vnpayBox.classList.add('hidden');
                codBox.classList.add('hidden');
            }
            paymentBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    hideAllPaymentBox();
                    if (btn.textContent.trim().includes('Momo')) {
                        momoBox.classList.remove('hidden');
                    } else if (btn.textContent.trim().includes('Bank')) {
                        vnpayBox.classList.remove('hidden');
                    } else if (btn.textContent.trim().includes('Thanh toán khi nhận hàng')) {
                        codBox.classList.remove('hidden');
                    }
                });
            });
        });
    </script>
@endsection

<style>
    /* Ẩn scrollbar cho modal cập nhật địa chỉ trên Chrome, Safari, Edge */
    #updateAddressModal .overflow-y-auto::-webkit-scrollbar {
        display: none;
    }

    /* Ẩn scrollbar cho modal chọn phương thức vận chuyển trên Chrome, Safari, Edge */
    #shippingModal .overflow-y-auto::-webkit-scrollbar {
        display: none;
    }
</style>

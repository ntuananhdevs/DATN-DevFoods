@extends('layouts.customer.fullLayoutMaster')

@section('title', 'Theo Dõi Đơn Hàng')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-orange-100 py-12">
    <div class="container mx-auto px-4 flex flex-col items-center">
        <div class="w-full sm:max-w-md lg:max-w-lg">
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-orange-500/90 rounded-full shadow-lg mb-6">
                    <svg class="w-12 h-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c.828 0 1.5-.672 1.5-1.5S12.828 5 12 5s-1.5.672-1.5 1.5S11.172 8 12 8zM12 9v6m-6 4h12a2 2 0 002-2V9a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <h1 class="text-4xl font-extrabold text-gray-800 tracking-wide mb-2">Theo Dõi Đơn Hàng</h1>
                <p class="text-gray-500 font-medium">Nhập mã đơn hàng để xem trạng thái</p>
            </div>

            <!-- Form Card -->
            <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl p-10 lg:p-12">
                <form method="POST" action="{{ route('customer.order.track.submit') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Order Code Input -->
                    <div>
                        <label for="order_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Mã đơn hàng <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="order_code" 
                                name="order_code" 
                                value="{{ old('order_code') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 text-center font-mono text-lg tracking-wider @error('order_code') border-red-500 @enderror"
                                placeholder="Nhập mã đơn hàng (VD: ABC12345)"
                                required
                                autocomplete="off"
                                style="text-transform: uppercase;"
                                maxlength="20"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        @error('order_code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Turnstile Component -->
                    <!-- Cloudflare Turnstile CAPTCHA -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 text-center">
                            Xác minh bảo mật <span class="text-red-500">*</span>
                        </label>
                        <div class="flex justify-center">
                            <div class="cf-turnstile"
                                 data-sitekey="{{ config('turnstile.site_key') }}"
                                 data-theme="{{ config('turnstile.theme') }}"
                                 data-size="{{ config('turnstile.size') }}"
                                 data-callback="onTurnstileCallback">
                            </div>
                        </div>
                        @error('cf-turnstile-response')
                            <div class="text-red-500 text-sm mt-1 text-center">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        data-turnstile-required
                        disabled
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold py-3 px-6 rounded-full transition-all duration-200 transform hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 opacity-50"
                    >
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Theo Dõi
                        </span>
                    </button>
                </form>

                <!-- Help Text -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-sm text-gray-500 mb-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Mã đơn được gửi qua email hoặc phần lịch sử đơn hàng
                        </p>
                        <p class="text-xs text-gray-400">
                            Bạn cần hỗ trợ? 
                            <a href="{{ route('contact.index') }}" class="text-orange-500 hover:text-orange-600 font-medium">
                                Liên hệ chúng tôi
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-4">
                <div class="bg-white/70 rounded-2xl p-5 shadow hover:shadow-md transition-all duration-150 flex flex-col items-center group">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-white rounded-full shadow-md mb-2">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-700 font-semibold group-hover:text-orange-600 transition-colors">Theo dõi thời gian thực</p>
                </div>
                <div class="bg-white/70 rounded-2xl p-5 shadow hover:shadow-md transition-all duration-150 flex flex-col items-center group">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-white rounded-full shadow-md mb-2">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-700 font-semibold group-hover:text-orange-600 transition-colors">Bảo mật cao</p>
                </div>
                <div class="bg-white/70 rounded-2xl p-5 shadow hover:shadow-md transition-all duration-150 flex flex-col items-center group">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-white rounded-full shadow-md mb-2">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-700 font-semibold group-hover:text-orange-600 transition-colors">Hỗ trợ 24/7</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Cloudflare Turnstile Script -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script>
        let turnstileToken = null;
        window.onTurnstileCallback = function(token){
            turnstileToken = token;
            document.querySelectorAll('[data-turnstile-required]').forEach(btn=>{
                btn.disabled = false;
                btn.classList.remove('opacity-50','cursor-not-allowed');
            });
        };
    </script>
@endpush 
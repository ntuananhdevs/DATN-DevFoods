@extends('layouts.driver.masterLayout') {{-- Đảm bảo layout này đã tải Tailwind CSS --}}

@section('title', 'Danh sách Đơn hàng')

@section('content')
    <div class="pt-4 p-4">
        {{-- Form tìm kiếm --}}
        <form action="{{ route('driver.orders.index') }}" method="GET" class="mb-4">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Tìm kiếm theo ID, địa chỉ, khách hàng..."
                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            {{-- Giữ nguyên input hidden cho tab để khi tìm kiếm không mất tab đang chọn --}}
            <input type="hidden" name="tab" value="{{ $currentTab }}">
        </form>

        {{-- Tabs trạng thái đơn hàng --}}
        <div class="flex space-x-2 mb-4 overflow-x-auto pb-2 custom-scrollbar">
            @foreach ($availableTabs as $key => $config)
                <a href="{{ route('driver.orders.index', ['tab' => $key, 'search' => request('search')]) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition 
                    {{ $currentTab === $key ? 'bg-blue-600 text-white shadow' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    {{ $config['label'] }} ({{ $config['count'] ?? 0 }})
                </a>
            @endforeach
        </div>

        {{-- Order List --}}
        <div class="space-y-3" id="orders-list-container">
            @forelse ($orders as $order)
                <a href="{{ route('driver.orders.show', $order->id) }}"
                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                    <div class="flex-shrink-0">
                        {{-- Sử dụng status_icon và status_color từ Order Model --}}
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-lg"
                            style="background-color: {{ $order->status_color['bg'] }};">
                            <i class="{{ $order->status_icon }}"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-base">Đơn hàng #{{ $order->order_code }}</h3>
                        <p class="text-sm text-gray-600">{{ $order->delivery_address }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{-- Dùng accessor status_text --}}
                            <span class="font-medium"
                                style="color: {{ $order->status_color['text'] }};">{{ $order->status_text }}</span>
                            @if ($order->estimated_delivery_time)
                                <span class="ml-2 text-xs text-gray-500"><i class="far fa-clock"></i>
                                    {{ $order->estimated_delivery_time->format('H:i, d/m') }}</span>
                            @endif
                        </p>
                    </div>

                    <div class="flex-shrink-0 text-gray-300">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
            @empty
                <div class="text-center text-gray-500 py-16">
                    <i class="fas fa-box-open text-5xl mb-4 text-gray-300"></i>
                    <p class="font-medium">Không có đơn hàng nào</p>
                    <p class="text-sm">Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm.</p>
                </div>
            @endforelse
        </div>

        @if ($orders->hasPages())
            <div class="mt-6">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        /* Optional: Custom scrollbar for tab navigation */
        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Live search / Tab filtering via AJAX
            const searchInput = document.querySelector('input[name="search"]');
            const tabLinks = document.querySelectorAll('.flex.space-x-2 a');
            const ordersListContainer = document.getElementById('orders-list-container');
            const currentTabInput = document.querySelector('input[name="tab"]');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


            let typingTimer;
            const doneTypingInterval = 500; // 0.5 seconds

            if (searchInput) {
                searchInput.addEventListener('keyup', () => {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(fetchOrders, doneTypingInterval);
                });
                searchInput.addEventListener('keydown', () => {
                    clearTimeout(typingTimer);
                });
            }

            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const newTab = this.getAttribute('href').split('tab=')[1].split('&')[0];
                    currentTabInput.value = newTab; // Update hidden tab input
                    // Update URL without full page reload
                    history.pushState(null, '', this.href);
                    fetchOrders();

                    // Update active tab styling
                    tabLinks.forEach(tabLink => {
                        tabLink.classList.remove('bg-blue-600', 'text-white', 'shadow');
                        tabLink.classList.add('bg-gray-200', 'text-gray-700',
                            'hover:bg-gray-300');
                    });
                    this.classList.add('bg-blue-600', 'text-white', 'shadow');
                    this.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                });
            });

            function fetchOrders() {
                const search = searchInput ? searchInput.value : '';
                const tab = currentTabInput ? currentTabInput.value : 'all';
                const url = `{{ route('driver.orders.index') }}?tab=${tab}&search=${search}`;

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest', // Important for Laravel's AJAX detection
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (ordersListContainer) {
                            ordersListContainer.innerHTML = data.orders; // Replace the list content
                        }
                        // Update tab counts
                        for (const tabKey in data.tabCounts) {
                            const tabElement = document.querySelector(`a[href*="tab=${tabKey}"]`);
                            if (tabElement) {
                                tabElement.textContent =
                                    `${data.availableTabs[tabKey].label} (${data.tabCounts[tabKey]})`;
                            }
                        }
                    })
                    .catch(error => console.error('Error fetching orders:', error));
            }

            // Initial fetch to ensure counts are correct if page loaded without AJAX
            // fetchOrders(); // Uncomment if you want to initially load via AJAX
        });
    </script>
@endpush

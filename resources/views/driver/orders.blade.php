@extends('layouts.driver.masterLayout')

@section('title', 'Quản lý Đơn hàng - DevFoods Driver')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-100 via-gray-50 to-slate-100 p-4 md:p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Quản lý Đơn hàng</h1>
            <p class="text-gray-600 mt-1">Theo dõi và quản lý tất cả đơn hàng của bạn.</p>
        </div>

        <!-- Search and Filters -->
        <div class="mb-6 p-5 bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="text" class="block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm placeholder-gray-500 text-gray-800" placeholder="Tìm kiếm đơn hàng (Mã, Khách hàng, Địa chỉ...)" data-search="orders">
                </div>
                <div class="flex items-center space-x-2">
                    <button class="p-3 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3 3a1 1 0 000 2h14a1 1 0 100-2H3zm0 4a1 1 0 000 2h14a1 1 0 100-2H3zm0 4a1 1 0 000 2h14a1 1 0 100-2H3zm0 4a1 1 0 000 2h14a1 1 0 100-2H3z" clip-rule="evenodd"></path></svg>
                    </button>
                    <select class="py-3 px-4 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm bg-white text-gray-800">
                        <option>Sắp xếp theo: Mới nhất</option>
                        <option>Sắp xếp theo: Phí ship cao</option>
                        <option>Sắp xếp theo: Khoảng cách gần</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Status Tabs -->
        <div class="mb-6 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <nav class="flex flex-wrap -mb-px" aria-label="Tabs">
                @php
                    $tabs = [
                        'pending' => ['label' => 'Chờ nhận', 'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>', 'color' => 'blue'],
                        'delivering' => ['label' => 'Đang giao', 'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>', 'color' => 'yellow'],
                        'completed' => ['label' => 'Hoàn thành', 'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>', 'color' => 'green'],
                        'cancelled' => ['label' => 'Đã hủy', 'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>', 'color' => 'red'],
                    ];
                @endphp

                @foreach($tabs as $statusKey => $tab)
                    <button data-tab="{{ $statusKey }}" 
                            class="tab-button flex items-center justify-center font-medium text-sm whitespace-nowrap py-4 px-5 md:px-6 
                                   {{ $loop->first ? 'text-'.$tab['color'].'-600 border-'.$tab['color'].'-500 border-b-2' : 'text-gray-500 hover:text-'.$tab['color'].'-600 hover:border-'.$tab['color'].'-500 border-transparent border-b-2' }} 
                                   focus:outline-none transition-all duration-150 ease-in-out tab-inactive">
                        {!! $tab['icon'] !!}
                        {{ $tab['label'] }}
                        <span class="ml-2 bg-{{ $tab['color'] }}-100 text-{{ $tab['color'] }}-700 py-0.5 px-2 rounded-full text-xs font-semibold">{{ count($orders[$statusKey] ?? []) }}</span>
                    </button>
                @endforeach
            </nav>
        </div>


        <!-- Tab Content -->
        <div class="space-y-6">
        @foreach(['pending' => 'Chờ nhận', 'delivering' => 'Đang giao', 'completed' => 'Đã hoàn thành', 'cancelled' => 'Đã hủy'] as $statusKey => $statusLabel)
        <div data-tab-content="{{ $statusKey }}" class="{{ $loop->first ? '' : 'hidden' }}">
            @if(isset($orders[$statusKey]) && count($orders[$statusKey]) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($orders[$statusKey] as $order)
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1" data-searchable="orders">
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-bold text-lg text-gray-800">Mã đơn: <span class="text-blue-600">#{{ $order['id'] }}</span></h4>
                                        <p class="text-xs text-gray-500 flex items-center mt-1">
                                            <svg class="w-3 h-3 mr-1.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                            {{ $order['order_time'] }}
                                        </p>
                                    </div>
                                    @php
                                        $statusConfig = [
                                            'pending' => ['text' => 'Chờ nhận', 'color' => 'blue', 'icon' => '<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a.75.75 0 01.75.75v6.5a.75.75 0 01-1.5 0v-6.5A.75.75 0 0110 2zM9.25 9.75a.75.75 0 000 1.5h1.5a.75.75 0 000-1.5h-1.5zM10 18a8 8 0 100-16 8 8 0 000 16zM9 6.75a.75.75 0 00-1.5 0v.09c-1.13.26-2.003 1.203-2.003 2.41a.75.75 0 001.5 0c0-.56.314-1.054.768-1.317L9 6.75zM13 8.25a.75.75 0 00-1.5 0v.09c-1.13.26-2.003 1.203-2.003 2.41a.75.75 0 001.5 0c0-.56.314-1.054.768-1.317L13 8.25z"></path></svg>'],
                                            'delivering' => ['text' => 'Đang giao', 'color' => 'yellow', 'icon' => '<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M3.5 3.75a.25.25 0 01.25-.25h3.5a.25.25 0 01.25.25v3.5a.25.25 0 01-.25.25h-3.5a.25.25 0 01-.25-.25v-3.5zM12.5 3.75a.25.25 0 01.25-.25h3.5a.25.25 0 01.25.25v3.5a.25.25 0 01-.25.25h-3.5a.25.25 0 01-.25-.25v-3.5zM3.5 12.75a.25.25 0 01.25-.25h3.5a.25.25 0 01.25.25v3.5a.25.25 0 01-.25.25h-3.5a.25.25 0 01-.25-.25v-3.5zM12.5 12.75a.25.25 0 01.25-.25h3.5a.25.25 0 01.25.25v3.5a.25.25 0 01-.25.25h-3.5a.25.25 0 01-.25-.25v-3.5z"></path></svg>'],
                                            'completed' => ['text' => 'Hoàn thành', 'color' => 'green', 'icon' => '<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"></path></svg>'],
                                            'cancelled' => ['text' => 'Đã hủy', 'color' => 'red', 'icon' => '<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"></path></svg>'],
                                            'default' => ['text' => 'Không xác định', 'color' => 'gray', 'icon' => '']
                                        ];
                                        $currentStatus = $statusConfig[$order['status_key']] ?? $statusConfig['default'];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-{{ $currentStatus['color'] }}-100 text-{{ $currentStatus['color'] }}-700">
                                        {!! $currentStatus['icon'] !!}
                                        {{ $currentStatus['text'] }}
                                    </span>
                                </div>
                                
                                <div class="space-y-2.5 text-sm text-gray-700">
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 mr-2.5 mt-0.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <div>
                                            <span class="font-medium text-gray-600">Lấy hàng:</span> {{ $order['pickup_branch'] }}
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 mr-2.5 mt-0.5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <div>
                                            <span class="font-medium text-gray-600">Giao đến:</span> {{ $order['delivery_address'] }}
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2.5 text-teal-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span class="font-medium text-gray-600">Khách:</span>&nbsp;{{ $order['customer_name'] }} - {{ $order['customer_phone'] }}
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-100 pt-4 mt-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <div>
                                            <span class="text-xs text-gray-500">Phí ship</span>
                                            <p class="font-bold text-green-600 text-lg">{{ number_format($order['shipping_fee']) }}đ</p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500">Khoảng cách</span>
                                            <p class="font-semibold text-gray-700 text-md text-right">{{ $order['distance'] }} km</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('driver.orders.detail', $order['id']) }}" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-2.5 px-4 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 flex items-center justify-center font-semibold shadow-md hover:shadow-lg">
                                        Xem chi tiết
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 7l5 5-5 5"></path></svg>
                                    </a>
                                    @if($statusKey == 'pending')
                                    <button class="mt-2 w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white py-2.5 px-4 rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-300 flex items-center justify-center font-semibold shadow-md hover:shadow-lg">
                                        Chấp nhận đơn
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-xl shadow-md border border-gray-200">
                    <svg class="w-20 h-20 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-xl font-semibold text-gray-700 mb-2">Không có đơn hàng nào</p>
                    <p class="text-gray-500">Hiện tại không có đơn hàng nào trong mục <span class="font-medium">"{{ strtolower($statusLabel) }}"</span>.</p>
                </div>
            @endif
        </div>
        @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.tab-button'); // Use class selector
    const tabContents = document.querySelectorAll('[data-tab-content]');
    const searchInput = document.querySelector('[data-search="orders"]');
    const tabConfig = @json($tabs ?? []); // Ensure $tabs is passed from controller

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const target = this.dataset.tab;

            tabs.forEach(t => {
                t.classList.remove('text-blue-600', 'border-blue-500', 'text-yellow-600', 'border-yellow-500', 'text-green-600', 'border-green-500', 'text-red-600', 'border-red-500', 'border-b-2');
                t.classList.add('text-gray-500', 'hover:text-' + (tabConfig[t.dataset.tab]?.color || 'gray') + '-600', 'hover:border-' + (tabConfig[t.dataset.tab]?.color || 'gray') + '-500', 'border-transparent');
            });
            
            if(tabConfig[target]){
                this.classList.add('text-' + tabConfig[target].color + '-600', 'border-' + tabConfig[target].color + '-500', 'border-b-2');
                this.classList.remove('text-gray-500', 'hover:text-' + tabConfig[target].color + '-600', 'hover:border-' + tabConfig[target].color + '-500', 'border-transparent');
            }

            tabContents.forEach(content => {
                if (content.dataset.tabContent === target) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });
        });
    });

    // Initialize first tab as active
    if (tabs.length > 0) {
        const firstTab = tabs[0];
        const firstTabTarget = firstTab.dataset.tab;
        
        if(tabConfig[firstTabTarget]){
            firstTab.classList.add('text-' + tabConfig[firstTabTarget].color + '-600', 'border-' + tabConfig[firstTabTarget].color + '-500', 'border-b-2');
            firstTab.classList.remove('text-gray-500', 'hover:text-' + tabConfig[firstTabTarget].color + '-600', 'hover:border-' + tabConfig[firstTabTarget].color + '-500', 'border-transparent');
        }
        
        tabContents.forEach(content => {
            if (content.dataset.tabContent === firstTabTarget) {
                content.classList.remove('hidden');
            } else {
                content.classList.add('hidden');
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const activeTabContent = document.querySelector('[data-tab-content]:not(.hidden)');
            if (!activeTabContent) return;

            const searchableItems = activeTabContent.querySelectorAll('[data-searchable="orders"]');
            let hasVisibleItems = false;

            searchableItems.forEach(item => {
                const orderIdElement = item.querySelector('h4 span');
                const customerElement = item.querySelector('.flex.items-center svg.text-teal-500 + span');
                const deliveryAddressElement = item.querySelector('.flex.items-start svg.text-red-500 + div');

                const orderId = orderIdElement ? orderIdElement.textContent.toLowerCase() : '';
                // Extract customer name and phone correctly
                let customerText = '';
                if (customerElement && customerElement.nextSibling) {
                    customerText = (customerElement.nextSibling.textContent || '').toLowerCase();
                }
                
                const deliveryAddress = deliveryAddressElement ? deliveryAddressElement.textContent.toLowerCase().replace('giao đến:', '').trim() : '';
                
                if (orderId.includes(searchTerm) || 
                    customerText.includes(searchTerm) || 
                    deliveryAddress.includes(searchTerm)) {
                    item.style.display = '';
                    hasVisibleItems = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show/hide no results message for the current tab
            const noResultsMessage = activeTabContent.querySelector('.text-center.py-16'); // Assuming this is the no orders message container
            const orderGrid = activeTabContent.querySelector('.grid');

            if (noResultsMessage && orderGrid) {
                 if (!hasVisibleItems && searchTerm !== '') {
                    orderGrid.classList.add('hidden'); // Hide the grid if it exists
                    // You might want to show a specific "no search results" message here
                    // For now, let's assume the existing "no orders" message can be adapted or a new one added
                    // Example: if (!activeTabContent.querySelector('.no-search-results')) { activeTabContent.innerHTML += '<p class="no-search-results">No results found.</p>'; }
                } else {
                    orderGrid.classList.remove('hidden');
                    // Remove any "no search results" message if you added one
                }
            }
        });
    }
});
</script>

@endsection


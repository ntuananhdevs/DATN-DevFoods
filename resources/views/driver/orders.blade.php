@extends('layouts.driver.masterLayout')

@section('title', 'Đơn hàng')

@section('content')
    <div class="p-4 md:p-6">
        <div class="relative mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="search" placeholder="Tìm kiếm đơn hàng (mã, tên, địa chỉ)..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pl-8" id="order-search-input" />
        </div>

        <div class="w-full">
            <div class="inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground grid w-full grid-cols-2 md:grid-cols-4 h-auto" role="tablist">
                @foreach($tabStatuses as $status)
                    <button type="button" role="tab" aria-selected="{{ $status === $initialStatus ? 'true' : 'false' }}" aria-controls="tab-content-{{ Str::slug($status) }}" data-state="{{ $status === $initialStatus ? 'active' : 'inactive' }}" class="inline-flex items-center justify-center whitespace-normal rounded-sm px-3 py-2 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm py-2 text-xs md:text-sm tab-trigger" data-tab="{{ $status }}">
                        {{ $status }} (<span id="count-{{ Str::slug($status) }}">{{ $statusCounts[$status] ?? 0 }}</span>)
                    </button>
                @endforeach
            </div>
            @foreach($tabStatuses as $status)
                <div data-state="{{ $status === $initialStatus ? 'active' : 'inactive' }}" role="tabpanel" id="tab-content-{{ Str::slug($status) }}" class="mt-4 space-y-4 tab-content {{ $status === $initialStatus ? '' : 'hidden' }}">
                    @if(count($ordersByStatus[$status]) > 0)
                        @foreach($ordersByStatus[$status] as $order)
                            @include('partials.driver.order-card', ['order' => $order])
                        @endforeach
                    @else
                        <p class="text-muted-foreground text-center py-8">Không có đơn hàng nào.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabTriggers = document.querySelectorAll('.tab-trigger');
            const tabContents = document.querySelectorAll('.tab-content');
            const searchInput = document.getElementById('order-search-input');

            function activateTab(tabName) {
                tabTriggers.forEach(trigger => {
                    if (trigger.dataset.tab === tabName) {
                        trigger.setAttribute('data-state', 'active');
                        trigger.setAttribute('aria-selected', 'true');
                    } else {
                        trigger.setAttribute('data-state', 'inactive');
                        trigger.setAttribute('aria-selected', 'false');
                    }
                });

                tabContents.forEach(content => {
                    if (content.id === `tab-content-${tabName.toLowerCase().replace(/\s/g, '-')}`) {
                        content.setAttribute('data-state', 'active');
                        content.classList.remove('hidden');
                    } else {
                        content.setAttribute('data-state', 'inactive');
                        content.classList.add('hidden');
                    }
                });
                filterOrders(); // Re-filter when tab changes
            }

            tabTriggers.forEach(trigger => {
                trigger.addEventListener('click', function() {
                    const tabName = this.dataset.tab;
                    activateTab(tabName);
                });
            });

            // Initial tab activation based on URL parameter or default
            const initialStatus = "{{ $initialStatus }}";
            activateTab(initialStatus);

            // Client-side search filtering
            function filterOrders() {
                const searchTerm = searchInput.value.toLowerCase();
                const activeTabContent = document.querySelector('.tab-content[data-state="active"]');
                if (!activeTabContent) return;

                const orderCards = activeTabContent.querySelectorAll('.order-card-container'); // Assuming order-card is wrapped in a div with this class
                let visibleCount = 0;

                orderCards.forEach(card => {
                    const orderId = card.querySelector('.order-id')?.textContent.toLowerCase() || '';
                    const customerName = card.querySelector('.customer-name')?.textContent.toLowerCase() || '';
                    const deliveryAddress = card.querySelector('.delivery-address')?.textContent.toLowerCase() || '';

                    if (orderId.includes(searchTerm) || customerName.includes(searchTerm) || deliveryAddress.includes(searchTerm)) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                const noOrdersMessage = activeTabContent.querySelector('.no-orders-message');
                if (visibleCount === 0) {
                    if (!noOrdersMessage) {
                        const p = document.createElement('p');
                        p.className = 'text-muted-foreground text-center py-8 no-orders-message';
                        p.textContent = 'Không có đơn hàng nào.';
                        activeTabContent.appendChild(p);
                    }
                } else {
                    if (noOrdersMessage) {
                        noOrdersMessage.remove();
                    }
                }
            }

            searchInput.addEventListener('input', filterOrders);

            // Add a class to the order card partial for easier selection in JS
            // This needs to be added to resources/views/driver/partials/order-card.blade.php
            // <div class="order-card-container">...</div>
            // Also, add classes to elements within order-card for search:
            // <span class="order-id">...</span>
            // <span class="customer-name">...</span>
            // <div class="delivery-address">...</div>
        });
    </script>
@endsection
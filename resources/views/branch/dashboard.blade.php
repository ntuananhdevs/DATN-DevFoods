@extends('layouts.branch.contentLayoutMaster')

@section('title', 'eCommerce Dashboard')
@section('description', 'Tổng quan về hoạt động bán hàng và doanh thu')

@section('styles')
<style>
    @keyframes highlightNewOrder {
        0% {
            background-color: #fef3c7;
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
        }
        50% {
            background-color: #fbbf24;
            transform: scale(1.02);
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
        }
        100% {
            background-color: transparent;
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
        }
    }
    
    .order-card {
        transition: all 0.3s ease;
    }
    
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
</style>
@endsection

@section('content')
<div class="fade-in">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mt-0">
        <div>
            <p class="text-muted-foreground">Tổng quan về hoạt động bán hàng và doanh thu</p>
        </div>
        <div class="flex items-center gap-2">
            <select class="border rounded-md px-3 py-2 bg-background text-sm">
                <option value="7d">7 ngày qua</option>
                <option value="30d" selected>30 ngày qua</option>
                <option value="90d">90 ngày qua</option>
                <option value="1y">1 năm qua</option>
            </select>
            <button class="btn btn-primary">Xuất báo cáo</button>
        </div>
    </div>

    <div class="mt-6">
        <div class="flex border-b space-x-1">
            <button class="px-4 py-2 text-sm font-medium border-b-2 border-primary" id="tab-overview">Tổng quan</button>
            <button class="px-4 py-2 text-sm font-medium text-muted-foreground" id="tab-products">Sản phẩm</button>
            <button class="px-4 py-2 text-sm font-medium text-muted-foreground" id="tab-orders">Đơn hàng</button>
            <button class="px-4 py-2 text-sm font-medium text-muted-foreground" id="tab-customers">Khách hàng</button>
        </div>
        
        <div id="content-overview" class="space-y-4 mt-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="card overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Doanh thu</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">{{ number_format($totalRevenue) }}đ</div>
                        <div class="flex items-center text-xs {{ $revenueGrowth >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                @if($revenueGrowth >= 0)
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                                @else
                                <path d="M12 19V5"></path>
                                <path d="m5 12 7 7 7-7"></path>
                                @endif
                            </svg>
                            <span>{{ number_format(abs($revenueGrowth), 1) }}% so với tháng trước</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gradient-to-r from-green-500 to-emerald-500"></div>
                </div>
                
                <div class="card overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Đơn hàng</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <circle cx="8" cy="21" r="1"></circle>
                                <circle cx="19" cy="21" r="1"></circle>
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">{{ number_format($orderCount) }}</div>
                        <div class="flex items-center text-xs {{ $orderGrowth >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                @if($orderGrowth >= 0)
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                                @else
                                <path d="M12 19V5"></path>
                                <path d="m5 12 7 7 7-7"></path>
                                @endif
                            </svg>
                            <span>{{ number_format(abs($orderGrowth), 1) }}% so với tháng trước</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gradient-to-r from-blue-500 to-violet-500"></div>
                </div>
                
                <div class="card overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Giá trị đơn hàng TB</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                                <path d="M3 6h18"></path>
                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">{{ number_format($averageOrderValue) }}đ</div>
                        <div class="flex items-center text-xs text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>Trung bình mỗi đơn hàng</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gradient-to-r from-amber-500 to-orange-500"></div>
                </div>
                
                <div class="card overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium">Tỷ lệ chuyển đổi</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="m17 14 3 3-3 3"></path>
                                <path d="M22 17h-8"></path>
                                <path d="m7 10-3-3 3-3"></path>
                                <path d="M2 7h8"></path>
                                <path d="M22 7H12"></path>
                                <path d="M12 17H2"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">{{ number_format($conversionRate, 2) }}%</div>
                        <div class="flex items-center text-xs text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="m5 12 7-7 7 7"></path>
                                <path d="M12 19V5"></path>
                            </svg>
                            <span>Tỷ lệ hoàn thành đơn hàng</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gradient-to-r from-rose-500 to-pink-500"></div>
                </div>
            </div>
            
            <div class="grid gap-4 md:grid-cols-7">
                <div class="card col-span-7 md:col-span-4">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Doanh thu theo tháng</h3>
                        <p class="text-sm text-muted-foreground">Biểu đồ hiển thị doanh thu theo từng tháng trong năm</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <canvas id="monthlyRevenueChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="card col-span-7 md:col-span-3">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Doanh thu theo danh mục</h3>
                        <p class="text-sm text-muted-foreground">Phân bổ doanh thu theo danh mục sản phẩm</p>
                        <div class="mt-4 h-[300px] bg-muted/20 rounded-md flex items-center justify-center">
                            <canvas id="categoryRevenueChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid gap-4 md:grid-cols-2">
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Top sản phẩm bán chạy</h3>
                        <p class="text-sm text-muted-foreground">Các sản phẩm có doanh số cao nhất trong tháng</p>
                        <div class="mt-4 space-y-4">
                            @forelse($topProducts as $product)
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-md bg-muted flex items-center justify-center">
                                    <span class="text-xs">{{ strtoupper(substr($product->name, 0, 2)) }}</span>
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">{{ $product->name }}</p>
                                    <p class="text-sm text-muted-foreground">{{ number_format($product->base_price) }}đ</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">{{ $product->sold_count ?? 0 }} đã bán</p>
                                    <div class="flex items-center text-xs text-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                            <path d="m5 12 7-7 7 7"></path>
                                            <path d="M12 19V5"></path>
                                        </svg>
                                        <span>{{ number_format($product->total_revenue ?? 0) }}đ</span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <p class="text-sm text-muted-foreground">Chưa có dữ liệu sản phẩm bán chạy</p>
                            </div>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-ghost w-full flex items-center justify-center">
                                Xem tất cả sản phẩm
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1">
                                    <path d="M5 12h14"></path>
                                    <path d="m12 5 7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Đơn hàng gần đây</h3>
                        <p class="text-sm text-muted-foreground">Các đơn hàng mới nhất trong hệ thống</p>
                        <div class="mt-4 space-y-4">
                            @forelse($recentOrders as $order)
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-muted flex items-center justify-center overflow-hidden">
                                    @if(isset($order->customer) && !empty($order->customer->avatar))
                                        <img src="{{ asset($order->customer->avatar) }}" alt="avatar" class="w-full h-full object-cover">
                                    @elseif(isset($order->customer) && !empty($order->customer->name))
                                        <span class="text-xs font-bold">
                                            {{ strtoupper(mb_substr($order->customer->name, 0, 1, 'UTF-8')) }}
                                        </span>
                                    @else
                                        <span class="text-xs font-bold">KH</span>
                                    @endif
                                </div>
                                <div class="ml-4 space-y-1 flex-1">
                                    <p class="text-sm font-medium leading-none">{{ $order->customer->name ?? 'Khách hàng' }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $order->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-sm font-medium">{{ number_format($order->total_amount) }}đ</p>
                                    @php
                                        $statusColors = [
                                            'pending' => 'blue',
                                            'processing' => 'blue', 
                                            'shipping' => 'amber',
                                            'completed' => 'green',
                                            'cancelled' => 'red'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Chờ xử lý',
                                            'processing' => 'Đang xử lý',
                                            'shipping' => 'Đang giao',
                                            'completed' => 'Hoàn thành',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        $color = $statusColors[$order->status] ?? 'gray';
                                        $label = $statusLabels[$order->status] ?? $order->status;
                                    @endphp
                                    <span class="inline-flex items-center rounded-full border border-{{ $color }}-500 px-2.5 py-0.5 text-xs font-medium text-{{ $color }}-500">
                                        {{ $label }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <p class="text-sm text-muted-foreground">Chưa có đơn hàng nào</p>
                            </div>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-ghost w-full flex items-center justify-center">
                                Xem tất cả đơn hàng
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1">
                                    <path d="M5 12h14"></path>
                                    <path d="m12 5 7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid gap-4 md:grid-cols-3">
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-sm font-medium">Mục tiêu doanh thu</h3>
                        <div class="mt-2">
                            <div class="text-2xl font-bold">{{ number_format($targetProgress, 1) }}%</div>
                            <div class="mt-2 progress">
                                <div class="progress-bar" style="width: {{ min($targetProgress, 100) }}%"></div>
                            </div>
                            <p class="mt-2 text-xs text-muted-foreground">{{ number_format($currentMonthRevenue) }}/{{ number_format($monthlyTarget) }} đồng mục tiêu tháng</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-sm font-medium">Đơn hàng đang xử lý</h3>
                        <div class="mt-2 flex items-center justify-between">
                            <div>
                                <div class="text-2xl font-bold">{{ $pendingOrdersToday }}</div>
                                <p class="text-xs text-muted-foreground">Cần xử lý trong ngày hôm nay</p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <circle cx="12" cy="12" r="2"></circle>
                                <path d="M12 8v-2"></path>
                                <path d="M12 16v2"></path>
                                <path d="m16 12 2 1"></path>
                                <path d="M8 12 6 11"></path>
                                <path d="m16 8-1.5 1"></path>
                                <path d="M9.5 15 8 16"></path>
                                <path d="m16 16-1.5-1"></path>
                                <path d="M9.5 9 8 8"></path>
                                <rect x="2" y="2" width="20" height="20" rx="5"></rect>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-sm font-medium">Khuyến mãi đang chạy</h3>
                        <div class="mt-2 flex items-center justify-between">
                            <div>
                                <div class="text-2xl font-bold">{{ $activePromotions }}</div>
                                <p class="text-xs text-muted-foreground">Chương trình khuyến mãi đang hoạt động</p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="content-products" class="hidden mt-4">
            <div class="h-[400px] flex items-center justify-center bg-muted/20 rounded-md">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-muted">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                        <path d="M3 6h18"></path>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium">Phân tích sản phẩm</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Thông tin chi tiết về sản phẩm sẽ hiển thị ở đây</p>
                </div>
            </div>
        </div>
        
        <div id="content-orders" class="hidden mt-4">
            <div class="h-[400px] flex items-center justify-center bg-muted/20 rounded-md">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-muted">
                        <path d="m7.5 4.27 9 5.15"></path>
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 22V12"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium">Phân tích đơn hàng</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Thông tin chi tiết về đơn hàng sẽ hiển thị ở đây</p>
                </div>
            </div>
        </div>
        
        <div id="content-customers" class="hidden mt-4">
            <div class="h-[400px] flex items-center justify-center bg-muted/20 rounded-md">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-muted">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium">Phân tích khách hàng</h3>
                    <p class="mt-2 text-sm text-muted-foreground">Thông tin chi tiết về khách hàng sẽ hiển thị ở đây</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('[id^="tab-"]');
        const contents = document.querySelectorAll('[id^="content-"]');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                tabs.forEach(t => {
                    t.classList.remove('border-b-2', 'border-primary');
                    t.classList.add('text-muted-foreground');
                });
                
                // Add active class to clicked tab
                this.classList.add('border-b-2', 'border-primary');
                this.classList.remove('text-muted-foreground');
                
                // Hide all content
                contents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Show corresponding content
                const contentId = this.id.replace('tab-', 'content-');
                document.getElementById(contentId).classList.remove('hidden');
            });
        });

        // Render chart for monthly revenue
        const monthlyCtx = document.getElementById('monthlyRevenueChart');
        if (monthlyCtx) {
            const monthlyChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_column($monthlyRevenue, 'month')) !!},
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: {!! json_encode(array_column($monthlyRevenue, 'revenue')) !!},
                        backgroundColor: 'rgba(16, 185, 129, 0.5)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Render chart for category revenue
        const categoryCtx = document.getElementById('categoryRevenueChart');
        if (categoryCtx) {
            const categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryRevenue->pluck('category_name')->toArray()) !!},
                    datasets: [{
                        data: {!! json_encode($categoryRevenue->pluck('total_revenue')->toArray()) !!},
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(236, 72, 153, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw;
                                    return label + ': ' + new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

@endsection

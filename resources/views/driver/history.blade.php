@extends('layouts.driver.masterLayout')

@section('title', 'Lịch sử giao hàng')

@section('content')
    <div class="p-4 md:p-6 space-y-6">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-4">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Tổng kết thu nhập và hiệu suất</h3>
            </div>
            <div class="p-4 pt-0 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 bg-muted rounded-lg text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8 text-green-600 mx-auto mb-2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    <p class="text-2xl font-bold">{{ number_format($totalEarnings, 0, ',', '.') }}đ</p>
                    <p class="text-sm text-muted-foreground">
                        Tổng thu nhập (
                        @if($filter === 'all') Toàn bộ
                        @elseif($filter === 'today') Hôm nay
                        @elseif($filter === 'week') Tuần này
                        @elseif($filter === 'month') Tháng này
                        @endif
                        )
                    </p>
                </div>
                <div class="p-4 bg-muted rounded-lg text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8 text-blue-600 mx-auto mb-2"><path d="M7 10v.01"/><path d="M10 10v.01"/><path d="M13 10v.01"/><path d="M16 10v.01"/><path d="M7 14v.01"/><path d="M10 14v.01"/><path d="M13 14v.01"/><path d="M16 14v.01"/><path d="M7 18v.01"/><path d="M10 18v.01"/><path d="M13 18v.01"/><path d="M16 18v.01"/><path d="M18 6V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v2"/><path d="M10 2v4"/><path d="M14 2v4"/><path d="M17 22H7a2 2 0 0 1-2-2V7.82a2 2 0 0 1 .82-1.57L7 5"/><path d="M17 22H7a2 2 0 0 1-2-2V7.82a2 2 0 0 1 .82-1.57L7 5"/></svg>
                    <p class="text-2xl font-bold">{{ $totalOrders }}</p>
                    <p class="text-sm text-muted-foreground">Tổng số đơn</p>
                </div>
                <div class="p-4 bg-muted rounded-lg text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8 text-yellow-500 mx-auto mb-2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <p class="text-2xl font-bold">{{ $averageRating }}</p>
                    <p class="text-sm text-muted-foreground">Đánh giá trung bình</p>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold">Lịch sử giao hàng</h2>
            <select id="history-filter" class="flex h-10 w-[180px] items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>Toàn bộ</option>
                <option value="today" {{ $filter === 'today' ? 'selected' : '' }}>Hôm nay</option>
                <option value="week" {{ $filter === 'week' ? 'selected' : '' }}>Tuần này</option>
                <option value="month" {{ $filter === 'month' ? 'selected' : '' }}>Tháng này</option>
            </select>
        </div>

        <div id="history-list-container" class="space-y-4">
            @if(count($filteredHistory) > 0)
                @foreach($filteredHistory as $entry)
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-4 pb-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-md font-semibold tracking-tight">Đơn hàng: {{ $entry['id'] }}</h3>
                                    <p class="text-xs text-muted-foreground flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 mr-1"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                                        {{ \Carbon\Carbon::parse($entry['orderTime'])->locale('vi')->isoFormat('HH:mm DD/MM/YYYY') }}
                                    </p>
                                </div>
                                <p class="font-semibold text-green-600 text-md">
                                    +{{ number_format($entry['driverEarnings'], 0, ',', '.') }}đ
                                </p>
                            </div>
                        </div>
                        <div class="p-4 pt-0 text-sm space-y-1">
                            <p>
                                <strong>Khách hàng:</strong> {{ $entry['customerName'] }}
                            </p>
                            <p>
                                <strong>Địa chỉ giao:</strong> {{ $entry['deliveryAddress'] }}
                            </p>
                            @if(isset($entry['rating']))
                                <div class="flex items-center">
                                    <strong>Đánh giá:</strong>&nbsp;
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 {{ $i <= $entry['rating'] ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300' }}">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                        </svg>
                                    @endfor
                                    <span class="ml-1">({{ $entry['rating'] }})</span>
                                </div>
                            @endif
                            @if(isset($entry['customerFeedback']) && !empty($entry['customerFeedback']))
                                <p class="text-xs italic text-muted-foreground flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 mr-1 mt-0.5 flex-shrink-0"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg> "{{ $entry['customerFeedback'] }}"
                                </p>
                            @endif
                        </div>
                        <div class="p-2 border-t">
                            <a href="{{ route('driver.orders.show', ['orderId' => $entry['id']]) }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 text-primary underline-offset-4 hover:underline h-10 px-4 py-2 text-xs">
                                Xem chi tiết đơn
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted-foreground text-center py-8">Không có lịch sử giao hàng nào cho bộ lọc này.</p>
            @endif
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterSelect = document.getElementById('history-filter');
            if (filterSelect) {
                filterSelect.addEventListener('change', function() {
                    const selectedFilter = this.value;
                    window.location.href = `{{ route('driver.history') }}?filter=${selectedFilter}`;
                });
            }
        });
    </script>
@endsection
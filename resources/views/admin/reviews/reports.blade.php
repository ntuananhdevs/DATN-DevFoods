@extends('layouts.admin.contentLayoutMaster')
@section('title', 'Danh sách báo cáo vi phạm bình luận')
@section('content')
<div class="min-h-screen bg-gradient-to-br">
    <div class=" flex flex-col gap-4 pb-4 delay-200 duration-700 ease-in-out">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-pink-600 text-white">
                <i class="fas fa-flag text-xl"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Danh sách báo cáo vi phạm bình luận</h2>
                <p class="text-muted-foreground">Kiểm tra, xử lý các báo cáo vi phạm về bình luận sản phẩm</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                {{-- The form for filtering reports is removed --}}
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người dùng</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lý do</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nội dung báo cáo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($reports as $report)
                            <tr>
                                <td class="px-4 py-3">{{ $report->id }}</td>
                                <td class="px-4 py-3">
                                    @if($report->review->product)
                                        {{ $report->review->product->name }}
                                    @elseif($report->review->combo)
                                        {{ $report->review->combo->name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $report->review->user->name ?? 'Ẩn danh' }}</td>
                                <td class="px-4 py-3">{{ $report->reason_type }}</td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $report->reason_detail }}">{{ Str::limit($report->reason_detail, 80) }}</td>
                                <td class="px-4 py-3">{{ $report->created_at ? $report->created_at->format('d/m/Y H:i') : '' }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.reviews.report.show', $report->review_id) }}" class="btn btn-xs btn-info" title="Xem chi tiết báo cáo">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">Không có báo cáo nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Hiển thị
                        <span class="font-medium text-gray-900">{{ ($reports->currentPage() - 1) * $reports->perPage() + 1 }}</span>
                        đến
                        <span class="font-medium text-gray-900">{{ min($reports->currentPage() * $reports->perPage(), $reports->total()) }}</span>
                        của
                        <span class="font-medium text-gray-900">{{ $reports->total() }}</span>
                        báo cáo
                    </div>
                    @if ($reports->lastPage() > 1)
                        <div class="flex items-center space-x-2">
                            @if (!$reports->onFirstPage())
                                <a href="{{ $reports->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">
                                    <i class="fas fa-chevron-left mr-1"></i>
                                    Trước
                                </a>
                            @endif
                            @php
                                $start = max(1, $reports->currentPage() - 2);
                                $end = min($reports->lastPage(), $reports->currentPage() + 2);
                                if ($start > 1) {
                                    echo '<a href="' . $reports->url(1) . '&' . http_build_query(request()->except('page')) . '" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">1</a>';
                                    if ($start > 2) {
                                        echo '<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">...</span>';
                                    }
                                }
                            @endphp
                            @for ($i = $start; $i <= $end; $i++)
                                <a href="{{ $reports->url($i) }}&{{ http_build_query(request()->except('page')) }}"
                                    class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transform transition-all duration-200 hover:scale-105 {{ $reports->currentPage() == $i ? 'bg-pink-600 text-white border-pink-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-700' }}">
                                    {{ $i }}
                                </a>
                            @endfor
                            @php
                                if ($end < $reports->lastPage()) {
                                    if ($end < $reports->lastPage() - 1) {
                                        echo '<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">...</span>';
                                    }
                                    echo '<a href="' . $reports->url($reports->lastPage()) . '&' . http_build_query(request()->except('page')) . '" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">' . $reports->lastPage() . '</a>';
                                }
                            @endphp
                            @if ($reports->hasMorePages())
                                <a href="{{ $reports->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">
                                    Tiếp
                                    <i class="fas fa-chevron-right ml-1"></i>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
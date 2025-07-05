<section id="rewards" class="mb-10">
    <h2 class="text-2xl font-bold mb-6">Điểm Thưởng & Ưu Đãi</h2>
    {{-- DYNAMIC REWARDS --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold mb-1">Điểm thưởng của bạn</h3>
                <p class="text-gray-500">Sử dụng điểm để đổi lấy ưu đãi hấp dẫn</p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="flex items-center bg-orange-50 px-4 py-2 rounded-lg"><i
                        class="fas fa-medal text-orange-500 mr-2"></i><span
                        class="text-2xl font-bold text-orange-500">{{ number_format($currentPoints, 0, ',', '.') }}</span><span
                        class="text-gray-500 ml-2">điểm</span></div>
            </div>
        </div>
        <div class="border-t border-gray-100 pt-6">
            <h4 class="font-bold mb-4">Lịch sử điểm thưởng</h4>
            <div class="space-y-4">
                @forelse($pointHistory as $history)
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium">{{ $history->reason }}</p>
                            <p class="text-sm text-gray-500">{{ $history->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                        @if ($history->points > 0)
                            <span class="text-green-500 font-medium">+{{ $history->points }} điểm</span>
                        @else
                            <span class="text-red-500 font-medium">{{ $history->points }} điểm</span>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-2">Chưa có lịch sử điểm thưởng.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold mb-6">Voucher của bạn</h3>
        <div class="space-y-4">
            @forelse($vouchers as $voucher)
                <div class="border border-dashed border-orange-200 rounded-lg p-4 bg-orange-50">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="mb-4 md:mb-0">
                            <h4 class="font-bold text-lg mb-1">{{ $voucher->name }}</h4>
                            <p class="text-gray-600 text-sm">{{ $voucher->description }}</p>
                            @if ($voucher->end_date)
                                <p class="text-gray-500 text-xs mt-2">Hết hạn:
                                    {{ $voucher->end_date->format('d/m/Y') }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col items-center"><span
                                class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full mb-2">{{ $voucher->code }}</span><button
                                class="text-orange-500 border border-orange-500 hover:bg-orange-50 px-4 py-1 rounded-lg text-sm transition-colors">Sử
                                dụng ngay</button></div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">Bạn không có voucher nào khả dụng.</p>
            @endforelse
        </div>
    </div>
</section>

<section id="overview" class="mb-10">
    <h2 class="text-2xl font-bold mb-6">Tổng Quan</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        {{-- DYNAMIC STATS --}}
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3"><i
                    class="fas fa-shopping-bag text-blue-500"></i></div>
            <h3 class="text-3xl font-bold mb-1">{{ $user->total_orders }}</h3>
            <p class="text-gray-500 text-sm">Đơn hàng</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-medal text-green-500"></i></div>
            <h3 class="text-3xl font-bold mb-1">{{ number_format($currentPoints, 0, ',', '.') }}</h3>
            <p class="text-gray-500 text-sm">Điểm thưởng</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-ticket-alt text-purple-500"></i></div>
            <h3 class="text-3xl font-bold mb-1">{{ $vouchers->count() }}</h3>
            <p class="text-gray-500 text-sm">Voucher</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3"><i
                    class="fas fa-heart text-red-500"></i></div>
            <h3 class="text-3xl font-bold mb-1">{{ $user->favorites->count() }}</h3>
            <p class="text-gray-500 text-sm">Yêu thích</p>
        </div>
    </div>

    @if ($currentRank)
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            {{-- DYNAMIC RANK PROGRESS --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold mb-1">Thành viên {{ $currentRank->name }}</h3>
                    @if ($nextRank)
                        <p class="text-gray-500 text-sm">Còn
                            {{ number_format(max(0, $nextRank->min_spending - $currentPoints), 0, ',', '.') }}
                            điểm nữa để lên hạng {{ $nextRank->name }}</p>
                    @else<p class="text-gray-500 text-sm">Bạn đã đạt hạng cao nhất!</p>
                    @endif
                </div>
                <div class="mt-2 md:mt-0">
                    @if ($nextRank)
                        <span
                            class="text-sm font-medium">{{ number_format($currentPoints, 0, ',', '.') }}/{{ number_format($nextRank->min_spending, 0, ',', '.') }}
                            điểm</span>
                    @else<span
                            class="text-sm font-medium">{{ number_format($currentPoints, 0, ',', '.') }}
                            điểm</span>
                    @endif
                </div>
            </div>
            <div class="relative h-4 bg-gray-100 rounded-full overflow-hidden mb-2">
                <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-yellow-400 to-yellow-500"
                    style="width: {{ $progressPercent }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500">
                @foreach ($allRanks as $rank)
                    <div class="flex flex-col items-center">
                        <div class="w-4 h-4 rounded-full mb-1 flex items-center justify-center"
                            style="background-color: {{ $rank->id === $currentRank->id ? ($rank->color ?? '#CCCCCC') . '40' : '#E5E7EB' }};">
                            <div class="w-2 h-2 rounded-full"
                                style="background-color: {{ $rank->color ?? '#9CA3AF' }};"></div>
                        </div>
                        <span>{{ $rank->name }}</span><span>{{ number_format($rank->min_spending, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>

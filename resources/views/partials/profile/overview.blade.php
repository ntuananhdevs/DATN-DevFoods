<section id="overview" class="mb-10">
    <h2 class="text-2xl font-bold mb-6">Tổng Quan</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        {{-- DYNAMIC STATS --}}
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3"><i
                    class="fas fa-shopping-bag text-blue-500"></i></div>
            <h3 class="text-3xl font-bold mb-1">{{ $orderAll }}</h3>
            <p class="text-gray-500 text-sm">Đơn hàng</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-medal text-green-500"></i>
            </div>
            <h3 class="text-3xl font-bold mb-1">{{ number_format($currentPoints, 0, ',', '.') }}</h3>
            <p class="text-gray-500 text-sm">Điểm thưởng</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-ticket-alt text-purple-500"></i>
            </div>
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
        @php
            $maxPoints = $allRanks->max('min_spending');
        @endphp
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            {{-- DYNAMIC RANK PROGRESS --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold mb-1">Thành viên {{ $currentRank->name }}</h3>
                    @if ($nextRank)
                        <p class="text-gray-500 text-sm">
                            Còn {{ number_format(max(0, $nextRank->min_spending - $currentPoints), 0, ',', '.') }} điểm
                            để lên hạng {{ $nextRank->name }}
                        </p>
                    @else
                        <p class="text-gray-500 text-sm">Bạn đã đạt hạng cao nhất!</p>
                    @endif
                </div>
                <div class="mt-2 md:mt-0">
                    <span class="text-sm font-medium">
                        {{ number_format($currentPoints, 0, ',', '.') }} /
                        {{ number_format($maxPoints, 0, ',', '.') }} điểm
                    </span>
                </div>
            </div>

            <div class="relative" style="height: 80px;">
                {{-- Thanh tiến trình nằm trên --}}
                <div class="absolute left-0 top-0 w-full h-4 bg-gray-100 rounded-full overflow-hidden z-10">
                    <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-yellow-400 to-yellow-500"
                        style="width: {{ $progressPercent }}%"></div>
                </div>
                {{-- Các mốc rank nằm dưới --}}
                @foreach ($allRanks as $rank)
                    @php
                        $left = $maxPoints > 0 ? ($rank->min_spending / $maxPoints) * 100 : 0;
                    @endphp
                    <div class="absolute left-0 flex flex-col items-center z-20"
                        style="top: 28px; left: {{ $left }}%; transform: translateX(-50%); min-width: 60px;">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center mb-1 shadow"
                            style="background-color: {{ $rank->id === $currentRank->id ? ($rank->color ?? '#CCCCCC') . '40' : '#E5E7EB' }};">
                            <div class="w-2.5 h-2.5 rounded-full"
                                style="background-color: {{ $rank->color ?? '#9CA3AF' }};"></div>
                        </div>
                        <div class="text-xs text-gray-500 text-center leading-tight" style="margin-top: 2px;">
                            {{ $rank->name }}<br>
                            {{ number_format($rank->min_spending, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>

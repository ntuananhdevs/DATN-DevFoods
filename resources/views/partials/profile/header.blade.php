<div class="bg-gradient-to-r from-orange-500 to-red-500 py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center">
            <div class="relative mb-6 md:mb-0 md:mr-8">
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-white p-1 shadow-lg">
                    {{-- DYNAMIC AVATAR --}}
                    <img src="{{ $user->avatar_url ?? asset('images/default-avatar.png') }}" alt="Ảnh đại diện"
                        class="w-full h-full rounded-full object-cover">
                </div>
                <a href="{{ route('customer.profile.edit') }}"
                    class="absolute bottom-0 right-0 bg-orange-600 hover:bg-orange-700 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-md transition-colors">
                    <i class="fas fa-camera"></i>
                </a>
            </div>
            <div class="text-center md:text-left text-white">
                {{-- DYNAMIC USER INFO --}}
                <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $user->full_name }}</h1>
                <p class="text-white/80 mb-4">Thành viên từ {{ $user->created_at->isoFormat('MMMM, YYYY') }}</p>
                <div class="flex flex-wrap justify-center md:justify-start gap-3">
                    @if ($currentRank)
                        <div class="bg-white/20 backdrop-blur-sm px-4 py-1 rounded-full flex items-center">
                            <i class="fas fa-star mr-2" style="color: {{ $currentRank->color ?? '#FFD700' }};"></i>
                            <span>Thành viên {{ $currentRank->name }}</span>
                        </div>
                    @endif
                    <div class="bg-white/20 backdrop-blur-sm px-4 py-1 rounded-full flex items-center">
                        <i class="fas fa-medal text-yellow-300 mr-2"></i>
                        <span>{{ number_format($currentPoints, 0, ',', '.') }} điểm</span>
                    </div>
                </div>
            </div>
            <div class="mt-6 md:mt-0 md:ml-auto">
                <a href="{{ route('customer.profile.edit') }}"
                    class="bg-white text-orange-500 hover:bg-orange-50 px-6 py-2 rounded-lg shadow-md transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Chỉnh sửa hồ sơ
                </a>
            </div>
        </div>
    </div>
</div>
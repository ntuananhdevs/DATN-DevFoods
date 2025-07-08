<section id="addresses" class="mb-10">
    <h2 class="text-2xl font-bold mb-6">Thông Tin Cá Nhân</h2>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <!-- Địa chỉ nhận hàng -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex items-center border border-orange-200 relative">
            <span class="text-orange-500 mr-3 text-xl">
                <i class="fas fa-map-marker-alt"></i>
            </span>
            <div class="flex-1">
                <div class="font-semibold text-base mb-1">
                    <span class="font-bold">Bùi Đức Dương đẹp trai Top1 Server</span>
                    <span class="ml-2">(+84) 355032605</span>
                    <span class="ml-2 align-middle">
                        <span class="border border-orange-500 text-orange-500 px-2 py-0.5 rounded text-xs font-medium bg-white">Mặc Định</span>
                    </span>
                </div>
                <div class="text-gray-800 text-sm">
                    Số Nhà 26, Ngách 66 Ngõ 250 Kim Giang, Phường Đại Kim, Quận Hoàng Mai, Hà Nội
                </div>
            </div>
            <a href="#" class="ml-4 text-blue-600 hover:underline font-medium text-sm">Thay Đổi</a>
        </div>
        <!-- Địa chỉ nhận hàng -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex items-center border border-orange-200 relative">
            <span class="text-orange-500 mr-3 text-xl">
                <i class="fas fa-map-marker-alt"></i>
            </span>
            <div class="flex-1">
                <div class="font-semibold text-base mb-1">
                    <span class="font-bold">Bùi Đức Dương đẹp trai số 1 Trái Đất</span>
                    <span class="ml-2">(+84) 559707081</span>
                </div>
                <div class="text-gray-800 text-sm">
                    Số Nhà 14, Ngách 55 Ngõ 259 Phú Diễn, Phường Phú Diễn, Quận Bắc Từ Liêm, Hà Nội
                </div>
            </div>
            <a href="#" class="ml-4 text-blue-600 hover:underline font-medium text-sm">Thay Đổi</a>
        </div>
    </div>

    <!-- Modal cập nhật địa chỉ -->
    <div id="updateAddressModal" class="fixed inset-0 z-60 flex items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
            <div class="max-h-[80vh] overflow-y-auto scrollbar-none" style="scrollbar-width: none; -ms-overflow-style: none;">
                <div class="px-6 py-4 border-b">
                    <span class="text-lg font-semibold">Cập nhật địa chỉ</span>
                </div>
                <form class="px-6 py-4">
                    <div class="flex gap-3 mb-3">
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Họ và tên</label>
                            <input type="text" class="w-full border rounded px-3 py-2" value="Bùi Đức Dương">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Số điện thoại</label>
                            <input type="text" class="w-full border rounded px-3 py-2" value="(+84) 355 032 605">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs text-gray-500 mb-1">Tỉnh/Thành phố</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>Hà Nội</option>
                            <!-- Thêm các tỉnh/thành khác nếu cần -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs text-gray-500 mb-1">Quận/Huyện</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>Quận Hoàng Mai</option>
                            <!-- Thêm các quận/huyện khác nếu cần -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs text-gray-500 mb-1">Phường/Xã</label>
                        <select class="w-full border rounded px-3 py-2">
                            <option>Phường Đại Kim</option>
                            <!-- Thêm các phường/xã khác nếu cần -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs text-gray-500 mb-1">Địa chỉ cụ thể</label>
                        <textarea class="w-full border rounded px-3 py-2" rows="2">Số Nhà 26, Ngách 66 Ngõ 250 Kim Giang</textarea>
                    </div>
                    <div class="mb-3">
                        <img src="https://maps.googleapis.com/maps/api/staticmap?center=21.002,105.825&zoom=16&size=400x120&markers=color:red%7C21.002,105.825&key=AIzaSyDUMMYKEY" alt="Google Map" class="w-full rounded border" style="height:120px;object-fit:cover;">
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs text-gray-500 mb-1">Loại địa chỉ:</label>
                        <div class="flex gap-2">
                            <button type="button" class="border border-orange-500 text-orange-600 bg-orange-50 px-4 py-1 rounded font-medium">Nhà Riêng</button>
                            <button type="button" class="border border-gray-300 text-gray-700 bg-white px-4 py-1 rounded font-medium">Văn Phòng</button>
                        </div>
                    </div>
                    <div class="mb-3 flex items-center">
                        <input type="checkbox" id="setDefaultAddress" class="mr-2" checked disabled>
                        <label for="setDefaultAddress" class="text-xs text-gray-400 select-none">Đặt làm địa chỉ mặc định</label>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" id="updateAddressBack" class="px-5 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">Trở Lại</button>
                        <button type="button" class="px-5 py-2 rounded bg-orange-500 text-white font-semibold hover:bg-orange-600">Hoàn thành</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.querySelector('.bg-white .text-blue-600');
        var modal = document.getElementById('updateAddressModal');
        var backBtn = document.getElementById('updateAddressBack');
        if (btn && modal) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                modal.classList.remove('hidden');
            });
        }
        if (backBtn && modal) {
            backBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
        }
        // Đóng modal khi click ra ngoài
        modal?.addEventListener('click', function(e) {
            if (e.target === modal) modal.classList.add('hidden');
        });
    });
</script>

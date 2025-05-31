<!-- Emergency Modal -->
<div x-show="showEmergencyModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div @click.away="showEmergencyModal = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="bg-white rounded-lg shadow-xl max-w-sm w-full">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Khẩn cấp</h3>
                    <p class="text-sm text-gray-600">Cần hỗ trợ ngay lập tức?</p>
                </div>
            </div>

            <div class="space-y-3">
                <button onclick="window.open('tel:113')"
                        class="w-full flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                    <i class="fas fa-phone text-red-600"></i>
                    <div class="text-left">
                        <div class="font-medium text-red-900">Gọi 113</div>
                        <div class="text-sm text-red-700">Cảnh sát</div>
                    </div>
                </button>

                <button onclick="window.open('tel:115')"
                        class="w-full flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                    <i class="fas fa-ambulance text-green-600"></i>
                    <div class="text-left">
                        <div class="font-medium text-green-900">Gọi 115</div>
                        <div class="text-sm text-green-700">Cấp cứu</div>
                    </div>
                </button>

                <button onclick="window.open('tel:19001234')"
                        class="w-full flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fas fa-headset text-blue-600"></i>
                    <div class="text-left">
                        <div class="font-medium text-blue-900">Hỗ trợ FoodDriver</div>
                        <div class="text-sm text-blue-700">1900 1234</div>
                    </div>
                </button>
            </div>

            <button @click="showEmergencyModal = false"
                    class="w-full mt-4 py-2 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Đóng
            </button>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div x-show="showConfirmModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div @click.away="showConfirmModal = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="bg-white rounded-lg shadow-xl max-w-sm w-full">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-question-circle text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900" x-text="confirmModal.title"></h3>
                    <p class="text-sm text-gray-600" x-text="confirmModal.message"></p>
                </div>
            </div>

            <div class="flex gap-3">
                <button @click="showConfirmModal = false"
                        class="flex-1 py-2 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Hủy
                </button>
                <button @click="confirmModal.onConfirm(); showConfirmModal = false"
                        class="flex-1 py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Xác nhận
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div x-show="showLoadingModal"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full mx-4">
        <div class="text-center">
            <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2" x-text="loadingModal.title"></h3>
            <p class="text-sm text-gray-600" x-text="loadingModal.message"></p>
        </div>
    </div>
</div>

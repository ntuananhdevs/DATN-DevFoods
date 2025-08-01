<!-- Action Confirmation Modal -->
<div id="action-confirmation-modal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="relative mx-auto p-5 border w-96 bg-white rounded-lg shadow-xl">
        <!-- Close button -->
        <button type="button" id="action-close-btn" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
        
        <div class="text-center">
            <h3 id="action-modal-title" class="text-xl font-medium text-gray-900">Hủy đơn hàng</h3>
            <p id="action-modal-message" class="text-sm text-gray-500 mt-2">Vui lòng cho chúng tôi biết lý do bạn muốn hủy đơn hàng này.</p>
            
            <!-- Phần chọn lý do hủy đơn -->
            <div id="cancel-reason-section" class="mt-4 text-left">
                <p class="text-sm font-medium text-gray-700 mb-2">Lý do hủy đơn hàng</p>
                <div class="space-y-2">
                    <div>
                        <input type="radio" id="reason-changed-mind" name="cancel_reason" value="Tôi đã thay đổi ý định" class="mr-2">
                        <label for="reason-changed-mind" class="text-sm text-gray-600">Tôi đã thay đổi ý định</label>
                    </div>
                    <div>
                        <input type="radio" id="reason-better-price" name="cancel_reason" value="Tìm thấy giá tốt hơn ở nơi khác" class="mr-2">
                        <label for="reason-better-price" class="text-sm text-gray-600">Tìm thấy giá tốt hơn ở nơi khác</label>
                    </div>
                    <div>
                        <input type="radio" id="reason-delivery-time" name="cancel_reason" value="Thời gian giao hàng quá lâu" class="mr-2">
                        <label for="reason-delivery-time" class="text-sm text-gray-600">Thời gian giao hàng quá lâu</label>
                    </div>
                    <div>
                        <input type="radio" id="reason-wrong-product" name="cancel_reason" value="Đặt nhầm sản phẩm" class="mr-2">
                        <label for="reason-wrong-product" class="text-sm text-gray-600">Đặt nhầm sản phẩm</label>
                    </div>
                    <div>
                        <input type="radio" id="reason-financial" name="cancel_reason" value="Vấn đề tài chính" class="mr-2">
                        <label for="reason-financial" class="text-sm text-gray-600">Vấn đề tài chính</label>
                    </div>
                    <div>
                        <input type="radio" id="reason-duplicate" name="cancel_reason" value="Đặt trùng đơn hàng" class="mr-2">
                        <label for="reason-duplicate" class="text-sm text-gray-600">Đặt trùng đơn hàng</label>
                    </div>
                    <div>
                        <input type="radio" id="reason-other" name="cancel_reason" value="Khác" class="mr-2">
                        <label for="reason-other" class="text-sm text-gray-600">Khác</label>
                    </div>
                    <div id="other-reason-container" class="mt-2 hidden">
                        <textarea id="other-reason-text" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500" placeholder="Nhập lý do cụ thể..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-between gap-3">
                <button id="action-abort-btn"
                    class="px-4 py-2 bg-gray-200 text-gray-800 hover:bg-gray-300 rounded-lg transition-colors">Quay lại</button>
                <button id="action-confirm-btn"
                    class="px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded-lg transition-colors">Xác nhận hủy</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast-message"
    class="fixed top-20 right-6 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 hidden transition-all duration-300">
</div>
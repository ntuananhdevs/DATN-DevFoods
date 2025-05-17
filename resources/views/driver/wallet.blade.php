
<!-- Wallet Panel -->
        <div id="walletPanel" class="detail-panel">
            <div class="detail-header">
                <button class="back-button" onclick="closeDetailPanel('walletPanel')">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 class="text-lg font-medium">Tài khoản</h2>
                <button class="ml-auto text-blue-600">Lịch sử</button>
            </div>
            
            <div class="wallet-content">
                <div class="wallet-balance">
                    <p class="wallet-balance-label">SỐ DƯ TẠM TÍNH(*)</p>
                    <p class="wallet-balance-amount">295.948đ</p>
                    <button class="deposit-button">Nạp tiền</button>
                </div>

                <div class="wallet-item">
                    <span class="wallet-item-label">Tài khoản Cake liên kết</span>
                    <span class="text-blue-600">Xem chi tiết</span>
                </div>

                <div class="wallet-item">
                    <span class="wallet-item-label">Tiền đang chờ duyệt</span>
                    <span>0đ</span>
                </div>

                <div class="wallet-item">
                    <span class="wallet-item-label">Có thể rút</span>
                    <span class="wallet-item-value green">295.948đ</span>
                </div>

                <div class="wallet-note">
                    <p>* Không thể rút 50.000đ - Phí duy trì tài khoản</p>
                    <p>* Mức tối đa bạn có thể rút là 3.000.000đ/ 1 ngày</p>
                </div>

                <div class="p-4 mt-auto">
                    <button class="withdraw-button">Rút tiền</button>
                </div>
            </div>
        </div>
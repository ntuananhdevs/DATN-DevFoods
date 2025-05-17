
<!-- Income Panel -->
        <div id="incomePanel" class="panel">
            <div class="panel-header">
                <h2 class="panel-title">Thu nhập và các khoản thanh toán khác</h2>
            </div>
            
            <div class="tabs">
                <div class="tab active" onclick="switchTab('income', 'day')">Ngày</div>
                <div class="tab" onclick="switchTab('income', 'week')">Tuần</div>
                <div class="tab" onclick="switchTab('income', 'month')">Tháng</div>
            </div>
            
            <div id="income-day" class="tab-content active">
                <div class="income-summary">
                    <button><i class="fas fa-chevron-left"></i></button>
                    <div class="income-date">
                        <div class="text-sm text-gray-500">14/05/2025</div>
                        <div class="income-amount">182.750 đ</div>
                        <div class="income-note">(Thu nhập từ chuyến đi và các khoản thanh toán khác)</div>
                    </div>
                    <button><i class="fas fa-chevron-right"></i></button>
                </div>
                
                <div class="service-summary">
                    <div class="service-item">
                        <div class="font-medium">beRide</div>
                        <div class="text-sm">7 chuyến</div>
                    </div>
                    <div class="service-item">
                        <div class="font-medium">beDelivery</div>
                        <div class="text-sm">2 chuyến</div>
                    </div>
                </div>
                
                <div class="history-header">
                    Lịch sử
                </div>
                
                <div class="history-item">
                    <div class="history-time">14:25, 14/05/2025</div>
                    <div class="flex items-center">
                        <span class="font-medium">15.390 đ</span>
                        <i class="fas fa-chevron-right ml-2 text-gray-400"></i>
                    </div>
                </div>
                
                <div class="history-item">
                    <div class="history-time">14:51, 14/05/2025</div>
                    <div class="flex items-center">
                        <span class="font-medium">13.465 đ</span>
                        <i class="fas fa-chevron-right ml-2 text-gray-400"></i>
                    </div>
                </div>
                
                <div class="history-item">
                    <div class="history-time">15:07, 14/05/2025</div>
                    <div class="flex items-center">
                        <span class="font-medium">16.672 đ</span>
                        <i class="fas fa-chevron-right ml-2 text-gray-400"></i>
                    </div>
                </div>
                
                <div class="history-item">
                    <div class="history-time">15:33, 14/05/2025</div>
                    <div class="flex items-center">
                        <span class="font-medium">37.833 đ</span>
                        <i class="fas fa-chevron-right ml-2 text-gray-400"></i>
                    </div>
                </div>
                
                <div class="history-item">
                    <div class="history-time">15:52, 14/05/2025</div>
                    <div class="flex items-center">
                        <span class="font-medium">17.955 đ</span>
                        <i class="fas fa-chevron-right ml-2 text-gray-400"></i>
                    </div>
                </div>
            </div>
            
            <div id="income-week" class="tab-content">
                <div class="flex items-center justify-center h-40">
                    <p class="text-gray-500">Dữ liệu thu nhập theo tuần</p>
                </div>
            </div>
            
            <div id="income-month" class="tab-content">
                <div class="flex items-center justify-center h-40">
                    <p class="text-gray-500">Dữ liệu thu nhập theo tháng</p>
                </div>
            </div>
        </div>
@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Chi Nhánh')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
   }
</style>
<div class="bg-gradient-to-r from-orange-500 to-red-500 py-12 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Chi Nhánh</h1>
        <p class="text-lg max-w-2xl mx-auto">
            Tìm chi nhánh FastFood gần bạn nhất để thưởng thức những món ăn ngon
        </p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <!-- Tìm kiếm chi nhánh -->
    <div class="max-w-4xl mx-auto mb-12">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold mb-4">Tìm Chi Nhánh</h2>
            
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium mb-1">Tỉnh/Thành phố</label>
                    <select id="city" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Tất cả</option>
                        <option value="hcm">TP. Hồ Chí Minh</option>
                        <option value="hn">Hà Nội</option>
                        <option value="dn">Đà Nẵng</option>
                        <option value="ct">Cần Thơ</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                
                <div>
                    <label for="district" class="block text-sm font-medium mb-1">Quận/Huyện</label>
                    <select id="district" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Tất cả</option>
                    </select>
                </div>
                
                <div>
                    <label for="service" class="block text-sm font-medium mb-1">Dịch vụ</label>
                    <select id="service" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Tất cả</option>
                        <option value="dine-in">Ăn tại chỗ</option>
                        <option value="takeaway">Mang đi</option>
                        <option value="delivery">Giao hàng</option>
                        <option value="drive-thru">Drive-thru</option>
                        <option value="24h">Mở cửa 24h</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-4">
                <button id="search-branch" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-md transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i>
                    Tìm kiếm
                </button>
            </div>
        </div>
    </div>
    
    <!-- Bản đồ và danh sách chi nhánh -->
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Danh sách chi nhánh -->
        <div class="lg:col-span-1">
            <h2 class="text-2xl font-bold mb-4">Danh Sách Chi Nhánh</h2>
            
            <div class="space-y-4 h-[600px] overflow-y-auto pr-2" id="branch-list">
                <!-- Chi nhánh 1 -->
                <div class="branch-item bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer" data-id="branch1">
                    <h3 class="font-bold text-lg mb-1">FastFood - Quận 1</h3>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                        123 Đường Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh
                    </p>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-phone-alt text-orange-500 mr-2"></i>
                        028 1234 5678
                    </p>
                    <p class="text-gray-600 mb-3">
                        <i class="fas fa-clock text-orange-500 mr-2"></i>
                        7:00 - 22:00, Thứ 2 - Chủ Nhật
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Ăn tại chỗ</span>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Mang đi</span>
                        <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Giao hàng</span>
                    </div>
                </div>
                
                <!-- Chi nhánh 2 -->
                <div class="branch-item bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer" data-id="branch2">
                    <h3 class="font-bold text-lg mb-1">FastFood - Quận 3</h3>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                        456 Đường Võ Văn Tần, Quận 3, TP. Hồ Chí Minh
                    </p>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-phone-alt text-orange-500 mr-2"></i>
                        028 2345 6789
                    </p>
                    <p class="text-gray-600 mb-3">
                        <i class="fas fa-clock text-orange-500 mr-2"></i>
                        7:00 - 22:00, Thứ 2 - Chủ Nhật
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Ăn tại chỗ</span>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Mang đi</span>
                        <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Giao hàng</span>
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Drive-thru</span>
                    </div>
                </div>
                
                <!-- Chi nhánh 3 -->
                <div class="branch-item bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer" data-id="branch3">
                    <h3 class="font-bold text-lg mb-1">FastFood - Quận 7</h3>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                        789 Đường Nguyễn Thị Thập, Quận 7, TP. Hồ Chí Minh
                    </p>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-phone-alt text-orange-500 mr-2"></i>
                        028 3456 7890
                    </p>
                    <p class="text-gray-600 mb-3">
                        <i class="fas fa-clock text-orange-500 mr-2"></i>
                        7:00 - 22:00, Thứ 2 - Chủ Nhật
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Ăn tại chỗ</span>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Mang đi</span>
                        <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Giao hàng</span>
                    </div>
                </div>
                
                <!-- Chi nhánh 4 -->
                <div class="branch-item bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer" data-id="branch4">
                    <h3 class="font-bold text-lg mb-1">FastFood - Quận Tân Bình</h3>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                        101 Đường Hoàng Văn Thụ, Quận Tân Bình, TP. Hồ Chí Minh
                    </p>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-phone-alt text-orange-500 mr-2"></i>
                        028 4567 8901
                    </p>
                    <p class="text-gray-600 mb-3">
                        <i class="fas fa-clock text-orange-500 mr-2"></i>
                        7:00 - 22:00, Thứ 2 - Chủ Nhật
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Ăn tại chỗ</span>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Mang đi</span>
                        <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Giao hàng</span>
                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Mở cửa 24h</span>
                    </div>
                </div>
                
                <!-- Chi nhánh 5 -->
                <div class="branch-item bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer" data-id="branch5">
                    <h3 class="font-bold text-lg mb-1">FastFood - Hà Nội</h3>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                        202 Đường Trần Duy Hưng, Quận Cầu Giấy, Hà Nội
                    </p>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-phone-alt text-orange-500 mr-2"></i>
                        024 5678 9012
                    </p>
                    <p class="text-gray-600 mb-3">
                        <i class="fas fa-clock text-orange-500 mr-2"></i>
                        7:00 - 22:00, Thứ 2 - Chủ Nhật
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Ăn tại chỗ</span>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Mang đi</span>
                        <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Giao hàng</span>
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Drive-thru</span>
                    </div>
                </div>
                
                <!-- Chi nhánh 6 -->
                <div class="branch-item bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer" data-id="branch6">
                    <h3 class="font-bold text-lg mb-1">FastFood - Đà Nẵng</h3>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                        303 Đường Nguyễn Văn Linh, Quận Hải Châu, Đà Nẵng
                    </p>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-phone-alt text-orange-500 mr-2"></i>
                        0236 789 0123
                    </p>
                    <p class="text-gray-600 mb-3">
                        <i class="fas fa-clock text-orange-500 mr-2"></i>
                        7:00 - 22:00, Thứ 2 - Chủ Nhật
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Ăn tại chỗ</span>
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Mang đi</span>
                        <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Giao hàng</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bản đồ -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold mb-4">Bản Đồ</h2>
            
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="relative h-[600px]">
                    <!-- Placeholder cho bản đồ -->
                    <div class="absolute inset-0 bg-gray-200 flex items-center justify-center">
                        <p class="text-gray-500">Bản đồ Google Maps sẽ hiển thị ở đây</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Thông tin chi nhánh chi tiết -->
    <div id="branch-detail" class="mt-12 bg-white rounded-lg shadow-sm p-6 hidden">
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-2xl font-bold" id="detail-title">FastFood - Quận 1</h2>
            <button id="close-detail" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3">Thông Tin Liên Hệ</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-orange-500 mt-1 mr-3"></i>
                            <span id="detail-address">123 Đường Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt text-orange-500 mr-3"></i>
                            <span id="detail-phone">028 1234 5678</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-orange-500 mr-3"></i>
                            <span id="detail-email">quan1@fastfood.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock text-orange-500 mr-3"></i>
                            <span id="detail-hours">7:00 - 22:00, Thứ 2 - Chủ Nhật</span>
                        </li>
                    </ul>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3">Dịch Vụ</h3>
                    <div class="flex flex-wrap gap-2" id="detail-services">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">Ăn tại chỗ</span>
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full">Mang đi</span>
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full">Giao hàng</span>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-3">Tiện Ích</h3>
                    <ul class="grid grid-cols-2 gap-2">
                        <li class="flex items-center">
                            <i class="fas fa-wifi text-orange-500 mr-2"></i>
                            <span>Wi-Fi miễn phí</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-parking text-orange-500 mr-2"></i>
                            <span>Bãi đậu xe</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-baby text-orange-500 mr-2"></i>
                            <span>Ghế trẻ em</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-credit-card text-orange-500 mr-2"></i>
                            <span>Thanh toán thẻ</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-wheelchair text-orange-500 mr-2"></i>
                            <span>Lối đi cho người khuyết tật</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-air-conditioner text-orange-500 mr-2"></i>
                            <span>Máy lạnh</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div>
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3">Hình Ảnh</h3>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden">
                            <img src="/placeholder.svg?height=150&width=150" alt="Chi nhánh" class="w-full h-full object-cover">
                        </div>
                        <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden">
                            <img src="/placeholder.svg?height=150&width=150" alt="Chi nhánh" class="w-full h-full object-cover">
                        </div>
                        <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden">
                            <img src="/placeholder.svg?height=150&width=150" alt="Chi nhánh" class="w-full h-full object-cover">
                        </div>
                        <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden">
                            <img src="/placeholder.svg?height=150&width=150" alt="Chi nhánh" class="w-full h-full object-cover">
                        </div>
                        <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden">
                            <img src="/placeholder.svg?height=150&width=150" alt="Chi nhánh" class="w-full h-full object-cover">
                        </div>
                        <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden">
                            <img src="/placeholder.svg?height=150&width=150" alt="Chi nhánh" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-3">Đánh Giá</h3>
                    <div class="flex items-center mb-2">
                        <div class="flex text-orange-400 mr-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="font-bold">4.5/5</span>
                        <span class="text-gray-500 ml-2">(120 đánh giá)</span>
                    </div>
                    
                    <div class="space-y-4 mt-4">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="flex justify-between items-center mb-1">
                                <div class="font-medium">Nguyễn Văn A</div>
                                <div class="flex text-orange-400 text-sm">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm">
                                Thức ăn ngon, nhân viên phục vụ nhiệt tình, không gian thoáng mát và sạch sẽ.
                            </p>
                        </div>
                        
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="flex justify-between items-center mb-1">
                                <div class="font-medium">Trần Thị B</div>
                                <div class="flex text-orange-400 text-sm">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm">
                                Đồ ăn ngon, giá cả hợp lý. Tuy nhiên thời gian phục vụ hơi lâu vào giờ cao điểm.
                            </p>
                        </div>
                    </div>
                    
                    <a href="#" class="text-orange-500 hover:text-orange-600 font-medium inline-flex items-center mt-3">
                        Xem tất cả đánh giá
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="mt-8 flex gap-4">
            <a href="/menu" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-md transition-colors inline-flex items-center justify-center">
                <i class="fas fa-utensils mr-2"></i>
                Đặt hàng
            </a>
            <a href="https://maps.google.com" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-md transition-colors inline-flex items-center justify-center">
                <i class="fas fa-directions mr-2"></i>
                Chỉ đường
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dữ liệu mẫu cho quận/huyện
    const districtData = {
        'hcm': ['Quận 1', 'Quận 3', 'Quận 7', 'Quận Tân Bình', 'Quận Bình Thạnh'],
        'hn': ['Quận Ba Đình', 'Quận Hoàn Kiếm', 'Quận Hai Bà Trưng', 'Quận Cầu Giấy'],
        'dn': ['Quận Hải Châu', 'Quận Thanh Khê', 'Quận Sơn Trà', 'Quận Ngũ Hành Sơn'],
        'ct': ['Quận Ninh Kiều', 'Quận Bình Thủy', 'Quận Cái Răng', 'Quận Ô Môn']
    };
    
    // Cập nhật danh sách quận/huyện khi chọn tỉnh/thành phố
    const citySelect = document.getElementById('city');
    const districtSelect = document.getElementById('district');
    
    if (citySelect && districtSelect) {
        citySelect.addEventListener('change', function() {
            const cityValue = this.value;
            
            // Xóa các option cũ
            districtSelect.innerHTML = '<option value="">Tất cả</option>';
            
            if (cityValue && districtData[cityValue]) {
                const districts = districtData[cityValue];
                
                districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.toLowerCase().replace(/\s+/g, '-');
                    option.textContent = district;
                    districtSelect.appendChild(option);
                });
            }
        });
    }
    
    // Xử lý tìm kiếm chi nhánh
    const searchButton = document.getElementById('search-branch');
    
    if (searchButton) {
        searchButton.addEventListener('click', function() {
            // Trong thực tế, đây sẽ là một API call để lấy dữ liệu chi nhánh
            // Giả lập tìm kiếm
            const submitButton = this;
            const originalText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang tìm kiếm...';
            
            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
                
                // Hiển thị thông báo
                showToast('Đã tìm thấy 6 chi nhánh phù hợp');
            }, 1000);
        });
    }
    
    // Xử lý hiển thị chi tiết chi nhánh
    const branchItems = document.querySelectorAll('.branch-item');
    const branchDetail = document.getElementById('branch-detail');
    const closeDetail = document.getElementById('close-detail');
    
    if (branchItems.length > 0 && branchDetail && closeDetail) {
        branchItems.forEach(item => {
            item.addEventListener('click', function() {
                // Lấy ID chi nhánh
                const branchId = this.getAttribute('data-id');
                
                // Cập nhật thông tin chi tiết (trong thực tế, đây sẽ là một API call)
                const branchName = this.querySelector('h3').textContent;
                const branchAddress = this.querySelector('p:nth-child(2)').textContent.trim();
                const branchPhone = this.querySelector('p:nth-child(3)').textContent.trim();
                const branchHours = this.querySelector('p:nth-child(4)').textContent.trim();
                
                // Cập nhật giao diện
                document.getElementById('detail-title').textContent = branchName;
                document.getElementById('detail-address').textContent = branchAddress.replace('123 ', '');
                document.getElementById('detail-phone').textContent = branchPhone.replace('028 ', '');
                document.getElementById('detail-hours').textContent = branchHours.replace('7:00 ', '');
                
                // Hiển thị chi tiết
                branchDetail.classList.remove('hidden');
                
                // Cuộn đến chi tiết
                branchDetail.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
        
        closeDetail.addEventListener('click', function() {
            branchDetail.classList.add('hidden');
        });
    }
    
    // Hàm hiển thị thông báo
    function showToast(message) {
        // Tạo element thông báo
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0';
        toast.textContent = message;
        
        // Thêm vào DOM
        document.body.appendChild(toast);
        
        // Hiển thị thông báo
        setTimeout(() => {
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
        }, 10);
        
        // Ẩn và xóa thông báo sau 3 giây
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
            
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
});
</script>
@endsection
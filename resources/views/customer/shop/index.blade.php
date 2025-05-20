@extends('layouts.customer.fullLayoutMaster')


@section('title', 'FastFood - Thực Đơn')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
    }
</style>
<div class="bg-gradient-to-r from-orange-500 to-red-500 py-12 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Thực Đơn</h1>
        <p class="text-lg max-w-2xl mx-auto">
            Khám phá các món ăn ngon, chất lượng và giá cả hợp lý tại FastFood
        </p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <!-- Bộ lọc và tìm kiếm -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <div class="relative w-full md:w-auto">
                <input type="text" placeholder="Tìm kiếm món ăn..." class="w-full md:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <div class="flex items-center gap-2 w-full md:w-auto">
                <span class="text-gray-600">Sắp xếp theo:</span>
                <select class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="popular">Phổ biến nhất</option>
                    <option value="price-asc">Giá: Thấp đến cao</option>
                    <option value="price-desc">Giá: Cao đến thấp</option>
                    <option value="name-asc">Tên: A-Z</option>
                </select>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <button class="category-btn px-4 py-2 bg-orange-500 text-white rounded-full hover:bg-orange-600 transition-colors active">
                Tất cả
            </button>
            <button class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                Burger
            </button>
            <button class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                Pizza
            </button>
            <button class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                Gà rán
            </button>
            <button class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                Mì Ý
            </button>
            <button class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                Món phụ
            </button>
            <button class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                Đồ uống
            </button>
            <button class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                Tráng miệng
            </button>
        </div>
    </div>
    
    <!-- Danh sách sản phẩm -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <!-- Sản phẩm 1 -->
        <div class="product-card bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="relative">
                <img src="/placeholder.svg?height=300&width=400" alt="Burger Gà Cay" class="w-full h-48 object-cover">
                <span class="custom-badge badge-new">Mới</span>
                <button class="absolute top-2 left-2 w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-orange-500 transition-colors">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex items-center mb-2">
                    <div class="flex text-orange-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="text-sm text-gray-500 ml-1">(120)</span>
                </div>
                <h3 class="font-bold text-lg mb-1">Burger Gà Cay</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                    Burger gà cay với sốt đặc biệt, rau xà lách tươi và phô mai tan chảy
                </p>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-orange-500">60.000₫</span>
                    <button class="bg-orange-500 hover:bg-orange-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Sản phẩm 2 -->
        <div class="product-card bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="relative">
                <img src="/placeholder.svg?height=300&width=400" alt="Pizza Hải Sản" class="w-full h-48 object-cover">
                <span class="custom-badge badge-sale">-15%</span>
                <button class="absolute top-2 left-2 w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-orange-500 transition-colors">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex items-center mb-2">
                    <div class="flex text-orange-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <span class="text-sm text-gray-500 ml-1">(85)</span>
                </div>
                <h3 class="font-bold text-lg mb-1">Pizza Hải Sản</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                    Pizza với hải sản tươi ngon, sốt cà chua đặc biệt và phô mai Mozzarella
                </p>
                <div class="flex justify-between items-center">
                    <div>
                        <span class="font-bold text-orange-500 mr-2">119.000₫</span>
                        <span class="text-gray-500 text-sm line-through">140.000₫</span>
                    </div>
                    <button class="bg-orange-500 hover:bg-orange-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Sản phẩm 3 -->
        <div class="product-card bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="relative">
                <img src="/placeholder.svg?height=300&width=400" alt="Gà Rán Sốt Cay" class="w-full h-48 object-cover">
                <button class="absolute top-2 left-2 w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-orange-500 transition-colors">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex items-center mb-2">
                    <div class="flex text-orange-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <span class="text-sm text-gray-500 ml-1">(150)</span>
                </div>
                <h3 class="font-bold text-lg mb-1">Gà Rán Sốt Cay</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                    Gà rán giòn với lớp sốt cay đặc trưng, phục vụ kèm khoai tây chiên
                </p>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-orange-500">85.000₫</span>
                    <button class="bg-orange-500 hover:bg-orange-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Sản phẩm 4 -->
        <div class="product-card bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="relative">
                <img src="/placeholder.svg?height=300&width=400" alt="Mì Ý Sốt Bò Bằm" class="w-full h-48 object-cover">
                <button class="absolute top-2 left-2 w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-orange-500 transition-colors">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex items-center mb-2">
                    <div class="flex text-orange-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <span class="text-sm text-gray-500 ml-1">(75)</span>
                </div>
                <h3 class="font-bold text-lg mb-1">Mì Ý Sốt Bò Bằm</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                    Mì Ý với sốt bò bằm đậm đà, phô mai Parmesan và rau thơm
                </p>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-orange-500">75.000₫</span>
                    <button class="bg-orange-500 hover:bg-orange-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Sản phẩm 5 -->
        <div class="product-card bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="relative">
                <img src="/placeholder.svg?height=300&width=400" alt="Khoai Tây Chiên" class="w-full h-48 object-cover">
                <button class="absolute top-2 left-2 w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-orange-500 transition-colors">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex items-center mb-2">
                    <div class="flex text-orange-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <span class="text-sm text-gray-500 ml-1">(95)</span>
                </div>
                <h3 class="font-bold text-lg mb-1">Khoai Tây Chiên</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                    Khoai tây chiên giòn, phục vụ kèm sốt mayonnaise và tương cà
                </p>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-orange-500">30.000₫</span>
                    <button class="bg-orange-500 hover:bg-orange-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Sản phẩm 6 -->
        <div class="product-card bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="relative">
                <img src="/placeholder.svg?height=300&width=400" alt="Coca Cola" class="w-full h-48 object-cover">
                <button class="absolute top-2 left-2 w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-orange-500 transition-colors">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex items-center mb-2">
                    <div class="flex text-orange-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <span class="text-sm text-gray-500 ml-1">(200)</span>
                </div>
                <h3 class="font-bold text-lg mb-1">Coca Cola</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                    Nước ngọt có ga Coca Cola, phục vụ với đá
                </p>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-orange-500">20.000₫</span>
                    <button class="bg-orange-500 hover:bg-orange-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Sản phẩm 7 -->
        <div class="product-card bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="relative">
                <img src="/placeholder.svg?height=300&width=400" alt="Bánh Flan Caramel" class="w-full h-48 object-cover">
                <span class="custom-badge badge-new">Mới</span>
                <button class="absolute top-2 left-2 w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-orange-500 transition-colors">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex items-center mb-2">
                    <div class="flex text-orange-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <span class="text-sm text-gray-500 ml-1">(65)</span>
                </div>
                <h3 class="font-bold text-lg mb-1">Bánh Flan Caramel</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                    Bánh flan mềm mịn với lớp caramel ngọt ngào
                </p>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-orange-500">25.000₫</span>
                    <button class="bg-orange-500 hover:bg-orange-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Sản phẩm 8 -->
        <div class="product-card bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="relative">
                <img src="/placeholder.svg?height=300&width=400" alt="Combo Gia Đình" class="w-full h-48 object-cover">
                <span class="custom-badge badge-sale">-20%</span>
                <button class="absolute top-2 left-2 w-8 h-8 bg-white rounded-full flex items-center justify-center text-gray-600 hover:text-orange-500 transition-colors">
                    <i class="far fa-heart"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="flex items-center mb-2">
                    <div class="flex text-orange-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="text-sm text-gray-500 ml-1">(110)</span>
                </div>
                <h3 class="font-bold text-lg mb-1">Combo Gia Đình</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                    2 burger, 1 pizza cỡ vừa, 4 miếng gà rán, khoai tây chiên và 4 nước ngọt
                </p>
                <div class="flex justify-between items-center">
                    <div>
                        <span class="font-bold text-orange-500 mr-2">320.000₫</span>
                        <span class="text-gray-500 text-sm line-through">400.000₫</span>
                    </div>
                    <button class="bg-orange-500 hover:bg-orange-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Phân trang -->
    <div class="flex justify-center mt-12">
        <nav class="flex items-center space-x-2">
            <a href="#" class="w-10 h-10 flex items-center justify-center rounded-md border border-gray-300 text-gray-600 hover:bg-orange-500 hover:text-white hover:border-orange-500 transition-colors">
                <i class="fas fa-chevron-left"></i>
            </a>
            <a href="#" class="w-10 h-10 flex items-center justify-center rounded-md bg-orange-500 text-white">1</a>
            <a href="#" class="w-10 h-10 flex items-center justify-center rounded-md border border-gray-300 text-gray-600 hover:bg-orange-500 hover:text-white hover:border-orange-500 transition-colors">2</a>
            <a href="#" class="w-10 h-10 flex items-center justify-center rounded-md border border-gray-300 text-gray-600 hover:bg-orange-500 hover:text-white hover:border-orange-500 transition-colors">3</a>
            <span class="w-10 h-10 flex items-center justify-center text-gray-600">...</span>
            <a href="#" class="w-10 h-10 flex items-center justify-center rounded-md border border-gray-300 text-gray-600 hover:bg-orange-500 hover:text-white hover:border-orange-500 transition-colors">8</a>
            <a href="#" class="w-10 h-10 flex items-center justify-center rounded-md border border-gray-300 text-gray-600 hover:bg-orange-500 hover:text-white hover:border-orange-500 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </a>
        </nav>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý nút danh mục
    const categoryButtons = document.querySelectorAll('.category-btn');
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Cập nhật trạng thái active
            categoryButtons.forEach(btn => {
                btn.classList.remove('bg-orange-500', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            });
            
            this.classList.remove('bg-gray-100', 'text-gray-700');
            this.classList.add('bg-orange-500', 'text-white');
            
            // Trong thực tế, ở đây sẽ có code để lọc sản phẩm theo danh mục
        });
    });
    
    // Xử lý nút yêu thích
    const favoriteButtons = document.querySelectorAll('.product-card .far.fa-heart');
    
    favoriteButtons.forEach(button => {
        button.parentElement.addEventListener('click', function(e) {
            e.preventDefault();
            
            const icon = this.querySelector('i');
            
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-red-500');
                
                // Hiển thị thông báo
                showToast('Đã thêm vào danh sách yêu thích');
            } else {
                icon.classList.remove('fas', 'text-red-500');
                icon.classList.add('far');
                
                // Hiển thị thông báo
                showToast('Đã xóa khỏi danh sách yêu thích');
            }
        });
    });
    
    // Xử lý nút thêm vào giỏ hàng
    const addToCartButtons = document.querySelectorAll('.product-card .bg-orange-500.rounded-full');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const productName = productCard.querySelector('h3').textContent;
            
            // Hiển thị thông báo
            showToast(`Đã thêm ${productName} vào giỏ hàng`);
            
            // Hiệu ứng khi thêm vào giỏ hàng
            this.innerHTML = '<i class="fas fa-check"></i>';
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-plus"></i>';
            }, 1500);
        });
    });
    
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
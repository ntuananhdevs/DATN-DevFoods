@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Burger Bò Cổ Điển')

@section('content')
<style>
    .container {
      max-width: 1280px;
      margin: 0 auto;
   }
</style>
<div class="container mx-auto px-4 py-8">
    <div class="grid md:grid-cols-2 gap-8 mb-12">
        <div class="space-y-4">
            <div class="relative h-[300px] sm:h-[400px] rounded-lg overflow-hidden border">
                <img src="/placeholder.svg?height=600&width=600" alt="Burger Bò Cổ Điển" class="object-cover w-full h-full" id="main-product-image">
                <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">Mới</span>
            </div>

            <div class="flex gap-2 overflow-x-auto pb-2">
                <button class="relative w-20 h-20 rounded border-2 border-orange-500 overflow-hidden flex-shrink-0 product-thumbnail">
                    <img src="/placeholder.svg?height=600&width=600" alt="Burger Bò Cổ Điển - Hình 1" class="object-cover w-full h-full">
                </button>
                <button class="relative w-20 h-20 rounded border-2 border-transparent overflow-hidden flex-shrink-0 product-thumbnail">
                    <img src="/placeholder.svg?height=600&width=600" alt="Burger Bò Cổ Điển - Hình 2" class="object-cover w-full h-full">
                </button>
                <button class="relative w-20 h-20 rounded border-2 border-transparent overflow-hidden flex-shrink-0 product-thumbnail">
                    <img src="/placeholder.svg?height=600&width=600" alt="Burger Bò Cổ Điển - Hình 3" class="object-cover w-full h-full">
                </button>
                <button class="relative w-20 h-20 rounded border-2 border-transparent overflow-hidden flex-shrink-0 product-thumbnail">
                    <img src="/placeholder.svg?height=600&width=600" alt="Burger Bò Cổ Điển - Hình 4" class="object-cover w-full h-full">
                </button>
            </div>
        </div>

        <div class="space-y-6">
            <h1 class="text-2xl sm:text-3xl font-bold">Burger Bò Cổ Điển</h1>

            <div class="flex items-center gap-2">
                <div class="flex items-center">
                    <i class="fas fa-star text-yellow-400"></i>
                    <i class="fas fa-star text-yellow-400"></i>
                    <i class="fas fa-star text-yellow-400"></i>
                    <i class="fas fa-star text-yellow-400"></i>
                    <i class="fas fa-star-half-alt text-yellow-400"></i>
                </div>
                <span class="text-gray-500">(120 đánh giá)</span>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-3xl font-bold text-orange-500">59.000đ</span>
            </div>

            <p class="text-gray-600">Burger bò với phô mai, rau xà lách, cà chua và sốt đặc biệt</p>

            <!-- Available branches -->
            <div class="bg-orange-50 p-3 rounded-lg">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-map-marker-alt h-4 w-4 text-orange-500"></i>
                    <span class="font-medium">Có sẵn tại 6 chi nhánh</span>
                </div>
                <select class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">Chọn chi nhánh</option>
                    <option value="branch-1">FastFood Quận 1</option>
                    <option value="branch-2">FastFood Quận 3</option>
                    <option value="branch-3">FastFood Quận 7</option>
                    <option value="branch-4">FastFood Quận 10</option>
                    <option value="branch-5">FastFood Thủ Đức</option>
                    <option value="branch-6">FastFood Hà Nội</option>
                </select>
                <div class="mt-2 text-sm text-gray-600 branch-address hidden">
                    123 Nguyễn Huệ, Phường Bến Nghé, Quận 1
                </div>
            </div>

            <!-- Product options -->
            <div class="space-y-4">
                <div>
                    <h3 class="font-medium mb-2">Kích cỡ</h3>
                    <div class="flex flex-wrap gap-2">
                        <label class="flex px-3 py-1.5 border rounded-md cursor-pointer hover:bg-gray-50 bg-orange-100 border-orange-500">
                            <input type="radio" name="size" value="small" class="sr-only" checked>
                            <span>Nhỏ</span>
                        </label>
                        <label class="flex px-3 py-1.5 border rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="size" value="medium" class="sr-only">
                            <span>Vừa</span>
                        </label>
                        <label class="flex px-3 py-1.5 border rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="size" value="large" class="sr-only">
                            <span>Lớn</span>
                        </label>
                    </div>
                </div>

                <div>
                    <h3 class="font-medium mb-2">Độ chín</h3>
                    <div class="flex flex-wrap gap-2">
                        <label class="flex px-3 py-1.5 border rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="doneness" value="rare" class="sr-only">
                            <span>Tái</span>
                        </label>
                        <label class="flex px-3 py-1.5 border rounded-md cursor-pointer hover:bg-gray-50 bg-orange-100 border-orange-500">
                            <input type="radio" name="doneness" value="medium" class="sr-only" checked>
                            <span>Vừa</span>
                        </label>
                        <label class="flex px-3 py-1.5 border rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="doneness" value="well-done" class="sr-only">
                            <span>Chín</span>
                        </label>
                    </div>
                </div>

                <div>
                    <h3 class="font-medium mb-2">Topping</h3>
                    <div class="flex flex-wrap gap-2">
                        <label class="flex px-3 py-1.5 border rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="topping" value="cheese" class="sr-only">
                            <span>Thêm phô mai</span>
                        </label>
                        <label class="flex px-3 py-1.5 border rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="topping" value="bacon" class="sr-only">
                            <span>Thêm thịt xông khói</span>
                        </label>
                        <label class="flex px-3 py-1.5 border rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="topping" value="egg" class="sr-only">
                            <span>Thêm trứng</span>
                        </label>
                        <label class="flex px-3 py-1.5 border rounded-md cursor-pointer hover:bg-gray-50 bg-orange-100 border-orange-500">
                            <input type="radio" name="topping" value="none" class="sr-only" checked>
                            <span>Không thêm</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-4 py-4 border-y">
                <div class="flex items-center gap-4">
                    <span class="font-medium">Số lượng:</span>
                    <div class="flex items-center">
                        <button class="h-8 w-8 rounded-r-none border border-gray-300 flex items-center justify-center hover:bg-gray-100" id="decrease-quantity">
                            <i class="fas fa-minus h-3 w-3"></i>
                            <span class="sr-only">Giảm</span>
                        </button>
                        <div class="h-8 px-3 flex items-center justify-center border-y border-gray-300" id="quantity">1</div>
                        <button class="h-8 w-8 rounded-l-none border border-gray-300 flex items-center justify-center hover:bg-gray-100" id="increase-quantity">
                            <i class="fas fa-plus h-3 w-3"></i>
                            <span class="sr-only">Tăng</span>
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button id="add-to-cart" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-md font-medium transition-colors flex-1 flex items-center justify-center">
                        <i class="fas fa-shopping-cart h-5 w-5 mr-2"></i>
                        Thêm vào giỏ hàng
                    </button>
                    <button class="border border-gray-300 hover:bg-gray-50 px-6 py-3 rounded-md font-medium transition-colors flex-1">
                        Mua ngay
                    </button>
                    <button class="border border-gray-300 hover:bg-gray-50 h-11 w-11 rounded-md flex items-center justify-center">
                        <i class="far fa-heart h-5 w-5"></i>
                        <span class="sr-only">Yêu thích</span>
                    </button>
                    <button class="border border-gray-300 hover:bg-gray-50 h-11 w-11 rounded-md flex items-center justify-center">
                        <i class="fas fa-share-alt h-5 w-5"></i>
                        <span class="sr-only">Chia sẻ</span>
                    </button>
                </div>
            </div>

            <div class="border-b">
                <div class="flex border-b">
                    <button class="px-4 py-2 font-medium border-b-2 border-orange-500 text-orange-500" id="tab-description">Mô tả</button>
                    <button class="px-4 py-2 font-medium border-b-2 border-transparent" id="tab-ingredients">Thành phần</button>
                    <button class="px-4 py-2 font-medium border-b-2 border-transparent" id="tab-reviews">Đánh giá</button>
                </div>
                
                <div class="py-4 tab-content" id="content-description">
                    <p class="text-gray-600">
                        Burger Bò Cổ Điển là món ăn biểu tượng của chúng tôi. Với lớp thịt bò Úc 100% tươi ngon, được nướng vừa tới, kết hợp với phô mai Cheddar béo ngậy, rau xà lách tươi giòn, cà chua mọng nước, hành tây ngọt và dưa chuột muối chua giòn. Tất cả được phủ lên bởi lớp sốt đặc biệt bí truyền và ôm trọn trong ổ bánh mì mềm xốp, nướng vàng thơm. Mỗi miếng cắn đều mang đến trải nghiệm vị giác tuyệt vời.
                    </p>
                </div>
                
                <div class="py-4 tab-content hidden" id="content-ingredients">
                    <ul class="list-disc pl-5 space-y-1 text-gray-600">
                        <li>Bánh mì burger</li>
                        <li>Thịt bò Úc</li>
                        <li>Phô mai Cheddar</li>
                        <li>Rau xà lách</li>
                        <li>Cà chua</li>
                        <li>Hành tây</li>
                        <li>Dưa chuột muối</li>
                        <li>Sốt đặc biệt</li>
                    </ul>
                </div>
                
                <div class="py-4 tab-content hidden" id="content-reviews">
                    <p class="text-gray-600">Chưa có đánh giá nào cho sản phẩm này.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Sản Phẩm Liên Quan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <!-- Related Product 1 -->
            <div class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <a href="/products/burger-cheese" class="block relative h-48 overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Burger Phô Mai Đặc Biệt" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">-10%</span>
                </a>

                <div class="p-4">
                    <div class="flex items-center gap-1 mb-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                        <span class="text-xs text-gray-500 ml-1">(95)</span>
                    </div>

                    <a href="/products/burger-cheese">
                        <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                            Burger Phô Mai Đặc Biệt
                        </h3>
                    </a>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">Burger với 2 lớp phô mai, thịt bò và sốt BBQ</p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-lg">69.000đ</span>
                            <span class="text-gray-500 text-sm line-through">76.000đ</span>
                        </div>

                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
                            <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                            Thêm
                        </button>
                    </div>
                </div>
            </div>

            <!-- Related Product 2 -->
            <div class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <a href="/products/chicken-fried" class="block relative h-48 overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Gà Rán Giòn Cay" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                    <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">Mới</span>
                </a>

                <div class="p-4">
                    <div class="flex items-center gap-1 mb-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-gray-200"></i>
                        <span class="text-xs text-gray-500 ml-1">(78)</span>
                    </div>

                    <a href="/products/chicken-fried">
                        <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                            Gà Rán Giòn Cay
                        </h3>
                    </a>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">Gà rán với lớp vỏ giòn và gia vị cay đặc biệt</p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-lg">55.000đ</span>
                        </div>

                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
                            <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                            Thêm
                        </button>
                    </div>
                </div>
            </div>

            <!-- Related Product 3 -->
            <div class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <a href="/products/burger-chicken" class="block relative h-48 overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Burger Gà Giòn Cay" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                </a>

                <div class="p-4">
                    <div class="flex items-center gap-1 mb-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-gray-200"></i>
                        <span class="text-xs text-gray-500 ml-1">(65)</span>
                    </div>

                    <a href="/products/burger-chicken">
                        <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                            Burger Gà Giòn Cay
                        </h3>
                    </a>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">Burger với thịt gà giòn, rau và sốt cay đặc biệt</p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-lg">55.000đ</span>
                        </div>

                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
                            <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                            Thêm
                        </button>
                    </div>
                </div>
            </div>

            <!-- Related Product 4 -->
            <div class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <a href="/products/sides-fries" class="block relative h-48 overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Khoai Tây Chiên" class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                </a>

                <div class="p-4">
                    <div class="flex items-center gap-1 mb-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star text-yellow-400"></i>
                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                        <span class="text-xs text-gray-500 ml-1">(98)</span>
                    </div>

                    <a href="/products/sides-fries">
                        <h3 class="font-medium text-lg mb-1 hover:text-orange-500 transition-colors line-clamp-1">
                            Khoai Tây Chiên
                        </h3>
                    </a>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">Khoai tây chiên giòn với muối</p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-lg">25.000đ</span>
                        </div>

                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded-md text-sm flex items-center transition-colors">
                            <i class="fas fa-shopping-cart h-4 w-4 mr-1"></i>
                            Thêm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Product image gallery
        const mainImage = document.getElementById('main-product-image');
        const thumbnails = document.querySelectorAll('.product-thumbnail');
        
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Update main image
                const imgSrc = this.querySelector('img').src;
                mainImage.src = imgSrc;
                
                // Update active thumbnail
                thumbnails.forEach(thumb => {
                    thumb.classList.remove('border-orange-500');
                    thumb.classList.add('border-transparent');
                });
                this.classList.remove('border-transparent');
                this.classList.add('border-orange-500');
            });
        });
        
        // Quantity controls
        const quantityElement = document.getElementById('quantity');
        const decreaseButton = document.getElementById('decrease-quantity');
        const increaseButton = document.getElementById('increase-quantity');
        let quantity = 1;
        
        decreaseButton.addEventListener('click', function() {
            if (quantity > 1) {
                quantity--;
                quantityElement.textContent = quantity;
            }
        });
        
        increaseButton.addEventListener('click', function() {
            quantity++;
            quantityElement.textContent = quantity;
        });
        
        // Tab functionality
        const tabButtons = [
            document.getElementById('tab-description'),
            document.getElementById('tab-ingredients'),
            document.getElementById('tab-reviews')
        ];
        
        const tabContents = [
            document.getElementById('content-description'),
            document.getElementById('content-ingredients'),
            document.getElementById('content-reviews')
        ];
        
        tabButtons.forEach((button, index) => {
            button.addEventListener('click', function() {
                // Update active tab button
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-orange-500', 'text-orange-500');
                    btn.classList.add('border-transparent');
                });
                button.classList.remove('border-transparent');
                button.classList.add('border-orange-500', 'text-orange-500');
                
                // Show active tab content
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                tabContents[index].classList.remove('hidden');
            });
        });
        
        // Branch selection
        const branchSelect = document.querySelector('select');
        const branchAddress = document.querySelector('.branch-address');
        
        branchSelect.addEventListener('change', function() {
            if (this.value) {
                branchAddress.classList.remove('hidden');
                // In a real application, you would fetch the address based on the selected branch
                if (this.value === 'branch-1') {
                    branchAddress.textContent = '123 Nguyễn Huệ, Phường Bến Nghé, Quận 1';
                } else if (this.value === 'branch-2') {
                    branchAddress.textContent = '456 Võ Văn Tần, Phường 5, Quận 3';
                } else {
                    branchAddress.textContent = 'Địa chỉ chi nhánh ' + this.value;
                }
            } else {
                branchAddress.classList.add('hidden');
            }
        });
        
        // Add to cart functionality
        const addToCartButton = document.getElementById('add-to-cart');
        
        addToCartButton.addEventListener('click', function() {
            // Get selected options
            const size = document.querySelector('input[name="size"]:checked').value;
            const doneness = document.querySelector('input[name="doneness"]:checked').value;
            const topping = document.querySelector('input[name="topping"]:checked').value;
            
            // Show toast notification
            showToast(`Đã thêm ${quantity} Burger Bò Cổ Điển vào giỏ hàng`);
            
            // You would typically update cart count and send data to server here
        });
        
        // Simple toast notification function
        function showToast(message) {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300 opacity-0';
            toast.textContent = message;
            
            // Add to DOM
            document.body.appendChild(toast);
            
            // Show toast
            setTimeout(() => {
                toast.classList.remove('opacity-0');
                toast.classList.add('opacity-100');
            }, 10);
            
            // Hide and remove toast after 3 seconds
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
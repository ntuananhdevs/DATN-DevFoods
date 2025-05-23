<footer class="bg-dark text-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3 mb-4">
                <h3 class="text-white mb-4">FastFood</h3>
                <p class="mb-4">Chúng tôi cung cấp những món ăn nhanh ngon, chất lượng với giá cả hợp lý.</p>
                <div class="social-links">
                    <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-light"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <h5 class="text-white mb-3">Liên Kết Nhanh</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ url('/') }}" class="text-light text-decoration-none hover-orange">Trang Chủ</a></li>
                    <li class="mb-2"><a href="{{ url('/products') }}" class="text-light text-decoration-none hover-orange">Thực Đơn</a></li>
                    <li class="mb-2"><a href="{{ url('/promotions') }}" class="text-light text-decoration-none hover-orange">Khuyến Mãi</a></li>
                    <li class="mb-2"><a href="{{ url('/about') }}" class="text-light text-decoration-none hover-orange">Về Chúng Tôi</a></li>
                    <li class="mb-2"><a href="{{ url('/contact') }}" class="text-light text-decoration-none hover-orange">Liên Hệ</a></li>
                </ul>
            </div>
            
            <div class="col-md-3 mb-4">
                <h5 class="text-white mb-3">Liên Hệ</h5>
                <ul class="list-unstyled">
                    <li class="mb-2 d-flex">
                        <i class="fas fa-map-marker-alt text-orange me-2 mt-1"></i>
                        <span>123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</span>
                    </li>
                    <li class="mb-2 d-flex">
                        <i class="fas fa-phone text-orange me-2 mt-1"></i>
                        <span>1900 1234</span>
                    </li>
                    <li class="mb-2 d-flex">
                        <i class="fas fa-envelope text-orange me-2 mt-1"></i>
                        <span>info@fastfood.com</span>
                    </li>
                </ul>
            </div>
            
            <div class="col-md-3 mb-4">
                <h5 class="text-white mb-3">Đăng Ký Nhận Tin</h5>
                <p class="mb-3">Đăng ký để nhận thông tin khuyến mãi mới nhất từ chúng tôi.</p>
                <form action="{{ url('/subscribe') }}" method="POST">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Email của bạn" required>
                        <button class="btn btn-orange" type="submit">Đăng Ký</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="border-top border-secondary mt-4 pt-4 text-center">
            <p class="mb-0">&copy; {{ date('Y') }} FastFood. Tất cả các quyền được bảo lưu.</p>
        </div>
    </div>
</footer>
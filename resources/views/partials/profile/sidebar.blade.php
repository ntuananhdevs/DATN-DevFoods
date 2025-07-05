<div class="lg:w-1/4">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-24">
        <div class="p-6 border-b border-gray-100">
            <h2 class="font-bold text-lg">Tài khoản của tôi</h2>
        </div>
        <nav class="p-2">
            <ul class="space-y-1">
                <li><a href="#overview"
                        class="sidebar-link flex items-center px-4 py-3 rounded-lg bg-orange-50 text-orange-500 font-medium"><i
                            class="fas fa-home mr-3 w-5 text-center"></i>Tổng quan</a></li>
                <li><a href="#orders"
                        class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                            class="fas fa-shopping-bag mr-3 w-5 text-center"></i>Đơn hàng của tôi</a></li>
                <li><a href="#addresses"
                        class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                            class="fas fa-map-marker-alt mr-3 w-5 text-center"></i>Địa chỉ đã lưu</a></li>
                <li><a href="#favorites"
                        class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                            class="fas fa-heart mr-3 w-5 text-center"></i>Món ăn yêu thích</a></li>
                <li><a href="#rewards"
                        class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                            class="fas fa-gift mr-3 w-5 text-center"></i>Điểm thưởng & Ưu đãi</a></li>
                <li><a href="{{ route('customer.profile.setting') }}"
                        class="sidebar-link flex items-center px-4 py-3 rounded-lg hover:bg-gray-50 transition-colors"><i
                            class="fas fa-cog mr-3 w-5 text-center"></i>Cài đặt tài khoản</a></li>
                <li class="border-t border-gray-100 mt-2 pt-2">
                    <form method="POST" action="{{ route('customer.logout') }}" id="logout-form">
                        @csrf
                        <a href="{{ route('customer.logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="sidebar-link flex items-center px-4 py-3 rounded-lg text-red-500 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>Đăng xuất
                        </a>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Nếu là link logout thì bỏ qua
                if (this.closest('form')) return;
                sidebarLinks.forEach(l => {
                    l.classList.remove('bg-orange-50', 'text-orange-500');
                    l.classList.add('hover:bg-gray-50');
                });
                this.classList.add('bg-orange-50', 'text-orange-500');
                this.classList.remove('hover:bg-gray-50');
            });
        });
    });
</script>
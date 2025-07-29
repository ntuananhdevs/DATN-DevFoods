@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Về Chúng Tôi')

@section('content')
{{-- <div class="relative h-[300px] md:h-[400px] overflow-hidden">
    <img src="/placeholder.svg?height=800&width=1600" alt="Về chúng tôi" class="object-cover w-full h-full">
    <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
        <div class="text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Về Chúng Tôi</h1>
            <p class="text-lg max-w-2xl mx-auto px-4">
                Hành trình mang đến những bữa ăn ngon, nhanh chóng và chất lượng
            </p>
        </div>
    </div>
</div> --}}


    @php
        $aboutsBanner = app('App\Http\Controllers\Customer\BannerController')->getBannersByPosition('abouts');
    @endphp
    @include('components.banner', ['banners' => $aboutsBanner])

    <div class="max-w-[1240px] mx-auto w-full">

<div class="container mx-auto px-4 py-12">
    <!-- Câu chuyện của chúng tôi -->
    <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
        <div>
            <h2 class="text-3xl font-bold mb-4">Câu Chuyện Của Chúng Tôi</h2>
            <div class="w-24 h-1 bg-orange-500 mb-6"></div>
            <p class="text-gray-600 mb-4">
                FastFood được thành lập vào năm 2010 với sứ mệnh mang đến những bữa ăn ngon, nhanh chóng và chất lượng cho
                mọi người. Chúng tôi bắt đầu với một cửa hàng nhỏ tại Thành phố Hồ Chí Minh và nhanh chóng phát triển
                thành chuỗi nhà hàng đồ ăn nhanh được yêu thích trên toàn quốc.
            </p>
            <p class="text-gray-600 mb-4">
                Với hơn 10 năm kinh nghiệm, chúng tôi tự hào về việc sử dụng những nguyên liệu tươi ngon nhất, công thức
                độc đáo và dịch vụ khách hàng xuất sắc. Mỗi món ăn tại FastFood đều được chuẩn bị với tình yêu và sự tận
                tâm, đảm bảo mang đến trải nghiệm ẩm thực tuyệt vời cho khách hàng.
            </p>
            <p class="text-gray-600">
                Hiện nay, FastFood đã có hơn 50 chi nhánh trên toàn quốc và tiếp tục mở rộng để phục vụ nhiều khách hàng
                hơn nữa. Chúng tôi cam kết duy trì chất lượng và không ngừng đổi mới để mang đến những món ăn ngon, an
                toàn và giá cả hợp lý.
            </p>
        </div>
        <div class="relative h-[400px] rounded-lg overflow-hidden">
            <img src="/placeholder.svg?height=800&width=800" alt="Câu chuyện của chúng tôi" class="object-cover w-full h-full">
        </div>
    </div>

    <!-- Tầm nhìn & Sứ mệnh -->
    <div class="grid md:grid-cols-2 gap-8 mb-16">
        <div class="bg-orange-50 p-8 rounded-lg">
            <div class="flex items-center mb-4">
                <i class="fas fa-eye text-orange-500 text-3xl mr-4"></i>
                <h2 class="text-2xl font-bold">Tầm Nhìn</h2>
            </div>
            <p class="text-gray-600">
                Trở thành thương hiệu đồ ăn nhanh hàng đầu Việt Nam, được biết đến với chất lượng sản phẩm vượt trội, dịch vụ xuất sắc và giá trị mang lại cho khách hàng. Chúng tôi hướng tới việc mở rộng thương hiệu ra thị trường quốc tế, đưa hương vị Việt Nam đến với bạn bè thế giới.
            </p>
        </div>
        <div class="bg-orange-50 p-8 rounded-lg">
            <div class="flex items-center mb-4">
                <i class="fas fa-bullseye text-orange-500 text-3xl mr-4"></i>
                <h2 class="text-2xl font-bold">Sứ Mệnh</h2>
            </div>
            <p class="text-gray-600">
                Mang đến những bữa ăn ngon, an toàn và tiện lợi với giá cả hợp lý cho mọi người. Chúng tôi cam kết sử dụng nguyên liệu chất lượng, quy trình chế biến nghiêm ngặt và dịch vụ tận tâm để đảm bảo sự hài lòng của khách hàng. Đồng thời, chúng tôi luôn nỗ lực đóng góp tích cực cho cộng đồng và môi trường.
            </p>
        </div>
    </div>

    <!-- Giá trị cốt lõi -->
    <div class="text-center mb-16">
        <h2 class="text-3xl font-bold mb-4">Giá Trị Cốt Lõi</h2>
        <div class="w-24 h-1 bg-orange-500 mb-6 mx-auto"></div>
        <div class="grid md:grid-cols-4 gap-6 mt-8">
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <i class="fas fa-utensils text-orange-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Chất Lượng</h3>
                <p class="text-gray-600">Chúng tôi cam kết sử dụng nguyên liệu tươi ngon nhất và quy trình chế biến nghiêm ngặt.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <i class="fas fa-users text-orange-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Khách Hàng</h3>
                <p class="text-gray-600">Khách hàng là trọng tâm của mọi quyết định và hành động của chúng tôi.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <i class="fas fa-award text-orange-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Đổi Mới</h3>
                <p class="text-gray-600">Chúng tôi không ngừng đổi mới để mang đến những trải nghiệm ẩm thực tuyệt vời.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <i class="fas fa-clock text-orange-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Nhanh Chóng</h3>
                <p class="text-gray-600">Chúng tôi đảm bảo thời gian phục vụ nhanh chóng mà không ảnh hưởng đến chất lượng.</p>
            </div>
        </div>
    </div>

    <!-- Lịch sử phát triển -->
    <div class="mb-16">
        <h2 class="text-3xl font-bold mb-4 text-center">Lịch Sử Phát Triển</h2>
        <div class="w-24 h-1 bg-orange-500 mb-10 mx-auto"></div>
        
        <div class="relative">
            <!-- Timeline -->
            <div class="absolute left-1/2 transform -translate-x-1/2 h-full w-1 bg-orange-200"></div>
            
            <div class="space-y-12">
                <!-- 2010 -->
                <div class="relative">
                    <div class="timeline-dot absolute left-1/2 transform -translate-x-1/2 w-6 h-6 bg-orange-500 rounded-full z-10"></div>
                    <div class="ml-auto mr-8 md:mr-auto md:ml-0 md:pr-0 md:pl-8 w-full md:w-1/2 text-right md:text-left">
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <h3 class="text-xl font-bold mb-2">2010</h3>
                            <p class="text-gray-600">
                                Thành lập cửa hàng đầu tiên tại Quận 1, TP. Hồ Chí Minh với menu chỉ có 5 món ăn.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- 2012 -->
                <div class="relative">
                    <div class="timeline-dot absolute left-1/2 transform -translate-x-1/2 w-6 h-6 bg-orange-500 rounded-full z-10"></div>
                    <div class="mr-auto ml-8 md:ml-auto md:mr-0 md:pl-0 md:pr-8 w-full md:w-1/2">
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <h3 class="text-xl font-bold mb-2">2012</h3>
                            <p class="text-gray-600">
                                Mở rộng thêm 3 chi nhánh tại TP. Hồ Chí Minh và phát triển menu với nhiều món ăn đa dạng hơn.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- 2015 -->
                <div class="relative">
                    <div class="timeline-dot absolute left-1/2 transform -translate-x-1/2 w-6 h-6 bg-orange-500 rounded-full z-10"></div>
                    <div class="ml-auto mr-8 md:mr-auto md:ml-0 md:pr-0 md:pl-8 w-full md:w-1/2 text-right md:text-left">
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <h3 class="text-xl font-bold mb-2">2015</h3>
                            <p class="text-gray-600">
                                Mở chi nhánh đầu tiên tại Hà Nội, đánh dấu bước phát triển ra thị trường miền Bắc.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- 2018 -->
                <div class="relative">
                    <div class="timeline-dot absolute left-1/2 transform -translate-x-1/2 w-6 h-6 bg-orange-500 rounded-full z-10"></div>
                    <div class="mr-auto ml-8 md:ml-auto md:mr-0 md:pl-0 md:pr-8 w-full md:w-1/2">
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <h3 class="text-xl font-bold mb-2">2018</h3>
                            <p class="text-gray-600">
                                Đạt mốc 20 chi nhánh trên toàn quốc và ra mắt ứng dụng di động đặt hàng trực tuyến.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- 2020 -->
                <div class="relative">
                    <div class="timeline-dot absolute left-1/2 transform -translate-x-1/2 w-6 h-6 bg-orange-500 rounded-full z-10"></div>
                    <div class="ml-auto mr-8 md:mr-auto md:ml-0 md:pr-0 md:pl-8 w-full md:w-1/2 text-right md:text-left">
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <h3 class="text-xl font-bold mb-2">2020</h3>
                            <p class="text-gray-600">
                                Kỷ niệm 10 năm thành lập và đạt mốc 35 chi nhánh. Ra mắt dòng sản phẩm ăn chay và thân thiện với môi trường.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- 2023 -->
                <div class="relative">
                    <div class="timeline-dot absolute left-1/2 transform -translate-x-1/2 w-6 h-6 bg-orange-500 rounded-full z-10"></div>
                    <div class="mr-auto ml-8 md:ml-auto md:mr-0 md:pl-0 md:pr-8 w-full md:w-1/2">
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <h3 class="text-xl font-bold mb-2">2023</h3>
                            <p class="text-gray-600">
                                Đạt mốc 50 chi nhánh trên toàn quốc và bắt đầu kế hoạch mở rộng ra thị trường Đông Nam Á.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Đội ngũ của chúng tôi -->
    <div class="mb-16">
        <h2 class="text-3xl font-bold mb-4 text-center">Đội Ngũ Của Chúng Tôi</h2>
        <div class="w-24 h-1 bg-orange-500 mb-10 mx-auto"></div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="relative h-64 mb-4 rounded-lg overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Nguyễn Văn A" class="object-cover w-full h-full">
                </div>
                <h3 class="text-xl font-bold">Nguyễn Văn A</h3>
                <p class="text-gray-600">Giám Đốc Điều Hành</p>
            </div>
            <div class="text-center">
                <div class="relative h-64 mb-4 rounded-lg overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Trần Thị B" class="object-cover w-full h-full">
                </div>
                <h3 class="text-xl font-bold">Trần Thị B</h3>
                <p class="text-gray-600">Bếp Trưởng</p>
            </div>
            <div class="text-center">
                <div class="relative h-64 mb-4 rounded-lg overflow-hidden">
                    <img src="/placeholder.svg?height=400&width=400" alt="Lê Văn C" class="object-cover w-full h-full">
                </div>
                <h3 class="text-xl font-bold">Lê Văn C</h3>
                <p class="text-gray-600">Giám Đốc Marketing</p>
            </div>
        </div>
    </div>

    <!-- Thành tựu -->
    <div class="mb-16">
        <h2 class="text-3xl font-bold mb-4 text-center">Thành Tựu</h2>
        <div class="w-24 h-1 bg-orange-500 mb-10 mx-auto"></div>
        
        <div class="grid md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <div class="text-4xl font-bold text-orange-500 mb-2">50+</div>
                <p class="text-gray-600">Chi nhánh trên toàn quốc</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <div class="text-4xl font-bold text-orange-500 mb-2">500+</div>
                <p class="text-gray-600">Nhân viên tận tâm</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <div class="text-4xl font-bold text-orange-500 mb-2">1M+</div>
                <p class="text-gray-600">Khách hàng hài lòng</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <div class="text-4xl font-bold text-orange-500 mb-2">10+</div>
                <p class="text-gray-600">Giải thưởng ẩm thực</p>
            </div>
        </div>
    </div>

    <!-- Trách nhiệm xã hội -->
    <div class="mb-16">
        <h2 class="text-3xl font-bold mb-4 text-center">Trách Nhiệm Xã Hội</h2>
        <div class="w-24 h-1 bg-orange-500 mb-10 mx-auto"></div>
        
        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center mb-4">
                    <i class="fas fa-leaf text-green-500 text-3xl mr-4"></i>
                    <h3 class="text-xl font-bold">Bảo Vệ Môi Trường</h3>
                </div>
                <p class="text-gray-600 mb-4">
                    Chúng tôi cam kết giảm thiểu tác động đến môi trường thông qua việc sử dụng bao bì thân thiện với môi trường, giảm rác thải nhựa và tiết kiệm năng lượng tại tất cả các chi nhánh.
                </p>
                <p class="text-gray-600">
                    FastFood đã chuyển đổi 100% bao bì sang vật liệu có thể tái chế hoặc phân hủy sinh học và đặt mục tiêu giảm 30% lượng khí thải carbon vào năm 2030.
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex items-center mb-4">
                    <i class="fas fa-hands-helping text-blue-500 text-3xl mr-4"></i>
                    <h3 class="text-xl font-bold">Hỗ Trợ Cộng Đồng</h3>
                </div>
                <p class="text-gray-600 mb-4">
                    FastFood tích cực tham gia các hoạt động từ thiện và hỗ trợ cộng đồng địa phương thông qua các chương trình tài trợ, quyên góp thực phẩm và tạo cơ hội việc làm.
                </p>
                <p class="text-gray-600">
                    Mỗi năm, chúng tôi dành 5% lợi nhuận để đóng góp cho các quỹ từ thiện và tổ chức phi lợi nhuận, đặc biệt là các chương trình hỗ trợ trẻ em có hoàn cảnh khó khăn.
                </p>
            </div>
        </div>
    </div>

    <!-- Tham gia cùng chúng tôi -->
    <div class="bg-orange-50 rounded-xl p-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Tham Gia Cùng Chúng Tôi</h2>
        <p class="text-gray-600 max-w-2xl mx-auto mb-6">
            Chúng tôi luôn tìm kiếm những người tài năng và đam mê để gia nhập đội ngũ FastFood. Nếu bạn muốn làm việc
            trong một môi trường năng động, sáng tạo và thân thiện, hãy xem các vị trí tuyển dụng hiện tại của chúng
            tôi.
        </p>
        <a href="/careers" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition-colors">
            Cơ Hội Nghề Nghiệp
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hiệu ứng hiển thị timeline
    const timelineDots = document.querySelectorAll('.timeline-dot');
    const timelineItems = document.querySelectorAll('.space-y-12 > .relative');
    
    // Kiểm tra xem phần tử có trong viewport không
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
    
    // Thêm hiệu ứng khi scroll
    function checkScroll() {
        timelineItems.forEach((item, index) => {
            if (isInViewport(item)) {
                setTimeout(() => {
                    item.classList.add('fade-in');
                }, index * 200);
            }
        });
    }
    
    // Thêm class cho animation
    timelineItems.forEach(item => {
        item.style.opacity = '0';
    });
    
    // Kiểm tra khi scroll
    window.addEventListener('scroll', checkScroll);
    
    // Kiểm tra lần đầu khi trang tải xong
    checkScroll();
});
</script>
@endsection
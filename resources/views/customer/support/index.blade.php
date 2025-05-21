@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Câu Hỏi Thường Gặp')

@section('content')
<div class="bg-gradient-to-r from-orange-500 to-red-500 py-12 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Câu Hỏi Thường Gặp</h1>
        <p class="text-lg max-w-2xl mx-auto">
            Tìm câu trả lời cho những thắc mắc của bạn về FastFood
        </p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-6xl mx-auto">
        <!-- Tìm kiếm -->
        <div class="mb-8">
            <div class="relative">
                <input type="text" id="faq-search" placeholder="Tìm kiếm câu hỏi..." class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        
        <!-- Danh mục câu hỏi -->
        <div class="mb-8">
            <div class="flex flex-wrap gap-2">
                <button class="category-btn px-4 py-2 bg-orange-500 text-white rounded-full hover:bg-orange-600 transition-colors active" data-category="all">
                    Tất cả
                </button>
                <button class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors" data-category="order">
                    Đặt hàng
                </button>
                <button class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors" data-category="delivery">
                    Giao hàng
                </button>
                <button class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors" data-category="payment">
                    Thanh toán
                </button>
                <button class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors" data-category="product">
                    Sản phẩm
                </button>
                <button class="category-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors" data-category="account">
                    Tài khoản
                </button>
            </div>
        </div>
        
        <!-- Câu hỏi thường gặp -->
        <div class="space-y-4">
            <!-- Đặt hàng -->
            <div class="faq-category" data-category="order">
                <h2 class="text-2xl font-bold mb-4">Đặt hàng</h2>
                
                <div class="space-y-4">
                    <div class="faq-item bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="faq-question p-4 cursor-pointer flex justify-between items-center">
                            <h3 class="font-medium">Làm thế nào để đặt hàng trực tuyến?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                        </div>
                        <div class="faq-answer px-4 pb-4">
                            <p class="text-gray-600">
                                Để đặt hàng trực tuyến, bạn có thể thực hiện theo các bước sau:
                            </p>
                            <ol class="list-decimal pl-5 mt-2 space-y-1 text-gray-600">
                                <li>Truy cập website hoặc ứng dụng di động của FastFood</li>
                                <li>Chọn các món ăn bạn muốn và thêm vào giỏ hàng</li>
                                <li>Nhấn vào biểu tượng giỏ hàng ở góc phải màn hình</li>
                                <li>Kiểm tra đơn hàng và nhấn "Thanh toán"</li>
                                <li>Điền thông tin giao hàng và phương thức thanh toán</li>
                                <li>Xác nhận đơn hàng</li>
                            </ol>
                            <p class="text-gray-600 mt-2">
                                Sau khi đặt hàng thành công, bạn sẽ nhận được email xác nhận và có thể theo dõi trạng thái đơn hàng trong tài khoản của mình.
                            </p>
                        </div>
                    </div>
                    
                    <div class="faq-item bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="faq-question p-4 cursor-pointer flex justify-between items-center">
                            <h3 class="font-medium">Tôi có thể thay đổi hoặc hủy đơn hàng không?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                        </div>
                        <div class="faq-answer px-4 pb-4">
                            <p class="text-gray-600">
                                Bạn có thể thay đổi hoặc hủy đơn hàng trong vòng 5 phút sau khi đặt hàng bằng cách gọi đến số hotline 1900 1234. Sau thời gian này, chúng tôi không thể đảm bảo việc thay đổi hoặc hủy đơn hàng vì đơn hàng của bạn có thể đã được chuẩn bị hoặc giao đi.
                            </p>
                            <p class="text-gray-600 mt-2">
                                Nếu bạn muốn thay đổi địa chỉ giao hàng hoặc thông tin liên hệ, vui lòng liên hệ với chúng tôi càng sớm càng tốt để được hỗ trợ.
                            </p>
                        </div>
                    </div>
                    
                    <div class="faq-item bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="faq-question p-4 cursor-pointer flex justify-between items-center">
                            <h3 class="font-medium">Tôi có thể đặt hàng trước không?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                        </div>
                        <div class="faq-answer px-4 pb-4">
                            <p class="text-gray-600">
                                Có, bạn có thể đặt hàng trước tối đa 7 ngày. Khi đặt hàng, bạn có thể chọn ngày và giờ giao hàng mong muốn. Chúng tôi sẽ chuẩn bị đơn hàng của bạn và giao đúng thời gian đã chọn.
                            </p>
                            <p class="text-gray-600 mt-2">
                                Đặt hàng trước đặc biệt hữu ích cho các sự kiện, tiệc tùng hoặc họp mặt gia đình. Bạn cũng có thể nhận được ưu đãi đặc biệt khi đặt hàng trước với số lượng lớn.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Giao hàng -->
            <div class="faq-category" data-category="delivery">
                <h2 class="text-2xl font-bold mb-4">Giao hàng</h2>
                
                <div class="space-y-4">
                    <div class="faq-item bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="faq-question p-4 cursor-pointer flex justify-between items-center">
                            <h3 class="font-medium">Phí giao hàng là bao nhiêu?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                        </div>
                        <div class="faq-answer px-4 pb-4">
                            <p class="text-gray-600">
                                Phí giao hàng phụ thuộc vào khoảng cách từ cửa hàng đến địa điểm giao hàng:
                            </p>
                            <ul class="list-disc pl-5 mt-2 space-y-1 text-gray-600">
                                <li>Dưới 2km: 15.000₫</li>
                                <li>Từ 2km đến 5km: 20.000₫</li>
                                <li>Từ 5km đến 10km: 30.000₫</li>
                                <li>Trên 10km: Vui lòng liên hệ để được báo giá</li>
                            </ul>
                            <p class="text-gray-600 mt-2">
                                Đơn hàng trên 200.000₫ sẽ được miễn phí giao hàng trong bán kính 5km. Chúng tôi thường xuyên có các chương trình khuyến mãi miễn phí giao hàng, vui lòng theo dõi website hoặc ứng dụng để cập nhật thông tin.
                            </p>
                        </div>
                    </div>
                    
                    <div class="faq-item bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="faq-question p-4 cursor-pointer flex justify-between items-center">
                            <h3 class="font-medium">Thời gian giao hàng là bao lâu?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                        </div>
                        <div class="faq-answer px-4 pb-4">
                            <p class="text-gray-600">
                                Thời gian giao hàng trung bình là 30-45 phút tùy thuộc vào khoảng cách và điều kiện giao thông. Trong giờ cao điểm hoặc điều kiện thời tiết xấu, thời gian giao hàng có thể kéo dài hơn.
                            </p>
                            <p class="text-gray-600 mt-2">
                                Khi đặt hàng, bạn sẽ nhận được thông báo về thời gian giao hàng dự kiến. Bạn cũng có thể theo dõi trạng thái đơn hàng và vị trí người giao hàng trực tiếp trên ứng dụng hoặc website của chúng tôi.
                            </p>
                        </div>
                    </div>
                    
                    <div class="faq-item bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="faq-question p-4 cursor-pointer flex justify-between items-center">
                            <h3 class="font-medium">FastFood có giao hàng vào ngày lễ không?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                        </div>
                        <div class="faq-answer px-4 pb-4">
                            <p class="text-gray-600">
                                Có, FastFood vẫn giao hàng vào các ngày lễ, Tết. Tuy nhiên, thời gian giao hàng có thể kéo dài hơn và một số khu vực có thể không được phục vụ tùy thuộc vào tình hình giao thông và nhân lực.
                            </p>
                            <p class="text-gray-600 mt-2">
                                Vào các dịp lễ lớn, chúng tôi khuyến khích bạn đặt hàng trước để đảm bảo được phục vụ đúng thời gian mong muốn. Chúng tôi cũng có thể áp dụng phụ phí giao hàng vào các ngày lễ đặc biệt.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Thanh toán -->
            <div class="faq-category" data-category="payment">
                <h2 class="text-2xl font-bold mb-4">Thanh toán</h2>
                
                <div class="space-y-4">
                    <div class="faq-item bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="faq-question p-4 cursor-pointer flex justify-between items-center">
                            <h3 class="font-medium">FastFood chấp nhận những phương thức thanh toán nào?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                        </div>
                        <div class="faq-answer px-4 pb-4">
                            <p class="text-gray-600">
                                FastFood chấp nhận nhiều phương thức thanh toán khác nhau để mang đến sự thuận tiện cho khách hàng:
                            </p>
                            <ul class="list-disc pl-5 mt-2 space-y-1 text-gray-600">
                                <li>Thanh toán khi nhận hàng (COD)</li>
                                <li>Thẻ tín dụng/ghi nợ (Visa, Mastercard, JCB)</li>
                                <li>Ví điện tử (MoMo, ZaloPay, VNPay)</li>
                                <li>Chuyển khoản ngân hàng</li>
                                <li>Thẻ quà tặng FastFood</li>
                            </ul>
                            <p class="text-gray-600 mt-2">
                                Tất cả các giao dịch trực tuyến đều được bảo mật và mã hóa để đảm bảo an toàn cho thông tin của bạn.
                            </p>
                        </div>
                    </div>
                    
                    <div class="faq-item bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="faq-question p-4 cursor-pointer flex justify-between items-center">
                            <h3 class="font-medium">Tôi có thể nhận hóa đơn VAT không?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                        </div>
                        <div class="faq-answer px-4 pb-4">
                            <p class="text-gray-600">
                                Có, bạn có thể yêu cầu hóa đơn VAT khi đặt hàng. Vui lòng cung cấp thông tin xuất hóa đơn (tên công ty, địa chỉ, mã số thuế) trong phần ghi chú khi thanh toán hoặc thông báo trực tiếp với nhân viên giao hàng.
                            </p>
                            <p class="text-gray-600 mt-2">
                                Đối với đơn hàng trực tuyến, bạn cũng có thể yêu cầu hóa đơn điện tử bằng cách liên hệ với bộ phận Chăm sóc Khách hàng của chúng tôi qua email hoặc hotline trong vòng 7 ngày kể từ ngày mua hàng.
                            </p>
                        </div>
                    </div>
                    
                    <div class="faq-item bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="faq-question p-4 cursor-pointer flex justify-between items-center">
                            <h3 class="font-medium">Làm thế nào để sử dụng mã giảm giá?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                        </div>
                        <div class="faq-answer px-4 pb-4">
                            <p class="text-gray-600">
                                Để sử dụng mã giảm giá, bạn cần thực hiện các bước sau:
                            </p>
                            <ol class="list-decimal pl-5 mt-2 space-y-1 text-gray-600">
                                <li>Thêm các món ăn vào giỏ hàng</li>
                                <li>Chuyển đến trang thanh toán</li>
                                <li>Nhập mã giảm giá vào ô "Mã giảm giá" và nhấn "Áp dụng"</li>
                                <li>Kiểm tra xem giảm giá đã được áp dụng chưa</li>
                                <li>Hoàn tất thanh toán</li>
                            </ol>
                            <p class="text-gray-600 mt-2">
                                Lưu ý rằng mỗi mã giảm giá có điều kiện áp dụng riêng (giá trị đơn hàng tối thiểu, thời hạn sử dụng, số lần sử dụng). Một số mã giảm giá không thể kết hợp với các khuyến mãi khác.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sản phẩm -->
            <div class="faq-category" data-category="product">
                <h2 class="text-2xl font-bold mb-4">Sản phẩm</h2>
                
                <div class="space-y-4">
                    <div class="faq-item bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="faq-question p-4 cursor-pointer flex justify-between items-center">
                            <h3 class="font-medium">FastFood có món ăn chay không?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                        </div>
                        <div class="faq-answer px-4 pb-4">
                            <p class="text-gray-600">
                                Có, FastFood có nhiều lựa chọn món ăn chay phù hợp với khách hàng ăn chay. Các món ăn chay của chúng tôi được chế biến riêng biệt để đảm bảo không bị lẫn với các nguyên liệu từ động vật.
                            </p>
                            <p class="text-gray-600 mt-2">
                                Một số món ăn chay phổ biến của chúng tôi bao gồm: Burger Rau Củ, Pizza Rau Củ, Salad Trộn, Mì Ý Sốt Nấm, và nhiều món khác. Bạn có thể tìm thấy các món ăn chay trong mục "Món Chay" trên thực đơn của chúng tôi.
                            </p>
                        </div>
                    </div>
                    
                    <div class="faq-item bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="faq-question p-4 cursor-pointer flex justify-between items-center">
                            <h3 class="font-medium">Thực phẩm của FastFood có chứa chất bảo quản không?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                        </div>
                        <div class="faq-answer px-4 pb-4">
                            <p class="text-gray-600">
                                FastFood cam kết sử dụng nguyên liệu tươi ngon và hạn chế tối đa việc sử dụng chất bảo quản. Chúng tôi ưu tiên sử dụng các phương pháp bảo quản tự nhiên và quy trình chế biến nghiêm ngặt để đảm bảo thực phẩm luôn tươi ngon và an toàn.
                            </p>
                            <p class="text-gray-600 mt-2">
                                Tất cả nguyên liệu của chúng tôi đều được kiểm tra chất lượng nghiêm ngặt và đáp ứng các tiêu chuẩn an toàn thực phẩm. Thông tin chi tiết về thành phần của từng món ăn được cung cấp trên website và ứng dụng của chúng tôi.
                            </p>
                        </div>
                    </div>
                    
                    <div class="faq-item bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="faq-question p-4 cursor-pointer flex justify-between items-center">
                            <h3 class="font-medium">FastFood có cung cấp thông tin dinh dưỡng cho các món ăn không?</h3>
                            <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                        </div>
                        <div class="faq-answer px-4 pb-4">
                            <p class="text-gray-600">
                                Có, FastFood cung cấp đầy đủ thông tin dinh dưỡng cho tất cả các món ăn trên thực đơn. Bạn có thể xem thông tin dinh dưỡng chi tiết bao gồm calo, protein, carbohydrate, chất béo, đường, muối và các thành phần khác trên website hoặc ứng dụng của chúng tôi.
                            </p>
                            <p class="text-gray-600 mt-2">
                                Chúng tôi cũng đánh dấu rõ các món ăn có chứa các thành phần gây dị ứng phổ biến như đậu phộng, hải sản, trứng, sữa, gluten, v.v. Nếu bạn có nhu cầu dinh dưỡng đặc biệt hoặ
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DevFood Vietnam')</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('fonts/feather/style.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/noui-slider@15.6.1/dist/nouislider.min.css">
`   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@6.8.4/swiper-bundle.min.css">
    <!-- Main CSS -->
    {{-- <link rel="stylesheet" href="{{ asset('css/customer/layout.css') }} --}}">
    <link rel="stylesheet" href="{{ asset('css/customer/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/footer.css') }}">
    @yield('styles')
</head>
@include('components.modal')
<body>
    <div class="scroll-progress-bar"></div>
    @include('panels.customer.header')
    <main>
        @yield('content')
    </main>
    @include('panels.customer.footer')
    
    <!-- JavaScript Files -->
    <script src="{{ asset('js/modal.js') }}"></script>
    @yield('scripts')
    
    <!-- Add the missing function -->
    <script>
        function dtmodalShowToast(message, type = 'success') {
            // Create toast element if it doesn't exist
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }
            
            // Create toast
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            
            // Toast content
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            
            // Add to container
            toastContainer.appendChild(toastEl);
            
            // Initialize and show toast
            const toast = new bootstrap.Toast(toastEl, {
                delay: 5000
            });
            toast.show();
            
            // Remove after hiding
            toastEl.addEventListener('hidden.bs.toast', function() {
                toastEl.remove();
            });
        }
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Thiết lập CSRF token cho tất cả các request Ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@6.8.4/swiper-bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Cập nhật số lượng sản phẩm trong giỏ hàng từ session
            const cartCount = {{ count(Session::get('cart', [])) }};
            $('.cart-count').text(cartCount);
            
            // Hàm cập nhật số lượng giỏ hàng - có thể gọi từ bất kỳ trang nào
            window.updateCartCount = function(count) {
                $('.cart-count').text(count);
            }
        });
    </script>
</body>

</html>

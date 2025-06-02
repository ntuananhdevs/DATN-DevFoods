@if (session('toast'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            dtmodalShowToast('{{ session("toast.type") }}', {
                title: '{{ session("toast.title") }}',
                message: '{{ session("toast.message") }}'
            });
        });
    </script>
@endif
@if (session('modal'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            dtmodalShowModal('{{ session("modal.type") }}', {
                title: '{{ session("modal.title") }}',
                subtitle: '{{ session("modal.subtitle", "") }}',
                message: '{{ session("modal.message") }}',
                confirmText: '{{ session("modal.confirmText", "Đồng ý") }}',
                cancelText: '{{ session("modal.cancelText", "Đóng") }}',
                createIfNotExists: true
            });
        });
    </script>
@endif

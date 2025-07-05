// Apple-style search overlay logic
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const searchBtn = document.getElementById('searchBtn');
        const searchSection = document.getElementById('searchSection');
        const closeBtn = document.getElementById('closeBtn');
        const overlay = document.getElementById('overlay');
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('search-results');

        function toggleSearch() {
            const isOpen = searchSection.classList.contains('open');
            const header = document.querySelector('header');
            if (isOpen) {
                searchSection.classList.remove('open');
                overlay.classList.add('hidden');
                if (header) header.classList.remove('search-active');
            } else {
                searchSection.classList.add('open');
                overlay.classList.remove('hidden');
                if (header) header.classList.add('search-active');
                searchInput.focus();
            }
        }
        if (searchBtn) searchBtn.addEventListener('click', toggleSearch);
        if (closeBtn) closeBtn.addEventListener('click', toggleSearch);
        if (overlay) overlay.addEventListener('click', toggleSearch);
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchSection.classList.contains('open')) {
                toggleSearch();
            }
        });

        if (searchInput && searchResults) {
            let searchTimeout = null;
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                const value = e.target.value.trim();
                if (value.length === 0) {
                    searchResults.innerHTML = '';
                    searchLoader.style.display = 'none';
                    return;
                }
                searchLoader.style.display = 'block';
                searchTimeout = setTimeout(() => {
                    fetch(`/shop/products?search=${encodeURIComponent(value)}&ajax=1`)
                        .then(res => res.text())
                        .then(html => {
                            searchResults.innerHTML = html;
                            searchLoader.style.display = 'none';
                        })
                        .catch(() => {
                            searchResults.innerHTML = '<div class="text-center text-gray-500 py-4">Không tìm thấy kết quả.</div>';
                            searchLoader.style.display = 'none';
                        });
                }, 400);
            });
        }
        // Đóng search-section khi submit form
        const searchForm = document.querySelector('.search-input-wrapper');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                searchSection.classList.remove('open');
                overlay.classList.add('hidden');
            });
        }
    });
})();

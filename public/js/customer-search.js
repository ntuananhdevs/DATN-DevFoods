// Apple-style search overlay logic
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const searchBtn = document.getElementById('searchBtn');
        const searchSection = document.getElementById('searchSection');
        const closeBtn = document.getElementById('closeBtn');
        const overlay = document.getElementById('overlay');
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('search-results');
        const searchAjaxDropdown = document.getElementById('search-ajax-dropdown');

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

        if (searchInput && searchAjaxDropdown) {
            let searchTimeout = null;
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                const value = e.target.value.trim();
                if (value.length < 2) {
                    searchAjaxDropdown.style.display = 'none';
                    searchAjaxDropdown.innerHTML = '';
                    return;
                }
                searchAjaxDropdown.style.display = 'block';
                searchTimeout = setTimeout(() => {
                    fetch("/search/ajax", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ search: value })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.results && data.results.length > 0) {
                            searchAjaxDropdown.innerHTML = data.results.map(item => {
                                let url = '';
                                if (item.type === 'combo') {
                                    url = window.LaravelRoutes.comboShow.replace('REPLACE_ID', item.id);
                                } else {
                                    url = window.LaravelRoutes.productShow.replace('REPLACE_ID', item.id);
                                }
                                let typeLabel = item.type === 'combo'
                                    ? '<span style="background:#f59e0b;color:white;font-size:0.75rem;padding:2px 8px;border-radius:8px;margin-right:8px;">Combo</span>'
                                    : '';
                                return `
                                    <a href="${url}" style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; text-decoration: none; color: #1f2937; border-bottom: 1px solid #f1f5f9;">
                                        <img src="${item.image_url}" alt="${item.name}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 0.4rem;">
                                        <span style="flex:1;">${typeLabel}${item.name}</span>
                                        <span style="color: #f97316; font-weight: 600;">${Math.round(item.price).toLocaleString('vi-VN', {maximumFractionDigits:0})}đ</span>
                                    </a>
                                `;
                            }).join('');
                        }
                    })
                    .catch(() => {
                        searchAjaxDropdown.innerHTML = '<div style="padding: 12px; color: #ef4444;">Lỗi tìm kiếm.</div>';
                        searchAjaxDropdown.style.display = 'block';
                    });
                }, 400);
            });
            // Ẩn dropdown khi click ra ngoài
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchAjaxDropdown.contains(e.target)) {
                    searchAjaxDropdown.style.display = 'none';
                }
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

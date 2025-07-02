// CSRF Token Helper
window.getCsrfToken = function() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.getAttribute('content') : '';
};

// Set up axios defaults
if (typeof axios !== 'undefined') {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = window.getCsrfToken();
}

// Set up fetch defaults
window.csrfFetch = function(url, options = {}) {
    const token = window.getCsrfToken();
    const defaultHeaders = {
        'X-CSRF-TOKEN': token,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    };
    
    return fetch(url, {
        ...options,
        headers: {
            ...defaultHeaders,
            ...options.headers
        }
    });
}; 
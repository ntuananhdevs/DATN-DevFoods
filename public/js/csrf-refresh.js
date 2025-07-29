/**
 * CSRF Token Auto-Refresh Script
 * Automatically refreshes the CSRF token when the page loads and periodically
 * to prevent 419 Page Expired errors.
 */

(function() {
    // Configuration
    const REFRESH_INTERVAL = 25 * 60 * 1000; // 25 minutes in milliseconds
    const DEBUG = false; // Set to true to enable console logging
    
    // Helper functions
    function log(message) {
        if (DEBUG) console.log(`[CSRF Refresh] ${message}`);
    }
    
    function getCsrfToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        return metaTag ? metaTag.getAttribute('content') : null;
    }
    
    function updateCsrfToken(newToken) {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            metaTag.setAttribute('content', newToken);
            log(`CSRF token updated: ${newToken.substring(0, 10)}...`);
            
            // Update jQuery AJAX setup if jQuery is available
            if (window.jQuery) {
                $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': newToken } });
                log('jQuery AJAX headers updated');
            }
            
            // Update Axios headers if Axios is available
            if (window.axios) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
                log('Axios headers updated');
            }
            
            // Dispatch a custom event that other scripts can listen for
            document.dispatchEvent(new CustomEvent('csrfTokenRefreshed', { detail: { token: newToken } }));
            return true;
        }
        return false;
    }
    
    async function refreshCsrfToken() {
        try {
            log('Refreshing CSRF token...');
            const response = await fetch('/refresh-csrf');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            if (data && data.csrf_token) {
                const updated = updateCsrfToken(data.csrf_token);
                if (updated) {
                    log('CSRF token refresh successful');
                    return true;
                }
            }
            log('Failed to update CSRF token - invalid response');
            return false;
        } catch (error) {
            log(`Error refreshing CSRF token: ${error.message}`);
            return false;
        }
    }
    
    // Setup error interceptors for AJAX and Axios
    function setupErrorInterceptors() {
        // For jQuery AJAX
        if (window.jQuery) {
            $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
                if (jqxhr.status === 419) {
                    log('419 error detected in jQuery AJAX request');
                    refreshCsrfToken().then(success => {
                        if (success && settings.url) {
                            log('Retrying failed request...');
                            // You could implement retry logic here if needed
                        }
                    });
                }
            });
        }
        
        // For Axios
        if (window.axios) {
            window.axios.interceptors.response.use(
                response => response,
                async error => {
                    if (error.response && error.response.status === 419) {
                        log('419 error detected in Axios request');
                        const success = await refreshCsrfToken();
                        if (success && error.config) {
                            log('Retrying failed Axios request...');
                            // Clone the original request
                            const config = {...error.config};
                            // Update the CSRF token in the retried request
                            if (config.headers) {
                                config.headers['X-CSRF-TOKEN'] = getCsrfToken();
                            }
                            // Retry the request
                            return axios(config);
                        }
                    }
                    return Promise.reject(error);
                }
            );
        }
    }
    
    // Initialize
    function initialize() {
        log('Initializing CSRF auto-refresh');
        
        // Refresh token immediately when page loads
        refreshCsrfToken();
        
        // Set up periodic refresh
        setInterval(refreshCsrfToken, REFRESH_INTERVAL);
        
        // Set up error interceptors
        setupErrorInterceptors();
        
        // Set up visibility change listener to refresh when tab becomes active
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                log('Page became visible, refreshing token');
                refreshCsrfToken();
            }
        });
        
        // Listen for storage events to sync token across tabs
        window.addEventListener('storage', function(event) {
            if (event.key === 'csrf_token_updated') {
                log('CSRF token updated in another tab');
                const newToken = event.newValue;
                if (newToken && newToken !== getCsrfToken()) {
                    updateCsrfToken(newToken);
                }
            }
        });
    }
    
    // Run when DOM is fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();
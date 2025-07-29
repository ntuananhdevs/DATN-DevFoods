{{-- CSRF Token Auto-Refresh Component --}}
<script src="{{ asset('js/csrf-refresh.js') }}" defer></script>
<script>
    // Fallback in case the external script fails to load
    document.addEventListener('DOMContentLoaded', function() {
        // Check if our script has already been loaded
        if (!window.csrfRefreshInitialized) {
            // Set up basic error handling for 419 errors
            if (window.axios) {
                window.axios.interceptors.response.use(
                    response => response,
                    async error => {
                        if (error.response && error.response.status === 419) {
                            try {
                                const response = await fetch('/refresh-csrf');
                                if (response.ok) {
                                    const data = await response.json();
                                    if (data && data.csrf_token) {
                                        // Update the CSRF token
                                        const metaTag = document.querySelector('meta[name="csrf-token"]');
                                        if (metaTag) {
                                            metaTag.setAttribute('content', data.csrf_token);
                                            
                                            // Update Axios headers
                                            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = data.csrf_token;
                                            
                                            // Update jQuery AJAX headers if jQuery is available
                                            if (window.jQuery) {
                                                $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': data.csrf_token } });
                                            }
                                            
                                            // Retry the failed request
                                            if (error.config) {
                                                const config = {...error.config};
                                                if (config.headers) {
                                                    config.headers['X-CSRF-TOKEN'] = data.csrf_token;
                                                }
                                                return axios(config);
                                            }
                                        }
                                    }
                                }
                            } catch (e) {
                                console.error('Error refreshing CSRF token:', e);
                            }
                        }
                        return Promise.reject(error);
                    }
                );
            }
            
            // Also handle jQuery AJAX errors if jQuery is available
            if (window.jQuery) {
                $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
                    if (jqxhr.status === 419) {
                        fetch('/refresh-csrf')
                            .then(res => res.json())
                            .then(data => {
                                if (data && data.csrf_token) {
                                    // Update the CSRF token
                                    const metaTag = document.querySelector('meta[name="csrf-token"]');
                                    if (metaTag) {
                                        metaTag.setAttribute('content', data.csrf_token);
                                        
                                        // Update jQuery AJAX headers
                                        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': data.csrf_token } });
                                        
                                        // Update Axios headers if Axios is available
                                        if (window.axios) {
                                            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = data.csrf_token;
                                        }
                                    }
                                }
                            });
                    }
                });
            }
        }
    });
</script>
/**
 * Driver Status Realtime Updates
 * 
 * This script handles real-time updates for driver status in the admin panel.
 * It listens to the 'drivers' channel for 'driver-status-updated' events and
 * updates the UI accordingly.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on a page that needs driver status updates
    const driverTrackingPage = document.querySelector('.driver-tracking-page');
    if (!driverTrackingPage) return;

    // Initialize Echo to listen for driver status updates
    if (typeof window.Echo !== 'undefined') {
        // Listen to the public channel for all drivers
        window.Echo.channel('drivers')
            .listen('driver-status-updated', (e) => {
                console.log('Driver status updated:', e);
                updateDriverStatus(e);
            });

        console.log('Listening for driver status updates on channel: drivers');
    } else {
        console.error('Echo is not defined. Make sure Laravel Echo is properly initialized.');
    }

    /**
     * Update the driver status in the UI
     * @param {Object} data - The event data containing driver information
     */
    function updateDriverStatus(data) {
        // Get the driver data from the event
        const driverId = data.driver_id;
        const newStatus = data.new_status;
        const isAvailable = data.is_available;
        
        // Update driver list if Alpine.js data is available
        if (window.Alpine) {
            // Find all Alpine components that might contain driver data
            document.querySelectorAll('[x-data]').forEach(el => {
                const alpineComponent = Alpine.$data(el);
                
                // Check if this component has drivers data
                if (alpineComponent.drivers) {
                    // Update the specific driver in the array
                    const driverIndex = alpineComponent.drivers.findIndex(d => d.id === driverId);
                    if (driverIndex !== -1) {
                        // Update driver status
                        alpineComponent.drivers[driverIndex].status = newStatus;
                        
                        // If the component has a method to update stats, call it
                        if (typeof alpineComponent.updateDriverStats === 'function') {
                            alpineComponent.updateDriverStats();
                        }
                        
                        // If the component has a method to update markers, call it
                        if (typeof alpineComponent.updateMarkers === 'function') {
                            alpineComponent.updateMarkers();
                        }
                        
                        console.log(`Updated driver ${driverId} status to ${newStatus}`);
                    }
                }
                
                // If this is the selected driver component, update it directly
                if (alpineComponent.selectedDriver && alpineComponent.selectedDriver.id === driverId) {
                    alpineComponent.selectedDriver.status = newStatus;
                    console.log(`Updated selected driver status to ${newStatus}`);
                }
            });
        }
        
        // Update any standalone driver status elements
        const statusElements = document.querySelectorAll(`[data-driver-id="${driverId}"] .driver-status`);
        if (statusElements.length > 0) {
            statusElements.forEach(element => {
                // Update status text
                element.textContent = getStatusLabel(newStatus);
                
                // Update status class
                element.className = 'driver-status ' + getStatusBadgeClass(newStatus);
            });
        }
        
        // Update status dot indicators
        const statusDots = document.querySelectorAll(`[data-driver-id="${driverId}"] .status-dot`);
        if (statusDots.length > 0) {
            statusDots.forEach(dot => {
                dot.className = 'status-dot ' + getStatusDotClass(newStatus);
            });
        }
    }

    /**
     * Get the display label for a driver status
     * @param {string} status - The driver status
     * @returns {string} The formatted status label
     */
    function getStatusLabel(status) {
        switch (status) {
            case 'available':
                return 'Sẵn sàng';
            case 'delivering':
                return 'Đang giao hàng';
            case 'offline':
                return 'Ngoại tuyến';
            default:
                return 'Không xác định';
        }
    }

    /**
     * Get the CSS class for a driver status badge
     * @param {string} status - The driver status
     * @returns {string} The CSS class for the badge
     */
    function getStatusBadgeClass(status) {
        switch (status) {
            case 'available':
                return 'bg-green-100 text-green-800';
            case 'delivering':
                return 'bg-blue-100 text-blue-800';
            case 'offline':
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Get the CSS class for a driver status dot indicator
     * @param {string} status - The driver status
     * @returns {string} The CSS class for the dot
     */
    function getStatusDotClass(status) {
        switch (status) {
            case 'available':
                return 'bg-green-500';
            case 'delivering':
                return 'bg-blue-500';
            case 'offline':
                return 'bg-gray-500';
            default:
                return 'bg-gray-500';
        }
    }
});
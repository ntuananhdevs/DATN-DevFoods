// Discount Updates Realtime Listener
// This file is now deprecated - discount updates are handled in index.js
// Keeping this file for backward compatibility but it won't initialize

class DiscountUpdatesListener {
    constructor() {
        console.log('⚠️ DiscountUpdatesListener is deprecated. Discount updates are now handled in index.js');
        // Don't initialize anything - let index.js handle it
    }

    init() {
        console.log('⚠️ DiscountUpdatesListener.init() is deprecated');
    }
}

// Don't auto-initialize
console.log('📝 discount-updates.js loaded but not initializing (handled by index.js)'); 
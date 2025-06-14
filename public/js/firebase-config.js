// Firebase configuration will be loaded dynamically from Laravel
let firebaseApp = null;
let firebaseConfig = null;
let isFirebaseInitialized = false;

// Initialize Firebase with config from Laravel
window.initializeFirebase = async function() {
    try {
        // Check if Firebase SDK is loaded
        if (typeof firebase === 'undefined') {
            console.error('Firebase SDK not loaded');
            return false;
        }

        // Get Firebase config from Laravel backend
        const response = await fetch('/api/firebase/config');
        const data = await response.json();
        
        console.log('Firebase config response:', data);
        
        if (!data.enabled) {
            console.warn('Firebase authentication is disabled');
            return false;
        }

        if (!data.providers.google) {
            console.warn('Google authentication is disabled');
            return false;
        }

        firebaseConfig = data.config;
        
        // Validate Firebase config
        const requiredFields = ['apiKey', 'authDomain', 'projectId'];
        for (const field of requiredFields) {
            if (!firebaseConfig[field]) {
                console.error(`Missing required Firebase config field: ${field}`);
                return false;
            }
        }
        
        console.log('Firebase config:', firebaseConfig);
        
        // Check if Firebase is already initialized
        if (firebase.apps.length === 0) {
            // Initialize Firebase with the config from Laravel
            firebaseApp = firebase.initializeApp(firebaseConfig);
            console.log('Firebase initialized successfully');
        } else {
            firebaseApp = firebase.app();
            console.log('Firebase already initialized');
        }
        
        isFirebaseInitialized = true;
        return true;
    } catch (error) {
        console.error('Failed to initialize Firebase:', error);
        isFirebaseInitialized = false;
        return false;
    }
};

// Google Sign In function
window.signInWithGoogle = async function() {
    try {
        // Ensure Firebase is initialized
        if (!isFirebaseInitialized) {
            console.log('Firebase not initialized, attempting to initialize...');
            const initialized = await window.initializeFirebase();
            if (!initialized) {
                throw new Error('Firebase initialization failed');
            }
            
            // Wait a bit for Firebase to be fully ready
            await new Promise(resolve => setTimeout(resolve, 500));
        }

        // Check if Firebase Auth is available
        if (!firebase.auth) {
            throw new Error('Firebase Auth not available');
        }

        console.log('Attempting Google Sign In...');
        
        // Create Google Auth Provider
        const provider = new firebase.auth.GoogleAuthProvider();
        provider.addScope('email');
        provider.addScope('profile');
        
        // Set custom parameters
        provider.setCustomParameters({
            'prompt': 'select_account'
        });

        console.log('Google provider created:', provider);
        
        // Attempt sign in
        const result = await firebase.auth().signInWithPopup(provider);
        console.log('Google Sign In result:', result);
        
        const user = result.user;
        
        if (!user) {
            throw new Error('No user returned from Google Sign In');
        }
        
        const idToken = await user.getIdToken();
        
        return {
            success: true,
            user: {
                uid: user.uid,
                email: user.email,
                displayName: user.displayName,
                photoURL: user.photoURL
            },
            idToken: idToken
        };
    } catch (error) {
        console.error('Google Sign In Error:', error);
        return {
            success: false,
            error: error.message,
            code: error.code
        };
    }
};

// Google Sign Out function
window.signOutGoogle = async function() {
    try {
        if (!isFirebaseInitialized || !firebase.auth) {
            console.warn('Firebase not initialized');
            return { success: true };
        }

        await firebase.auth().signOut();
        return { success: true };
    } catch (error) {
        console.error('Google Sign Out Error:', error);
        return { success: false, error: error.message };
    }
};

// Check authentication state
window.checkAuthState = function(callback) {
    if (!isFirebaseInitialized || !firebase.auth) {
        console.warn('Firebase not initialized');
        return;
    }
    firebase.auth().onAuthStateChanged(callback);
};

// Handle Google Login and send to Laravel backend
window.handleGoogleLogin = async function() {
    try {
        console.log('Starting Google login process...');
        
        const result = await window.signInWithGoogle();
        
        console.log('Sign in result:', result);
        
        if (result.success) {
            console.log('Sending data to Laravel backend...');
            
            // Send Firebase token and user data to Laravel backend
            const response = await fetch('/api/auth/google', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    firebase_token: result.idToken,
                    google_user_data: result.user
                })
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('HTTP Error:', response.status, errorText);
                throw new Error(`HTTP ${response.status}: ${errorText || 'Unknown error'}`);
            }

            const data = await response.json();
            console.log('Laravel response:', data);
            
            if (data.success) {
                console.log('Login successful, redirecting to:', data.redirect_url);
                // Redirect to appropriate page
                window.location.href = data.redirect_url || '/';
            } else {
                throw new Error(data.message || 'Đăng nhập thất bại');
            }
        } else {
            console.error('Google Sign In failed:', result.error);
            let errorMessage = 'Đăng nhập Google thất bại';
            
            if (result.code === 'auth/popup-closed-by-user') {
                errorMessage = 'Đăng nhập bị hủy bởi người dùng';
            } else if (result.code === 'auth/popup-blocked') {
                errorMessage = 'Popup đăng nhập bị chặn. Vui lòng cho phép popup và thử lại';
            } else if (result.error) {
                errorMessage += ': ' + result.error;
            }
            
            alert(errorMessage);
        }
    } catch (error) {
        console.error('Handle Google Login Error:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack,
            name: error.name
        });
        
        let errorMessage = 'Đã xảy ra lỗi trong quá trình đăng nhập. Vui lòng thử lại.';
        
        if (error.message.includes('HTTP 419')) {
            errorMessage = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
        } else if (error.message.includes('HTTP 422')) {
            errorMessage = 'Dữ liệu không hợp lệ. Vui lòng thử lại.';
        } else if (error.message.includes('HTTP 500')) {
            errorMessage = 'Lỗi server. Vui lòng thử lại sau.';
        }
        
        alert(errorMessage);
    }
};

// Wait for Firebase SDK to load before initializing
window.addEventListener('load', function() {
    // Check if Firebase is available
    if (typeof firebase !== 'undefined') {
        console.log('Firebase SDK loaded, initializing...');
        window.initializeFirebase();
    } else {
        console.error('Firebase SDK not loaded');
        
        // Try to load Firebase if not available
        let attempts = 0;
        const checkFirebase = setInterval(() => {
            attempts++;
            if (typeof firebase !== 'undefined') {
                console.log('Firebase SDK loaded after', attempts, 'attempts');
                clearInterval(checkFirebase);
                window.initializeFirebase();
            } else if (attempts >= 10) {
                console.error('Firebase SDK failed to load after 10 attempts');
                clearInterval(checkFirebase);
            }
        }, 500);
    }
}); 
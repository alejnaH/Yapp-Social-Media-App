const Auth = {
    token: sessionStorage.getItem('auth_token'),
    user: JSON.parse(sessionStorage.getItem('user') || 'null'),

    isAuthenticated() {
        const hasToken = this.token && this.token !== 'null' && this.token.length > 0;
        const hasUser = this.user && this.user !== null && typeof this.user === 'object';
        return hasToken && hasUser;
    },

    isAdmin() {
        return this.user && this.user.role === 'admin';
    },

    async login(username, password) {
        console.log('Auth.login called with username:', username);
        
        try {
            const response = await fetch('https://yapp-backend-ixlrf.ondigitalocean.app/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });

            console.log('Login response status:', response.status);
            
            if (!response.ok) {
                console.error('HTTP error:', response.status, response.statusText);
                return { 
                    status: 'error', 
                    message: `Server error: ${response.status} ${response.statusText}` 
                };
            }

            const data = await response.json();
            console.log('Login response data:', data);
            
            if (data.status === 'success') {
                // Validate the response data
                if (!data.data || !data.data.token || !data.data.user) {
                    console.error('Invalid login response format:', data);
                    return { 
                        status: 'error', 
                        message: 'Invalid response from server' 
                    };
                }
                
                this.token = data.data.token;
                this.user = data.data.user;
                
                // Store in sessionStorage
                sessionStorage.setItem('auth_token', this.token);
                sessionStorage.setItem('user', JSON.stringify(this.user));
                
                console.log('Login successful, user:', this.user);
                console.log('Token stored:', this.token ? 'Yes' : 'No');
                
                // Update navigation for SPAPP
                this.updateNavigation();
                
                return data;
            } else {
                console.error('Login failed:', data.message);
                return data;
            }
            
        } catch (error) {
            console.error('Login network error:', error);
            return { 
                status: 'error', 
                message: `Network error: ${error.message}` 
            };
        }
    },

    async register(userData) {
        console.log('Auth.register called with data:', userData);
        
        try {
            const response = await fetch('https://yapp-backend-ixlrf.ondigitalocean.app/users', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(userData)
            });

            const data = await response.json();
            console.log('Registration response:', data);
            return data;
            
        } catch (error) {
            console.error('Registration network error:', error);
            return { 
                status: 'error', 
                message: `Network error: ${error.message}` 
            };
        }
    },

    logout() {
        console.log('Auth.logout called');
        
        this.token = null;
        this.user = null;
        sessionStorage.removeItem('auth_token');
        sessionStorage.removeItem('user');
        
        this.updateNavigation();
        
        // Force redirect to login
        window.location.hash = '#login';
        
        // Trigger SPAPP route change
        if (typeof $ !== 'undefined') {
            $(window).trigger('hashchange');
        }
    },

    getHeaders() {
        if (!this.isAuthenticated()) {
            console.warn('Trying to get headers but user not authenticated');
            return { 'Content-Type': 'application/json' };
        }
        
        return {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.token}`
        };
    },

    updateNavigation() {
        console.log('Updating navigation, authenticated:', this.isAuthenticated());
        
        // Update the login/logout button in your existing navigation
        const loginButton = document.querySelector('.button-login, .button-logout');
        
        if (this.isAuthenticated()) {
            // User is logged in - show logout
            if (loginButton) {
                loginButton.textContent = 'Logout';
                loginButton.className = 'btn btn-outline-danger button-logout fw-bold';
                
                // Remove existing click handler and add logout handler
                loginButton.onclick = (e) => {
                    e.preventDefault();
                    this.logout();
                };
            }
            
            // Update profile link to show current user
            const profileImg = document.querySelector('.profile-img');
            if (profileImg) {
                profileImg.title = this.user.name || this.user.username;
            }
        } else {
            // User is not logged in - show login button
            if (loginButton) {
                loginButton.textContent = 'Log In';
                loginButton.className = 'btn btn-outline-danger button-login fw-bold';
                loginButton.onclick = (e) => {
                    e.preventDefault();
                    window.location.hash = '#login';
                };
            }
        }
    },

    requireAuth() {
        const authenticated = this.isAuthenticated();
        console.log('Auth.requireAuth called, authenticated:', authenticated);
        
        if (!authenticated) {
            console.log('Redirecting to login...');
            window.location.hash = '#login';
            return false;
        }
        return true;
    },

    // Debug method to check current state
    debug() {
        return {
            token: this.token ? 'Present' : 'Missing',
            user: this.user,
            isAuthenticated: this.isAuthenticated(),
            sessionToken: sessionStorage.getItem('auth_token') ? 'Present' : 'Missing',
            sessionUser: sessionStorage.getItem('user')
        };
    }
};

// Debug authentication state on load
console.log('Auth initialized:', Auth.debug());
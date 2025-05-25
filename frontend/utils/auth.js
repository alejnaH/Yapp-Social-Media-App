const Auth = {
    token: localStorage.getItem('auth_token'),
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    
    isAuthenticated() {
        return this.token && this.user;
    },
    
    isAdmin() {
        return this.user && this.user.role === 'admin';
    },
    
    login(username, password) {
        return fetch('backend/api/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                this.token = data.data.token;
                this.user = data.data.user;
                localStorage.setItem('auth_token', this.token);
                localStorage.setItem('user', JSON.stringify(this.user));
            }
            return data;
        });
    },
    
    logout() {
        this.token = null;
        this.user = null;
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
    },
    
    getHeaders() {
        return {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.token}`
        };
    }
};

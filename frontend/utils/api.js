const API = {
    baseURL: 'https://yapp-backend-ixlrf.ondigitalocean.app/api',

    async call(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const defaultOptions = {
        headers: Auth.isAuthenticated() ? Auth.getHeaders() : { 'Content-Type': 'application/json' }
    };

    const config = { ...defaultOptions, ...options };
    
    // Enhanced logging
    console.log('API Call Details:', {
        url: url,
        method: config.method || 'GET',
        headers: config.headers,
        body: config.body,
        authenticated: Auth.isAuthenticated()
    });
    
    try {
        const response = await fetch(url, config);
        console.log('API Response Status:', response.status);
        
        const data = await response.json();
        console.log('API Response Data:', data);

        if (response.status === 401) {
            console.log('Authentication failed, logging out...');
            Auth.logout();
            return { status: 'error', message: 'Session expired. Please login again.' };
        }
        
        return data;
    } catch (error) {
        console.error('API call error details:', {
            url: url,
            error: error,
            message: error.message,
            stack: error.stack
        });
        return { status: 'error', message: 'Network error occurred: ' + error.message };
    }
},

    posts: {
        getAll: () => API.call('/posts'),
        getById: (id) => API.call(`/posts/${id}`),
        getByUser: (userId) => API.call(`/posts/user/${userId}`),
        getByCommunity: (communityId) => API.call(`/posts/community/${communityId}`),
        create: (postData) => API.call('/posts', {
            method: 'POST',
            body: JSON.stringify(postData)
        }),
        update: (id, postData) => API.call(`/posts/${id}`, {
            method: 'PUT',
            body: JSON.stringify(postData)
        }),
        delete: (id) => API.call(`/posts/${id}`, { method: 'DELETE' })
    },

    comments: {
        getAll: () => API.call('/comments'),
        getById: (id) => API.call(`/comments/${id}`),
        getByPost: (postId) => API.call(`/comments/post/${postId}`),
        getByUser: (userId) => API.call(`/comments/user/${userId}`),
        create: (commentData) => API.call('/comments', {
            method: 'POST',
            body: JSON.stringify(commentData)
        }),
        update: (id, commentData) => API.call(`/comments/${id}`, {
            method: 'PUT',
            body: JSON.stringify(commentData)
        }),
        delete: (id) => API.call(`/comments/${id}`, { method: 'DELETE' })
    },

    communities: {
        getAll: () => API.call('/communities'),
        getById: (id) => API.call(`/communities/${id}`),
        create: (communityData) => API.call('/communities', {
            method: 'POST',
            body: JSON.stringify(communityData)
        }),
        update: (id, communityData) => API.call(`/communities/${id}`, {
            method: 'PUT',
            body: JSON.stringify(communityData)
        }),
        delete: (id) => API.call(`/communities/${id}`, { method: 'DELETE' })
    },

    users: {
        getAll: () => API.call('/users'),
        getById: (id) => API.call(`/users/${id}`),
        create: (userData) => API.call('/users', {
            method: 'POST',
            body: JSON.stringify(userData)
        }),
        update: (id, userData) => API.call(`/users/${id}`, {
            method: 'PUT',
            body: JSON.stringify(userData)
        }),
        delete: (id) => API.call(`/users/${id}`, { method: 'DELETE' })
    },

    countries: {
        getAll: () => API.call('/countries'),
        getById: (id) => API.call(`/countries/${id}`),
        create: (countryData) => API.call('/countries', {
            method: 'POST',
            body: JSON.stringify(countryData)
        }),
        update: (id, countryData) => API.call(`/countries/${id}`, {
            method: 'PUT',
            body: JSON.stringify(countryData)
        }),
        delete: (id) => API.call(`/countries/${id}`, { method: 'DELETE' })
    },

    postLikes: {
        like: (postId, userId) => API.call('/post-likes', {
            method: 'POST',
            body: JSON.stringify({ 
                postId: parseInt(postId), 
                userId: parseInt(userId) 
            })
        }),
        unlike: (userId, postId) => API.call(`/post-likes/user/${userId}/post/${postId}`, {
            method: 'DELETE'
        }),
        getCount: (postId) => API.call(`/post-likes/count/${postId}`),
        getByUser: (userId, postId) => API.call(`/post-likes/user/${userId}/post/${postId}`)
    },

    communityPosts: {
        getAll: () => API.call('/community-posts'),
        getByCommunity: (communityId) => API.call(`/community-posts/community/${communityId}`),
        getByPost: (postId) => API.call(`/community-posts/post/${postId}`),
        addToComm: (postId, communityId) => API.call('/community-posts', {
            method: 'POST',
            body: JSON.stringify({ postId, communityId })
        }),
        remove: (id) => API.call(`/community-posts/${id}`, { method: 'DELETE' })
    }
};

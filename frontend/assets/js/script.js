$(document).ready(function() {
    console.log('Script.js loaded, setting up SPAPP integration...');
    
    // Update navigation on app start
    Auth.updateNavigation();

    // Handle navigation clicks
    $(document).on('click', 'a[href^="#"]', function(e) {
        const hash = $(this).attr('href');
        
        // Check authentication for protected routes
        if (['#dashboard', '#community', '#profile', '#post'].includes(hash.split('?')[0])) {
            if (!Auth.requireAuth()) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Handle logout button click in navigation
    $(document).on('click', '.button-logout', function(e) {
        e.preventDefault();
        Auth.logout();
    });

    // Handle login button click in navigation
    $(document).on('click', '.button-login', function(e) {
        e.preventDefault();
        window.location.hash = '#login';
    });

    // SPAPP page loaded event handler
    $(document).on('spapp.page.loaded', function(event, data) {
        console.log('SPAPP page loaded event fired:', data.req);
        
        // Load content based on which page was loaded
        switch(data.req) {
            case 'dashboard':
                console.log('Loading dashboard via SPAPP event...');
                setTimeout(() => {
                    if (typeof loadDashboardContent === 'function') {
                        loadDashboardContent();
                    } else {
                        console.error('loadDashboardContent function not found');
                    }
                }, 100);
                break;
                
            case 'community':
                console.log('Loading community via SPAPP event...');
                setTimeout(() => {
                    if (typeof loadCommunityContent === 'function') {
                        loadCommunityContent();
                    } else {
                        console.error('loadCommunityContent function not found');
                    }
                }, 100);
                break;
                
            case 'profile':
                console.log('Loading profile via SPAPP event...');
                setTimeout(() => {
                    if (typeof loadProfileContent === 'function') {
                        loadProfileContent();
                    } else {
                        console.error('loadProfileContent function not found');
                    }
                }, 100);
                break;
                
            case 'post':
                console.log('SPAPP loaded post template, now loading content...');
                setTimeout(() => {
                    if (typeof loadPostContent === 'function') {
                        loadPostContent();
                    } else {
                        console.error('loadPostContent function not found');
                    }
                }, 100);
                break;
                
            case 'login':
                // Clear form when navigating to login
                setTimeout(() => {
                    const form = document.getElementById('loginForm');
                    if (form) form.reset();
                    const alerts = document.getElementById('login-alerts');
                    if (alerts) alerts.innerHTML = '';
                }, 100);
                break;
                
            case 'signup':
                // Clear form when navigating to signup
                setTimeout(() => {
                    const form = document.getElementById('signupForm');
                    if (form) form.reset();
                    const alerts = document.getElementById('signup-alerts');
                    if (alerts) alerts.innerHTML = '';
                }, 100);
                break;
        }
    });

    // Use hashchange event as backup AND to handle post parameters
    // 
    $(window).on('hashchange', function() {
        const hash = window.location.hash;
        console.log('Hash changed to:', hash);
        
        // Handle different routes
        if (hash === '#dashboard') {
            setTimeout(() => {
                if (typeof loadDashboardContent === 'function') {
                    console.log('Loading dashboard via hashchange...');
                    loadDashboardContent();
                }
            }, 200);
        } else if (hash === '#community') {
            setTimeout(() => {
                if (typeof loadCommunityContent === 'function') {
                    console.log('Loading community via hashchange...');
                    loadCommunityContent();
                }
            }, 200);
        } else if (hash === '#profile') {
            setTimeout(() => {
                if (typeof loadProfileContent === 'function') {
                    console.log('Loading profile via hashchange...');
                    loadProfileContent();
                }
            }, 200);
        } else if (hash.startsWith('#post?id=')) {
            // SPECIAL HANDLING for post with parameters
            console.log('Detected post with parameter, handling specially...');
            
            // First, make sure SPAPP loads the post template
            // We'll navigate to #post first, then load content with parameter
            setTimeout(() => {
                const postContainer = document.getElementById('post-content');
                if (!postContainer) {
                    console.log('Post container not found, forcing SPAPP to load post template...');
                    
                    // Store the current hash with parameter
                    const targetHash = hash;
                    
                    // Navigate to #post to make SPAPP load the template
                    window.location.hash = '#post';
                    
                    // Wait for SPAPP to load the template, then restore the hash with parameter
                    setTimeout(() => {
                        console.log('Template loaded, restoring hash with parameter...');
                        window.location.hash = targetHash;
                        
                        // Now load the post content
                        setTimeout(() => {
                            if (typeof loadPostContent === 'function') {
                                console.log('Loading post content after template load...');
                                loadPostContent();
                            }
                        }, 100);
                    }, 300);
                } else {
                    // Container exists, load content directly
                    console.log('Post container found, loading content directly...');
                    if (typeof loadPostContent === 'function') {
                        loadPostContent();
                    }
                }
            }, 100);
        }
    });

    // FORCE INITIAL CONTENT LOAD
    // Sometimes SPAPP events don't fire on initial load
    setTimeout(() => {
        const currentHash = window.location.hash;
        console.log('Force loading content for hash:', currentHash);
        
        if (currentHash === '#dashboard' || currentHash === '') {
            if (Auth.isAuthenticated() && typeof loadDashboardContent === 'function') {
                console.log('Force loading dashboard...');
                loadDashboardContent();
            }
        } else if (currentHash === '#community') {
            if (Auth.isAuthenticated() && typeof loadCommunityContent === 'function') {
                console.log('Force loading community...');
                loadCommunityContent();
            }
        } else if (currentHash === '#profile') {
            if (Auth.isAuthenticated() && typeof loadProfileContent === 'function') {
                console.log('Force loading profile...');
                loadProfileContent();
            }
        } else if (currentHash.startsWith('#post?id=')) {
            // Handle post with parameter on initial load
            console.log('Force loading post with parameter...');
            
            const postContainer = document.getElementById('post-content');
            if (!postContainer) {
                console.log('No post container on initial load, navigating to #post first...');
                // Store the target hash
                const targetHash = currentHash;
                
                // Navigate to #post to load template
                window.location.hash = '#post';
                
                // Wait for template to load, then restore hash and load content
                setTimeout(() => {
                    window.location.hash = targetHash;
                    setTimeout(() => {
                        if (Auth.isAuthenticated() && typeof loadPostContent === 'function') {
                            loadPostContent();
                        }
                    }, 100);
                }, 300);
            } else {
                // Container exists, load content
                if (Auth.isAuthenticated() && typeof loadPostContent === 'function') {
                    loadPostContent();
                }
            }
        }
    }, 500);

    // Global error handler
    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled promise rejection:', event.reason);
    });

    // Auto-refresh authentication status
    setInterval(() => {
        if (Auth.isAuthenticated()) {
            // Check if token is still valid by making a test API call
            API.call('/posts').then(result => {
                if (result.status === 'error' && result.message.includes('Session expired')) {
                    Auth.logout();
                }
            }).catch(() => {
                // Network error, don't logout automatically
            });
        }
    }, 300000); // Check every 5 minutes

    // Initialize tooltips and other Bootstrap components
    if (typeof bootstrap !== 'undefined') {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

// Profile loading function
async function loadProfileContent() {
    console.log('loadProfileContent called...');
    
    if (!Auth.isAuthenticated()) {
        window.location.hash = '#login';
        return;
    }

    const container = document.getElementById('profile-content');
    if (!container) {
        console.error('Profile container not found');
        return;
    }
    
    const userId = Auth.user.user_id || Auth.user.id;
    
    try {
        console.log('Fetching user profile for ID:', userId);
        const result = await API.users.getById(userId);
        console.log('Profile result:', result);
        
        if (result.status === 'success' && result.data) {
            const user = result.data;
            
            container.innerHTML = `
                <div class="text-center">
                    <img src="frontend/assets/images/profile-icon.png" alt="Profile" class="rounded-circle mb-3" style="width: 120px; height: 120px;">
                    <h4 class="card-title mb-3">${user.name || user.username}</h4>
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <span id="profile-text" class="me-2">
                            ${user.email ? 'Active member of the community' : 'Community member'}
                        </span>
                        <button class="btn btn-sm btn-outline-primary" onclick="editProfileText()">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <div class="text-start mt-4">
                        <p><strong>Username:</strong> ${user.username}</p>
                        <p><strong>Email:</strong> ${user.email || 'Not specified'}</p>
                        <p><strong>Country:</strong> ${user.country_name || 'Not specified'}</p>
                        <p><strong>Role:</strong> ${user.role || 'User'}</p>
                        <p class="mb-0"><strong>Joined:</strong> ${formatDate(user.created_at)}</p>
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-outline-danger" onclick="Auth.logout()">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </div>
                </div>
            `;
            console.log('Profile content loaded successfully');
        } else {
            console.error('Failed to load profile:', result);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Error loading profile
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading profile:', error);
        container.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Error loading profile
            </div>
        `;
    }
}

// Post loading function  
async function loadPostContent() {
    console.log('loadPostContent called...');
    
    if (!Auth.isAuthenticated()) {
        window.location.hash = '#login';
        return;
    }

    // Get post ID from URL hash
    const hash = window.location.hash;
    console.log('Current hash for post:', hash);
    
    const match = hash.match(/id=(\d+)/);
    if (!match) {
        console.error('No post ID found in hash:', hash);
        const container = document.getElementById('post-content');
        if (container) {
            container.innerHTML = `
                <div class="card-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Invalid post ID
                    </div>
                </div>
            `;
        }
        return;
    }

    const currentPostId = parseInt(match[1]);
    console.log('Loading post ID:', currentPostId);
    
    // Wait for container to exist (with retries)
    let retries = 0;
    const maxRetries = 10;
    
    const waitForContainer = () => {
        const container = document.getElementById('post-content');
        
        if (container) {
            console.log('Post container found, loading content...');
            loadPostContentWithContainer(container, currentPostId);
        } else {
            retries++;
            console.log(`Post container not found, retry ${retries}/${maxRetries}...`);
            
            if (retries < maxRetries) {
                setTimeout(waitForContainer, 100);
            } else {
                console.error('Post container not found after max retries');
            }
        }
    };
    
    waitForContainer();
}

// Separated function to load post content once container exists
async function loadPostContentWithContainer(container, currentPostId) {
    try {
        console.log('Fetching post data for ID:', currentPostId);
        const result = await API.posts.getById(currentPostId);
        console.log('Post result:', result);
        
        if (result.status === 'success' && result.data) {
            const post = result.data;
            
            const likeResult = await API.postLikes.getCount(currentPostId);
            const likeCount = likeResult.status === 'success' ? likeResult.data.count : 0;
            
            let isLiked = false;
            if (Auth.isAuthenticated()) {
                const userId = Auth.user.user_id || Auth.user.id;
                const userLikes = await API.postLikes.getByUser(userId);
                if (userLikes.status === 'success') {
                    isLiked = userLikes.data.some(like => like.postId == currentPostId);
                }
            }

            const canModify = canUserModify(post.userId, Auth.user.role);
            const editDeleteButtons = canModify ? `
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="event.preventDefault(); event.stopPropagation(); editPost(${post.id}, ${JSON.stringify(post.title).replace(/"/g, '&quot;')}, ${JSON.stringify(post.body).replace(/"/g, '&quot;')});">
                            <i class="fas fa-edit"></i> Edit
                        </a></li>
                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="event.preventDefault(); event.stopPropagation(); deletePost(${post.id}, ${JSON.stringify(post.title).replace(/"/g, '&quot;')});">
                            <i class="fas fa-trash"></i> Delete
                        </a></li>
                    </ul>
                </div>
            ` : '';

            container.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex">
                            <img src="frontend/assets/images/profile-icon.png" class="rounded-circle me-3" alt="Avatar" style="width: 50px; height: 50px;">
                            <div>
                                <h5 class="mb-1">${post.user_name || post.username}</h5>
                                <p class="text-muted small">Posted ${formatDate(post.created_at)}</p>
                            </div>
                        </div>
                        ${editDeleteButtons}
                    </div>
                    <h4 class="mt-3">${post.title}</h4>
                    <p class="mt-3">${post.body}</p>
                    <div class="d-flex">
                        <button class="btn btn-sm btn-outline-primary me-2 ${isLiked ? 'liked' : ''}" onclick="toggleLike(${post.id}, this)">
                            <i class="fas fa-heart"></i> <span id="post-likes">${isLiked ? 'Liked' : 'Like'} (${likeCount})</span>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-comment"></i> <span id="post-comments-count">Comments</span>
                        </button>
                    </div>
                </div>
            `;
            
            const commentsSection = document.getElementById('comments-section');
            if (commentsSection) {
                commentsSection.style.display = 'block';
                loadPostComments(currentPostId);
            }
            
            console.log('Post content loaded successfully');
            
        } else {
            console.error('Post not found:', result);
            container.innerHTML = `
                <div class="card-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Post not found
                    </div>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading post:', error);
        container.innerHTML = `
            <div class="card-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Error loading post
                </div>
            </div>
        `;
    }
}

// Load comments for a post
async function loadPostComments(postId) {
    console.log('Loading comments for post:', postId);
    
    const container = document.getElementById('comments-container');
    if (!container) {
        console.error('Comments container not found');
        return;
    }
    
    container.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div></div>';
    
    try {
        const result = await API.comments.getByPost(postId);
        console.log('Comments result:', result);
        
        if (result.status === 'success') {
            let commentsHTML = '';
            
            if (result.data.length === 0) {
                commentsHTML = `
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <p>No comments yet. Be the first to comment!</p>
                    </div>
                `;
            } else {
                result.data.forEach(comment => {
                    const canModify = canUserModify(comment.userId, Auth.user.role);
                    const editDeleteButtons = canModify ? `
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="event.preventDefault(); event.stopPropagation(); editComment(${comment.id}, ${JSON.stringify(comment.body).replace(/"/g, '&quot;')});">
                                    <i class="fas fa-edit"></i> Edit
                                </a></li>
                                <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="event.preventDefault(); event.stopPropagation(); deleteComment(${comment.id});">
                                    <i class="fas fa-trash"></i> Delete
                                </a></li>
                            </ul>
                        </div>
                    ` : '';

                    commentsHTML += `
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="d-flex">
                                        <img src="frontend/assets/images/profile-icon.png" class="rounded-circle me-2" alt="Avatar" style="width: 40px; height: 40px;">
                                        <div>
                                            <h6 class="mb-0">${comment.user_name || comment.username}</h6>
                                            <small class="text-muted">${formatDate(comment.created_at)}</small>
                                        </div>
                                    </div>
                                    ${editDeleteButtons}
                                </div>
                                <p class="mb-0">${comment.body}</p>
                            </div>
                        </div>
                    `;
                });
            }
            
            container.innerHTML = commentsHTML;
            
            const countElement = document.getElementById('post-comments-count');
            if (countElement) {
                countElement.textContent = `Comments (${result.data.length})`;
            }
            
            console.log('Comments loaded successfully');
        }
    } catch (error) {
        console.error('Error loading comments:', error);
        container.innerHTML = '<div class="alert alert-danger">Error loading comments</div>';
    }
}

// Global profile edit function
window.editProfileText = function() {
    var textElement = document.getElementById("profile-text");
    if (!textElement) return;
    
    var currentText = textElement.innerText;
    var newText = prompt("Edit your description:", currentText);
    
    if (newText !== null && newText.trim() !== "") {
        textElement.innerText = newText;
        showAlert('Description updated!', 'success');
    }
};

// Make functions globally available
window.loadProfileContent = loadProfileContent;
window.loadPostContent = loadPostContent;
window.loadPostComments = loadPostComments;

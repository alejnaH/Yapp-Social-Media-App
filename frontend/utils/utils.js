// Utility functions for the application
function showAlert(message, type = 'info', container = null) {
    const alertHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const targetContainer = container || document.querySelector('#spapp .active') || document.querySelector('#spapp');
    if (targetContainer) {
        targetContainer.insertAdjacentHTML('afterbegin', alertHTML);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            const alert = targetContainer.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
}

function formatDate(dateString) {
    if (!dateString) return 'Unknown';
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    
    // Less than 1 minute
    if (diff < 60000) return 'Just now';
    
    // Less than 1 hour
    if (diff < 3600000) {
        const minutes = Math.floor(diff / 60000);
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    }
    
    // Less than 1 day
    if (diff < 86400000) {
        const hours = Math.floor(diff / 3600000);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    }
    
    // Less than 1 week
    if (diff < 604800000) {
        const days = Math.floor(diff / 86400000);
        return `${days} day${days > 1 ? 's' : ''} ago`;
    }
    
    // Default to full date
    return date.toLocaleDateString();
}

// Global functions for post interactions
window.toggleLike = async function(postId, button) {
    if (!Auth.isAuthenticated()) {
        showAlert('Please log in to like posts', 'warning');
        return;
    }
    
    // Disable button during request to prevent double-clicking
    button.disabled = true;
    
    try {
        const userId = Auth.user.user_id || Auth.user.id;
        
        // Check current like status from the button's class
        const isCurrentlyLiked = button.classList.contains('liked');
        
        console.log('Toggle like - Post:', postId, 'User:', userId, 'Currently liked:', isCurrentlyLiked);
        
        let result;
        
        if (isCurrentlyLiked) {
            // User has liked it, so unlike it
            console.log('Attempting to unlike post...');
            result = await API.postLikes.unlike(userId, postId);
        } else {
            // User hasn't liked it, so like it
            console.log('Attempting to like post...');
            result = await API.postLikes.like(postId, userId);
        }
        
        console.log('Like/unlike result:', result);
        
        if (result.status === 'success') {
            // Get updated like count
            const countResult = await API.postLikes.getCount(postId);
            const newCount = countResult.status === 'success' ? countResult.data.count : 0;
            
            // Update button appearance and text
            const icon = button.querySelector('i');
            const spanElement = button.querySelector('span');
            
            if (isCurrentlyLiked) {
                // Was liked, now unliked
                button.classList.remove('liked');
                icon.style.color = '';
                if (spanElement) {
                    spanElement.textContent = `Like (${newCount})`;
                } else {
                    button.innerHTML = `<i class="fas fa-heart"></i> Like (${newCount})`;
                }
                console.log('Post unliked successfully');
            } else {
                // Was not liked, now liked
                button.classList.add('liked');
                icon.style.color = 'red';
                if (spanElement) {
                    spanElement.textContent = `Liked (${newCount})`;
                } else {
                    button.innerHTML = `<i class="fas fa-heart" style="color: red;"></i> Liked (${newCount})`;
                }
                console.log('Post liked successfully');
            }

            updateAllLikeButtonsForPost(postId, !isCurrentlyLiked, newCount);
            
        } else {
            console.error('Like/unlike error:', result);
            showAlert(result.message || 'Error updating like status', 'danger');
        }
    } catch (error) {
        console.error('Error toggling like:', error);
        showAlert('Error updating like status: ' + error.message, 'danger');
    } finally {
        // Re-enable button
        button.disabled = false;
    }
};

// Helper function to update all like buttons for a specific post
// I tried to to make the liked buttons have a red heart icon but it didn't work, probably because of the spapp
function updateAllLikeButtonsForPost(postId, isLiked, likeCount) {
    // Find all like buttons for this post (there might be multiple on dashboard/community pages)
    const allLikeButtons = document.querySelectorAll(`[onclick*="toggleLike(${postId}"]`);
    
    allLikeButtons.forEach(button => {
        const icon = button.querySelector('i');
        const spanElement = button.querySelector('span');
        
        if (isLiked) {
            button.classList.add('liked');
            if (icon) icon.style.color = 'red';
            if (spanElement) {
                spanElement.textContent = `Liked (${likeCount})`;
            } else {
                button.innerHTML = `<i class="fas fa-heart" style="color: red;"></i> Liked (${likeCount})`;
            }
        } else {
            button.classList.remove('liked');
            if (icon) icon.style.color = '';
            if (spanElement) {
                spanElement.textContent = `Like (${likeCount})`;
            } else {
                button.innerHTML = `<i class="fas fa-heart"></i> Like (${likeCount})`;
            }
        }
    });
}

// FIXED: viewPost function for SPAPP compatibility
window.viewPost = function(postId) {
    console.log('viewPost called with ID:', postId);
    
    // Use SPAPP-compatible navigation
    const newHash = `#post?id=${postId}`;
    console.log('Navigating to:', newHash);
    
    window.location.hash = newHash;
    
    // Force trigger hashchange event for SPAPP
    setTimeout(() => {
        $(window).trigger('hashchange');
    }, 100);
};

window.showCreatePostModal = function() {
    if (!Auth.isAuthenticated()) {
        showAlert('Please log in to create posts', 'warning');
        return;
    }
    
    // Create modal HTML if it doesn't exist
    let modal = document.getElementById('createPostModal');
    if (!modal) {
        const modalHTML = `
        <div class="modal fade" id="createPostModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Post</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="createPostForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="postTitle" class="form-label">Title</label>
                                <input type="text" class="form-control" id="postTitle" required>
                            </div>
                            <div class="mb-3">
                                <label for="postBody" class="form-label">Content</label>
                                <textarea class="form-control" id="postBody" rows="5" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Add form handler
        document.getElementById('createPostForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleCreatePost();
        });
    }
    
    const modalInstance = new bootstrap.Modal(document.getElementById('createPostModal'));
    modalInstance.show();
};

async function handleCreatePost() {
    const title = document.getElementById('postTitle').value;
    const body = document.getElementById('postBody').value;
    const userId = Auth.user.user_id || Auth.user.id;
    
    try {
        const result = await API.posts.create({ title, body, userId });
        
        if (result.status === 'success') {
            const modal = bootstrap.Modal.getInstance(document.getElementById('createPostModal'));
            modal.hide();
            document.getElementById('createPostForm').reset();
            showAlert('Post created successfully!', 'success');
            
            // Reload current page content
            if (window.location.hash.includes('dashboard')) {
                loadDashboardContent();
            } else if (window.location.hash.includes('community')) {
                loadCommunityContent();
            }
        } else {
            showAlert(result.message || 'Error creating post', 'danger');
        }
    } catch (error) {
        console.error('Error creating post:', error);
        showAlert('Error creating post', 'danger');
    }
}

// Load functions for different pages
async function loadDashboardContent() {
    console.log('Loading dashboard content...');
    
    if (!Auth.isAuthenticated()) {
        window.location.hash = '#login';
        return;
    }

    const container = document.getElementById('dashboard-posts');
    if (!container) {
        console.error('Dashboard container not found');
        return;
    }
    
    try {
        console.log('Fetching posts...');
        const result = await API.posts.getAll();
        console.log('Posts result:', result);
        
        if (result.status === 'success' && result.data) {
            let postsHTML = '';
            
            if (result.data.length === 0) {
                postsHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No posts yet</h5>
                        <p class="text-muted">Be the first to create a post!</p>
                        <button class="btn btn-primary" onclick="showCreatePostModal()">Create Post</button>
                    </div>
                `;
            } else {
                for (const post of result.data) {
                    const likeResult = await API.postLikes.getCount(post.id);
                    const likeCount = likeResult.status === 'success' ? likeResult.data.count : 0;
                    
                    let isLiked = false;
                    if (Auth.isAuthenticated()) {
                        const userId = Auth.user.user_id || Auth.user.id;
                        const userLikes = await API.postLikes.getByUser(userId);
                        if (userLikes.status === 'success') {
                            isLiked = userLikes.data.some(like => like.postId == post.id);
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

                    postsHTML += `
                        <div class="card mb-3 shadow-sm">
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
                                <h6 class="mt-3">${post.title}</h6>
                                <p class="mt-3">${post.body.length > 200 ? post.body.substring(0, 200) + '...' : post.body}</p>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary me-2 ${isLiked ? 'liked' : ''}" onclick="toggleLike(${post.id}, this)">
                                            <i class="fas fa-heart"></i> ${isLiked ? 'Liked' : 'Like'} (${likeCount})
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="viewPost(${post.id})">
                                            <i class="fas fa-comment"></i> Comments
                                        </button>
                                    </div>
                                    <button class="btn btn-sm btn-link" onclick="viewPost(${post.id})">View Full Post</button>
                                </div>
                            </div>
                        </div>
                    `;
                }
            }
            
            container.innerHTML = postsHTML;
            console.log('Dashboard content loaded successfully');
        } else {
            console.error('Failed to load posts:', result);
            container.innerHTML = `
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i> No posts available
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading dashboard:', error);
        container.innerHTML = `
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-circle"></i> Error loading posts
            </div>
        `;
    }
}

async function loadCommunityContent() {
    console.log('Loading community content...');
    
    if (!Auth.isAuthenticated()) {
        window.location.hash = '#login';
        return;
    }

    const container = document.getElementById('community-posts');
    if (!container) {
        console.error('Community container not found');
        return;
    }
    
    try {
        console.log('Fetching community posts...');
        const result = await API.posts.getAll();
        console.log('Community posts result:', result);
        
        if (result.status === 'success' && result.data) {
            let postsHTML = '';
            
            if (result.data.length === 0) {
                postsHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No community posts yet</h5>
                        <p class="text-muted">Start the conversation!</p>
                        <button class="btn btn-primary" onclick="showCreatePostModal()">Create Post</button>
                    </div>
                `;
            } else {
                for (const post of result.data) {
                    const likeResult = await API.postLikes.getCount(post.id);
                    const likeCount = likeResult.status === 'success' ? likeResult.data.count : 0;
                    
                    const commentsResult = await API.comments.getByPost(post.id);
                    const commentCount = commentsResult.status === 'success' ? commentsResult.data.length : 0;
                    
                    let isLiked = false;
                    if (Auth.isAuthenticated()) {
                        const userId = Auth.user.user_id || Auth.user.id;
                        const userLikes = await API.postLikes.getByUser(userId);
                        if (userLikes.status === 'success') {
                            isLiked = userLikes.data.some(like => like.postId == post.id);
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

                    postsHTML += `
                        <div class="card mb-3 shadow-sm">
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
                                <h6 class="mt-3">${post.title}</h6>
                                <p class="mt-3">${post.body}</p>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary me-2 ${isLiked ? 'liked' : ''}" onclick="toggleLike(${post.id}, this)">
                                            <i class="fas fa-heart"></i> ${isLiked ? 'Liked' : 'Like'} (${likeCount})
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="viewPost(${post.id})">
                                            <i class="fas fa-comment"></i> Comments (${commentCount})
                                        </button>
                                    </div>
                                    <button class="btn btn-sm btn-link" onclick="viewPost(${post.id})">View Full Post</button>
                                </div>
                            </div>
                        </div>
                    `;
                }
            }
            
            container.innerHTML = postsHTML;
            console.log('Community content loaded successfully');
        } else {
            console.error('Failed to load community posts:', result);
            container.innerHTML = `
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i> No community posts available
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading community posts:', error);
        container.innerHTML = `
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-circle"></i> Error loading community posts
            </div>
        `;
    }
}

window.editPost = function(postId, currentTitle, currentBody) {
    if (!Auth.isAuthenticated()) {
        showAlert('Please log in to edit posts', 'warning');
        return;
    }
    
    // Prevent event bubbling
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    let modal = document.getElementById('editPostModal');
    if (!modal) {
        const modalHTML = `
        <div class="modal fade" id="editPostModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Post</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editPostForm">
                        <div class="modal-body">
                            <input type="hidden" id="editPostId">
                            <div class="mb-3">
                                <label for="editPostTitle" class="form-label">Title</label>
                                <input type="text" class="form-control" id="editPostTitle" required>
                            </div>
                            <div class="mb-3">
                                <label for="editPostBody" class="form-label">Content</label>
                                <textarea class="form-control" id="editPostBody" rows="5" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        document.getElementById('editPostForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            await handleEditPost();
        });
    }
    
    document.getElementById('editPostId').value = postId;
    document.getElementById('editPostTitle').value = currentTitle;
    document.getElementById('editPostBody').value = currentBody;
    
    const modalInstance = new bootstrap.Modal(document.getElementById('editPostModal'));
    modalInstance.show();
};

async function handleEditPost() {
    const postId = document.getElementById('editPostId').value;
    const title = document.getElementById('editPostTitle').value;
    const body = document.getElementById('editPostBody').value;
    const userId = Auth.user.user_id || Auth.user.id;
    
    try {
        const result = await API.posts.update(postId, { title, body, userId });
        
        if (result.status === 'success') {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editPostModal'));
            modal.hide();
            showAlert('Post updated successfully!', 'success');
            
            if (window.location.hash.includes('dashboard')) {
                loadDashboardContent();
            } else if (window.location.hash.includes('community')) {
                loadCommunityContent();
            } else if (window.location.hash.includes('post')) {
                loadPostContent();
            }
        } else {
            showAlert(result.message || 'Error updating post', 'danger');
        }
    } catch (error) {
        console.error('Error updating post:', error);
        showAlert('Error updating post', 'danger');
    }
}

window.deletePost = function(postId, postTitle) {
    if (!Auth.isAuthenticated()) {
        showAlert('Please log in to delete posts', 'warning');
        return;
    }
    
    // Prevent event bubbling
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    if (confirm(`Are you sure you want to delete the post "${postTitle}"? This action cannot be undone.`)) {
        handleDeletePost(postId);
    }
};

async function handleDeletePost(postId) {
    try {
        const result = await API.posts.delete(postId);
        
        if (result.status === 'success') {
            showAlert('Post deleted successfully!', 'success');
            
            if (window.location.hash.includes('post')) {
                window.location.hash = '#dashboard';
            } else if (window.location.hash.includes('dashboard')) {
                loadDashboardContent();
            } else if (window.location.hash.includes('community')) {
                loadCommunityContent();
            }
        } else {
            showAlert(result.message || 'Error deleting post', 'danger');
        }
    } catch (error) {
        console.error('Error deleting post:', error);
        showAlert('Error deleting post', 'danger');
    }
}

window.editComment = function(commentId, currentBody) {
    if (!Auth.isAuthenticated()) {
        showAlert('Please log in to edit comments', 'warning');
        return;
    }
    
    let modal = document.getElementById('editCommentModal');
    if (!modal) {
        const modalHTML = `
        <div class="modal fade" id="editCommentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editCommentForm">
                        <div class="modal-body">
                            <input type="hidden" id="editCommentId">
                            <div class="mb-3">
                                <label for="editCommentBody" class="form-label">Comment</label>
                                <textarea class="form-control" id="editCommentBody" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Comment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        document.getElementById('editCommentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleEditComment();
        });
    }
    
    document.getElementById('editCommentId').value = commentId;
    document.getElementById('editCommentBody').value = currentBody;
    
    const modalInstance = new bootstrap.Modal(document.getElementById('editCommentModal'));
    modalInstance.show();
};

async function handleEditComment() {
    const commentId = document.getElementById('editCommentId').value;
    const postId = document.getElementById('editCommentPostId').value;
    const body = document.getElementById('editCommentBody').value;
    
    try {
        const comment = await API.comments.getById(commentId);
        if (comment.status !== 'success') {
            showAlert('Error getting comment data', 'danger');
            return;
        }
        
        const result = await API.comments.update(commentId, {
            body: body,
            postId: comment.data.postId,
            userId: comment.data.userId
        });
        
        if (result.status === 'success') {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editCommentModal'));
            modal.hide();
            showAlert('Comment updated successfully!', 'success');
            
            // my reload approach since I had problems with the spapp again
            setTimeout(() => {
                if (typeof loadPostComments === 'function' && postId) {
                    loadPostComments(parseInt(postId));
                }
            }, 300);
        } else {
            showAlert(result.message || 'Error updating comment', 'danger');
        }
    } catch (error) {
        console.error('Error updating comment:', error);
        showAlert('Error updating comment', 'danger');
    }
}

window.deleteComment = function(commentId) {
    if (!Auth.isAuthenticated()) {
        showAlert('Please log in to delete comments', 'warning');
        return;
    }
    
    if (confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
        handleDeleteComment(commentId);
    }
};

async function handleDeleteComment(commentId) {
    try {
        const result = await API.comments.delete(commentId);
        
        if (result.status === 'success') {
            showAlert('Comment deleted successfully!', 'success');
            
            const hash = window.location.hash;
            const match = hash.match(/id=(\d+)/);
            if (match) {
                loadPostComments(parseInt(match[1]));
            }
        } else {
            showAlert(result.message || 'Error deleting comment', 'danger');
        }
    } catch (error) {
        console.error('Error deleting comment:', error);
        showAlert('Error deleting comment', 'danger');
    }
}

function canUserModify(itemUserId, userRole) {
    if (!Auth.isAuthenticated()) return false;
    const currentUserId = Auth.user.user_id || Auth.user.id;
    return itemUserId == currentUserId || userRole === 'admin' || Auth.user.role === 'admin';
}

function canUserModify(itemUserId, userRole) {
    if (!Auth.isAuthenticated()) return false;
    const currentUserId = Auth.user.user_id || Auth.user.id;
    return itemUserId == currentUserId || userRole === 'admin' || Auth.user.role === 'admin';
}

window.editPost = function(postId, currentTitle, currentBody) {
    if (!Auth.isAuthenticated()) {
        showAlert('Please log in to edit posts', 'warning');
        return;
    }
    
    let modal = document.getElementById('editPostModal');
    if (!modal) {
        const modalHTML = `
        <div class="modal fade" id="editPostModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Post</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editPostForm">
                        <div class="modal-body">
                            <input type="hidden" id="editPostId">
                            <div class="mb-3">
                                <label for="editPostTitle" class="form-label">Title</label>
                                <input type="text" class="form-control" id="editPostTitle" required>
                            </div>
                            <div class="mb-3">
                                <label for="editPostBody" class="form-label">Content</label>
                                <textarea class="form-control" id="editPostBody" rows="5" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        document.getElementById('editPostForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            await handleEditPost();
        });
    }
    
    document.getElementById('editPostId').value = postId;
    document.getElementById('editPostTitle').value = currentTitle;
    document.getElementById('editPostBody').value = currentBody;
    
    const modalInstance = new bootstrap.Modal(document.getElementById('editPostModal'));
    modalInstance.show();
};

async function handleEditPost() {
    const postId = document.getElementById('editPostId').value;
    const title = document.getElementById('editPostTitle').value;
    const body = document.getElementById('editPostBody').value;
    const userId = Auth.user.user_id || Auth.user.id;
    
    try {
        const result = await API.posts.update(postId, { title, body, userId });
        
        if (result.status === 'success') {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editPostModal'));
            modal.hide();
            showAlert('Post updated successfully!', 'success');
            
            // Store current location before refresh
            const currentHash = window.location.hash;
            
            if (currentHash.includes('dashboard')) {
                setTimeout(() => loadDashboardContent(), 300);
            } else if (currentHash.includes('community')) {
                setTimeout(() => loadCommunityContent(), 300);
            } else if (currentHash.includes('post?id=')) {
                // Force reload the post content by temporarily changing hash
                setTimeout(() => {
                    window.location.hash = '#post';
                    setTimeout(() => {
                        window.location.hash = `#post?id=${postId}`;
                    }, 100);
                }, 300);
            }
        } else {
            showAlert(result.message || 'Error updating post', 'danger');
        }
    } catch (error) {
        console.error('Error updating post:', error);
        showAlert('Error updating post', 'danger');
    }
}

window.deletePost = function(postId, postTitle) {
    if (!Auth.isAuthenticated()) {
        showAlert('Please log in to delete posts', 'warning');
        return;
    }
    
    if (confirm(`Are you sure you want to delete the post "${postTitle}"? This action cannot be undone.`)) {
        handleDeletePost(postId);
    }
};

async function handleDeletePost(postId) {
    try {
        const result = await API.posts.delete(postId);
        
        if (result.status === 'success') {
            showAlert('Post deleted successfully!', 'success');
            
            // Always redirect to dashboard after deletion
            setTimeout(() => {
                window.location.hash = '#dashboard';
                setTimeout(() => {
                    if (typeof loadDashboardContent === 'function') {
                        loadDashboardContent();
                    }
                }, 200);
            }, 500);
        } else {
            showAlert(result.message || 'Error deleting post', 'danger');
        }
    } catch (error) {
        console.error('Error deleting post:', error);
        showAlert('Error deleting post', 'danger');
    }
}

window.editComment = function(commentId, currentBody) {
    if (!Auth.isAuthenticated()) {
        showAlert('Please log in to edit comments', 'warning');
        return;
    }
    
    // Prevent event bubbling that might interfere with SPAPP
    event.preventDefault();
    event.stopPropagation();
    
    const hash = window.location.hash;
    const match = hash.match(/id=(\d+)/);
    const currentPostId = match ? parseInt(match[1]) : null;
    
    if (!currentPostId) {
        showAlert('Error: Unable to determine post ID', 'danger');
        return;
    }
    
    let modal = document.getElementById('editCommentModal');
    if (!modal) {
        const modalHTML = `
        <div class="modal fade" id="editCommentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editCommentForm">
                        <div class="modal-body">
                            <input type="hidden" id="editCommentId">
                            <input type="hidden" id="editCommentPostId">
                            <div class="mb-3">
                                <label for="editCommentBody" class="form-label">Comment</label>
                                <textarea class="form-control" id="editCommentBody" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Comment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        document.getElementById('editCommentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            await handleEditComment();
        });
    }
    
    document.getElementById('editCommentId').value = commentId;
    document.getElementById('editCommentPostId').value = currentPostId;
    document.getElementById('editCommentBody').value = currentBody;
    
    const modalInstance = new bootstrap.Modal(document.getElementById('editCommentModal'));
    modalInstance.show();
};

async function handleEditComment() {
    const commentId = document.getElementById('editCommentId').value;
    const postId = document.getElementById('editCommentPostId').value;
    const body = document.getElementById('editCommentBody').value;
    
    try {
        const comment = await API.comments.getById(commentId);
        if (comment.status !== 'success') {
            showAlert('Error getting comment data', 'danger');
            return;
        }
        
        const result = await API.comments.update(commentId, {
            body: body,
            postId: comment.data.postId,
            userId: comment.data.userId
        });
        
        if (result.status === 'success') {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editCommentModal'));
            modal.hide();
            showAlert('Comment updated successfully!', 'success');
            
            // Use the stored post ID instead of parsing from URL since I had issues with SPAPP :)
            if (postId) {
                setTimeout(() => {
                    loadPostComments(parseInt(postId));
                }, 300);
            }
        } else {
            showAlert(result.message || 'Error updating comment', 'danger');
        }
    } catch (error) {
        console.error('Error updating comment:', error);
        showAlert('Error updating comment', 'danger');
    }
}

window.deleteComment = function(commentId) {
    if (!Auth.isAuthenticated()) {
        showAlert('Please log in to delete comments', 'warning');
        return;
    }
    
    // Prevent event bubbling that might interfere with SPAPP
    event.preventDefault();
    event.stopPropagation();
    
    const hash = window.location.hash;
    const match = hash.match(/id=(\d+)/);
    const currentPostId = match ? parseInt(match[1]) : null;
    
    if (!currentPostId) {
        showAlert('Error: Unable to determine post ID', 'danger');
        return;
    }
    
    if (confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
        handleDeleteComment(commentId, currentPostId);
    }
};

async function handleDeleteComment(commentId, postId) {
    try {
        const result = await API.comments.delete(commentId);
        
        if (result.status === 'success') {
            showAlert('Comment deleted successfully!', 'success');
            
            // Use a more stable approach to reload comments
            setTimeout(() => {
                if (typeof loadPostComments === 'function' && postId) {
                    loadPostComments(parseInt(postId));
                }
            }, 300);
        } else {
            showAlert(result.message || 'Error deleting comment', 'danger');
        }
    } catch (error) {
        console.error('Error deleting comment:', error);
        showAlert('Error deleting comment', 'danger');
    }
}

// Make functions globally available
window.loadDashboardContent = loadDashboardContent;
window.loadCommunityContent = loadCommunityContent;

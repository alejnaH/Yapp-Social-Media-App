$(document).ready(function() {
    window.currentPostId = null;
    
    window.posts = {
        "1": {
            id: 1,
            author: "Jane Smith",
            date: "2 hours ago",
            title: "My Research on Renewable Energy",
            body: "Just published my research paper on renewable energy! So excited to share it with this community. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla ac justo vel nisl consequat efficitur. Phasellus vel sagittis nisl, eget aliquam leo. Phasellus vel sagittis nisl, eget aliquam leo.",
            image: "frontend/assets/images/beach.png",
            likes: 24,
            comments: [
                { user: "Alex Johnson", time: "1 hour ago", text: "Great research! I especially liked the section on solar energy." },
                { user: "Maria Garcia", time: "30 minutes ago", text: "Would love to collaborate on a follow-up study. DM me!" }
            ]
        },
        "2": {
            id: 2,
            author: "Alex Johnson",
            date: "Yesterday",
            title: "Tech Conference Highlights",
            body: "Has anyone been to the new tech conference? What were your thoughts on the AI demonstrations? I was blown away by the latest advancements in machine learning and neural networks. The possibilities seem endless!",
            image: null,
            likes: 18,
            comments: [
                { user: "John Doe", time: "22 hours ago", text: "The AI demos were amazing. I couldn't believe how advanced they've become." },
                { user: "Sarah Williams", time: "20 hours ago", text: "I was there too! The robotics section was also impressive." }
            ]
        },
        "3": {
            id: 3,
            author: "Maria Garcia",
            date: "3 days ago",
            title: "One Piece - What a Journey!",
            body: "I just finished watching One Piece. What an amazing journey! Anyone else a fan? The character development and world-building are simply incredible. I never thought I'd get so invested in a story about pirates!",
            image: "frontend/assets/images/onepiece.png",
            likes: 45,
            comments: [
                { user: "Robert Chen", time: "2 days ago", text: "One Piece is the best! Who's your favorite character?" },
                { user: "Emily Davis", time: "2 days ago", text: "I'm still catching up, but loving it so far!" }
            ]
        }
    };

    var app = $.spapp({
        defaultView: "#dashboard",
        templateDir: "frontend/views/"
    });
    
    app.route({
        view: "dashboard",
        onCreate: function() {
            console.log("Dashboard view created");
        },
        onReady: function() {
            console.log("Dashboard view ready");
        }
    });
    
    app.route({
        view: "login",
        onCreate: function() {
            console.log("Login view created");
        },
        onReady: function() {
            console.log("Login view ready");
            if (typeof mdb !== 'undefined') {
                if (mdb.Input) {
                    document.querySelectorAll('.form-outline').forEach((formOutline) => {
                        new mdb.Input(formOutline).init();
                    });
                }
            }
        }
    });
    
    app.route({
        view: "signup",
        onCreate: function() {
            console.log("Signup view created");
        },
        onReady: function() {
            console.log("Signup view ready");
            if (typeof mdb !== 'undefined') {
                if (mdb.Input) {
                    document.querySelectorAll('.form-outline').forEach((formOutline) => {
                        new mdb.Input(formOutline).init();
                    });
                }
            }
        }
    });
    
    app.route({
        view: "profile",
        onCreate: function() {
            console.log("Profile view created");
        },
        onReady: function() {
            console.log("Profile view ready");
        }
    });
    
    app.route({
        view: "community",
        onCreate: function() {
            console.log("Community view created");
        },
        onReady: function() {
            console.log("Community view ready");
        }
    });
    
    app.route({
        view: "post",
        onCreate: function() {
            console.log("Post view created");
        },
        onReady: function() {
            console.log("Post view ready");
            
            if (window.currentPostId && window.posts[window.currentPostId]) {
                loadPost(window.posts[window.currentPostId]);
            } else {
                showPostError();
            }
        }
    });
    
    app.run();
});

function viewPost(postId) {
    window.currentPostId = postId;

    window.location.hash = "post";
}

function loadPost(post) {
    $('#post-author').text(post.author);
    $('#post-date').text(post.date);
    $('#post-title').text(post.title);
    $('#post-body').text(post.body);
    $('#post-likes').text(`Like (${post.likes})`);
    $('#post-comments-count').text(`Comments (${post.comments.length})`);
    
    const imageContainer = $('#post-image-container');
    if (post.image) {
        imageContainer.html(`<img src="${post.image}" class="img-fluid rounded" alt="Post image">`);
    } else {
        imageContainer.empty();
    }
    
    const commentsContainer = $('#comments-container');
    commentsContainer.empty();
    
    if (post.comments && post.comments.length > 0) {
        post.comments.forEach(comment => {
            commentsContainer.append(`
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="d-flex mb-2">
                            <img src="frontend/assets/images/profile-icon.png" class="rounded-circle me-2" alt="Avatar" style="width: 40px; height: 40px;">
                            <div>
                                <h6 class="mb-0">${comment.user}</h6>
                                <small class="text-muted">${comment.time}</small>
                            </div>
                        </div>
                        <p class="mb-0">${comment.text}</p>
                    </div>
                </div>
            `);
        });
    } else {
        commentsContainer.append('<p class="text-muted">No comments yet. Be the first to comment!</p>');
    }
}

function showPostError() {
    $('#post-content').html(`
        <div class="card-body text-center">
            <h4 class="text-danger">Post Not Found</h4>
            <p>Sorry, the post you're looking for doesn't exist or has been removed.</p>
            <a href="#community" class="btn btn-primary">Return to Community</a>
        </div>
    `);
    $('#comments-container').empty();
}

function toggleLike(button) {
    if ($(button).hasClass('active')) {
        $(button).removeClass('active');
    } else {
        $(button).addClass('active');
    }
}

function enlargeImage() {
    alert("Image clicked!");
}

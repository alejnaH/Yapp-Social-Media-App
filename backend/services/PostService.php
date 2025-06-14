<?php
require_once 'BaseService.php';
require_once 'dao/PostDao.php';

class PostService extends BaseService {
    
    public function __construct() {
        parent::__construct(new PostDao());
    }
    
    protected function validate($data, $id = null) {
        if (empty($data['title'])) {
            throw new Exception("Title is required");
        }
        
        if (strlen($data['title']) > 255) {
            throw new Exception("Title cannot exceed 255 characters");
        }
        
        if (empty($data['body'])) {
            throw new Exception("Post body is required");
        }
        
        if (empty($data['userId'])) {
            throw new Exception("User ID is required");
        }
        
        if (!is_numeric($data['userId']) || $data['userId'] <= 0) {
            throw new Exception("Invalid user ID");
        }
    }
    
    public function canModify($postId, $currentUserId, $userRole) {
        $post = $this->getById($postId);
        if (!$post) {
            throw new Exception("Post not found");
        }
        return $post['userId'] == $currentUserId || $userRole === 'admin';
    }
    
    public function updateWithAuth($id, $data, $currentUserId, $userRole) {
        if (!$this->canModify($id, $currentUserId, $userRole)) {
            throw new Exception("You don't have permission to modify this post");
        }
        return $this->update($id, $data);
    }
    
    public function deleteWithAuth($id, $currentUserId, $userRole) {
        if (!$this->canModify($id, $currentUserId, $userRole)) {
            throw new Exception("You don't have permission to delete this post");
        }
        return $this->delete($id);
    }
    
    public function getByUserId($userId) {
        if (!is_numeric($userId) || $userId <= 0) {
            throw new Exception("Invalid user ID");
        }
        return $this->dao->getByUserId($userId);
    }
    
    public function getPostsWithUserInfo($currentUserId = null) {
        if ($currentUserId && (!is_numeric($currentUserId) || $currentUserId <= 0)) {
            throw new Exception("Invalid user ID");
        }
        return $this->dao->getPostsWithUserInfo($currentUserId);
    }
    
    public function getPostWithDetails($postId) {
        if (!is_numeric($postId) || $postId <= 0) {
            throw new Exception("Invalid post ID");
        }
        
        $post = $this->dao->getPostWithDetails($postId);
        if (!$post) {
            throw new Exception("Post not found");
        }
        return $post;
    }
    
    public function getPostsByCommunity($communityId) {
        if (!is_numeric($communityId) || $communityId <= 0) {
            throw new Exception("Invalid community ID");
        }
        return $this->dao->getPostsByCommunity($communityId);
    }
}
?>

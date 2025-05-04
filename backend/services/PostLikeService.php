<?php
require_once 'BaseService.php';
require_once 'dao/PostLikeDao.php';

class PostLikeService extends BaseService {
    public function __construct() {
        parent::__construct(new PostLikeDao());
    }
    
    protected function validate($data, $id = null) {
        if (empty($data['postId'])) {
            throw new Exception("Post ID is required");
        }
        if (!is_numeric($data['postId']) || $data['postId'] <= 0) {
            throw new Exception("Invalid post ID");
        }

        if (empty($data['userId'])) {
            throw new Exception("User ID is required");
        }
        if (!is_numeric($data['userId']) || $data['userId'] <= 0) {
            throw new Exception("Invalid user ID");
        }

        if (!$id && $this->dao->hasUserLikedPost($data['userId'], $data['postId'])) {
            throw new Exception("User has already liked this post");
        }
    }
    
    public function getByPostId($postId) {
        if (!is_numeric($postId) || $postId <= 0) {
            throw new Exception("Invalid post ID");
        }
        
        return $this->dao->getByPostId($postId);
    }
    
    public function getByUserId($userId) {
        if (!is_numeric($userId) || $userId <= 0) {
            throw new Exception("Invalid user ID");
        }
        
        return $this->dao->getByUserId($userId);
    }
    
    public function getLikeCount($postId) {
        if (!is_numeric($postId) || $postId <= 0) {
            throw new Exception("Invalid post ID");
        }
        
        return $this->dao->getLikeCount($postId);
    }
    
    public function hasUserLikedPost($userId, $postId) {
        if (!is_numeric($userId) || $userId <= 0) {
            throw new Exception("Invalid user ID");
        }
        if (!is_numeric($postId) || $postId <= 0) {
            throw new Exception("Invalid post ID");
        }
        
        return $this->dao->hasUserLikedPost($userId, $postId);
    }
    
    public function removeLike($userId, $postId) {
        if (!is_numeric($userId) || $userId <= 0) {
            throw new Exception("Invalid user ID");
        }
        if (!is_numeric($postId) || $postId <= 0) {
            throw new Exception("Invalid post ID");
        }
        
        return $this->dao->removeLike($userId, $postId);
    }
}
?>

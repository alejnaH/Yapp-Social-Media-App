<?php
require_once 'BaseService.php';
require_once 'dao/CommentDao.php';

class CommentService extends BaseService {
    public function __construct() {
        parent::__construct(new CommentDao());
    }
    
    protected function validate($data, $id = null) {
        if (empty($data['body'])) {
            throw new Exception("Comment body is required");
        }
        if (strlen($data['body']) > 1000) {
            throw new Exception("Comment cannot exceed 1000 characters");
        }

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
    }

    public function canModify($commentId, $currentUserId, $userRole) {
        $comment = $this->getById($commentId);
        if (!$comment) {
            throw new Exception("Comment not found");
        }
        return $comment['userId'] == $currentUserId || $userRole === 'admin';
    }

    public function updateWithAuth($id, $data, $currentUserId, $userRole) {
        if (!$this->canModify($id, $currentUserId, $userRole)) {
            throw new Exception("You don't have permission to modify this comment");
        }
        return $this->update($id, $data);
    }

    public function deleteWithAuth($id, $currentUserId, $userRole) {
        if (!$this->canModify($id, $currentUserId, $userRole)) {
            throw new Exception("You don't have permission to delete this comment");
        }
        return $this->delete($id);
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
    
    public function getCommentsWithUserInfo($postId) {
        if (!is_numeric($postId) || $postId <= 0) {
            throw new Exception("Invalid post ID");
        }
        
        return $this->dao->getCommentsWithUserInfo($postId);
    }
}
?>

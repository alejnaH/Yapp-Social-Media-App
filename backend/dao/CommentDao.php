<?php
require_once 'BaseDao.php';

class CommentDao extends BaseDao {
    public function __construct() {
        parent::__construct("Comment");
    }

    public function getByPostId($postId) {
        $stmt = $this->connection->prepare("SELECT * FROM Comment WHERE postId = :postId");
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByUserId($userId) {
        $stmt = $this->connection->prepare("SELECT * FROM Comment WHERE userId = :userId");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCommentsWithUserInfo($postId) {
        $sql = "SELECT c.*, u.username, u.name as user_name 
                FROM Comment c 
                JOIN User u ON c.userId = u.id 
                WHERE c.postId = :postId 
                ORDER BY c.id ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>

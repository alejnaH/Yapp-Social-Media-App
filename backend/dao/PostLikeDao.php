<?php
require_once 'BaseDao.php';

class PostLikeDao extends BaseDao {
    public function __construct() {
        parent::__construct("PostLike");
    }

    public function getByPostId($postId) {
        $stmt = $this->connection->prepare("SELECT * FROM PostLike WHERE postId = :postId");
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByUserId($userId) {
        $stmt = $this->connection->prepare("SELECT * FROM PostLike WHERE userId = :userId");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getLikeCount($postId) {
        $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM PostLike WHERE postId = :postId");
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function hasUserLikedPost($userId, $postId) {
        $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM PostLike 
                                         WHERE userId = :userId AND postId = :postId");
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    public function removeLike($userId, $postId) {
        $stmt = $this->connection->prepare("DELETE FROM PostLike 
                                         WHERE userId = :userId AND postId = :postId");
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':postId', $postId);
        return $stmt->execute();
    }
}
?>

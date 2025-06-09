<?php
require_once 'BaseDao.php';

class PostDao extends BaseDao {
    
    public function __construct() {
        parent::__construct("Post");
    }
    
    public function getByUserId($userId) {
        $stmt = $this->connection->prepare("SELECT * FROM Post WHERE userId = :userId");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getPostsWithUserInfo($currentUserId = null) {
        if ($currentUserId) {
            $sql = "SELECT p.*, u.username, u.name as user_name,
                    COUNT(DISTINCT pl.id) as like_count,
                    MAX(CASE WHEN pl.userId = :currentUserId THEN 1 ELSE 0 END) as user_liked
                    FROM Post p
                    JOIN User u ON p.userId = u.id
                    LEFT JOIN PostLike pl ON p.id = pl.postId
                    GROUP BY p.id, u.id
                    ORDER BY p.id DESC";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':currentUserId', $currentUserId);
        } else {
            $sql = "SELECT p.*, u.username, u.name as user_name,
                    COUNT(DISTINCT pl.id) as like_count,
                    0 as user_liked
                    FROM Post p
                    JOIN User u ON p.userId = u.id
                    LEFT JOIN PostLike pl ON p.id = pl.postId
                    GROUP BY p.id, u.id
                    ORDER BY p.id DESC";
            $stmt = $this->connection->prepare($sql);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getPostWithDetails($postId) {
        $sql = "SELECT p.*, u.username, u.name as user_name, 
                       COUNT(DISTINCT c.id) as comment_count, 
                       COUNT(DISTINCT pl.id) as like_count 
                FROM Post p 
                JOIN User u ON p.userId = u.id 
                LEFT JOIN Comment c ON p.id = c.postId 
                LEFT JOIN PostLike pl ON p.id = pl.postId 
                WHERE p.id = :postId 
                GROUP BY p.id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function getPostsByCommunity($communityId) {
        $sql = "SELECT p.*, u.username, u.name as user_name 
                FROM Post p 
                JOIN User u ON p.userId = u.id 
                JOIN CommunityPost cp ON p.id = cp.postId 
                WHERE cp.communityId = :communityId 
                ORDER BY p.id DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':communityId', $communityId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>

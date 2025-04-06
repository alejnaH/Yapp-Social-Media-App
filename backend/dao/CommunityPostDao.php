<?php
require_once 'BaseDao.php';

class CommunityPostDao extends BaseDao {
    public function __construct() {
        parent::__construct("CommunityPost");
    }

    public function getByCommunityId($communityId) {
        $stmt = $this->connection->prepare("SELECT * FROM CommunityPost WHERE communityId = :communityId");
        $stmt->bindParam(':communityId', $communityId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByPostId($postId) {
        $stmt = $this->connection->prepare("SELECT * FROM CommunityPost WHERE postId = :postId");
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCommunitiesForPost($postId) {
        $sql = "SELECT c.* 
                FROM Community c 
                JOIN CommunityPost cp ON c.id = cp.communityId 
                WHERE cp.postId = :postId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function isPostInCommunity($postId, $communityId) {
        $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM CommunityPost 
                                         WHERE postId = :postId AND communityId = :communityId");
        $stmt->bindParam(':postId', $postId);
        $stmt->bindParam(':communityId', $communityId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}
?>

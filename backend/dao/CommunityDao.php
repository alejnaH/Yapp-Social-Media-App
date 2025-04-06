<?php
require_once 'BaseDao.php';

class CommunityDao extends BaseDao {
    public function __construct() {
        parent::__construct("Community");
    }

    public function getByName($name) {
        $stmt = $this->connection->prepare("SELECT * FROM Community WHERE name = :name");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getCommunitiesWithPostCount() {
        $sql = "SELECT c.*, COUNT(cp.id) as post_count 
                FROM Community c 
                LEFT JOIN CommunityPost cp ON c.id = cp.communityId 
                GROUP BY c.id 
                ORDER BY post_count DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>

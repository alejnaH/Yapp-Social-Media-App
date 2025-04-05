<?php
require_once 'BaseDao.php';

class UserDao extends BaseDao {
    public function __construct() {
        parent::__construct("User");
    }

    public function getByUsername($username) {
        $stmt = $this->connection->prepare("SELECT * FROM User WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getUserWithCountry($id) {
        $sql = "SELECT u.*, c.name as country_name 
                FROM User u 
                LEFT JOIN Country c ON u.countryId = c.id 
                WHERE u.id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getAllUsersWithCountry() {
        $sql = "SELECT u.*, c.name as country_name 
                FROM User u 
                LEFT JOIN Country c ON u.countryId = c.id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getUsersByCountry($countryId) {
        $stmt = $this->connection->prepare("SELECT * FROM User WHERE countryId = :countryId");
        $stmt->bindParam(':countryId', $countryId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>

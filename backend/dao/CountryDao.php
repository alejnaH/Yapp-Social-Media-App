<?php
require_once 'BaseDao.php';

class CountryDao extends BaseDao {
    public function __construct() {
        parent::__construct("Country");
    }

    public function getByName($name) {
        $stmt = $this->connection->prepare("SELECT * FROM Country WHERE name = :name");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getCountriesWithUserCount() {
        $sql = "SELECT c.*, COUNT(u.id) as user_count 
                FROM Country c 
                LEFT JOIN User u ON c.id = u.countryId 
                GROUP BY c.id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>

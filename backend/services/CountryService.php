<?php
require_once 'BaseService.php';
require_once 'dao/CountryDao.php';

class CountryService extends BaseService {
    public function __construct() {
        parent::__construct(new CountryDao());
    }
    
    protected function validate($data, $id = null) {
        if (empty($data['name'])) {
            throw new Exception("Country name is required");
        }
        if (strlen($data['name']) < 2 || strlen($data['name']) > 100) {
            throw new Exception("Country name must be between 2 and 100 characters");
        }

        $existingCountry = $this->dao->getByName($data['name']);
        if ($existingCountry && (!$id || $existingCountry['id'] != $id)) {
            throw new Exception("Country name already exists");
        }
    }
    
    public function getByName($name) {
        if (empty($name)) {
            throw new Exception("Country name is required");
        }
        
        return $this->dao->getByName($name);
    }
    
    public function getCountriesWithUserCount() {
        return $this->dao->getCountriesWithUserCount();
    }
}
?>

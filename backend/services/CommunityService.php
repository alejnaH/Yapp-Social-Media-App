<?php
require_once 'BaseService.php';
require_once 'dao/CommunityDao.php';

class CommunityService extends BaseService {
    public function __construct() {
        parent::__construct(new CommunityDao());
    }
    
    protected function validate($data, $id = null) {
        if (empty($data['name'])) {
            throw new Exception("Community name is required");
        }
        if (strlen($data['name']) < 2 || strlen($data['name']) > 100) {
            throw new Exception("Community name must be between 2 and 100 characters");
        }

        $existingCommunity = $this->dao->getByName($data['name']);
        if ($existingCommunity && (!$id || $existingCommunity['id'] != $id)) {
            throw new Exception("Community name already exists");
        }
    }
    
    public function getByName($name) {
        if (empty($name)) {
            throw new Exception("Community name is required");
        }
        
        return $this->dao->getByName($name);
    }
    
    public function getCommunitiesWithPostCount() {
        return $this->dao->getCommunitiesWithPostCount();
    }
}
?>

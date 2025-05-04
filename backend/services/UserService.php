<?php
require_once 'BaseService.php';
require_once 'dao/UserDao.php';

class UserService extends BaseService {
    public function __construct() {
        parent::__construct(new UserDao());
    }
    
    protected function validate($data, $id = null) {
        if (empty($data['name'])) {
            throw new Exception("Name is required");
        }
        if (strlen($data['name']) < 2 || strlen($data['name']) > 100) {
            throw new Exception("Name must be between 2 and 100 characters");
        }

        if (empty($data['username'])) {
            throw new Exception("Username is required");
        }
        if (strlen($data['username']) < 3 || strlen($data['username']) > 100) {
            throw new Exception("Username must be between 3 and 100 characters");
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            throw new Exception("Username can only contain letters, numbers and underscores");
        }

        $existingUser = $this->dao->getByUsername($data['username']);
        if ($existingUser && (!$id || $existingUser['id'] != $id)) {
            throw new Exception("Username already exists");
        }

        if (!$id || !empty($data['password'])) {
            if (empty($data['password'])) {
                throw new Exception("Password is required");
            }
            if (strlen($data['password']) < 6) {
                throw new Exception("Password must be at least 6 characters");
            }
        }

        if (!empty($data['countryId']) && !is_numeric($data['countryId'])) {
            throw new Exception("Invalid country ID");
        }
    }
    
    public function create($data) {
        $this->validate($data);

        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $this->dao->insert($data);
    }
    
    public function update($id, $data) {
        $this->validate($data, $id);

        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        
        return $this->dao->update($id, $data);
    }
    
    public function authenticate($username, $password) {
        $user = $this->dao->getByUsername($username);
        
        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid username or password");
        }

        unset($user['password']);
        
        return $user;
    }
    
    public function getUserWithCountry($id) {
        return $this->dao->getUserWithCountry($id);
    }
    
    public function getAllUsersWithCountry() {
        return $this->dao->getAllUsersWithCountry();
    }
    
    public function getUsersByCountry($countryId) {
        if (!is_numeric($countryId) || $countryId <= 0) {
            throw new Exception("Invalid country ID");
        }
        
        return $this->dao->getUsersByCountry($countryId);
    }
}
?>

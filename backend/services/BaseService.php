<?php
require_once __DIR__ . '/../config.php';

class BaseService {
    protected $dao;
    protected $validator;
    
    public function __construct($dao) {
        $this->dao = $dao;
    }
    
    public function getAll() {
        return $this->dao->getAll();
    }
    
    public function getById($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID");
        }
        
        $result = $this->dao->getById($id);
        if (!$result) {
            throw new Exception("Record not found");
        }
        
        return $result;
    }
    
    public function create($data) {
        $this->validate($data);
        return $this->dao->insert($data);
    }
    
    public function update($id, $data) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID");
        }
        
        $this->validate($data, $id);
        return $this->dao->update($id, $data);
    }
    
    public function delete($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID");
        }
        
        return $this->dao->delete($id);
    }
    
    protected function validate($data, $id = null) {
        // Override in child classes
    }
}
?>

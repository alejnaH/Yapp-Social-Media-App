<?php
require_once 'BaseService.php';
require_once 'dao/CommunityPostDao.php';

class CommunityPostService extends BaseService {
    public function __construct() {
        parent::__construct(new CommunityPostDao());
    }
    
    protected function validate($data, $id = null) {
        if (empty($data['postId'])) {
            throw new Exception("Post ID is required");
        }
        if (!is_numeric($data['postId']) || $data['postId'] <= 0) {
            throw new Exception("Invalid post ID");
        }

        if (empty($data['communityId'])) {
            throw new Exception("Community ID is required");
        }
        if (!is_numeric($data['communityId']) || $data['communityId'] <= 0) {
            throw new Exception("Invalid community ID");
        }

        if (!$id && $this->dao->isPostInCommunity($data['postId'], $data['communityId'])) {
            throw new Exception("Post is already in this community");
        }
    }
    
    public function getByCommunityId($communityId) {
        if (!is_numeric($communityId) || $communityId <= 0) {
            throw new Exception("Invalid community ID");
        }
        
        return $this->dao->getByCommunityId($communityId);
    }
    
    public function getByPostId($postId) {
        if (!is_numeric($postId) || $postId <= 0) {
            throw new Exception("Invalid post ID");
        }
        
        return $this->dao->getByPostId($postId);
    }
    
    public function getCommunitiesForPost($postId) {
        if (!is_numeric($postId) || $postId <= 0) {
            throw new Exception("Invalid post ID");
        }
        
        return $this->dao->getCommunitiesForPost($postId);
    }
    
    public function isPostInCommunity($postId, $communityId) {
        if (!is_numeric($postId) || $postId <= 0) {
            throw new Exception("Invalid post ID");
        }
        if (!is_numeric($communityId) || $communityId <= 0) {
            throw new Exception("Invalid community ID");
        }
        
        return $this->dao->isPostInCommunity($postId, $communityId);
    }
}
?>

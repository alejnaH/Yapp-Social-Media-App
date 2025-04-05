<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'dao/UserDao.php';
require_once 'dao/CountryDao.php';
require_once 'dao/PostDao.php';
require_once 'dao/CommentDao.php';
require_once 'dao/CommunityDao.php';
require_once 'dao/CommunityPostDao.php';
require_once 'dao/PostLikeDao.php';

$userDao = new UserDao();
$countryDao = new CountryDao();
$postDao = new PostDao();
$commentDao = new CommentDao();
$communityDao = new CommunityDao();
$communityPostDao = new CommunityPostDao();
$postLikeDao = new PostLikeDao();

echo "<h2>Testing Country DAO</h2>";
$countryDao->insert([
    'name' => 'United States'
]);
$countryDao->insert([
    'name' => 'Canada'
]);

$countries = $countryDao->getAll();
echo "<pre>Countries: ";
print_r($countries);
echo "</pre>";

echo "<h2>Testing User DAO</h2>";
$userDao->insert([
    'name' => 'John Doe',
    'username' => 'johndoe',
    'password' => password_hash('password123', PASSWORD_DEFAULT),
    'countryId' => 1
]);

$userDao->insert([
    'name' => 'Jane Smith',
    'username' => 'janesmith',
    'password' => password_hash('password456', PASSWORD_DEFAULT),
    'countryId' => 2
]);

$users = $userDao->getAll();
echo "<pre>Users: ";
print_r($users);
echo "</pre>";

$usersWithCountry = $userDao->getAllUsersWithCountry();
echo "<pre>Users with Country: ";
print_r($usersWithCountry);
echo "</pre>";

echo "<h2>Testing Post DAO</h2>";
$postDao->insert([
    'title' => 'My First Post',
    'body' => 'This is the content of my first post.',
    'userId' => 1
]);

$postDao->insert([
    'title' => 'Second Post',
    'body' => 'Content for the second post.',
    'userId' => 2
]);

$posts = $postDao->getAll();
echo "<pre>Posts: ";
print_r($posts);
echo "</pre>";

$postsWithUserInfo = $postDao->getPostsWithUserInfo();
echo "<pre>Posts with User Info: ";
print_r($postsWithUserInfo);
echo "</pre>";

echo "<h2>Testing Community DAO</h2>";
$communityDao->insert([
    'name' => 'Technology'
]);
$communityDao->insert([
    'name' => 'Travel'
]);

$communities = $communityDao->getAll();
echo "<pre>Communities: ";
print_r($communities);
echo "</pre>";

echo "<h2>Testing Community Post DAO</h2>";
$communityPostDao->insert([
    'postId' => 1,
    'communityId' => 1
]);
$communityPostDao->insert([
    'postId' => 2,
    'communityId' => 2
]);

$communityPosts = $communityPostDao->getAll();
echo "<pre>Community Posts: ";
print_r($communityPosts);
echo "</pre>";

echo "<h2>Testing Comment DAO</h2>";
$commentDao->insert([
    'body' => 'Great post!',
    'postId' => 1,
    'userId' => 2
]);
$commentDao->insert([
    'body' => 'I agree with this.',
    'postId' => 1,
    'userId' => 1
]);

$comments = $commentDao->getByPostId(1);
echo "<pre>Comments for Post 1: ";
print_r($comments);
echo "</pre>";

echo "<h2>Testing Post Like DAO</h2>";
$postLikeDao->insert([
    'postId' => 1,
    'userId' => 2
]);
$postLikeDao->insert([
    'postId' => 2,
    'userId' => 1
]);

$postLikes = $postLikeDao->getByPostId(1);
echo "<pre>Likes for Post 1: ";
print_r($postLikes);
echo "</pre>";

$likeCount = $postLikeDao->getLikeCount(1);
echo "<p>Like count for Post 1: $likeCount</p>";

echo "<h2>DAO Testing Complete</h2>";
?>
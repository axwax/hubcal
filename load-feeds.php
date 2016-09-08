<?
require 'class.iCalReader.php';
require '../includes/db-functions.php';
db_auth();

$categories = db_select('categories', array('*'));
$categories = $categories['result'];

$theCategories = array();
foreach($categories as $category){
  $theCategories[$category['id']] = $category;  
}


$category = $_POST['category'];
$category = 1;
if($category){
  $where = array('category' => $category);
}
else {
  $where = array();
}

$feeds = db_select('feeds', array('*'), $where);
$feeds = $feeds['result'];

$theFeeds = array();
foreach($feeds as $feed){
  $theFeeds[$feed['id']] = $feed;  
}

echo json_encode(array('categories' => $theCategories, 'feeds' => $theFeeds));
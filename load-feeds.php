<?
require 'class.iCalReader.php';
require '../includes/db-functions.php';
db_auth();
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

echo json_encode($theFeeds);
<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once('includes/hubconfig.php');
require_once('includes/db-functions.php');
db_auth($db,$user,$pw);

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
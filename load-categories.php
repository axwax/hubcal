<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once('includes/hubconfig.php');
require_once('includes/db-functions.php');
db_auth($db,$user,$pw);

// get all categories and sort them by category id
$categories = db_select('categories', array('*'));
$categories = $categories['result'];

$theCategories = array();
foreach($categories as $category){
  $theCategories[$category['id']] = $category;  
}

echo json_encode(array('categories' => $theCategories, 'feeds' => array()));
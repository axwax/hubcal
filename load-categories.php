<?php

/*
 * returns all calendar categories in JSON format
**/

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
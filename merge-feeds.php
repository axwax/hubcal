<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once('iCalcreator/iCalcreator.php');
require_once('includes/hubconfig.php');
require_once('includes/db-functions.php');
db_auth($db,$user,$pw);

// sanitize categories
$requestedCategories = $_GET['categories'];
$requestedCategoriesArray = explode(',',$requestedCategories);
foreach($requestedCategoriesArray as $categoryID){
  if(!is_numeric($categoryID)){
    die;
  }
}
$requestedCategories=implode(',',$requestedCategoriesArray);

// get names for categories and construct name for our calendar
$where ="`id` IN (" . $requestedCategories . ")";
$categories = db_select('categories', array('*'), $where);
$categories = $categories['result'];

$catnamesArray = array();
foreach($categories as $category){
  $catnamesArray[] = $category['name'];
}
$catnames = implode(', ',$catnamesArray);
$calendarName = "Hubcal" . ($catnames ? ": $catnames" : "");

// get feeds for selected categories
$where ="`category` IN (" . $requestedCategories . ")";
$feeds = db_select('feeds', array('*'), $where);
$feeds = $feeds['result'];
$requestedFeedsArray = array();
foreach($feeds as $feed){
  $requestedFeedsArray[] = $feed['id'];
}
$requestedFeeds=implode(',',$requestedFeedsArray);

$path = $root_path . 'export/';

$file = hash('md5',$requestedFeeds).'.ics';

if(file_exists($path.$file) && (time() - filemtime($path.$file) <3600)){
  header('Content-type: text/calendar; charset=utf-8');
  header('Content-Disposition: attachment; filename=' . $file);
  readfile($path.$file);
  die;
}
$where = "`id` IN (" . $requestedFeeds . ")"; // TODO: Sanitise!


$feeds = db_select('feeds', array('*'), $where);
$feeds = $feeds['result'];

// set up calendar
// the variables are stored in hubconfig
$config = array("unique_id" => $ical_id,
     "directory" => "import",
     "TZID"      => $timezone
);
$vcalendar = new vcalendar($config);


// loop through feeds and merge
foreach($feeds as $feed){
  $vcalendar->setConfig("url" , $feed['url']);
  $vcalendar->parse();
}

$calendarDescription = "HubCal - stay updated on upcoming ethical, environmental and sustainable activities in Cambridge!";
$vcalendar->setProperty( "X-WR-CALNAME", $calendarName);
$vcalendar->setProperty( "X-WR-CALDESC", $calendarDescription);
$vcalendar->setConfig("directory", "export");
$vcalendar->setConfig("filename", $file);
$vcalendar->saveCalendar();
  header('Content-type: text/calendar; charset=utf-8');
  header('Content-Disposition: attachment; filename=' . $file);
  readfile($path.$file);
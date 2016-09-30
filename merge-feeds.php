<?php

/**
 * this script merges the iCal feeds for the requested categories
**/

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

// set up path for caching the exported iCal feed
$path = $root_path . 'export/';

// generate the filename - this will only be different if a new combination of categories is requested
$file = hash('md5',$requestedFeeds).'.ics';

// use the cached version or generate a fresh copy?
if(file_exists($path.$file) && (time() - filemtime($path.$file) <3600)){
  header('Content-type: text/calendar; charset=utf-8');
  header('Content-Disposition: attachment; filename=' . $file);
  readfile($path.$file);
  die;
}

// fetch the requested feeds from the database 
$where = "`id` IN (" . $requestedFeeds . ")";
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

// configure the new iCal feed
$calendarDescription = "HubCal - stay updated on upcoming ethical, environmental and sustainable activities in Cambridge!";
$vcalendar->setProperty( "X-WR-CALNAME", $calendarName);
$vcalendar->setProperty( "X-WR-CALDESC", $calendarDescription);
$vcalendar->setConfig("directory", "export");
$vcalendar->setConfig("filename", $file);

// save and output the feed
$vcalendar->saveCalendar();
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $file);
readfile($path.$file);
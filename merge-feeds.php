<?php

require_once('iCalcreator/iCalcreator.php');
require '../includes/db-functions.php';
db_auth();

$requestedFeeds = $_GET['feeds'];
$requestedFeedsArray = explode(',',$requestedFeeds);
foreach($requestedFeedsArray as $feedID){
  if(!is_numeric($feedID)){
    die;
  }
}

$requestedFeeds=implode(',',$requestedFeedsArray);

$path = '/var/www/vhosts/green-calendar.gigx.co.uk/export/';

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

$config = array("unique_id" => "green-calendar.gigx.co.uk",
     "directory" => "import",
);
$vcalendar = new vcalendar($config);


// loop through feeds and merge
foreach($feeds as $feed){
  $vcalendar->setConfig("url" , $feed['url']);
  $vcalendar->parse();
}
//$vcalendar->returnCalendar();
$vcalendar->setConfig("directory", "export");
$vcalendar->setConfig("filename", $file);
$vcalendar->saveCalendar();
  header('Content-type: text/calendar; charset=utf-8');
  header('Content-Disposition: attachment; filename=' . $file);
  readfile($path.$file);
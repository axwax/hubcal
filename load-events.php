<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once('class.iCalReader.php');
require_once('includes/hubconfig.php');
require_once('includes/db-functions.php');
db_auth($db,$user,$pw);

// TODO: Sanitise!!!!
$requestedCategories = $_POST['categories'];
$requestedFeeds = $_POST['feeds'];
$start = date("Y-m-d", strtotime($_POST['start'] . " -1 month"));
$end = date("Y-m-d", strtotime($_POST['end'] . " +1 month"));

// only display selected categories if requested
$where = '';
if($requestedCategories){
  $requestedCategoryIDs = array_keys($requestedCategories);
  $where ="`category` IN (" . implode(',', $requestedCategoryIDs) . ")";
}
else{
  die('[]');
}

// get all feeds and sort them by feed id
$feeds = db_select('feeds', array('*'), $where);
$feeds = $feeds['result'];
$theFeeds = array();

foreach($feeds as $feed){
  $theFeeds[$feed['id']] = $feed;  
}
$feedIDs = array_keys($theFeeds);

// get all events for this month and the previous month and create an eventObject for each
$where = "`start` > '$start' AND `end` < '$end'";
$where .= " AND `feedID` IN (" . implode(',', $feedIDs) . ")";
$events = db_select('events', array('*'), $where, array('start' => 'ASC'));
$events = $events['result'];

foreach ($events as $event) {
  $eventObj = new stdClass();
  $eventObj->id = $event['id'];
  $eventObj->start = $event['start'];
  $eventObj->end = $event['end'];
  if(strtotime($event['end']) - strtotime($event['start'])>86399){
    $eventObj->allDay = true;
  }
  $eventObj->title = $event['title'];
  $eventObj->body = $event['body'];
  $eventObj->url = $event['url'];
  $eventObj->location = $event['location'];
  $eventObj->attachment = $event['attachment'];
  $eventObj->organizerName = $event['organizerName'];
  $eventObj->organizerEmail = $event['organizerEmail'];
  $eventObj->color = $requestedCategories[$theFeeds[$event['feedID']]['category']];
  $eventObj->eventSource = $theFeeds[$event['feedID']]['name'];
  $eventObj->eventSourceURL = $theFeeds[$event['feedID']]['source_url'];
  $eventObj->eventFeedURL = $theFeeds[$event['feedID']]['url'];  
  $outArr[] = $eventObj;    
}

// output the array in JSON format
echo json_encode($outArr);die;
<?php

require 'class.iCalReader.php';
require '../includes/db-functions.php';
db_auth();

// TODO: Sanitise!!!!
$requestedFeeds = $_POST['feeds'];
$start = date("Y-m-d", strtotime($_POST['start'] . " -1 month"));
$end = date("Y-m-d", strtotime($_POST['end'] . " +1 month"));

$category=1;
$where = '';
if($requestedFeeds){
  $requestedFeedIDs = array_keys($requestedFeeds);
  $where = "`id` IN (" . implode(',', $requestedFeedIDs) . ")"; // TODO: Sanitise!
}
else{
  die('[]');
}
if($category){
  $where .=" AND `category` = '$category'";
}


$feeds = db_select('feeds', array('*'), $where);
$feeds = $feeds['result'];

$theFeeds = array();
foreach($feeds as $feed){
  $theFeeds[$feed['id']] = $feed;  
}
$feedIDs = array_keys($theFeeds);

if($requestedFeeds){
  $feedIDs = array_intersect($feedIDs, $requestedFeedIDs);
}

$where = '';
if($start && $end){
  $where = "`start` > '$start' AND `end` < '$end'";
}
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
  $eventObj->url = $event['URL'];
  $eventObj->location = $event['location'];
  $eventObj->attachment = $event['attachment'];
  $eventObj->organizerName = $event['organizerName'];
  $eventObj->organizerEmail = $event['organizerEmail'];
  
  $eventObj->color = $requestedFeeds[$event['feedID']];
  $eventObj->eventSource = $theFeeds[$event['feedID']]['name'];
  $eventObj->eventSourceURL = $theFeeds[$event['feedID']]['url'];
  
  $outArr[] = $eventObj;    
}

echo json_encode($outArr);die;


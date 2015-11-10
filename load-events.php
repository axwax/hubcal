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
  //print_r(implode(',',array_keys($requestedFeeds)));
  $requestedFeedIDs = array_keys($requestedFeeds);
  //$feedIDs = implode(',',$requestedFeedIDs);
  $where = "`id` IN (" . implode(',', $requestedFeedIDs) . ")"; // TODO: Sanitise!
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
//print_r($requestedFeedIDs);
//print_r($feedIDs);
//die;

$where = '';
if($start && $end){
  $where = "`start` > '$start' AND `end` < '$end'";
}
//if($requestedFeeds){
  $where .= " AND `feedID` IN (" . implode(',', $feedIDs) . ")";
//}


if(!$requestedFeeds) die('[]');

//print_r($where);die;

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


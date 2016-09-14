<?php

// this allows to convert public events for a facebook page to iCal
// it requires a facebook app from https://developers.facebook.com/apps,
// which needs to have the domain hosting hubcal added to settings|basic|app domains
// add your access token to includes/hubconfig.php


//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once('iCalcreator/iCalcreator.php');
require_once('includes/hubconfig.php');

$fb_id = $_GET['id'];
$is_group = $_GET['group'];
$path = $root_path . 'export/';
$file = hash('md5',$fb_id).'.ics';

// these are stored in hubconfig:
$config = array( "unique_id" => $ical_id
               , "TZID"      => $timezone );
               
// load the cached version?
if(file_exists($path.$file) && (time() - filemtime($path.$file) <3600)){
  header('Content-type: text/calendar; charset=utf-8');
  header('Content-Disposition: attachment; filename=' . $file);
  readfile($path.$file);
  die;
}

// get facebook events for group or page
if($is_group) {
  $access_token = "";  // TODO: find out how to generate one of these - you can get a temporary one through graph explorer: https://developers.facebook.com/tools/explorer
  $outArr = get_graph($fb_id."/events/");
  $events = $outArr['data'];
}
else {
  // either use access token, or construct one from app_id/secret
  $access_token = ($access_token ? $access_token : "$facebook_app_id|$facebook_app_secret");
  $outArr = get_graph($fb_id,'events');
  $events = $outArr['events']['data'];
}

// stop if we have no events
if(empty($events)) die;

// get Page/Group name
$outArr = get_graph($fb_id, 'name,description');
$calendarName = $outArr['name'];
$calendarDesc = $outArr['description'];

// sort events by date
$sortedEvents = array();
foreach($events as $event){
  $sortedEvents[$event['start_time']] = $event;
}
ksort($sortedEvents);

// start calendar output
  // opt. "calendar" timezone
$v      = new vcalendar( $config );
  // create a new calendar instance
$v->setProperty( "method", "PUBLISH" );
  // required of some calendar software
$v->setProperty( "x-WR-CALNAME", $calendarName );
  // required of some calendar software
$v->setProperty( "X-WR-CALDESC", $calendarDesc );
  // required of some calendar software
$v->setProperty( "X-WR-TIMEZONE", $timezone );
  // required of some calendar software
$xprops = array( "X-LIC-LOCATION" => $timezone );
  // required of some calendar software
iCalUtilityFunctions::createTimezone( $v, $timezone, $xprops );
  // create timezone component(-s) opt. 1
  // based on present date

// loop through events
$out = '';
foreach($sortedEvents as $event){
  $title = $event['name'];
  $start = $event['start_time'];
  $end = $event['end_time'];
  $id = $event['id'];
  $location = $event['location'];
  $detailsArr = get_graph($id,'cover,description');
  $description = $detailsArr['description'];  
  $image = $detailsArr['cover']['source'];
  $url = "https://www.facebook.com/events/$id/";
  
  // new iCal Event:  
  $vevent = & $v->newComponent( "vevent" ); // create an event calendar component
  $vevent->setProperty( "dtstart", dateArray(strtotime($start)) );
  if($end) $vevent->setProperty( "dtend",   dateArray(strtotime($end)) );
  $vevent->setProperty( "LOCATION", $location );
  $vevent->setProperty( "summary", $title );
  $vevent->setProperty( "description", $description );
  if(!empty($comment)) $vevent->setProperty( "comment", $comment );
  $vevent->setProperty( "ATTACH" , $image );
  $vevent->setProperty( "URL" , $url );
  $vevent->setProperty( "UID" , $start."-".$id."@".$ical_id);
}

// save & output the iCal file
$v->setConfig("directory", "export");
$v->setConfig("filename", $file);
$v->saveCalendar();
  header('Content-type: text/calendar; charset=utf-8');
  header('Content-Disposition: attachment; filename=' . $file);
  readfile($path.$file);  


// get graph object/fields via curl
function get_graph($object,$fields=false){
  global $access_token;
  $graph_url= "https://graph.facebook.com/$object" . ($fields ? "?fields=$fields&access_token=" : "?access_token=") . $access_token;
  //echo $graph_url;die;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $graph_url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $output = curl_exec($ch);
  curl_close($ch);
  return json_decode($output,true);;  
}

// iCal functions
function dateArray($timestamp){ 
  $dateArray = array( "year"  => date('Y',$timestamp)
                    , "month" => date('m',$timestamp)
                    , "day"   => date('d',$timestamp)
                    , "hour"  => date('H',$timestamp)
                    , "min"   => date('i',$timestamp)
                    , "sec"   => date('s',$timestamp) );
  return $dateArray;
}
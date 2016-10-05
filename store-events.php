<?php

/**
 * fetches events for all feed sources (from feeds table),
 * parses and sanitises them and stores them in the events table
**/

set_time_limit(100); // try to increase the PHP time limit to 100s

require_once('class.iCalReader.php');
require_once('includes/hubconfig.php');
require_once('includes/db-functions.php');
db_auth($db,$user,$pw);

// to delete all existing events
// $sQuery = "TRUNCATE TABLE `events`";
// $rResult = mysql_query($sQuery);
// die;

// load all feed sources from database
$feeds = db_select('feeds', array('*'));
$feeds = $feeds['result'];

echo (!empty($_SERVER['HTTP_HOST']) ? "<pre>\r\n" : ""); // formatting for web view
// loop through all feed sources
foreach ($feeds as $index => $feed){
  echo "fetching feed ".$feed['id'].") ".$feed['name']."\r\n";
  
  // fetch events for feed source
  $ical   = new ICal($feed['url']);
  $events = $ical->events();
  if(!$events){continue;}
  // loop through all events for a feed source
  foreach ($events as $event) {
 
    // grab and sanitise the fields
    $uid = $event['UID'];
    $title = tidy($event['SUMMARY']);
  //echo "&nbsp;&nbsp;fetching event <b>".$uid."</b> ".$title."<br/>";

    $body = tidy($event['DESCRIPTION']);
    
    // turn URL into links
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    if(preg_match($reg_exUrl, $body, $url)){
      $body = preg_replace($reg_exUrl, "<a href='{$url[0]}' target='_blank'>{$url[0]}</a> ", $body);
    }
    
    // turn email address into links
    $mail_pattern = "/([A-z0-9\._-]+\@[A-z0-9_-]+\.)([A-z0-9\_\-\.]{1,}[A-z])/";
    $body = preg_replace($mail_pattern, '<a href="mailto:$1$2">$1$2</a>', $body);
    
    // if we don't have a start date we can't add the event
    if(empty($event['DTSTART'])) continue;
    
    // validate and format the event data
    $start = date(DATE_ISO8601, $ical->iCalDateToUnixTimestamp($event['DTSTART']));
    $end = (!empty($event['DTEND']) ? date(DATE_ISO8601, $ical->iCalDateToUnixTimestamp($event['DTEND'])) : false);

    $url = (!empty($event['URL']) ? $event['URL'] : false);
    $location = (!empty($event['LOCATION']) ? tidy($event['LOCATION']) : false);
    $modified = (!empty($event['LAST-MODIFIED']) ? $event['LAST-MODIFIED'] : false);
    $organizerName = (!empty($event['ORGANIZER_array'][0]['CN']) ? $event['ORGANIZER_array'][0]['CN'] : false);
    $organizerEmail = (!empty($event['ORGANIZER_array'][1]) ? substr($event['ORGANIZER_array'][1],7) : false); // remove MAILTO:
    $attachment = (!empty($event['ATTACH']) ? $event['ATTACH'] : false);
    $sequence = (!empty($event['SEQUENCE']) ? $event['SEQUENCE'] : false);

    // insert the event into the database
    $aFields = array(
                      'feedID' =>$feed['id'],
                      'UID' => $uid,
                      'title' => $title,
                      'body' => $body,
                      'start' => $start,
                      'end' => $end,
                      'url' => $url,
                      'location' => $location,
                      'modified' => $modified,
                      'organizerName' => $organizerName,
                      'organizerEmail' => $organizerEmail,
                      'attachment' => $attachment
                     ); 
    $result = db_insert('events', $aFields);
    if(!empty($result['error'])){
      // the event already exists - only update if there has been a revision
      if($sequence){
        echo "\tupdating event ".$uid." ".$title."\r\n";
        db_update('events', $aFields, array('UID'=>$uid));
      }
    }
    else {
      echo "&nbsp;&nbsp;adding event ".$uid." ".$title."\r\n";        
    }
  }
  
}
echo "\r\nfinished storing events\r\n";
$executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
echo "\r\nThis script took $executionTime to execute.\r\n";
echo (!empty($_SERVER['HTTP_HOST']) ? "</pre>\r\n" : ""); // formatting for web view

// tidy up various escaped characters
function tidy($txt){
  $tidy = str_replace("\\n"," <br/>",$txt);
  $tidy = str_replace("\\,",",",$tidy);
  $tidy = str_replace("\\;",";",$tidy);
  $tidy = str_replace("\\t","    ",$tidy);
  return $tidy;
}
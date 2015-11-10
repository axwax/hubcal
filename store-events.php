<?php

require 'class.iCalReader.php';
require '../includes/db-functions.php';
db_auth();

// delete all existing events
//$sQuery = "TRUNCATE TABLE `events`";
//$rResult = mysql_query($sQuery);

$category = 1;
$feeds = db_select('feeds', array('*'), array('category' => $category ));
$feeds = $feeds['result'];

// loop through all feed sources
foreach ($feeds as $index => $feed){
  echo "<h2>fetching feed <b>".$feed['id'].") ".$feed['name']."</b></h2>";
  
  // fetch events for feed source
  $ical   = new ICal($feed['url']);
  $events = $ical->events();

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

    
    $start = date(DATE_ISO8601, $ical->iCalDateToUnixTimestamp($event['DTSTART']));
    $end = date(DATE_ISO8601, $ical->iCalDateToUnixTimestamp($event['DTEND']));
    $url = $event['URL'];
    $location = tidy($event['LOCATION']);
    $modified = $event['LAST-MODIFIED'];
    $organizerName = $event['ORGANIZER_array'][0]['CN'];
    $organizerEmail = substr($event['ORGANIZER_array'][1],7); // remove MAILTO:
    $attachment = $event['ATTACH'];
    $sequence = $event['SEQUENCE'];

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
    if($result['error']){
      // the event already exists - only update if there has been a revision
      //echo "<br/>EVENT EXISTS";
      if($sequence){
        //echo "<br/>UPDATING EVENT:";
        echo "&nbsp;&nbsp;updating event <b>".$uid."</b> ".$title."<br/>";
        db_update('events', $aFields, array('UID'=>$uid));
      }
    }
    else {
      echo "&nbsp;&nbsp;adding event <b>".$uid."</b> ".$title."<br/>";        
    }
  }
  
}

function tidy($txt){
  $tidy = str_replace("\\n"," <br/>",$txt);
  $tidy = str_replace("\\,",",",$tidy);
  $tidy = str_replace("\\;",";",$tidy);
  $tidy = str_replace("\\t","    ",$tidy);
  return $tidy;
}

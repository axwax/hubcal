<?php

/**
 * dodgy script to add feed sources to the feeds table in the database
 * this does not check if a feed has already been added
 * so you'll have to use a database admin tool to keep this table tidy if you make any mistakes
**/

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once('class.iCalReader.php');
require_once('includes/hubconfig.php');
require_once('includes/db-functions.php');
db_auth($db,$user,$pw);


$feedURLs = array();

/*
 * here you need to add the feed URLs in the form
 * $feedURLs['feed name'] = 'http://feed_url';
 * some examples:
$feedURLs['Cambridge Conservation Volunteers'] = 'https://www.google.com/calendar/ical/cbhiefng3frcml9u64n3c4refk%40group.calendar.google.com/public/basic.ics';
$feedURLs['PiNGS'] = 'http://groupspaces.com/PiNGS/api/events?alt=ical';
$feedURLs['Afrinspire'] = 'http://groupspaces.com/AFRINSPIRE/api/events?alt=ical';
$feedURLs['Centre for Global Equality'] = 'http://centreforglobalequality.org/calendar/list/?ical=1&tribe_display=list';
$feedURLs['Cambridge Hub'] = 'https://www.google.com/calendar/ical/cambridge.green.calendar%40gmail.com/public/basic.ics';
$feedURLs['Cambridge Carbon Footprint'] = 'http://cambridgecarbonfootprint.org/?ec3_ical';
$feedURLs['Cambridge Sustainable Food City'] = 'http://www.cambridgesustainablefood.org/events/?ical=1&tribe_display=list';
$feedURLs['Transition Cambridge'] = 'http://www.meetup.com/Transition-Cambridge/events/ical/';
*/

$feedcount = 0;
foreach ($feedURLs as $name => $url){
  $aFields = array ('name' => $name, 'url' => $url, 'category' => 1);
  db_insert('feeds', $aFields); 
  $feedcount++;
}
echo "$feedcount feeds added";
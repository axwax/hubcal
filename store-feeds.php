<?php

require 'class.iCalReader.php';
require '../../includes/db-functions.php';
db_auth();

$feedURLs = array();
$feedURLs['PiNGS'] = 'http://groupspaces.com/PiNGS/api/events?alt=ical';
$feedURLs['Afrinspire'] = 'http://groupspaces.com/AFRINSPIRE/api/events?alt=ical';
$feedURLs['Centre for Global Equality'] = 'http://centreforglobalequality.org/calendar/list/?ical=1&tribe_display=list';
$feedURLs['Cambridge Hub'] = 'https://www.google.com/calendar/ical/cambridge.green.calendar%40gmail.com/public/basic.ics';
$feedURLs['Cambridge Carbon Footprint'] = 'http://cambridgecarbonfootprint.org/?ec3_ical';
$feedURLs['Cambridge Sustainable Food City'] = 'http://www.cambridgesustainablefood.org/events/?ical=1&tribe_display=list';
$feedURLs['Transition Cambridge'] = 'http://www.meetup.com/Transition-Cambridge/events/ical/';

$feedcount = 0;
foreach ($feedURLs as $name => $url){
  //$feed = db_select('feeds', array('*'), array('name' => $feedName ));
  $aFields = array ('name' => $name, 'url' => $url);
  db_insert('feeds', $aFields); 
  $feedcount++;
}
echo "$feedcount feeds added";
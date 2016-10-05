<?php

// fill in the below variables and rename the file to hubconfig.php

// database details
$db = "";                                 // database name
$user = "";                               // database username
$pw = "";                                 // database password

// file system
// this should give the root folder for hubcal (ie one level up from this includes directory)
// and MUST include a trailing slash
$root_path = dirname(dirname(__FILE__))."/";
// if the above doesn't work for you enter the absolute path manually, eg:
//$root_path = "/var/www/example.com/hubcal/";

// these are used for iCal creation (ie by fb2ical and by merge-feeds)
$ical_id = "hubcal.cambridgehub.org";          // unique id for iCal feed- can be anything, but should contain your domain name
$timezone = "Europe/London";

// facebook app details
$facebook_app_id = "187948597266";
$facebook_app_secret = "cd7f1cd7429fd243383d959c5d9ddf3a";
// alternatively, you could generate an app token through facebook and then put it in the variable below
$facebook_app_token = "";

// end configuration - do not edit below this line
///////////////////////////
if(!empty($_SERVER['HTTP_HOST'])){ // do not define root when run in php cli
  $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
}
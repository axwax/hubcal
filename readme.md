![hubCal](https://github.com/axwax/hubcal/raw/master/images/hubcal1200.jpg "hubCal")

# Installation
## 1. Download the latest version
* Go to the [Releases](https://github.com/axwax/hubcal/releases) page to download the latest stable version.
* Extract the zip file and upload it to the root (or subfolder) of your webserver

## 2. Create a MySQL database:
* create a database named 'hubcal'
* import either the complete (hubcal.sql) or structure-only (hubcal-structure.sql) sql file

## 3. Create the config file:
* edit includes/hubconfig.example.php and fill in your database details
* optionally enter your facebook app details (see below)
* save the file as hubconfig.php

## 4. To add feeds:
* edit store-feeds.php
* add an element in the form `$feedURLs['Feed Name'] = 'http://ical-feed-url.com/feed.ics'` to the $feedURLs array
* by default these get added to category 1 (umbrella groups) - change the $category variable to add the feed to another category
* execute the file in the browser
* to edit or delete feeds you will need to edit the feeds table in the database directly
List of categories and their IDs:

| ID | Name |
| ---: | --- |
| 1 | Umbrella Groups |
| 2 | Environment - Energy, Sustainability & Ethical Finance |
| 3 | Environment- Conservation |
| 4 | Environment- Ethical Consumerism |
| 5 | Environment- Food Waste & Food Shortage |
| 6 | Education |
| 7 | Homelessness |
| 8 | Health & Wellbeing |
| 9 | International Volunteering |
| 10 | International Campaigning |

## 5. Set up Cron Job for events import:
To update the events you will need to set up a cron job to run the store-event file once a day. For debugging purposes you can also manually update the events table by running `http://your-server.com/store-events.php`
* `crontab -e`
* add a call to the store-events.php script, for example:

    `00 01 * * * /usr/bin/php /var/www/yourhost.com/httpdocs/store-events.php`
    
    this executes the script every day at 1am
    
    you may also have to adjust the path to php
    
* `:wq` to save and exit (or `:q!` to quit without saving)

make sure your max_execution_time setting for php cli is sufficiently high - the script can easily exceed 30s execution time!

## 6. To add feeds for a public Facebook page:
* you will have to set up a facebook app at https://developers.facebook.com/apps, which needs to have the domain hosting hubcal added to settings|basic|app domains.
* then add your app id/secret or your access token to includes/hubconfig.php
* you can now add a feed by editing store-feeds.php to include a url in the form
    `http://your-hubcal-url.com/fb2ical.php?id=CambridgeHub`
    (replace CambridgeHub with the id of the Facebook page)

# Used Libraries:
## PHP
* [ics-parser](https://github.com/MartinThoma/ics-parser/) version 2015-04-16 (you may want to upgrade to [its successor](https://github.com/u01jmg3/ics-parser))
* [iCalcreator](http://kigkonsult.se/iCalcreator/) version 2.22

## Javascript / CSS
* [Moment.js](http://momentjs.com/) version 2.9.0
* [jQuery](https://jquery.com/) version 2.1.3
* [FullCalendar](http://fullcalendar.io) version 2.4.0
* [Bootstrap](http://getbootstrap.com/) version 3.3.5

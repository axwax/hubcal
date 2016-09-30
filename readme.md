# Installation
## Set up Cron Job for events import:
* `crontab -e`
* add a call to the store-events.php script, for example:

    `00 01 * * * /usr/bin/php /var/www/yourhost.com/httpdocs/store-events.php`
    
    this executes the script every day at 1am
    
    you may also have to adjust the path to php
    
* `:wq` to save and exit (or `:q!` to quit without saving)

## To add feeds for a public Facebook page:
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

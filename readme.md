# Installation
## Set up Cron Job for events import:
* `crontab -e`
* add a call to the store-events.php script, for example:

    `00 01 * * * /usr/bin/php /var/www/yourhost.com/httpdocs/store-events.php`
    
    this executes the script every day at 1am
    
    you may also have to adjust the path to php
    
* `:wq` to save and exit (or `:q!` to quit without saving)

# Used Libraries:
## PHP
* [ics-parser](https://github.com/MartinThoma/ics-parser/)
* [iCalcreator](http://kigkonsult.se/iCalcreator/) version 2.22

## Javascript / CSS
* [Moment.js](http://momentjs.com/) version 2.15.0
* [jQuery](https://jquery.com/) version 3.1.0
* [FullCalendar](http://fullcalendar.io) version 3.0.0
* [Bootstrap](http://getbootstrap.com/) version 3.3.5

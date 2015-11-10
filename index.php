<?php
if($_GET['date']){
  $defaultDate = date("Y-m-d", strtotime($_GET['date']));
  $scrollTime = date("H:i:s", strtotime($_GET['date']));
}
else{
  $defaultDate = date("Y-m-d");
  $scrollTime = date("H:i:s");
}
if($_GET['mintime']){
  $minTime = date("H:i:s", strtotime("today " . $_GET['mintime']));
}
$defaultView = '';
if($_GET['view']){
  $views = array('month', 'basicWeek', 'basicDay', 'agendaWeek', 'agendaDay');
  if(in_array($_GET['view'], $views, true)){
    $defaultView = $_GET['view'];
  }
}

if($_GET['height'] && is_numeric($_GET['height']) && $_GET['height']>200){
  $calendarHeight = $_GET['height']-100;
}
else {
  $calendarHeight = 'false';
}


?><!DOCTYPE html>
<meta name="robots" content="noindex">
  <meta name="viewport" content="width=device-width">
<html>
<head>
<title>Cambridge Green Calendar</title>

<meta charset='utf-8' />

<meta property="og:locale" content="en_GB" />
<meta property="og:type" content="website" />
<meta property="og:title" content="Cambridge Green Calendar" />
<meta property="og:description" content="This currently shows events from PiNGS, Afrinspire, Centre for Global Equality, Cambridge Hub, Cambridge Carbon Footprint, Cambridge Sustainable Food City, Transition Cambridge, Cambridge Conservation Forum and Cambridge Conservation Volunteers" />
<meta property="og:url" content="http://green-calendar.gigx.co.uk/" />
<meta property="og:site_name" content="Cambridge Green Calendar" />
<meta property="og:image" content="http://green-calendar.gigx.co.uk/images/green-calendar-2015-11-09.png" />

<link href='http://fullcalendar.io/js/fullcalendar-2.4.0/fullcalendar.css' rel='stylesheet' />
<link href='http://fullcalendar.io/js/fullcalendar-2.4.0/fullcalendar.print.css' rel='stylesheet' media='print' />
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" />

<link rel="icon" href="http://green-calendar.gigx.co.uk/images/calendar-icon-32x32.png" sizes="32x32" />
<link rel="icon" href="http://green-calendar.gigx.co.uk/images/calendar-icon-192x192.png" sizes="192x192" />
<link rel="apple-touch-icon-precomposed" href="http://green-calendar.gigx.co.uk/images/calendar-icon-180x180.png">
<meta name="msapplication-TileImage" content="http://green-calendar.gigx.co.uk/images/calendar-icon-270x270.png">

<script src='http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js'></script>
<script src='http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src='http://fullcalendar.io/js/fullcalendar-2.4.0/fullcalendar.js'></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" ></script>

<style>
  
/* general */  
body {
  margin: 10px 10px 40px 10px;
  padding: 0;
  font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
  font-size: 14px;
}
.aligncenter{
  clear: both;
    display: block;
    margin-left: auto;
    margin-right: auto;
}
.responsive{
  max-width: 100%;
  height: auto;
}


/* settings button */
#menu-button {
  display: none;
  margin-bottom: 10px;
}


/* settings / feeds */
#settings {
    background-color: #eee;
    padding: 3px 5px;
    width: auto;
    margin: 0 auto 10px auto;
    display: inline-block;
    border-radius: 5px;
}
#settings .header {display: none; text-align: right; background-color: #ddd;}
#settings h4{display: inline-block;}
#feedList {display:inline;margin: 0;}
#feedList li {
	display:inline-block;
	padding: 2px 5px;
	border-radius: 5px;
	margin: 5px 5px 5px 0;
	border: 1px solid transparent;
	box-shadow: 0;
}
#feedList li:hover {
	cursor:pointer;
	box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset,
             0 0 2px rgba(255, 255, 255, 0.3) inset,
             0 1px 2px rgba(0, 0, 0, 0.29);
}
#feedList li.disabled{
	background-color: transparent !important;
	color: #666 !important;
	border: 1px solid #666;
}

#menu-button {display: inline-block;}
#settings {width: 100%; display: none;text-align:center;}
#close-button {float:left;}

.mobile #menu-button {display: inline-block;}
.mobile #settings {width: 100%; display: none;}
.mobile #settings .header {display: block;}
.mobile #feedList li {display: block;}
.mobile .fc-center {margin-top: 1em;}

/* calendar */
#calendar {
  max-width: 900px;
  margin: 0 auto;
}
.fc-content:hover{cursor: pointer;}

/* modals */
.btn a{color:#fff;}
#progressModal{top:30%;}

/* colour scheme */
.btn-primary{background-color: #8dc63f; border-color: #75a434;}
.btn-primary:hover, .btn-primary.active.focus, .btn-primary.active:focus, .btn-primary.active:hover, .btn-primary:active.focus, .btn-primary:active:focus, .btn-primary:active:hover, .open>.dropdown-toggle.btn-primary.focus, .open>.dropdown-toggle.btn-primary:focus, .open>.dropdown-toggle.btn-primary:hover {
    background-color: #7db038;
    border-color: #304416;
}
</style>
</head>
<body>
  <header>
    <button id ="menu-button" type="button" class="btn btn-default">
      <span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
    </button>
    <div id="settings">
      <button id ="close-button" type="button" class="btn btn-default">
        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
      </button>      
      <h4>Calendars:</h4>
      <ul id="feedList"></ul>
    </div>
  </header>
	<div id='calendar'></div>
  
<div id="fullCalModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span> <span class="sr-only">close</span></button>
                <h4 id="modalTitle" class="modal-title"></h4>
            </div>
            <div id="modalBody" class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button class="btn btn-primary"><a id="eventUrl" target="_blank">Event Page</a></button>
            </div>
        </div>
    </div>
</div>

<div id="progressModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span> <span class="sr-only">close</span></button>
                <h4 id="progressModalTitle" class="modal-title">Fetching Calendars - please wait</h4>
            </div>
            <div id="progressModalBody" class="modal-body">
                <div class="progress">
                  <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                    <span class="sr-only">Fetching Calendars</span>
                  </div>
                </div>              
            </div>            
        </div>
    </div>
</div>



<script id="jsbin-javascript">
$(function() { // document ready
  
  // default View & mobile/desktop buttons/styles
  defaultView = '<?php echo $defaultView; ?>';
  if (!defaultView && localStorage.getItem( 'defaultView' )) {
    defaultView = localStorage.getItem( 'defaultView' );
  }
  if ($(window).width() < 514){
    viewButtons = 'month,basicWeek,basicDay';
    $('body').addClass('mobile');
    if (!defaultView) {
      defaultView = 'basicDay';
    }
  }
  else{
    viewButtons = 'month,agendaWeek,agendaDay';
    if (!defaultView) {
      defaultView = 'month';
    }    
  }

  // height
  calendarHeight = <?php echo $calendarHeight; ?>;
  if (typeof calendarHeight === 'undefined' || !calendarHeight) {
    calendarHeight = 'auto';
  }
  
  // minTime and scrollTime
  minTime = '<?php echo $minTime; ?>';
  scrollTime = '<?php echo $scrollTime; ?>';
  if (typeof minTime === 'undefined' || !minTime) {
    minTime = '00:00:00';
  }
  if (typeof scrollTime === 'undefined' || !scrollTime) {
    scrollTime = '06:00:00';
  }
  
  feeds = {};
  selectedFeeds = {};
  colours = ['Blue', 'BlueViolet', 'Brown', 'CadetBlue', 'Crimson', 'Coral', 'CornflowerBlue', 'ForestGreen', 'MidnightBlue', 'DarkBlue', 'DarkGreen', 'DarkGoldenRod', 'Chocolate', 'DarkMagenta', 'DarkOliveGreen', 'Darkorange', 'DarkOrchid', 'DarkRed', 'DarkSalmon', 'DarkSeaGreen', 'DarkSlateBlue', 'DarkSlateGray', 'DarkSlateGrey', 'DarkTurquoise', 'DarkViolet', 'DeepPink', 'DeepSkyBlue', 'DimGray', 'DimGrey', 'DodgerBlue', 'FireBrick', 'Fuchsia', 'Gainsboro', 'Gold', 'GoldenRod', 'Gray', 'Grey', 'Green', 'HoneyDew', 'HotPink', 'IndianRed', 'Indigo', 'Ivory', 'Khaki', 'Lavender', 'LavenderBlush', 'LawnGreen', 'LemonChiffon', 'LightBlue', 'LightCoral', 'LightCyan', 'LightGoldenRodYellow', 'LightGray', 'LightGrey', 'LightGreen', 'LightPink', 'LightSalmon', 'LightSeaGreen', 'LightSkyBlue', 'LightSlateGray', 'LightSlateGrey', 'LightSteelBlue', 'LightYellow', 'Lime', 'LimeGreen', 'Linen', 'Magenta', 'Maroon', 'MediumAquaMarine', 'MediumBlue', 'MediumOrchid', 'MediumPurple', 'MediumSeaGreen', 'MediumSlateBlue', 'MediumSpringGreen', 'MediumTurquoise', 'MediumVioletRed', 'MintCream', 'MistyRose', 'Moccasin', 'NavajoWhite', 'Navy', 'OldLace', 'Olive', 'OliveDrab', 'Orange', 'OrangeRed', 'Orchid', 'PaleVioletRed', 'Peru', 'Purple', 'Red', 'RosyBrown', 'RoyalBlue', 'SaddleBrown', 'Salmon', 'SandyBrown', 'SeaGreen', 'SeaShell', 'Sienna', 'Silver', 'SkyBlue', 'SlateBlue', 'SlateGray', 'SlateGrey', 'Snow', 'SpringGreen', 'SteelBlue', 'Tan', 'Teal', 'Thistle', 'Tomato', 'Turquoise', 'Violet', 'Wheat', 'White', 'WhiteSmoke', 'YellowGreen'];
  $('#progressModal').modal('show');
  $.getJSON( "load-feeds.php", function( data ) {
    feeds = data;

    var feedListHTML = '';
    selectedFeeds = JSON.parse( localStorage.getItem( 'selectedFeeds' ) );
    if(typeof selectedFeeds === 'undefined' || selectedFeeds === null){
      selectedFeeds= {};
      $.each(feeds, function(feedID, feed){
        selectedFeeds[feedID] = true;
      });
    }
    $.each(feeds, function(feedID, feed){
      feeds[feedID]['colour'] = colours[feedID];
      feedListHTML += '<li class="disabled" id="'+feedID+'" style="background-color: '+colours[feedID]+'; color: white;">'+feed.name+'</li>'
    });
    $('#feedList').html(feedListHTML);
      $.each(selectedFeeds, function(feedID,selected){
      $('#'+feedID).removeClass('disabled');
    });

    console.log(scrollTime);
    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: viewButtons
      },
      defaultView: defaultView,
      editable: true,
      firstDay: 1,
      defaultDate: "<?php echo $defaultDate; ?>",
      
      columnFormat: { month: 'ddd', week: 'ddd D/M', day: 'dddd D/M' },    
      events: {
          editable: false,
          url: 'load-events.php',
          type: 'POST',
          data: function() { // a function that returns an object
              var feedsToFetch = {};
              $.each(selectedFeeds, function(key, selected){
                if (typeof feeds[key] !== 'undefined') {
                  feedsToFetch[key] = feeds[key].colour;
                }
                
              });
              return {
                  feeds: feedsToFetch
              };
          },
          error: function() {
              $('#modalBody').html('there was an error while fetching events!');
          },
          success: function() {
                $('#progressModal').modal('hide');
          },       
      },
      height: calendarHeight,
      minTime: minTime,
      scrollTime: scrollTime,
      timeFormat: 'H:mm' ,
      axisFormat: 'HH:mm',
      viewRender: function( view, element ){
        localStorage.setItem( 'defaultView', view.type);
      },
      windowResize: function(view) {
        // switch view depending on screen size
        if ($(window).width() < 514){
          $('body').addClass('mobile');        
        } else {
          $('body').removeClass('mobile');      
        }        
      },
      
      eventClick: function(event, jsEvent, view) {
          var start = moment(event.start).format("dddd, MMMM Do YYYY, h:mm a");
          var end = moment(event.end).format("dddd, MMMM Do YYYY, h:mm a");
          console.log(event);
          var eventHeader = (event.location ? '<b>Venue: </b> '+event.location +'<br/>' : '')+'<b>Starts:</b> '+start+'<br/>'+'<b>Ends:</b> '+end+'<br/>'+'<b>Event Source:</b> '+event.eventSource +' <a href="'+event.eventSourceURL+'">[iCal Feed]</a><br/>';
              $('#modalTitle').html(event.title);
              if (typeof event.attachment !== 'undefined') {
                eventHeader = '<img class="aligncenter responsive" src="' + event.attachment + '"/>' + eventHeader;
              }
              if (typeof event.organizerEmail !== 'undefined' && typeof event.organizerName !== 'undefined' && event.organizerEmail&& event.organizerName) {
                console.log(event);
                eventHeader += '<b>Contact Organiser:</b> <a href="mailto:'+event.organizerEmail+'">'+event.organizerName+'</a><br/>';
              }
              body = eventHeader+'<br/>'+event.body;
              $('#modalBody').html(body);
              $('#eventUrl').attr('href',event.url);
              $('#fullCalModal').modal();
           return false;
      },
    });  
  });
  

	

  $('#feedList').on('click','li',function(event){
		$(this).toggleClass("disabled");

		selectedFeeds = {};
		$('#feedList li:not(.disabled)').each(function(index, element){
			var feedID = $(element).attr('id');
			console.log(feedID);
			selectedFeeds[feedID] = true;
		});
		localStorage.setItem( 'selectedFeeds', JSON.stringify(selectedFeeds) );
		$('#calendar').fullCalendar('refetchEvents');
	});

  $(document).on('click','#menu-button', function(){
    $('#menu-button').fadeOut('fast',function(){$('#settings').slideDown('fast');});
  });  
  $('#settings').on('click','#close-button', function(){
    $('#settings').slideUp('slow',function(){$('#menu-button').fadeIn('fast')});

  });

  
});
</script>
</body>
</html>

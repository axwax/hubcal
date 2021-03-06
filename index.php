<?php

/* The main calendar page
**/

// get the root URL (for any resources that need a full path)
$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';

// parse GET data and set up variables
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
<title>HubCal</title>

<meta charset='utf-8' />

<meta property="og:locale" content="en_GB" />
<meta property="og:type" content="website" />
<meta property="og:title" content="HubCal - Cambridge Hub's Ethical & Sustainable Calendar" />
<meta property="og:description" content="View and subscribe to a variety of events feeds from ethical, environmental and sustainability projects in Cambridge." />
<meta property="og:url" content="<?php echo $root; ?>" />
<meta property="og:site_name" content="HubCal - Cambridge Hub's Ethical & Sustainable Calendar" />
<meta property="og:image" content="<?php echo $root; ?>images/hubcal1200.jpg" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />

<link href='//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css' rel='stylesheet' />
<link href='//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.print.css' rel='stylesheet' media='print' />
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
<link href="style.css" rel="stylesheet" />

<link rel="icon" href="<?php echo $root; ?>images/calendar-icon-32x32.png" sizes="32x32" />
<link rel="icon" href="<?php echo $root; ?>images/calendar-icon-192x192.png" sizes="192x192" />
<link rel="apple-touch-icon-precomposed" href="<?php echo $root; ?>images/calendar-icon-180x180.png">
<meta name="msapplication-TileImage" content="<?php echo $root; ?>images/calendar-icon-270x270.png">

<script src='//cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js'></script>
<script src='//cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/locale/en-gb.js'></script>
<script src="//code.jquery.com/jquery-3.1.1.min.js"   integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="   crossorigin="anonymous"></script>
<script src='//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js'></script>
<script src='//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/locale/en-gb.js'></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ></script>

</head>
<body>
	<header>
		<button id ="menu-button" type="button" class="btn btn-default">
	    <span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
		</button>
		<img class="desktop-logo" src="images/hubcal-logo.png" width="175" height="32"/>
		<div id="settings">
      <button id ="close-button" type="button" class="btn btn-default">
        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
      </button>
      <h4>Categories:</h4>
      <ul id="categoryList"></ul><br/>
      <div>Subscribe to selected categories: 
      <a class="ical" target="_blank"><img width="16" src="/images/google-calendar-64x64.png"/> Add to Google Calendar/Other</a>
      <a class="webcal" target="_blank"><img width="16" src="/images/ical-64x64.png"/> Add to Calendar App (Apple Calendar/Outlook/iPhone?)</a>
      <div id='iCalURL' >
				<input type="text"/>
        <p>Copy the above URL (Windows: CTRL+C, OSX: Command+C) and paste it into any calendar app that takes iCal feeds.</p>
      </div>
      </div>
      <sub>***It may take a while to import to your calendar - please bear with us, and re-import if it doesn't appear!***</sub>
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
  
  $('#settings #iCalURL').hide();
  
  // default View & mobile/desktop buttons/styles
  defaultView = '<?php echo $defaultView; ?>';
  if (!defaultView && localStorage.getItem( 'defaultView' )) {
    defaultView = localStorage.getItem( 'defaultView' );
  }
  if ($(window).width() < 514){
    viewButtons = 'month,basicWeek,list,listMonth'; // could replace basicDay with list 
    $('body').addClass('mobile');
    if (!defaultView) {
      defaultView = 'basicDay';
    }
  }
  else{
    viewButtons = 'month,agendaWeek,agendaDay,listMonth'; // could add listMonth
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
  
  selectedCategories = {};
  colours = ['', 'Crimson', 'DarkGreen', 'ForestGreen', 'DarkOliveGreen', 'DarkSeaGreen', 'CornflowerBlue', 'Brown', 'BlueViolet', 'Coral', 'CornflowerBlue', 'MidnightBlue', 'DarkBlue', 'DarkGoldenRod', 'Chocolate', 'DarkMagenta', 'Darkorange', 'DarkOrchid', 'DarkRed', 'DarkSalmon', 'DarkSlateBlue', 'DarkSlateGray', 'DarkSlateGrey', 'DarkTurquoise', 'DarkViolet', 'DeepPink', 'DeepSkyBlue', 'DimGray', 'DimGrey', 'DodgerBlue', 'FireBrick', 'Fuchsia', 'Gainsboro', 'Gold', 'GoldenRod', 'Gray', 'Grey', 'Green', 'HoneyDew', 'HotPink', 'IndianRed', 'Indigo', 'Ivory', 'Khaki', 'Lavender', 'LavenderBlush', 'LemonChiffon', 'LightBlue', 'LightCoral', 'LightCyan', 'LightGoldenRodYellow', 'LightGray', 'LightGrey', 'LightGreen', 'LightPink', 'LightSalmon', 'LightSeaGreen', 'LightSkyBlue', 'LightSlateGray', 'LightSlateGrey', 'LightSteelBlue', 'LightYellow', 'Lime', 'LimeGreen', 'Linen', 'Magenta', 'Maroon', 'MediumAquaMarine', 'MediumBlue', 'MediumOrchid', 'MediumPurple', 'MediumSeaGreen', 'MediumSlateBlue', 'MediumTurquoise', 'MediumVioletRed', 'MintCream', 'MistyRose', 'Moccasin', 'NavajoWhite', 'Navy', 'OldLace', 'Olive', 'OliveDrab', 'Orange', 'OrangeRed', 'Orchid', 'PaleVioletRed', 'Peru', 'Purple', 'Red', 'RosyBrown', 'RoyalBlue', 'SaddleBrown', 'Salmon', 'SandyBrown', 'SeaGreen', 'SeaShell', 'Sienna', 'Silver', 'SkyBlue', 'SlateBlue', 'SlateGray', 'SlateGrey', 'Snow', 'SpringGreen', 'SteelBlue', 'Tan', 'Teal', 'Thistle', 'Tomato', 'Turquoise', 'Violet', 'Wheat', 'White', 'WhiteSmoke', 'YellowGreen'];
  
  // load the feeds
  $('#progressModal').modal('show');
  $.getJSON( "load-categories.php", function( data ) {
    categories = data.categories;
    // if we don't have a list of selected feeds in local storage, select all feeds
    var categoryListHTML = '';
    
    // select all categories if there's no local storage yet
    selectedCategories = JSON.parse( localStorage.getItem( 'selectedCategories' ) );
    if(typeof selectedCategories === 'undefined' || selectedCategories === null){
      selectedCategories= {};
      $.each(categories, function(categoryID, category){
        selectedCategories[categoryID] = true;
      });
    }
          
    // display list of categories and enable all selected ones 
    $.each(categories, function(categoryID, category){
      categories[categoryID]['colour'] = colours[categoryID];
      categoryListHTML += '<li class="disabled" id="'+categoryID+'" style="background-color: '+colours[categoryID]+'; color: white;">'+category.name+'</li>'
    });
    $('#categoryList').html(categoryListHTML);
    $.each(selectedCategories, function(categoryID,selected){
      $('#'+categoryID).removeClass('disabled');
    });
    updateLinks(selectedCategories);


    //initialise the calendar
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
      events: {
          editable: false,
          url: 'load-events.php',
          type: 'POST',
          data: function() { // a function that returns an object
              var categoriesToFetch = {};
              $.each(selectedCategories, function(key, selected){
                if (typeof categories[key] !== 'undefined') {
                  categoriesToFetch[key] = categories[key].colour;
                }
              });
              return {
                  categories: categoriesToFetch
              };
          },
          error: function() {
              $('#modalBody').html('there was an error while fetching events!');
          },
          success: function(events) {
                $('#progressModal').modal('hide');
          },       
      },
      height: calendarHeight,
			locale: 'en-gb',
      minTime: minTime,
			navLinks: true,
      scrollTime: scrollTime,
      timeFormat: 'H:mm' ,
      slotLabelFormat: 'H:mm',
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
        console.log(event);
          var start = moment(event.start).format("dddd, MMMM Do YYYY, h:mm a");
          var end = moment(event.end).format("dddd, MMMM Do YYYY, h:mm a");
          console.log(event.end);
          var eventHeader = (event.location ? '<b>Venue: </b> '+event.location +'<br/>' : '')+'<b>Starts:</b> '+start+'<br/>'+(event.end ? '<b>Ends:</b> '+end+'<br/>' : '')+'<b>Event Source:</b> <a href="'+event.eventSourceURL+'" target="_blank">' + event.eventSource + '</a> <a href="'+event.eventFeedURL+'">[iCal Feed]</a><br/>';
              $('#modalTitle').html(event.title);
              if (typeof event.attachment !== 'undefined') {
                eventHeader = '<img class="aligncenter responsive" src="' + event.attachment + '"/>' + eventHeader;
              }
              if (typeof event.organizerEmail !== 'undefined' && typeof event.organizerName !== 'undefined' && event.organizerEmail&& event.organizerName) {
                
                eventHeader += '<b>Contact Organiser:</b> <a href="mailto:'+event.organizerEmail+'">'+event.organizerName+'</a><br/>';
              }
              body = eventHeader+'<br/>'+event.body;
              $('#modalBody').html(body);
              if (event.url) {
                $('#eventUrl').attr('href',event.url);
                $('#eventUrl').parent().show();
              }
              else{
                $('#eventUrl').parent().hide();
              }
              console.log('setting event url:'+event.url);
              $('#fullCalModal').modal();
           return false;
      },
    });
		// replace buttons with icons
		$(".fc-month-button").html('<i class="icon icon-month" title="month view"></i>');
		$(".fc-agendaWeek-button,.fc-basicWeek-button").html('<i class="icon icon-week" title="week view"></i>');
		$(".fc-agendaDay-button,.fc-basicDay-button,.fc-list-button").html('<i class="icon icon-day" title="day view"></i>');
		$(".fc-listMonth-button").html('<i class="icon icon-list" title="list view"></i>');
		
  });
  
  // click handler for categoryList
  $('#categoryList').on('click','li',function(event){
		$(this).toggleClass("disabled");

		selectedCategories = {};
		$('#categoryList li:not(.disabled)').each(function(index, element){
			var categoryID = $(element).attr('id');
			console.log(categoryID);
			selectedCategories[categoryID] = true;
		});
		localStorage.setItem( 'selectedCategories', JSON.stringify(selectedCategories) );
    updateLinks(selectedCategories);

    
		$('#calendar').fullCalendar('refetchEvents');
	});

  

  $(document).on('click','#menu-button', function(){
    $('#buttonhelp').fadeOut('fast');
    $('#menu-button').fadeOut('fast',function(){$('#settings').slideDown('fast');});
  });  
  $('#settings').on('click','#close-button', function(){
    $('#settings').slideUp('slow',function(){$('#menu-button').fadeIn('fast')});
    $('#buttonhelp').fadeIn('fast');
    $('#settings #iCalURL').slideUp('fast');
  });

  
});

function updateLinks(categoriesArray){
    var mergeURL = '<?php echo $root; ?>/merge-feeds.php?categories='+Object.keys(selectedCategories).join();
    var webcalURL = 'webcal://'+mergeURL;
    var iCalURL = 'http://'+mergeURL;
    $('#settings #iCalURL input').val(iCalURL);  
    $('#settings .webcal').attr('href',webcalURL);  
}

$('#settings .ical').on('click', function(){
  $('#settings #iCalURL').slideToggle('fast').find('input').focus().select();
});

</script>
</body>
</html>

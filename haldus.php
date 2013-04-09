<?php 
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
require_once "include/Session.php";
$session = new Session();
if (isset($session->user) && ($session->user->usertype === 'peakasutaja')): ?>

<div class="tabbable">
     <ul id="myTabs" class="nav nav-tabs" >
		<li><a id='haldus-tab-1' data-target="#ruumi_lisamine" data-toggle="tab" href="room/add-room.php">Lisa Ruum</a></li>
		<li><a id='haldus-tab-2' data-target="#kasutajad" data-toggle="tab" href="person/person.php">Kasutajad</a></li>
		<li><a id='haldus-tab-3' data-target="#ruumid" data-toggle="tab" href="room/rooms.php">Ruumid</a></li>
		<li><a id='haldus-tab-4' data-target="#klassifikaatorid" data-toggle="tab" href="type/typeinfo.php">Klassifikaatorid</a></li>
	</ul>

    <div class="tab-content">
        <div class="tab-pane" id="ruumi_lisamine"></div>
        <div class="tab-pane" id="kasutajad"></div>
        <div class="tab-pane" id="ruumid"></div>
        <div class="tab-pane" id="klassifikaatorid"></div>
    </div>  
</div>
<script>
"use strict";
$(document).ready(function() {
$("#myTabs").tab(); // initialize tabs
	$("#myTabs").bind("show", function(e) {
		var contentID = $(e.target).attr("data-target");
		var contentURL = $(e.target).attr("href");
		if (typeof(contentURL) != 'undefined') {
			// state: has a url to load from
			$(contentID).load(contentURL, function(){
				$("#myTabs").tab(); // reinitialize tabs
			});
		} else {
			//state: no url, to show static data
			$(contentID).tab('show');
			}
});
	$('#haldus-tab-3').tab("show"); // Load and display content for first tab
});
</script>
<?php endif ?>
<?php
require_once "include/Session.php";
$session = new Session();
?>
<ul class="nav" id="myNavBar">
	<!--<li><a href="teated.php" data-toggle="tab" data-target="#content">Teated</a></li>-->

<?php if (isset($session->user)): ?>
	<li><a  href="broneeringud.php" data-toggle="tab" data-target="#content"><i class="icon-list icon-white"></i> Broneeringud</a></li>
<?php endif ?> 

	<li><a id='cal' href="kalender.php" data-toggle="tab" data-target="#content"><i class="icon-calendar icon-white"></i> Kalender</a></li>

<?php if (isset($session->user) && ($session->user->usertype === 'peakasutaja')): ?>
	<li><a  href="haldus.php" data-toggle="tab" data-target="#content"><i class="icon-cog icon-white"></i> Haldusliides</a></li>
<?php endif ?>
</ul>

<ul class="nav pull-right">
<?php if (isset($session->user)): ?>
	<li>
	<a href="_logout.php"><i class="icon-off icon-white"></i> Logout (<?php echo $session->user->username?>)</a>
	</li>
<?php else: ?>
</ul>

<form method="post" action="_validate.php" class="navbar-form pull-right">
     <input type="text" name="username" class="input-medium" placeholder="Username">
     <input type="password" name="password" class="input-medium" placeholder="Password">
     <input type="submit" class="btn" value="Sign in">
</form>
<?php endif ?>

<script type="text/javascript">
/*
$(function() {
$("#myNavBar").tab(); // initialize tabs
	$("#myNavBar").bind("show", function(e) {
		var contentID = $(e.target).attr("data-target");
		var contentURL = $(e.target).attr("href");
		//console.log(contentID, contentURL);
		if (typeof(contentURL) != 'undefined') {
			// state: has a url to load from
			$(contentID).load(contentURL, function(){
				$("#myNavBar").tab(); // reinitialize tabs
			});
		} else {
			//state: no url, to show static data
			$(contentID).tab('show');
			}
});
	$('#myNavBar a:first').tab("show"); // Load and display content for first tab
});*/

//HACK BROWSERI BACK BUTTONI JAOKS (hash)
$(document).ready(function(){

// Load and display content for first tab
	$('#content').load('kalender.php');
	$('#cal').tab("show");

// part 1 : add a hash to the location when I load a sub-page.
		$('#myNavBar a').click(function(){
			location.hash=$(this).attr('href').match(/(^.*)\./)[1]
			$('#myNavBar li').removeClass("active");
			//$(this).addClass("active");
			$(this).tab("show");
			return false
		})
// part 2 : check the location.hash when the page is loaded.
		var originalTitle=document.title
		function hashChange(){
			var page=location.hash.slice(1);
			if (page!=""){
				$('a[href="' + page + '.php"]').tab("show");
				$('#content').load(page+".php");
				document.title=originalTitle+' – '+page
			}
		}
// part 3 : monitor for hash change events.
		if ("onhashchange" in window){ // cool browser
			$(window).on('hashchange',hashChange).trigger('hashchange')
		}else{ // lame browser
			var lastHash=''
			setInterval(function(){
				if (lastHash!=location.hash)
					hashChange()
				lastHash=location.hash
			},100)
		}
});
</script>
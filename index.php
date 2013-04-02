<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <!--[if lte IE 7]><script src="js/ie7/warning.js"></script><script>window.onload=function(){e("js/ie7/")}</script><![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title>Broneerimissüsteem</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' href='css/bootstrap.css' />
    <link rel='stylesheet' type='text/css' href="css/bootstrap-responsive.css">
    <link rel='stylesheet' type='text/css' href='css/fullcalendar.css' />
    <link rel='stylesheet' type='text/css' href="css/datetimepicker.css">
    <link rel='stylesheet' type='text/css' href="css/custom_styling.css">

    <!--Modali jaoks vajalik-->
    <script type='text/javascript' src="js/jquery-1.9.1.js"></script>
    <script type='text/javascript' src='js/bootstrap.js'></script>
    <script type='text/javascript' src='js/fullcalendar.js'></script>
    <!--<script type='text/javascript' src='js/jquery-ui-1.9.2.custom.min.js'></script>-->
    <script type='text/javascript' src='js/jquery-ui-1.10.2.custom.min.js'></script>
    <script type="text/javascript" src="js/jquery.validate.min.js" ></script>
    <script type="text/javascript" src="js/bootbox.min.js" ></script>
    <!-- <script type="text/javascript" src="js/index.js"></script>-->
    <script type='text/javascript' src='js/bootstrap-datetimepicker.min.js'></script>

</head>
<body>

<div class="navbar navbar-static-top navbar-inverse">
    <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
           <a class="brand" href="index.php"><img src="img/ati_logo.png"></a>
             <div class="nav-collapse collapse">
             	<?php require_once "include/links.php" ?>
            </div><!-- /.nav-collapse -->
        </div><!-- /.container-fluid -->
    </div> <!-- /.navbar-inner -->
</div> <!-- /.navbar -->
<div class="container-fluid"><!-- container -->
</br>
<div class="row-fluid">
<!-- Ülemine nav-bar, data-target asub include/links.php -->
<div class="tab-content">
        <div class="tab-pane" id="content"></div>
</div> 
<!-- Ülemine nav-bar -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-body"></div>
</div>

</div><!-- row-fluid -->
</div> <!-- container -->


</body>

</html>
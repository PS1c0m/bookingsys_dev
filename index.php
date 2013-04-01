<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <!--[if lte IE 7]><script src="js/ie7/warning.js"></script><script>window.onload=function(){e("js/ie7/")}</script><![endif]-->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title>Broneerimissüsteem</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel='stylesheet' type='text/css' href='css/bootstrap.css' />
    <link rel='stylesheet' type='text/css' href="css/bootstrap-responsive.css">
    <link rel='stylesheet' type='text/css' href='css/fullcalendar.css' />
    <link rel='stylesheet' type='text/css' href="css/datetimepicker.css">

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

    <script type="text/javascript">

    </script>
<style type="text/css">

body > .navbar {
font-size: 14px;
}
/*
body > .navbar .nav > li > a {
      padding: 10px 20px;
}
body > .navbar .brand {
      padding: 10px 20px;
}*/

@media (max-width: 480px) { 
    .nav-tabs > li {
        float:none;
    }
}
@media only screen and (max-width: 800px) {
    
    /* Force table to not be like tables anymore */
    #no-more-tables table, 
    #no-more-tables thead, 
    #no-more-tables tbody, 
    #no-more-tables th, 
    #no-more-tables td, 
    #no-more-tables tr { 
        display: block; 
    }
 
    /* Hide table headers (but not display: none;, for accessibility) */
    #no-more-tables thead tr { 
        position: absolute;
        top: -9999px;
        left: -9999px;
    }
 
    #no-more-tables tr { border: 1px solid #ccc; }
 
    #no-more-tables td { 
        /* Behave  like a "row" */
        border: none;
        border-bottom: 1px solid #eee; 
        position: relative;
        padding-left: 50%; 
        white-space: normal;
        text-align:left;
    }
 
    #no-more-tables td:before { 
        /* Now like a table header */
        position: absolute;
        /* Top/left values mimic padding */
        top: 6px;
        left: 6px;
        width: 45%; 
        padding-right: 10px; 
        white-space: nowrap;
        text-align:left;
        font-weight: bold;
    }
 
    /*
    Label the data
    */
    #no-more-tables td:before { content: attr(data-title); }
}
</style>
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
           <a class="brand" href="index.php"><img src="img/ati_logo_bw_kiri.png"></a>
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
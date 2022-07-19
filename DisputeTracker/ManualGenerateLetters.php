<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');



if ($accesslevel < 5){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}



//print "First";

//require('assets/includes/generateLetters.php');

//All this page does it set a session variable, redirect to the generate letters php file, then, because of the session variable, the generate letters file will redirect to viewGeneratedDocuments.php
//The session variable will be unset in viewGeneratedDocuments.php

include("assets/includes/loadingHTML.php");

print "<script>showLoading();</script>";

$_SESSION["TransactionAdded"] = 'Letter\'s have begun being made. Please give it a few minutes to be made and check the <a href="viewGeneratedDocuments.php">View Documents</a> page again.';

//var_dump($_SESSION);

header( "Location: generateLetters.php" );
exit();




?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - PC Letter</title>
	<!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
     <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
   <!-- Favicon -->
    <link rel="icon" type="image/x-icon" id="pageFavicon" href="./assets/img/favicon.ico" />
   <link href="assets/css/CalendarControl.css" rel="stylesheet" type="text/css">
   <script src="assets/js/jquery.betterTooltip.js" type="text/javascript"></script>
   <script src="assets/js/jquery.tools.min.js" type="text/javascript"></script>
   <script src="assets/js/CalendarControl.js" language="javascript"></script>
   <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
	<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
   
<?php

//This Variable is for the Title
$PageTitle = "Dispute Track - Generate Letters";

if($accesslevel >= 7){
	include("assets/includes/HTMLscript.php");
}


include("assets/includes/autologout.php");



?>


</head>
<body>
     
           
          
    <div id="wrapper">
        
		<?php include("assets/includes/head+menu.php"); ?>
        
        <div id="page-wrapper" >
            <div id="page-inner">
                
				<div class="row">
                    <div class="col-md-2">
                     <h2>Letters are Generating</h2> 
                    </div>
                </div> 
				
				<hr />
				
				<div class="row">
                    <div class="col-md-2">
                    
                    </div>
                </div> 
			  
              
                
    </div>
	
	
			<!--  Footer  -->
	<p></p>
	
	
             <!-- /. PAGE INNER  -->
            </div>
         <!-- /. PAGE WRAPPER  -->
        </div>
		
		
		<!--  Bottom of Menu Nav  -->
		
     <!-- /. WRAPPER  -->
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS -->
    <script src="assets/js/jquery-1.10.2.js"></script>
      <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="assets/js/jquery.metisMenu.js"></script>
      <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
    
   
</body>
</html>

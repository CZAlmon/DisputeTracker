<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');


if ($accesslevel < 5){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}


?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - New Dispute</title>
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
$PageTitle = "Dispute Track - New Dispute";

if($accesslevel >= 7){
	include("assets/includes/HTMLscript.php");
}


include("assets/includes/autologout.php");

include("assets/includes/loadingHTML.php");

?>
   
   
</head>
<body>
     
           
          
    <div id="wrapper">
	
        <?php include("assets/includes/head+menu.php"); ?>
		
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-2">
                     <h2>New Reversal Letter</h2>   
                    </div>
					<div class="col-md-10">
<?php
 //Session Error

if(isset($_SESSION["ADD_DISPUTE_ERROR"])){

	print "<h2><span style='color:red'>" . $_SESSION["ADD_DISPUTE_ERROR"] . "</span></h2>";
	
	unset($_SESSION["ADD_DISPUTE_ERROR"]);
	unset($_SESSION["TransactionAdded"]);
	
}
else if(isset($_SESSION["TransactionAdded"])){
	
	print "<h2><span style='color:blue'>" . $_SESSION["TransactionAdded"] . "</span></h2>";
	
	unset($_SESSION["TransactionAdded"]);
	
}

?>

					</div>
                </div>          

				<!--  Below H2, above HR  -->
				
                 <!-- /. ROW  -->
                  <hr />
				  
				  <!--  Main Body  -->

					<form id="newReversalLetter_id" autocomplete="off" name="newReversalLetter" method="post" action="GenerateReversalLetters.php" onsubmit="showLoading();">
					
					<input type="hidden" id="GenerateDay_id" name="GenerateDay" value="<?php date_default_timezone_set("America/Chicago"); print date("Y-m-d"); ?>">
					
					<!-- /. ROW  -->
					
						<div class="row">
							<div class="col-md-3">
								<label>Case Number</label>&nbsp;<a target="_blank" title="Case Number must be Digits only"><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="CaseNumber" maxlength="255" id="CaseNumber_id" pattern="^[0-9]+$" title="Numbers only." required>
								
							</div>
						</div>
						
						<hr />
						<!-- 
						<div class="row">
						
						</div>
						<hr />
						-->
						
							
						<input type="submit" class="btn btn-success" name="newreversalletterSubmit" value="Next">  <!--  onclick="validate();" -->

					
					</form>
					
					
	


	
             <!-- /. PAGE INNER  -->
            </div>
				<!--  Footer  -->
			<p></p>
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

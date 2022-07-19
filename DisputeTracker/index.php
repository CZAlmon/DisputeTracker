<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');



if ($accesslevel < 1){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: login.php" );
	exit();
}




// Get all cases from user and in descending order ---------------------------------------------
$case_select = "SELECT checkcases.id, checkcases.casestartdate, checkcases.custfname, checkcases.custlname, checkcases.custphone, checkcases.custemail, checkcases.casedeleted FROM checkcases WHERE checkcases.userstarted = '".$username."' AND checkcases.casedeleted = '0' ORDER BY checkcases.id DESC";
$case_fetch = $dtcon->query($case_select);

$case_items = $case_fetch->fetch_all();
// ---------------------------------------------------------------------------------------------

$NumberofCases = count($case_items);

$AllDisputesArray = array();

//Get Case Items
for ($i=0; $i < $NumberofCases; $i++){
	
	if($case_items[$i][6] == 0){
		$temparr_1 = array();
		$caseIDtemp = $case_items[$i][0];	
		$caseDatetmp = $case_items[$i][1];
		$caseFNametmp = $case_items[$i][2];
		$caseLNametmp = $case_items[$i][3];
		$casePhonetmp = $case_items[$i][4];
		$caseEmailtmp = $case_items[$i][5];

		
		array_push($temparr_1, $caseIDtemp);
		array_push($temparr_1, $caseDatetmp);
		array_push($temparr_1, $caseFNametmp);
		array_push($temparr_1, $caseLNametmp);
		array_push($temparr_1, $casePhonetmp);
		array_push($temparr_1, $caseEmailtmp);
		
		array_push($AllDisputesArray, $temparr_1);
	}
	
	
}

//var_dump($AllDisputesArray);

$DisputeCount = count($AllDisputesArray);


//var_dump($_SESSION);


?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - Index</title>
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
   <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
	<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
   
<?php

//This Variable is for the Title
$PageTitle = "Dispute Track - Home";

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
                     <h2>Home</h2> 
                    </div>
					<div class="col-md-10">
<?php
//Session Error
if(isset($_SESSION["ADD_DISPUTE_ERROR"])){

	print "<h2><span style='color:red'>" . $_SESSION["ADD_DISPUTE_ERROR"] . "</span></h2>";
	
	unset($_SESSION["ADD_DISPUTE_ERROR"]);
	unset($_SESSION["TransactionAdded"]);
	
}
?>

					</div>
                </div>           

				<!--  Below H2, above HR  -->
				<hr />
				
				<div class="row">
					
					<div class="col-md-2">
						<label>Add a New dispute:</label>
						<a href="newDispute.php" class="btn btn-success form-control">New Dispute</a>
					</div>
					
<?php

//Check to Edit/Finish Old dispute
$unFinishedCases = "SELECT id FROM checkcases WHERE userstarted='".$username."' AND casedoneinput=FALSE AND casedeleted <> TRUE";
$UnfinishedFetch = $dtcon->query($unFinishedCases);

$unfinishedItems = $UnfinishedFetch->fetch_all();

$numofItems = count($unfinishedItems);

//Check to see if Items are still Open.
if($numofItems == 0){
	//No Cases Open, Do nothing
}
else if($numofItems == 1){
	//There is a case still open
	print "<div class='col-md-3'><label><span style='color:red'>You have a case still open, please finish it.</span></label>";
	//print "<script language='javascript'>alert('You have a case still open, please finish it.')</script>";
	print "<a href='newDispute.php' class='btn btn-warning form-control'>Finish Dispute Case</a></div>";
	
}
else{
	//If Multiple cases are open they must contact System Admin to fix. This Should not Happen.
	print "<h1><span style='color:red'>You have Multiple Cases still open! Please contact the System Admin Immediately!</span></h1>";
	//print "<script language='javascript'>alert('You have Multiple Cases still open! Please contact the System Admin Immediately!')</script>";

}

?>
				
				</div>

                 <!-- /. ROW  -->
                  <hr />
				  
				  <div class="row">
                    <div class="col-md-5 col-sm-4 col-xs-6">
                        <h5>Disputes:</h5>
                        <div class="panel panel-primary text-center no-boder bg-color-blue">
                            <div class="panel-body">
								<i class="fa fa-desktop fa-5x"></i>
<?php

//$username
//$extensionVar = end((array_values($pathparts)));
//validatePostedDate($TransactionDate, $PostedDate)

$DisputesOpen = 0;
$LastDispute = "";

$caseStartDateArray = array();

$caseStart_query = "SELECT casestartdate FROM checkcases WHERE userstarted='".$username."' AND caseclosed <> TRUE AND casedeleted <> TRUE";
$caseStart_query_data = $dtcon->query($caseStart_query);
$caseStartDateArray = $caseStart_query_data->fetch_all();
$caseStart_numbers = count($caseStartDateArray);


if($caseStart_numbers <= 0){
	$DisputesOpen = 0;
	$LastDispute = "";
}
else{
	$DisputesOpen = $caseStart_numbers;
	$LastDispute = end((array_values($caseStartDateArray)));		//end gets last element, if array_values wasn't in the line the Array would have to be reset because of end(). So do end(array_values()) to avoid having that issue.
}


//var_dump($caseStartDateArray);
//var_dump($LastDispute);





print "<pre>\nDisputes Currently Open:\t\t\t  ".$DisputesOpen."\n"; 
print "Last Dispute Opened:\t\t".$LastDispute[0]."</pre>";







?>
                                <!--<pre>
Disputes currently open:		3
Last Dispute opened:		2017-01-05</pre> -->
                            </div>
                        </div>
                    </div>
                
                </div>
				
				<hr />
				




				<div class='row' id='ViewALLTable'>
					<div class='col-md-12'>
						<label>Your last 5 Disputes:</label>
						<table class='order-table table table-striped table-bordered table-hover'>
							<thead>
								<tr>
								<th>Case ID</th>
								<th>Case Date</th>
								<th>Customer Name</th>
								<th>Customer Phone</th>
								<th>Customer Email</th>
								</tr>
							</thead>
							<tbody id='tableBODY'>
<?php
//Only show first 5, which since it is in descending order, will be the Last 5 items
$LoopCount = 1;

for($i=0; $i < $DisputeCount; $i++){
	
	print "<tr>";
	print "<td>".$AllDisputesArray[$i][0]."</td>";
	print "<td>".$AllDisputesArray[$i][1]."</td>";
	print "<td>".$AllDisputesArray[$i][2]." ".$AllDisputesArray[$i][3]."</td>";
	print "<td>".$AllDisputesArray[$i][4]."</td>";
	print "<td>".$AllDisputesArray[$i][5]."</td>";
	print "</tr>";
	
	if($LoopCount == 5){
		break;
	}
	
	$LoopCount++;
	
}

?>
				
							</tbody>
						</table>
					</div>
				</div>
				
				
				
				
				
				
				

				  <!-- Make Landing for Back End -->
				  
				
				  
				  
				  <!-- Make landing for Front End -->
				  
				  
				  
				  
              
                 <!-- /. ROW  -->           
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
	
<?php


//Check to see if Items are still Open.
if($numofItems == 0){
	//No Cases Open, Do nothing
}
else if($numofItems == 1){
	//There is a case still open
	print "<script language='javascript'>alert('You have a case still open, please finish it.')</script>";
	
}
else{
	//If Multiple cases are open they must contact System Admin to fix. This Should not Happen.
	print "<script language='javascript'>alert('You have Multiple Cases still open! Please contact the System Admin Immediately!')</script>";

}

?>
   
</body>
</html>

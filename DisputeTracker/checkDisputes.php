<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');



if ($accesslevel < 5){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}




// ---------------------------------------------------------------------------------------------
$case_select = "SELECT checkcases.id, checkcases.casestartdate, checkcases.custfname, checkcases.custlname, checkcases.custphone, checkaccountnumbers.accountnumber, checkcardnumbers.id, checkcardnumbers.cardnumber, checkcases.caseclosed, checkcases.casedeleted, checkaccountnumbers.accoountnew FROM checkcases, checkaccountnumbers, checkcardnumbers WHERE checkcases.id = checkaccountnumbers.caseid AND checkcases.id = checkcardnumbers.caseid AND checkcases.casedeleted = '0' ORDER BY checkcases.id";
$case_fetch = $dtcon->query($case_select);

$case_items = $case_fetch->fetch_all();
// ---------------------------------------------------------------------------------------------
$transaction_select = "SELECT caseid, cardid, amount, transactiondate, description, procreditgiven, transactiondeleted FROM checktransactions WHERE transactiondeleted = '0' ORDER BY caseid";
$transaction_fetch = $dtcon->query($transaction_select);

$transaction_items = $transaction_fetch->fetch_all();
// ---------------------------------------------------------------------------------------------

$NumberofCases = count($case_items);
$NumberofTransactions = count($transaction_items);

$AllDisputesArray = array();


for ($i=0; $i < $NumberofCases; $i++){
	
	if($case_items[$i][9] == 0){
		$temparr_1 = array();
		$caseIDtemp = $case_items[$i][0];	
		$caseDatetmp = $case_items[$i][1];
		$caseFNametmp = $case_items[$i][2];
		$caseLNametmp = $case_items[$i][3];
		$casePhonetmp = $case_items[$i][4];
		$checkAcctmp = $case_items[$i][5];
		$checkCardIDtmp = $case_items[$i][6];
		$checkCardtmp = $case_items[$i][7];
		$CASEclosedTmp = $case_items[$i][8];
		$AccountAge = $case_items[$i][10];
		
		array_push($temparr_1, $caseIDtemp);
		array_push($temparr_1, $caseDatetmp);
		array_push($temparr_1, $caseFNametmp);
		array_push($temparr_1, $caseLNametmp);
		array_push($temparr_1, $casePhonetmp);
		array_push($temparr_1, $checkAcctmp);
		array_push($temparr_1, $checkCardIDtmp);
		array_push($temparr_1, $checkCardtmp);
		array_push($temparr_1, $CASEclosedTmp);
		array_push($temparr_1, $AccountAge);
		
		
		//$transactionTmp = array();
		
		for($m=0; $m < $NumberofTransactions; $m++){
			if($transaction_items[$i][9] == 0){
				if($caseIDtemp == $transaction_items[$m][0]){
					array_push($temparr_1, $transaction_items[$m]);
				}
			}
		}
		
		//array_push($temparr_1, $transactionTmp);
		
		array_push($AllDisputesArray, $temparr_1);
	}
}

//var_dump($AllDisputesArray);

$DisputeCount = count($AllDisputesArray);

//exit();


?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - Check Disputes</title>
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
$PageTitle = "Dispute Track - Check Disputes";

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
                     <h2>Check Disputes:</h2> 
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
					<div class='col-md-12'>
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOverdue" class="collapsed"><font color="red">Overdue Disputes:</font></a>
                                    </h4>
                                </div>
                                <div id="collapseOverdue" class="panel-collapse collapse" style="height: 0px;">
                                    <div class="panel-body">
                                        
				<div class='row' id='ViewALLTable'>
					<div class='col-md-12'>
						<table class='order-table table table-striped table-bordered table-hover'>
							<thead>
								<tr>
									<th style='width:5%;'>Case ID</th>
									<th style='width:10%;'>Case Start Date</th>
									<th style='width:8%;'>Days Overdue</th>
									<th style='width:9%;'>Customer Name</th>
									<th style='width:9%;'>Customer Phone</th>
									<th style='width:9%;'>Account Number</th>
									<th style='width:10%;'>Card Number</th>
									<th style='width:15%;'><div style='float:left; width:50%; text-align:left'>Transaction Date</div><div style='float:right; width:50%; text-align:right'>Amount</div></th>
									<th style='width:17%;'>Dispute Description</th>
									<th style='width:4%;'>View</th>
									<th style='width:4%;'>Edit</th>
								</tr>
							</thead>
							<tbody id='tableBODY'>
<?php

$day45time = 3888000;
$day90time = 7776000;

$day35time = 3024000;
$day80time = 6912000;

$day20time = 1728000;
$day10time = 864000;

for($i=0; $i < $DisputeCount; $i++){
	
	if($AllDisputesArray[$i][8] == '0'){			//If case is not closed
		
		$tmpDate = $AllDisputesArray[$i][1];
		$tmpExplode = explode(",", $tmpDate);
		
		$timeStartedDate = $tmpExplode[0] . " GMT";
		
		date_default_timezone_set("America/Chicago");
		$timeStarted = $_SESSION["time_started"];
		$timeNow = date("Y-m-d");
		$timeNow = $timeNow . " GMT";
		
		$Temp_Diff = strtotime($timeNow) - strtotime($timeStartedDate);
		
		/*
			Reason for '. "GMT"' on the top two times: 
				What I am trying to do is convert 2 dates (dates only: YYYY-MM-DD) to seconds.
				I need to get rid of all hours, and only treat them as complete dates.
				
				For some reason, strtotime will convert $timeNow to 5AM Always.
				
				Usually, strtotime will convert $timeStartedDate to 5AM but SOMETIMES for some reason it will convert dates (YYYY-MM-DD) to 6AM. I can't figure out why. So I add the GMT so then both will ALWAYS be converted to 12AM.

				So the GMT is needed, even if it looks weird.
		*/
		
		$TempAccountAge = $AllDisputesArray[$i][9];
		
		$AgetoUse = $day90time;
		
		if($TempAccountAge == "0"){
			$AgetoUse = $day45time;
		}
		
		//var_dump(strtotime($timeNow));
		//var_dump(strtotime($timeStartedDate));
		//var_dump($Temp_Diff);
		
		if($Temp_Diff > $AgetoUse){
			
			if($TempAccountAge == "0"){ //User inactive = False
				print '<tr>';
			}
			else{
				print '<tr class="info">';
			}
			
			print "<td>".$AllDisputesArray[$i][0]."</td>";
			print "<td>".$AllDisputesArray[$i][1]."</td>";
			print "<td>".convert_seconds($Temp_Diff-$AgetoUse)."</td>";
			print "<td>".$AllDisputesArray[$i][2]." ".$AllDisputesArray[$i][3]."</td>";
			print "<td>".$AllDisputesArray[$i][4]."</td>";
			print "<td>".$AllDisputesArray[$i][5]."</td>";
			print "<td>".$AllDisputesArray[$i][7]."</td>";
			
			$tmpNum = count($AllDisputesArray[$i]);
			
			print "<td>";
			for($m=10; $m < $tmpNum; $m++){
				print "<div style='float:left; width:50%; text-align:left'>";
				print $AllDisputesArray[$i][$m][3];
				print "</div>";
				
				print "<div style='float:right; width:50%; text-align:right'>";
				print $AllDisputesArray[$i][$m][2];
				print "</div>";
			}
			print "</td>";
			
			print "<td>";
			for($m=10; $m < $tmpNum; $m++){
				print "<div style='float:left; width:100%; text-align:left'>";
				print $AllDisputesArray[$i][$m][4];
				print "</div>";
			}
			print "</td>";
			
			print "<td><form method='post' action='DisputeViewOnly.php'><button type='submit' name='ViewSubmit' value='".$AllDisputesArray[$i][0]."' class='fa fa-external-link-square btn btn-primary'></button></form></td>";
			
			print "<td><form method='post' action='editDispute.php'><button type='submit' name='EditSubmit' value='".$AllDisputesArray[$i][0]."' class='fa fa-edit btn btn-primary'></button></form></td>";
			
			print "</tr>";
			
		}
	}
}


?>
				
							</tbody>
						</table>
					</div>
				</div>
									
									</div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
				
				<hr />
				
				<div class="row">
					<div class='col-md-12'>
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseExpiring" class="collapsed"><font color="blue">Expiring Disputes (Within 10 Calendar Days):</font></a>
                                    </h4>
                                </div>
                                <div id="collapseExpiring" class="panel-collapse collapse" style="height: 0px;">
                                    <div class="panel-body">
				
				<div class='row' id='ViewALLTable'>
					<div class='col-md-12'>
						<table class='order-table table table-striped table-bordered table-hover'>
							<thead>
								<tr>
									<th style='width:5%;'>Case ID</th>
									<th style='width:10%;'>Case Start Date</th>
									<th style='width:8%;'>Days Left</th>
									<th style='width:9%;'>Customer Name</th>
									<th style='width:9%;'>Customer Phone</th>
									<th style='width:9%;'>Account Number</th>
									<th style='width:10%;'>Card Number</th>
									<th style='width:15%;'><div style='float:left; width:50%; text-align:left'>Transaction Date</div><div style='float:right; width:50%; text-align:right'>Amount</div></th>
									<th style='width:17%;'>Dispute Description</th>
									<th style='width:4%;'>View</th>
									<th style='width:5%;'>Edit</th>
								</tr>
							</thead>
							<tbody id='tableBODY'>
<?php

for($i=0; $i < $DisputeCount; $i++){
	
	if($AllDisputesArray[$i][8] == '0'){
		
		$tmpDate = $AllDisputesArray[$i][1];
		$tmpExplode = explode(",", $tmpDate);
		
		$timeStartedDate = $tmpExplode[0] . " GMT";
		
		date_default_timezone_set("America/Chicago");
		$timeStarted = $_SESSION["time_started"];
		$timeNow = date("Y-m-d");
		$timeNow = $timeNow . " GMT";
		
		$Temp_Diff = strtotime($timeNow) - strtotime($timeStartedDate);
		
		/*
			Reason for '. "GMT"' on the top two times: 
				What I am trying to do is convert 2 dates (dates only: YYYY-MM-DD) to seconds.
				I need to get rid of all hours, and only treat them as complete dates.
				
				For some reason, strtotime will convert $timeNow to 5AM Always.
				
				Usually, strtotime will convert $timeStartedDate to 5AM but SOMETIMES for some reason it will convert dates (YYYY-MM-DD) to 6AM. I can't figure out why. So I add the GMT so then both will ALWAYS be converted to 12AM.

				So the GMT is needed, even if it looks weird.
		*/
		
		$TempAccountAge = $AllDisputesArray[$i][9];
		
		$FIRSTAgetoUse = $day90time;
		$SECONAgetoUse = $day80time;
		
		if($TempAccountAge == "0"){
			$FIRSTAgetoUse = $day45time;
			$SECONAgetoUse = $day35time;
		}
		
		
		//var_dump(strtotime($timeNow));
		//var_dump(strtotime($timeStartedDate));
		//var_dump($Temp_Diff);
		
		if($Temp_Diff > $SECONAgetoUse && $Temp_Diff < $FIRSTAgetoUse){
			
			if($TempAccountAge == "0"){ //User inactive = False
				print '<tr>';
			}
			else{
				print '<tr class="info">';
			}
			
			print "<td>".$AllDisputesArray[$i][0]."</td>";
			print "<td>".$AllDisputesArray[$i][1]."</td>";
			print "<td>".convert_seconds($Temp_Diff-$FIRSTAgetoUse)."</td>";
			print "<td>".$AllDisputesArray[$i][2]." ".$AllDisputesArray[$i][3]."</td>";
			print "<td>".$AllDisputesArray[$i][4]."</td>";
			print "<td>".$AllDisputesArray[$i][5]."</td>";
			print "<td>".$AllDisputesArray[$i][7]."</td>";
			
			$tmpNum = count($AllDisputesArray[$i]);
			
			print "<td>";
			for($m=10; $m < $tmpNum; $m++){
				print "<div style='float:left; width:50%; text-align:left'>";
				print $AllDisputesArray[$i][$m][3];
				print "</div>";
				
				print "<div style='float:right; width:50%; text-align:right'>";
				print $AllDisputesArray[$i][$m][2];
				print "</div>";
			}
			print "</td>";
			
			print "<td>";
			for($m=10; $m < $tmpNum; $m++){
				print "<div style='float:left; width:100%; text-align:left'>";
				print $AllDisputesArray[$i][$m][4];
				print "</div>";
			}
			print "</td>";
			
			print "<td><form method='post' action='DisputeViewOnly.php'><button type='submit' name='ViewSubmit' value='".$AllDisputesArray[$i][0]."' class='fa fa-external-link-square btn btn-primary'></button></form></td>";
			
			print "<td><form method='post' action='editDispute.php'><button type='submit' name='EditSubmit' value='".$AllDisputesArray[$i][0]."' class='fa fa-edit btn btn-primary'></button></form></td>";
			
			print "</tr>";
			
		}
	}
}


?>
				
							</tbody>
						</table>
					</div>
				</div>
				
									</div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
				
				<hr />
				
				<div class="row">
					<div class='col-md-12'>
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapsePC" class="collapsed"><font color="black">Cases that Provisional Credit have not been made:</font></a>
                                    </h4>
                                </div>
                                <div id="collapsePC" class="panel-collapse collapse" style="height: 0px;">
                                    <div class="panel-body">
				
				<div class='row' id='ViewALLTable'>
					<div class='col-md-12'>
						<table class='order-table table table-striped table-bordered table-hover'>
							<thead>
								<tr>
									<th style='width:5%;'>Case ID</th>
									<th style='width:10%;'>Case Start Date</th>
									<th style='width:8%;'>Days Left</th>
									<th style='width:9%;'>Customer Name</th>
									<th style='width:9%;'>Customer Phone</th>
									<th style='width:9%;'>Account Number</th>
									<th style='width:10%;'>Card Number</th>
									<th style='width:15%;'><div style='float:left; width:50%; text-align:left'>Transaction Date</div><div style='float:right; width:50%; text-align:right'>Amount</div></th>
									<th style='width:17%;'>Dispute Description</th>
									<th style='width:4%;'>View</th>
									<th style='width:5%;'>Edit</th>
								</tr>
							</thead>
							<tbody id='tableBODY'>
<?php

//var_dump($DisputeCount);
//var_dump($AllDisputesArray);


for($i=0; $i < $DisputeCount; $i++){
	
	
	if($AllDisputesArray[$i][8] == '0'){
		
		$PCLetterSent = TRUE;
		
		$tmpNum = count($AllDisputesArray[$i]);
		
		for($m=10; $m < $tmpNum; $m++){
			
			if($AllDisputesArray[$i][$m][5] == ""){
				
				//var_dump($AllDisputesArray[$i]);
				//var_dump($m);
				//var_dump($AllDisputesArray[$i][$m][6]);
				
				$PCLetterSent = FALSE;
			}
		}
		
		$tmpDate = $AllDisputesArray[$i][1];
		$tmpExplode = explode(",", $tmpDate);
		
		$timeStartedDate = $tmpExplode[0] . " GMT";
		
		date_default_timezone_set("America/Chicago");
		$timeStarted = $_SESSION["time_started"];
		$timeNow = date("Y-m-d");
		$timeNow = $timeNow . " GMT";
		
		$Temp_Diff = strtotime($timeNow) - strtotime($timeStartedDate);
		
		/*
			Reason for '. "GMT"' on the top two times: 
				What I am trying to do is convert 2 dates (dates only: YYYY-MM-DD) to seconds.
				I need to get rid of all hours, and only treat them as complete dates.
				
				For some reason, strtotime will convert $timeNow to 5AM Always.
				
				Usually, strtotime will convert $timeStartedDate to 5AM but SOMETIMES for some reason it will convert dates (YYYY-MM-DD) to 6AM. I can't figure out why. So I add the GMT so then both will ALWAYS be converted to 12AM.

				So the GMT is needed, even if it looks weird.
		*/
		
		$TempAccountAge = $AllDisputesArray[$i][9];
		
		$AgetoUse = $day20time;
		
		if($TempAccountAge == "0"){
			$AgetoUse = $day10time;
		}
		
		
		
		if(!$PCLetterSent){
			
			if($TempAccountAge == "0"){ //User inactive = False
				print '<tr>';
			}
			else{
				print '<tr class="info">';
			}
			
			print "<td>".$AllDisputesArray[$i][0]."</td>";
			print "<td>".$AllDisputesArray[$i][1]."</td>";
			
			if($Temp_Diff-$AgetoUse > 0){
				print "<td class='danger'>".convert_seconds($Temp_Diff-$AgetoUse)." Overdue!!</td>";
			}
			else{
				print "<td>".convert_seconds($Temp_Diff-$AgetoUse)."</td>";
			}
			
			
			print "<td>".$AllDisputesArray[$i][2]." ".$AllDisputesArray[$i][3]."</td>";
			print "<td>".$AllDisputesArray[$i][4]."</td>";
			print "<td>".$AllDisputesArray[$i][5]."</td>";
			print "<td>".$AllDisputesArray[$i][7]."</td>";
			
			$tmpNum = count($AllDisputesArray[$i]);
			
			print "<td>";
			for($m=9; $m < $tmpNum; $m++){
				print "<div style='float:left; width:50%; text-align:left'>";
				print $AllDisputesArray[$i][$m][3];
				print "</div>";
				
				print "<div style='float:right; width:50%; text-align:right'>";
				print $AllDisputesArray[$i][$m][2];
				print "</div>";
			}
			print "</td>";
			
			print "<td>";
			for($m=9; $m < $tmpNum; $m++){
				print "<div style='float:left; width:100%; text-align:left'>";
				print $AllDisputesArray[$i][$m][4];
				print "</div>";
			}
			print "</td>";
			
			print "<td><form method='post' action='DisputeViewOnly.php'><button type='submit' name='ViewSubmit' value='".$AllDisputesArray[$i][0]."' class='fa fa-external-link-square btn btn-primary'></button></form></td>";
			
			print "<td><form method='post' action='editDispute.php'><button type='submit' name='EditSubmit' value='".$AllDisputesArray[$i][0]."' class='fa fa-edit btn btn-primary'></button></form></td>";
			
			print "</tr>";
			
		}
	}
}

?>
				
							</tbody>
						</table>
					</div>
				</div>
				
				
				
									</div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
				
				<hr />
				
				<div class="row">
					<div class='col-md-12'>
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseAll" class="collapsed"><font color="black">All Opened Cases:</font></a>
                                    </h4>
                                </div>
                                <div id="collapseAll" class="panel-collapse collapse" style="height: 0px;">
                                    <div class="panel-body">
				
				<div class='row' id='ViewALLTable'>
					<div class='col-md-12'>
						<table class='order-table table table-striped table-bordered table-hover'>
							<thead>
								<tr>
									<th style='width:5%;'>Case ID</th>
									<th style='width:10%;'>Case Start Date</th>
									<th style='width:8%;'>Days Left</th>
									<th style='width:9%;'>Customer Name</th>
									<th style='width:9%;'>Customer Phone</th>
									<th style='width:9%;'>Account Number</th>
									<th style='width:10%;'>Card Number</th>
									<th style='width:15%;'><div style='float:left; width:50%; text-align:left'>Transaction Date</div><div style='float:right; width:50%; text-align:right'>Amount</div></th>
									<th style='width:17%;'>Dispute Description</th>
									<th style='width:4%;'>View</th>
									<th style='width:5%;'>Edit</th>
								</tr>
							</thead>
							<tbody id='tableBODY'>
<?php

for($i=0; $i < $DisputeCount; $i++){
	
	if($AllDisputesArray[$i][8] == '0'){
		
		$tmpDate = $AllDisputesArray[$i][1];
		$tmpExplode = explode(",", $tmpDate);
		
		$timeStartedDate = $tmpExplode[0] . " GMT";
		
		date_default_timezone_set("America/Chicago");
		$timeStarted = $_SESSION["time_started"];
		$timeNow = date("Y-m-d");
		$timeNow = $timeNow . " GMT";
		
		$Temp_Diff = strtotime($timeNow) - strtotime($timeStartedDate);
		
		/*
			Reason for '. "GMT"' on the top two times: 
				What I am trying to do is convert 2 dates (dates only: YYYY-MM-DD) to seconds.
				I need to get rid of all hours, and only treat them as complete dates.
				
				For some reason, strtotime will convert $timeNow to 5AM Always.
				
				Usually, strtotime will convert $timeStartedDate to 5AM but SOMETIMES for some reason it will convert dates (YYYY-MM-DD) to 6AM. I can't figure out why. So I add the GMT so then both will ALWAYS be converted to 12AM.

				So the GMT is needed, even if it looks weird.
		*/
		
		//print "<br><br>\n";
		//echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br />';
		//var_dump(strtotime($timeNow));
		//var_dump(strtotime($timeStartedDate));
		//var_dump($timeNow);
		//var_dump($timeStartedDate);
		//var_dump($Temp_Diff);
		//print "<br><br>\n";
		
		$TempAccountAge = $AllDisputesArray[$i][9];
		
		$FIRSTAgetoUse = $day90time;
		$SECONAgetoUse = $day80time;
		
		if($TempAccountAge == "0"){
			$FIRSTAgetoUse = $day45time;
			$SECONAgetoUse = $day35time;
		}
		
		//var_dump(strtotime($timeNow));
		//var_dump(strtotime($timeStartedDate));
		//var_dump($Temp_Diff);
	
		if($TempAccountAge == "0"){ //User inactive = False
			print '<tr>';
		}
		else{
			print '<tr class="info">';
		}
		
		print "<td>".$AllDisputesArray[$i][0]."</td>";
		print "<td>".$AllDisputesArray[$i][1]."</td>";
		
		//print $Temp_Diff-$FIRSTAgetoUse;
		
		if($Temp_Diff-$FIRSTAgetoUse > 0){
			print "<td class='danger'>".convert_seconds($Temp_Diff-$FIRSTAgetoUse)." Overdue!!</td>";
		}
		else{
			print "<td>".convert_seconds($Temp_Diff-$FIRSTAgetoUse)."</td>";
		}
		
		print "<td>".$AllDisputesArray[$i][2]." ".$AllDisputesArray[$i][3]."</td>";
		print "<td>".$AllDisputesArray[$i][4]."</td>";
		print "<td>".$AllDisputesArray[$i][5]."</td>";
		print "<td>".$AllDisputesArray[$i][7]."</td>";
		
		$tmpNum = count($AllDisputesArray[$i]);
		
		print "<td>";
		for($m=9; $m < $tmpNum; $m++){
			print "<div style='float:left; width:50%; text-align:left'>";
			print $AllDisputesArray[$i][$m][3];
			print "</div>";
			
			print "<div style='float:right; width:50%; text-align:right'>";
			print $AllDisputesArray[$i][$m][2];
			print "</div>";
		}
		print "</td>";
		
		print "<td>";
		for($m=9; $m < $tmpNum; $m++){
			print "<div style='float:left; width:100%; text-align:left'>";
			print $AllDisputesArray[$i][$m][4];
			print "</div>";
		}
		print "</td>";
		
		print "<td><form method='post' action='DisputeViewOnly.php'><button type='submit' name='ViewSubmit' value='".$AllDisputesArray[$i][0]."' class='fa fa-external-link-square btn btn-primary'></button></form></td>";
		
		print "<td><form method='post' action='editDispute.php'><button type='submit' name='EditSubmit' value='".$AllDisputesArray[$i][0]."' class='fa fa-edit btn btn-primary'></button></form></td>";
		
		print "</tr>";
		
	
	}
}


?>
				
							</tbody>
						</table>
					</div>
				</div>
				
									</div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
				
				<hr />
				
				
                 <!-- /. ROW  -->
                 
				  
              
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
    
   
</body>
</html>

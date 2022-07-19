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
$case_select = "SELECT checkcases.id, checkcases.casestartdate, checkcases.custfname, checkcases.custlname, checkcases.custphone, checkaccountnumbers.accountnumber, checkcardnumbers.id, checkcardnumbers.cardnumber, checkcases.casedeleted, checkcases.casedoneinput FROM checkcases, checkaccountnumbers, checkcardnumbers WHERE checkcases.id = checkaccountnumbers.caseid AND checkcases.id = checkcardnumbers.caseid AND checkcases.casedeleted = '0' ORDER BY checkcases.id";
$case_fetch = $dtcon->query($case_select);

$case_items = $case_fetch->fetch_all();
// ---------------------------------------------------------------------------------------------
$transaction_select = "SELECT caseid, cardid, amount, transactiondate, transactiondeleted FROM checktransactions WHERE transactiondeleted = '0' ORDER BY caseid";
$transaction_fetch = $dtcon->query($transaction_select);

$transaction_items = $transaction_fetch->fetch_all();
// ---------------------------------------------------------------------------------------------


//var_dump($case_items);

$NumberofCases = count($case_items);
$NumberofTransactions = count($transaction_items);

$AllDisputesArray = array();


for ($i=0; $i < $NumberofCases; $i++){
	
	if($case_items[$i][8] == 0){
		$temparr_1 = array();
		$caseIDtemp = $case_items[$i][0];	
		$caseDatetmp = $case_items[$i][1];
		$caseFNametmp = $case_items[$i][2];
		$caseLNametmp = $case_items[$i][3];
		$casePhonetmp = $case_items[$i][4];
		$checkAcctmp = $case_items[$i][5];
		$checkCardIDtmp = $case_items[$i][6];
		$checkCardtmp = $case_items[$i][7];
		$checkCaseDone = $case_items[$i][9];
		
		array_push($temparr_1, $caseIDtemp);
		array_push($temparr_1, $caseDatetmp);
		array_push($temparr_1, $caseFNametmp);
		array_push($temparr_1, $caseLNametmp);
		array_push($temparr_1, $casePhonetmp);
		array_push($temparr_1, $checkAcctmp);
		array_push($temparr_1, $checkCardIDtmp);
		array_push($temparr_1, $checkCardtmp);
		array_push($temparr_1, $checkCaseDone);
		
		//$transactionTmp = array();
		
		for($m=0; $m < $NumberofTransactions; $m++){
			if($transaction_items[$m][4] == 0){
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




// =========================================================================================================

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	
	if(isset($_POST["SubmitSearch"])){
		
		$newTableArray = array();
		
		$caseID_SEARCH = "";
		$name_SEARCH = "";
		$phone_SEARCH = "";
		$accNum_SEARCH = "";
		$cardNum_SEARCH = "";
		$StartDate_SEARCH = "";
		$EndDate_SEARCH = "";
		
		//$_SESSION["ADD_DISPUTE_ERROR"]
		
		if(isset($_POST["SearchCaseID"])){
			$caseID_SEARCH = $_POST["SearchCaseID"];
		}
		if(isset($_POST["SearchName"])){
			$name_SEARCH = $_POST["SearchName"];
		}
		if(isset($_POST["SearchPhone"])){
			$phone_SEARCH = $_POST["SearchPhone"];
		}
		if(isset($_POST["SearchAccNum"])){
			$accNum_SEARCH = $_POST["SearchAccNum"];
		}
		if(isset($_POST["SearchCardNum"])){
			$cardNum_SEARCH = $_POST["SearchCardNum"];
		}
		if(isset($_POST["SearchSDate"])){
			$StartDate_SEARCH = $_POST["SearchSDate"];
		}
		if(isset($_POST["SearchEDate"])){
			$EndDate_SEARCH = $_POST["SearchEDate"];
		}

		
		if($caseID_SEARCH == ""){
			if($name_SEARCH == ""){
				if($phone_SEARCH == ""){
					if($accNum_SEARCH == ""){
						if($cardNum_SEARCH == ""){
							if($StartDate_SEARCH == ""){
								if($EndDate_SEARCH == ""){
									$_SESSION["ADD_DISPUTE_ERROR"] = 'To Search, you must input some criteria.';
									header( "Location: viewDisputes.php" );
									exit();
								}
								else{	//$EndDate contains info, At this point StartDate would not contain info
									$_SESSION["ADD_DISPUTE_ERROR"] = 'To Search by Date, you must input both Dates in the correct fields.';
									header( "Location: viewDisputes.php" );
									exit();
								}
							}
							else{	//$StartDate contains info
								
								if($EndDate_SEARCH == ""){
									$_SESSION["ADD_DISPUTE_ERROR"] = 'To Search by Date, you must input both Dates in the correct fields.';
									header( "Location: viewDisputes.php" );
									exit();
								}
								
								$StartdateGood = validateDate($StartDate_SEARCH, 'Y-m-d');
								$StartdateGoodVAR = validatePastDate($StartDate_SEARCH);
								
								$EnddateGood = validateDate($EndDate_SEARCH, 'Y-m-d');
								$EnddateGoodVAR = validatePastDate($EndDate_SEARCH);
								
								$dateGoodVARTWO = validatePostedDate($StartDate_SEARCH, $EndDate_SEARCH);
								
								if(($StartdateGood != true) || ($StartdateGoodVAR != true) || ($EnddateGood != true) || ($EnddateGoodVAR != true) || ($dateGoodVARTWO != true)){
									$_SESSION["ADD_DISPUTE_ERROR"] = 'To Search by Date, you must input both Valid Dates in the correct fields. Please contact the System Admin if you have any questions.';
									header( "Location: viewDisputes.php" );
									exit();
								}
								
								
								for($i=0;$i<$DisputeCount;$i++){
									
									$tempCaseDate = $AllDisputesArray[$i][1];
									
									$tempCaseDate = array_shift(explode(",", $tempCaseDate));
									
									$StartTIME = strtotime($StartDate_SEARCH);
									$EndTIME = strtotime($EndDate_SEARCH);
									$TestTIME = strtotime($tempCaseDate);
									
									if(($TestTIME >= $StartTIME) && ($TestTIME <= $EndTIME)){
										
										array_push($newTableArray, $AllDisputesArray[$i]);
										
									}
								}								
							}
						}
						else{	//$CardNum contains info
							
							if (!is_numeric($cardNum_SEARCH)){
								$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number can be Numbers only!';
								header( "Location: viewDisputes.php" );
								exit();
							}
							
							for($i=0;$i<$DisputeCount;$i++){
								
								$tempCardNum = $AllDisputesArray[$i][7];
								
								$pos = strpos($tempCardNum, $cardNum_SEARCH);
								
								if(($tempCardNum == $cardNum_SEARCH) || ($pos !== FALSE)){
									
									array_push($newTableArray, $AllDisputesArray[$i]);
									
								}
							}
							
							
						}
					}
					else{	//$accNum contains info
						
						if (!is_numeric($accNum_SEARCH)){
							$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number can be Numbers only!';
							header( "Location: viewDisputes.php" );
							exit();
						}
						
						for($i=0;$i<$DisputeCount;$i++){
							
							$tempAccNum = $AllDisputesArray[$i][5];
							
							$pos = strpos($tempAccNum, $accNum_SEARCH);
							
							if(($tempAccNum == $accNum_SEARCH) || ($pos !== FALSE)){
								
								array_push($newTableArray, $AllDisputesArray[$i]);
								
							}
						}
					}
				}
				else{	//$phone contains info
					
					for($i=0;$i<$DisputeCount;$i++){
						
						$tempPhoneNum = $AllDisputesArray[$i][4];
						
						$pos = strpos($tempPhoneNum, $phone_SEARCH);
						
						if(($tempPhoneNum == $phone_SEARCH) || ($pos !== FALSE)){
							
							array_push($newTableArray, $AllDisputesArray[$i]);
							
						}
					}
				}
			}
			else{	//$name contains info
				
				for($i=0;$i<$DisputeCount;$i++){
					
					$tempNameNum = strtolower($AllDisputesArray[$i][2]." ".$AllDisputesArray[$i][3]);
					
					$pos = strpos($tempNameNum, strtolower($name_SEARCH));
					
					if(($tempNameNum == strtolower($name_SEARCH)) || ($pos !== FALSE)){
						
						array_push($newTableArray, $AllDisputesArray[$i]);
						
					}
				}
			}
		}
		else{	//$caseID contains info
			
			if (!is_numeric($caseID_SEARCH)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number can be Numbers only!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			for($i=0;$i<$DisputeCount;$i++){
				
				$tempCaseIDNum = $AllDisputesArray[$i][0];
				
				$pos = strpos($tempCaseIDNum, $caseID_SEARCH);
				
				if(($tempCaseIDNum == $caseID_SEARCH) || ($pos !== FALSE)){
					
					array_push($newTableArray, $AllDisputesArray[$i]);
					
				}
			}
		
		}
		
		$newTableCount = count($newTableArray);
		
	}
	
	if(isset($_POST["ResetForm"])){
		
		$_SESSION["ResetSession"] = "True";
		header( "Location: viewDisputes.php" );
		exit();
	}
	
}



?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - View Disputes</title>
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
$PageTitle = "Dispute Track - View Disputes";

if($accesslevel >= 7){
	include("assets/includes/HTMLscript.php");
}


include("assets/includes/autologout.php");



?>


   
	<script>
		
		function viewALL() {
			
			//Show All
			
			var x = document.getElementById('ViewALLTable');
			
			x.style.display = 'block';
			
		}
		
		function hideALL() {
			
			var x = document.getElementById('ViewALLTable');
			
			x.style.display = 'none';
			
		}
		
		function ShowAdvanced() {
			
			//Show All
			
			var x = document.getElementById('AdvancedSearch');
			
			x.style.display = 'block';
			
		}
		
		function HideAdvanced() {
			
			var x = document.getElementById('AdvancedSearch');
			
			x.style.display = 'none';
			
		}	
		
		
	</script>
   
   
   
</head>
<body>
     
           
          
    <div id="wrapper">
	
        <?php include("assets/includes/head+menu.php"); ?>
		
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-5">
                     <h2>View Disputes</h2>   
                    </div>
					<div class="col-md-7">
<?php
 //Session Error
 
//$_SESSION["TransactionAdded"] = "Test";

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
				<hr />
                
				<!-- /. ROW  -->
                  
				<div class="row">
				
					<div class="col-md-6">
						<button class="btn btn-primary" id="ViewAll" onclick="viewALL();">Show All Disputes</button>
						<button class="btn btn-danger" id="HideAll" onclick="hideALL();">Hide All Disputes</button>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<button class="btn btn-primary" id="ViewAll" onclick="ShowAdvanced();">Advanced Search</button>
						<button class="btn btn-danger" id="HideAll" onclick="HideAdvanced();">Hide Advanced Search</button>
					</div>
				</div>
				
<?php

$DisplayValue = FALSE;

if(isset($_POST['SearchCaseID'])){
	$DisplayValue = TRUE;
}
if(isset($_SESSION["ResetSession"])){
	$DisplayValue = TRUE;
	unset($_SESSION["ResetSession"]);
}


?>
				
				<div id='AdvancedSearch' style='<?php print $DisplayValue ? 'display:block' : 'display:none' ?>'>
					<hr />
					<div class="row">
						<div class="col-md-12">
							<label>Search by:</label>
						</div>
					</div>
					
					<form id="viewDisputeSearchID" autocomplete="off" name="viewDisputeSearch" method="post" action="viewDisputes.php">
					
				
					<div class='row'>
						<div class="col-md-2">
							<label>Case ID</label>
							<input type="text" class="form-control" name="SearchCaseID" placeholder="Search" value="<?php print isset($_POST['SearchCaseID']) ? $_POST['SearchCaseID'] : '' ?>">
						</div>
						<div class="col-md-2">
							<label>Name</label>
							<input type="text" class="form-control" name="SearchName" placeholder="Search" value="<?php print isset($_POST['SearchName']) ? $_POST['SearchName'] : '' ?>">
						</div>
						<div class="col-md-2">
							<label>Phone</label>
							<input type="text" class="form-control" name="SearchPhone" placeholder="Search" value="<?php print isset($_POST['SearchPhone']) ? $_POST['SearchPhone'] : '' ?>">
						</div>
						<div class="col-md-2">
							<label>Account Number</label>
							<input type="text" class="form-control" name="SearchAccNum" placeholder="Search" value="<?php print isset($_POST['SearchAccNum']) ? $_POST['SearchAccNum'] : '' ?>">
						</div>
						<div class="col-md-2">
							<label>Card Number</label>
							<input type="text" class="form-control" name="SearchCardNum" placeholder="Search" value="<?php print isset($_POST['SearchCardNum']) ? $_POST['SearchCardNum'] : '' ?>">
						</div>
					</div>
					<br>
					<div class='row'>
						<div class="col-md-2">
							<label>Case Start Date</label>
							<input type="text" class="form-control" name="SearchSDate" placeholder="Search" onFocus="showCalendarControl(this);" value="<?php print isset($_POST['SearchSDate']) ? $_POST['SearchSDate'] : '' ?>">
						</div>
						<div class="col-md-2">
							<label>Case End Date</label>
							<input type="text" class="form-control" name="SearchEDate" placeholder="Search" onFocus="showCalendarControl(this);" value="<?php print isset($_POST['SearchEDate']) ? $_POST['SearchEDate'] : '' ?>">
						</div>
						
					</div>
					<br>
					<div class='row'>
						<div class="col-md-6">
							<button type="submit" class="btn btn-primary" id="SubmitSearchID" name="SubmitSearch" value="Search">Search</button>
							<input type="submit" value="Reset Form" name="ResetForm" style="float:right" class="btn btn-danger">
							<!-- 
							<button type="reset" id='myReset' style="float:right" class="btn btn-danger" onclick="this.form.reset();">Reset Search Form</button> 
							<input type="submit" value="Reset Form" name="ResetForm" style="float:right" class="btn btn-danger" onClick="window.location.href=window.location.href"> 
							-->
						</div>
					</div>
					</form>
				</div>
				
				
				<hr />
				
				
				  <!--  Main Body  -->

<?php

// =========================================================================================================

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	
	if(isset($_POST["SubmitSearch"])){
		
		print "<div class='row'>";
		print "<div class='col-md-12'>";
		print "<label>Dispute Results Found: *[Red Rows means case is not done yet.]*</label>";
		print "<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>";
		print "<thead>";
		print "<tr>";
		print "<th style='width:5%;'>Case ID</th>";
		print "<th style='width:10%;'>Case Date</th>";
		print "<th style='width:10%;'>Customer Name</th>";
		print "<th style='width:10%;'>Customer Phone</th>";
		print "<th style='width:10%;'>Account Number</th>";
		print "<th style='width:10%;'>Card Number</th>";
		print "<th style='width:15%;'><div style='float:left; width:50%; text-align:left'>Transaction Date</div><div style='float:right; width:50%; text-align:right'>Amount</div></th>";
		print "<th style='width:5%;'>View</th>";
		print "<th style='width:5%;'>Edit</th>";
		print "</tr>";
		print "</thead>";
		print "<tbody>";

		for($i=$newTableCount-1; $i >= 0; $i--){
			
			if($AllDisputesArray[$i][8] == "1"){
				print '<tr>';
			}
			else{
				print '<tr class="danger">';
			}
			
			print "<td>".$newTableArray[$i][0]."</td>";
			print "<td>".$newTableArray[$i][1]."</td>";
			print "<td>".$newTableArray[$i][2]." ".$newTableArray[$i][3]."</td>";
			print "<td>".$newTableArray[$i][4]."</td>";
			print "<td>".$newTableArray[$i][5]."</td>";
			print "<td>".$newTableArray[$i][7]."</td>";
			
			$tmpNum = count($newTableArray[$i]);
			
			print "<td>";
			for($m=9; $m < $tmpNum; $m++){
				print "<div style='float:left; width:50%; text-align:left'>";
				print $newTableArray[$i][$m][3];
				print "</div>";
				
				print "<div style='float:right; width:50%; text-align:right'>";
				print $newTableArray[$i][$m][2];
				print "</div>";
			}
			print "</td>";
			
			print "<td><form method='post' action='DisputeViewOnly.php'><button type='submit' name='ViewSubmit' value='".$newTableArray[$i][0]."' class='fa fa-external-link-square btn btn-primary'></button></form></td>";
			
			print "<td><form method='post' action='editDispute.php'><button type='submit' name='EditSubmit' value='".$newTableArray[$i][0]."' class='fa fa-edit btn btn-primary'></button></form></td>";
			
			print "</tr>";
		}

		print "</tbody>";
		print "</table>";
		print "</div>";
		print "</div><hr />";
		
		
	}
	
}



// =========================================================================================================







// ---------------------------------------------------------------------------------------------------------


print "<div class='row' id='ViewALLTable' style='display:block'>";
print "<div class='col-md-12'>";
print "<label>All Disputes: [Red Rows means case is not done yet.]</label>";
print "<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>";
print "<thead>";
print "<tr>";
print "<th style='width:5%;'>Case ID</th>";
print "<th style='width:10%;'>Case Date</th>";
print "<th style='width:10%';'>Customer Name</th>";
print "<th style='width:10%;'>Customer Phone</th>";
print "<th style='width:10%;'>Account Number</th>";
print "<th style='width:10%;'>Card Number</th>";
print "<th style='width:15%;'><div style='float:left; width:50%; text-align:left'>Transaction Date</div><div style='float:right; width:50%; text-align:right'>Amount</div></th>";
print "<th style='width:5%;'>View</th>";
print "<th style='width:5%;'>Edit</th>";
print "</tr>";
print "</thead>";
print "<tbody id='tableBODY'>";

for($i=$DisputeCount-1; $i >= 0; $i--){
	
	if($AllDisputesArray[$i][8] == "1"){
		print '<tr>';
	}
	else{
		print '<tr class="danger">';
	}
	
	print "<td>".$AllDisputesArray[$i][0]."</td>";
	print "<td>".$AllDisputesArray[$i][1]."</td>";
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

	print "<td><form method='post' action='DisputeViewOnly.php'><button type='submit' name='ViewSubmit' value='".$AllDisputesArray[$i][0]."' class='fa fa-external-link-square btn btn-primary'></button></form></td>";
	
	print "<td><form method='post' action='editDispute.php'><button type='submit' name='EditSubmit' value='".$AllDisputesArray[$i][0]."' class='fa fa-edit btn btn-primary'></button></form></td>";
	
	print "</tr>";
}

print "</tbody>";
print "</table>";
print "</div>";
print "</div>";


?>
					
					
					
					
			</div>
	<!--  Footer  -->
<p></p>
	
             <!-- /. PAGE INNER  -->
            </div>
         <!-- /. PAGE WRAPPER  -->
        </div>

		<!--  Bottom of Menu Nav  -->
		
     <!-- /. WRAPPER  -->

    
   
</body>

</html>

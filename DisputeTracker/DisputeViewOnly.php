<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');



if ($accesslevel < 5){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}


$ViewCaseID = 0;


//DB Queries
//===============================================================
$acct_type_query = "SELECT * FROM accounttype WHERE iddeleted='0'";
$acct_type_query_data = $dtcon->query($acct_type_query);
$acct_type_data = $acct_type_query_data->fetch_all();

//var_dump($acct_type_data);
$Numberof_acct_Types = count($acct_type_data);
//===============================================================
$type_query = "SELECT * FROM cardtype WHERE iddeleted='0'";
$type_query_data = $dtcon->query($type_query);
$type_data = $type_query_data->fetch_all();

//var_dump($type_data);
$CardTypeCOUNT = count($type_data);
//===============================================================
$status_query = "SELECT * FROM cardstatus WHERE iddeleted='0'";
$status_query_data = $dtcon->query($status_query);
$status_data = $status_query_data->fetch_all();

//var_dump($status_data);
$CardStatusCOUNT = count($status_data);
//===============================================================
$possession_query = "SELECT * FROM cardpossession WHERE iddeleted='0'";
$possession_query_data = $dtcon->query($possession_query);
$possession_data = $possession_query_data->fetch_all();

//var_dump($possession_data);
$CardPossessionCOUNT = count($possession_data);
//===============================================================
$compromise_query = "SELECT * FROM compromise WHERE iddeleted='0'";
$compromise_query_data = $dtcon->query($compromise_query);
$compromise_data = $compromise_query_data->fetch_all();

//var_dump($compromise_data);
$NumberofComp = count($compromise_data);
//===============================================================
$reversal_query = "SELECT * FROM reversalerrors WHERE iddeleted='0'";
$reversal_query_data = $dtcon->query($reversal_query);
$reversal_data = $reversal_query_data->fetch_all();

//var_dump($reversal_data);
$NumberofRever = count($reversal_data);
//===============================================================
$disputeReason_query = "SELECT * FROM disputereasons WHERE iddeleted='0'";
$disputeReason_query_data = $dtcon->query($disputeReason_query);
$disputeReason_data = $disputeReason_query_data->fetch_all();

//var_dump($reversal_data);
$NumberofdisputeReason = count($disputeReason_data);
//===============================================================


//You can only get to this page via View disputes OR Coming back from Edit Dispute Transactions
//Edit Dispute Transactions is the 'EditDisputeError'
if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_SESSION["EditDisputeError"])){
	
	if(isset($_POST["ViewSubmit"])){
		
		//var_dump($_POST);
		
		$ViewCaseID = $_POST["ViewSubmit"];
		
		//var_dump($ViewCaseID);
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Server POST, please contact the System Admin.';
		header( "Location: viewDisputes.php" );
		exit();
	}
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select a case to view from View Disputes.';
	header( "Location: viewDisputes.php" );
	exit();
}


$CaseDONEQuery = "SELECT casedoneinput FROM checkcases where id='" . $ViewCaseID . "'";
$CaseDONEQuery_Data = $dtcon->query($CaseDONEQuery);
$CaseDONEData = $CaseDONEQuery_Data->fetch_all();

if(empty($CaseDONEData)){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Number not found!';
	header( "Location: viewDisputes.php" );
	exit();
}
if($CaseDONEData[0][0] == '0'){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Number: '.$ViewCaseID.' is not done! You can\'t view the case until it is done. Contact IT if you have any questions.';
	//$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Number: '.$ViewCaseID.' is not done! You can only view the case.';
	header( "Location: viewDisputes.php" );
	exit();
}




?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - Dispute View Only</title>
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
$PageTitle = "Dispute Track - View Dispute";

if($accesslevel >= 7){
	include("assets/includes/HTMLscript.php");
}


include("assets/includes/autologout.php");

include("assets/includes/loadingHTML.php");

?>

	<script>
		
		function addDate() {
			
			var currSelectID = document.getElementById('CardPossessionID');
			var currValue = currSelectID.options[currSelectID.selectedIndex].value;
			
			if (currValue == "select" || currValue == "1"){
				
				var x = document.getElementById('possDate');
				
				x.style.display = 'none';
			
				document.getElementById('possessionDateID').value = '';
				
			}
			else{
				
				var x = document.getElementById('possDate');
			
				x.style.display = 'block';
				
			}
		}
		
	</script>



</head>
<body>
     
           
          
    <div id="wrapper">
        
		<?php include("assets/includes/head+menu.php"); ?>
        
        <div id="page-wrapper" >
            <div id="page-inner">
                
				<div class="row">
                    <div class="col-md-4">
                     <h2>Dispute (View Only)</h2> 
                    </div>
					<div class="col-md-8">
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
				
				<hr />
				
<!--   Case Edit Information         -->
<?php

//$ViewCaseID
//Check Case ID
if($ViewCaseID == 0 || $ViewCaseID == NULL){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select a case to edit from View Disputes.';
	echo '<script> window.location = "viewDisputes.php";</script>';
	//header( "Location: viewDisputes.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}


//SQL Info--------------------------------------------------------------------------------------------------------

//Get Case Infomation
$CaseQuery = "SELECT * FROM checkcases where id='" . $ViewCaseID . "'";
$CaseQuery_Data = $dtcon->query($CaseQuery);
$CaseData = $CaseQuery_Data->fetch_all();

//var_dump($CaseData);

if (!empty($CaseData)){
	if (!empty($CaseData[0])){
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Information for the Case ID, '.$ViewCaseID.', Was not found! Please Contact the System Admin.';
		echo '<script> window.location = "index.php";</script>';
		//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
		exit();
	}
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Information for the Case ID, '.$ViewCaseID.', Was not found! Please Contact the System Admin.';
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

$AccountNumQuery = "SELECT * FROM checkaccountnumbers where caseid='" . $ViewCaseID . "'";
$AccountNumQuery_Data = $dtcon->query($AccountNumQuery);
$AccountNumData = $AccountNumQuery_Data->fetch_all();

//var_dump($AccountNumData);

if (!empty($AccountNumData)){
	if (!empty($AccountNumData[0])){
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID, '.$ViewCaseID.', Was not found! Please Contact the System Admin.';
		echo '<script> window.location = "index.php";</script>';
		//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
		exit();
	}
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID, '.$ViewCaseID.', Was not found! Please Contact the System Admin.';
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

$CardNumQuery = "SELECT * FROM checkcardnumbers where caseid='" . $ViewCaseID . "'";
$CardNumQuery_Data = $dtcon->query($CardNumQuery);
$CardNumData = $CardNumQuery_Data->fetch_all();

//var_dump($CardNumData);

if (!empty($CardNumData)){
	if (!empty($CardNumData[0])){
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number for the Case ID, '.$ViewCaseID.', Was not found! Please Contact the System Admin.';
		echo '<script> window.location = "index.php";</script>';
		//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
		exit();
	}
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number for the Case ID, '.$ViewCaseID.', Was not found! Please Contact the System Admin.';
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}


//Get Transaction Information
$TransactionQuery = "SELECT * FROM checktransactions where transactiondeleted=FALSE AND caseid='" . $ViewCaseID . "'";
$TransactionQuery_Data = $dtcon->query($TransactionQuery);
$TransactionData = $TransactionQuery_Data->fetch_all();

$TransactionCount = count($TransactionData);

//var_dump($TransactionData, $TransactionCount);

//At the moment No one but the person who started the case can Add a transaction, Only Delete the case and start over.
//You also cant Add a new transaction at all when the case is done being added.
if (empty($TransactionData)){
	$_SESSION["ADD_DISPUTE_ERROR"] = "This Case doesn't have any transactions! Please contact the system Admin!";
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
	
}


?>

				<div class="row">
                    <div class="col-md-4">
						<h4>Case ID: <?php print $ViewCaseID; ?></h4>
                    </div>
                </div> <hr />
			  
			<div class="row">
				<div class="col-md-3">
					<label>Back to View Disputes</label>
				<!-- 2 Buttons, One to go back to view disputes without editing the Case and one to Delete the Case and All Transactions -->
					<a href="viewDisputes.php" class="btn btn-success form-control">View Disputes</a>
				</div>
				<div class="col-md-3">
					<label>Edit Dispute Case and Transactions</label>
					<form id="editDispute_ID" autocomplete="off" name="GoToEditDisputeForm" method="post" action="editDispute.php">
						<input type='hidden' name='EditSubmit' value='<?php print $ViewCaseID; ?>'>
						<input type="submit" class="btn btn-primary form-control" name="ViewEditSubmit" value="Edit Dispute">
					</form>
				</div>
				<div class="col-md-3">
					<label>Reprint the VISA Dispute Form</label>
					<form id="RePrintDisputeFormForm_ID" autocomplete="off" name="RePrintDisputeFormForm" method="post" action="generateDisputeForm.php" onsubmit="showLoading();">
						<input type='hidden' name='redoDisputeForm' value='<?php print $ViewCaseID; ?>'>
						<input type="submit" class="btn btn-ZnewFour" name="ReprintDisputeForm" value="Reprint the VISA Dispute Form">
					</form>
				</div>
				
				
			</div><hr />
			
			
<?php

$LettersArray = array();

$DisputeFormArray = array();

$LetterDirPath = './FileFolder/ProvisionalCreditLetters/';

$GeneratedFormPath = './FileFolder/VisaCheckCardDisputeForms/';

$LetterDirFiles = scandir($LetterDirPath);

$GeneratedFormFiles = scandir($GeneratedFormPath);


foreach($LetterDirFiles as $fileName){
	
	if ($fileName == "."){
		
	}
	else if ($fileName == ".."){
		
	}
	else if ($fileName == "index.html"){
		
	}
	else{
		$pathparts = explode('_', $fileName);
		
		//var_dump($pathparts);
		
		if(is_numeric($pathparts[1])){
			if($pathparts[1] == $ViewCaseID){
				array_push($LettersArray,$fileName);
			}
		}
	}
}

foreach($GeneratedFormFiles as $fileName){
	
	if ($fileName == "."){
		
	}
	else if ($fileName == ".."){
		
	}
	else if ($fileName == "index.html"){
		
	}
	else{
		$pathparts = explode('_', $fileName);
		
		//var_dump($pathparts);
		
		if(is_numeric($pathparts[1])){
			if($pathparts[1] == $ViewCaseID){
				array_push($DisputeFormArray,$fileName);
			}
		}
	}
}




?>
			<div class="row">
				
				<div class="col-md-6">
					<label>Provisional Credit Letters:</label>
					<ul>
<?php

foreach($LettersArray as $filePathName){
	print "<li><a target='_blank' href=\"./FileFolder/ProvisionalCreditLetters/" . $filePathName . "\">".$filePathName."</a></li>";
}

?>
					</ul> 
				</div>
				<div class="col-md-6">
					<label>VISA Dispute Forms:</label>
					<ul>
<?php

foreach($DisputeFormArray as $filePathName){
	print "<li><a target='_blank' href=\"./FileFolder/VisaCheckCardDisputeForms/" . $filePathName . "\">".$filePathName."</a></li>";
}

?>
					</ul>
				</div>
				
			</div><hr />
			
			
			
	<div class="row">
		<div class="col-md-3">
			<label>First Name</label>
			<input type="text" class="form-control" value="<?php print $CaseData[0][2]; ?>" id="firstname" readonly>
		</div>
		<div class="col-md-3">
			<label>Last Name</label>
			<input type="text" class="form-control" value="<?php print $CaseData[0][3]; ?>" id="lastname" readonly>
		</div>
		<div class="col-md-3">
			<label>Phone Number</label>
			<input type="text" class="form-control" value="<?php print $CaseData[0][4]; ?>" id="phonenum" readonly>
		</div>
		<div class="col-md-3">
			<label>Email Address</label>
			<input type="text" class="form-control" value="<?php print $CaseData[0][5]; ?>" id="emailaddr" readonly>
		</div>
	</div><hr />
	
	<div class="row">
		<div class="col-md-3">
			<label>Address One:</label>
			<input type="text" class="form-control" value="<?php print $CaseData[0][6]; ?>" id="addr1" readonly>
		</div>
		<div class="col-md-3">
			<label>Address Two:</label>
			<input type="text" class="form-control" value="<?php print $CaseData[0][7]; ?>" id="addr2" readonly>
		</div>
		<div class="col-md-2">
			<label>City</label>
			<input type="text" class="form-control" value="<?php print $CaseData[0][8]; ?>" id="city" readonly>
		</div>
		<div class="col-md-2">
			<label>State</label>
			<select name="StateAddr" class="form-control" readonly>
<?php

//Print each state as it is returned by the function
$CaseState = $CaseData[0][9];
$ReturnArray = stateCheck($CaseState);

foreach($ReturnArray as $PrintLine){
	print $PrintLine;
}

?>
			</select>
		</div>
		<div class="col-md-2">
			<label>Zip Code:</label>
			<input type="text" class="form-control" value="<?php print $CaseData[0][10]; ?>" id="zip" readonly>
		</div>
	</div><hr />
	
	<div class="row">
		<div class="col-md-3">
			<label>Is the Customer in person or on the phone?</label>
			<select name="inpersonorPhone" class="form-control" readonly>
<?php

if($CaseData[0][16] == '1'){
	print '<option value="inperson" selected="true">In Person</option>';
	print '<option value="phone" disabled>On Phone</option>';
}
else if($CaseData[0][16] == '2'){
	print '<option value="inperson" disabled>In Person</option>';
	print '<option value="phone" selected="true">On Phone</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Customer Start Method Error! Please contact the system Admin!";
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

?>
			</select>
		</div>
		
		<div class="col-md-3">
			<label>Is this Account New?</label>
			<select name="newAccount" class="form-control" readonly>
<?php

if($AccountNumData[0][6] == 0){
	print '<option value="newAccTrue" disabled>Account is New</option>';
	print '<option value="newAccFalse" selected="true">Account is not New</option>';
}
else if($AccountNumData[0][6] == 1){
	print '<option value="newAccTrue" selected="true">Account is New</option>';
	print '<option value="newAccFalse" disabled>Account is not New</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Account Age Error! Please contact the system Admin!";
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

?>
			</select> 
		</div>
		<div class="col-md-6">
			<label>Case Comments</label>
			<textarea rows="5" class="form-control" readonly><?php print $CaseData[0][13]; ?></textarea>
		</div>
		
	</div><hr />
	
	<div class="row">
		<div class="col-md-2">
			<label>User Started:</label>
			<input type="text" class="form-control2" value="<?php print $CaseData[0][14]; ?>" readonly>
		</div>
		<div class="col-md-2">
			<label>Is the Case Red Flagged</label>
			<select name="RedFlag" class="form-control" readonly>
<?php

if($CaseData[0][11] == 1){
	print '<option value="Positive" selected="true">Case Red Flagged</option>';
	print '<option value="Negative" disabled>Case Not Flagged</option>';
}
else if($CaseData[0][11] == 0){
	print '<option value="Positive" disabled>Case Red Flagged</option>';
	print '<option value="Negative" selected="true">Case Not Flagged</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Case Red Flag Error! Please contact the system Admin!";
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

?>
			</select>
		</div>
		<div class="col-md-2">
			<label>Sev Lev</label>
			<input type="text" class="form-control" value="<?php print $CaseData[0][12]; ?>" id="sevlev" readonly>
		</div>
		
		<div class="col-md-2">
			<label>PC Letter Print</label>
			<select name="PCPrintFlag" class="form-control" readonly>
<?php 

if($CaseData[0][17] == 1){
	print '<option value="Positive" selected="true">Case Flagged to Print</option>';
	print '<option value="Negative" disabled>Case Not Flagged to Print</option>';
}
else if($CaseData[0][17] == 0){
	print '<option value="Positive" disabled>Case Flagged to Print</option>';
	print '<option value="Negative" selected="true">Case Not Flagged to Print</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Case Print Flag Error! Please contact the system Admin!";
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

?>
			</select>
		</div>
		<div class="col-md-2">
			<label>Case Closed</label>
			<select name="CaseClosed" class="form-control" readonly>
<?php

if($CaseData[0][18] == 1){
	print '<option value="Positive" selected="true">Case is Closed</option>';
	print '<option value="Negative" disabled>Case is not Closed</option>';
}
else if($CaseData[0][18] == 0){
	print '<option value="Positive" disabled>Case is Closed</option>';
	print '<option value="Negative" selected="true">Case is not Closed</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Case Closed Flag Error! Please contact the system Admin!";
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

?>
			</select>
		</div>
		
	</div><hr />
	
	<!-- Account Number Info -->
	
	<div class="row">
		<div class="col-md-2">
			<label>Account Number</label>
			<input type="text" class="form-control" value="<?php print $AccountNumData[0][2]; ?>" id="accountnum" readonly>
		</div>
		<div class="col-md-2">
			<label>Consumer or Business Account:</label>
			<select name="BusiCustType" class="form-control" readonly>
<?php

if($AccountNumData[0][4] == 1){
	print '<option value="Consumer" disabled>Consumer</option>';
	print '<option value="Business" selected="true">Business</option>';
}
else if($AccountNumData[0][4] == 0){
	print '<option value="Consumer" selected="true">Consumer</option>';
	print '<option value="Business" disabled>Business</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Consumer or Business Account Type Error! Please contact the system Admin!";
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

?>
			</select>
		</div>
		<div class="col-md-2">
			<label>Account Type:</label>
			<select name="AccountType" class="form-control" readonly>
<?php

for ($i=0; $i < $Numberof_acct_Types; $i++){
	
	if($acct_type_data[$i][0] == $AccountNumData[0][3]){
		print '<option value="' . $acct_type_data[$i][0] . '" selected="true">' . $acct_type_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $acct_type_data[$i][0] . '" disabled>' . $acct_type_data[$i][1] . '</option>';
	}
}

?>
			</select>
		</div>
		<div class="col-md-6">
			<label>Account Comments</label>
			<textarea rows="5" class="form-control" readonly><?php print $AccountNumData[0][5]; ?></textarea>
		</div>
	</div><hr />
	
	<!-- Card Number Info -->
	
	<div class="row">	
		<div class="col-md-2">
			<label>Card Number</label>
			<input type="text" class="form-control" value="<?php print $CardNumData[0][2]; ?>" id="cardnum" readonly>
		</div>
		<div class="col-md-2">
			<label>Is the card a chip card?</label>
			<select name="ChipCard" class="form-control" readonly>
<?php

if($CardNumData[0][7] == 0){
	print '<option value="yes" disabled>Yes</option>';
	print '<option value="no" selected="true">No</option>';
}
else if($CardNumData[0][7] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no" disabled>No</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Chip Card Error! Please contact the system Admin!";
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

?>
			</select>
		</div>
		<div class="col-md-2">
			<label>Card Type</label>
			<select name="CardType" class="form-control" readonly>
<?php

for ($i=0; $i < $CardTypeCOUNT; $i++){
	
	if($type_data[$i][0] == $CardNumData[0][3]){
		print '<option value="' . $type_data[$i][0] . '" selected="true">' . $type_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $type_data[$i][0] . '" disabled>' . $type_data[$i][1] . '</option>';
	}
}

?>
			</select>
		</div>
		<div class="col-md-2">
			<label>Current Card Status</label>
			<select name="CardStatus" class="form-control" readonly>
<?php

for ($i=0; $i < $CardStatusCOUNT; $i++){
	
	if($status_data[$i][0] == $CardNumData[0][4]){
		print '<option value="' . $status_data[$i][0] . '" selected="true">' . $status_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $status_data[$i][0] . '" disabled>' . $status_data[$i][1] . '</option>';
	}
}

?>
			</select>
		</div>
		<div class="col-md-2">
			<label>Card Possession</label>
			<select id="CardPossessionID" name="CardPossession" onchange="addDate();" class="form-control" readonly>
<?php

for ($i=0; $i < $CardPossessionCOUNT; $i++){
	
	if($possession_data[$i][0] == $CardNumData[0][5]){
		print '<option value="' . $possession_data[$i][0] . '" selected="true">' . $possession_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $possession_data[$i][0] . '" disabled>' . $possession_data[$i][1] . '</option>';
	}
}

?>		
			</select>
		</div>
		<div class="col-md-2" id="possDate">
			<label>Card Missing Date</label>
			<input type="text" class="form-control" id="possessionDateID" name="possessionDate" value="<?php print $CardNumData[0][6]; ?>" readonly>
		</div>
	</div><hr />
		
	<div class="row">
		<div class="col-md-6">
			<label>Card Comments</label>
			<textarea rows="5" class="form-control" onfocus="this.select();" maxlength="4096" readonly><?php print $CardNumData[0][8]; ?></textarea>
		</div>
	</div><hr />
	
	
	<div class="row">
		<div class="col-md-12">
			
			<h5>Transaction Items</h5>
			<div class="panel-group" id="accordion">
				
<?php

for($m=0;$m<$TransactionCount;$m++){

?>
			
			<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php print $m+1; ?>" class="collapsed">Transaction Number: <?php print $m+1; ?></a>
						</h4>
					</div>
					<div id="collapse<?php print $m+1;?>" class="panel-collapse collapse" style="height: 0px;">
						<div class="panel-body">
						
			
				<div class="row">
					<div class="col-md-2">
						<label>Transaction Date</label>
						<input type="text" class="form-control" value="<?php print $TransactionData[$m][4]; ?>" id="transactiondate" readonly>
						
					</div>
					<div class="col-md-2">
						<label>Date Posted to Account</label>
						<input type="text" class="form-control" value="<?php print $TransactionData[$m][5]; ?>" id="posteddate" readonly>
						
					</div>
					<div class="col-md-2">
						<label>Amount Disputed</label>
						<input type="text" class="form-control" value="<?php print $TransactionData[$m][3]; ?>" id="amountDisputed" readonly>
					</div>
					<div class="col-md-2">
						<label>Is this amount a Loss?</label>
						<select name="amountLoss" class="form-control" readonly> 
<?php

if($TransactionData[$m][13] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no" disabled>No</option>';
}
else if($TransactionData[$m][13] == 0){
	print '<option value="yes" disabled>Yes</option>';
	print '<option value="no" selected="true">No</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Amount Loss Error! Please contact the system Admin!";
	echo '<script> window.location = "viewDisputes.php";</script>';
	//header( "Location: viewDisputes.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

?>
						</select> 
					</div>
					
					<div class="col-md-4">
						<label>Does the customer have a receipt for the transaction?</label>
						<select id="receiptstatusID" name="receiptstatus" class="form-control" readonly> 
<?php

if($TransactionData[$m][12] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no" disabled>No</option>';
}
else if($TransactionData[$m][12] == 0){
	print '<option value="yes" disabled>Yes</option>';
	print '<option value="no" selected="true">No</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Customer Receipt Error! Please contact the system Admin!";
	echo '<script> window.location = "viewDisputes.php";</script>';
	//header( "Location: viewDisputes.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

?>
						</select> 
					</div>
					
					
				</div><hr />
				
				<div class="row">
					<div class="col-md-4">
						<label>Dispute Reason:</label>
						<select name="DisputeReason" class="form-control" readonly>
<?php

for ($i=0; $i < $NumberofdisputeReason; $i++){
	
	if($disputeReason_data[$i][0] == $TransactionData[$m][6]){
		print '<option value="' . $disputeReason_data[$i][0] . '" selected="true">' . $disputeReason_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $disputeReason_data[$i][0] . '" disabled>' . $disputeReason_data[$i][1] . '</option>';
	}
}

?>
						</select>
					</div>
					<div class="col-md-8">
						<label>Dispute Description</label>
						<textarea rows="5" type="text" class="form-control" readonly><?php print $TransactionData[$m][7]; ?></textarea>
					</div>
				</div><hr />
				
				<div class="row">
					<div class="col-md-3">
						<label>Merchant Name:</label>
						<input type="text" class="form-control" value="<?php print $TransactionData[$m][8]; ?>" id="merchname" readonly>
					</div>
					<div class="col-md-3">
						<label>Merchant Phone:</label>
						<input type="text" class="form-control" value="<?php print $TransactionData[$m][25]; ?>" id="merchphone" readonly>
					</div>
					<div class="col-md-3">
						<label>Has the Merchant been contacted?</label>
						<select id="marchantcontactstatusID" name="merchantcontactstatus" class="form-control" readonly>
<?php

if($TransactionData[$m][9] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no" disabled>No</option>';
}
else if($TransactionData[$m][9] == 0){
	print '<option value="yes" disabled>Yes</option>';
	print '<option value="no" selected="true">No</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Merchant Contact Error! Please contact the system Admin!";
	echo '<script> window.location = "viewDisputes.php";</script>';
	//header( "Location: viewDisputes.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}




$DisplayValue = FALSE;

if($TransactionData[$m][9] == 1){
	$DisplayValue = TRUE;
}


?>
						</select> 
					</div>
					<div class="col-md-3" id="MerchantContactDayID" style='<?php print $DisplayValue ? 'display:block' : 'display:none' ?>'>
						<label>Contact Date:</label>
						<input type="text" class="form-control" id="MerchContactDate" value="<?php print $TransactionData[$m][10]; ?>" readonly>
					</div>
				</div><hr />
				
				<div class="row">
					<div class="col-md-6">
						<label>Merchant Contact Description:</label>
						<textarea rows="5" class="form-control" readonly><?php print $TransactionData[$m][11]; ?></textarea>
					</div>
					<div class="col-md-6">
						<label>Merchant Notes:</label>
						<textarea rows="5" class="form-control" readonly><?php print $TransactionData[$m][26]; ?></textarea>
					</div>
				</div><hr />
				
				<div class="row">
					<div class="col-md-12">
						<label>Employee Comments:</label>
						<textarea rows="5" class="form-control" readonly><?php print $TransactionData[$m][21]; ?></textarea>
					</div>
					
				</div><hr />
				
				<!-- Compromise, Transaction LOSS, Provisional Credit (All Letters and Reversal Error), ChargeBack -->
				
				<div class="row">
					<div class="col-md-3">
						<label>Date Provisional Credit Letter Created:</label>
						<input type="text" class="form-control2" value="<?php print $TransactionData[$m][15]; ?>" readonly>
					</div>
					<div class="col-md-3">
						<label>Date Provisional Credit Letter Sent:</label>
						<input type="text" class="form-control" value="<?php print $TransactionData[$m][17]; ?>" id="datePClettersent" readonly>
					</div>
				</div><hr />
				
				<div class="row">
					<div class="col-md-3">
						<label>Provisional Credit Reversal Reason:</label>
						<select name="ReversalID" class="form-control" readonly>
<?php

for ($i=0; $i < $NumberofRever; $i++){
	
	if($reversal_data[$i][0] == $TransactionData[$m][14]){
		print '<option value="' . $reversal_data[$i][0] . '" selected="true">' . $reversal_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $reversal_data[$i][0] . '" disabled>' . $reversal_data[$i][1] . '</option>';
	}
}

?>
						</select>
					</div>
					<div class="col-md-3">
						<label>Date Provisional Credit Rescinded:</label>
						<input type="text" class="form-control" value="<?php print $TransactionData[$m][16]; ?>" id="datePCrescind" readonly>
					</div>
					<div class="col-md-3">
						<label>Date Provisional Credit Reversal Letter Sent:</label>
						<input type="text" class="form-control" value="<?php print $TransactionData[$m][18]; ?>" id="dateReversalPCLettersent" readonly>
					</div>
				</div><hr />
				
				<div class="row">
					<div class="col-md-3">
						<label>Chargeback Submitted</label>
						<select name="chargebackSubmitted" class="form-control" readonly> 
<?php

if($TransactionData[$m][19] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no" disabled>No</option>';
}
else if($TransactionData[$m][19] == 0){
	print '<option value="yes" disabled>Yes</option>';
	print '<option value="no" selected="true">No</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Chargeback Submitted Error! Please contact the system Admin!";
	echo '<script> window.location = "viewDisputes.php";</script>';
	//header( "Location: viewDisputes.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

?>
						</select> 
					</div>
					<div class="col-md-3">
						<label>Chargeback Accepted</label>
						<select name="chargebackAccepted" class="form-control" readonly> 
<?php

if($TransactionData[$m][20] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no" disabled>No</option>';
}
else if($TransactionData[$m][20] == 0){
	print '<option value="yes" disabled>Yes</option>';
	print '<option value="no" selected="true">No</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Chargeback Accepted Error! Please contact the system Admin!";
	echo '<script> window.location = "viewDisputes.php";</script>';
	//header( "Location: viewDisputes.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

?>
						</select> 
					</div>
				</div><hr />
				
				<div class="row">
					<div class="col-md-3">
						<label>Compromise:</label>
						<select name="compromiseID" class="form-control" readonly>
							<option value="select" disabled>Select One</option>
<?php

for ($i=0; $i < $NumberofComp; $i++){
	
	if($compromise_data[$i][0] == $TransactionData[$m][22]){
		print '<option value="' . $compromise_data[$i][0] . '" selected="true">' . $compromise_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $compromise_data[$i][0] . '" disabled>' . $compromise_data[$i][1] . '</option>';
	}
}

?>
						</select>
					</div>
					<div class="col-md-9">
						<label>Transaction Results/Fiserv Results:</label>
						<textarea rows="5" class="form-control" readonly><?php print $TransactionData[$m][23]; ?></textarea>
					</div>
				</div><hr />
						
						
						</div>
					</div>
				</div>
				
<?php

}

?>
			</div>
		</div>
	</div>
	
				
				
				<div class="row">
					<div class="col-md-12">
						<label>Attached Documents:</label>
<?php

$AllAttachmentQuery = "SELECT filename, comments FROM checkattachments WHERE caseid='" . $ViewCaseID . "' AND iddeleted=FALSE";
$AllAttachmentQuery_Data = $dtcon->query($AllAttachmentQuery);
$AllAttachmentData = $AllAttachmentQuery_Data->fetch_all();

//var_dump($AllAttachmentQuery, $AllAttachmentData);

print "<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>";
print "<thead>";
print "<tr>";
print "<th style='width:30%;'>Attachment Name</th>";
print "<th style='width:70%;'>Attachment Comment</th>";
print "</tr>";
print "</thead>";
print "<tbody>";

foreach($AllAttachmentData as $AttachmentArr){
	print "<tr>";
	print "<td><a target='_blank' href=\"./FileFolder/UserAttachedDocuments/" . $AttachmentArr[0] . "\">" . $AttachmentArr[0] . "</a></td>";
	print '<td>'.$AttachmentArr[1].'</td>';
	print "</tr>";
}

print "</tbody>";
print "</table>";




?>
					</div>
				</div><hr />
			
			
			<div style="line-height: 2000%">
				&nbsp;
			</div>
			
    </div>
	
	
			<!--  Footer  -->
	<p></p>
	
	
             <!-- /. PAGE INNER  -->
            </div>
         <!-- /. PAGE WRAPPER  -->
        </div>
		
	<script>
		addDate();
	</script>
		
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

<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');


if ($accesslevel < 5){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}



//DB Queries
//===============================================================
$reason_query = "SELECT * FROM disputereasons WHERE iddeleted='0'";
$reason_query_data = $dtcon->query($reason_query);
$reason_data = $reason_query_data->fetch_all();

//var_dump($reason_data);
$NumberofReason = count($reason_data);
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
$NumberofTypes = count($type_data);
//===============================================================
$status_query = "SELECT * FROM cardstatus WHERE iddeleted='0'";
$status_query_data = $dtcon->query($status_query);
$status_data = $status_query_data->fetch_all();

//var_dump($status_data);
$NumberofStatus = count($status_data);
//===============================================================
$possession_query = "SELECT * FROM cardpossession WHERE iddeleted='0'";
$possession_query_data = $dtcon->query($possession_query);
$possession_data = $possession_query_data->fetch_all();

//var_dump($possession_data);
$NumberofPoss = count($possession_data);
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



$EditCaseID = 0;


//You can only get to this page via View disputes OR Coming back from Edit Dispute Transactions
//Edit Dispute Transactions is the 'EditDisputeError'
if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_SESSION["EditDisputeError"])){
	
	if(isset($_POST["EditSubmit"])){
		
		//var_dump($_POST);
		
		$EditCaseID = $_POST["EditSubmit"];
		
		//var_dump($EditCaseID);
		
	}
	else if(isset($_SESSION["EditDisputeError"])){
		
		//var_dump($EditDisputeError);
		
		$EditCaseID = $_SESSION["EditDisputeError"];
		
		unset($_SESSION["EditDisputeError"]);
		
		//var_dump($EditCaseID);
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Server POST, please contact the System Admin.';
		header( "Location: viewDisputes.php" );
		exit();
	}
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select a case to edit from View Disputes.';
	header( "Location: viewDisputes.php" );
	exit();
}



$CaseDONEQuery = "SELECT casedoneinput FROM checkcases where id='" . $EditCaseID . "'";
$CaseDONEQuery_Data = $dtcon->query($CaseDONEQuery);
$CaseDONEData = $CaseDONEQuery_Data->fetch_all();

if(empty($CaseDONEData)){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Number not found!';
	header( "Location: viewDisputes.php" );
	exit();
}
//Ops wants to be able to see these unfinished cases to be able to pinpoint problems. Unknown if this could cause issues down the line.
//if($CaseDONEData[0][0] == '0'){
//	$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Number: '.$EditCaseID.' is not done! You can\'t edit the case until it is done. Contact IT if you have any questions.';
//	header( "Location: viewDisputes.php" );
//	exit();
//}


?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - Edit Dispute</title>
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
$PageTitle = "Dispute Track - Edit Disputes";

if($accesslevel >= 7){
	include("assets/includes/HTMLscript.php");
}


include("assets/includes/autologout.php");

include("assets/includes/loadingHTML.php");

?>

	<script>
	
		function phonenumChange(element){
			
			var successBOOL = false;
			var tempValue = element.value;
			
			if(/^[0-9]{3}[ \-]{0,1}[0-9]{3}[ \-]{0,1}[0-9]{4}$/.test(tempValue)){
				successBOOL = true;
			}
			else{
				return element.value;
			}
			
			tempValue = tempValue.replace(/-/g, "");
			tempValue = tempValue.replace(/ /g, "");
			
			tempValue = tempValue.slice(0,3)+"-"+tempValue.slice(3,6)+"-"+tempValue.slice(6);
			element.value = tempValue;
			return element.value;

		}
		
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
                     <h2>Edit Dispute Case Information</h2> 
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

				<!--  Below H2, above HR  -->
				<hr />
			
                 <!-- /. ROW  -->
                  
<!--   Case Edit Information         -->

<?php

//$EditCaseID
//Check Case ID
if($EditCaseID == 0 || $EditCaseID == NULL){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select a case to edit from View Disputes.';
	echo '<script> window.location = "viewDisputes.php";</script>';
	//header( "Location: viewDisputes.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

//SQL Info--------------------------------------------------------------------------------------------------------

//Get Case Infomation
$CaseQuery = "SELECT * FROM checkcases where id='" . $EditCaseID . "'";
$CaseQuery_Data = $dtcon->query($CaseQuery);
$CaseData = $CaseQuery_Data->fetch_all();

//var_dump($CaseData);

if (!empty($CaseData)){
	if (!empty($CaseData[0])){
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Information for the Case ID, '.$EditCaseID.', Was not found! Please Contact the System Admin.';
		echo '<script> window.location = "index.php";</script>';
		//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
		exit();
	}
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Information for the Case ID, '.$EditCaseID.', Was not found! Please Contact the System Admin.';
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

$AccountNumQuery = "SELECT * FROM checkaccountnumbers where caseid='" . $EditCaseID . "'";
$AccountNumQuery_Data = $dtcon->query($AccountNumQuery);
$AccountNumData = $AccountNumQuery_Data->fetch_all();

//var_dump($AccountNumData);

if (!empty($AccountNumData)){
	if (!empty($AccountNumData[0])){
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID, '.$EditCaseID.', Was not found! Please Contact the System Admin.';
		echo '<script> window.location = "index.php";</script>';
		//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
		exit();
	}
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID, '.$EditCaseID.', Was not found! Please Contact the System Admin.';
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

$CardNumQuery = "SELECT * FROM checkcardnumbers where caseid='" . $EditCaseID . "'";
$CardNumQuery_Data = $dtcon->query($CardNumQuery);
$CardNumData = $CardNumQuery_Data->fetch_all();

//var_dump($CardNumData);

if (!empty($CardNumData)){
	if (!empty($CardNumData[0])){
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number for the Case ID, '.$EditCaseID.', Was not found! Please Contact the System Admin.';
		echo '<script> window.location = "index.php";</script>';
		//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
		exit();
	}
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number for the Case ID, '.$EditCaseID.', Was not found! Please Contact the System Admin.';
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}


//Get Transaction Information
$TransactionQuery = "SELECT * FROM checktransactions where transactiondeleted=FALSE AND caseid='" . $EditCaseID . "'";
$TransactionQuery_Data = $dtcon->query($TransactionQuery);
$TransactionData = $TransactionQuery_Data->fetch_all();

//var_dump($TransactionData);

//At the moment No one but the person who started the case can Add a transaction, Only Delete the case and start over.
//You also cant Add a new transaction at all when the case is done being added.
if (empty($TransactionData)){
	$_SESSION["ADD_DISPUTE_ERROR"] = "This Case doesn't have any transactions! Please contact the system Admin!";
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
	
}



//SQL Info End--------------------------------------------------------------------------------------------------------

//Case Edit Information


//var_dump($CaseData);
//var_dump($AccountNumData);
//var_dump($CardNumData);
//var_dump($TransactionData);
//var_dump($AttachmentData);

//var_dump($_SESSION);

//$NumberofReason
//$Numberof_acct_Types
//$NumberofTypes
//$NumberofStatus
//$NumberofPoss
//$NumberofComp
//$NumberofRever

//var_dump($NumberofReason, $Numberof_acct_Types, $NumberofTypes, $NumberofStatus, $NumberofPoss, $NumberofComp, $NumberofRever);

//var_dump($reason_data);
//var_dump($acct_type_data);
//var_dump($type_data);
//var_dump($status_data);
//var_dump($possession_data);
//var_dump($compromise_data);
//var_dump($reversal_data);

//Values are printed in Small segments where they need to be so that they can be edited.

?>

	<div class="row">
		<div class="col-md-2">
			<h4>Case ID: <?php print $EditCaseID; ?></h4>
		</div>
	</div><hr />
	<div class="row">
		<div class="col-md-2">
		
		<!-- 2 Buttons, One to Skip to Transactions without editing the Case and one to Delete the Case and All Transactions -->
		
			<form id="EditDisputeForm_ID" autocomplete="off" name="EditDisputeSkipForm" method="post" action="editDisputeTransactions.php">
				<input type='hidden' name='caseID' value='<?php print $EditCaseID; ?>'>
				<input type="submit" class="btn btn-success" name="SkipCaseSubmit" value="Skip Case Change">
			</form>
		</div>
		<div class="col-md-2">
			<form id="DeleteDisputeForm_ID" autocomplete="off" name="DeleteisputeSkipForm" method="post" action="editDisputeTransactions.php">
				<input type='hidden' name='caseID' value='<?php print $EditCaseID; ?>'>
				<input type="submit" class="btn btn-danger" name="DeleteCase" value="Delete Case" onclick="return confirm('Click OK to confirm deletion.')">
			</form>
		</div>
		
		<div class="col-md-2">
			<form id="ViewDisputeForm_ID" autocomplete="off" name="ViewDisputeForm" method="post" action="DisputeViewOnly.php">
				<input type='hidden' name='ViewSubmit' value='<?php print $EditCaseID; ?>'>
				<input type="submit" class="btn btn-primary" name="ViewCase" value="Back to View Case">
			</form>
		</div>
		
		<div class="col-md-2">
			<form id="RePrintDisputeFormForm_ID" autocomplete="off" name="RePrintDisputeFormForm" method="post" action="generateDisputeForm.php" onsubmit="showLoading();">
				<input type='hidden' name='redoDisputeForm' value='<?php print $EditCaseID; ?>'>
				<input type="submit" class="btn btn-ZnewFour" name="ReprintDisputeForm" value="Reprint the VISA Dispute Form">
			</form>
		</div>
		
	</div><hr />
	
	<form id="EditDisputeForm_ID" autocomplete="off" name="EditDisputeForm" method="post" action="editDisputeTransactions.php">
	
	<input type='hidden' name='caseID' value='<?php print $EditCaseID; ?>'>
	
	<div class="row">
		<div class="col-md-3">
			<label>Dispute Case Start Day:</label>&nbsp;<a target="_blank" title="If the Start day is wrong, please set the correct start day. This value has to be in the past."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<input type="text" class="form-control" name="DisputeCaseDay" maxlength="255" onFocus="showCalendarControl(this);" value="<?php print $CaseData[0][1]; ?>">
		</div>
	</div><hr />
	
	<div class="row">
		<div class="col-md-3">
			<label>First Name</label>&nbsp;<a target="_blank" title="Names must be letters only. No digits or special characters. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<input type="text" class="form-control" name="FirstName" maxlength="255" value="<?php print $CaseData[0][2]; ?>" id="firstname" pattern="^[a-zA-Z ,.'-]+$" title="Letters Only" onblur="this.value = this.value.toUpperCase();" required>
		</div>
		<div class="col-md-3">
			<label>Last Name</label>&nbsp;<a target="_blank" title="Names must be letters only. No digits or special characters. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<input type="text" class="form-control" name="LastName" maxlength="255" value="<?php print $CaseData[0][3]; ?>" id="lastname" pattern="^[a-zA-Z ,.'-]+$" title="Letters Only" onblur="this.value = this.value.toUpperCase();" required>
		</div>
		<div class="col-md-3">
			<label>Phone Number</label>&nbsp;<a target="_blank" title="Phone numbers can be in one of the following format: '123 456 7890', '123-456-7890', '1234567890'. The form will autocomplete with dashes. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<input type="text" class="form-control" name="PhoneNum" maxlength="255" value="<?php print $CaseData[0][4]; ?>" id="phonenum" pattern="^[0-9]{3}[ \-]{0,1}[0-9]{3}[ \-]{0,1}[0-9]{4}$" onblur="phonenumChange(this);" title="'123 456 7890', '123-456-7890', '1234567890'" required>
		</div>
		<div class="col-md-3">
			<label>Email Address</label>&nbsp;<a target="_blank" title="Email can can in the following formats: 'test.test@email.co', 'test@email.com'. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<input type="text" class="form-control" name="EmailVar" maxlength="255" value="<?php print $CaseData[0][5]; ?>" id="emailaddr" title="'test.test@email.co', 'test@email.com'" onblur="this.value = this.value.toUpperCase();">
		</div>
	</div><hr />
	
	<div class="row">
		<div class="col-md-3">
			<label>Address One:</label>&nbsp;<a target="_blank" title="The address can contain digits, letters, and some special characters. ex.: '1234 Main', '123 Main St. Apt. 7'. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<input type="text" class="form-control" name="AddressOne" maxlength="255" value="<?php print $CaseData[0][6]; ?>" id="addr1" pattern="^[a-zA-Z0-9.\-\s]+$" title="'1234 Main', '123 Main St. Apt. 7'" onblur="this.value = this.value.toUpperCase();" required>
		</div>
		<div class="col-md-3">
			<label>Address Two:</label>&nbsp;<a target="_blank" title="The address can contain digits, letters, and some special characters. ex.: 'Apt. 7'."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<input type="text" class="form-control" name="AddressTwo" maxlength="255" value="<?php print $CaseData[0][7]; ?>" id="addr2" pattern="^[a-zA-Z0-9.\-\s]+$" title="'Apt. 7'" onblur="this.value = this.value.toUpperCase();">
		</div>
		<div class="col-md-2">
			<label>City</label>&nbsp;<a target="_blank" title="The city name can contain letters and spaces only. Ex.: 'Hopkinsville', 'Bowling Green'. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<input type="text" class="form-control" name="CityAddr" maxlength="255" value="<?php print $CaseData[0][8]; ?>" id="city" pattern="^[a-zA-Z ]+$" title="'Hopkinsville', 'Bowling Green'" onblur="this.value = this.value.toUpperCase();" required>
		</div>
		<div class="col-md-2">
			<label>State</label>&nbsp;<a target="_blank" title="Please select the state. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<select name="StateAddr" class="form-control">
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
			<label>Zip Code:</label>&nbsp;<a target="_blank" title="Please enter the 5 digit zip code. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<input type="text" class="form-control" name="ZipCode" maxlength="5" value="<?php print $CaseData[0][10]; ?>" id="zip" pattern="^[0-9]+$" title="'42240'" onblur="this.value = this.value.toUpperCase();" required>
		</div>
	</div><hr />
	
	<div class="row">
		<div class="col-md-3">
			<label>Is the Customer in person or on the phone?</label>&nbsp;<a target="_blank" title="Is the customer starting this dispute over the phone or in person? This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<select name="inpersonorPhone" class="form-control">
<?php

if($CaseData[0][16] == '1'){
	print '<option value="inperson" selected="true">In Person</option>';
	print '<option value="phone">On Phone</option>';
}
else if($CaseData[0][16] == '2'){
	print '<option value="inperson">In Person</option>';
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
		<!--
		<div class="col-md-2">
			<label>Dispute Case Type:</label>
			<input type="text" class="form-control2" name='checkACH' value="Check Card" readonly>
		</div>
		-->
		
		<div class="col-md-3">
			<label>Is this Account New?</label>&nbsp;<a target="_blank" title="Please select if the account has been recently opened. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<select name="newAccount" class="form-control">
<?php

if($AccountNumData[0][6] == 0){
	print '<option value="newAccTrue">Account is New</option>';
	print '<option value="newAccFalse" selected="true">Account is not New</option>';
}
else if($AccountNumData[0][6] == 1){
	print '<option value="newAccTrue" selected="true">Account is New</option>';
	print '<option value="newAccFalse">Account is not New</option>';
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
			<label>Case Comments</label>&nbsp;<a target="_blank" title="This field is for comments about the case. This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<textarea rows="5" class="form-control" name="CaseComments" onblur="this.value = this.value.toUpperCase();" rows="3" maxlength="4096"><?php print $CaseData[0][13]; ?></textarea>
		</div>
		
	</div><hr />
	
	<div class="row">
		<div class="col-md-2">
			<label>Is the Case Red Flagged</label>&nbsp;<a target="_blank" title="Please select whether the case is red flagged. The case is set to not flagged by default."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<select name="RedFlag" class="form-control">
<?php

if($CaseData[0][11] == 1){
	print '<option value="Positive" selected="true">Case Red Flagged</option>';
	print '<option value="Negative">Case Not Flagged</option>';
}
else if($CaseData[0][11] == 0){
	print '<option value="Positive">Case Red Flagged</option>';
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
			<label>Sev Lev</label>&nbsp;<a target="_blank" title="Please enter the Sev Lev. The default is 0. This is a required field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<input type="text" class="form-control" name="SevLevVar" maxlength="255" value="<?php print $CaseData[0][12]; ?>" id="sevlev" pattern="^[0-9]+$" title="'1', '2', '5'" required>
		</div>
		
	</div><hr />
	
	<div class="row">
		<div class="col-md-2">
			<label>User Started:</label>
			<input type="text" class="form-control2" value="<?php print $CaseData[0][14]; ?>" readonly>
		</div>
		<div class="col-md-2">
			<label>PC Letter Print</label>&nbsp;<a target="_blank" title="This field is to print provisional credit letters for each case. This field is set to not flagged to print by default. The program will automatically make letters every night. There is a link to the left side to manually create letters. When the letter is made, this value will be set back to not flagged to print."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<select name="PCPrintFlag" class="form-control">
<?php 

if($CaseData[0][17] == 1){
	print '<option value="Positive" selected="true">Case Flagged to Print</option>';
	print '<option value="Negative">Case Not Flagged to Print</option>';
}
else if($CaseData[0][17] == 0){
	print '<option value="Positive">Case Flagged to Print</option>';
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
			<label>Case Closed</label>&nbsp;<a target="_blank" title="This field is to close the case. This does not delete the case. This will close the case to say the case is 'done'. The default value is set to not closed."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<select name="CaseClosed" class="form-control">
<?php

if($CaseData[0][18] == 1){
	print '<option value="Positive" selected="true">Case is Closed</option>';
	print '<option value="Negative">Case is not Closed</option>';
}
else if($CaseData[0][18] == 0){
	print '<option value="Positive">Case is Closed</option>';
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
	</div><hr /><br><br>
	
	<!-- Account Number Info -->
	
	<div class="row">
		<div class="col-md-2">
			<label>Account Number</label>&nbsp;<a target="_blank" title="Please enter the customers account number. This field accepts digits only. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<input type="text" class="form-control" name="AccountNumber" maxlength="12" value="<?php print $AccountNumData[0][2]; ?>" id="accountnum" pattern="^[0-9]+$" title="'1234', '123456789012'" required>
		</div>
		<div class="col-md-2">
			<label>Consumer or Business Account:</label>&nbsp;<a target="_blank" title="Please select whether the account is a consumer account or business account. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<select name="BusiCustType" class="form-control">
<?php

if($AccountNumData[0][4] == 1){
	print '<option value="Consumer">Consumer</option>';
	print '<option value="Business" selected="true">Business</option>';
}
else if($AccountNumData[0][4] == 0){
	print '<option value="Consumer" selected="true">Consumer</option>';
	print '<option value="Business">Business</option>';
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
			<label>Account Type:</label>&nbsp;<a target="_blank" title="Please select the account type. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<select name="AccountType" class="form-control">
<?php

for ($i=0; $i < $Numberof_acct_Types; $i++){
	
	if($acct_type_data[$i][0] == $AccountNumData[0][3]){
		print '<option value="' . $acct_type_data[$i][0] . '" selected="true">' . $acct_type_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $acct_type_data[$i][0] . '">' . $acct_type_data[$i][1] . '</option>';
	}
}

?>
			</select>
		</div>
		<div class="col-md-6">
			<label>Account Comments</label>&nbsp;<a target="_blank" title="This field is for comments about the account(s). This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<textarea rows="5" class="form-control" onblur="this.value = this.value.toUpperCase();" name="AccountComments" rows="3" maxlength="4096"><?php print $AccountNumData[0][5]; ?></textarea>
		</div>
	</div><hr />
	
	<!-- Card Number Info -->
	
	<div class="row">	
		<div class="col-md-2">
			<label>Card Number</label>&nbsp;<a target="_blank" title="This field is for check card numbers. It accepts 16 digits exactly. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<input type="text" class="form-control" name="Card_Number" maxlength="16" value="<?php print $CardNumData[0][2]; ?>" id="cardnum" pattern="^\d{16}$" title="'1234567890123456'" required>
		</div>
		<div class="col-md-2">
			<label>Is the card a chip card?</label>&nbsp;<a target="_blank" title="Please select if the card is a chip card or not.  This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<select name="ChipCard" class="form-control">
<?php

if($CardNumData[0][7] == 0){
	print '<option value="yes">Yes</option>';
	print '<option value="no" selected="true">No</option>';
}
else if($CardNumData[0][7] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no">No</option>';
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
			<label>Card Type</label>&nbsp;<a target="_blank" title="Please Select the card type. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<select name="CardType" class="form-control">
<?php

for ($i=0; $i < $NumberofTypes; $i++){
	
	if($type_data[$i][0] == $CardNumData[0][3]){
		print '<option value="' . $type_data[$i][0] . '" selected="true">' . $type_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $type_data[$i][0] . '">' . $type_data[$i][1] . '</option>';
	}
}

?>
			</select>
		</div>
		<div class="col-md-2">
			<label>Current Card Status</label>&nbsp;<a target="_blank" title="Please select the card status. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<select name="CardStatus" class="form-control">
<?php

for ($i=0; $i < $NumberofStatus; $i++){
	
	if($status_data[$i][0] == $CardNumData[0][4]){
		print '<option value="' . $status_data[$i][0] . '" selected="true">' . $status_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $status_data[$i][0] . '">' . $status_data[$i][1] . '</option>';
	}
}

?>
			</select>
		</div>
		<div class="col-md-2">
			<label>Card Possession</label>&nbsp;<a target="_blank" title="Please select the card possession. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<select id="CardPossessionID" name="CardPossession" onchange="addDate();" class="form-control">
<?php

for ($i=0; $i < $NumberofPoss; $i++){
	
	if($possession_data[$i][0] == $CardNumData[0][5]){
		print '<option value="' . $possession_data[$i][0] . '" selected="true">' . $possession_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $possession_data[$i][0] . '">' . $possession_data[$i][1] . '</option>';
	}
}

?>		
			</select>
		</div>
		<div class="col-md-2" id="possDate">
			<label>Card Missing Date</label>&nbsp;<a target="_blank" title="If the card possession is lost or stolen, please select approximately when the card went missing. This field is only required when the card possession is lost or stolen."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<input type="text" class="form-control" id="possessionDateID" name="possessionDate" onFocus="showCalendarControl(this);" value="<?php print $CardNumData[0][6]; ?>">
		</div>
	</div><hr />
		
	<div class="row">
		<div class="col-md-6">
			<label>Card Comments</label>&nbsp;<a target="_blank" title="This field is for comments about the card(s). This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
			<textarea rows="5" class="form-control" onblur="this.value = this.value.toUpperCase();" name="CardComments" rows="3" onfocus="this.select();" maxlength="4096"><?php print $CardNumData[0][8]; ?></textarea>
		</div>
	</div><hr />
		
	<input type="submit" class="btn btn-primary" name="EditSubmit" value="Submit Changes">

			</form>
			
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
<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');


if ($accesslevel < 3){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}


//Check for open disputes

//Edit Old dispute
$unFinishedCases = "SELECT id FROM checkcases WHERE userstarted='".$username."' AND casedoneinput=FALSE AND casedeleted=FALSE";
$UnfinishedFetch = $dtcon->query($unFinishedCases);

$unfinishedItems = $UnfinishedFetch->fetch_all();

$numofItems = count($unfinishedItems);

//var_dump($unfinishedItems);
//exit();

if($numofItems == 0){
	//No Cases Open, Do nothing
}
else if($numofItems == 1){
	
	$_SESSION["WorkingDispute"] = 'True';
	$_SESSION["Dispute_CaseID"] = $unfinishedItems[0][0];
	
	$unfishedTransactions = "SELECT id FROM checktransactions WHERE caseid='".$_SESSION["Dispute_CaseID"]."'";
	$UnfinishedTransFetch = $dtcon->query($unfishedTransactions);
	
	$unfinishedTransactionItems = $UnfinishedTransFetch->fetch_all();
	
	$numofItems = count($unfinishedItems);
	
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You still have a case unfinished. Please finish this Case.';
	
	if($numofItems == 0){
		$_SESSION["ADD_DISPUTE_ERROR"] = $_SESSION["ADD_DISPUTE_ERROR"] . "<br>Please Add a transaction to continue, or contact the System Admin.";
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = $_SESSION["ADD_DISPUTE_ERROR"] . "<br>Please Add a transaction to continue or click the green Next button to finish.";
		$_SESSION["CaseCanBeDone"] = 'TRUE';
	}
	
	header( "Location: newDisputeTransactions.php" );
	exit();
	
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "You have more than 1 case open! Please contact the System Admin before you attempt to complete another case!";
	header( "Location: DisputetrackError.php" );
	exit();
	
}



unset($_SESSION["WorkingDispute"]);
unset($_SESSION["Dispute_CaseID"]);
unset($_SESSION['FinishDispute']);
unset($_SESSION['CaseCanBeDone']);
//unset($_SESSION['ADD_DISPUTE_ERROR']);



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
   
   
   <script type="text/javascript">
   
		function addDate() {
			
			var currSelectID = document.getElementById('CardPossessionID');
			var currValue = currSelectID.options[currSelectID.selectedIndex].value;
			
			if (currValue == "select" || currValue == "1" || currValue == ""){
				
				var x = document.getElementById('possDate');
				
				x.style.display = 'none';
			
				document.getElementById('possessionDateID').value = '';
				
			}
			else{
				
				var x = document.getElementById('possDate');
			
				x.style.display = 'block';
				
			}
		}
		
		//Unused as of 1/17/2017
		function cleanData(stringVar){
			
			var CleanstringVar = "";
			var stringTemp = "";
			
			stringTemp = stringVar.replace("/=/g", "");
			CleanstringVar = stringTemp.tolowercase();
			stringTemp = CleanstringVar.replace("/drop table/g", "");
			CleanstringVar = stringVar.replace("/;/g", "");
			
			
			return CleanstringVar;
		}
		
		
		
		function isValidDate(dateString){
			// First check for the pattern
			var regex_date = /^\d{4}\-\d{1,2}\-\d{1,2}$/;

			if(!regex_date.test(dateString))
			{
				return false;
			}

			// Parse the date parts to integers
			var parts   = dateString.split("-");
			var day     = parseInt(parts[2], 10);
			var month   = parseInt(parts[1], 10);
			var year    = parseInt(parts[0], 10);

			// Check the ranges of month and year
			if(year < 1000 || year > 3000 || month == 0 || month > 12)
			{
				return false;
			}

			var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

			// Adjust for leap years
			if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
			{
				monthLength[1] = 29;
			}

			// Check the range of the day
			if (!(day > 0 && day <= monthLength[month - 1])){
				return false;
			}
			
			return true;
			
			
		}
		
		
		function validate(form){
			
			var successful_bool = false;
			
			var DisputeDay = form.DisputeDay.value;
			
			var firstname = form.FirstName.value;
			var lastname = form.LastName.value;
			var PhoneNumberVAR = form.phoneNumber.value;
			var EmailAddrVAR = form.emailAddr.value;
			
			var AddrOneVAR = form.AddressOne.value;
			var AddrTwoVAR = form.AddressTwo.value;
			var CityAddrVAR = form.CityAddr.value;
			var StateAddrVAR = form.StateAddr.value;
			var ZipCodeVAR = form.ZipCode.value;
			
			var custinpersonphone = form.inpersonorPhone.value;
			var checkACH = form.checkACH.value;
			
			var accountNumber = form.AccountNumber.value;
			var accountType = form.AccountType.value;
			var consumerorBusiness = form.BusiCustType.value;
			var accountcomments = form.AccountComments.value;
			
			var CARDNumber = form.Card_Number.value;
			var CARDType = form.CardType.value;
			var CARDstatus = form.CardStatus.value;
			var CARDpossession = form.CardPossessionID.value;
			var CARDmissingdate = form.possessionDateID.value;
			var CARDchipVAR = form.ChipCard.value;
			var CARDnumbercomments = form.CardComments.value;
			
			
			//First name tester -----------------------------------------------------------------------------May need more validation
			if (/^[a-zA-Z ,.'-]+$/.test(firstname)){
				if (firstname.length > 0 && firstname.length < 255){
					successful_bool = true;
				}
				else{
					alert("First Name can't be longer than 255 characters!");
					successful_bool = false;
					return false;
				}
			}
			else{
				alert("Names must be letters only.");
				successful_bool = false;
				return false;
			}
			//Last name tester ------------------------------------------------------------------------------May need more validation
			if (/^[a-zA-Z ,.'-]+$/.test(lastname)){
				if (lastname.length > 0 && lastname.length < 255){
					successful_bool = true;
				}
				else{
					alert("Last Name can't be longer than 255 characters!");
					successful_bool = false;
					return false;
				}
			}
			else{
				alert("Names must be letters only.");
				successful_bool = false;
				return false;
			}
			//Phone -----------------------------------------------------------------------------------------May need more validation
			if(/^[0-9]{3}[ \-]{0,1}[0-9]{3}[ \-]{0,1}[0-9]{4}$/.test(PhoneNumberVAR)){
				successful_bool = true;
			}
			else{
				alert("Phone Number must be 10 digits with slashes, spaces, parentheses, or dashes only. Ex: '123 456 7890', '123-456-7890', '1234567890'");
				successful_bool = false;
				return false;
			}
			//Email -----------------------------------------------------------------------------------------May need more validation
			if((/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(EmailAddrVAR)) || EmailAddrVAR==""){
				successful_bool = true;
			}
			else{
				alert("Email address Error. Please try again.");
				successful_bool = false;
				return false;
			}
			//Address One ---------------------------------------------------------------------------------------
			if (/^[a-zA-Z0-9.\-\s]+$/.test(AddrOneVAR)){
				if (AddrOneVAR.length > 0 && AddrOneVAR.length < 255){
					successful_bool = true;
				}
				else{
					alert("The Address can't be longer than 255 characters!");
					successful_bool = false;
					return false;
				}
			}
			else{
				alert("The Address must be letters and numbers only.");
				successful_bool = false;
				return false;
			}
			//Address Two ---------------------------------------------------------------------------------------
			if (/^[a-zA-Z0-9.\-\s]+$/.test(AddrTwoVAR) || AddrTwoVAR.length == 0){
				if (AddrTwoVAR.length >= 0 && AddrTwoVAR.length < 255){
					successful_bool = true;
				}
				else{
					alert("The Address can't be longer than 255 characters!");
					successful_bool = false;
					return false;
				}
			}
			else{
				alert("The Address must be letters only.");
				successful_bool = false;
				return false;
			}
			//City ---------------------------------------------------------------------------------------
			if (/^[a-zA-Z ]+$/.test(CityAddrVAR)){
				if (CityAddrVAR.length > 0 && CityAddrVAR.length < 255){
					successful_bool = true;
				}
				else{
					alert("The City Name can't be longer than 255 characters!");
					successful_bool = false;
					return false;
				}
			}
			else{
				alert("The City Name must be letters only.");
				successful_bool = false;
				return false;
			}
			//State ---------------------------------------------------------------------------------------
			if (/^[a-zA-Z]+$/.test(StateAddrVAR)){
				if (StateAddrVAR.length == 2){
					successful_bool = true;
				}
				else{
					alert("The State Must be Selected");
					successful_bool = false;
					return false;
				}
			}
			else{
				alert("The State must be Selected.");
				successful_bool = false;
				return false;
			}
			//Zip Code ---------------------------------------------------------------------------------------
			if (/^[0-9]+$/.test(ZipCodeVAR)){
				if (ZipCodeVAR.length > 0 && ZipCodeVAR.length < 6){
					successful_bool = true;
				}
				else{
					alert("The Zip code must be entered");
					successful_bool = false;
					return false;
				}
			}
			else{
				alert("The Zip Code must be numbers only.");
				successful_bool = false;
				return false;
			}
			
			
			//Customer on Phone or in Person  ---------------------------------------------------------------
			if (custinpersonphone == "inperson" || custinpersonphone == "phone"){
				successful_bool = true;
			}
			else{
				alert("You must select if the customer is on the phone or in person!");
				successful_bool = false;
				return false;
			}
			//Check or ACH Dispute  -------------------------------------------------------------------------
			//if (checkACH == "ACH" || checkACH == "Check"){
			//	successful_bool = true;
			//}
			//else{
			//	alert("You must select if the dispute is ACH or a Check dispute!");
			//	successful_bool = false;
			//	return false;
			//}
			//Account Number  -------------------------------------------------------------------------------May need more validation
			if (/^[0-9]+$/.test(accountNumber)){
				if (accountNumber.length > 0 && accountNumber.length < 30){
					successful_bool = true;
				}
				else{
					alert("Account Number can't be longer than 12 digits!");
					successful_bool = false;
					return false;
				}
			}
			else{
				alert("Account Number must be digits only");
				successful_bool = false;
				return false;
			}
			//Account Type ----------------------------------------------------------------------------------
			if (accountType != ""){
				successful_bool = true;
			}
			else{
				alert("You must select the account type!");
				successful_bool = false;
				return false;
			}
			//Business Acount -------------------------------------------------------------------------------
			if(consumerorBusiness == "Consumer" || consumerorBusiness == "Business"){
				successful_bool = true;
			}
			else{
				alert("You must select if the account is a consumer account or business account");
				successful_bool = false;
				return false;
			}
			//Acount Comments -------------------------------------------------------------------------------May need more validation
			if (/^[\x00-\x7F]*$/.test(accountcomments)){
				successful_bool = true;
			}
			else{
				alert("You must input only regular characters in the Account Comments.");
				successful_bool = false;
				return false;
			}
			//Card Number -----------------------------------------------------------------------------------May need more validation
			if (/^[0-9]+$/.test(CARDNumber)){
				if (CARDNumber.length > 15 && CARDNumber.length < 17){
					successful_bool = true;
				}
				else{
					alert("Card Numbers has to be 16 digits!");
					successful_bool = false;
					return false;
				}
			}
			else{
				alert("Card Numbers must be digits only");
				successful_bool = false;
				return false;
			}
			//Card Type ------------------------------------------------------------------------------------
			if (CARDType == ""){
				alert("You must select the card type!");
				successful_bool = false;
				return false;
			}
			else{
				successful_bool = true;
			}
			//Card status ----------------------------------------------------------------------------------
			if (CARDstatus == ""){
				alert("You must select the card status!");
				successful_bool = false;
				return false;
			}
			else{
				successful_bool = true;
			}
			//Card possession ------------------------------------------------------------------------------
			if (CARDpossession == ""){
				alert("You must select if the card is missing!");
				successful_bool = false;
				return false;
			}
			else{
				successful_bool = true;
			}
			//Card Possession ------------------------------------------------------------------------------
			if(CARDchipVAR == "yes" || CARDchipVAR == "no"){
				successful_bool = true;
			}
			else{
				alert("You must select if the card is a chip card!");
				successful_bool = false;
				return false;
			}
			//Card missing date ----------------------------------------------------------------------------May need more validation
			if (CARDpossession == "" || CARDpossession == "1"){
				document.getElementById('possessionDateID').value = '';
				successful_bool = true;
			}
			else{
				if (/^[0-9-]+$/.test(CARDmissingdate)){
					if (isValidDate(CARDmissingdate)){
						successful_bool = true;
					}
					else{
						alert("Date must be in format 'YYYY-MM-DD'.");
						successful_bool = false;
						return false;
					}
				}
				else{
					alert("Date must be in format 'YYYY-MM-DD'.");
					successful_bool = false;
					return false;
				}
			}
			//Card comments --------------------------------------------------------------------------------May need more validation
			if (/^[\x00-\x7F]*$/.test(CARDnumbercomments)){
				successful_bool = true;
			}
			else{
				alert("You must select if the account is a consumer account or business account");
				successful_bool = false;
				return false;
			}
			
			
			
			
			
			if (successful_bool){
				//document.newDisputeForm.submit();
				return true;
			}
			else{
				return false;
			}
			
			return false;
			
		}
		
		
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
		
		
   </script>
   
   
</head>
<body>
     
           
          
    <div id="wrapper">
	
        <?php include("assets/includes/head+menu.php"); ?>
		
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-2">
                     <h2>New Dispute</h2>   
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
				
                 <!-- /. ROW  -->
                  <hr />
				  
				  <!--  Main Body  -->

					<form id="newDisputeForm_ID" autocomplete="off" name="newDisputeSubmit" method="post" action="newDisputeTransactions.php" onsubmit="return validate(this);showLoading();">
					
					<input type="hidden" id="DisputeDay" name="DisputeDay" value="<?php date_default_timezone_set("America/Chicago"); print date("Y-m-d, G:i:s"); ?>">
					
					<!-- /. ROW  -->
					
					<!--
						<div class="row">
							<div class="col-md-3">
								<label>Case Number:</label>&nbsp;<a target="_blank" title=""><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="TempCaseNum" maxlength="255" id="caseIDNUMBER" pattern="^[0-9]+$" title="Numbers only." required>
							</div>
						</div>
						<hr />
					-->
						<div class="row">
							<div class="col-md-3">
								<label>First Name</label>&nbsp;<a target="_blank" title="Names must be letters only. No digits or special characters. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="FirstName" maxlength="255" id="firstnameid" pattern="^[a-zA-Z ,.'-]+$" title="Letters only." onblur="this.value = this.value.toUpperCase();" required>
								
							</div>
							<div class="col-md-3">
								<label>Last Name</label>&nbsp;<a target="_blank" title="Names must be letters only. No digits or special characters. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="LastName" maxlength="255" id="lastnameid" onblur="this.value = this.value.toUpperCase();" pattern="^[a-zA-Z ,.'-]+$" title="Letters only." required>
							
							</div>
							<div class="col-md-3">
								<label>Phone Number</label>&nbsp;<a target="_blank" title="Phone numbers can be in one of the following format: '123 456 7890', '123-456-7890', '1234567890'. The form will autocomplete with dashes. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="phoneNumber" maxlength="255" id="phonenum" pattern="^[0-9]{3}[ \-]{0,1}[0-9]{3}[ \-]{0,1}[0-9]{4}$" title="'123 456 7890', '123-456-7890', '1234567890'" onblur="phonenumChange(this);" required>
							
							</div>
							<div class="col-md-3">
								<label>Email</label>&nbsp;<a target="_blank" title="Email can can in the following formats: 'test.test@email.co', 'test@email.com'. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="emailAddr" onblur="this.value = this.value.toUpperCase();" id="emailaddrid" title="'test.test@email.co', 'test@email.com'">
							
							</div>
							
						</div>
						
						<hr />
						<div class="row">
							<div class="col-md-3">
								<label>Address One (Mailing Address):</label>&nbsp;<a target="_blank" title="The address can contain digits, letters, and some special characters. ex.: '1234 Main', '123 Main St. Apt. 7'. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="AddressOne" maxlength="255" id="addr1" pattern="^[a-zA-Z0-9.\-\s]+$" title="'1234 Main', '123 Main St. Apt. 7'" onblur="this.value = this.value.toUpperCase();" required>
								
							</div>
							<div class="col-md-3">
								<label>Address Two:</label>&nbsp;<a target="_blank" title="The address can contain digits, letters, and some special characters. ex.: 'Apt. 7'."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="AddressTwo" maxlength="255" id="addr2" pattern="^[a-zA-Z0-9.\-\s]+$" title="'Apt. 7'" onblur="this.value = this.value.toUpperCase();">
							
							</div>
							<div class="col-md-2">
								<label>City:</label>&nbsp;<a target="_blank" title="The city name can contain letters and spaces only. Ex.: 'Hopkinsville', 'Bowling Green'. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="CityAddr" maxlength="255" id="city" pattern="^[a-zA-Z ]+$" title="'Hopkinsville', 'Bowling Green'" onblur="this.value = this.value.toUpperCase();" required>
							
							</div>
							<div class="col-md-2">
								<label>State</label>&nbsp;<a target="_blank" title="Please select the state. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<select name="StateAddr" class="form-control" required>
										<option value="" selected="true">Select One</option>
										<option value="KY">Kentucky</option>
										<option value="TN">Tennessee</option>
										<option value="AL">Alabama</option>
										<option value="AK">Alaska</option>
										<option value="AZ">Arizona</option>
										<option value="AR">Arkansas</option>
										<option value="CA">California</option>
										<option value="CO">Colorado</option>
										<option value="CT">Connecticut</option>
										<option value="DE">Delaware</option>
										<option value="DC">District Of Columbia</option>
										<option value="FL">Florida</option>
										<option value="GA">Georgia</option>
										<option value="HI">Hawaii</option>
										<option value="ID">Idaho</option>
										<option value="IL">Illinois</option>
										<option value="IN">Indiana</option>
										<option value="IA">Iowa</option>
										<option value="KS">Kansas</option>
										<option value="LA">Louisiana</option>
										<option value="ME">Maine</option>
										<option value="MD">Maryland</option>
										<option value="MA">Massachusetts</option>
										<option value="MI">Michigan</option>
										<option value="MN">Minnesota</option>
										<option value="MS">Mississippi</option>
										<option value="MO">Missouri</option>
										<option value="MT">Montana</option>
										<option value="NE">Nebraska</option>
										<option value="NV">Nevada</option>
										<option value="NH">New Hampshire</option>
										<option value="NJ">New Jersey</option>
										<option value="NM">New Mexico</option>
										<option value="NY">New York</option>
										<option value="NC">North Carolina</option>
										<option value="ND">North Dakota</option>
										<option value="OH">Ohio</option>
										<option value="OK">Oklahoma</option>
										<option value="OR">Oregon</option>
										<option value="PA">Pennsylvania</option>
										<option value="RI">Rhode Island</option>
										<option value="SC">South Carolina</option>
										<option value="SD">South Dakota</option>
										<option value="TX">Texas</option>
										<option value="UT">Utah</option>
										<option value="VT">Vermont</option>
										<option value="VA">Virginia</option>
										<option value="WA">Washington</option>
										<option value="WV">West Virginia</option>
										<option value="WI">Wisconsin</option>
										<option value="WY">Wyoming</option>
								</select>							
							</div>
							<div class="col-md-2">
								<label>Zip Code:</label>&nbsp;<a target="_blank" title="Please enter the 5 digit zip code. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="ZipCode" maxlength="5" id="zipid" pattern="^[0-9]+$" title="'42240'" required>
							
							</div>
						</div>
						<hr />
						
						
					<!-- /. ROW  -->
						<div class="row">
							<div class="col-md-4">
								<label>Is the Customer in person or on the phone?</label>&nbsp;<a target="_blank" title="Is the customer starting this dispute over the phone or in person? This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<select name="inpersonorPhone" class="form-control" required>
									<option value="" selected="true">Select One</option>
									<option value="inperson">In Person</option>
									<option value="phone">On Phone</option>
								</select> 
							</div>
							
							<!--
							<div class="col-md-3">
								<label>Is this a Check Card or ACH dispute?</label>&nbsp;<a target="_blank" title=""><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<select name="checkACH" class="form-control" disabled="disabled">
									<option value="">Select One</option>
									<option value="Check" selected="true">Check Card</option>
									<option value="ACH">ACH</option>
								</select> 
								<input type="hidden" id="hiddenCheckACH" name="checkACH" value="Check" />
							</div>
							-->
							
							<div class="col-md-4">
								<label>Is this Account New?</label>&nbsp;<a target="_blank" title="Please select if the account has been recently opened. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<select name="newAccount" class="form-control" required>
									<option value="" selected="true">Select One</option>
									<option value="newAccTrue">Account is New</option>
									<option value="newAccFalse">Account is not New</option>
								</select> 
							</div>
							
						</div>
					<hr />
					<!-- /. ROW  -->
						<div class="row">
							<div class="col-md-2">
								<label>Account Number</label>&nbsp;<a target="_blank" title="Please enter the customers account number. This field accepts digits only. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="AccountNumber" maxlength="12" id="accountid" pattern="^[0-9]+$" title="'1234', '123456789012'" required>
								
							</div>
							<div class="col-md-2">
								<label>Account Type</label>&nbsp;<a target="_blank" title="Please select the account type. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
<?php

$acct_type_query = "SELECT id, typetext FROM accounttype WHERE iddeleted='0'";

$acct_type_query_data = $dtcon->query($acct_type_query);

$acct_type_data = $acct_type_query_data->fetch_all();

//var_dump($acct_type_data);
$Numberof_acct_Types = count($acct_type_data);

print '<select name="AccountType" class="form-control" required>';
print '<option value="" selected="true">Select One</option>';

for ($i=0; $i < $Numberof_acct_Types; $i++){
	
	print '<option value="' . $acct_type_data[$i][0] . '">' . $acct_type_data[$i][1] . '</option>';
	
	
}

print '</select>';


?>
								
							</div>
							<div class="col-md-2">
								<label>Consumer or Business Account</label>&nbsp;<a target="_blank" title="Please select whether the account is a consumer account or business account. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<select name="BusiCustType" class="form-control" required> 
									<option value="" selected="true">Select One</option>
									<option value="Consumer">Consumer</option>
									<option value="Business">Business</option>
								</select> 
								
							</div>
						</div>
						<div class="row" id="AccountComments">
							<div class="col-md-12">
								<label>Account Comments</label>&nbsp;<a target="_blank" title="This field is for comments about the account(s). This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<textarea class="form-control" onblur="this.value = this.value.toUpperCase();" name="AccountComments" rows="4" onfocus="this.select();" maxlength="4096" value=""></textarea>
							</div>
							
						</div>	
							
						<hr />
					<!-- /. ROW  -->
						<div class="row" id="addCard">

							<div class="col-md-2">
								<label>Card Number</label>&nbsp;<a target="_blank" title="This field is for check card numbers. It accepts 16 digits exactly. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="Card_Number" maxlength="16" id="cardnumid" pattern="^\d{16}$" title="16 Digits e.g. '1234567890123456'" required>
								
							</div>
							<div class="col-md-2">
								<label>Is the card a chip card?</label>&nbsp;<a target="_blank" title="Please select if the card is a chip card or not.  This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<select name="ChipCard" class="form-control" required> 
									<option value="" selected="true">Select One</option>
									<option value="yes">Yes</option>
									<option value="no">No</option>
								</select> 
								
							</div>
							<div class="col-md-2">
								<label>Card Type</label>&nbsp;<a target="_blank" title="Please Select the card type. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
<?php

$type_query = "SELECT id, typetext FROM cardtype WHERE iddeleted='0'";

$type_query_data = $dtcon->query($type_query);

$type_data = $type_query_data->fetch_all();

//var_dump($type_data);
$NumberofTypes = count($type_data);

print '<select name="CardType" class="form-control" required>';
print '<option value="" selected="true">Select One</option>';

for ($i=0; $i < $NumberofTypes; $i++){
	
	print '<option value="' . $type_data[$i][0] . '">' . $type_data[$i][1] . '</option>';
	
	
}

print '</select>';


?>
							</div>
							<div class="col-md-2">
								<label>Current Card Status</label>&nbsp;<a target="_blank" title="Please select the card status. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
<?php


$status_query = "SELECT id, statustext FROM cardstatus WHERE iddeleted='0'";

$status_query_data = $dtcon->query($status_query);

$status_data = $status_query_data->fetch_all();

//var_dump($status_data);
$NumberofStatus = count($status_data);

print '<select name="CardStatus" class="form-control" required>';
print '<option value="" selected="true">Select One</option>';

for ($i=0; $i < $NumberofStatus; $i++){
	
	print '<option value="' . $status_data[$i][0] . '">' . $status_data[$i][1] . '</option>';
	
	
}

print '</select>';



?>
							</div>
							<div class="col-md-2">
								<label>Card Possession</label>&nbsp;<a target="_blank" title="Please select the card possession. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
<?php


$possession_query = "SELECT id, possessiontext FROM cardpossession WHERE iddeleted='0'";

$possession_query_data = $dtcon->query($possession_query);

$possession_data = $possession_query_data->fetch_all();

//var_dump($possession_data);
$NumberofPoss = count($possession_data);

print '<select id="CardPossessionID" name="CardPossession" class="form-control" onchange="addDate();" required>';
print '<option value="" selected="true">Select One</option>';

for ($i=0; $i < $NumberofPoss; $i++){
	
	print '<option value="' . $possession_data[$i][0] . '">' . $possession_data[$i][1] . '</option>';
	
	
}

print '</select>';



?>
							</div>
							<div class="col-md-2" id="possDate" style="display:none">
								<label>Card Missing Date</label>&nbsp;<a target="_blank" title="If the card possession is lost or stolen, please select approximately when the card went missing. This field is required when the card possession is lost or stolen."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" id="possessionDateID" name="possessionDate" onFocus="showCalendarControl(this);" onchange="changeFunc(this.value)" value="" id="cardmissdateid" pattern="^[0-9-]+$" title="'YYYY-MM-DD'"> 
							</div>

							
						</div>	
							
							
						<div class="row" id="CardCommentsID">
							<div class="col-md-12">
								<label>Card Comments</label>&nbsp;<a target="_blank" title="This field is for comments about the card(s). This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<textarea class="form-control" onblur="this.value = this.value.toUpperCase();" name="CardComments" rows="4" onfocus="this.select();" maxlength="4096"></textarea>
							</div>
							
						</div>	

						
					<hr />
							
							
							
						<input type="submit" class="btn btn-success" name="newDisputeSubmit" value="Next">  <!--  onclick="validate();" -->

					
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

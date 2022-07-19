<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');


if ($accesslevel < 5){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}



$EditCaseID = 0;
$TransactionIDnumber = 0;
$BacktoCaseBOOL = FALSE;


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


if ($_SERVER["REQUEST_METHOD"] == "POST"){
	
	//var_dump($_POST);
	
	$changedBool = FALSE;
	
	if(isset($_POST["EditSubmit"])){
		
		date_default_timezone_set("America/Chicago");
		$timeofChange = date("Y-m-d, G:i:s");
		
		//Case Information needs to be changed, go to transaction data here.
		
		//var_dump($_POST);
		//var_dump($_SESSION);
		//var_dump($NumberofReason, $Numberof_acct_Types, $NumberofTypes, $NumberofStatus, $NumberofPoss, $NumberofComp, $NumberofRever);
		//exit();
		
		
		$EditCaseID = $_POST["caseID"];
		
		//var_dump($EditCaseID);
		
		$TransactionIDnumber = 1;
		
		
		//SQL Info--------------------------------------------------------------------------------------------------------

		$CaseQuery = "SELECT * FROM checkcases where id='" . $EditCaseID . "'";
		$CaseQuery_Data = $dtcon->query($CaseQuery);
		$CaseData = $CaseQuery_Data->fetch_all();

		//var_dump($CaseData);

		if (!empty($CaseData)){
			if (!empty($CaseData[0])){
				
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Information for the Case ID, '.$EditCaseID.', Was not found! Please Contact the System Admin.';
				header( "Location: viewDisputes.php" );
				exit();
			}
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Information for the Case ID, '.$EditCaseID.', Was not found! Please Contact the System Admin.';
			header( "Location: viewDisputes.php" );
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
				header( "Location: viewDisputes.php" );
				exit();
			}
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID, '.$EditCaseID.', Was not found! Please Contact the System Admin.';
			header( "Location: viewDisputes.php" );
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
				header( "Location: viewDisputes.php" );
				exit();
			}
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number for the Case ID, '.$EditCaseID.', Was not found! Please Contact the System Admin.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		
		//SQL Info--------------------------------------------------------------------------------------------------------
		
		
		//Current Data:
		$CaseDataLog = "PREVIOUS Case Data - Case Number: ".$CaseData[0][0]." - Case Start Date: ".$CaseData[0][1]." - Customer First Name: ".$CaseData[0][2]." - Customer Last Name: ".$CaseData[0][3]." - Customer Phone: ".$CaseData[0][4]." - Customer Email: ".$CaseData[0][5]." - Customer Address 1: ".$CaseData[0][6]." - Customer Address 2: ".$CaseData[0][7]." - Customer City: ".$CaseData[0][8]." - Customer State: ".$CaseData[0][9]." - Customer Zip: ".$CaseData[0][10]." - Red Flag Status: ".$CaseData[0][11]." - Case Sev Lev: ".$CaseData[0][12]." - Case Comments: ".$CaseData[0][13]." - User Started: ".$CaseData[0][14]." - Case Done Input: ".$CaseData[0][15]." - Customer Start Method: ".$CaseData[0][16]." - PC Letter Print Flag: ".$CaseData[0][17]." - Case Closed: ".$CaseData[0][18]." - Archive: ".$CaseData[0][19];
		$AccountDataLog = "PREVIOUS Account Number Data - Account ID: ".$AccountNumData[0][0]." - Case ID: ".$AccountNumData[0][1]." - Account Number: ".$AccountNumData[0][2]." - Account Type: ".$AccountNumData[0][3]." - Business Account: ".$AccountNumData[0][4]." - Account Comments: ".$AccountNumData[0][5]." - New Account: ".$AccountNumData[0][6];
		$CardDataLog = "PREVIOUS Card Number Data - Card ID: ".$CardNumData[0][0]." - Case ID: ".$CardNumData[0][1]." - Card Number: ".$CardNumData[0][2]." - Card Type: ".$CardNumData[0][3]." - Card Status: ".$CardNumData[0][4]." - Card Possession: ".$CardNumData[0][5]." - Card Missing Date: ".$CardNumData[0][6]." - Chip Card: ".$CardNumData[0][7]." - Card Comments: ".$CardNumData[0][8];
		
		
		$CaseDataLog = $dtcon->real_escape_string($CaseDataLog);
		$AccountDataLog = $dtcon->real_escape_string($AccountDataLog);
		$CardDataLog = $dtcon->real_escape_string($CardDataLog);
		
		
		//$editCaseOldContent = array();
		//$edidAccountOldContent = array();
		//$editCardOldContent = array();
		
		//array_merge($editCaseOldContent, $CaseData[0]);
		//array_merge($edidAccountOldContent, $AccountNumData[0]);
		//array_merge($editCardOldContent, $CardNumData[0]);
		
		//POST VALIDATION ======================================================================================
		
		//Check to see if case start day is valid date and in the past. 
		if(!isset($_POST["DisputeCaseDay"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Dispute Day Error! Please Contact the System Admin.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$disputedayVAR = $_POST["DisputeCaseDay"]; //Value to inset
			$dateGood = validateDate($disputedayVAR, 'Y-m-d');
			$dateGoodVAR = validatePastDate($disputedayVAR);
			
			//var_dump($disputedayVAR);
			//print("<br><br>");
			//var_dump($dateGood);
			//print("<br><br>");
			//var_dump($dateGoodVAR);
			//exit();
			
			if ($dateGood != true || $dateGoodVAR != true){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Day Date! Contact the System Admin!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			
		}
		
		
		if(!isset($_POST["FirstName"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Name must be entered!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$firstnameVAR = $_POST["FirstName"];
			$stringGood = isAscii($firstnameVAR);
			
			//var_dump($stringGood);
			//print("<br><br>");
			//var_dump($firstnameVAR);
			//exit();
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the name were Detected! Please try again using regular characters!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z ,.'-]+$/", $firstnameVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the name were Detected! Please contact the admin!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$firstnameVAR = $dtcon->real_escape_string($firstnameVAR);
			
		}
		
		if(!isset($_POST["LastName"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Name must be entered!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$lastnameVAR = $_POST["LastName"];
			$stringGood = isAscii($lastnameVAR);
			
			//var_dump($stringGood);
			//print("<br><br>");
			//var_dump($lastnameVAR);
			//exit();
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the name were Detected! Please try again using regular characters!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z ,.'-]+$/", $lastnameVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the name were Detected! Please contact the admin!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$lastnameVAR = $dtcon->real_escape_string($lastnameVAR);
			
		}
		
		if(!isset($_POST["PhoneNum"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Phone Number must be entered.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$phoneNumVAR = $_POST["PhoneNum"];
			
			if(!preg_match("/^[0-9]{3}[ \-]{0,1}[0-9]{3}[ \-]{0,1}[0-9]{4}$/", $phoneNumVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Phone Number must be digits, slashes, parentheses, or dashes only. Ex: \'123 456 7890\', \'123-456-7890\', \'1234567890\'';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$phoneNumVAR = $dtcon->real_escape_string($phoneNumVAR);
			
		}
		
		if(!isset($_POST["EmailVar"])){
			$emailAddrVAR = "";
		}
		else{
			$emailAddrVAR = $_POST["EmailVar"];
			$stringGood = isAscii($emailAddrVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Email address Error. Please try again.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			//if(!preg_match("/^\w+[[\.-]?\w+]*@\w+[[\.-]?\w+]*\.\w{2,3}+$/", $emailAddrVAR) && $emailAddrVAR != ""){
			//	$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the email were Detected! Please try again!';
			//	header( "Location: viewDisputes.php" );
			//	exit();
			//}
			
			$emailAddrVAR = $dtcon->real_escape_string($emailAddrVAR);
			
		}
		
		if(!isset($_POST["AddressOne"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Address Line One Error. Please try again.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$AddressVar = $_POST["AddressOne"];
			$stringGood = isAscii($AddressVar);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Address Line One Error. Please try again.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z0-9\.\-\s]+$/", $AddressVar)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in Address One were Detected! Please try again!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$AddressVar = $dtcon->real_escape_string($AddressVar);
			
		}
		
		if(!isset($_POST["AddressTwo"])){
			$Address2VAR = "";
		}
		else{
			$Address2VAR = $_POST["AddressTwo"];
			$stringGood = isAscii($Address2VAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Address Line Two Error. Please try again.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z0-9\.\-\s]+$/", $Address2VAR) && $Address2VAR != ""){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in Address Two were Detected! Please try again!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$Address2VAR = $dtcon->real_escape_string($Address2VAR);
			
		}
		
		if(!isset($_POST["CityAddr"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'City Line Error. Please try again.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$CityAddrVar = $_POST["CityAddr"];
			$stringGood = isAscii($CityAddrVar);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'City Line Error. Please try again.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z ]+$/", $CityAddrVar)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the City Name were Detected! Please try again!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$CityAddrVar = $dtcon->real_escape_string($CityAddrVar);
			
		}
		
		if(!isset($_POST["StateAddr"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'State Line Error. Please try again.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$StateAddrVar = $_POST["StateAddr"];
			$stringGood = isAscii($StateAddrVar);
			$stringState = isState($StateAddrVar);
			
			if(!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'State Line Error. Please try again.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			if(!$stringState){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'State Line Error. Please try again.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$StateAddrVar = $dtcon->real_escape_string($StateAddrVar);
			
		}
		
		if(!isset($_POST["ZipCode"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Zip code must be entered!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$ZipCodeVar = $_POST["ZipCode"];
			
			if (!is_numeric($ZipCodeVar)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Zip code can be Numbers only!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if(strlen($ZipCodeVar) < 5 || strlen($ZipCodeVar) > 5){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Zip code can be 5 Numbers only!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			//Future Update?
			
		} 
		
		if(!isset($_POST["inpersonorPhone"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the customer is on the phone or in person!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$custinpersonphoneVAR = $_POST["inpersonorPhone"];
			
			if ($custinpersonphoneVAR != "inperson" && $custinpersonphoneVAR != "phone"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Customer is on the phone or in person! Please contact the System Admin for more help.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if($custinpersonphoneVAR == "inperson"){
				$custinpersonphoneVAR = 1;
			}
			else{
				$custinpersonphoneVAR = 2;
			}
		} 
		
		if(!isset($_POST["RedFlag"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the case is Red Flagged!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$RedFlagTmp = $_POST["RedFlag"];
			
			if ($RedFlagTmp != "Positive" && $RedFlagTmp != "Negative"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Something is wrong with the Red Flag Value! Please contact the System Admin.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if($RedFlagTmp == "Positive"){
				$RedFlagVar = 1;
			}
			else{
				$RedFlagVar = 0;
			}
		} 
		
		if(!isset($_POST["SevLevVar"])){
			$SevLevNumberVAR= 0;
		}
		else{
			$SevLevNumberVAR = $_POST["SevLevVar"];
			
			if (!is_numeric($SevLevNumberVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Sev Lev can be Numbers only!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if(strlen($SevLevNumberVAR) < 1 || strlen($SevLevNumberVAR) > 1){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Sev Lev can be 1 Number only!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			//Future Update?
			
		} 
		
		if(!isset($_POST["CaseComments"])){
			$casecommentsVAR = "";
		}
		else{
			$casecommentsVAR = $_POST["CaseComments"];
			
			$stringGood = isAscii($casecommentsVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Case Comments were Detected! Please try again using regular characters!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$casecommentsVAR = $dtcon->real_escape_string($casecommentsVAR);
			
		} 
		
		if(!isset($_POST["PCPrintFlag"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the case is Print Flagged!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$PrintFlagTmp = $_POST["PCPrintFlag"];
			
			if ($PrintFlagTmp != "Positive" && $PrintFlagTmp != "Negative"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Something is wrong with the Print Flag Value! Please contact the System Admin.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if($PrintFlagTmp == "Positive"){
				$PrintFlagVar = 1;
			}
			else{
				$PrintFlagVar = 0;
			}
		} 
		
		if(!isset($_POST["CaseClosed"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the case is closed or not!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$CaseClosedTmp = $_POST["CaseClosed"];
			
			if ($CaseClosedTmp != "Positive" && $CaseClosedTmp != "Negative"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Something is wrong with the Case Closed Flag Value! Please contact the System Admin.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if($CaseClosedTmp == "Positive"){
				$CaseClosedVar = 1;
			}
			else{
				$CaseClosedVar = 0;
			}
		} 
		
		if(!isset($_POST["AccountNumber"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'An Account Number must be entered!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$accountNumberVAR = $_POST["AccountNumber"];
			
			if (!is_numeric($accountNumberVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number can be Numbers only!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			//Future Update?
			
		} 
		
		if(!isset($_POST["AccountType"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select the Account Type!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$accountTypeVAR = $_POST["AccountType"];
			
			if (!is_numeric($accountTypeVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is an error with the Account Type! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$accountTypeINT = intval($accountTypeVAR);
			
			if($accountTypeINT < 1 || $accountTypeINT > $Numberof_acct_Types){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Account Type! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			//Future Update?
			
		} 
		
		if(!isset($_POST["BusiCustType"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the dispute is a Business Customer or a Consumer!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$consumerorBusinessVAR = $_POST["BusiCustType"];
			
			if ($consumerorBusinessVAR != "Consumer" && $consumerorBusinessVAR != "Business"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Illegal Characters detected in Consumer or Business Selection! Please contact the System Admin!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if($consumerorBusinessVAR == "Business"){
				$consumerorBusinessVAR = "1";
			}
			else{
				$consumerorBusinessVAR = "0";
			}
		} 
		
		if(!isset($_POST["AccountComments"])){
			$accountcommentsVAR = "";
		}
		else{
			$accountcommentsVAR = $_POST["AccountComments"];
			
			$stringGood = isAscii($accountcommentsVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Account Comments were Detected! Please try again using regular characters!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$accountcommentsVAR = $dtcon->real_escape_string($accountcommentsVAR);
			
		} 
		
		if(!isset($_POST["newAccount"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the Account is new or not!';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$NewAccountVAR = $_POST["newAccount"];
			
			if ($NewAccountVAR != 'newAccTrue' && $NewAccountVAR != 'newAccFalse'){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the Account is new or not! Please contact the System Admin for more help.';
				header( "Location: newDispute.php" );
				exit();
			}
			
			if($NewAccountVAR == 'newAccTrue'){
				$NewAccountBool = 1;
			}
			else{
				$NewAccountBool = 0;
			}
			
			
		} 
		
		if(!isset($_POST["Card_Number"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Card Number must be entered!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$CARDNumberVAR = $_POST["Card_Number"];
			
			if (!is_numeric($CARDNumberVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number can be Numbers only!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$len = strlen($CARDNumberVAR);
			
			if($len != 16){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number has to be exactly 16 digits!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			
		} 
		
		if(!isset($_POST["ChipCard"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Receipt Status Error! Please select "yes" or "no".';
			header( "Location: indexTransactions.php" );
			exit();
		}
		else{
			$ChipCardVAR = $_POST["ChipCard"];
			
			if ($ChipCardVAR != "yes" && $ChipCardVAR != "no"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Customer has the receipt! Please contact the System Admin for more help.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if($ChipCardVAR == "yes"){
				$ChipCardVAR = "1";
			}
			else{
				$ChipCardVAR = "0";
			}
		}
		
		if(!isset($_POST["CardType"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the card type!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$CARDTypeVAR = $_POST["CardType"];
			
			if (!is_numeric($CARDTypeVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Type! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$CARDTypeINT = intval($CARDTypeVAR);
			
			if($CARDTypeINT < 1 || $CARDTypeINT > $CardTypeCOUNT){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Type! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			//Future Update?
		} 
		
		if(!isset($_POST["CardStatus"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select the card status!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$CARDstatusVAR = $_POST["CardStatus"];
			
			if (!is_numeric($CARDstatusVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Status Selection! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$CARDstatusINT = intval($CARDstatusVAR);
			
			if($CARDstatusINT < 1 || $CARDstatusINT > $CardStatusCOUNT){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Status Selection! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			//Future Update?
		} 
		
		if(!isset($_POST["CardPossession"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the customer has the card or not!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$CARDpossessionVAR = $_POST["CardPossession"];
			
			if (!is_numeric($CARDpossessionVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Possession Selection! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$CARDpossessionINT = intval($CARDpossessionVAR);
			
			if($CARDpossessionINT < 1 || $CARDpossessionINT > $CardPossessionCOUNT){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Possession Selection! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			//Future Update?
		} 
		
		if(!isset($_POST["possessionDate"])){
			$CARDmissingdateVAR = "";
		}
		else{
			$CARDmissingdateVAR = $_POST["possessionDate"];
			
			if ($CARDmissingdateVAR != ""){
				$POSSdateGood = validateDate($CARDmissingdateVAR, 'Y-m-d');
				$POSSdateGoodVAR = validatePastDate($CARDmissingdateVAR, 'Y-m-d');
				
				if ($POSSdateGood != true || $POSSdateGoodVAR != true){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Card Missing Date! Contact the System Admin!';
					header( "Location: viewDisputes.php" );
					exit();
				}
			}
		} 
		
		if(!isset($_POST["CardComments"])){
			$CARDnumbercommentsVAR = "";
		}
		else{
			$CARDnumbercommentsVAR = $_POST["CardComments"];
			
			$stringGood = isAscii($CARDnumbercommentsVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Card Comments were Detected! Please try again using regular characters!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$CARDnumbercommentsVAR = $dtcon->real_escape_string($CARDnumbercommentsVAR);
			
		}
		
		/*
		$EditCaseID
		print $username . "<br>";
		print $ipaddress . "<br>";
		print $timeofChange . "<br>";
		print $checkACH_VAR . "<br>";
		
		print $disputedayVAR . "<br>";
		print $firstnameVAR . "<br>";
		print $lastnameVAR . "<br>";
		print $phoneNumVAR . "<br>";
		print $emailAddrVAR . "<br>";
		print $AddressVar . "<br>";
		print $Address2VAR . "<br>";
		print $CityAddrVar . "<br>";
		print $StateAddrVar . "<br>";
		print $ZipCodeVar . "<br>";
		
		print $RedFlagVar . "<br>";
		print $SevLevNumberVAR . "<br>";
		print $casecommentsVAR . "<br>";
		print $PrintFlagTmp . "<br>";
		print $CaseClosedTmp . "<br>";
		
		print $accountNumberVAR . "<br>";
		print $accountTypeVAR . "<br>";
		print $consumerorBusinessVAR . "<br>";
		print $accountcommentsVAR . "<br>";
		
		
		print $CARDNumberVAR . "<br>";
		print $ChipCardVAR . "<br>";
		print $CARDTypeVAR . "<br>";
		print $CARDstatusVAR . "<br>";
		print $CARDpossessionVAR . "<br>";
		print $CARDmissingdateVAR . "<br>";
		print $CARDnumbercommentsVAR . "<br>";
		
		
		if($checkACH_VAR == "ACH"){
			
		}
		else if($checkACH_VAR == "Check"){
			
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Please Select either ACH or Check Card Dispute!';
			header( "Location: newDispute.php" );
			exit();
		}
		
		*/
		
		//POST VALIDATION ======================================================================================
		
		
		$checkCaseUpdate = "UPDATE checkcases SET casestartdate='".$disputedayVAR."', custfname='".$firstnameVAR."', custlname='".$lastnameVAR."', custphone='".$phoneNumVAR."', custemail='".$emailAddrVAR."', custaddressone='".$AddressVar."', custaddresstwo='".$Address2VAR."', custcityaddr='".$CityAddrVar."', custstateaddr='".$StateAddrVar."', custzipaddr='".$ZipCodeVar."', redflag='".$RedFlagVar."', sevlev='".$SevLevNumberVAR."', comments='".$casecommentsVAR."', pcletterprintflag='".$PrintFlagVar."', caseclosed='".$CaseClosedVar."' WHERE id='".$EditCaseID."'";
		
		$AccountUpdate = "UPDATE checkaccountnumbers SET accountnumber='".$accountNumberVAR."', accounttype='".$accountTypeVAR."', businessaccount='".$consumerorBusinessVAR."', comments='".$accountcommentsVAR."', accoountnew='".$NewAccountBool."' WHERE caseid='".$EditCaseID."'";
		
		$CardUpdate = "UPDATE checkcardnumbers SET cardnumber='".$CARDNumberVAR."', cardtype='".$CARDTypeVAR."', cardstatus='".$CARDstatusVAR."', cardpossession='".$CARDpossessionVAR."', cardmissingdate='".$CARDmissingdateVAR."', chipcard='".$ChipCardVAR."', comments='".$CARDnumbercommentsVAR."' WHERE caseid='".$EditCaseID."'";
		
		//Log Data ---------------------------
		
		$checkCaseUpdateVAR = "UPDATE checkcases SET casestartdate=".$disputedayVAR.", custfname=".$firstnameVAR.", custlname=".$lastnameVAR.", custphone=".$phoneNumVAR.", custemail=".$emailAddrVAR.", custaddressone=".$AddressVar.", custaddresstwo=".$Address2VAR.", custcityaddr=".$CityAddrVar.", custstateaddr=".$StateAddrVar.", custzipaddr=".$ZipCodeVar.", redflag=".$RedFlagVar.", sevlev=".$SevLevNumberVAR.", comments=".$casecommentsVAR.", pcletterprintflag=".$PrintFlagVar.", caseclosed=".$CaseClosedVar." WHERE id=".$EditCaseID."";
		
		$AccountUpdateVAR = "UPDATE checkaccountnumbers SET accountnumber=".$accountNumberVAR.", accounttype=".$accountTypeVAR.", businessaccount=".$consumerorBusinessVAR.", comments=".$accountcommentsVAR.", accoountnew=".$NewAccountBool." WHERE caseid=".$EditCaseID."";
		
		$CardUpdateVAR = "UPDATE checkcardnumbers SET cardnumber=".$CARDNumberVAR.", cardtype=".$CARDTypeVAR.", cardstatus=".$CARDstatusVAR.", cardpossession=".$CARDpossessionVAR.", cardmissingdate=".$CARDmissingdateVAR.", chipcard=".$ChipCardVAR.", comments=".$CARDnumbercommentsVAR." WHERE caseid=".$EditCaseID."";
		
		//Log Data --------------------------
		
		$userChangeLog = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', 2, '".$EditCaseID."', 'User Updated Content from checkcases: ".$CaseDataLog." -- from checkaccountnumbers: ".$AccountDataLog." -- from checkcardnumbers: ".$CardDataLog." ----- New checkcases Data: ".$checkCaseUpdateVAR." -- New checkaccountnumbers Data: ".$AccountUpdateVAR." -- New CardDataLog Data: ".$CardUpdateVAR."')";
		
		
		//var_dump($checkCaseUpdate, $AccountUpdate, $CardUpdate, $checkCaseUpdateVAR, $AccountUpdateVAR, $CardUpdateVAR, $userChangeLog);
		//var_dump($checkCaseUpdate);
		//print("<br><br>");
		//var_dump($AccountUpdate);
		//print("<br><br>");
		//var_dump($CardUpdate);
		//print("<br><br>");
		//var_dump($checkCaseUpdateVAR);
		//print("<br><br>");
		//var_dump($AccountUpdateVAR);
		//print("<br><br>");
		//var_dump($CardUpdateVAR);
		//print("<br><br>");
		//var_dump($userChangeLog);
		//exit();
		/*
		$editCaseNewContent = array();
		$editAccountNewContent = array();
		$editCardNewContent = array();
		
		array_push($editCaseNewContent, $disputedayVAR);
		array_push($editCaseNewContent, $firstnameVAR);
		array_push($editCaseNewContent, $lastnameVAR);
		array_push($editCaseNewContent, $phoneNumVAR);
		array_push($editCaseNewContent, $emailAddrVAR);
		array_push($editCaseNewContent, $AddressVar);
		array_push($editCaseNewContent, $Address2VAR);
		array_push($editCaseNewContent, $CityAddrVar);
		array_push($editCaseNewContent, $StateAddrVar);
		array_push($editCaseNewContent, $ZipCodeVar);
		array_push($editCaseNewContent, $RedFlagVar);
		array_push($editCaseNewContent, $SevLevNumberVAR);
		array_push($editCaseNewContent, $casecommentsVAR);
		array_push($editCaseNewContent, $PrintFlagVar);
		array_push($editCaseNewContent, $CaseClosedVar);
		
		$tmp_post_bool = editContentCheck($editUserOldContent, $editUserNewContent);
		*/
		
		
		if ($dtcon->query($userChangeLog) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		
		if ($dtcon->query($checkCaseUpdate) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Case Update! Please Contact the System Admin!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		if ($dtcon->query($AccountUpdate) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Account Update! Please Contact the System Admin!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		if ($dtcon->query($CardUpdate) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Card Update! Please Contact the System Admin!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		
		$_SESSION["TransactionAdded"] = "Case information for Case number: ".$EditCaseID." has been updated successfully.";
		
	}
	else if(isset($_POST["SkipCaseSubmit"])){
		
		//Case Information was skipped, go to transaction data here.
		
		//var_dump($_POST);
		
		$EditCaseID = $_POST["caseID"];
		
		//var_dump($EditCaseID);
		
		$TransactionIDnumber = 1;
		
		//$TRANSID = 0;
		
	}
	else if(isset($_POST["editTransactionSubmit"])){
		
		//Transaction info was submitted, process it, display next transaction.
		
		$EditCaseID = $_POST["caseID"];
		
		//var_dump($EditCaseID);
		
		//var_dump($_POST);
		
		date_default_timezone_set("America/Chicago");
		$timeofChange = date("Y-m-d, G:i:s");
		
		
		$OldTransactionIDnumber = $_POST["transactionID"];
		
		if($OldTransactionIDnumber == 0 || $OldTransactionIDnumber == null){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong Transaction Number, please contact the System Admin.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		
		$AllTransactionQuery = "SELECT * FROM checktransactions where transactiondeleted=FALSE AND caseid='" . $EditCaseID . "' ORDER BY id";
		$AllTransactionQuery_Data = $dtcon->query($AllTransactionQuery);
		$AllTransactionData = $AllTransactionQuery_Data->fetch_all();
		
		$TransactionData = $AllTransactionData[$OldTransactionIDnumber-1];
		$TransactionID = $TransactionData[0];
		
		//var_dump($OldTransactionIDnumber, $AllTransactionData, $TransactionData, $TransactionID);
		//var_dump($OldTransactionIDnumber, $TransactionID, $TransactionData);
		//print $OldTransactionIDnumber . "<br><br>";
		//print $TransactionID . "<br><br>";
		//var_dump($TransactionData);
		//exit();
		
		$TransactionDataLog = "PREVIOUS Transaction Data - Transaction ID: ".$TransactionData[0]." - Case ID: ".$TransactionData[1]." - Card ID: ".$TransactionData[2]." - Amount: ".$TransactionData[3]." - Transaction Date: ".$TransactionData[4]." - Date Posted: ".$TransactionData[5]." - Dispute Reason: ".$TransactionData[6]." - Dispute Description: ".$TransactionData[7]." - Merchant Name: ".$TransactionData[8]." - Merchant Contacted: ".$TransactionData[9]." - Merchant Contacted Day: ".$TransactionData[10]." - Merchant Description: ".$TransactionData[11]." - Receipt Status: ".$TransactionData[12]." - Loss: ".$TransactionData[13]." - Reversal Error: ".$TransactionData[14]." - PC Given: ".$TransactionData[15]." - PC Recended: ".$TransactionData[16]." - PC Letter Sent: ".$TransactionData[17]." - PC Reversal Letter Sent: ".$TransactionData[18]." - Charge Back Submitted: ".$TransactionData[19]." - Chargeback Accepted: ".$TransactionData[20]." - Transaction Comments: ".$TransactionData[21]." - Compromise ID: ".$TransactionData[22]." - Compromise Comments: ".$TransactionData[23]." - Merchant Phone Number: ".$TransactionData[25]." - Merchant Notes: ".$TransactionData[26];
		
		$TransactionDataLog = $dtcon->real_escape_string($TransactionDataLog);
		
		
		if(!isset($_POST["TransactionDay"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Transaction Day Error! Please Enter a valid Day the transaction took place in the format YYYY-MM-DD.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$TransactionDayVar = $_POST["TransactionDay"];
			$dateGood = validateDate($TransactionDayVar, 'Y-m-d');
			$dateGoodVAR = validatePastDate($TransactionDayVar);
			
			if ($dateGood != true || $dateGoodVAR != true){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Transaction Day Error! Please Enter a valid Day the transaction took place in the format YYYY-MM-DD. You entered: '.$TransactionDayVar;
				header( "Location: viewDisputes.php" );
				exit();
			}
		}
		
		if(!isset($_POST["PostedDay"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Day the transaction posted in the format YYYY-MM-DD.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$PostedDayVar = $_POST["PostedDay"];
			$dateGood = validateDate($PostedDayVar, 'Y-m-d');
			$dateGoodVAR = validatePastDate($PostedDayVar);
			$dateGoodVARTWO = validatePostedDate($TransactionDayVar, $PostedDayVar);
			
			//var_dump($PostedDayVar);
		
			//var_dump($dateGood);
			//var_dump($dateGoodVAR);
			//var_dump($dateGoodVARTWO);
			//var_dump((($dateGood != true) || ($dateGoodVAR != true) || ($dateGoodVARTWO != true)) || ($PostedDayVar != ""));
			//exit();
			
			if($PostedDayVar == ""){
				//Pass
			}
			elseif($dateGood != true){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Day the transaction posted in the format YYYY-MM-DD. You entered: '.$PostedDayVar;
				header( "Location: viewDisputes.php" );
				exit();
			}
			elseif($dateGoodVAR != true){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Past Day the transaction posted in the format YYYY-MM-DD. You entered: '.$PostedDayVar;
				header( "Location: viewDisputes.php" );
				exit();
			}
			elseif($dateGoodVARTWO != true){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Past Posted Date the transaction posted in the format YYYY-MM-DD. You entered: '.$PostedDayVar;
				header( "Location: viewDisputes.php" );
				exit();
			}
			else{
				//Pass
			}
			
			//if ((($dateGood != true) || ($dateGoodVAR != true) || ($dateGoodVARTWO != true)) || ($PostedDayVar != "")){
			//	$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Day the transaction posted in the format YYYY-MM-DD. You entered: '.$PostedDayVar;
			//	header( "Location: viewDisputes.php" );
			//	exit();
			//}
		}
		
		if(!isset($_POST["AmountDisputed"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Amount Disputed Error! Please enter a valid dollar amount.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$AmountdisputedVar = $_POST["AmountDisputed"];
			
			$CurrencyVar = preg_match("/^\d{1,3}(?:\d{3})*(?:\.\d{0,2})?$/",$AmountdisputedVar);
			
			//var_dump($AmountdisputedVar);
			//var_dump($CurrencyVar);
			//exit();
			
			if($CurrencyVar){
				//Matches [(1-3 Digits)(Optional Exactly 3 digits)(Optional: '.' Exactly 1 or 2 digits)]
				//Eg. '1234.0', '1234.20', '123123', '123.3', '1234'
				//Does not Match: '1,234.', '1,234.009', '12.004', '123.' and Anything with non-digits
				//If (preg_match != true)
				
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Amount Disputed Error! Please enter a valid dollar amount. You entered: '.$AmountdisputedVar.'<br>Valid amounts are: "45", "12345", "1234.00", "12345678.99"';
				header( "Location: viewDisputes.php" );
				exit();
			}
		}
		
		if(!isset($_POST["amountLoss"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Amount Loss Error! Please select "yes" or "no".';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$LossStatusVar = $_POST["amountLoss"];
			
			if ($LossStatusVar != "yes" && $LossStatusVar != "no"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Amount is a loss or not! Please contact the System Admin for more help.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if($LossStatusVar == "yes"){
				$LossStatusVar = "1";
			}
			else{
				$LossStatusVar = "0";
			}
		}
		
		if(!isset($_POST["receiptstatus"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Receipt Status Error! Please select "yes" or "no".';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$RecieptStatusVar = $_POST["receiptstatus"];
			
			if ($RecieptStatusVar != "yes" && $RecieptStatusVar != "no"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Customer has the receipt! Please contact the System Admin for more help.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if($RecieptStatusVar == "yes"){
				$RecieptStatusVar = "1";
			}
			else{
				$RecieptStatusVar = "0";
			}
		}
		
		if(!isset($_POST["MerchantName"])){		//Maybe not required??
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Name Error! Please input a merchant name.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$MerchantNameVAR = $_POST["MerchantName"];
			$stringGood = isAscii($MerchantNameVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Merchant Name were Detected! Please try again using regular characters!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z0-9\'\.\-\s\:\;\?\/\,\]\[\}\{\!\@\#\$\%\^\*\(\)\_\=\+]+$/", $MerchantNameVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Merchant Name were Detected! Please try again using regular characters!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			//print $MerchantNameVAR;
			$MerchantNameVAR = $dtcon->real_escape_string($MerchantNameVAR);
			//print $MerchantNameVAR;
			//exit();
		}
		
		if(!isset($_POST["merchantcontactstatus"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Contact Status Error! Please Select a Contact Status.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$MerchantContactVar = $_POST["merchantcontactstatus"];
			
			if ($MerchantContactVar != "yes" && $MerchantContactVar != "no"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Merchant has been Contacted!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if($MerchantContactVar == "yes"){
				$MerchantContactVar = "1";
			}
			else{
				$MerchantContactVar = "0";
			}
		}
		
		if(!isset($_POST["MerchantContactDay"])){
			$MerchantContactDayVar = "";
		}
		else{
			
			if($MerchantContactVar == "0"){
				$MerchantContactDayVar = "";
			}
			else{
				$MerchantContactDayVar = $_POST["MerchantContactDay"];
				$dateGood = validateDate($MerchantContactDayVar, 'Y-m-d');
				$dateGoodVAR = validatePastDate($MerchantContactDayVar, 'Y-m-d');
				$dateGoodVARTWO = validatePostedDate($TransactionDayVar, $MerchantContactDayVar);
				
				if ($dateGood != true || $dateGoodVAR != true || $dateGoodVARTWO != true){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Contact Day Error! Please Enter a valid Day the merchant was contacted in the format YYYY-MM-DD. You entered: '.$MerchantContactDayVar;
					header( "Location: viewDisputes.php" );
					exit();
				}	
			}
		}
		
		if(!isset($_POST["DisputeReason"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Dispute Reason Error! Please Select a Reason.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$DisputeReasonVar = $_POST["DisputeReason"];
			
			if (!is_numeric($DisputeReasonVar)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Dispute Reason Selection! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}

			$DisputeReasonINT = intval($DisputeReasonVar);
			
			if($DisputeReasonINT < 1 || $DisputeReasonINT > $NumberofdisputeReason){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Dispute Reason Selection! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}
		}
		
		if(!isset($_POST["DisputeDescription"])){
			$DisputeDescriptionVar = "";
		}
		else{
			$DisputeDescriptionVar = $_POST["DisputeDescription"];
			
			$stringGood = isAscii($DisputeDescriptionVar);

			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Dispute Description were Detected! Please try again using regular characters!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$DisputeDescriptionVar = $dtcon->real_escape_string($DisputeDescriptionVar);
			
		}
		
		if(!isset($_POST["merchantDescription"])){
			$MerchantDescriptionVar = "";
		}
		else{
			$MerchantDescriptionVar = $_POST["merchantDescription"];
			
			$stringGood = isAscii($MerchantDescriptionVar);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Merchant Description were Detected! Please try again using regular characters!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$MerchantDescriptionVar = $dtcon->real_escape_string($MerchantDescriptionVar);
			
		}
		
		if(!isset($_POST["transactionDescription"])){
			$TransactionCommentsVar = "";
		}
		else{
			$TransactionCommentsVar = $_POST["transactionDescription"];
			
			$stringGood = isAscii($TransactionCommentsVar);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Transaction Comments were Detected! Please try again using regular characters!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$TransactionCommentsVar = $dtcon->real_escape_string($TransactionCommentsVar);
		}
		
		if(!isset($_POST["pcCredGiven"])){
			$PCGivenDayVar == "";
		}
		else{
			$PCGivenDayVar = $_POST["pcCredGiven"];
			if($PCGivenDayVar == ""){
				//Do nothing, left blank intentionally
			}
			else{
				$dateGood = validateDate($PCGivenDayVar);
				$dateGoodVAR = validatePastDate($PCGivenDayVar);
				
				if ($dateGood != true || $dateGoodVAR != true){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Provisional Credit Date Created Error! Please Enter a valid date in the format YYYY-MM-DD. You entered: '.$PCGivenDayVar;
					header( "Location: viewDisputes.php" );
					exit();
				}
			}
			
		}
		
		if(!isset($_POST["pcCredLetterSent"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Provisional Credit Letter Sent Date Error! Please Enter a valid date in the format YYYY-MM-DD.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$PCGivenLetterVar = $_POST["pcCredLetterSent"];
			if($PCGivenLetterVar == ""){
				//Do nothing, left blank intentionally
			}
			else{
				$dateGood = validateDate($PCGivenLetterVar, 'Y-m-d');
				$dateGoodVAR = validatePastDate($PCGivenLetterVar);
				
				if ($dateGood != true || $dateGoodVAR != true){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Provisional Credit Letter Sent Date Error! Please Enter a valid date in the format YYYY-MM-DD. You entered: '.$PCGivenLetterVar;
					header( "Location: viewDisputes.php" );
					exit();
				}
			}
		}
		
		if(!isset($_POST["ReversalID"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Reversal Error Reason Error! Please Select a Reason.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$ReversalIDVar = $_POST["ReversalID"];
			
			if (!is_numeric($ReversalIDVar)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Reversal Error Reason Selection! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}

			$ReversalIDINT = intval($ReversalIDVar);
			
			if($ReversalIDINT < 0 || $ReversalIDINT > $NumberofRever){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Reversal Error Reason Selection! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}
		}
		
		if(!isset($_POST["pcCredRecend"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Provisional Credit Letter Sent Date Error! Please Enter a valid date in the format YYYY-MM-DD.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$PCcreditRecendVar = $_POST["pcCredRecend"];
			if($PCcreditRecendVar == ""){
				//Do nothing, left blank intentionally
			}
			else{
				$dateGood = validateDate($PCcreditRecendVar, 'Y-m-d');
				$dateGoodVAR = validatePastFutureDate($PCcreditRecendVar); //Validate past and only 1 week in future
				
				if ($dateGood != true || $dateGoodVAR != true){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Reversal PC Letter Created Date Error! Please Enter a valid date in the format YYYY-MM-DD. You entered: '.$PCcreditRecendVar;
					header( "Location: viewDisputes.php" );
					exit();
				}
			}
		}
		
		if(!isset($_POST["pcCredReverseLetterSent"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Provisional Credit Reversal Letter Sent Date Error! Please Enter a valid date in the format YYYY-MM-DD.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$PCcredReverseLetterVar = $_POST["pcCredReverseLetterSent"];
			if($PCcredReverseLetterVar == ""){
				//Do nothing, left blank intentionally
			}
			else{
				$dateGood = validateDate($PCcredReverseLetterVar, 'Y-m-d');
				$dateGoodVAR = validatePastFutureDate($PCcredReverseLetterVar); //Validate past and only 1 week in future
				
				if ($dateGood != true || $dateGoodVAR != true){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Reversal PC Reversal Letter Sent Date Error! Please Enter a valid date in the format YYYY-MM-DD. You entered: '.$PCcredReverseLetterVar;
					header( "Location: viewDisputes.php" );
					exit();
				}
			}
		}
		
		if(!isset($_POST["chargebackSubmitted"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Charge back submitted Error! Please select "yes" or "no".';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$CBsubmitVar = $_POST["chargebackSubmitted"];
			
			if ($CBsubmitVar != "yes" && $CBsubmitVar != "no"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Charge back has been submitted! Please contact the System Admin for more help.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if($CBsubmitVar == "yes"){
				$CBsubmitVar = "1";
			}
			else{
				$CBsubmitVar = "0";
			}
		}
		
		if(!isset($_POST["chargebackAccepted"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Charge back accepted Error! Please select "yes" or "no".';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$CBacceptVar = $_POST["chargebackAccepted"];
			
			if ($CBacceptVar != "yes" && $CBacceptVar != "no"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Charge back has been accepted! Please contact the System Admin for more help.';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			if($CBacceptVar == "yes"){
				$CBacceptVar = "1";
			}
			else{
				$CBacceptVar = "0";
			}
		}
		
		if(!isset($_POST["compromiseID"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Compromise selection Error! Please make Selection.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$compromiseIDVar = $_POST["compromiseID"];
			
			if (!is_numeric($compromiseIDVar)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Compromise Selection! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}

			$compromiseIDINT = intval($compromiseIDVar);
			
			if($compromiseIDINT < 0 || $compromiseIDINT > $NumberofComp){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Compromise Selection! Please contact the System Admin';
				header( "Location: viewDisputes.php" );
				exit();
			}
		}
		
		if(!isset($_POST["compromiseDescription"])){
			$compromiseDescriptionVAR = "";
		}
		else{
			$compromiseDescriptionVAR = $_POST["compromiseDescription"];
			$stringGood = isAscii($compromiseDescriptionVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Compromise description were Detected! Please try again using regular characters!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$compromiseDescriptionVAR = $dtcon->real_escape_string($compromiseDescriptionVAR);
			
		}
		
		if(!isset($_POST["MerchantPhoneNum"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Phone Number must be set!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		else{
			$MerchantphoneNumVAR = $_POST["MerchantPhoneNum"];
			
			if(!preg_match("/^[0-9]{3}[ \-]{0,1}[0-9]{3}[ \-]{0,1}[0-9]{4}$", $MerchantphoneNumVAR) && $MerchantphoneNumVAR != ""){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Phone Number must be digits, slashes, parentheses, or dashes only. Ex: \'123 456 7890\', \'123-456-7890\', \'1234567890\'';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
		}
		
		if(!isset($_POST["MerchantNotes"])){
			$MerchantNotesVAR = "";
		}
		else{
			$MerchantNotesVAR = $_POST["MerchantNotes"];
			$stringGood = isAscii($MerchantNotesVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Compromise description were Detected! Please try again using regular characters!';
				header( "Location: viewDisputes.php" );
				exit();
			}
			
			$MerchantNotesVAR = $dtcon->real_escape_string($MerchantNotesVAR);
			
		}
		
		//=================================================================================================================================================================
		
		$AttachmentIDFieldCount = count($_POST["attachmentID"]);
		$AttachmentCommentsFieldCount = count($_POST["attachmentComments"]);
		
		//var_dump($AttachmentIDFieldCount, $AttachmentCommentsFieldCount);
		
		if(!($AttachmentIDFieldCount == $AttachmentCommentsFieldCount)){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the attachment field values! Please try again or contact the system admin.';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		
		$AttachmentIDArray = $_POST["attachmentID"];
		$AttachmentCommentsArray = $_POST["attachmentComments"];
		
		$DONEAttachmentID = array();
		$DONEAttachmentComments = array();
		
		for($i=0;$i<$AttachmentIDFieldCount;$i++){
			
			if(!isset($_POST["attachmentID"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Attachment ID! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$TempAttachID = $AttachmentIDArray[$i];
				
				if (!is_numeric($TempAttachID)){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Attachment ID Value! Please contact the System Admin';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				array_push($DONEAttachmentID, $TempAttachID);
			}
			
			if(!isset($_POST["attachmentComments"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Attachment Comments! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$TempComments = $AttachmentCommentsArray[$i];
				
				$stringGood = isAscii($TempComments);
			
				if (!$stringGood){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Attachment Comments Value! Please contact the System Admin';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				array_push($DONEAttachmentComments, $TempComments);
			}
			
			
		}
		
		$NewChangeInsertstring = "";
		
		//Attachment Fixes
		for($i=0;$i<$AttachmentIDFieldCount;$i++){
			
			$tempAttachmentFix = "UPDATE checkattachments SET comments='".$DONEAttachmentComments[$i]."' WHERE id='".$DONEAttachmentID[$i]."' and caseid='".$EditCaseID."'";
			
			if ($dtcon->query($tempAttachmentFix) === TRUE) {
				
				//print "Insert Worked";
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Attachment Update! Attachment number: '.$DONETranIDArray[$i].' failed. Please Contact the System Admin!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$NewChangeInsertstring = $NewChangeInsertstring . "\n\n" . $tempAttachmentFix;
		}
		
		
		//=================================================================================================================================================================
		
		$checkTransactionUpdate = "UPDATE checktransactions SET amount='".$AmountdisputedVar."', transactiondate='".$TransactionDayVar."', dateposted='".$PostedDayVar."', disputereason='".$DisputeReasonINT."', description='".$DisputeDescriptionVar."', merchantname='".$MerchantNameVAR."', merchantcontacted='".$MerchantContactVar."', merchantcontacteddate='".$MerchantContactDayVar."', merchantcontactdescription='".$MerchantDescriptionVar."', receiptstatus='".$RecieptStatusVar."', loss='".$LossStatusVar."', reversalerror='".$ReversalIDVar."', procreditgiven='".$PCGivenDayVar."', pcrescinded='".$PCcreditRecendVar."', pclettersent='".$PCGivenLetterVar."', pcreverselettersent='".$PCcredReverseLetterVar."', cbinitiated='".$CBsubmitVar."', cbaccepted='".$CBacceptVar."', comments='".$TransactionCommentsVar."', compromiseid='".$compromiseIDINT."', compromisecomments='".$compromiseDescriptionVAR."', merchantphone='".$MerchantphoneNumVAR."', merchantnotes='".$MerchantNotesVAR."' WHERE id='".$TransactionID."'";
		
		//Log Data ---------------------------
		
		$checkTransactionUpdateVAR = "UPDATE checktransactions SET amount=".$AmountdisputedVar.", transactiondate=".$TransactionDayVar.", dateposted=".$PostedDayVar.", disputereason=".$DisputeReasonINT.", description=".$DisputeDescriptionVar.", merchantname=".$MerchantNameVAR.", merchantcontacted=".$MerchantContactVar.", merchantcontacteddate=".$MerchantContactDayVar.", merchantcontactdescription=".$MerchantDescriptionVar.", receiptstatus=".$RecieptStatusVar.", loss=".$LossStatusVar.", reversalerror=".$ReversalIDVar.", procreditgiven=".$PCGivenDayVar.", pcrescinded=".$PCcreditRecendVar.", pclettersent=".$PCGivenLetterVar.", pcreverselettersent=".$PCcredReverseLetterVar.", cbinitiated=".$CBsubmitVar.", cbaccepted=".$CBacceptVar.", comments=".$TransactionCommentsVar.", compromiseid=".$compromiseIDINT.", compromisecomments=".$compromiseDescriptionVAR.", merchantphone=".$MerchantphoneNumVAR.", merchantnotes=".$MerchantNotesVAR." WHERE id=".$TransactionID."";
		
		//Log Data --------------------------
		
		$NewChangeInsertstring = $dtcon->real_escape_string($NewChangeInsertstring);
		
		$userChangeLog = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '2', '".$EditCaseID."', 'User Updated Content from checktransactions: ".$TransactionDataLog." ----- New checktransactions Data: ".$checkTransactionUpdateVAR." ----- New Attachment Data: ".$NewChangeInsertstring."')";
		
		
		//var_dump($TransactionDataLog, $checkTransactionUpdate, $checkTransactionUpdateVAR, $userChangeLog);
		//print $TransactionDataLog . "<br><br>";
		//print $checkTransactionUpdate . "<br><br>";
		//print $checkTransactionUpdateVAR . "<br><br>";
		//print $MerchantNameVAR . "<br><br>";
		//print $userChangeLog . "<br><br>";
		//exit();
		
		
		if ($dtcon->query($userChangeLog) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		
		if ($dtcon->query($checkTransactionUpdate) === TRUE) {
			
			//print "Insert Worked";
			//exit();
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Transaction Update! Please Contact the System Admin!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		
		
		
		$TransactionIDnumber = $OldTransactionIDnumber;
		
		$_SESSION["TransactionAdded"] = "Transaction Number: ".$TransactionIDnumber." has been updated successfully.";
		
	}
	else if(isset($_POST["editTransactionSkip"])){
		
		//Transaction info was skipped, display next transaction.
		
		//var_dump($_POST);
		
		$EditCaseID = $_POST["caseID"];
		
		//var_dump($EditCaseID);
		//exit();
		
		$OldTransactionIDnumber = $_POST["transactionID"];
		
		if($OldTransactionIDnumber == 0 || $OldTransactionIDnumber == null){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong Transaction Number, please contact the System Admin.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		
		$tmpTransactionQuery = "SELECT * FROM checktransactions WHERE transactiondeleted=FALSE AND caseid='" . $EditCaseID . "'";
		$tmpTransactionQuery_Data = $dtcon->query($tmpTransactionQuery);
		$tmpTransactionData = $tmpTransactionQuery_Data->fetch_all();
		$tmpTransactionCount = count($tmpTransactionData);
		
		if($OldTransactionIDnumber == $tmpTransactionCount){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'This is the last transaction.';
			$TransactionIDnumber = $OldTransactionIDnumber;
		}
		else if($OldTransactionIDnumber > $tmpTransactionCount){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong, please try again';
			$TransactionIDnumber = 1;
		}
		else{
			$TransactionIDnumber = $OldTransactionIDnumber + 1;
		}
		
		//var_dump($OldTransactionIDnumber, $_POST["transactionID"]);
		//exit();
		
		//$TRANSID = 0;
		
		
		
		
		
	}
	else if(isset($_POST["previousPage"])){
		
		$EditCaseID = $_POST["caseID"];
		$OldTransactionIDnumber = $_POST["transactionID"];
		
		if($OldTransactionIDnumber == 0 || $OldTransactionIDnumber == null){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong Transaction Number, please contact the System Admin.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		
		//var_dump($EditCaseID, $OldTransactionIDnumber, $_POST, $_SESSION);
		
		if ($OldTransactionIDnumber == 1){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'This is the first transaction.';
			$TransactionIDnumber = $OldTransactionIDnumber;
		}
		else if($OldTransactionIDnumber < 0){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong, please try again';
			$TransactionIDnumber = 1;
		}
		else{
			$TransactionIDnumber = $OldTransactionIDnumber - 1;
		}
		
		
	}
	else if(isset($_POST["deleteTransaction"])){
		
		date_default_timezone_set("America/Chicago");
		$timeofChange = date("Y-m-d, G:i:s");
		
		$EditCaseID = $_POST["caseID"];
		$OldTransactionIDnumber = $_POST["transactionID"];
		
		if($OldTransactionIDnumber == 0 || $OldTransactionIDnumber == null){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong Transaction Number, please contact the System Admin.';
			header( "Location: viewDisputes.php" );
			exit();
		}
		
		
		$TransactionQuery = "SELECT * FROM checktransactions where transactiondeleted=FALSE AND caseid='" . $EditCaseID . "' ORDER BY id";
		$TransactionQuery_Data = $dtcon->query($TransactionQuery);
		$TransactionData = $TransactionQuery_Data->fetch_all();
		
		$TransactionID = $TransactionData[$OldTransactionIDnumber-1][0];
		
		$TransactionDeleteUpdate = "UPDATE checktransactions SET transactiondeleted = TRUE WHERE caseid='".$EditCaseID."' AND id='".$TransactionID."'"; 
		
		$TransactionDeleteUpdateVAR = "UPDATE checktransactions SET transactiondeleted = TRUE WHERE caseid=".$EditCaseID." AND id=".$TransactionID."";
		
		$userChangeLogQuery = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '3', '".$EditCaseID."', 'User deleted Transaction number: ".$TransactionID." from case number: ".$EditCaseID." --- Command: ".$TransactionDeleteUpdateVAR."')";
		
		
		//var_dump($_SESSION, $_POST);
		//var_dump($EditCaseID, $TransactionQuery);
		//var_dump($OldTransactionIDnumber, $TransactionData, $TransactionID, $TransactionDeleteUpdate, $TransactionDeleteUpdateVAR, $userChangeLogQuery);
		//exit();
		
		if ($dtcon->query($TransactionDeleteUpdate) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with Deleting the Transaction Data for the Case! Please Contact the System Admin!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		
		if ($dtcon->query($userChangeLogQuery) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: viewDisputes.php" );
			exit();
		}	
		
		$TransactionIDnumber = $OldTransactionIDnumber;
		
		$_SESSION["TransactionAdded"] = "Transaction for Case number: ".$EditCaseID." has been deleted successfully.";
		
	}
	else if(isset($_POST["DeleteCase"])){
		
		date_default_timezone_set("America/Chicago");
		$timeofChange = date("Y-m-d, G:i:s");
		
		$EditCaseID = $_POST["caseID"];
		
		
		$caseDeleteUpdate = "UPDATE checkcases SET casedeleted = TRUE WHERE id='".$EditCaseID."'";
		$TransactionDeleteUpdate = "UPDATE checktransactions SET transactiondeleted = TRUE WHERE caseid='".$EditCaseID."'"; //This line can hit multiple SQL table rows.
		
		
		$caseDeleteUpdateVAR = "UPDATE checkcases SET casedeleted = TRUE WHERE id=".$EditCaseID."";
		$TransactionDeleteUpdateVAR = "UPDATE checktransactions SET transactiondeleted = TRUE WHERE caseid=".$EditCaseID."";
		
		
		$userChangeLogQuery = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '3', '".$EditCaseID."', 'User deleted case number: ".$EditCaseID." --- Commands: ".$caseDeleteUpdateVAR." -- ".$TransactionDeleteUpdateVAR."')";
		
		
		//var_dump($_SESSION, $_POST);
		//var_dump($EditCaseID);
		//var_dump($caseDeleteUpdate, $TransactionDeleteUpdate, $userChangeLogQuery);
		//exit();
		
		
		if ($dtcon->query($caseDeleteUpdate) === TRUE) {
				
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with Deleting the Case Data! Please Contact the System Admin!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		if ($dtcon->query($TransactionDeleteUpdate) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with Deleting the Transaction Data for the Case! Please Contact the System Admin!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		
		
		if ($dtcon->query($userChangeLogQuery) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: viewDisputes.php" );
			exit();
		}
		
		$_SESSION["TransactionAdded"] = "Case number: ".$EditCaseID." has been deleted successfully.";
		header( "Location: viewDisputes.php" );
		exit();
		
	}
	else if(isset($_POST["deleteAttach"])){
		
		$EditCaseID = $_POST["caseID"];
		
		$TransactionIDnumber = $_POST["transactionID"];
		
		$DeleteAttachID = $_POST["deleteAttach"];
		
		//var_dump($DeleteAttachID);
		
		//Setup 'Delete'/Update String
		$DeleteString = "UPDATE checkattachments SET iddeleted = TRUE WHERE id='".$DeleteAttachID."'"; 
		
		if ($dtcon->query($DeleteString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with Deleting the attachment! Please Contact the System Admin!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
			
		}
		
		$userChangeLogQuery = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '3', '".$EditCaseID."', 'User deleted attachment number ".$DeleteAttachID." from case.')";
		
		if ($dtcon->query($userChangeLogQuery) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: viewDisputes.php" );
			exit();
		}	
		
		$_SESSION["TransactionAdded"] = "Attachment successfully deleted.";
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Server POST, please contact the System Admin.';
		header( "Location: viewDisputes.php" );
		exit();
	}
	
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select a case to edit from View Disputes or Edit Disputes.';
	header( "Location: viewDisputes.php" );
	exit();
}



?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - Edit Disputes</title>
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
		
		function merchantChange(){
			
			var currReasonID = document.getElementById('marchantcontactstatusID');
			var currValue = currReasonID.options[currReasonID.selectedIndex].value;
			
			if (currValue == 'yes'){
				
				var x = document.getElementById('MerchantContactDayID');
			
				x.style.display = 'block';
				
			}
			else{
				
				var x = document.getElementById('MerchantContactDayID');
				
				x.style.display = 'none';
			
				document.getElementById('MerchContactDate').value = '';

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
                    <div class="col-md-5">
                     <h2>Edit Dispute Transaction Information</h2> 
                    </div>
					<div class="col-md-7">
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
				
				<hr />
				
				<div class="row">
					<div class="col-md-2">
						<h4>Case ID: <?php print $EditCaseID; ?></h4>
					</div>
					<div class="col-md-2">
						<h4>Transaction Number: <?php print $TransactionIDnumber; ?></h4>
					</div>
					<!--
					<div class="col-md-2">
						<h4>TRANSACTION ID: <?php print $TransactionIDnumber; ?></h4>
					</div-->
				</div><hr />
				<div class="row">
					<div class="col-md-2">
						<form id="BacktoCaseDisputeForm_ID" autocomplete="off" name="BacktoCaseForm" method="post" action="editDispute.php">
							<input type='hidden' name='caseID' value='<?php print $EditCaseID; ?>'>
							<button type="submit" class="btn btn-ZnewFive" name="EditSubmit" value="<?php print $EditCaseID; ?>"  style="width:185px;">Back to Case Edit</button>
						</form>
					</div> <!-- Confirmation: onclick="return confirm('Are you sure?')" -->
					<div class="col-md-2">
						<form id="PreviousTransactionDisputeForm_ID" autocomplete="off" name="PreviousDisputeTransactionForm" method="post" action="editDisputeTransactions.php">
							<input type='hidden' name='caseID' value='<?php print $EditCaseID; ?>'>
							<input type='hidden' name='transactionID' value='<?php print $TransactionIDnumber; ?>'>
							<input type="submit" class="btn btn-ZnewTwo" name="previousPage" value="Previous Transaction" style="width:185px;">
						</form>
					</div>
					<div class="col-md-2">
						<form id="DeleteTransactionDisputeForm_ID" autocomplete="off" name="DeleteDisputeTransactionForm" method="post" action="editDisputeTransactions.php">
							<input type='hidden' name='caseID' value='<?php print $EditCaseID; ?>'>
							<input type='hidden' name='transactionID' value='<?php print $TransactionIDnumber; ?>'>
							<input type="submit" class="btn btn-danger" name="deleteTransaction" value="Delete Transaction" style="width:185px;" onclick="return confirm('Click OK to confirm deletion.')">
						</form>
					</div>
					<div class="col-md-2">
						<form id="SkipTransactionDisputeForm_ID" autocomplete="off" name="SkipDisputeTransactionForm" method="post" action="editDisputeTransactions.php">
							<input type='hidden' name='caseID' value='<?php print $EditCaseID; ?>'>
							<input type='hidden' name='transactionID' value='<?php print $TransactionIDnumber; ?>'>
							<input type="submit" class="btn btn-success" name="editTransactionSkip" value="Skip Transaction Change" style="width:185px;">
						</form>
					</div>
					
					<div class="col-md-2">
						<form id="ViewDisputeForm_ID" autocomplete="off" name="ViewDisputeForm" method="post" action="DisputeViewOnly.php">
							<input type='hidden' name='ViewSubmit' value='<?php print $EditCaseID; ?>'>
							<input type="submit" class="btn btn-primary" name="ViewCase" value="Back to View Case">
						</form>
					</div>
					
				</div><hr />
				
				<form id="EditTransactionDisputeForm_ID" autocomplete="off" name="EditDisputeTransactionForm" method="post" action="editDisputeTransactions.php">
				
				<input type='hidden' name='caseID' value='<?php print $EditCaseID; ?>'>
				<input type='hidden' name='transactionID' value='<?php print $TransactionIDnumber; ?>'>
				
<?php


if($EditCaseID == 0 || $EditCaseID == NULL){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select a case to edit from View Disputes or Edit Disputes.';
	echo '<script> window.location = "viewDisputes.php";</script>';
	//header( "Location: viewDisputes.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}
if($TransactionIDnumber == 0 || $TransactionIDnumber == null){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong Transaction Number, please contact the System Admin.';
	echo '<script> window.location = "viewDisputes.php";</script>';
	//header( "Location: viewDisputes.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}


//SQL Info -----------------------------------------------------------------------------------------------


$AllTransactionQuery = "SELECT * FROM checktransactions WHERE transactiondeleted=FALSE AND caseid='" . $EditCaseID . "'";
$AllTransactionQuery_Data = $dtcon->query($AllTransactionQuery);
$AllTransactionData = $AllTransactionQuery_Data->fetch_all();

$TransactionData = array();

array_push($TransactionData, $AllTransactionData[$TransactionIDnumber-1]);

//var_dump($AllTransactionQuery);
//var_dump($AllTransactionData);
//var_dump($TransactionData);
//exit();

if (empty($TransactionData)){
	$_SESSION["ADD_DISPUTE_ERROR"] = "This Case doesn't have any transactions! Please contact the system Admin!";
	echo '<script> window.location = "viewDisputes.php";</script>';
	//header( "Location: viewDisputes.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
	
}


//SQL Info -----------------------------------------------------------------------------------------------


//var_dump($TransactionData);

//var_dump($_SESSION);

//exit();

//var_dump($compromise_data);
//var_dump($reversal_data);

?>
				
				<div class="row">
					<div class="col-md-2">
						<label>Transaction Date</label>&nbsp;<a target="_blank" title="Please select when the transaction occurred using the calendar tool. The format must be: YYYY-MM-DD. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<input type="text" class="form-control" name="TransactionDay" value="<?php print $TransactionData[0][4]; ?>" onFocus="showCalendarControl(this);"  id="transactiondate" pattern="^[0-9-]+$" title="'YYYY-MM-DD'" required>
						
					</div>
					<div class="col-md-2">
						<label>Date Posted to Account</label>&nbsp;<a target="_blank" title="Please select when the transaction posted to the account using the calendar tool. The format must be: YYYY-MM-DD."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<input type="text" class="form-control" name="PostedDay" value="<?php print $TransactionData[0][5]; ?>" onFocus="showCalendarControl(this);" id="posteddate" pattern="^[0-9-]+$" title="'YYYY-MM-DD'">
						
					</div>
					<div class="col-md-2">
						<label>Amount Disputed</label>&nbsp;<a target="_blank" title="Please enter the transaction amount. Please do not include the dollar sign. This field accepts digits and a single period. Ex.: '45', '45.86'. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<input type="text" class="form-control" style="background: url(./assets/img/dollar-sign.png) no-repeat 0px 0px; background-size: 30px; padding-left:22px;" name="AmountDisputed" maxlength="11" value="<?php print $TransactionData[0][3]; ?>" id="amountDisputed" pattern="^\d+(?:\.\d{0,2})?$" title="'123.45', '123', '1234567.00'" required>
					</div>
					<div class="col-md-2">
						<label>Is this amount a Loss?</label>&nbsp;<a target="_blank" title="Please select if the amount is a loss. The default value is yes."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<select name="amountLoss" class="form-control"> 
<?php

if($TransactionData[0][13] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no">No</option>';
}
else if($TransactionData[0][13] == 0){
	print '<option value="yes">Yes</option>';
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
						<label>Does the customer have a receipt for the transaction?</label>&nbsp;<a target="_blank" title="Please select if the customer has the receipt for the transaction. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<select id="receiptstatusID" name="receiptstatus" class="form-control"> 
<?php

if($TransactionData[0][12] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no">No</option>';
}
else if($TransactionData[0][12] == 0){
	print '<option value="yes">Yes</option>';
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
						<label>Dispute Reason:</label>&nbsp;<a target="_blank" title="Please select the customers dispute reason for this transaction. Please see the notice to CSR for any additional information needed. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<select name="DisputeReason" class="form-control">
							<option value="select">Select One</option>
<?php

for ($i=0; $i < $NumberofdisputeReason; $i++){
	
	if($disputeReason_data[$i][0] == $TransactionData[0][6]){
		print '<option value="' . $disputeReason_data[$i][0] . '" selected="true">' . $disputeReason_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $disputeReason_data[$i][0] . '">' . $disputeReason_data[$i][1] . '</option>';
	}
}

?>
						</select>
					</div>
					<div class="col-md-8">
						<label>Dispute Description</label>&nbsp;<a target="_blank" title="This field is for comments about the account(s). This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<textarea type="text" class="form-control" onblur="this.value = this.value.toUpperCase();" name="DisputeDescription" rows="2" maxlength="4096"><?php print $TransactionData[0][7]; ?></textarea>
					</div>
				</div><hr />
				
				<div class="row">
					<div class="col-md-3">
						<label>Merchant Name:</label>&nbsp;<a target="_blank" title="This field is for merchant names. You may use alphabetic, numeric and some special characters. Ex.: 'Forever 21', 'Walmart', 'Trader Joe's'. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<input type="text" class="form-control" onblur="this.value = this.value.toUpperCase();" name="MerchantName" maxlength="255" value="<?php print $TransactionData[0][8]; ?>" id="merchname" pattern="^[\x00-\x7F]*$" title="Letters, digits, periods, and spaces only." required>
					</div>
					<div class="col-md-3">
						<label>Merchant Phone:</label>&nbsp;<a target="_blank" title="This field is for the merchants phone number. For now this is not required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<input type="text" class="form-control" name="MerchantPhoneNum" maxlength="255" value="<?php print $TransactionData[0][25]; ?>" id="merchphone" pattern="^[0-9]{3}[ \-]{0,1}[0-9]{3}[ \-]{0,1}[0-9]{4}$" title="'123 456 7890', '123-456-7890', '1234567890'">
					</div>
					<div class="col-md-3">
						<label>Has the Merchant been contacted?</label>&nbsp;<a target="_blank" title="Please select whether the merchant has been contacted by the customer or you. If they have been contacted please select the date of contact in the Contact Date field when it pops up."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<select id="marchantcontactstatusID" name="merchantcontactstatus" class="form-control" onchange="merchantChange();">
<?php

if($TransactionData[0][9] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no">No</option>';
}
else if($TransactionData[0][9] == 0){
	print '<option value="yes">Yes</option>';
	print '<option value="no" selected="true">No</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Merchant Contact Error! Please contact the system Admin!";
	echo '<script> window.location = "viewDisputes.php";</script>';
	//header( "Location: viewDisputes.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}




$DisplayValue = FALSE;

if($TransactionData[0][9] == 1){
	$DisplayValue = TRUE;
}


?>
						</select> 
					</div>
					<div class="col-md-3" id="MerchantContactDayID" style='<?php print $DisplayValue ? 'display:block' : 'display:none' ?>'>
						<label>Contact Date:</label>&nbsp;<a target="_blank" title="If the merchant has been contacted, please select approximately when they were contacted. This field is required when the merchant has been contacted."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<input type="text" class="form-control" id="MerchContactDate" name="MerchantContactDay" value="<?php print $TransactionData[0][10]; ?>" onFocus="showCalendarControl(this);" id="merchcontactdate" pattern="^[0-9-]+$" title="'YYYY-MM-DD'">
					</div>
				</div><hr />
				
				<div class="row">
					<div class="col-md-6">
						<label>Merchant Contact Description:</label>&nbsp;<a target="_blank" title="This field is for description/comments about the merchant. This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<textarea class="form-control" onblur="this.value = this.value.toUpperCase();" name="merchantDescription" rows="2" maxlength="4096"><?php print $TransactionData[0][11]; ?></textarea>
					</div>
					<div class="col-md-6">
						<label>Merchant Notes:</label>&nbsp;<a target="_blank" title="This field is for any additional notes about the merchant. This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<textarea class="form-control" onblur="this.value = this.value.toUpperCase();" name="MerchantNotes" rows="2" maxlength="4096"><?php print $TransactionData[0][26]; ?></textarea>
					</div>
				</div><hr />
				
				<div class="row">
					<div class="col-md-12">
						<label>Employee Comments:</label>&nbsp;<a target="_blank" title="This field is for Institutional comments. These comments are private and for our use only. This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<textarea class="form-control" onblur="this.value = this.value.toUpperCase();" name="transactionDescription" rows="2" maxlength="4096"><?php print $TransactionData[0][21]; ?></textarea>
					</div>
					
				</div><hr />
				
				<!-- Compromise, Transaction LOSS, Provisional Credit (All Letters and Reversal Error), ChargeBack -->
				
				<div class="row">
					<div class="col-md-3">
						<label>Date Provisional Credit Letter Created:</label>&nbsp;<a target="_blank" title="This field shows you when the provisional credit letter was printed. This is not editable."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<input type="text" class="form-control2" name="pcCredGiven" value="<?php print $TransactionData[0][15]; ?>" onFocus="showCalendarControl(this);" readonly>
					</div>
					<div class="col-md-3">
						<label>Date Provisional Credit Letter Mailed out:</label>&nbsp;<a target="_blank" title="This field is when the provisional credit letter was printed off and sent to the customer. This should be set using the calendar pop-up tool when you print the letters off."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<input type="text" class="form-control" name="pcCredLetterSent" value="<?php print $TransactionData[0][17]; ?>" onFocus="showCalendarControl(this);" id="datePClettersent" pattern="^[0-9-]+$" title="'YYYY-MM-DD'">
					</div>
				</div><hr />
				<div class="row">
					<div class="col-md-3">
						<label>Provisional Credit Reversal Reason:</label>&nbsp;<a target="_blank" title="This field shows why the provisional credit was reversed. The default value is not reversed."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<select name="ReversalID" class="form-control">
<?php

for ($i=0; $i < $NumberofRever; $i++){
	
	if($reversal_data[$i][0] == $TransactionData[0][14]){
		print '<option value="' . $reversal_data[$i][0] . '" selected="true">' . $reversal_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $reversal_data[$i][0] . '">' . $reversal_data[$i][1] . '</option>';
	}
}

?>
						</select>
					</div>
					<div class="col-md-3">
						<label>Date Provisional Credit Rescinded:</label>&nbsp;<a target="_blank" title="This field shows when the provisional credit was reversed/rescinded. Please select when the using the calendar tool."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<input type="text" class="form-control" name="pcCredRecend" value="<?php print $TransactionData[0][16]; ?>" onFocus="showCalendarControl(this);" id="datePCrescind" pattern="^[0-9-]+$" title="'YYYY-MM-DD'">
					</div>
					<div class="col-md-3">
						<label>Date Provisional Credit Reversal Letter Sent:</label>&nbsp;<a target="_blank" title="This field is when the provisional credit reversal letter was printed off and sent to the customer."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<input type="text" class="form-control" name="pcCredReverseLetterSent" value="<?php print $TransactionData[0][18]; ?>" onFocus="showCalendarControl(this);" id="dateReversalPCLettersent" pattern="^[0-9-]+$" title="'YYYY-MM-DD'">
					</div>
				</div><hr />
				
				<div class="row">
					<div class="col-md-3">
						<label>Chargeback Submitted</label>&nbsp;<a target="_blank" title="This field is to show that a chargeback was submitted. The default value is no."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<select name="chargebackSubmitted" class="form-control"> 
<?php

if($TransactionData[0][19] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no">No</option>';
}
else if($TransactionData[0][19] == 0){
	print '<option value="yes">Yes</option>';
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
						<label>Chargeback Accepted</label>&nbsp;<a target="_blank" title="This field is to show that a chargeback was accepted. The default value is no."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<select name="chargebackAccepted" class="form-control"> 
<?php

if($TransactionData[0][20] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no">No</option>';
}
else if($TransactionData[0][20] == 0){
	print '<option value="yes">Yes</option>';
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
						<label>Compromise:</label>&nbsp;<a target="_blank" title="Please select the compromise that let to this dispute. The default value is not found for if there was no compromise or if it is unknown what led to the dispute."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<select name="compromiseID" class="form-control">
							<option value="select">Select One</option>
<?php

for ($i=0; $i < $NumberofComp; $i++){
	
	if($compromise_data[$i][0] == $TransactionData[0][22]){
		print '<option value="' . $compromise_data[$i][0] . '" selected="true">' . $compromise_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $compromise_data[$i][0] . '">' . $compromise_data[$i][1] . '</option>';
	}
}

?>
						</select>
					</div>
					<div class="col-md-9">
						<label>Transaction Results/Fiserv Results:</label>&nbsp;<a target="_blank" title="This field is for comments/description about the compromise that led to the dispute."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<textarea class="form-control" onblur="this.value = this.value.toUpperCase();" name="compromiseDescription" rows="2" maxlength="4096"><?php print $TransactionData[0][23]; ?></textarea>
					</div>
				</div><hr />
				
				<div class="row">
					<div class="col-md-12">
						<label>Attached Documents:</label>
<?php

$AllAttachmentQuery = "SELECT filename, comments, id FROM checkattachments WHERE caseid='" . $EditCaseID . "' AND iddeleted=FALSE";
$AllAttachmentQuery_Data = $dtcon->query($AllAttachmentQuery);
$AllAttachmentData = $AllAttachmentQuery_Data->fetch_all();

//var_dump($AllAttachmentQuery, $AllAttachmentData);

print "<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>";
print "<thead>";
print "<tr>";
print "<th style='width:23%;'>Attachment Name</th>";
print "<th style='width:60%;'>Attachment Comment</th>";
print '<th style="width:17%;">Delete Attachment&nbsp;<a target="_blank" title="If you delete an attachment by accident, please contact IT to get it restored."><img src="./assets/img/help-button-icon.png" height="15px"/></a></th>';
print "</tr>";
print "</thead>";
print "<tbody>";

foreach($AllAttachmentData as $AttachmentArr){
	print "<tr>";
	print "<td><a target='_blank' href=\"./FileFolder/UserAttachedDocuments/" . $AttachmentArr[0] . "\">" . $AttachmentArr[0] . "</a></td>";
	print "<input type='hidden' name='attachmentID[]' value='".$AttachmentArr[2]."'>";
	print '<td><textarea class="form-control" onblur="this.value = this.value.toUpperCase();" name="attachmentComments[]" rows="2" maxlength="4096">'.$AttachmentArr[1].'</textarea></td>';
	print '<td><button type="submit" class="btn btn-danger" name="deleteAttach" value="'.$AttachmentArr[2].'" onclick="return confirm(\'Click OK to confirm deletion.\')">Delete</button></td>';
	print "</tr>";
}

print "</tbody>";
print "</table>";




?>
					</div>
				</div><hr />
				
				
				<input type="submit" class="btn btn-primary" name="editTransactionSubmit" value="Submit Changes" style="width:185px;">
				
				</form>
                
				
				
				
				<div style="line-height: 1000%">
				&nbsp;
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

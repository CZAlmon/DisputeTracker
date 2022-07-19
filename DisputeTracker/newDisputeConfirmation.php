<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');



if ($accesslevel < 3){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}



$caseID = 0;


//DB Queries
//===============================================================
$AccountTypeCOUNT = 0;
$CardTypeCOUNT = 0;
$CardStatusCOUNT = 0;
$CardPossessionCOUNT = 0;
$DisputeReasonCOUNT = 0;
$CompromiseSelectCOUNT = 0;


if($AccTypeResult = $dtcon->query("SELECT typetext FROM accounttype WHERE iddeleted='0'")){
	$AccountTypeCOUNT = $AccTypeResult->num_rows;
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Account Type Count Query! Please contact the System Admin';
	header( "Location: newDisputeTransactions.php" );
	exit();
}
if($CardTypeResult = $dtcon->query("SELECT typetext FROM cardtype WHERE iddeleted='0'")){
	$CardTypeCOUNT = $CardTypeResult->num_rows;
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Type Count Query! Please contact the System Admin';
	header( "Location: newDisputeTransactions.php" );
	exit();
}
if($CardStatusResult = $dtcon->query("SELECT statustext FROM cardstatus WHERE iddeleted='0'")){
	$CardStatusCOUNT = $CardStatusResult->num_rows;
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Status Count Query! Please contact the System Admin';
	header( "Location: newDisputeTransactions.php" );
	exit();
}
if($CardPossResult = $dtcon->query("SELECT possessiontext FROM cardpossession WHERE iddeleted='0'")){
	$CardPossessionCOUNT = $CardPossResult->num_rows;
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Possession Count Query! Please contact the System Admin';
	header( "Location: newDisputeTransactions.php" );
	exit();
}
if($DisputeReasonResult = $dtcon->query("SELECT reason FROM disputereasons WHERE iddeleted='0'")){
	$DisputeReasonCOUNT = $DisputeReasonResult->num_rows;
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Dispute Reason Count Query! Please contact the System Admin';
	header( "Location: newDisputeTransactions.php" );
	exit();
}
if($ComproSelResult = $dtcon->query("SELECT merchantid FROM compromise WHERE iddeleted='0'")){
	$CompromiseSelectCOUNT = $ComproSelResult->num_rows;
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Compromise Selection Count Query! Please contact the System Admin';
	header( "Location: newDisputeTransactions.php" );
	exit();
}

if($AccountTypeCOUNT == 0 || $CardTypeCOUNT == 0 || $CardStatusCOUNT == 0 || $CardPossessionCOUNT == 0 || $DisputeReasonCOUNT == 0 || $CompromiseSelectCOUNT == 0){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Count Queries! Please contact the System Admin';
	header( "Location: newDisputeTransactions.php" );
	exit();
}

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


$caseID = $_SESSION["Dispute_CaseID"];




if(($_SERVER["REQUEST_METHOD"] == "POST") || isset($_SESSION["FinishDisputeOne"])){
	
	date_default_timezone_set("America/Chicago");
	$timeofChange = date("Y-m-d, G:i:s");
	
	//print $caseID;
	//var_dump($_SESSION, $_FILES);
	//var_dump($_POST);
	//exit();
	
	if(isset($_POST["changeValues"])){
		
		if(!isset($_POST["FirstName"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Name must be entered!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$firstnameVAR = $_POST["FirstName"];
			$stringGood = isAscii($firstnameVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Ascii Characters in the first name were Detected! Please try again using regular characters!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z ,.'-]+$/", $firstnameVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the first name were Detected! Please try again using regular characters!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$firstnameVAR = $dtcon->real_escape_string($firstnameVAR);
			
		}
		
		if(!isset($_POST["LastName"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Name must be entered!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$lastnameVAR = $_POST["LastName"];
			$stringGood = isAscii($lastnameVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Ascii Characters in the last name were Detected! Please try again using regular characters!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z ,.'-]+$/", $lastnameVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the last name were Detected! Please try again using regular characters!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$lastnameVAR = $dtcon->real_escape_string($lastnameVAR);
			
		}
		
		if(!isset($_POST["PhoneNum"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Phone Number must be entered.';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$phoneNumVAR = $_POST["PhoneNum"];
			
			if(!preg_match("/^[0-9]{3}[ \-]{0,1}[0-9]{3}[ \-]{0,1}[0-9]{4}$/", $phoneNumVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Phone Number must be digits, slashes, parentheses, or dashes only. Ex: \'123 456 7890\', \'123-456-7890\', \'1234567890\'';
				header( "Location: newDisputeConfirmation.php" );
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
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			//Old Regex: ^\w+[[\.-]?\w+]*@\w+[[\.-]?\w+]*\.\w{2,3}+$
			//if(!preg_match("/^\S+@\S+\.\S+$/", $emailAddrVAR) && $emailAddrVAR != ""){
			//	$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the email were Detected! Please try again!';
			//	header( "Location: newDisputeConfirmation.php" );
			//	exit();
			//}
			
			$emailAddrVAR = $dtcon->real_escape_string($emailAddrVAR);
			
		}
		
		if(!isset($_POST["AddressOne"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Address Line One Error. Please try again.';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$AddressVar = $_POST["AddressOne"];
			$stringGood = isAscii($AddressVar);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Address Line One Error. Please try again.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z0-9\.\-\s]+$/", $AddressVar)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in Address One were Detected! Please try again!';
				header( "Location: newDisputeConfirmation.php" );
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
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z0-9\.\-\s]+$/", $Address2VAR) && $Address2VAR != ""){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in Address Two were Detected! Please try again!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$Address2VAR = $dtcon->real_escape_string($Address2VAR);
			
		}
		
		if(!isset($_POST["CityAddr"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'City Line Error. Please try again.';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$CityAddrVar = $_POST["CityAddr"];
			$stringGood = isAscii($CityAddrVar);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'City Line Error. Please try again.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z ]+$/", $CityAddrVar)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the City Name were Detected! Please try again!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$CityAddrVar = $dtcon->real_escape_string($CityAddrVar);
			
		}
		
		if(!isset($_POST["StateAddr"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'State Line Error. Please try again.';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$StateAddrVar = $_POST["StateAddr"];
			$stringGood = isAscii($StateAddrVar);
			$stringState = isState($StateAddrVar);
			
			if(!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'State Line Error. Please try again.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			if(!$stringState){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'State Line Error. Please try again.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$StateAddrVar = $dtcon->real_escape_string($StateAddrVar);
			
		}
		
		if(!isset($_POST["ZipCode"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Zip code must be entered!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$ZipCodeVar = $_POST["ZipCode"];
			
			if (!is_numeric($ZipCodeVar)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Zip code can be Numbers only!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if(strlen($ZipCodeVar) < 5 || strlen($ZipCodeVar) > 5){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Zip code can be 5 Numbers only!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			//Future Update?
			
		} 
		
		if(!isset($_POST["inpersonorPhone"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the customer is on the phone or in person!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$custinpersonphoneVAR = $_POST["inpersonorPhone"];
			
			if ($custinpersonphoneVAR != "inperson" && $custinpersonphoneVAR != "phone"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Customer is on the phone or in person! Please contact the System Admin for more help.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if($custinpersonphoneVAR == "inperson"){
				$custinpersonphoneVAR = 1;
			}
			else{
				$custinpersonphoneVAR = 2;
			}
		} 
		
		if(!isset($_POST["AccountNumber"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'An Account Number must be entered!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$accountNumberVAR = $_POST["AccountNumber"];
			
			if (!is_numeric($accountNumberVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number can be Numbers only!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			//Future Update?
			
		} 
		
		if(!isset($_POST["AccountType"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select the Account Type!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$accountTypeVAR = $_POST["AccountType"];
			
			if (!is_numeric($accountTypeVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is an error with the Account Type! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$accountTypeINT = intval($accountTypeVAR);
			
			if($accountTypeINT < 1 || $accountTypeINT > $Numberof_acct_Types){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Account Type! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			//Future Update?
			
		} 
		
		if(!isset($_POST["BusiCustType"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the dispute is a Business Customer or a Consumer!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$consumerorBusinessVAR = $_POST["BusiCustType"];
			
			if ($consumerorBusinessVAR != "Consumer" && $consumerorBusinessVAR != "Business"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Illegal Characters detected in Consumer or Business Selection! Please contact the System Admin!';
				header( "Location: newDisputeConfirmation.php" );
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
				header( "Location: newDisputeConfirmation.php" );
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
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$CARDNumberVAR = $_POST["Card_Number"];
			
			if (!is_numeric($CARDNumberVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number can be Numbers only!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$len = strlen($CARDNumberVAR);
			
			if($len != 16){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number has to be exactly 16 digits!';
				header( "Location: newDisputeConfirmation.php" );
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
				header( "Location: newDisputeConfirmation.php" );
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
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$CARDTypeVAR = $_POST["CardType"];
			
			if (!is_numeric($CARDTypeVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Type! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$CARDTypeINT = intval($CARDTypeVAR);
			
			if($CARDTypeINT < 1 || $CARDTypeINT > $CardTypeCOUNT){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Type! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			//Future Update?
		} 
		
		if(!isset($_POST["CardStatus"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select the card status!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$CARDstatusVAR = $_POST["CardStatus"];
			
			if (!is_numeric($CARDstatusVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Status Selection! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$CARDstatusINT = intval($CARDstatusVAR);
			
			if($CARDstatusINT < 1 || $CARDstatusINT > $CardStatusCOUNT){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Status Selection! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			//Future Update?
		} 
		
		if(!isset($_POST["CardPossession"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the customer has the card or not!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$CARDpossessionVAR = $_POST["CardPossession"];
			
			if (!is_numeric($CARDpossessionVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Possession Selection! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$CARDpossessionINT = intval($CARDpossessionVAR);
			
			if($CARDpossessionINT < 1 || $CARDpossessionINT > $CardPossessionCOUNT){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Possession Selection! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
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
					header( "Location: newDisputeConfirmation.php" );
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
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$CARDnumbercommentsVAR = $dtcon->real_escape_string($CARDnumbercommentsVAR);
			
		}
		
		if(!isset($_POST["AttachmentDescription"])){
			$NEWAttachmentDescription = "";
		}
		else{
			$NEWAttachmentDescription = $_POST["AttachmentDescription"];
			
			$stringGood = isAscii($NEWAttachmentDescription);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Attachment Comments were Detected! Please try again using regular characters!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$NEWAttachmentDescription = $dtcon->real_escape_string($NEWAttachmentDescription);
			
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
		//=================================================================================================================================================================
		
		
		
		$FieldCountTranID = count($_POST["transactionID"]);
		$FieldCountTranDay = count($_POST["TransactionDay"]);
		$FieldCountPostDay = count($_POST["PostedDay"]);
		$FieldCountAmount = count($_POST["AmountDisputed"]);
		$FieldCountReceipt = count($_POST["receiptstatus"]);
		$FieldCountDisputeRe = count($_POST["DisputeReason"]);
		$FieldCountDisputeDes = count($_POST["DisputeDescription"]);
		$FieldCountMerchName = count($_POST["MerchantName"]);
		$FieldCountMerchCont = count($_POST["merchantcontactstatus"]);
		$FieldCountMerchContDay = count($_POST["MerchantContactDay"]);
		$FieldCountMerchDes = count($_POST["merchantDescription"]);
		$FieldCountTranDes = count($_POST["transactionDescription"]);
		
		//var_dump($FieldCountTranID, $FieldCountTranDay, $FieldCountPostDay, $FieldCountAmount, $FieldCountReceipt, $FieldCountDisputeRe, $FieldCountDisputeDes, $FieldCountMerchName, $FieldCountMerchCont, $FieldCountMerchContDay, $FieldCountMerchDes, $FieldCountTranDes);
		
		//If all the values dont equal each other
		if(!(($FieldCountTranID == $FieldCountTranDay) && ($FieldCountTranDay == $FieldCountPostDay) && ($FieldCountPostDay == $FieldCountAmount) && ($FieldCountAmount == $FieldCountReceipt) && ($FieldCountReceipt == $FieldCountDisputeRe) && ($FieldCountDisputeRe == $FieldCountDisputeDes) && ($FieldCountDisputeDes == $FieldCountMerchName) && ($FieldCountMerchName == $FieldCountMerchCont) && ($FieldCountMerchCont == $FieldCountMerchContDay) && ($FieldCountMerchContDay == $FieldCountMerchDes) && ($FieldCountMerchDes == $FieldCountTranDes))){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the transaction field values! Please try again or contact the system admin.';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		
		$TranIDArray = $_POST["transactionID"];
		$TranDayArray = $_POST["TransactionDay"];
		$PostDayArray = $_POST["PostedDay"];
		$AmountArray = $_POST["AmountDisputed"];
		$ReceiptArray = $_POST["receiptstatus"];
		$DisputeReArray = $_POST["DisputeReason"];
		$DisputeDesArray = $_POST["DisputeDescription"];
		$MerchNameArray = $_POST["MerchantName"];
		$MerchContArray = $_POST["merchantcontactstatus"];
		$ContDayArray = $_POST["MerchantContactDay"];
		$MerchDesArray = $_POST["merchantDescription"];
		$TranDesArray = $_POST["transactionDescription"];
		
		$DONETranIDArray = array();				//array_push($DONETranIDArray, $TempTransactionID);
		$DONETranDayArray = array();			//array_push($DONETranDayArray, $TransactionDayVar);
		$DONEPostDayArray = array();			//array_push($DONEPostDayArray, $PostedDayVar);
		$DONEAmountArray = array();				//array_push($DONEAmountArray, $AmountdisputedVar);
		$DONEReceiptArray = array();			//array_push($DONEReceiptArray, $RecieptStatusVar);
		$DONEDisputeReArray = array();			//array_push($DONEDisputeReArray, $DisputeReasonVar);
		$DONEDisputeDesArray = array();			//array_push($DONEDisputeDesArray, $DisputeDescriptionVar);
		$DONEMerchNameArray = array();			//array_push($DONEMerchNameArray, $MerchantNameVAR);
		$DONEMerchContArray = array();			//array_push($DONEMerchContArray, $MerchantContactVar);
		$DONEContDayArray = array();			//array_push($DONEContDayArray, $MerchantContactDayVar);
		$DONEMerchDesArray = array();			//array_push($DONEMerchDesArray, $MerchantDescriptionVar);
		$DONETranDesArray = array();			//array_push($DONETranDesArray, $TransactionCommentsVar);
		
		
		
		for($i=0;$i<$FieldCountTranID;$i++){
			
			if(!isset($_POST["transactionID"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Transaction ID! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$TempTransactionID = $TranIDArray[$i];
				
				if (!is_numeric($TempTransactionID)){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Transaction ID Value! Please contact the System Admin';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				array_push($DONETranIDArray, $TempTransactionID);
			}
			
			if(!isset($_POST["TransactionDay"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Transaction Day Error! Please Enter a valid Day the transaction took place in the format YYYY-MM-DD.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$TransactionDayVar = $TranDayArray[$i];
				$dateGood = validateDate($TransactionDayVar, 'Y-m-d');
				$dateGoodVAR = validatePastDate($TransactionDayVar);
				
				if ($dateGood != true || $dateGoodVAR != true){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Transaction Day Error! Please Enter a valid Day the transaction took place in the format YYYY-MM-DD. You entered: '.$TransactionDayVar;
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				array_push($DONETranDayArray, $TransactionDayVar);
			}
			
			if(!isset($_POST["PostedDay"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Day the transaction posted in the format YYYY-MM-DD.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$PostedDayVar = $PostDayArray[$i];
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
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				elseif($dateGoodVAR != true){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Past Day the transaction posted in the format YYYY-MM-DD. You entered: '.$PostedDayVar;
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				elseif($dateGoodVARTWO != true){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Past Posted Date the transaction posted in the format YYYY-MM-DD. You entered: '.$PostedDayVar;
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				else{
					//Pass
				}
				
				//if ((($dateGood != true) || ($dateGoodVAR != true) || ($dateGoodVARTWO != true)) || ($PostedDayVar != "")){
				//	$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Day the transaction posted in the format YYYY-MM-DD. You entered: '.$PostedDayVar;
				//	header( "Location: newDisputeConfirmation.php" );
				//	exit();
				//}
				
				array_push($DONEPostDayArray, $PostedDayVar);
			}
			
			if(!isset($_POST["AmountDisputed"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Amount Disputed Error! Please enter a valid dollar amount.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$AmountdisputedVar = $AmountArray[$i];
				
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
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				array_push($DONEAmountArray, $AmountdisputedVar);
			}
			
			if(!isset($_POST["receiptstatus"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Receipt Status Error! Please select "yes" or "no".';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$RecieptStatusVar = $ReceiptArray[$i];
				
				if ($RecieptStatusVar != "yes" && $RecieptStatusVar != "no"){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Customer has the receipt! Please contact the System Admin for more help.';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				if($RecieptStatusVar == "yes"){
					$RecieptStatusVar = "1";
				}
				else{
					$RecieptStatusVar = "0";
				}
				
				array_push($DONEReceiptArray, $RecieptStatusVar);
			}
			
			if(!isset($_POST["MerchantName"])){		//Maybe not required??
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Name Error! Please input a merchant name.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$MerchantNameVAR = $MerchNameArray[$i];
				$stringGood = isAscii($MerchantNameVAR);
				
				if (!$stringGood){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Merchant Name were Detected! Please try again using regular characters!';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				if(!preg_match("/^[a-zA-Z0-9\&\'\.\-\s\:\;\?\/\,\]\[\}\{\!\@\#\$\%\^\*\(\)\_\=\+]+$/", $MerchantNameVAR)){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Merchant Name were Detected! Please try again using regular characters!';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				//print $MerchantNameVAR;
				$MerchantNameVAR = $dtcon->real_escape_string($MerchantNameVAR);
				//print $MerchantNameVAR;
				//exit();
				
				array_push($DONEMerchNameArray, $MerchantNameVAR);
			}
			
			if(!isset($_POST["merchantcontactstatus"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Contact Status Error! Please Select a Contact Status.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$MerchantContactVar = $MerchContArray[$i];
				
				if ($MerchantContactVar != "yes" && $MerchantContactVar != "no"){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Merchant has been Contacted!';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				if($MerchantContactVar == "yes"){
					$MerchantContactVar = "1";
				}
				else{
					$MerchantContactVar = "0";
				}
				
				array_push($DONEMerchContArray, $MerchantContactVar);
			}
			
			if(!isset($_POST["MerchantContactDay"])){
				$MerchantContactDayVar = "";
			}
			else{
				
				if($MerchantContactVar == "0"){
					$MerchantContactDayVar = "";
				}
				else{
					$MerchantContactDayVar = $ContDayArray[$i];
					$dateGood = validateDate($MerchantContactDayVar, 'Y-m-d');
					$dateGoodVAR = validatePastDate($MerchantContactDayVar, 'Y-m-d');
					$dateGoodVARTWO = validatePostedDate($TransactionDayVar, $MerchantContactDayVar);
					
					if ($dateGood != true || $dateGoodVAR != true || $dateGoodVARTWO != true){
						$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Contact Day Error! Please Enter a valid Day the merchant was contacted in the format YYYY-MM-DD. You entered: '.$MerchantContactDayVar;
						header( "Location: newDisputeConfirmation.php" );
						exit();
					}	
				}
				
				array_push($DONEContDayArray, $MerchantContactDayVar);
			}
			
			if(!isset($_POST["DisputeReason"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Dispute Reason Error! Please Select a Reason.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$DisputeReasonVar = $DisputeReArray[$i];
				
				if (!is_numeric($DisputeReasonVar)){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Dispute Reason Selection Value! Please contact the System Admin';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}

				$DisputeReasonINT = intval($DisputeReasonVar);
				
				if($DisputeReasonINT < 1 || $DisputeReasonINT > $NumberofReason){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Dispute Reason Selection! Please contact the System Admin';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				array_push($DONEDisputeReArray, $DisputeReasonVar);
			}
			
			if(!isset($_POST["DisputeDescription"])){
				$DisputeDescriptionVar = "";
			}
			else{
				$DisputeDescriptionVar = $DisputeDesArray[$i];
				
				$stringGood = isAscii($DisputeDescriptionVar);

				if (!$stringGood){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Dispute Description were Detected! Please try again using regular characters!';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				$DisputeDescriptionVar = $dtcon->real_escape_string($DisputeDescriptionVar);
				
				array_push($DONEDisputeDesArray, $DisputeDescriptionVar);
			}
			
			if(!isset($_POST["merchantDescription"])){
				$MerchantDescriptionVar = "";
			}
			else{
				$MerchantDescriptionVar = $MerchDesArray[$i];
				
				$stringGood = isAscii($MerchantDescriptionVar);
				
				if (!$stringGood){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Merchant Description were Detected! Please try again using regular characters!';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				$MerchantDescriptionVar = $dtcon->real_escape_string($MerchantDescriptionVar);
				
				array_push($DONEMerchDesArray, $MerchantDescriptionVar);
			}
			
			if(!isset($_POST["transactionDescription"])){
				$TransactionCommentsVar = "";
			}
			else{
				$TransactionCommentsVar = $TranDesArray[$i];
				
				$stringGood = isAscii($TransactionCommentsVar);
				
				if (!$stringGood){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Transaction Comments were Detected! Please try again using regular characters!';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				$TransactionCommentsVar = $dtcon->real_escape_string($TransactionCommentsVar);
				
				array_push($DONETranDesArray, $TransactionCommentsVar);
			}
			
		}
		
		
		/*
		var_dump($_POST);
		print "<br>";
		var_dump($_SESSION);
		print "<br>";
		var_dump($_FILES);
		print "<br>";
		var_dump($caseID);
		print "<br>";
		
		var_dump($firstnameVAR);
		print "<br>";
		var_dump($lastnameVAR);
		print "<br>";
		var_dump($phoneNumVAR);
		print "<br>";
		var_dump($emailAddrVAR);
		print "<br>";
		var_dump($AddressVar);
		print "<br>";
		var_dump($Address2VAR);
		print "<br>";
		var_dump($CityAddrVar);
		print "<br>";
		var_dump($StateAddrVar);
		print "<br>";
		var_dump($ZipCodeVar);
		print "<br>";
		var_dump($custinpersonphoneVAR);
		print "<br>";
		var_dump($accountNumberVAR);
		print "<br>";
		var_dump($accountTypeVAR);
		print "<br>";
		var_dump($consumerorBusinessVAR);
		print "<br>";
		var_dump($accountcommentsVAR);
		print "<br>";
		var_dump($NewAccountBool);
		print "<br>";
		var_dump($CARDNumberVAR);
		print "<br>";
		var_dump($ChipCardVAR);
		print "<br>";
		var_dump($CARDTypeVAR);
		print "<br>";
		var_dump($CARDstatusVAR);
		print "<br>";
		var_dump($CARDpossessionVAR);
		print "<br>";
		var_dump($CARDmissingdateVAR);
		print "<br>";
		var_dump($CARDnumbercommentsVAR);
		print "<br>";
		var_dump($NEWAttachmentDescription);
		print "<br>";
		
		var_dump($DONETranIDArray);				//array_push($DONETranIDArray, $TempTransactionID);
		print "<br>";
		var_dump($DONETranDayArray);			//array_push($DONETranDayArray, $TransactionDayVar);
		print "<br>";
		var_dump($DONEPostDayArray);			//array_push($DONEPostDayArray, $PostedDayVar);
		print "<br>";
		var_dump($DONEAmountArray);				//array_push($DONEAmountArray, $AmountdisputedVar);
		print "<br>";
		var_dump($DONEReceiptArray);			//array_push($DONEReceiptArray, $RecieptStatusVar);
		print "<br>";
		var_dump($DONEDisputeReArray);			//array_push($DONEDisputeReArray, $DisputeReasonVar);
		print "<br>";
		var_dump($DONEDisputeDesArray);			//array_push($DONEDisputeDesArray, $DisputeDescriptionVar);
		print "<br>";
		var_dump($DONEMerchNameArray);			//array_push($DONEMerchNameArray, $MerchantNameVAR);
		print "<br>";
		var_dump($DONEMerchContArray);			//array_push($DONEMerchContArray, $MerchantContactVar);
		print "<br>";
		var_dump($DONEContDayArray);			//array_push($DONEContDayArray, $MerchantContactDayVar);
		print "<br>";
		var_dump($DONEMerchDesArray);			//array_push($DONEMerchDesArray, $MerchantDescriptionVar);
		print "<br>";
		var_dump($DONETranDesArray);			//array_push($DONETranDesArray, $TransactionCommentsVar);
		print "<br>";
		var_dump($DONEAttachmentID);			
		print "<br>";
		var_dump($DONEAttachmentComments);			
		print "<br>";
		exit();
		*/
		
		
		$AttachmentsArray = array();
		$FilesExist = FALSE;
		$FilesBOOL = TRUE;

		foreach($_FILES['Attachments']['error'] as $LoopVal){
			$FilesExist = TRUE;
			
			if($LoopVal != 0){			//If any File has an error, Dont go in that If statement
				$FilesBOOL = FALSE;
			}
		}

		if (isset($_FILES['Attachments']) && $FilesBOOL && $FilesExist){
			
			$newAttachName = "";
			$attach_query = "SELECT filename FROM checkattachments WHERE caseid='".$caseID."'";
			$attach_query_data = $dtcon->query($attach_query);
			$attach_data = $attach_query_data->fetch_all();
			$attachnumbers = count($attach_data);
			
			$checkUploadAddress = "FileFolder/UserAttachedDocuments/";
			
			$errors = array();
			
			foreach($_FILES['Attachments']['tmp_name'] as $key => $tmp_name ){
				
				$tmpArray = array();
				
				$file_name = $key.$_FILES['Attachments']['name'][$key];
				$file_size = $_FILES['Attachments']['size'][$key];
				$file_tmp  = $_FILES['Attachments']['tmp_name'][$key];
				$file_type = $_FILES['Attachments']['type'][$key]; 
				$file_errors = $_FILES['Attachments']['error'][$key]; 
				
				// 15000000 bytes = ~14 MB
				if($file_size > 15000000){
					$errors[]='File size must be less than 14 MB';
				}
				
				if(empty($errors)==true){
					
					$attachnumbers++;
					$pathparts = explode('.', $file_name);
					$extensionVar = end((array_values($pathparts)));
					$newName = $checkUploadAddress . "case" . $caseID . "_attachment" . $attachnumbers . "." . $extensionVar;
					
					if(rename($file_tmp, $newName)){
						
						$tmpName = "case" . $caseID . "_attachment" . $attachnumbers . "." . $extensionVar;
						array_push($tmpArray, $tmpName);
						array_push($tmpArray, $newName);
						array_push($AttachmentsArray, $tmpArray);
						//It works.
					}
					else{
						$_SESSION["ADD_DISPUTE_ERROR"] = 'File Move Failed! Please Contact the System Admin.';
						header( "Location: newDisputeConfirmation.php" );
						exit();
					}
					
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'An Error occured with the file upload! Please Contact the System Admin.'.$errors;
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
			}
		}

		//var_dump($AttachmentsArray);
		//print "<br>";
		//var_dump($_FILES);
		//exit();
		
		$NewChangeInsertstring = "";

		$newAtatchNum = count($AttachmentsArray);

		for($i=0; $i < $newAtatchNum; $i++){
			
			$checkAttachmentInsert = "INSERT INTO checkattachments(id, caseid, filename, filelocation, comments, iddeleted) VALUES (NULL, '".$caseID."', '".$AttachmentsArray[$i][0]."', '".$AttachmentsArray[$i][1]."', '".$NEWAttachmentDescription."', FALSE)";
			
			if ($dtcon->query($checkAttachmentInsert) === TRUE) {
				
				//print "Insert Worked";
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with an Attachment Insert! Please Contact the System Admin!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$NewChangeInsertstring = $NewChangeInsertstring . "\n\n" . $checkAttachmentInsert;
		}
		
		
		$caseFix = "UPDATE checkcases SET custfname='".$firstnameVAR."', custlname='".$lastnameVAR."', custphone='".$phoneNumVAR."', custemail='".$emailAddrVAR."', custaddressone='".$AddressVar."', custaddresstwo='".$Address2VAR."', custcityaddr='".$CityAddrVar."', custstateaddr='".$StateAddrVar."', custzipaddr='".$ZipCodeVar."', customerstartmethod='".$custinpersonphoneVAR."' WHERE id='".$caseID."'";
		
		$accountFix = "UPDATE checkaccountnumbers SET accountnumber='".$accountNumberVAR."', accounttype='".$accountTypeVAR."', businessaccount='".$consumerorBusinessVAR."', comments='".$accountcommentsVAR."', accoountnew='".$NewAccountBool."' WHERE caseid='".$caseID."'";
		
		$cardFix = "UPDATE checkcardnumbers SET cardnumber='".$CARDNumberVAR."', cardtype='".$CARDTypeVAR."', cardstatus='".$CARDstatusVAR."', cardpossession='".$CARDpossessionVAR."', cardmissingdate='".$CARDmissingdateVAR."', chipcard='".$ChipCardVAR."', comments='".$CARDnumbercommentsVAR."' WHERE caseid='".$caseID."'";
		
		/*
		var_dump($caseFix);
		print "<br>";
		print "<br>";
		var_dump($accountFix);
		print "<br>";
		print "<br>";
		var_dump($cardFix);
		exit();
		*/
		
		$NewChangeInsertstring = $NewChangeInsertstring . "\n\n" . $caseFix . "\n\n" . $accountFix . "\n\n" . $cardFix;
		
		if ($dtcon->query($caseFix) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Case Update! Please Contact the System Admin!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		if ($dtcon->query($accountFix) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Account Update! Please Contact the System Admin!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		if ($dtcon->query($cardFix) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Card Update! Please Contact the System Admin!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		
		
		//Transaction Fixes
		for($i=0;$i<$FieldCountTranID;$i++){
			
			$temptransactionFix = "UPDATE checktransactions SET amount='".$DONEAmountArray[$i]."', transactiondate='".$DONETranDayArray[$i]."', dateposted='".$DONEPostDayArray[$i]."', disputereason='".$DONEDisputeReArray[$i]."', description='".$DONEDisputeDesArray[$i]."', merchantname='".$DONEMerchNameArray[$i]."', merchantcontacted='".$DONEMerchContArray[$i]."', merchantcontacteddate='".$DONEContDayArray[$i]."', merchantcontactdescription='".$DONEMerchDesArray[$i]."', receiptstatus='".$DONEReceiptArray[$i]."', comments='".$DONETranDesArray[$i]."' WHERE id='".$DONETranIDArray[$i]."' AND caseid='".$caseID."'";
			
			if ($dtcon->query($temptransactionFix) === TRUE) {
				
				//print "Insert Worked";
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Transaction Update! Transaction number: '.$DONETranIDArray[$i].' failed. Please Contact the System Admin!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$NewChangeInsertstring = $NewChangeInsertstring . "\n\n" . $temptransactionFix;
		}
		
		//Attachment Fixes
		for($i=0;$i<$AttachmentIDFieldCount;$i++){
			
			$tempAttachmentFix = "UPDATE checkattachments SET comments='".$DONEAttachmentComments[$i]."' WHERE id='".$DONEAttachmentID[$i]."' and caseid='".$caseID."'";
			
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
		
		$NewChangeInsertstring = $dtcon->real_escape_string($NewChangeInsertstring);
		
		//substr($NewChangeInsertstring, 4);
		
		if (strlen($NewChangeInsertstring)<16000) {
			//echo "less than 140";
			//Do nothing
		}
		else{
			if (strlen($NewChangeInsertstring)>16000) {
				//echo "more than 140";
				//Cut down String to 16000 
				//We can't limit the disputes, and the column is limited to 16384 characters. So we have to truncate the string and lose some of the inset values. This shouldn't happen for around 10 transactions, but is all dependant on Comments/text lengths.
				$NewChangeInsertstring = substr($NewChangeInsertstring,0,16000);
			}
			else {
				//echo "exactly 140";
				//Do nothing
			}
		}
		
		
		$NewChangeLogInsert = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '2', '".$caseID."', '".substr($NewChangeInsertstring, 4)."')";
		
		//var_dump($NewChangeLogInsert);
		//exit();
		
		if ($dtcon->query($NewChangeLogInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin! <p hidden>'.$NewChangeLogInsert.'</p>';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		
		
		$_SESSION["TransactionAdded"] = 'Dispute Updated Successfully!';
		
	}
	else if(isset($_POST["confirmValues"])){
		
		if(!isset($_POST["FirstName"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Name must be entered!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$firstnameVAR = $_POST["FirstName"];
			$stringGood = isAscii($firstnameVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the name were Detected! Please try again using regular characters!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z ,.'-]+$/", $firstnameVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the name were Detected! Please try again using regular characters!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$firstnameVAR = $dtcon->real_escape_string($firstnameVAR);
			
		}
		
		if(!isset($_POST["LastName"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Name must be entered!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$lastnameVAR = $_POST["LastName"];
			$stringGood = isAscii($lastnameVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the name were Detected! Please try again using regular characters!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z ,.'-]+$/", $lastnameVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the name were Detected! Please try again using regular characters!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$lastnameVAR = $dtcon->real_escape_string($lastnameVAR);
			
		}
		
		if(!isset($_POST["PhoneNum"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Phone Number must be entered.';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$phoneNumVAR = $_POST["PhoneNum"];
			
			if(!preg_match("/^[0-9]{3}[ \-]{0,1}[0-9]{3}[ \-]{0,1}[0-9]{4}$/", $phoneNumVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Phone Number must be digits, slashes, parentheses, or dashes only. Ex: \'123 456 7890\', \'123-456-7890\', \'1234567890\'';
				header( "Location: newDisputeConfirmation.php" );
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
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			//Old Regex: ^\w+[[\.-]?\w+]*@\w+[[\.-]?\w+]*\.\w{2,3}+$
			//if(!preg_match("/^\S+@\S+\.\S+$/", $emailAddrVAR) && $emailAddrVAR != ""){
			//	$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the email were Detected! Please try again!';
			//	header( "Location: newDisputeConfirmation.php" );
			//	exit();
			//}
			
			$emailAddrVAR = $dtcon->real_escape_string($emailAddrVAR);
			
		}
		
		if(!isset($_POST["AddressOne"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Address Line One Error. Please try again.';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$AddressVar = $_POST["AddressOne"];
			$stringGood = isAscii($AddressVar);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Address Line One Error. Please try again.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z0-9\.\-\s]+$/", $AddressVar)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in Address One were Detected! Please try again!';
				header( "Location: newDisputeConfirmation.php" );
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
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z0-9\.\-\s]+$/", $Address2VAR) && $Address2VAR != ""){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in Address Two were Detected! Please try again!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$Address2VAR = $dtcon->real_escape_string($Address2VAR);
			
		}
		
		if(!isset($_POST["CityAddr"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'City Line Error. Please try again.';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$CityAddrVar = $_POST["CityAddr"];
			$stringGood = isAscii($CityAddrVar);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'City Line Error. Please try again.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if(!preg_match("/^[a-zA-Z ]+$/", $CityAddrVar)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the City Name were Detected! Please try again!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$CityAddrVar = $dtcon->real_escape_string($CityAddrVar);
			
		}
		
		if(!isset($_POST["StateAddr"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'State Line Error. Please try again.';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$StateAddrVar = $_POST["StateAddr"];
			$stringGood = isAscii($StateAddrVar);
			$stringState = isState($StateAddrVar);
			
			if(!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'State Line Error. Please try again.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			if(!$stringState){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'State Line Error. Please try again.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$StateAddrVar = $dtcon->real_escape_string($StateAddrVar);
			
		}
		
		if(!isset($_POST["ZipCode"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Zip code must be entered!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$ZipCodeVar = $_POST["ZipCode"];
			
			if (!is_numeric($ZipCodeVar)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Zip code can be Numbers only!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if(strlen($ZipCodeVar) < 5 || strlen($ZipCodeVar) > 5){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Zip code can be 5 Numbers only!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			//Future Update?
			
		} 
		
		if(!isset($_POST["inpersonorPhone"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the customer is on the phone or in person!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$custinpersonphoneVAR = $_POST["inpersonorPhone"];
			
			if ($custinpersonphoneVAR != "inperson" && $custinpersonphoneVAR != "phone"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Customer is on the phone or in person! Please contact the System Admin for more help.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			if($custinpersonphoneVAR == "inperson"){
				$custinpersonphoneVAR = 1;
			}
			else{
				$custinpersonphoneVAR = 2;
			}
		} 
		
		if(!isset($_POST["AccountNumber"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'An Account Number must be entered!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$accountNumberVAR = $_POST["AccountNumber"];
			
			if (!is_numeric($accountNumberVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number can be Numbers only!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			//Future Update?
			
		} 
		
		if(!isset($_POST["AccountType"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select the Account Type!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$accountTypeVAR = $_POST["AccountType"];
			
			if (!is_numeric($accountTypeVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is an error with the Account Type! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$accountTypeINT = intval($accountTypeVAR);
			
			if($accountTypeINT < 1 || $accountTypeINT > $Numberof_acct_Types){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Account Type! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			//Future Update?
			
		} 
		
		if(!isset($_POST["BusiCustType"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the dispute is a Business Customer or a Consumer!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$consumerorBusinessVAR = $_POST["BusiCustType"];
			
			if ($consumerorBusinessVAR != "Consumer" && $consumerorBusinessVAR != "Business"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Illegal Characters detected in Consumer or Business Selection! Please contact the System Admin!';
				header( "Location: newDisputeConfirmation.php" );
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
				header( "Location: newDisputeConfirmation.php" );
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
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$CARDNumberVAR = $_POST["Card_Number"];
			
			if (!is_numeric($CARDNumberVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number can be Numbers only!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$len = strlen($CARDNumberVAR);
			
			if($len != 16){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number has to be exactly 16 digits!';
				header( "Location: newDisputeConfirmation.php" );
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
				header( "Location: newDisputeConfirmation.php" );
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
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$CARDTypeVAR = $_POST["CardType"];
			
			if (!is_numeric($CARDTypeVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Type! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$CARDTypeINT = intval($CARDTypeVAR);
			
			if($CARDTypeINT < 1 || $CARDTypeINT > $CardTypeCOUNT){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Type! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			//Future Update?
		} 
		
		if(!isset($_POST["CardStatus"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select the card status!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$CARDstatusVAR = $_POST["CardStatus"];
			
			if (!is_numeric($CARDstatusVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Status Selection! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$CARDstatusINT = intval($CARDstatusVAR);
			
			if($CARDstatusINT < 1 || $CARDstatusINT > $CardStatusCOUNT){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Status Selection! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			//Future Update?
		} 
		
		if(!isset($_POST["CardPossession"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the customer has the card or not!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		else{
			$CARDpossessionVAR = $_POST["CardPossession"];
			
			if (!is_numeric($CARDpossessionVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Possession Selection! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$CARDpossessionINT = intval($CARDpossessionVAR);
			
			if($CARDpossessionINT < 1 || $CARDpossessionINT > $CardPossessionCOUNT){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Possession Selection! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
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
					header( "Location: newDisputeConfirmation.php" );
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
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$CARDnumbercommentsVAR = $dtcon->real_escape_string($CARDnumbercommentsVAR);
			
		}
		
		if(!isset($_POST["AttachmentDescription"])){
			$NEWAttachmentDescription = "";
		}
		else{
			$NEWAttachmentDescription = $_POST["AttachmentDescription"];
			
			$stringGood = isAscii($NEWAttachmentDescription);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Attachment Comments were Detected! Please try again using regular characters!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$NEWAttachmentDescription = $dtcon->real_escape_string($NEWAttachmentDescription);
			
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
		//=================================================================================================================================================================
		
		
		
		$FieldCountTranID = count($_POST["transactionID"]);
		$FieldCountTranDay = count($_POST["TransactionDay"]);
		$FieldCountPostDay = count($_POST["PostedDay"]);
		$FieldCountAmount = count($_POST["AmountDisputed"]);
		$FieldCountReceipt = count($_POST["receiptstatus"]);
		$FieldCountDisputeRe = count($_POST["DisputeReason"]);
		$FieldCountDisputeDes = count($_POST["DisputeDescription"]);
		$FieldCountMerchName = count($_POST["MerchantName"]);
		$FieldCountMerchCont = count($_POST["merchantcontactstatus"]);
		$FieldCountMerchContDay = count($_POST["MerchantContactDay"]);
		$FieldCountMerchDes = count($_POST["merchantDescription"]);
		$FieldCountTranDes = count($_POST["transactionDescription"]);
		
		//var_dump($FieldCountTranID, $FieldCountTranDay, $FieldCountPostDay, $FieldCountAmount, $FieldCountReceipt, $FieldCountDisputeRe, $FieldCountDisputeDes, $FieldCountMerchName, $FieldCountMerchCont, $FieldCountMerchContDay, $FieldCountMerchDes, $FieldCountTranDes);
		
		//If all the values dont equal each other
		if(!(($FieldCountTranID == $FieldCountTranDay) && ($FieldCountTranDay == $FieldCountPostDay) && ($FieldCountPostDay == $FieldCountAmount) && ($FieldCountAmount == $FieldCountReceipt) && ($FieldCountReceipt == $FieldCountDisputeRe) && ($FieldCountDisputeRe == $FieldCountDisputeDes) && ($FieldCountDisputeDes == $FieldCountMerchName) && ($FieldCountMerchName == $FieldCountMerchCont) && ($FieldCountMerchCont == $FieldCountMerchContDay) && ($FieldCountMerchContDay == $FieldCountMerchDes) && ($FieldCountMerchDes == $FieldCountTranDes))){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the transaction field values! Please try again or contact the system admin.';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		
		$TranIDArray = $_POST["transactionID"];
		$TranDayArray = $_POST["TransactionDay"];
		$PostDayArray = $_POST["PostedDay"];
		$AmountArray = $_POST["AmountDisputed"];
		$ReceiptArray = $_POST["receiptstatus"];
		$DisputeReArray = $_POST["DisputeReason"];
		$DisputeDesArray = $_POST["DisputeDescription"];
		$MerchNameArray = $_POST["MerchantName"];
		$MerchContArray = $_POST["merchantcontactstatus"];
		$ContDayArray = $_POST["MerchantContactDay"];
		$MerchDesArray = $_POST["merchantDescription"];
		$TranDesArray = $_POST["transactionDescription"];
		
		$DONETranIDArray = array();				//array_push($DONETranIDArray, $TempTransactionID);
		$DONETranDayArray = array();			//array_push($DONETranDayArray, $TransactionDayVar);
		$DONEPostDayArray = array();			//array_push($DONEPostDayArray, $PostedDayVar);
		$DONEAmountArray = array();				//array_push($DONEAmountArray, $AmountdisputedVar);
		$DONEReceiptArray = array();			//array_push($DONEReceiptArray, $RecieptStatusVar);
		$DONEDisputeReArray = array();			//array_push($DONEDisputeReArray, $DisputeReasonVar);
		$DONEDisputeDesArray = array();			//array_push($DONEDisputeDesArray, $DisputeDescriptionVar);
		$DONEMerchNameArray = array();			//array_push($DONEMerchNameArray, $MerchantNameVAR);
		$DONEMerchContArray = array();			//array_push($DONEMerchContArray, $MerchantContactVar);
		$DONEContDayArray = array();			//array_push($DONEContDayArray, $MerchantContactDayVar);
		$DONEMerchDesArray = array();			//array_push($DONEMerchDesArray, $MerchantDescriptionVar);
		$DONETranDesArray = array();			//array_push($DONETranDesArray, $TransactionCommentsVar);
		
		
		
		for($i=0;$i<$FieldCountTranID;$i++){
			
			if(!isset($_POST["transactionID"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Transaction ID! Please contact the System Admin';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$TempTransactionID = $TranIDArray[$i];
				
				if (!is_numeric($TempTransactionID)){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Transaction ID Value! Please contact the System Admin';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				array_push($DONETranIDArray, $TempTransactionID);
			}
			
			if(!isset($_POST["TransactionDay"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Transaction Day Error! Please Enter a valid Day the transaction took place in the format YYYY-MM-DD.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$TransactionDayVar = $TranDayArray[$i];
				$dateGood = validateDate($TransactionDayVar, 'Y-m-d');
				$dateGoodVAR = validatePastDate($TransactionDayVar);
				
				if ($dateGood != true || $dateGoodVAR != true){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Transaction Day Error! Please Enter a valid Day the transaction took place in the format YYYY-MM-DD. You entered: '.$TransactionDayVar;
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				array_push($DONETranDayArray, $TransactionDayVar);
			}
			
			if(!isset($_POST["PostedDay"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Day the transaction posted in the format YYYY-MM-DD.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$PostedDayVar = $PostDayArray[$i];
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
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				elseif($dateGoodVAR != true){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Past Day the transaction posted in the format YYYY-MM-DD. You entered: '.$PostedDayVar;
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				elseif($dateGoodVARTWO != true){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Past Posted Date the transaction posted in the format YYYY-MM-DD. You entered: '.$PostedDayVar;
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				else{
					//Pass
				}
				
				//if ((($dateGood != true) || ($dateGoodVAR != true) || ($dateGoodVARTWO != true)) || ($PostedDayVar != "")){
				//	$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Day the transaction posted in the format YYYY-MM-DD. You entered: '.$PostedDayVar;
				//	header( "Location: newDisputeConfirmation.php" );
				//	exit();
				//}
				
				array_push($DONEPostDayArray, $PostedDayVar);
			}
			
			if(!isset($_POST["AmountDisputed"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Amount Disputed Error! Please enter a valid dollar amount.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$AmountdisputedVar = $AmountArray[$i];
				
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
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				array_push($DONEAmountArray, $AmountdisputedVar);
			}
			
			if(!isset($_POST["receiptstatus"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Receipt Status Error! Please select "yes" or "no".';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$RecieptStatusVar = $ReceiptArray[$i];
				
				if ($RecieptStatusVar != "yes" && $RecieptStatusVar != "no"){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Customer has the receipt! Please contact the System Admin for more help.';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				if($RecieptStatusVar == "yes"){
					$RecieptStatusVar = "1";
				}
				else{
					$RecieptStatusVar = "0";
				}
				
				array_push($DONEReceiptArray, $RecieptStatusVar);
			}
			
			if(!isset($_POST["MerchantName"])){		//Maybe not required??
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Name Error! Please input a merchant name.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$MerchantNameVAR = $MerchNameArray[$i];
				$stringGood = isAscii($MerchantNameVAR);
				
				if (!$stringGood){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Merchant Name were Detected! Please try again using regular characters!';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				if(!preg_match("/^[a-zA-Z0-9\'\.\-\s\:\;\?\/\,\]\[\}\{\!\@\#\$\%\^\*\(\)\_\=\+]+$/", $MerchantNameVAR)){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Merchant Name were Detected! Please try again using regular characters!';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				//print $MerchantNameVAR;
				$MerchantNameVAR = $dtcon->real_escape_string($MerchantNameVAR);
				//print $MerchantNameVAR;
				//exit();
				
				array_push($DONEMerchNameArray, $MerchantNameVAR);
			}
			
			if(!isset($_POST["merchantcontactstatus"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Contact Status Error! Please Select a Contact Status.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$MerchantContactVar = $MerchContArray[$i];
				
				if ($MerchantContactVar != "yes" && $MerchantContactVar != "no"){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Merchant has been Contacted!';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				if($MerchantContactVar == "yes"){
					$MerchantContactVar = "1";
				}
				else{
					$MerchantContactVar = "0";
				}
				
				array_push($DONEMerchContArray, $MerchantContactVar);
			}
			
			if(!isset($_POST["MerchantContactDay"])){
				$MerchantContactDayVar = "";
			}
			else{
				
				if($MerchantContactVar == "0"){
					$MerchantContactDayVar = "";
				}
				else{
					$MerchantContactDayVar = $ContDayArray[$i];
					$dateGood = validateDate($MerchantContactDayVar, 'Y-m-d');
					$dateGoodVAR = validatePastDate($MerchantContactDayVar, 'Y-m-d');
					$dateGoodVARTWO = validatePostedDate($TransactionDayVar, $MerchantContactDayVar);
					
					if ($dateGood != true || $dateGoodVAR != true || $dateGoodVARTWO != true){
						$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Contact Day Error! Please Enter a valid Day the merchant was contacted in the format YYYY-MM-DD. You entered: '.$MerchantContactDayVar;
						header( "Location: newDisputeConfirmation.php" );
						exit();
					}	
				}
				
				array_push($DONEContDayArray, $MerchantContactDayVar);
			}
			
			if(!isset($_POST["DisputeReason"])){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Dispute Reason Error! Please Select a Reason.';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			else{
				$DisputeReasonVar = $DisputeReArray[$i];
				
				if (!is_numeric($DisputeReasonVar)){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Dispute Reason Selection Value! Please contact the System Admin';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}

				$DisputeReasonINT = intval($DisputeReasonVar);
				
				if($DisputeReasonINT < 1 || $DisputeReasonINT > $NumberofReason){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Dispute Reason Selection! Please contact the System Admin';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				array_push($DONEDisputeReArray, $DisputeReasonVar);
			}
			
			if(!isset($_POST["DisputeDescription"])){
				$DisputeDescriptionVar = "";
			}
			else{
				$DisputeDescriptionVar = $DisputeDesArray[$i];
				
				$stringGood = isAscii($DisputeDescriptionVar);

				if (!$stringGood){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Dispute Description were Detected! Please try again using regular characters!';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				$DisputeDescriptionVar = $dtcon->real_escape_string($DisputeDescriptionVar);
				
				array_push($DONEDisputeDesArray, $DisputeDescriptionVar);
			}
			
			if(!isset($_POST["merchantDescription"])){
				$MerchantDescriptionVar = "";
			}
			else{
				$MerchantDescriptionVar = $MerchDesArray[$i];
				
				$stringGood = isAscii($MerchantDescriptionVar);
				
				if (!$stringGood){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Merchant Description were Detected! Please try again using regular characters!';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				$MerchantDescriptionVar = $dtcon->real_escape_string($MerchantDescriptionVar);
				
				array_push($DONEMerchDesArray, $MerchantDescriptionVar);
			}
			
			if(!isset($_POST["transactionDescription"])){
				$TransactionCommentsVar = "";
			}
			else{
				$TransactionCommentsVar = $TranDesArray[$i];
				
				$stringGood = isAscii($TransactionCommentsVar);
				
				if (!$stringGood){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Transaction Comments were Detected! Please try again using regular characters!';
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
				
				$TransactionCommentsVar = $dtcon->real_escape_string($TransactionCommentsVar);
				
				array_push($DONETranDesArray, $TransactionCommentsVar);
			}
			
		}
		
		
		/*
		var_dump($_POST);
		print "<br>";
		var_dump($_SESSION);
		print "<br>";
		var_dump($_FILES);
		print "<br>";
		var_dump($caseID);
		print "<br>";
		
		var_dump($firstnameVAR);
		print "<br>";
		var_dump($lastnameVAR);
		print "<br>";
		var_dump($phoneNumVAR);
		print "<br>";
		var_dump($emailAddrVAR);
		print "<br>";
		var_dump($AddressVar);
		print "<br>";
		var_dump($Address2VAR);
		print "<br>";
		var_dump($CityAddrVar);
		print "<br>";
		var_dump($StateAddrVar);
		print "<br>";
		var_dump($ZipCodeVar);
		print "<br>";
		var_dump($custinpersonphoneVAR);
		print "<br>";
		var_dump($accountNumberVAR);
		print "<br>";
		var_dump($accountTypeVAR);
		print "<br>";
		var_dump($consumerorBusinessVAR);
		print "<br>";
		var_dump($accountcommentsVAR);
		print "<br>";
		var_dump($NewAccountBool);
		print "<br>";
		var_dump($CARDNumberVAR);
		print "<br>";
		var_dump($ChipCardVAR);
		print "<br>";
		var_dump($CARDTypeVAR);
		print "<br>";
		var_dump($CARDstatusVAR);
		print "<br>";
		var_dump($CARDpossessionVAR);
		print "<br>";
		var_dump($CARDmissingdateVAR);
		print "<br>";
		var_dump($CARDnumbercommentsVAR);
		print "<br>";
		var_dump($NEWAttachmentDescription);
		print "<br>";
		
		var_dump($DONETranIDArray);				//array_push($DONETranIDArray, $TempTransactionID);
		print "<br>";
		var_dump($DONETranDayArray);			//array_push($DONETranDayArray, $TransactionDayVar);
		print "<br>";
		var_dump($DONEPostDayArray);			//array_push($DONEPostDayArray, $PostedDayVar);
		print "<br>";
		var_dump($DONEAmountArray);				//array_push($DONEAmountArray, $AmountdisputedVar);
		print "<br>";
		var_dump($DONEReceiptArray);			//array_push($DONEReceiptArray, $RecieptStatusVar);
		print "<br>";
		var_dump($DONEDisputeReArray);			//array_push($DONEDisputeReArray, $DisputeReasonVar);
		print "<br>";
		var_dump($DONEDisputeDesArray);			//array_push($DONEDisputeDesArray, $DisputeDescriptionVar);
		print "<br>";
		var_dump($DONEMerchNameArray);			//array_push($DONEMerchNameArray, $MerchantNameVAR);
		print "<br>";
		var_dump($DONEMerchContArray);			//array_push($DONEMerchContArray, $MerchantContactVar);
		print "<br>";
		var_dump($DONEContDayArray);			//array_push($DONEContDayArray, $MerchantContactDayVar);
		print "<br>";
		var_dump($DONEMerchDesArray);			//array_push($DONEMerchDesArray, $MerchantDescriptionVar);
		print "<br>";
		var_dump($DONETranDesArray);			//array_push($DONETranDesArray, $TransactionCommentsVar);
		print "<br>";
		var_dump($DONEAttachmentID);			
		print "<br>";
		var_dump($DONEAttachmentComments);			
		print "<br>";
		exit();
		*/
		
		
		$AttachmentsArray = array();
		$FilesExist = FALSE;
		$FilesBOOL = TRUE;

		foreach($_FILES['Attachments']['error'] as $LoopVal){
			$FilesExist = TRUE;
			
			if($LoopVal != 0){			//If any File has an error, Dont go in that If statement
				$FilesBOOL = FALSE;
			}
		}

		if (isset($_FILES['Attachments']) && $FilesBOOL && $FilesExist){
			
			$newAttachName = "";
			$attach_query = "SELECT filename FROM checkattachments WHERE caseid='".$caseID."'";
			$attach_query_data = $dtcon->query($attach_query);
			$attach_data = $attach_query_data->fetch_all();
			$attachnumbers = count($attach_data);
			
			$checkUploadAddress = "FileFolder/UserAttachedDocuments/";
			
			$errors = array();
			
			foreach($_FILES['Attachments']['tmp_name'] as $key => $tmp_name ){
				
				$tmpArray = array();
				
				$file_name = $key.$_FILES['Attachments']['name'][$key];
				$file_size = $_FILES['Attachments']['size'][$key];
				$file_tmp  = $_FILES['Attachments']['tmp_name'][$key];
				$file_type = $_FILES['Attachments']['type'][$key]; 
				$file_errors = $_FILES['Attachments']['error'][$key]; 
				
				// 15000000 bytes = ~14 MB
				if($file_size > 15000000){
					$errors[]='File size must be less than 14 MB';
				}
				
				if(empty($errors)==true){
					
					$attachnumbers++;
					$pathparts = explode('.', $file_name);
					$extensionVar = end((array_values($pathparts)));
					$newName = $checkUploadAddress . "case" . $caseID . "_attachment" . $attachnumbers . "." . $extensionVar;
					
					if(rename($file_tmp, $newName)){
						
						$tmpName = "case" . $caseID . "_attachment" . $attachnumbers . "." . $extensionVar;
						array_push($tmpArray, $tmpName);
						array_push($tmpArray, $newName);
						array_push($AttachmentsArray, $tmpArray);
						//It works.
					}
					else{
						$_SESSION["ADD_DISPUTE_ERROR"] = 'File Move Failed! Please Contact the System Admin.';
						header( "Location: newDisputeConfirmation.php" );
						exit();
					}
					
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'An Error occured with the file upload! Please Contact the System Admin.'.$errors;
					header( "Location: newDisputeConfirmation.php" );
					exit();
				}
			}
		}

		//var_dump($AttachmentsArray);
		//print "<br>";
		//var_dump($_FILES);
		//exit();
		
		$NewChangeInsertstring = "";

		$newAtatchNum = count($AttachmentsArray);

		for($i=0; $i < $newAtatchNum; $i++){
			
			$checkAttachmentInsert = "INSERT INTO checkattachments(id, caseid, filename, filelocation, comments, iddeleted) VALUES (NULL, '".$caseID."', '".$AttachmentsArray[$i][0]."', '".$AttachmentsArray[$i][1]."', '".$NEWAttachmentDescription."', FALSE)";
			
			if ($dtcon->query($checkAttachmentInsert) === TRUE) {
				
				//print "Insert Worked";
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with an Attachment Insert! Please Contact the System Admin!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$NewChangeInsertstring = $NewChangeInsertstring . "\n\n" . $checkAttachmentInsert;
		}
		
		
		$caseFix = "UPDATE checkcases SET custfname='".$firstnameVAR."', custlname='".$lastnameVAR."', custphone='".$phoneNumVAR."', custemail='".$emailAddrVAR."', custaddressone='".$AddressVar."', custaddresstwo='".$Address2VAR."', custcityaddr='".$CityAddrVar."', custstateaddr='".$StateAddrVar."', custzipaddr='".$ZipCodeVar."', customerstartmethod='".$custinpersonphoneVAR."' WHERE id='".$caseID."'";
		
		$accountFix = "UPDATE checkaccountnumbers SET accountnumber='".$accountNumberVAR."', accounttype='".$accountTypeVAR."', businessaccount='".$consumerorBusinessVAR."', comments='".$accountcommentsVAR."', accoountnew='".$NewAccountBool."' WHERE caseid='".$caseID."'";
		
		$cardFix = "UPDATE checkcardnumbers SET cardnumber='".$CARDNumberVAR."', cardtype='".$CARDTypeVAR."', cardstatus='".$CARDstatusVAR."', cardpossession='".$CARDpossessionVAR."', cardmissingdate='".$CARDmissingdateVAR."', chipcard='".$ChipCardVAR."', comments='".$CARDnumbercommentsVAR."' WHERE caseid='".$caseID."'";
		
		/*
		var_dump($caseFix);
		print "<br>";
		print "<br>";
		var_dump($accountFix);
		print "<br>";
		print "<br>";
		var_dump($cardFix);
		exit();
		*/
		
		$NewChangeInsertstring = $NewChangeInsertstring . "\n\n" . $caseFix . "\n\n" . $accountFix . "\n\n" . $cardFix;
		
		if ($dtcon->query($caseFix) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Case Update! Please Contact the System Admin!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		if ($dtcon->query($accountFix) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Account Update! Please Contact the System Admin!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		if ($dtcon->query($cardFix) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Card Update! Please Contact the System Admin!';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		
		
		//Transaction Fixes
		for($i=0;$i<$FieldCountTranID;$i++){
			
			$temptransactionFix = "UPDATE checktransactions SET amount='".$DONEAmountArray[$i]."', transactiondate='".$DONETranDayArray[$i]."', dateposted='".$DONEPostDayArray[$i]."', disputereason='".$DONEDisputeReArray[$i]."', description='".$DONEDisputeDesArray[$i]."', merchantname='".$DONEMerchNameArray[$i]."', merchantcontacted='".$DONEMerchContArray[$i]."', merchantcontacteddate='".$DONEContDayArray[$i]."', merchantcontactdescription='".$DONEMerchDesArray[$i]."', receiptstatus='".$DONEReceiptArray[$i]."', comments='".$DONETranDesArray[$i]."' WHERE id='".$DONETranIDArray[$i]."' AND caseid='".$caseID."'";
			
			if ($dtcon->query($temptransactionFix) === TRUE) {
				
				//print "Insert Worked";
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Transaction Update! Transaction number: '.$DONETranIDArray[$i].' failed. Please Contact the System Admin!';
				header( "Location: newDisputeConfirmation.php" );
				exit();
			}
			
			$NewChangeInsertstring = $NewChangeInsertstring . "\n\n" . $temptransactionFix;
		}
		
		//Attachment Fixes
		for($i=0;$i<$AttachmentIDFieldCount;$i++){
			
			$tempAttachmentFix = "UPDATE checkattachments SET comments='".$DONEAttachmentComments[$i]."' WHERE id='".$DONEAttachmentID[$i]."' and caseid='".$caseID."'";
			
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
		
		$NewChangeInsertstring = $dtcon->real_escape_string($NewChangeInsertstring);
		
		$tempNewVar = "";				//Unknown if Temp var needed for Substr later in the check keeping just in case
		
		if (strlen($NewChangeInsertstring)<16000) {
			//echo "less than 140";
			//Do nothing
		}
		else{
			if (strlen($NewChangeInsertstring)>16000) {
				//echo "more than 140";
				//Cut down String to 16000 
				//We can't limit the disputes, and the column is limited to 16384 characters. So we have to truncate the string and lose some of the inset values. This shouldn't happen for around 10 transactions, but is all dependant on Comments/text lengths.
				$tempNewVar = substr($NewChangeInsertstring,0,15000);
				
				$NewChangeInsertstring = $tempNewVar;
			}
			else {
				//echo "exactly 140";
				//Do nothing
			}
		}
		
		//substr($NewChangeInsertstring, 4);
		
		$NewChangeLogInsert = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '2', '".$caseID."', '".substr($NewChangeInsertstring, 4)."')";
		
		//var_dump($NewChangeInsertstring);
		//exit();
		
		if ($dtcon->query($NewChangeLogInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin! <p hidden>'.$NewChangeLogInsert.'</p>';
			header( "Location: newDisputeConfirmation.php" );
			exit();
		}
		
		
		
		$_SESSION["TransactionAdded"] = 'Dispute added Successfully!';
		
		$_SESSION["FinishDispute"] = "True";
		header( "Location: newDisputeFinal.php" ); 
		exit();
	}
	else if(isset($_POST["deleteAttach"])){
		
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
		
	}
	else if(isset($_SESSION["DisputeDelete"])){
		
		date_default_timezone_set("America/Chicago");
		$timeofChange = date("Y-m-d, G:i:s");
		
		//Currently Working Case Session
		//$_SESSION["WorkingDispute"] = 'True';
		//$_SESSION["Dispute_CaseID"] = $caseID;
		
		$caseID = $_SESSION["Dispute_CaseID"];
		
		$CardCaseQuery = "SELECT * FROM checkcases where id='" . $caseID . "'";
		$CardCaseQuery_Data = $dtcon->query($CardCaseQuery);
		$CardCaseData = $CardCaseQuery_Data->fetch_all();
		
		//var_dump($CardCaseData);
		
		if (!empty($CardCaseData)){
			if (!empty($CardCaseData[0])){
				
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Information for the Case ID Was not found! Please Contact the System Admin.';
				header( "Location: newDisputeTransactions.php" );
				exit();
			}
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Information for the Case ID Was not found! Please Contact the System Admin.';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		
		
		$caseDeleteUpdate = "UPDATE checkcases SET casedeleted = TRUE WHERE id='".$caseID."'";
		$TransactionDeleteUpdate = "UPDATE checktransactions SET transactiondeleted = TRUE WHERE caseid='".$caseID."'"; //This line can hit multiple SQL table rows.
		
		
		$caseDeleteUpdateVAR = "UPDATE checkcases SET casedeleted = TRUE WHERE id=".$caseID."";
		$TransactionDeleteUpdateVAR = "UPDATE checktransactions SET transactiondeleted = TRUE WHERE caseid=".$caseID."";
		
		
		$userChangeLogQuery = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', 3, '".$caseID."', 'User deleted case number: ".$caseID." --- Commands: ".$caseDeleteUpdateVAR." -- ".$TransactionDeleteUpdateVAR."')";
		
		
		if ($dtcon->query($caseDeleteUpdate) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with Deleting the Case Data! Please Contact the System Admin!';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		if ($dtcon->query($TransactionDeleteUpdate) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with Deleting the Transaction Data for the Case! Please Contact the System Admin!';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		
		
		if ($dtcon->query($userChangeLogQuery) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}	
		
		
		unset($_SESSION["WorkingDispute"]);
		unset($_SESSION["Dispute_CaseID"]);
		header( "Location: index.php" );
		exit();
		
	}
	else if(isset($_SESSION["FinishDisputeOne"])){
		//Default Value, when user gets redirected here with error message or from transaction page.
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] .= 'Something went wrong with the Server Settings.';
		header( "Location: index.php" );
		exit();
	}
	
	
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] .= 'Something went wrong with the Server Post.';
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
                    <div class="col-md-4">
						<h2>Confirm New Dispute</h2> 
                    </div>
					
					<div class="col-md-8">
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
				
				<hr />
				
				<div class="row">
					<div class="col-md-2">
						<h4>Case ID: <?php print $caseID; ?></h4>
					</div>
					<div class="col-md-2">
						<label>Add more transactions:</label>&nbsp;<a target="_blank" title="Click this button to add more transactions. You won't be able to edit transactions using this button."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<a href="newDisputeTransactions.php" class="btn btn-success form-control">Add more Transactions</a>
					</div>
				</div>
				
				<hr />
				
<?php



//$caseID
//Check Case ID
if($caseID == 0 || $caseID == NULL){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select a case to edit from View Disputes.';
	echo '<script> window.location = "newDisputeConfirmation.php";</script>';
	//header( "Location: newDisputeConfirmation.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}


//SQL Info--------------------------------------------------------------------------------------------------------

//Get Case Infomation
$CaseQuery = "SELECT * FROM checkcases where id='" . $caseID . "'";
$CaseQuery_Data = $dtcon->query($CaseQuery);
$CaseData = $CaseQuery_Data->fetch_all();

//var_dump($CaseData);

if (!empty($CaseData)){
	if (!empty($CaseData[0])){
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Information for the Case ID, '.$caseID.', Was not found! Please Contact the System Admin.';
		echo '<script> window.location = "index.php";</script>';
		//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
		exit();
	}
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Information for the Case ID, '.$caseID.', Was not found! Please Contact the System Admin.';
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

$AccountNumQuery = "SELECT * FROM checkaccountnumbers where caseid='" . $caseID . "'";
$AccountNumQuery_Data = $dtcon->query($AccountNumQuery);
$AccountNumData = $AccountNumQuery_Data->fetch_all();

//var_dump($AccountNumData);

if (!empty($AccountNumData)){
	if (!empty($AccountNumData[0])){
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID, '.$caseID.', Was not found! Please Contact the System Admin.';
		echo '<script> window.location = "index.php";</script>';
		//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
		exit();
	}
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID, '.$caseID.', Was not found! Please Contact the System Admin.';
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

$CardNumQuery = "SELECT * FROM checkcardnumbers where caseid='" . $caseID . "'";
$CardNumQuery_Data = $dtcon->query($CardNumQuery);
$CardNumData = $CardNumQuery_Data->fetch_all();

//var_dump($CardNumData);

if (!empty($CardNumData)){
	if (!empty($CardNumData[0])){
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number for the Case ID, '.$caseID.', Was not found! Please Contact the System Admin.';
		echo '<script> window.location = "index.php";</script>';
		//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
		exit();
	}
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number for the Case ID, '.$caseID.', Was not found! Please Contact the System Admin.';
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}


//Get Transaction Information
$TransactionQuery = "SELECT * FROM checktransactions where transactiondeleted=FALSE AND caseid='" . $caseID . "'";
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
					<div class="col-md-12">
						<h5>Case Information</h5>
						<div class="panel-group" id="CaseAccordion">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#CaseAccordion" href="#collapse_Case" class="collapsed">Case Information</a>
									</h4>
								</div>
								<div id="collapse_Case" class="panel-collapse collapse" style="height: 0px;">
									<div class="panel-body">
										
										<form id="ConfirmDispute_ID" autocomplete="off" enctype="multipart/form-data" name="ConfirmDispute" method="post" action="newDisputeConfirmation.php" onsubmit="showLoading();">
	
											<input type='hidden' name='caseID' value='<?php print $caseID; ?>'>
											
											<div class="row">
												<div class="col-md-3">
													<label>Dispute Case Start Day:</label>
													<input type="text" class="form-control2" name="DisputeCaseDay" maxlength="255" value="<?php print $CaseData[0][1]; ?>" readonly>
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
												
												<div class="col-md-4">
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
												
											</div><hr />
											
											<!-- Account Number Info -->
											
											<div class="row">
												<div class="col-md-2">
													<label>Account Number</label>&nbsp;<a target="_blank" title="Please enter the customers account number. This field accepts digits only. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
													<input type="text" class="form-control" name="AccountNumber" maxlength="12" value="<?php print $AccountNumData[0][2]; ?>" id="accountnum" pattern="^[0-9]+$" title="'1234', '123456789012'" required>
												</div>
												<div class="col-md-3">
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
												<div class="col-md-5">
													<label>Account Comments</label>&nbsp;<a target="_blank" title="This field is for comments about the account(s). This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
													<textarea class="form-control" onblur="this.value = this.value.toUpperCase();" name="AccountComments" rows="4" maxlength="4096"><?php print $AccountNumData[0][5]; ?></textarea>
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
												<div class="col-md-2" id="possDate" style="display:none">
													<label>Card Missing Date</label>&nbsp;<a target="_blank" title="If the card possession is lost or stolen, please select approximately when the card went missing. This field is only required when the card possession is lost or stolen."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
													<input type="text" class="form-control" id="possessionDateID" name="possessionDate" onFocus="showCalendarControl(this);" value="<?php print $CardNumData[0][6]; ?>">
												</div>
											</div><hr />
												
											<div class="row">
												<div class="col-md-6">
													<label>Card Comments</label>&nbsp;<a target="_blank" title="This field is for comments about the card(s). This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
													<textarea class="form-control" onblur="this.value = this.value.toUpperCase();" name="CardComments" rows="3" onfocus="this.select();" maxlength="4096"><?php print $CardNumData[0][8]; ?></textarea>
												</div>
											</div><hr />
											

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<hr />
				
				
				
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
								
								<input type='hidden' name='transactionID[]' value='<?php print $TransactionData[$m][0]; ?>'>
							
								<div class="col-md-2">
									<label>Transaction Date</label>&nbsp;<a target="_blank" title="Please select when the transaction occurred using the calendar tool. The format must be: YYYY-MM-DD. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
									<input type="text" class="form-control" name="TransactionDay[]" value="<?php print $TransactionData[$m][4]; ?>" onFocus="showCalendarControl(this);"  id="transactiondate" pattern="^[0-9-]+$" title="'YYYY-MM-DD'" required>
									
								</div>
								<div class="col-md-2">
									<label>Date Posted to Account</label>&nbsp;<a target="_blank" title="Please select when the transaction posted to the account using the calendar tool. The format must be: YYYY-MM-DD. This field can be blank."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
									<input type="text" class="form-control" name="PostedDay[]" value="<?php print $TransactionData[$m][5]; ?>" onFocus="showCalendarControl(this);" id="posteddate" pattern="^[0-9-]+$" title="'YYYY-MM-DD'">
									
								</div>
								<div class="col-md-2">
									<label>Amount Disputed</label>&nbsp;<a target="_blank" title="Please enter the transaction amount. Please do not include the dollar sign. This field accepts digits and a single period. Ex.: '45', '45.86'. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
									<input type="text" class="form-control" style="background: url(./assets/img/dollar-sign.png) no-repeat 0px 0px; background-size: 30px; padding-left:22px;" name="AmountDisputed[]" maxlength="11" value="<?php print $TransactionData[$m][3]; ?>" id="amountDisputed" pattern="^\d+(?:\.\d{0,2})?$" title="'123.45', '123', '1234567.00'" required>
								</div>
								
								<div class="col-md-4">
									<label>Does the customer have a receipt for the transaction?</label>&nbsp;<a target="_blank" title="Please select if the customer has the receipt for the transaction. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
									<select id="receiptstatusID" name="receiptstatus[]" class="form-control"> 
<?php

if($TransactionData[$m][12] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no">No</option>';
}
else if($TransactionData[$m][12] == 0){
	print '<option value="yes">Yes</option>';
	print '<option value="no" selected="true">No</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Customer Receipt Error! Please contact the system Admin!";
	echo '<script> window.location = "newDisputeConfirmation.php";</script>';
	//header( "Location: newDisputeConfirmation.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

?>
									</select> 
								</div>
								
								
							</div><hr />
							
							<div class="row">
								<div class="col-md-4">
									<label>Dispute Reason:</label>&nbsp;<a target="_blank" title="Please select the customers dispute reason for this transaction. Please see the notice to CSR for any additional information needed. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
									<select name="DisputeReason[]" class="form-control">
										<option value="select">Select One</option>
<?php

for ($i=0; $i < $NumberofReason; $i++){
	
	if($reason_data[$i][0] == $TransactionData[$m][6]){
		print '<option value="' . $reason_data[$i][0] . '" selected="true">' . $reason_data[$i][1] . '</option>';
	}
	else{
		print '<option value="' . $reason_data[$i][0] . '">' . $reason_data[$i][1] . '</option>';
	}
}

?>
									</select>
								</div>
								<div class="col-md-8">
									<label>Dispute Description</label>&nbsp;<a target="_blank" title="This field is for comments about the account(s). This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
									<textarea type="text" class="form-control" onblur="this.value = this.value.toUpperCase();" name="DisputeDescription[]" rows="2" maxlength="4096"><?php print $TransactionData[$m][7]; ?></textarea>
								</div>
							</div><hr />
							
							<div class="row">
								<div class="col-md-3">
									<label>Merchant Name:</label>&nbsp;<a target="_blank" title="This field is for merchant names. You may use alphabetic, numeric and some special characters. Ex.: 'Forever 21', 'Walmart', 'Trader Joe's'. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
									<input type="text" class="form-control" onblur="this.value = this.value.toUpperCase();" name="MerchantName[]" maxlength="255" value="<?php print $TransactionData[$m][8]; ?>" id="merchname" pattern="^[\x00-\x7F]*$" title="Letters, digits, periods, and spaces only." required>
								</div>
								<div class="col-md-3">
									<label>Has the Merchant been contacted?</label>&nbsp;<a target="_blank" title="Please select whether the merchant has been contacted by the customer or you. If they have been contacted please select the date of contact in the Contact Date field when it pops up."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
									<select id="marchantcontactstatusID" name="merchantcontactstatus[]" class="form-control" onchange="merchantChange();">
<?php

if($TransactionData[$m][9] == 1){
	print '<option value="yes" selected="true">Yes</option>';
	print '<option value="no">No</option>';
}
else if($TransactionData[$m][9] == 0){
	print '<option value="yes">Yes</option>';
	print '<option value="no" selected="true">No</option>';
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = "Merchant Contact Error! Please contact the system Admin!";
	echo '<script> window.location = "newDisputeConfirmation.php";</script>';
	//header( "Location: newDisputeConfirmation.php" ); //HEADER ONLY WORKS Before HTML Content
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
									<label>Contact Date:</label>&nbsp;<a target="_blank" title="If the merchant has been contacted, please select approximately when they were contacted. This field is required when the merchant has been contacted."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
									<input type="text" class="form-control" id="MerchContactDate" name="MerchantContactDay[]" value="<?php print $TransactionData[$m][10]; ?>" onFocus="showCalendarControl(this);" id="merchcontactdate" pattern="^[0-9-]+$" title="'YYYY-MM-DD'">
								</div>
							</div><hr />
							
							<div class="row">
								<div class="col-md-6">
									<label>Merchant Contact Description:</label>&nbsp;<a target="_blank" title="This field is for description/comments about the merchant. This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
									<textarea class="form-control" onblur="this.value = this.value.toUpperCase();" name="merchantDescription[]" rows="2" maxlength="4096"><?php print $TransactionData[$m][11]; ?></textarea>
								</div>
								<div class="col-md-6">
									<label>Employee Comments:</label>&nbsp;<a target="_blank" title="This field is for Institutional comments. These comments are private and for our use only. This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
									<textarea class="form-control" onblur="this.value = this.value.toUpperCase();" name="transactionDescription[]" rows="2" maxlength="4096"><?php print $TransactionData[$m][21]; ?></textarea>
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
	<hr />
	
				<div class="row" id="AttachmentRowIDDIV">
					<div>
						<input type="hidden" name="MAX_FILE_SIZE" value="15000000" />
					</div>
					<div class='col-md-3' id='AttachmentRow_1'>
						<label>Attachments</label>&nbsp;<a target="_blank" title="This field is for any receipts/documents/pictures the customer may provide about the dispute/transaction. Please scan in any documents, and attach them to this page. This field is optional."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<input type='file' id='Attachments_1_ID' name='Attachments[]' multiple value="Browse"></input>
						<input type="button" onclick="location.reload(true);" value="Reset Attachment Selection">
					</div>
					<div class='col-md-3' id='AttachmentRowComments_1'>
						<label>Attachment Comments</label>&nbsp;<a target="_blank" title="This field is for comments about any attachments. These comments are not saved if you do not attach any documents. This field is optional."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<textarea class='form-control' onblur="this.value = this.value.toUpperCase();" name='AttachmentDescription' rows='2' maxlength='4096'></textarea>
					</div>	
					
				</div><hr />
					
				<div class="row">
					<div class="col-md-12">
						<label>Attached Documents:</label>
<?php

$AllAttachmentQuery = "SELECT filename, comments, id FROM checkattachments WHERE caseid='" . $caseID . "' AND iddeleted=FALSE";
$AllAttachmentQuery_Data = $dtcon->query($AllAttachmentQuery);
$AllAttachmentData = $AllAttachmentQuery_Data->fetch_all();

//var_dump($AllAttachmentQuery, $AllAttachmentData);

print "<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>";
print "<thead>";
print "<tr>";
print "<th style='width:20%;'>Attachment Name</th>";
print "<th style='width:60%;'>Attachment Comment</th>";
print '<th style="width:20%;">Delete Attachment&nbsp;<a target="_blank" title="If you delete an attachment by accident, please contact IT to get it restored."><img src="./assets/img/help-button-icon.png" height="15px"/></a></th>';
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
				
										
						<div class="row">	
							<div class="col-md-2">
								<label>Change Values</label>&nbsp;<a target="_blank" title="This button will submit changes and bring you back to this same page to confirm values. To confirm values and advance to the final page please click the 'Confirm Case Information' button."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="submit" class="btn btn-primary" name="changeValues" value="Submit new Changes">
							</div>

							
							<div class="col-md-2">
								<label>Submit Case</label>&nbsp;<a target="_blank" title="This button confirms any values on the page and then advances to the final page."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="submit" class="btn btn-success" name="confirmValues" value="Confirm Case Information" onclick="return confirm('Please click okay to confirm all values.')">
							</div>
	
							
							<!--  Delete Case -->
							<div class="col-md-2">
								<label>Delete Case</label>&nbsp;<a target="_blank" title="This button is to delete the case information and any transactions that have been added."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="submit" class="btn btn-danger"  name="DisputeDelete" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Delete Case&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" onclick="return confirm('Click OK to confirm deletion.')">
							</div>
						</div>
							
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

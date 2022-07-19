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
$cardNumber = 0;
$accountNumber = 0;
$cardID_Variable = 0;

$ipaddress = $_SERVER['REMOTE_ADDR'];

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
	header( "Location: index.php" );
	exit();
}
if($CardTypeResult = $dtcon->query("SELECT typetext FROM cardtype WHERE iddeleted='0'")){
	$CardTypeCOUNT = $CardTypeResult->num_rows;
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Type Count Query! Please contact the System Admin';
	header( "Location: index.php" );
	exit();
}
if($CardStatusResult = $dtcon->query("SELECT statustext FROM cardstatus WHERE iddeleted='0'")){
	$CardStatusCOUNT = $CardStatusResult->num_rows;
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Status Count Query! Please contact the System Admin';
	header( "Location: index.php" );
	exit();
}
if($CardPossResult = $dtcon->query("SELECT possessiontext FROM cardpossession WHERE iddeleted='0'")){
	$CardPossessionCOUNT = $CardPossResult->num_rows;
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Possession Count Query! Please contact the System Admin';
	header( "Location: index.php" );
	exit();
}
if($DisputeReasonResult = $dtcon->query("SELECT reason FROM disputereasons WHERE iddeleted='0'")){
	$DisputeReasonCOUNT = $DisputeReasonResult->num_rows;
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Dispute Reason Count Query! Please contact the System Admin';
	header( "Location: index.php" );
	exit();
}
if($ComproSelResult = $dtcon->query("SELECT merchantid FROM compromise WHERE iddeleted='0'")){
	$CompromiseSelectCOUNT = $ComproSelResult->num_rows;
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Compromise Selection Count Query! Please contact the System Admin';
	header( "Location: index.php" );
	exit();
}


if($AccountTypeCOUNT == 0 || $CardTypeCOUNT == 0 || $CardStatusCOUNT == 0 || $CardPossessionCOUNT == 0 || $DisputeReasonCOUNT == 0 || $CompromiseSelectCOUNT == 0){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Count Queries! Please contact the System Admin';
	header( "Location: index.php" );
	exit();
}


// Transaction and Next Transaction Code:
function addTransaction($caseID, $cardID_Variable, $DisputeINT, $func_DTCON, $func_username, $func_ipaddr){
	
	
	//var_dump($_POST);
	//var_dump($_FILES);
	//var_dump($_SESSION);
	/*
	var_dump($AccountTypeCOUNT, $CardTypeCOUNT, $CardStatusCOUNT, $CardPossessionCOUNT, $DisputeReasonCOUNT, $CompromiseSelectCOUNT);
	
	print $AccountTypeCOUNT."<br>";
	print $CardTypeCOUNT."<br>";
	print $CardStatusCOUNT."<br>";
	print $CardPossessionCOUNT."<br>";
	print $DisputeReasonCOUNT."<br>";
	print $CompromiseSelectCOUNT."<br>";
	
	
	$AccountINT = $AccountTypeCOUNT;
	$CardTypeINT = $AccountTypeCOUNT;
	$CardStatusINT = $AccountTypeCOUNT;
	$CardPossINT = $AccountTypeCOUNT;
	$DisputeINT = $AccountTypeCOUNT;
	$CompromiseINT = $AccountTypeCOUNT;
	*/

	//print $DisputeINT."<br>";
	//exit();
	
	
	date_default_timezone_set("America/Chicago");
	$timeofChange = date("Y-m-d, G:i:s");
	
	//Currently Working Case Session
	//$_SESSION["WorkingDispute"] = 'True';
	//$_SESSION["Dispute_CaseID"] = $caseID;

	if(!isset($_POST["DisputeDay"])){
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Transaction Day Error! Please Enter a valid Day the transaction took place in the format YYYY-MM-DD.';
		header( "Location: newDisputeTransactions.php" );
		exit();
	}
	else{
		$TransactionDayVar = $_POST["DisputeDay"];
		$dateGood = validateDate($TransactionDayVar, 'Y-m-d');
		$dateGoodVAR = validatePastDate($TransactionDayVar);
		
		if ($dateGood != true || $dateGoodVAR != true){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Transaction Day Error! Please Enter a valid Day the transaction took place in the format YYYY-MM-DD. You entered: '.$TransactionDayVar;
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
	}
	
	if(!isset($_POST["PostedDay"])){
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Day the transaction posted in the format YYYY-MM-DD.';
		header( "Location: newDisputeTransactions.php" );
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
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		elseif($dateGoodVAR != true){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Past Day the transaction posted in the format YYYY-MM-DD. You entered: '.$PostedDayVar;
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		elseif($dateGoodVARTWO != true){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Past Posted Date the transaction posted in the format YYYY-MM-DD. You entered: '.$PostedDayVar;
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		else{
			//Pass
		}
		
		//if ((($dateGood != true) || ($dateGoodVAR != true) || ($dateGoodVARTWO != true)) || ($PostedDayVar != "")){
		//	$_SESSION["ADD_DISPUTE_ERROR"] = 'Posted Day Error! Please Enter a valid Day the transaction posted in the format YYYY-MM-DD. You entered: '.$PostedDayVar;
		//	header( "Location: newDisputeTransactions.php" );
		//	exit();
		//}
		
		
	}
	
	if(!isset($_POST["AmountDisputed"])){
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Amount Disputed Error! Please enter a valid dollar amount.';
		header( "Location: newDisputeTransactions.php" );
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
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
	}
	
	if(!isset($_POST["receiptstatus"])){
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Receipt Status Error! Please select "yes" or "no".';
		header( "Location: newDisputeTransactions.php" );
		exit();
	}
	else{
		$RecieptStatusVar = $_POST["receiptstatus"];
		
		if ($RecieptStatusVar != "yes" && $RecieptStatusVar != "no"){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Customer has the receipt! Please contact the System Admin for more help.';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		
		if($RecieptStatusVar == "yes"){
			$RecieptStatusVar = "1";
		}
		else{
			$RecieptStatusVar = "0";
		}
	}
	
	if(!isset($_POST["DisputeReason"])){
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Dispute Reason Error! Please Select a Reason.';
		header( "Location: newDisputeTransactions.php" );
		exit();
	}
	else{
		$DisputeReasonVar = $_POST["DisputeReason"];
		
		if (!is_numeric($DisputeReasonVar)){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Dispute Reason Selection! Please contact the System Admin';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}

		$DisputeReasonINT = intval($DisputeReasonVar);
		
		if($DisputeReasonINT < 1 || $DisputeReasonINT > $DisputeINT){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Dispute Reason Selection! Please contact the System Admin';
			header( "Location: newDisputeTransactions.php" );
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
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		
		$DisputeDescriptionVar = $func_DTCON->real_escape_string($DisputeDescriptionVar);
		
	}
	
	if(!isset($_POST["MerchantName"])){
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Name Error! Please input a merchant name.';
		header( "Location: newDisputeTransactions.php" );
		exit();
	}
	else{
		$MerchantNameVAR = $_POST["MerchantName"];
		$stringGood = isAscii($MerchantNameVAR);
		
		if (!$stringGood){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the merchant name were Detected! Please try again using regular characters!';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		
		$MerchantNameVAR = $func_DTCON->real_escape_string($MerchantNameVAR);
		
	}
	
	if(!isset($_POST["merchantcontactstatus"])){
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Contact Status Error! Please Select a Contact Status.';
		header( "Location: newDisputeTransactions.php" );
		exit();
	}
	else{
		$MerchantContactVar = $_POST["merchantcontactstatus"];
		
		if ($MerchantContactVar != "yes" && $MerchantContactVar != "no"){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Merchant has been Contacted!';
			header( "Location: newDisputeTransactions.php" );
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
			
			if ($dateGood != true || $dateGoodVAR != true){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Merchant Contact Day Error! Please Enter a valid Day the merchant was contacted in the format YYYY-MM-DD. You entered: '.$MerchantContactDayVar;
				header( "Location: newDisputeTransactions.php" );
				exit();
			}	
		}
	}
	
	if(!isset($_POST["merchantDescrption"])){
		$MerchantDescriptionVar = "";
	}
	else{
		$MerchantDescriptionVar = $_POST["merchantDescrption"];
		
		$stringGood = isAscii($MerchantDescriptionVar);
		
		if (!$stringGood){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Merchant Description were Detected! Please try again using regular characters!';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		
		$MerchantDescriptionVar = $func_DTCON->real_escape_string($MerchantDescriptionVar);
		
	}
	
	if(!isset($_POST["transactionComments"])){
		$TransactionCommentsVar = "";
	}
	else{
		$TransactionCommentsVar = $_POST["transactionComments"];
		
		$stringGood = isAscii($TransactionCommentsVar);
		
		if (!$stringGood){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Comments were Detected! Please try again using regular characters!';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		
		$TransactionCommentsVar = $func_DTCON->real_escape_string($TransactionCommentsVar);
	}
	
	if(!isset($_POST["AttachmentDescription"])){
		$AttachmentDescription = "";
	}
	else{
		$AttachmentDescription = $_POST["AttachmentDescription"];
		
		$stringGood = isAscii($AttachmentDescription);
		
		if (!$stringGood){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Attachment Comments were Detected! Please try again using regular characters!';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		
		$AttachmentDescription = $func_DTCON->real_escape_string($AttachmentDescription);
	}
	
	/*
	if(!isset($_POST["compromiseSelect"])){
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Compromise Selection Error! Please Select a Compromise.';
		header( "Location: newDisputeTransactions.php" );
		exit();
	}
	else{
		$CompromiseVar = $_POST["compromiseSelect"];
		
		if (!is_numeric($CompromiseVar)){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Compromise Selection! Please contact the System Admin';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		
		$CompromiseINT = intval($CompromiseVar);
		
		if($CompromiseINT < 0 || $CompromiseINT > $CompromiseSelectCOUNT){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Compromise Selection! Please contact the System Admin';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
	}
	
	if(!isset($_POST["CompromiseDescription"])){
		$CompromiseCommentsVar = "";
	}
	else{
		$CompromiseCommentsVar = $_POST["CompromiseDescription"];
		
		$stringGood = isAscii($CompromiseCommentsVar);
		
		if (!$stringGood){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Compromise Description were Detected! Please try again using regular characters!';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		
		$CompromiseCommentsVar = $func_DTCON->real_escape_string($CompromiseCommentsVar);
		
	}
	*/
	
	
	//Everything is here and good

	/*
	print $caseID . "<br>";
	
	print $TransactionDayVar . "<br>";
	print $PostedDayVar . "<br>";
	print $AmountdisputedVar . "<br>";
	print $RecieptStatusVar . "<br>";
	
	print $DisputeReasonVar . "<br>";
	print $DisputeDescriptionVar . "<br>";
	print $MerchantContactVar . "<br>";
	print $MerchantDescriptionVar . "<br>";
	print $TransactionCommentsVar . "<br>";
	
	print $AttachmentDescription . "<br>";
	
	print $CompromiseVar . "<br>";
	print $CompromiseCommentsVar . "<br>";
	
	var_dump($AttachmentsArrayVar);
	var_dump($_POST);
	var_dump($_FILES);
	
	exit();
	*/
	
	$checkTransactionInsert = "INSERT INTO checktransactions(id, caseid, cardid, amount, transactiondate, dateposted, disputereason, description, merchantname, merchantcontacted, merchantcontacteddate, merchantcontactdescription, receiptstatus, loss, reversalerror, procreditgiven, pcrescinded, pclettersent, pcreverselettersent, cbinitiated, cbaccepted, comments, compromiseid, compromisecomments, transactiondeleted, merchantphone, merchantnotes) VALUES (NULL, '".$caseID."', '".$cardID_Variable."', '".$AmountdisputedVar."', '".$TransactionDayVar."', '".$PostedDayVar."', '".$DisputeReasonVar."', '".$DisputeDescriptionVar."', '".$MerchantNameVAR."', '".$MerchantContactVar."', '".$MerchantContactDayVar."', '".$MerchantDescriptionVar."', '".$RecieptStatusVar."', TRUE, '0', '', '', '', '', FALSE, FALSE, '".$TransactionCommentsVar."', '1', '', FALSE, '', '')";
		
	if ($func_DTCON->query($checkTransactionInsert) === TRUE) {
		
		//print "Insert Worked";
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Transaction Insert! Please Contact the System Admin!';
		header( "Location: newDisputeTransactions.php" );
		exit();
	}
	
	$AttachmentsArray = array();
	$FilesBOOL = TRUE;
	
	foreach($_FILES['Attachments']['error'] as $LoopVal){
		
		if($LoopVal != 0){			//If any File has an error, Dont go in that If statement
			$FilesBOOL = FALSE;
		}
		
	}
	
	if (isset($_FILES['Attachments']) && $FilesBOOL){
		
		$newAttachName = "";
		$attach_query = "SELECT filename FROM checkattachments WHERE caseid='".$caseID."'";
		$attach_query_data = $func_DTCON->query($attach_query);
		$attach_data = $attach_query_data->fetch_all();
		$attachnumbers = count($attach_data);
		
		$checkUploadAddress = "FileFolder/UserAttachedDocuments/";
		
		// 15000000 bytes = ~14 MB
		
		$errors = array();
		
		foreach($_FILES['Attachments']['tmp_name'] as $key => $tmp_name ){
			
			$tmpArray = array();
			
			$file_name = $key.$_FILES['Attachments']['name'][$key];
			$file_size = $_FILES['Attachments']['size'][$key];
			$file_tmp  = $_FILES['Attachments']['tmp_name'][$key];
			$file_type = $_FILES['Attachments']['type'][$key]; 
			$file_errors = $_FILES['Attachments']['error'][$key]; 
			
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
					header( "Location: newDisputeTransactions.php" );
					exit();
				}
				
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'An Error occured with the file upload! Please Contact the System Admin.'.$errors;
				header( "Location: newDisputeTransactions.php" );
				exit();
			}
		}
	}
	
	//var_dump($AttachmentsArray);
	
	$newAtatchNum = count($AttachmentsArray);
	$CheckAttachmentVALUES = "";
	
	for($i=0; $i < $newAtatchNum; $i++){
		
		$checkAttachmentInsert = "INSERT INTO checkattachments(id, caseid, filename, filelocation, comments, iddeleted) VALUES (NULL, '".$caseID."', '".$AttachmentsArray[$i][0]."', '".$AttachmentsArray[$i][1]."', '".$AttachmentDescription."', FALSE)";
		
		$CheckAttachmentVALUES = $CheckAttachmentVALUES . " -- INSERT INTO checkattachments(id, caseid, filename, filelocation, comments) VALUES (NULL, ".$caseID.", ".$AttachmentsArray[$i][0].", ".$AttachmentsArray[$i][1].", ".$AttachmentDescription.") -- ";
		
		
		if ($func_DTCON->query($checkAttachmentInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with an Attachment Insert! Please Contact the System Admin!';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		
		
	}
	
	
	$checkTransactionVALUES = "INSERT INTO checktransactions(id, caseid, cardid, amount, transactiondate, dateposted, disputereason, description, merchantname, merchantcontacted, merchantcontactdescription, receiptstatus, loss, reversalerror, procreditgiven, pcrescinded, pclettersent, pcreverselettersent, cbinitiated, cbaccepted, comments, compromiseid, compromisecomments) VALUES (NULL, ".$caseID.", ".$cardID_Variable.", ".$AmountdisputedVar.", ".$TransactionDayVar.", ".$PostedDayVar.", ".$DisputeReasonVar.", ".$DisputeDescriptionVar.", ".$MerchantNameVAR.", ".$MerchantContactVar.", ".$MerchantDescriptionVar.", ".$RecieptStatusVar.", TRUE, 0, , , , , FALSE, FALSE, ".$TransactionCommentsVar.", 1, , FALSE, , )";

	
	//USER CHANGE LOG INSERT-----------------------------------------------------------------------------------------------------------------
	$userChangeInsert = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$func_username."', '".$func_ipaddr."', '1', '".$caseID."', 'Check Transaction Insert Sequence: ".$checkTransactionVALUES." -- Check Attachment Insert Sequences: ".$CheckAttachmentVALUES."')";
	
	//print $userChangeInsert;
	
	if ($func_DTCON->query($userChangeInsert) === TRUE) {
		
		//print "Insert Worked";
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
		header( "Location: newDisputeTransactions.php" );
		exit();
	}	
	
}

//---------------------------------------------------






if (($_SERVER["REQUEST_METHOD"] == "POST" || isset($_SESSION["WorkingDispute"])) && !isset($_SESSION["ADD_DISPUTE_ERROR"])){
	
	//var_dump($_POST);
	//var_dump($_SESSION);
	//var_dump($_FILES);
	//exit();
	
	if(isset($_POST["newDisputeSubmit"])){
		
		date_default_timezone_set("America/Chicago");
		$timeofChange = date("Y-m-d, G:i:s");
		
		if(!isset($_POST["DisputeDay"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Dispute Day Error! Please Contact the System Admin.';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$disputedayVAR = $_POST["DisputeDay"];
			$dateGood = validateDate($disputedayVAR);
			$dateGoodVAR = validatePastDate($disputedayVAR);
			
			if ($dateGood != true || $dateGoodVAR != true){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Day Date! Contact the System Admin!';
				header( "Location: newDispute.php" );
				exit();
			}
		}
		
		/*
		if(!isset($_POST["TempCaseNum"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Number is required.';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$TEMPcaseNumVAR = $_POST["TempCaseNum"];
			
			if (!is_numeric($TEMPcaseNumVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'The Case Number can be Numbers only!';
				header( "Location: newDispute.php" );
				exit();
			}
		}
		*/
		
		
		if(!isset($_POST["FirstName"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Name must be entered!';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$firstnameVAR = $_POST["FirstName"];
			$stringGood = isAscii($firstnameVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the name were Detected! Please try again using regular characters!';
				header( "Location: newDispute.php" );
				exit();
			}
			
			
			$firstnameVAR = $dtcon->real_escape_string($firstnameVAR);
			
		}
		
		if(!isset($_POST["LastName"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Name must be entered!';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$lastnameVAR = $_POST["LastName"];
			$stringGood = isAscii($lastnameVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the name were Detected! Please try again using regular characters!';
				header( "Location: newDispute.php" );
				exit();
			}
			
			$lastnameVAR = $dtcon->real_escape_string($lastnameVAR);
			
		}
		
		if(!isset($_POST["phoneNumber"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Phone Number needs to be given.';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$phoneNumVAR = $_POST["phoneNumber"];
			
			if(!preg_match("/^[0-9]{3}[ \-]{0,1}[0-9]{3}[ \-]{0,1}[0-9]{4}$/", $phoneNumVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Phone Number must be digits, slashes, parentheses, or dashes only. Ex: \'123 456 7890\', \'123-456-7890\', \'1234567890\'';
				header( "Location: newDispute.php" );
				exit();
			}
			
		}
		
		if(!isset($_POST["emailAddr"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Email address Error. Please try again.';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$emailAddrVAR = $_POST["emailAddr"];
			$stringGood = isAscii($emailAddrVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Email address Error. Please try again.';
				header( "Location: newDispute.php" );
				exit();
			}
			
			$emailAddrVAR = $dtcon->real_escape_string($emailAddrVAR);
			
		}
		
		if(!isset($_POST["AddressOne"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Address Line One Error. Please try again.';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$AddressVar = $_POST["AddressOne"];
			$stringGood = isAscii($AddressVar);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Address Line One Error. Please try again.';
				header( "Location: newDispute.php" );
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
				header( "Location: newDispute.php" );
				exit();
			}
			
			$Address2VAR = $dtcon->real_escape_string($Address2VAR);
			
		}
		
		if(!isset($_POST["CityAddr"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'City Line Error. Please try again.';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$CityAddrVar = $_POST["CityAddr"];
			$stringGood = isAscii($CityAddrVar);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'City Line Error. Please try again.';
				header( "Location: newDispute.php" );
				exit();
			}
			
			$CityAddrVar = $dtcon->real_escape_string($CityAddrVar);
			
		}
		
		if(!isset($_POST["StateAddr"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'State Line Error. Please try again.';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$StateAddrVar = $_POST["StateAddr"];
			$stringGood = isAscii($StateAddrVar);
			$stringState = isState($StateAddrVar);
			
			if(!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'State Line Error. Please try again.';
				header( "Location: newDispute.php" );
				exit();
			}
			if(!$stringState){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'State Line Error. Please try again.';
				header( "Location: newDispute.php" );
				exit();
			}
			
			$StateAddrVar = $dtcon->real_escape_string($StateAddrVar);
			
		}
		
		if(!isset($_POST["ZipCode"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Zip code must be entered!';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$ZipCodeVar = $_POST["ZipCode"];
			
			if (!is_numeric($ZipCodeVar)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'The Zip code can be Numbers only!';
				header( "Location: newDispute.php" );
				exit();
			}
			
			//Future Update?
			
		} 
		
		if(!isset($_POST["inpersonorPhone"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the customer is on the phone or in person!';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$custinpersonphoneVAR = $_POST["inpersonorPhone"];
			
			if ($custinpersonphoneVAR != "inperson" && $custinpersonphoneVAR != "phone"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Customer is on the phone or in person! Please contact the System Admin for more help.';
				header( "Location: newDispute.php" );
				exit();
			}
			
			if($custinpersonphoneVAR == "inperson"){
				$custinpersonphoneVAR = 1;
			}
			else{
				$custinpersonphoneVAR = 2;
			}
		} 
		
		/*
		if(!isset($_POST["checkACH"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the dispute is an ACH or Check dispute!';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$checkACH_VAR = $_POST["checkACH"];
			
			if ($checkACH_VAR != "Check" && $checkACH_VAR != "ACH"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the dispute is a Check Card or ACH dispute! Please contact the System Admin for more help.';
				header( "Location: newDispute.php" );
				exit();
			}
		} 
		*/
		
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
		
		if(!isset($_POST["AccountNumber"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'An Account Number must be entered!';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$accountNumberVAR = $_POST["AccountNumber"];
			
			if (!is_numeric($accountNumberVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number can be Numbers only!';
				header( "Location: newDispute.php" );
				exit();
			}
			
			//Future Update?
			
		} 
		
		if(!isset($_POST["AccountType"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select the Account Type!';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$accountTypeVAR = $_POST["AccountType"];
			
			if (!is_numeric($accountTypeVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Account Type! Please contact the System Admin';
				header( "Location: newDispute.php" );
				exit();
			}
			
			$accountTypeINT = intval($accountTypeVAR);
			
			if($accountTypeINT < 1 || $accountTypeINT > $AccountTypeCOUNT){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Account Type! Please contact the System Admin';
				header( "Location: newDispute.php" );
				exit();
			}
			
			//Future Update?
			
		} 
		
		if(!isset($_POST["BusiCustType"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the dispute is a Business Customer or a Consumer!';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$consumerorBusinessVAR = $_POST["BusiCustType"];
			
			if ($consumerorBusinessVAR != "Consumer" && $consumerorBusinessVAR != "Business"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Illegal Characters detected in Consumer or Business Selection! Please contact the System Admin!';
				header( "Location: newDispute.php" );
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
				header( "Location: newDispute.php" );
				exit();
			}
			
			$accountcommentsVAR = $dtcon->real_escape_string($accountcommentsVAR);
			
		} 
		
		if(!isset($_POST["Card_Number"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Card Number must be entered!';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$CARDNumberVAR = $_POST["Card_Number"];
			
			if (!is_numeric($CARDNumberVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number can be Numbers only!';
				header( "Location: newDispute.php" );
				exit();
			}
			
			$len = strlen($CARDNumberVAR);
			
			if($len != 16){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number has to be exactly 16 digits!';
				header( "Location: newDispute.php" );
				exit();
			}
			
		} 
		
		if(!isset($_POST["CardType"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the card type!';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$CARDTypeVAR = $_POST["CardType"];
			
			if (!is_numeric($CARDTypeVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Type! Please contact the System Admin';
				header( "Location: newDispute.php" );
				exit();
			}
			
			$CARDTypeINT = intval($CARDTypeVAR);
			
			if($CARDTypeINT < 1 || $CARDTypeINT > $CardTypeCOUNT){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Type! Please contact the System Admin';
				header( "Location: newDispute.php" );
				exit();
			}
			
			//Future Update?
		} 
		
		if(!isset($_POST["CardStatus"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select the card status!';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$CARDstatusVAR = $_POST["CardStatus"];
			
			if (!is_numeric($CARDstatusVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Status Selection! Please contact the System Admin';
				header( "Location: newDispute.php" );
				exit();
			}
			
			$CARDstatusINT = intval($CARDstatusVAR);
			
			if($CARDstatusINT < 1 || $CARDstatusINT > $CardStatusCOUNT){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Status Selection! Please contact the System Admin';
				header( "Location: newDispute.php" );
				exit();
			}
			
			//Future Update?
		} 
		
		if(!isset($_POST["CardPossession"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the customer has the card or not!';
			header( "Location: newDispute.php" );
			exit();
		}
		else{
			$CARDpossessionVAR = $_POST["CardPossession"];
			
			if (!is_numeric($CARDpossessionVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Possession Selection! Please contact the System Admin';
				header( "Location: newDispute.php" );
				exit();
			}
			
			$CARDpossessionINT = intval($CARDpossessionVAR);
			
			if($CARDpossessionINT < 1 || $CARDpossessionINT > $CardPossessionCOUNT){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Card Possession Selection! Please contact the System Admin';
				header( "Location: newDispute.php" );
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
					header( "Location: newDispute.php" );
					exit();
				}
			}
		} 
		
		if(!isset($_POST["ChipCard"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Receipt Status Error! Please select "yes" or "no".';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		else{
			$ChipCardVAR = $_POST["ChipCard"];
			
			if ($ChipCardVAR != "yes" && $ChipCardVAR != "no"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Please Select if the Customer has the receipt! Please contact the System Admin for more help.';
				header( "Location: newDispute.php" );
				exit();
			}
			
			if($ChipCardVAR == "yes"){
				$ChipCardVAR = "1";
			}
			else{
				$ChipCardVAR = "0";
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
				header( "Location: newDispute.php" );
				exit();
			}
			
			$CARDnumbercommentsVAR = $dtcon->real_escape_string($CARDnumbercommentsVAR);
			
		}
		
		//Everything is here and good

		/*
		print $checkACH_VAR . "<br>";
		
		print $disputedayVAR . "<br>";
		print $firstnameVAR . "<br>";
		print $lastnameVAR . "<br>";
		print $custinpersonphoneVAR . "<br>";
		print $username . "<br>";
		
		print $accountNumberVAR . "<br>";
		print $accountTypeVAR . "<br>";
		print $consumerorBusinessVAR . "<br>";
		print $accountcommentsVAR . "<br>";
		
		print $CARDNumberVAR . "<br>";
		print $CARDTypeVAR . "<br>";
		print $CARDstatusVAR . "<br>";
		print $CARDpossessionVAR . "<br>";
		print $CARDmissingdateVAR . "<br>";
		print $CARDnumbercommentsVAR . "<br>";
		
		print $username . "<br>";
		print $ipaddress . "<br>";
		print $timeofChange . "<br>";
		*/
		
		/*
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
		
		//Dispute Case INSERT-----------------------------------------------------------------------------------------------------------------
		$checkCaseInsert = "INSERT INTO checkcases(id, casestartdate, custfname, custlname, custphone, custemail, custaddressone, custaddresstwo, custcityaddr, custstateaddr, custzipaddr, redflag, sevlev, comments, userstarted, casedoneinput, customerstartmethod, pcletterprintflag, caseclosed, casedeleted, archive) VALUES (NULL, '".$disputedayVAR."', '".$firstnameVAR."', '".$lastnameVAR."', '".$phoneNumVAR."', '".$emailAddrVAR."', '".$AddressVar."', '".$Address2VAR."', '".$CityAddrVar."', '".$StateAddrVar."', '".$ZipCodeVar."', FALSE, '0', '', '".$username."', FALSE, '".$custinpersonphoneVAR."', FALSE, FALSE, FALSE, FALSE)";
		
		//'".$TEMPcaseNumVAR."'
		//var_dump($checkCaseInsert);
		//exit();
		
		if ($dtcon->query($checkCaseInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Case Insert! Please Contact the System Admin!';
			$_SESSION["SQL_Command"] = $checkCaseInsert;
			header( "Location: DisputetrackError.php" );
			exit();
		}
		
		$caseID = $dtcon->insert_id;
		$cardNumber = $CARDNumberVAR;
		$accountNumber = $accountNumberVAR;
		
		//Currently Working Case Session
		$_SESSION["WorkingDispute"] = 'True';
		$_SESSION["Dispute_CaseID"] = $caseID;
		
		//print $caseID;
		
		//Dispute Case ACCOUNT INSERT----------------------------------------------------------------------------------------------------------
		$checkAccountInsert  = "INSERT INTO checkaccountnumbers(id, caseid, accountnumber, accounttype, businessaccount, comments, accoountnew) VALUES (NULL, '".$caseID."', '".$accountNumberVAR."', '".$accountTypeVAR."', '".$consumerorBusinessVAR."', '".$accountcommentsVAR."', '".$NewAccountBool."')";
		
		//var_dump($checkAccountInsert);
		//exit();
		
		if ($dtcon->query($checkAccountInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Account Insert! Please Contact the System Admin!';
			$_SESSION["SQL_Command"] = $checkAccountInsert;
			header( "Location: DisputetrackError.php" );
			exit();
		}
		
		//Dispute Case CARD INSERT--------------------------------------------------------------------------------------------------------------
		$checkCardInsert = "INSERT INTO checkcardnumbers(id, caseid, cardnumber, cardtype, cardstatus, cardpossession, cardmissingdate, chipcard, comments) VALUES (NULL, '".$caseID."', '".$CARDNumberVAR."', '".$CARDTypeVAR."', '".$CARDstatusVAR."', '".$CARDpossessionVAR."', '".$CARDmissingdateVAR."', '".$ChipCardVAR."', '".$CARDnumbercommentsVAR."')";
		
		//var_dump($checkCardInsert);
		
		if ($dtcon->query($checkCardInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Card Insert! Please Contact the System Admin!';
			$_SESSION["SQL_Command"] = $checkCardInsert;
			header( "Location: DisputetrackError.php" );
			exit();
		}
		
		$checkcaseVALUES = "(NULL, ".$disputedayVAR.", ".$firstnameVAR.", ".$lastnameVAR.", ".$phoneNumVAR.", ".$emailAddrVAR.", ".$AddressVar.", ".$Address2VAR.", ".$CityAddrVar.", ".$StateAddrVar.", ".$ZipCodeVar.", FALSE, 0, , ".$username.", FALSE, ".$custinpersonphoneVAR.", FALSE, FALSE, FALSE)";
		$checkaccountVALUES = "(NULL, ".$caseID.", ".$accountNumberVAR.", ".$accountTypeVAR.", ".$consumerorBusinessVAR.", ".$accountcommentsVAR.")";
		$checkcardVALUES = "(NULL, ".$caseID.", ".$CARDNumberVAR.", ".$CARDTypeVAR.", ".$CARDstatusVAR.", ".$CARDpossessionVAR.", ".$CARDmissingdateVAR.", ".$CARDnumbercommentsVAR.")";
		
		
		//USER CHANGE LOG INSERT-----------------------------------------------------------------------------------------------------------------
		$userChangeInsert = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '1', '".$caseID."', 'Check Case Insert Sequence: ".$checkcaseVALUES." -- Check Account Insert Sequence: ".$checkaccountVALUES." -- Check Card Insert Sequence: ".$checkcardVALUES."')";
		
		//print $userChangeInsert;
		
		if ($dtcon->query($userChangeInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			$_SESSION["SQL_Command"] = $userChangeInsert;
			header( "Location: newDispute.php" );
			exit();
		}		
		
		$_SESSION["TransactionAdded"] = 'Case added Successfully!';
		
	}
	else if(isset($_POST["newDisputeTransactionsSubmit"])){
		
		$caseID = $_SESSION["Dispute_CaseID"];
		
		$AccountNumQuery = "SELECT accountnumber FROM checkaccountnumbers where caseid='" . $caseID . "'";	
		$AccountNumQuery_Data = $dtcon->query($AccountNumQuery);
		$AccountNumData = $AccountNumQuery_Data->fetch_all();
		
		//var_dump($AccountNumData);
		
		if (!empty($AccountNumData)){
			if (!empty($AccountNumData[0])){
				$accountNumber = $AccountNumData[0][0];
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID Was not found! Please Contact the System Admin.';
				unset($_SESSION["WorkingDispute"]);
				unset($_SESSION["Dispute_CaseID"]);
				header( "Location: newDispute.php" );
				exit();
			}
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID Was not found! Please Contact the System Admin.';
			unset($_SESSION["WorkingDispute"]);
			unset($_SESSION["Dispute_CaseID"]);
			header( "Location: newDispute.php" );
			exit();
		}
		
		$CardNumQuery = "SELECT id, cardnumber FROM checkcardnumbers where caseid='" . $caseID . "'";
		$CardNumQuery_Data = $dtcon->query($CardNumQuery);
		$CardNumData = $CardNumQuery_Data->fetch_all();
		
		//var_dump($CardNumData);
		
		if (!empty($CardNumData)){
			if (!empty($CardNumData[0])){
				$cardID_Variable = $CardNumData[0][0];
				$cardNumber = $CardNumData[0][1];
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number for the Case ID Was not found! Please Contact the System Admin.';
				unset($_SESSION["WorkingDispute"]);
				unset($_SESSION["Dispute_CaseID"]);
				header( "Location: newDispute.php" );
				exit();
			}
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number for the Case ID Was not found! Please Contact the System Admin.';
			unset($_SESSION["WorkingDispute"]);
			unset($_SESSION["Dispute_CaseID"]);
			header( "Location: newDispute.php" );
			exit();
		}
		
		//addTransaction($caseID, $cardID_Variable, $AccountTypeCOUNT, $CardTypeCOUNT, $CardStatusCOUNT, $CardPossessionCOUNT, $DisputeReasonCOUNT, $CompromiseSelectCOUNT);
		
		addTransaction($caseID, $cardID_Variable, $DisputeReasonCOUNT, $dtcon, $username, $ipaddress);
		
		
		$_SESSION["TransactionAdded"] = 'Transaction added Successfully!';
		$_SESSION["CaseCanBeDone"] = 'TRUE';
		
	}
	else if(isset($_POST["newDisputeTransactionsSubmitNext"])){
		
		//var_dump($_SESSION);
		//var_dump($_POST);
		//exit();
		
		//print "Default<br>";
		if(isset($_SESSION["CaseCanBeDone"]) && $_SESSION["CaseCanBeDone"] == 'TRUE'){
			//print "Inside One<br>";
			if(isset($_POST["DisputeDay"]) && isset($_POST["PostedDay"]) && isset($_POST["AmountDisputed"]) && isset($_POST["receiptstatus"]) && isset($_POST["DisputeReason"]) && isset($_POST["DisputeDescription"]) && isset($_POST["MerchantName"]) && isset($_POST["merchantcontactstatus"]) && isset($_POST["MerchantContactDay"]) && isset($_POST["merchantDescrption"]) && isset($_POST["transactionComments"]) && isset($_POST["AttachmentDescription"]) && isset($_POST["newDisputeTransactionsSubmitNext"])){
				//print "Inside Two<br>";
				if($_POST["DisputeDay"] == '' && $_POST["PostedDay"] == '' && $_POST["AmountDisputed"] == '' && $_POST["receiptstatus"] == 'select' && $_POST["DisputeReason"] == 'select' && $_POST["DisputeDescription"] == '' && $_POST["MerchantName"] == '' && $_POST["merchantcontactstatus"] == 'select' && $_POST["MerchantContactDay"] == '' && $_POST["merchantDescrption"] == '' && $_POST["transactionComments"] == '' && $_POST["AttachmentDescription"] == '' && $_POST["newDisputeTransactionsSubmitNext"] == 'Next'){
					//print "Inside Three<br>";
					//exit();
					//unset($_SESSION['CaseCanBeDone']);
					$_SESSION["FinishDisputeOne"] = "True";
					header( "Location: newDisputeConfirmation.php" ); 
					exit();
				}
			}
		}
		//exit();
		
		
		$caseID = $_SESSION["Dispute_CaseID"];
		
		$AccountNumQuery = "SELECT accountnumber FROM checkaccountnumbers where caseid='" . $caseID . "'";	
		$AccountNumQuery_Data = $dtcon->query($AccountNumQuery);
		$AccountNumData = $AccountNumQuery_Data->fetch_all();
		
		//var_dump($AccountNumData);
		
		if (!empty($AccountNumData)){
			if (!empty($AccountNumData[0])){
				$accountNumber = $AccountNumData[0][0];
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID Was not found! Please Contact the System Admin.';
				unset($_SESSION["WorkingDispute"]);
				unset($_SESSION["Dispute_CaseID"]);
				header( "Location: newDispute.php" );
				exit();
			}
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID Was not found! Please Contact the System Admin.';
			unset($_SESSION["WorkingDispute"]);
			unset($_SESSION["Dispute_CaseID"]);
			header( "Location: newDispute.php" );
			exit();
		}
		
		$CardNumQuery = "SELECT id, cardnumber FROM checkcardnumbers where caseid='" . $caseID . "'";
		$CardNumQuery_Data = $dtcon->query($CardNumQuery);
		$CardNumData = $CardNumQuery_Data->fetch_all();
		
		//var_dump($CardNumData);
		
		if (!empty($CardNumData)){
			if (!empty($CardNumData[0])){
				$cardID_Variable = $CardNumData[0][0];
				$cardNumber = $CardNumData[0][1];
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number for the Case ID Was not found! Please Contact the System Admin.';
				unset($_SESSION["WorkingDispute"]);
				unset($_SESSION["Dispute_CaseID"]);
				header( "Location: newDispute.php" );
				exit();
			}
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number for the Case ID Was not found! Please Contact the System Admin.';
			unset($_SESSION["WorkingDispute"]);
			unset($_SESSION["Dispute_CaseID"]);
			header( "Location: newDispute.php" );
			exit();
		}
		
		//addTransaction($caseID, $cardID_Variable, $AccountTypeCOUNT, $CardTypeCOUNT, $CardStatusCOUNT, $CardPossessionCOUNT, $DisputeReasonCOUNT, $CompromiseSelectCOUNT);
		
		addTransaction($caseID, $cardID_Variable, $DisputeReasonCOUNT, $dtcon, $username, $ipaddress);
		
		
		
		
		$_SESSION["FinishDisputeOne"] = "True";
		header( "Location: newDisputeConfirmation.php" ); 
		exit();
	}
	else if(isset($_POST["NewDisputeDelete"])){
		
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
		
		/*
		$AccountNumQuery = "SELECT * FROM checkaccountnumbers where caseid='" . $caseID . "'";
		$AccountNumQuery_Data = $dtcon->query($AccountNumQuery);
		$AccountNumData = $AccountNumQuery_Data->fetch_all();
		
		//var_dump($AccountNumData);
		
		if (!empty($AccountNumData)){
			if (!empty($AccountNumData[0])){
				
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID Was not found! Please Contact the System Admin.';
				header( "Location: newDisputeTransactions.php" );
				exit();
			}
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID Was not found! Please Contact the System Admin.';
			header( "Location: newDisputeTransactions.php" );
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
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number for the Case ID Was not found! Please Contact the System Admin.';
				header( "Location: newDisputeTransactions.php" );
				exit();
			}
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number for the Case ID Was not found! Please Contact the System Admin.';
			header( "Location: newDisputeTransactions.php" );
			exit();
		}
		
		
		$TransactionQuery = "SELECT * FROM checktransactions where caseid='" . $caseID . "'";
		$TransactionQuery_Data = $dtcon->query($TransactionQuery);
		$TransactionData = $TransactionQuery_Data->fetch_all();
		
		//var_dump($TransactionData);
		
		$AttachmentQuery = "SELECT * FROM checkattachments where caseid='" . $caseID . "'";
		$AttachmentQuery_Data = $dtcon->query($AttachmentQuery);
		$AttachmentData = $AttachmentQuery_Data->fetch_all();
		
		//var_dump($AttachmentData);
		*/
		
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
	else if($_SERVER["REQUEST_METHOD"] != "POST" && isset($_SESSION["WorkingDispute"])){
		
		//Currently Working Case Session
		//$_SESSION["WorkingDispute"] = 'True';
		//$_SESSION["Dispute_CaseID"] = $caseID;
		
		$caseID = $_SESSION["Dispute_CaseID"];
		
		$AccountNumQuery = "SELECT accountnumber FROM checkaccountnumbers where caseid='" . $caseID . "'";
		$AccountNumQuery_Data = $dtcon->query($AccountNumQuery);
		$AccountNumData = $AccountNumQuery_Data->fetch_all();
		
		//var_dump($AccountNumData);
		
		if (!empty($AccountNumData)){
			if (!empty($AccountNumData[0])){
				$accountNumber = $AccountNumData[0][0];
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID Was not found! Please Contact the System Admin.';
				unset($_SESSION["WorkingDispute"]);
				unset($_SESSION["Dispute_CaseID"]);
				header( "Location: newDispute.php" );
				exit();
			}
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID Was not found! Please Contact the System Admin.';
			unset($_SESSION["WorkingDispute"]);
			unset($_SESSION["Dispute_CaseID"]);
			header( "Location: newDispute.php" );
			exit();
		}
		
		$CardNumQuery = "SELECT id, cardnumber FROM checkcardnumbers where caseid='" . $caseID . "'";
		$CardNumQuery_Data = $dtcon->query($CardNumQuery);
		$CardNumData = $CardNumQuery_Data->fetch_all();
		
		//var_dump($CardNumData);
		
		if (!empty($CardNumData)){
			if (!empty($CardNumData[0])){
				//$cardID_Variable = $CardNumData[0][0];
				$cardNumber = $CardNumData[0][1];
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID Was not found! Please Contact the System Admin.';
				unset($_SESSION["WorkingDispute"]);
				unset($_SESSION["Dispute_CaseID"]);
				header( "Location: newDispute.php" );
				exit();
			}
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number for the Case ID Was not found! Please Contact the System Admin.';
			unset($_SESSION["WorkingDispute"]);
			unset($_SESSION["Dispute_CaseID"]);
			header( "Location: newDispute.php" );
			exit();
		}
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Server POST, please contact the System Admin.';
		header( "Location: index.php" );
		exit();
	}
	
	
}
else if(($_SERVER["REQUEST_METHOD"] == "POST" || isset($_SESSION["WorkingDispute"])) && isset($_SESSION["ADD_DISPUTE_ERROR"])){
	
	//Currently Working Case Session
	//$_SESSION["WorkingDispute"] = 'True';
	//$_SESSION["Dispute_CaseID"] = $caseID;
	
	$caseID = $_SESSION["Dispute_CaseID"];
	
	$AccountNumQuery = "SELECT accountnumber FROM checkaccountnumbers where caseid='" . $caseID . "'";
	$AccountNumQuery_Data = $dtcon->query($AccountNumQuery);
	$AccountNumData = $AccountNumQuery_Data->fetch_all();
	
	//var_dump($AccountNumData);
	
	if (!empty($AccountNumData)){
		if (!empty($AccountNumData[0])){
			$accountNumber = $AccountNumData[0][0];
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] .= '<br>Account Number for the Case ID Was not found! Please Contact the System Admin.';
			unset($_SESSION["WorkingDispute"]);
			unset($_SESSION["Dispute_CaseID"]);
			header( "Location: newDispute.php" );
			exit();
		}
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] .= '<br>Account Number for the Case ID Was not found! Please Contact the System Admin.';
		unset($_SESSION["WorkingDispute"]);
		unset($_SESSION["Dispute_CaseID"]);
		header( "Location: newDispute.php" );
		exit();
	}
	
	$CardNumQuery = "SELECT id, cardnumber FROM checkcardnumbers where caseid='" . $caseID . "'";
	$CardNumQuery_Data = $dtcon->query($CardNumQuery);
	$CardNumData = $CardNumQuery_Data->fetch_all();
	
	//var_dump($CardNumData);
	
	if (!empty($CardNumData)){
		if (!empty($CardNumData[0])){
			//$cardID_Variable = $CardNumData[0][0];
			$cardNumber = $CardNumData[0][1];
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] .= '<br>Account Number for the Case ID Was not found! Please Contact the System Admin.';
			unset($_SESSION["WorkingDispute"]);
			unset($_SESSION["Dispute_CaseID"]);
			header( "Location: newDispute.php" );
			exit();
		}
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] .= '<br>Account Number for the Case ID Was not found! Please Contact the System Admin.';
		unset($_SESSION["WorkingDispute"]);
		unset($_SESSION["Dispute_CaseID"]);
		header( "Location: newDispute.php" );
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
    <title>Dispute Tracker - Add Amounts</title>
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
$PageTitle = "Dispute Track - New Dispute Transaction";

if($accesslevel >= 7){
	include("assets/includes/HTMLscript.php");
}


include("assets/includes/autologout.php");

include("assets/includes/loadingHTML.php");

?>

   
   <script>
		var DeleteClicked = false;
   		
		function addNotices(numberofNotices) {
			
			for (i = 0; i < numberofNotices; i++){
				
				var newInt = i+1
				
				var allElementnames = 'noticeforReason_' + newInt;
				
				var allNoticeID = document.getElementById(allElementnames);
			
				allNoticeID.style.display = 'none';
				
			}

			var currReasonID = document.getElementById('DisputeReasonID');
			var currValue = currReasonID.options[currReasonID.selectedIndex].value;
			
			var newElementName = 'noticeforReason_' + currValue;
			
			var currNoticeID = document.getElementById(newElementName);
			
			currNoticeID.style.display = 'block';
			
			
			
		}
		
		
		function merchantChange(){
			
			var currReasonID = document.getElementById('marchantcontactstatusID');
			var currValue = currReasonID.options[currReasonID.selectedIndex].value;
			
			if (currValue == 'yes'){
				
				var x = document.getElementById('MerchantContactDayID');
			
				x.style.display = 'block';
				
			}
			else{
				alert("Please have customer contact the merchant, if possible, at this time. \nOtherwise please note down details in the merchant contact description box");
				
				var x = document.getElementById('MerchantContactDayID');
				
				x.style.display = 'none';
			
				document.getElementById('MerchContactDate').value = '';

			}
			
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

			
			var TransactionDayVAR = form.DisputeDay.value;
			var PostedDayVAR = form.PostedDay.value;
			var AmountDisputedVAR = form.AmountDisputed.value;
			var receiptstatusVAR = form.receiptstatus.value;
			
			var DisputeReasonVAR = form.DisputeReason.value;
			var DisputeDescriptionVAR = form.DisputeDescription.value;
			
			var MerchantNameVAR = form.MerchantName.value;
			var merchantcontactstatusVAR = form.merchantcontactstatus.value;
			var MerchantContactDayVAR = form.MerchantContactDay.value;
			var merchantDescrptionVAR = form.merchantDescrption.value;
			var transactionCommentsVAR	= form.transactionComments.value;
			
			var AttachmentDescriptionVAR = form.AttachmentDescription.value;
			
			//alert(MerchantNameVAR);
			
			if(TransactionDayVAR == "" && PostedDayVAR == "" && AmountDisputedVAR == "" && receiptstatusVAR == "select" && DisputeReasonVAR == "select" && DisputeDescriptionVAR == "" && MerchantNameVAR == "" && merchantcontactstatusVAR == "select" && MerchantContactDayVARb == "" && merchantDescrptionVAR == "" && transactionCommentsVAR == ""){
				return true;
			}
			
			if(DeleteClicked == "true"){
				return true;
			}
			
			
			
			//Transaction Day --------------------------------------------------------------
			if(/^[0-9-]+$/.test(TransactionDayVAR)){
				if (isValidDate(TransactionDayVAR)){
					successful_bool = true;
				}
				else{
					alert("Date must be in format 'YYYY-MM-DD'.");
					var successful_bool = false;
					return false;
				}
			}
			else{
				alert("Date must be in format 'YYYY-MM-DD'.");
				var successful_bool = false;
				return false;
			}
			//Posted Day --------------------------------------------------------------
			if(/^[0-9-]+$/.test(PostedDayVAR) || PostedDayVAR.value.length == 0){
				if (isValidDate(PostedDayVAR)){
					successful_bool = true;
				}
				else if(PostedDayVAR.value.length == 0){
					successful_bool = true;
				}
				else{
					alert("Date must be in format 'YYYY-MM-DD'.");
					var successful_bool = false;
					return false;
				}
			}
			else{
				alert("Date must be in format 'YYYY-MM-DD'.");
				var successful_bool = false;
				return false;
			}
			//Amount Disputed ----------------------------------------------------------
			if(/^\d+(?:\.\d{0,2})?$/.test(AmountDisputedVAR)){
				successful_bool = true;
			}
			else{
				alert("Amounts must be in the form of '123.45', '123', '1234567.00'");
				successful_bool = false;
				return false;
			}
			//Receipt Status ----------------------------------------------------------
			if(receiptstatusVAR == "yes" || receiptstatusVAR == "no"){
				successful_bool = true;
			}
			else{
				alert("You must select if the customer has the receipt.");
				successful_bool = false;
				return false;
			}
			//Dispute Reason ----------------------------------------------------------
			if(/^\d+$/.test(DisputeReasonVAR)){
				successful_bool = true;
			}
			else{
				alert("You must select a Dispute Reason.");
				successful_bool = false;
				return false;
			}
			//Dispute Description ----------------------------------------------------------
			if(/^[\x00-\x7F]*$/.test(DisputeDescriptionVAR)){
				successful_bool = true;
			}
			else{
				alert("You must input only regular characters in the Dispute Description.");
				successful_bool = false;
				return false;
			}
			//Merchant Name ---------------------------------------------------------------------
			if(/^[\x00-\x7F]*$/.test(MerchantNameVAR)){
				//alert(MerchantNameVAR);
				if(MerchantNameVAR == ""){
					alert("Merchant Name must be letters and digits only.");
					successful_bool = false;
					return false;
				}
				else{
					successful_bool = true;
				}
				
			}
			else{
				alert("Merchant Name must be letters and digits only.");
				successful_bool = false;
				return false;
			}
			//Merchant Contact Status ----------------------------------------------------------
			if(merchantcontactstatusVAR == "yes" || merchantcontactstatusVAR == "no"){
				successful_bool = true;
			}
			else{
				alert("Select if the Merchant has been contacted.");
				successful_bool = false;
				return false;
			}
			//Merchant Contact Date ---------------------------------------------------------------------
			if(merchantcontactstatusVAR == "no"){
				successful_bool = true;
				MerchantContactDayVAR = "";
			}
			else{
				if(/^[0-9-]+$/.test(MerchantContactDayVAR)){
					if (isValidDate(MerchantContactDayVAR)){
						successful_bool = true;
					}
					else{
						alert("Merchant Date must be in format 'YYYY-MM-DD'.");
						var successful_bool = false;
						return false;
					}
				}
				else{
					alert("Merchant Date must be in format 'YYYY-MM-DD'.");
					var successful_bool = false;
					return false;
				}
			}
			//Merchant Description ----------------------------------------------------------
			if(/^[\x00-\x7F]*$/.test(merchantDescrptionVAR)){
				
				if(merchantDescrptionVAR == ""){
					alert("Merchant Description is required. It must be letters and digits only.");
					successful_bool = false;
					return false;
				}
				else{
					successful_bool = true;
				}
			}
			else{
				alert("You must input only regular characters in the Merchant Description.");
				successful_bool = false;
				return false;
			}
			//Transaction Comments ----------------------------------------------------------
			if(/^[\x00-\x7F]*$/.test(transactionCommentsVAR)){
				successful_bool = true;
			}
			else{
				alert("You must input only regular characters in the Transaction Comments.");
				successful_bool = false;
				return false;
			}
			//Attachment Comments ----------------------------------------------------------
			if(/^[\x00-\x7F]*$/.test(AttachmentDescriptionVAR)){
				successful_bool = true;
			}
			else{
				alert("You must input only regular characters in the Attachment comments.");
				successful_bool = false;
				return false;
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
                     <h2>New Dispute - Add New Transaction</h2>   
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
				
                 <!-- /. ROW  -->
                  <hr />
				  
				  <!--  Main Body  -->
					
					<!-- /. ROW  -->
						<div class="row">
							<div class="col-md-2">
								<h4>Case ID: <?php print $caseID; ?></h4>
							</div>
							<div class="col-md-4">
								<h4>Card Number: <?php print $cardNumber ?></h4>
							</div>
							<div class="col-md-4">
								<h4>Account Number: <?php print $accountNumber ?></h4>
							</div>
						</div>
						<hr />
					
					
					<!-- /. ROW  -->
					
					<form id="newDisputeFormTransaction_ID" autocomplete="off" enctype="multipart/form-data" name="newDisputeTransactionsForm" method="post" action="newDisputeTransactions.php" onsubmit="return validate(this);showLoading();">
						
						<div class="row">
							<div class="col-md-2">
								<label>Transaction Date</label>&nbsp;<a target="_blank" title="Please select when the transaction occurred using the calendar tool. The format must be: YYYY-MM-DD. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="DisputeDay" onFocus="showCalendarControl(this);">
								
							</div>
							<div class="col-md-2">
								<label>Date Posted to Account</label>&nbsp;<a target="_blank" title="Please select when the transaction posted to the account using the calendar tool. The format must be: YYYY-MM-DD. This can be left blank."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" name="PostedDay" onFocus="showCalendarControl(this);">
								
							</div>
							<div class="col-md-2">
								<label>Amount Disputed</label>&nbsp;<a target="_blank" title="Please enter the transaction amount. Please do not include the dollar sign. This field accepts digits and a single period. Ex.: '45', '45.86'. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" style="background: url(./assets/img/dollar-sign.png) no-repeat 0px 0px; background-size: 30px; padding-left:22px;" name="AmountDisputed" maxlength="11">
							</div>
							<div class="col-md-4">
								<label>Does the customer have a receipt for the transaction?</label>&nbsp;<a target="_blank" title="Please select if the customer has the receipt for the transaction. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<select id="receiptstatusID" name="receiptstatus" class="form-control"> 
									<option value="select" selected="select">Select One</option>
									<option value="yes">Yes</option>
									<option value="no">No</option>
								</select> 
							</div>
						</div>
						<hr />
						
					
					<!-- /. Row -->
						<div class="row">
						
							<div class="col-md-6">
								<label>Dispute Reason</label>&nbsp;<a target="_blank" title="Please select the customers dispute reason for this transaction. Please see the notice to CSR for any additional information needed. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
<?php


$reason_query = "SELECT id, reason, noticetext FROM disputereasons WHERE iddeleted='0'";

$reason_query_data = $dtcon->query($reason_query);

$reason_data = $reason_query_data->fetch_all();

//var_dump($reason_data);
$NumberofReason = count($reason_data);

print '<select id="DisputeReasonID" name="DisputeReason" class="form-control" onchange="addNotices(' . $NumberofReason . ');">';
print '<option value="select" selected="true">Select One</option>';

for ($i=0; $i < $NumberofReason; $i++){
	
	print '<option value="' . $reason_data[$i][0] . '">' . $reason_data[$i][1] . '</option>';
	
	
}

print '</select>';



?>
							</div>
							
							
<?php

for ($i=0; $i < $NumberofReason; $i++){
	
	$newvalue = $i + 1;
	
	print '<div class="col-md-6" id="noticeforReason_' . $newvalue . '" style="display:none"><label>Notice to CSR:</label>&nbsp;<a target="_blank" title="This field serves only as a notice."><img src="./assets/img/help-button-icon.png" height="15px"/></a>';
	print '<textarea type="text" style="height: 60px;" class="form-control2" value="" readonly>' . $reason_data[$i][2] . '</textarea>';
	print '</div>';
	
}


//print '<input style="height: 120px;" class="form-control2" value="' . $reason_data[$i][2] . '" readonly>';
//print '<textarea type="text" class="form-control2" value="" rows="3" readonly>' . $reason_data[$i][2] . '</textarea>';


?>
						
						</div>
					<hr />
					<!-- /. ROW  -->
						<div class="row">
							<div class="col-md-12">
								<label>Dispute Description</label>&nbsp;<a target="_blank" title="This field is for comments about the account(s). This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<textarea type="text" class="form-control" onblur="this.value = this.value.toUpperCase();" name="DisputeDescription" rows="2" maxlength="4096"></textarea>
							</div>
						
						</div>
						<hr />	
						
						<div class="row">
							<div class="col-md-3">
								<label>Merchant Name and Location:</label>&nbsp;<a target="_blank" title="This field is for merchant names. You may use alphabetic, numeric and some special characters. Ex.: 'Forever 21', 'Walmart', 'Trader Joe's'. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" onblur="this.value = this.value.toUpperCase();" name="MerchantName" maxlength="255">
							</div>
							<div class="col-md-3">
								<label>Has the Merchant been contacted?</label>&nbsp;<a target="_blank" title="Please select whether the merchant has been contacted by the customer or you. Please select the date of contact in the Contact Date field when it pops up."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<select id="marchantcontactstatusID" name="merchantcontactstatus" class="form-control" onchange="merchantChange();"> 
									<option value="select" selected="select">Select One</option>
									<option value="yes">Yes</option>
									<option value="no">No</option>
								</select> 
							</div>
							<div class="col-md-3" id="MerchantContactDayID" style="display:none">
								<label>Contact Date:</label>&nbsp;<a target="_blank" title="If the merchant has been contacted, please select approximately when they were contacted. This field is required when the merchant has been contacted."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<input type="text" class="form-control" id="MerchContactDate" name="MerchantContactDay" onFocus="showCalendarControl(this);">
							</div>
						</div>
						<hr />
						<div class="row">
							<div class="col-md-9">
								<label>Merchant Contact Description: (Please provide Steps taken with merchant to resolve dispute)</label>&nbsp;<a target="_blank" title="This field is for description/comments about the merchant. This is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<textarea class="form-control" onblur="this.value = this.value.toUpperCase();" name="merchantDescrption" rows="3" maxlength="4096"></textarea>
							</div>
						
					
						</div>
						<hr />	
						
					<!-- /. ROW  -->
					
						<div class="row">
							<div class="col-md-12">
								<label>Employee Comments:</label>&nbsp;<a target="_blank" title="This field is for Institutional comments. These comments are private and for our use only. This is an optional field."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
								<textarea class="form-control" onblur="this.value = this.value.toUpperCase();" name="transactionComments" rows="3" maxlength="4096"></textarea>
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
							
						</div>
						
			
						<hr />

						<input type="submit" class="btn btn-primary" name="newDisputeTransactionsSubmit" value="More Transactions">
						
						<!--  Delete Case -->
						<input type="submit" class="btn btn-danger" style="text-align: center; margin-left:37%" name="NewDisputeDelete" value="Delete Case" onclick="DeleteClicked='true';return confirm('Click OK to confirm deletion.')">
						
						
						<input type="submit" class="btn btn-success" style="width:145px; float:right" name="newDisputeTransactionsSubmitNext" value="Next"> <!-- onclick="validate()" --> 
						
						</form>
						
						<hr />
					
						
					
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

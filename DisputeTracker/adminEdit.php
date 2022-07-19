<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');



if ($accesslevel < 7){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}




if ($_SERVER["REQUEST_METHOD"] == "POST"){
	
	date_default_timezone_set("America/Chicago");
	$timeofChange = date("Y-m-d, G:i:s");
	
	$changedBool = FALSE;
	
	//Edit Rows Below
	
	//var_dump($_POST);
	//All Functions are similar. For comments check EditDispute.
	if(isset($_POST["EditDispute"])){
		
		//Get Arrays of Data from POST
		$DisputeIDPOST = $_POST["DisputeIDs"];
		$DisputeReasonPOST = $_POST["DisputeReasons"];
		$DisputeNoticePOST = $_POST["DisputeNotices"];
		
		//var_dump($DisputeIDPOST, $DisputeReasonPOST, $DisputeNoticePOST);
		
		//They should all be the same length
		$Count = count($DisputeReasonPOST);
		
		//Loop over each Row
		for($i=0;$i<$Count;$i++){
			
			//Check each string
			$stringGoodZERO = isAscii($DisputeIDPOST[$i]);
			$stringGoodONE = isAscii($DisputeReasonPOST[$i]);
			$stringGoodTWO = isAscii($DisputeNoticePOST[$i]);
			
			if (!$stringGoodZERO || !$stringGoodONE || !$stringGoodTWO){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Reason or Notice Text were Detected! Please try again using regular characters!';
				header( "Location: adminEdit.php" );
				exit();
			}
			
			//Escape each string
			$DisputeIDPOST[$i] = $dtcon->real_escape_string($DisputeIDPOST[$i]);
			$DisputeReasonPOST[$i] = $dtcon->real_escape_string($DisputeReasonPOST[$i]);
			$DisputeNoticePOST[$i] = $dtcon->real_escape_string($DisputeNoticePOST[$i]);
			
			//Update Strings. The VAR string has no ' so it can input into the database as a string
			$tmpUpdateString = "UPDATE disputereasons SET reason='".$DisputeReasonPOST[$i]."', noticetext='".$DisputeNoticePOST[$i]."' WHERE id='".$DisputeIDPOST[$i]."'";
			$tmpUpdateStringVAR = "UPDATE disputereasons SET reason=".$DisputeReasonPOST[$i].", noticetext=".$DisputeNoticePOST[$i]." WHERE id=".$DisputeIDPOST[$i]."";
			
			//Changelog ID
			$constantNum = 999900000;
			$tmpNum = $constantNum + $DisputeIDPOST[$i];
			
			$tmpChangeLogString = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '2', '".$tmpNum."', 'User Updated Content from disputereasons --- New Content: ".$tmpUpdateStringVAR."')";
			
			//Check to make sure something changed
			$tmp_edit_query = "SELECT * FROM disputereasons WHERE id='".$DisputeIDPOST[$i]."'";
			$tmp_edit_query_data = $dtcon->query($tmp_edit_query);
			$tmp_edit_data = $tmp_edit_query_data->fetch_all();
			
			$editOldContent = array();
			$editNewContent = array();
			
			//Setup Arrays for the check function.
			array_push($editOldContent, $tmp_edit_data[0][0]);
			array_push($editOldContent, $dtcon->real_escape_string($tmp_edit_data[0][1]));
			array_push($editOldContent, $dtcon->real_escape_string($tmp_edit_data[0][2]));
			
			array_push($editNewContent, $DisputeIDPOST[$i]);
			array_push($editNewContent, $DisputeReasonPOST[$i]);
			array_push($editNewContent, $DisputeNoticePOST[$i]);
			
			$tmp_post_bool = editContentCheck($editOldContent, $editNewContent);
			
			//If something changed, log it.
			if($tmp_post_bool){
				
				//var_dump($tmp_edit_data, $editOldContent, $editNewContent, $tmp_post_bool);
				//exit();
				
				if ($dtcon->query($tmpChangeLogString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				if ($dtcon->query($tmpUpdateString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Edit Update! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				$changedBool = TRUE;
			}
			else{
				//No Change
			}
		}
		
		if($changedBool){
			$_SESSION["EditSuccess"] = "Dispute Reason Edited Successfully.";
		}
		else{
			$_SESSION["EditSuccess"] = "No Change Detected. If you think this is an error, please contact the System Admin.";
		}
		
	}
	else if(isset($_POST["EditAccount"])){
		
		$AccountIDPOST = $_POST["AccountIDs"];
		$AccountTypePOST = $_POST["AccountType"];
		
		//var_dump($AccountIDPOST, $AccountTypePOST);
		
		
		$Count = count($AccountIDPOST);
		
		for($i=0;$i<$Count;$i++){
			
			$stringGoodONE = isAscii($AccountIDPOST[$i]);
			$stringGoodTWO = isAscii($AccountTypePOST[$i]);
			
			if (!$stringGoodONE || !$stringGoodTWO){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Type Text were Detected! Please try again using regular characters!';
				header( "Location: adminEdit.php" );
				exit();
			}
			
			$AccountIDPOST[$i] = $dtcon->real_escape_string($AccountIDPOST[$i]);
			$AccountTypePOST[$i] = $dtcon->real_escape_string($AccountTypePOST[$i]);
			
			$tmpUpdateString = "UPDATE accounttype SET typetext='".$AccountTypePOST[$i]."' WHERE id='".$AccountIDPOST[$i]."'";
			$tmpUpdateStringVAR = "UPDATE accounttype SET typetext=".$AccountTypePOST[$i]." WHERE id=".$AccountIDPOST[$i]."";
			
			$constantNum = 998800000;
			$tmpNum = $constantNum + $AccountIDPOST[$i];
			
			$tmpChangeLogString = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '2', '".$tmpNum."', 'User Updated Content from accounttype --- New Content: ".$tmpUpdateStringVAR."')";
			
			
			
			$tmp_edit_query = "SELECT * FROM accounttype WHERE id='".$AccountIDPOST[$i]."'";
			$tmp_edit_query_data = $dtcon->query($tmp_edit_query);
			$tmp_edit_data = $tmp_edit_query_data->fetch_all();
			
			$editOldContent = array();
			$editNewContent = array();
			
			array_push($editOldContent, $tmp_edit_data[0][0]);
			array_push($editOldContent, $dtcon->real_escape_string($tmp_edit_data[0][1]));
			
			array_push($editNewContent, $AccountIDPOST[$i]);
			array_push($editNewContent, $AccountTypePOST[$i]);
			
			$tmp_post_bool = editContentCheck($editOldContent, $editNewContent);
			
			if($tmp_post_bool){
				
				if ($dtcon->query($tmpChangeLogString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				if ($dtcon->query($tmpUpdateString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Edit Update! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				$changedBool = TRUE;
			}
			else{
				//No Change
			}
		}
		
		if($changedBool){
			$_SESSION["EditSuccess"] = "Account Type Edited Successfully.";
		}
		else{
			$_SESSION["EditSuccess"] = "No Change Detected. If you think this is an error, please contact the System Admin.";
		}
		
	}
	else if(isset($_POST["EditCompromise"])){
		
		$CompromiseIDPOST = $_POST["CompromiseIDs"];
		$CompAlertPOST = $_POST["CompAlert"];
		$CompMerchPOST = $_POST["CompMerch"];
		$CompActivationPOST = $_POST["CompActivation"];
		$CompStartDatePOST = $_POST["CompStartDate"];
		$CompEndDatePOST = $_POST["CompEndDate"];
		$CompDescriptionPOST = $_POST["CompDescription"];
		
		//var_dump($CompromiseIDPOST, $CompAlertPOST, $CompMerchPOST, $CompActivationPOST, $CompStartDatePOST, $CompEndDatePOST, $CompDescriptionPOST);
		
		
		$Count = count($CompromiseIDPOST);
		
		for($i=0;$i<$Count;$i++){
			
			$stringGoodONE = isAscii($CompromiseIDPOST[$i]);
			$stringGoodTWO = isAscii($CompAlertPOST[$i]);
			$stringGoodTHR = isAscii($CompMerchPOST[$i]);
			$stringGoodFOU = isAscii($CompActivationPOST[$i]);
			$stringGoodFIV = isAscii($CompStartDatePOST[$i]);
			$stringGoodSIX = isAscii($CompEndDatePOST[$i]);
			$stringGoodSEV = isAscii($CompDescriptionPOST[$i]);
			
			if (!$stringGoodONE || !$stringGoodTWO || !$stringGoodTHR || !$stringGoodFOU || !$stringGoodFIV || !$stringGoodSIX || !$stringGoodSEV){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters were Detected! Please try again using regular characters!';
				header( "Location: adminEdit.php" );
				exit();
			}
			
			$CompromiseIDPOST[$i] = $dtcon->real_escape_string($CompromiseIDPOST[$i]);
			$CompAlertPOST[$i] = $dtcon->real_escape_string($CompAlertPOST[$i]);
			$CompMerchPOST[$i] = $dtcon->real_escape_string($CompMerchPOST[$i]);
			$CompActivationPOST[$i] = $dtcon->real_escape_string($CompActivationPOST[$i]);
			$CompStartDatePOST[$i] = $dtcon->real_escape_string($CompStartDatePOST[$i]);
			$CompEndDatePOST[$i] = $dtcon->real_escape_string($CompEndDatePOST[$i]);
			$CompDescriptionPOST[$i] = $dtcon->real_escape_string($CompDescriptionPOST[$i]);
			
			$tmpUpdateString = "UPDATE compromise SET alertnum='".$CompAlertPOST[$i]."', merchantid='".$CompMerchPOST[$i]."', activationdate='".$CompActivationPOST[$i]."', startdate='".$CompStartDatePOST[$i]."', enddate='".$CompEndDatePOST[$i]."', description='".$CompDescriptionPOST[$i]."' WHERE id='".$CompromiseIDPOST[$i]."'";
			$tmpUpdateStringVAR = "UPDATE compromise SET alertnum=".$CompAlertPOST[$i].", merchantid=".$CompMerchPOST[$i].", activationdate=".$CompActivationPOST[$i].", startdate=".$CompStartDatePOST[$i].", enddate=".$CompEndDatePOST[$i].", description=".$CompDescriptionPOST[$i]." WHERE id=".$CompromiseIDPOST[$i]."";
			
			$constantNum = 997700000;
			$tmpNum = $constantNum + $CompromiseIDPOST[$i];
			
			$tmpChangeLogString = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '2', '".$tmpNum."', 'User Updated Content from compromise --- New Content: ".$tmpUpdateStringVAR."')";
			
			
			$tmp_edit_query = "SELECT * FROM compromise WHERE id='".$CompromiseIDPOST[$i]."'";
			$tmp_edit_query_data = $dtcon->query($tmp_edit_query);
			$tmp_edit_data = $tmp_edit_query_data->fetch_all();
			
			$editOldContent = array();
			$editNewContent = array();
			
			array_push($editOldContent, $tmp_edit_data[0][0]);
			array_push($editOldContent, $dtcon->real_escape_string($tmp_edit_data[0][1]));
			array_push($editOldContent, $dtcon->real_escape_string($tmp_edit_data[0][2]));
			array_push($editOldContent, $dtcon->real_escape_string($tmp_edit_data[0][3]));
			array_push($editOldContent, $dtcon->real_escape_string($tmp_edit_data[0][4]));
			array_push($editOldContent, $dtcon->real_escape_string($tmp_edit_data[0][5]));
			array_push($editOldContent, $dtcon->real_escape_string($tmp_edit_data[0][6]));
			
			array_push($editNewContent, $CompromiseIDPOST[$i]);
			array_push($editNewContent, $CompAlertPOST[$i]);
			array_push($editNewContent, $CompMerchPOST[$i]);
			array_push($editNewContent, $CompActivationPOST[$i]);
			array_push($editNewContent, $CompStartDatePOST[$i]);
			array_push($editNewContent, $CompEndDatePOST[$i]);
			array_push($editNewContent, $CompDescriptionPOST[$i]);
			
			$tmp_post_bool = editContentCheck($editOldContent, $editNewContent);
			
			if($tmp_post_bool){
				
				if ($dtcon->query($tmpChangeLogString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				if ($dtcon->query($tmpUpdateString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Edit Update! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				$changedBool = TRUE;
			}
			else{
				//No Change
			}
		}
		
		if($changedBool){
			$_SESSION["EditSuccess"] = "Compromise Edited Successfully.";
		}
		else{
			$_SESSION["EditSuccess"] = "No Change Detected. If you think this is an error, please contact the System Admin.";
		}
		
	}
	else if(isset($_POST["EditCardType"])){
		
		$CardTypeIDPOST = $_POST["CardTypeIDs"];
		$CardTypePOST = $_POST["EditCardTypes"];
		
		//var_dump($CardTypeIDPOST, $CardTypePOST);
		
		
		$Count = count($CardTypeIDPOST);
		
		for($i=0;$i<$Count;$i++){
			
			$stringGoodONE = isAscii($CardTypeIDPOST[$i]);
			$stringGoodTWO = isAscii($CardTypePOST[$i]);
			
			if (!$stringGoodONE || !$stringGoodTWO){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Type Text were Detected! Please try again using regular characters!';
				header( "Location: adminEdit.php" );
				exit();
			}
			
			$CardTypeIDPOST[$i] = $dtcon->real_escape_string($CardTypeIDPOST[$i]);
			$CardTypePOST[$i] = $dtcon->real_escape_string($CardTypePOST[$i]);
			
			$tmpUpdateString = "UPDATE cardtype SET typetext='".$CardTypePOST[$i]."' WHERE id='".$CardTypeIDPOST[$i]."'";
			$tmpUpdateStringVAR = "UPDATE cardtype SET typetext=".$CardTypePOST[$i]." WHERE id=".$CardTypeIDPOST[$i]."";
			
			$constantNum = 996600000;
			$tmpNum = $constantNum + $CardTypeIDPOST[$i];
			
			$tmpChangeLogString = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '2', '".$tmpNum."', 'User Updated Content from cardtype --- New Content: ".$tmpUpdateStringVAR."')";
			
			
			
			$tmp_edit_query = "SELECT * FROM cardtype WHERE id='".$CardTypeIDPOST[$i]."'";
			$tmp_edit_query_data = $dtcon->query($tmp_edit_query);
			$tmp_edit_data = $tmp_edit_query_data->fetch_all();
			
			$editOldContent = array();
			$editNewContent = array();
			
			array_push($editOldContent, $tmp_edit_data[0][0]);
			array_push($editOldContent, $dtcon->real_escape_string($tmp_edit_data[0][1]));
			
			array_push($editNewContent, $CardTypeIDPOST[$i]);
			array_push($editNewContent, $CardTypePOST[$i]);
			
			$tmp_post_bool = editContentCheck($editOldContent, $editNewContent);
			
			if($tmp_post_bool){
				
				if ($dtcon->query($tmpChangeLogString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				if ($dtcon->query($tmpUpdateString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Edit Update! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				$changedBool = TRUE;
			}
			else{
				//No Change
			}
		}
		
		if($changedBool){
			$_SESSION["EditSuccess"] = "Card Type Edited Successfully.";
		}
		else{
			$_SESSION["EditSuccess"] = "No Change Detected. If you think this is an error, please contact the System Admin.";
		}
		
	}
	else if(isset($_POST["EditCardStatus"])){
		
		$CardStatusIDPOST = $_POST["CardStatusIDs"];
		$CardStatusPOST = $_POST["EditCardStatuses"];
		
		//var_dump($CardStatusIDPOST, $CardStatusPOST);
		
		$Count = count($CardStatusIDPOST);
		
		for($i=0;$i<$Count;$i++){
			
			$stringGoodONE = isAscii($CardStatusIDPOST[$i]);
			$stringGoodTWO = isAscii($CardStatusPOST[$i]);
			
			if (!$stringGoodONE || !$stringGoodTWO){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Status Text were Detected! Please try again using regular characters!';
				header( "Location: adminEdit.php" );
				exit();
			}
			
			$CardStatusIDPOST[$i] = $dtcon->real_escape_string($CardStatusIDPOST[$i]);
			$CardStatusPOST[$i] = $dtcon->real_escape_string($CardStatusPOST[$i]);
			
			$tmpUpdateString = "UPDATE cardstatus SET statustext='".$CardStatusPOST[$i]."' WHERE id='".$CardStatusIDPOST[$i]."'";
			$tmpUpdateStringVAR = "UPDATE cardstatus SET statustext=".$CardStatusPOST[$i]." WHERE id=".$CardStatusIDPOST[$i]."";
			
			$constantNum = 995500000;
			$tmpNum = $constantNum + $CardStatusIDPOST[$i];
			
			$tmpChangeLogString = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '2', '".$tmpNum."', 'User Updated Content from cardstatus --- New Content: ".$tmpUpdateStringVAR."')";
			
			
			$tmp_edit_query = "SELECT * FROM cardstatus WHERE id='".$CardStatusIDPOST[$i]."'";
			$tmp_edit_query_data = $dtcon->query($tmp_edit_query);
			$tmp_edit_data = $tmp_edit_query_data->fetch_all();
			
			$editOldContent = array();
			$editNewContent = array();
			
			array_push($editOldContent, $tmp_edit_data[0][0]);
			array_push($editOldContent, $dtcon->real_escape_string($tmp_edit_data[0][1]));
			
			array_push($editNewContent, $CardStatusIDPOST[$i]);
			array_push($editNewContent, $CardStatusPOST[$i]);
			
			$tmp_post_bool = editContentCheck($editOldContent, $editNewContent);
			
			if($tmp_post_bool){
				
				if ($dtcon->query($tmpChangeLogString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				if ($dtcon->query($tmpUpdateString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Edit Update! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				$changedBool = TRUE;
			}
			else{
				//No Change
			}
		}
		
		if($changedBool){
			$_SESSION["EditSuccess"] = "Card Status Edited Successfully.";
		}
		else{
			$_SESSION["EditSuccess"] = "No Change Detected. If you think this is an error, please contact the System Admin.";
		}
		
	}
	else if(isset($_POST["EditCardPoss"])){
		
		$CardPossessionIDPOST = $_POST["CardPossessionIDs"];
		$CardPossessionPOST = $_POST["CardPossessions"];
		
		//var_dump($CardPossessionIDPOST, $CardPossessionPOST);
		
		$Count = count($CardPossessionIDPOST);
		
		for($i=0;$i<$Count;$i++){
			
			$stringGoodONE = isAscii($CardPossessionIDPOST[$i]);
			$stringGoodTWO = isAscii($CardPossessionPOST[$i]);
			
			if (!$stringGoodONE || !$stringGoodTWO){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Possession Text were Detected! Please try again using regular characters!';
				header( "Location: adminEdit.php" );
				exit();
			}
			
			$CardPossessionIDPOST[$i] = $dtcon->real_escape_string($CardPossessionIDPOST[$i]);
			$CardPossessionPOST[$i] = $dtcon->real_escape_string($CardPossessionPOST[$i]);
			
			$tmpUpdateString = "UPDATE cardpossession SET possessiontext='".$CardPossessionPOST[$i]."' WHERE id='".$CardPossessionIDPOST[$i]."'";
			$tmpUpdateStringVAR = "UPDATE cardpossession SET possessiontext=".$CardPossessionPOST[$i]." WHERE id=".$CardPossessionIDPOST[$i]."";
			
			$constantNum = 994400000;
			$tmpNum = $constantNum + $CardPossessionIDPOST[$i];
			
			$tmpChangeLogString = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '2', '".$tmpNum."', 'User Updated Content from cardpossession --- New Content: ".$tmpUpdateStringVAR."')";
			
			
			$tmp_edit_query = "SELECT * FROM cardpossession WHERE id='".$CardPossessionIDPOST[$i]."'";
			$tmp_edit_query_data = $dtcon->query($tmp_edit_query);
			$tmp_edit_data = $tmp_edit_query_data->fetch_all();
			
			$editOldContent = array();
			$editNewContent = array();
			
			array_push($editOldContent, $tmp_edit_data[0][0]);
			array_push($editOldContent, $dtcon->real_escape_string($tmp_edit_data[0][1]));
			
			array_push($editNewContent, $CardPossessionIDPOST[$i]);
			array_push($editNewContent, $CardPossessionPOST[$i]);
			
			$tmp_post_bool = editContentCheck($editOldContent, $editNewContent);
			
			if($tmp_post_bool){
				
				if ($dtcon->query($tmpChangeLogString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				if ($dtcon->query($tmpUpdateString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Edit Update! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				$changedBool = TRUE;
			}
			else{
				//No Change
			}
		}
		
		if($changedBool){
			$_SESSION["EditSuccess"] = "Card Possession Edited Successfully.";
		}
		else{
			$_SESSION["EditSuccess"] = "No Change Detected. If you think this is an error, please contact the System Admin.";
		}
		
	}
	else if(isset($_POST["EditReversal"])){
		
		$ReversalIDPOST = $_POST["ReversalErrorIDs"];
		$ReversalPOST = $_POST["EditReversalErrors"];
		
		//var_dump($ReversalIDPOST, $ReversalPOST);
		
		$Count = count($ReversalIDPOST);
		
		for($i=0;$i<$Count;$i++){
			
			$stringGoodONE = isAscii($ReversalIDPOST[$i]);
			$stringGoodTWO = isAscii($ReversalPOST[$i]);
			
			if (!$stringGoodONE || !$stringGoodTWO){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Reversal Error Text were Detected! Please try again using regular characters!';
				header( "Location: adminEdit.php" );
				exit();
			}
			
			$ReversalIDPOST[$i] = $dtcon->real_escape_string($ReversalIDPOST[$i]);
			$ReversalPOST[$i] = $dtcon->real_escape_string($ReversalPOST[$i]);
			
			$tmpUpdateString = "UPDATE reversalerrors SET reversalerrortext='".$ReversalPOST[$i]."' WHERE id='".$ReversalIDPOST[$i]."'";
			$tmpUpdateStringVAR = "UPDATE reversalerrors SET reversalerrortext=".$ReversalPOST[$i]." WHERE id=".$ReversalIDPOST[$i]."";
			
			$constantNum = 993300000;
			$tmpNum = $constantNum + $ReversalIDPOST[$i];
			
			$tmpChangeLogString = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '2', '".$tmpNum."', 'User Updated Content from reversalerrors --- New Content: ".$tmpUpdateStringVAR."')";
			
			
			$tmp_edit_query = "SELECT * FROM reversalerrors WHERE id='".$ReversalIDPOST[$i]."'";
			$tmp_edit_query_data = $dtcon->query($tmp_edit_query);
			$tmp_edit_data = $tmp_edit_query_data->fetch_all();
			
			$editOldContent = array();
			$editNewContent = array();
			
			array_push($editOldContent, $tmp_edit_data[0][0]);
			array_push($editOldContent, $dtcon->real_escape_string($tmp_edit_data[0][1]));
			
			array_push($editNewContent, $ReversalIDPOST[$i]);
			array_push($editNewContent, $ReversalPOST[$i]);
			
			$tmp_post_bool = editContentCheck($editOldContent, $editNewContent);
			
			if($tmp_post_bool){
				
				if ($dtcon->query($tmpChangeLogString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				if ($dtcon->query($tmpUpdateString) === TRUE) {
					
					//print "Insert Worked";
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Edit Update! Please Contact the System Admin!';
					header( "Location: addUser.php" );
					exit();
				}
				
				$changedBool = TRUE;
			}
			else{
				//No Change
			}
		}
		
		if($changedBool){
			$_SESSION["EditSuccess"] = "Reversal Error Edited Successfully.";
		}
		else{
			$_SESSION["EditSuccess"] = "No Change Detected. If you think this is an error, please contact the System Admin.";
		}
		
	}	
	
	//Delete Rows Below
	//All Functions are similar. For comments check DeleteDispute.
	else if(isset($_POST["DeleteDispute"])){
		
		//Get Data ID from POST
		$DeleteDisputeID = $_POST["DeleteDispute"];
		
		//var_dump($DeleteDisputeID);
		
		//Setup 'Delete'/Update String
		$DeleteString = "UPDATE disputereasons SET iddeleted = TRUE WHERE id='".$DeleteDisputeID."'"; 
		$tmpString = "UPDATE disputereasons SET iddeleted = TRUE WHERE id=".$DeleteDisputeID.""; 
		
		//Changelog ID
		$constantNum = 999900000;
		$tmpNum = $constantNum + $DeleteDisputeID;
		
		$userChangeLogQuery = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '3', '".$tmpNum."', 'User deleted disputereasons ID number: ".$DeleteDisputeID." --- Command: ".$tmpString."')";
		
		//var_dump($userChangeLogQuery, $DeleteString);
		//exit();
		
		if ($dtcon->query($userChangeLogQuery) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
			
		}
		
		if ($dtcon->query($DeleteString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Deletion! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$_SESSION["EditSuccess"] = "Dispute Reason Deletion Successful.";
		
	}
	else if(isset($_POST["DeleteAccount"])){
		
		$DeleteDisputeID = $_POST["DeleteAccount"];
		
		//var_dump($DeleteDisputeID);
		
		$DeleteString = "UPDATE accounttype SET iddeleted = TRUE WHERE id='".$DeleteDisputeID."'"; 
		$tmpString = "UPDATE accounttype SET iddeleted = TRUE WHERE id=".$DeleteDisputeID.""; 
		
		$constantNum = 998800000;
		$tmpNum = $constantNum + $DeleteDisputeID;
		
		$userChangeLogQuery = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '3', '".$tmpNum."', 'User deleted disputereasons ID number: ".$DeleteDisputeID." --- Command: ".$tmpString."')";
		
		if ($dtcon->query($userChangeLogQuery) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
			
		}
		
		if ($dtcon->query($DeleteString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Deletion! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$_SESSION["EditSuccess"] = "Account Type Deletion Successful.";
		
	}
	else if(isset($_POST["DeleteCompromise"])){
		
		$DeleteDisputeID = $_POST["DeleteCompromise"];
		
		if($DeleteDisputeID == 0){
			
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Default Compromise Text can\'t be deleted. Please contact the System Admin for help.';
			header( "Location: adminEdit.php" );
			exit();
			
		}
		
		//var_dump($DeleteDisputeID);
		
		$DeleteString = "UPDATE compromise SET iddeleted = TRUE WHERE id='".$DeleteDisputeID."'"; 
		$tmpString = "UPDATE compromise SET iddeleted = TRUE WHERE id=".$DeleteDisputeID.""; 
		
		$constantNum = 997700000;
		$tmpNum = $constantNum + $DeleteDisputeID;
		
		$userChangeLogQuery = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '3', '".$tmpNum."', 'User deleted disputereasons ID number: ".$DeleteDisputeID." --- Command: ".$tmpString."')";
		
		if ($dtcon->query($userChangeLogQuery) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
			
		}
		
		if ($dtcon->query($DeleteString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Deletion! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$_SESSION["EditSuccess"] = "Compromise Deletion Successful.";
		
	}
	else if(isset($_POST["DeleteCardType"])){
		
		$DeleteDisputeID = $_POST["DeleteCardType"];
		
		//var_dump($DeleteDisputeID);
		
		$DeleteString = "UPDATE cardtype SET iddeleted = TRUE WHERE id='".$DeleteDisputeID."'"; 
		$tmpString = "UPDATE cardtype SET iddeleted = TRUE WHERE id=".$DeleteDisputeID.""; 
		
		$constantNum = 996600000;
		$tmpNum = $constantNum + $DeleteDisputeID;
		
		$userChangeLogQuery = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '3', '".$tmpNum."', 'User deleted disputereasons ID number: ".$DeleteDisputeID." --- Command: ".$tmpString."')";
		
		if ($dtcon->query($userChangeLogQuery) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
			
		}
		
		if ($dtcon->query($DeleteString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Deletion! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$_SESSION["EditSuccess"] = "Card Type Deletion Successful.";
		
	}
	else if(isset($_POST["DeleteCardStatus"])){
		
		$DeleteDisputeID = $_POST["DeleteCardStatus"];
		
		//var_dump($DeleteDisputeID);
		
		$DeleteString = "UPDATE cardstatus SET iddeleted = TRUE WHERE id='".$DeleteDisputeID."'"; 
		$tmpString = "UPDATE cardstatus SET iddeleted = TRUE WHERE id=".$DeleteDisputeID.""; 
		
		$constantNum = 995500000;
		$tmpNum = $constantNum + $DeleteDisputeID;
		
		$userChangeLogQuery = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '3', '".$tmpNum."', 'User deleted disputereasons ID number: ".$DeleteDisputeID." --- Command: ".$tmpString."')";
		
		if ($dtcon->query($userChangeLogQuery) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
			
		}
		
		if ($dtcon->query($DeleteString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Deletion! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$_SESSION["EditSuccess"] = "Card Status Deletion Successful.";
		
	}
	else if(isset($_POST["DeleteCardPoss"])){
		
		$DeleteDisputeID = $_POST["DeleteCardPoss"];
		
		//var_dump($DeleteDisputeID);
		
		$DeleteString = "UPDATE cardpossession SET iddeleted = TRUE WHERE id='".$DeleteDisputeID."'"; 
		$tmpString = "UPDATE cardpossession SET iddeleted = TRUE WHERE id=".$DeleteDisputeID.""; 
		
		$constantNum = 994400000;
		$tmpNum = $constantNum + $DeleteDisputeID;
		
		$userChangeLogQuery = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '3', '".$tmpNum."', 'User deleted disputereasons ID number: ".$DeleteDisputeID." --- Command: ".$tmpString."')";
		
		if ($dtcon->query($userChangeLogQuery) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
			
		}
		
		if ($dtcon->query($DeleteString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Deletion! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$_SESSION["EditSuccess"] = "Card Possession Deletion Successful.";
		
	}
	else if(isset($_POST["DeleteReversal"])){
		
		$DeleteDisputeID = $_POST["DeleteReversal"];
		
		if($DeleteDisputeID == 0){
			
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Default Reversal Text can\'t be deleted. Please contact the System Admin for help.';
			header( "Location: adminEdit.php" );
			exit();
			
		}
		
		//var_dump($DeleteDisputeID);
		
		$DeleteString = "UPDATE reversalerrors SET iddeleted = TRUE WHERE id='".$DeleteDisputeID."'"; 
		$tmpString = "UPDATE reversalerrors SET iddeleted = TRUE WHERE id=".$DeleteDisputeID.""; 
		
		$constantNum = 993300000;
		$tmpNum = $constantNum + $DeleteDisputeID;
		
		$userChangeLogQuery = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '3', '".$tmpNum."', 'User deleted disputereasons ID number: ".$DeleteDisputeID." --- Command: ".$tmpString."')";
		
		if ($dtcon->query($userChangeLogQuery) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
			
		}
		
		if ($dtcon->query($DeleteString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Deletion! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$_SESSION["EditSuccess"] = "Reversal Error Deletion Successful.";
		
	}
	
	//Add New Rows Below
	//All Functions are similar. For comments check AddDispute.
	else if(isset($_POST["AddDispute"])){
		
		//Get Data from POST
		$DisputeReason = $_POST["AddDisputeReason"];
		$DisputeNotice = $_POST["AddDisputeNoticeText"];
		
		//Check Data
		if(!isset($_POST["AddDisputeReason"]) || $DisputeReason == "" || !isset($_POST["AddDisputeNoticeText"]) || $DisputeNotice == ""){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Values can\'t be empty.';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$stringGoodONE = isAscii($DisputeReason);
		$stringGoodTWO = isAscii($DisputeNotice);
		
		if (!$stringGoodONE || !$stringGoodTWO){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Reason or Notice were Detected! Please try again using regular characters!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$DisputeReason = $dtcon->real_escape_string($DisputeReason);
		$DisputeNotice = $dtcon->real_escape_string($DisputeNotice);
		
		$InputString = "INSERT INTO disputereasons (id, reason, noticetext, iddeleted) VALUES (NULL, '".$DisputeReason."', '".$DisputeNotice."', FALSE)";
		$tmpString = "INSERT INTO disputereasons (id, reason, noticetext, iddeleted) VALUES (NULL, ".$DisputeReason.", ".$DisputeNotice.", FALSE)";
		
		
		if ($dtcon->query($InputString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Dispute Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$newAddID = $dtcon->insert_id;
		
		$constantNum = 999900000;
		$newAddID = $constantNum + $newAddID;
		
		$userChangeInsert = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '1', '".$newAddID."', 'disputereasons Sequence: ".$tmpString."')";
		
		if ($dtcon->query($userChangeInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}			
		
		$_SESSION["EditSuccess"] = "Dispute Reason Addition Successful.";
		
	}	
	else if(isset($_POST["AddAccount"])){
		
		$AccountTypeText = $_POST["AddAccountText"];
		
		if(!isset($_POST["AddAccountText"]) || $AccountTypeText == ""){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Values can\'t be empty.';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$stringGoodONE = isAscii($AccountTypeText);
		
		if (!$stringGoodONE){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Type Text were Detected! Please try again using regular characters!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$AccountTypeText = $dtcon->real_escape_string($AccountTypeText);
		
		$InputString = "INSERT INTO accounttype (id, typetext, iddeleted) VALUES (NULL, '".$AccountTypeText."', FALSE)";
		$tmpString = "INSERT INTO accounttype (id, typetext, iddeleted) VALUES (NULL, ".$AccountTypeText.", FALSE)";
		
		
		if ($dtcon->query($InputString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Account Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$newAddID = $dtcon->insert_id;
		
		$constantNum = 998800000;
		$newAddID = $constantNum + $newAddID;
		
		$userChangeInsert = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '1', '".$newAddID."', 'accounttype Sequence: ".$tmpString."')";
		
		if ($dtcon->query($userChangeInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}			
		
		$_SESSION["EditSuccess"] = "Account Type Addition Successful.";
		
	}
	else if(isset($_POST["AddCompromise"])){
		
		$CompAlert = $_POST["AddCompAlertNum"];
		$CompMerch = $_POST["AddCompMerch"];
		$CompActivation = $_POST["AddCompActDate"];
		$CompStart = $_POST["AddCompStartDate"];
		$CompEnd = $_POST["AddCompEndDate"];
		$CompDescription = $_POST["AddCompDescrip"];
		
		if(!isset($_POST["AddCompAlertNum"]) || $CompAlert == "" || !isset($_POST["AddCompMerch"]) || $CompMerch == "" || !isset($_POST["AddCompActDate"]) || $CompActivation == "" || !isset($_POST["AddCompStartDate"]) || $CompStart == "" || !isset($_POST["AddCompEndDate"]) || $CompEnd == "" || !isset($_POST["AddCompDescrip"]) || $CompDescription == ""){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Values can\'t be empty.';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$stringGoodONE = isAscii($CompAlert);
		$stringGoodTWO = isAscii($CompMerch);
		$stringGoodTHR = isAscii($CompActivation);
		$stringGoodFOU = isAscii($CompStart);
		$stringGoodFIV = isAscii($CompEnd);
		$stringGoodSIX = isAscii($CompDescription);
		
		if (!$stringGoodONE || !$stringGoodTWO || !$stringGoodTHR || !$stringGoodFOU || !$stringGoodFIV || !$stringGoodSIX){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters were Detected! Please try again using regular characters!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$CompAlert = $dtcon->real_escape_string($CompAlert);
		$CompMerch = $dtcon->real_escape_string($CompMerch);
		$CompActivation = $dtcon->real_escape_string($CompActivation);
		$CompStart = $dtcon->real_escape_string($CompStart);
		$CompEnd = $dtcon->real_escape_string($CompEnd);
		$CompDescription = $dtcon->real_escape_string($CompDescription);
		
		$InputString = "INSERT INTO compromise (id, alertnum, merchantid, activationdate, startdate, enddate, description, iddeleted) VALUES (NULL, '".$CompAlert."', '".$CompMerch."', '".$CompActivation."', '".$CompStart."', '".$CompEnd."', '".$CompDescription."', FALSE)";
		$tmpString = "INSERT INTO compromise (id, alertnum, merchantid, activationdate, startdate, enddate, description, iddeleted) VALUES (NULL, ".$CompAlert.", ".$CompMerch.", ".$CompActivation.", ".$CompStart.", ".$CompEnd.", ".$CompDescription.", FALSE)";
		
		
		if ($dtcon->query($InputString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Compromise Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$newAddID = $dtcon->insert_id;
		
		$constantNum = 997700000;
		$newAddID = $constantNum + $newAddID;
		
		$userChangeInsert = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '1', '".$newAddID."', 'compromise Sequence: ".$tmpString."')";
		
		if ($dtcon->query($userChangeInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}			
		
		$_SESSION["EditSuccess"] = "Compromise Addition Successful.";
		
	}
	else if(isset($_POST["AddCardType"])){
		
		$CardTypeText = $_POST["AddCardTypeText"];
		
		if(!isset($_POST["AddCardTypeText"]) || $CardTypeText == ""){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Values can\'t be empty.';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$stringGoodONE = isAscii($CardTypeText);
		
		if (!$stringGoodONE){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Type Text were Detected! Please try again using regular characters!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$CardTypeText = $dtcon->real_escape_string($CardTypeText);
		
		$InputString = "INSERT INTO cardtype (id, typetext, iddeleted) VALUES (NULL, '".$CardTypeText."', FALSE)";
		$tmpString = "INSERT INTO cardtype (id, typetext, iddeleted) VALUES (NULL, ".$CardTypeText.", FALSE)";
		
		
		if ($dtcon->query($InputString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Card Type Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$newAddID = $dtcon->insert_id;
		
		$constantNum = 996600000;
		$newAddID = $constantNum + $newAddID;
		
		$userChangeInsert = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '1', '".$newAddID."', 'cardtype Sequence: ".$tmpString."')";
		
		if ($dtcon->query($userChangeInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}		
		
		$_SESSION["EditSuccess"] = "Card Type Addition Successful.";
		
	}
	else if(isset($_POST["AddCardStatus"])){
		
		$CardStatusText = $_POST["AddCardStatusText"];
		
		if(!isset($_POST["AddCardStatusText"]) || $CardStatusText == ""){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Values can\'t be empty.';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$stringGoodONE = isAscii($CardStatusText);
		
		if (!$stringGoodONE){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Status Text were Detected! Please try again using regular characters!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$CardStatusText = $dtcon->real_escape_string($CardStatusText);
		
		$InputString = "INSERT INTO cardstatus (id, statustext, iddeleted) VALUES (NULL, '".$CardStatusText."', FALSE)";
		$tmpString = "INSERT INTO cardstatus (id, statustext, iddeleted) VALUES (NULL, ".$CardStatusText.", FALSE)";
		
		
		if ($dtcon->query($InputString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Card Status Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$newAddID = $dtcon->insert_id;
		
		$constantNum = 995500000;
		$newAddID = $constantNum + $newAddID;
		
		$userChangeInsert = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '1', '".$newAddID."', 'cardstatus Sequence: ".$tmpString."')";
		
		if ($dtcon->query($userChangeInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$_SESSION["EditSuccess"] = "Card Status Addition Successful.";
		
	}
	else if(isset($_POST["AddCardPoss"])){
		
		$CardPossessionText = $_POST["AddCardPossText"];
		
		if(!isset($_POST["AddCardPossText"]) || $CardPossessionText == ""){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Values can\'t be empty.';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$stringGoodONE = isAscii($CardPossessionText);
		
		if (!$stringGoodONE){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Card Possession Text were Detected! Please try again using regular characters!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$CardPossessionText = $dtcon->real_escape_string($CardPossessionText);
		
		$InputString = "INSERT INTO cardpossession (id, possessiontext, iddeleted) VALUES (NULL, '".$CardPossessionText."', FALSE)";
		$tmpString = "INSERT INTO cardpossession (id, possessiontext, iddeleted) VALUES (NULL, ".$CardPossessionText.", FALSE)";
		
		
		if ($dtcon->query($InputString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Card Possession Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$newAddID = $dtcon->insert_id;
		
		$constantNum = 994400000;
		$newAddID = $constantNum + $newAddID;
		
		$userChangeInsert = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '1', '".$newAddID."', 'cardpossession Sequence: ".$tmpString."')";
		
		if ($dtcon->query($userChangeInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$_SESSION["EditSuccess"] = "Card Possession Addition Successful.";
		
	}
	else if(isset($_POST["AddReversal"])){
		
		$CardReversalText = $_POST["AddReversalText"];
		
		if(!isset($_POST["AddReversalText"]) || $CardReversalText == ""){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Values can\'t be empty.';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$stringGoodONE = isAscii($CardReversalText);
		
		if (!$stringGoodONE){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Reversal Text were Detected! Please try again using regular characters!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$CardReversalText = $dtcon->real_escape_string($CardReversalText);
		
		$InputString = "INSERT INTO reversalerrors (id, reversalerrortext, iddeleted) VALUES (NULL, '".$CardReversalText."', FALSE)";
		$tmpString = "INSERT INTO reversalerrors (id, reversalerrortext, iddeleted) VALUES (NULL, ".$CardReversalText.", FALSE)";
		
		
		if ($dtcon->query($InputString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Reversal Error Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$newAddID = $dtcon->insert_id;
		
		$constantNum = 993300000;
		$newAddID = $constantNum + $newAddID;
		
		$userChangeInsert = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '1', '".$newAddID."', 'reversalerrors Sequence: ".$tmpString."')";
		
		if ($dtcon->query($userChangeInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: adminEdit.php" );
			exit();
		}
		
		$_SESSION["EditSuccess"] = "Reversal Error Addition Successful.";
		
	}	
	
	//End
	
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Server POST, please contact the System Admin.';
		header( "Location: adminEdit.php" );
		exit();
	}
}



//Get Database Data from the 7 Tables for this page.
//===============================================================
$reason_query = "SELECT * FROM disputereasons WHERE iddeleted=FALSE";
$reason_query_data = $dtcon->query($reason_query);
$reason_data = $reason_query_data->fetch_all();

//var_dump($reason_data);
$NumberofReason = count($reason_data);
//===============================================================
$acct_type_query = "SELECT * FROM accounttype WHERE iddeleted=FALSE";
$acct_type_query_data = $dtcon->query($acct_type_query);
$acct_type_data = $acct_type_query_data->fetch_all();

//var_dump($acct_type_data);
$Numberof_acct_Types = count($acct_type_data);
//===============================================================
$type_query = "SELECT * FROM cardtype WHERE iddeleted=FALSE";
$type_query_data = $dtcon->query($type_query);
$type_data = $type_query_data->fetch_all();

//var_dump($type_data);
$NumberofTypes = count($type_data);
//===============================================================
$status_query = "SELECT * FROM cardstatus WHERE iddeleted=FALSE";
$status_query_data = $dtcon->query($status_query);
$status_data = $status_query_data->fetch_all();

//var_dump($status_data);
$NumberofStatus = count($status_data);
//===============================================================
$possession_query = "SELECT * FROM cardpossession WHERE iddeleted=FALSE";
$possession_query_data = $dtcon->query($possession_query);
$possession_data = $possession_query_data->fetch_all();

//var_dump($possession_data);
$NumberofPoss = count($possession_data);
//===============================================================
$compromise_query = "SELECT * FROM compromise WHERE iddeleted=FALSE";
$compromise_query_data = $dtcon->query($compromise_query);
$compromise_data = $compromise_query_data->fetch_all();

//var_dump($compromise_data);
$NumberofComp = count($compromise_data);
//===============================================================
$reversal_query = "SELECT * FROM reversalerrors WHERE iddeleted=FALSE";
$reversal_query_data = $dtcon->query($reversal_query);
$reversal_data = $reversal_query_data->fetch_all();

//var_dump($reversal_data);
$NumberofRever = count($reversal_data);
//===============================================================





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
//exit();

?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - Admin Edit</title>
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
$PageTitle = "Dispute Track - Admin Edit";

if($accesslevel >= 7){
	include("assets/includes/HTMLscript.php");
}


include("assets/includes/autologout.php");

include("assets/includes/loadingHTML.php");


?>
   
   <script>
		
		/*
		//Old JS Code.
		var addCardID = 1;
		
		function addInput() {
    
		    var addList = document.getElementById('AddNewCard');
			var docstyle = addList.style.display;

			addCardID++;
			
			var text = document.createElement('div');
			text.innerHTML = "<div class='row'><div id='CardNumber_" + addCardID + "_ID' class='col-md-2'><label>Card Number " + addCardID + "</label><input type='text' class='form-control' name='Card_Number_" + addCardID + "' maxlength='16'></div><div id='CardStatus_" + addCardID + "_ID' class='col-md-6'><label>Card " + addCardID + " Status</label><p class='form-control'><input type='checkbox' name='CardStatus1_" + addCardID + "' value='InPoss'>&nbsp;&nbsp;Card in Their Possession&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='CardStatus2_" + addCardID + "' value='Lost'>&nbsp;&nbsp;Card is Lost&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='CardStatus3_" + addCardID + "' value='HaveRec'>&nbsp;&nbsp;They Have Their Receipt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='CardStatus4_" + addCardID + "' value='NoRec'>&nbsp;&nbsp;Don't Have Receipt&nbsp;&nbsp;</p></div></div>";

			addList.appendChild(text);
			
			document.getElementById('NumberofCardsID').value = addCardID;
			
			var y = document.getElementById('RemoveCard_button');
			
			y.style.display = 'block';
			
			
		}
		
		function removeElement() {
			
			if (addCardID <= 1){
				
			}
			else{
				
				LastID = addCardID;
				addCardID--;
				
				document.getElementById('NumberofCardsID').value = addCardID;
				
				CardNumber_ToDestroy = "CardNumber_" + LastID + "_ID";
				CardStatus_ToDestroy = "CardStatus_" + LastID + "_ID";
				
				var NumberElement = document.getElementById(CardNumber_ToDestroy);
				NumberElement.parentNode.removeChild(NumberElement);
				
				var StatusElement = document.getElementById(CardStatus_ToDestroy);
				StatusElement.parentNode.removeChild(StatusElement);
				
				if (addCardID <= 1){
					
					var y = document.getElementById('RemoveCard_button');
			
					y.style.display = 'none';
					
				}
				
				
				
			}
			
		}
		
		*/
		
	</script>
	<script>
		
		//Hide all the Adding rows, then show only the one selected.
		function AddRow(){
			var currViewID = document.getElementById('AddRowID');
			var currValue = currViewID.options[currViewID.selectedIndex].value;
			
			var l = document.getElementById('AddDisputeReasonsID');
			l.style.display = 'none';
			
			var m = document.getElementById('AddAccountTypeID');
			m.style.display = 'none';
			
			var n = document.getElementById('AddCompromiseID');
			n.style.display = 'none';
			
			var o = document.getElementById('AddCardTypeID');
			o.style.display = 'none';
			
			var p = document.getElementById('AddCardStatusID');
			p.style.display = 'none';
			
			var q = document.getElementById('AddCardPossID');
			q.style.display = 'none';
			
			var r = document.getElementById('AddReversalErrorsID');
			r.style.display = 'none';
			
			
			if (currValue == 'DisputeReasons'){
				l.style.display = 'block';
			}
			if (currValue == 'AccountTypes'){
				m.style.display = 'block';
			}
			if (currValue == 'Compromises'){
				n.style.display = 'block';
			}
			if (currValue == 'CardTypes'){
				o.style.display = 'block';
			}
			if (currValue == 'CardStatuses'){
				p.style.display = 'block';
			}
			if (currValue == 'CardPossesionTypes'){
				q.style.display = 'block';
			}
			if (currValue == 'ReversalErrors'){
				r.style.display = 'block';
			}
			
		}
		
		//Hide all the Editing rows, then show only the one selected.
		function EditRow(){
			
			var currViewID = document.getElementById('ViewChangeID');
			var currValue = currViewID.options[currViewID.selectedIndex].value;
			
			var t = document.getElementById('DisputeReasonsID');
			t.style.display = 'none';
			
			var u = document.getElementById('AccountTypeID');
			u.style.display = 'none';
			
			var v = document.getElementById('CompromiseID');
			v.style.display = 'none';
			
			var w = document.getElementById('CardTypeID');
			w.style.display = 'none';
			
			var x = document.getElementById('CardStatusID');
			x.style.display = 'none';
			
			var y = document.getElementById('CardPossID');
			y.style.display = 'none';
			
			var z = document.getElementById('ReversalErrorsID');
			z.style.display = 'none';
			
			
			if (currValue == 'DisputeReasons'){
				t.style.display = 'block';
			}
			if (currValue == 'AccountTypes'){
				u.style.display = 'block';
			}
			if (currValue == 'Compromises'){
				v.style.display = 'block';
			}
			if (currValue == 'CardTypes'){
				w.style.display = 'block';
			}
			if (currValue == 'CardStatuses'){
				x.style.display = 'block';
			}
			if (currValue == 'CardPossesionTypes'){
				y.style.display = 'block';
			}
			if (currValue == 'ReversalErrors'){
				z.style.display = 'block';
			}
			
		}
		
		//Hide Everything, the Rows and the drop downs, then show only the Selected Drop down.
		function AddorEdit(){
			
			var currID = document.getElementById('AddorEditID');
			var currValue = currID.options[currID.selectedIndex].value;
			
			var a = document.getElementById('AddRow');
			a.style.display = 'none';
			
			document.getElementById("AddRowID").value = "select";
			
			var l = document.getElementById('AddDisputeReasonsID');
			l.style.display = 'none';
			
			var m = document.getElementById('AddAccountTypeID');
			m.style.display = 'none';
			
			var n = document.getElementById('AddCompromiseID');
			n.style.display = 'none';
			
			var o = document.getElementById('AddCardTypeID');
			o.style.display = 'none';
			
			var p = document.getElementById('AddCardStatusID');
			p.style.display = 'none';
			
			var q = document.getElementById('AddCardPossID');
			q.style.display = 'none';
			
			var r = document.getElementById('AddReversalErrorsID');
			r.style.display = 'none';
			
			
			// =============================================================
			
			
			var b = document.getElementById('EditRow');
			b.style.display = 'none';
			
			document.getElementById("ViewChangeID").value = "select";
			
			var t = document.getElementById('DisputeReasonsID');
			t.style.display = 'none';
			
			var u = document.getElementById('AccountTypeID');
			u.style.display = 'none';
			
			var v = document.getElementById('CompromiseID');
			v.style.display = 'none';
			
			var w = document.getElementById('CardTypeID');
			w.style.display = 'none';
			
			var x = document.getElementById('CardStatusID');
			x.style.display = 'none';
			
			var y = document.getElementById('CardPossID');
			y.style.display = 'none';
			
			var z = document.getElementById('ReversalErrorsID');
			z.style.display = 'none';
			
			
			
			if (currValue == 'Add'){
				a.style.display = 'block';
			}
			if (currValue == 'Edit'){
				b.style.display = 'block';
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
                    <div class="col-md-3">
                     <h2>Admin Edit Page</h2> 
                    </div>
					<div class="col-md-9">
<?php
//Session Error
if(isset($_SESSION["ADD_DISPUTE_ERROR"])){

	print "<h2><span style='color:red'>" . $_SESSION["ADD_DISPUTE_ERROR"] . "</span></h2>";
	
	unset($_SESSION["ADD_DISPUTE_ERROR"]);
	unset($_SESSION["TransactionAdded"]);
	
}
//Session Success
else if(isset($_SESSION["EditSuccess"])){
	
	print "<h2><span style='color:blue'>" . $_SESSION["EditSuccess"] . "</span></h2>";
	
	unset($_SESSION["EditSuccess"]);
	
}

?>

					</div>
                </div>           
				<hr />
				<!--  Below H2, above HR  -->
				
				<div class="row">
                    <div class="col-md-8">
						<label>Do you want to Add a row to a Table or Edit a Table:</label>
						<select id="AddorEditID" name="AddorEdit_Name" class="form-control" onchange="AddorEdit();">
							<option value="select" selected="select">Select One</option>
							<option value="Add">Add a Row</option>
							<option value="Edit">Edit a Table</option>
						</select>
					</div>
					<div class="col-md-2">
						
					</div>
				</div>
				<hr />
				
				<!-- /. ROW  -->
				
				<div class="row" id='AddRow' style="display:none">
                    <div class="col-md-2">
						<label>Add a Row to Which Table:</label>
						<select id="AddRowID" name="AddRowName" class="form-control" onchange="AddRow();">
							<option value="select" selected="select">Select One</option>
							<option value="DisputeReasons">Dispute Reasons</option>
							<option value="AccountTypes">Account Types</option>
							<option value="Compromises">Compromises</option>
							<option value="CardTypes">Card Types</option>
							<option value="CardStatuses">Card Statuses</option>
							<option value="CardPossesionTypes">Card Possession Types</option>
							<option value="ReversalErrors">Reversal Errors</option>
						</select>
					</div>
				</div>
				
				<div class="row" id='EditRow' style="display:none">
                    <div class="col-md-2">
						<label>View Table:</label>
						<select id="ViewChangeID" name="ViewChange" class="form-control" onchange="EditRow();">
							<option value="select" selected="select">Select One</option>
							<option value="DisputeReasons">Dispute Reasons</option>
							<option value="AccountTypes">Account Types</option>
							<option value="Compromises">Compromises</option>
							<option value="CardTypes">Card Types</option>
							<option value="CardStatuses">Card Statuses</option>
							<option value="CardPossesionTypes">Card Possession Types</option>
							<option value="ReversalErrors">Reversal Errors</option>
						</select>
					</div>
				</div>
				
				<hr />
                 <!-- /. ROW  -->
                
				<!-- Edit Tables -->
				<!-- ================================================================================================================== -->
				
				<div id='DisputeReasonsID' style="display:none">
				<form id="EditDispute_ID" autocomplete="off" name="EditDispute_FormName" method="post" action="adminEdit.php">
					<div class="row">
						<div class="col-md-12">
							<label>Dispute Reason Table:</label>
							<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>
								<thead>
									<tr>
									<th style='width:10%;'>Reason ID</th>
									<th style='width:40%;'>Reason Text</th>
									<th style='width:40%;'>CSR Notice Text</th>
									<th style='width:10%;'>Delete Row</th>
									</tr>
								</thead>
								<tbody id='tableBODY'>
<?php

for($i=0; $i < $NumberofReason; $i++){
	
	print '<tr>';
	print '<input type="hidden" name="DisputeIDs[]" value="'.$reason_data[$i][0].'">';
	print '<td><input type="text" class="form-control" value="'.$reason_data[$i][0].'" readonly></td>';
	print '<td><textarea class="form-control" name="DisputeReasons[]" rows="2" maxlength="255">'.$reason_data[$i][1].'</textarea></td>';
	print '<td><textarea class="form-control" name="DisputeNotices[]" rows="2" maxlength="255">'.$reason_data[$i][2].'</textarea></td>';
	print '<td><button type="submit" class="btn btn-danger" name="DeleteDispute" value="'.$reason_data[$i][0].'" onclick="return confirm(\'Click OK to confirm deletion.\')">Delete</button></td>';
	print '</tr>';
}

?>
								</tbody>
							</table>
						</div>
					</div>
				<input type="submit" class="btn btn-success" name="EditDispute" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<div id='AccountTypeID' style="display:none">
				<form id="EditAccount_ID" autocomplete="off" name="EditAccount_FormName" method="post" action="adminEdit.php">
					<div class="row">
						<div class="col-md-12">
							<label>Account Type Table:</label>
							<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>
								<thead>
									<tr>
									<th style='width:10%;'>Type ID</th>
									<th style='width:85%;'>Type Text</th>
									<th style='width:5%;'>Delete Row</th>
									</tr>
								</thead>
								<tbody id='tableBODY'>
<?php

for($i=0; $i < $Numberof_acct_Types; $i++){

	print '<tr>';
	print '<input type="hidden" name="AccountIDs[]" value="'.$acct_type_data[$i][0].'">';
	print '<td><input type="text" class="form-control" value="'.$acct_type_data[$i][0].'" readonly></td>';
	print '<td><textarea class="form-control" name="AccountType[]" rows="2" maxlength="255">'.$acct_type_data[$i][1].'</textarea></td>';
	print '<td><button type="submit" class="btn btn-danger" name="DeleteAccount" value="'.$acct_type_data[$i][0].'" onclick="return confirm(\'Click OK to confirm deletion.\')">Delete</button></td>';
	print '</tr>';
}

?>
								</tbody>
							</table>
						</div>
					</div>
				<input type="submit" class="btn btn-success" name="EditAccount" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<div id='CompromiseID' style="display:none">
				<form id="EditCompromise_ID" autocomplete="off" name="EditCompromise_FormName" method="post" action="adminEdit.php">
				<div class="row">
                    <div class="col-md-12">
						<label>Compromise Table:</label>
						<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>
							<thead>
								<tr>
								<th style='width:5%;'>Compromise ID</th>
								<th style='width:10%;'>Alert Number</th>
								<th style='width:10%;'>Merchant ID</th>
								<th style='width:10%;'>Activation Date</th>
								<th style='width:10%;'>Start Date</th>
								<th style='width:10%;'>End Date</th>
								<th style='width:40%;'>Description</th>
								<th style='width:5%;'>Delete Row</th>
								</tr>
							</thead>
							<tbody id='tableBODY'>
<?php

for($i=0; $i < $NumberofComp; $i++){
	
	print '<tr>';
	print '<input type="hidden" name="CompromiseIDs[]" value="'.$compromise_data[$i][0].'">';
	print '<td><input type="text" class="form-control" value="'.$compromise_data[$i][0].'" readonly></td>';
	print '<td><textarea class="form-control" name="CompAlert[]" rows="2" maxlength="255">'.$compromise_data[$i][1].'</textarea></td>';
	print '<td><textarea class="form-control" name="CompMerch[]" rows="2" maxlength="255">'.$compromise_data[$i][2].'</textarea></td>';
	print '<td><input type="text" class="form-control" name="CompActivation[]" maxlength="255" value="'.$compromise_data[$i][1].'" onFocus="showCalendarControl(this);"></td>';
	print '<td><input type="text" class="form-control" name="CompStartDate[]" maxlength="255" value="'.$compromise_data[$i][1].'" onFocus="showCalendarControl(this);"></td>';
	print '<td><input type="text" class="form-control" name="CompEndDate[]" maxlength="255" value="'.$compromise_data[$i][1].'" onFocus="showCalendarControl(this);"></td>';
	print '<td><textarea class="form-control" name="CompDescription[]" rows="2" maxlength="4096">'.$compromise_data[$i][6].'</textarea></td>';
	print '<td><button type="submit" class="btn btn-danger" name="DeleteCompromise" value="'.$compromise_data[$i][0].'" onclick="return confirm(\'Click OK to confirm deletion.\')">Delete</button></td>';
	print '</tr>';
}

?>
							</tbody>
						</table>
					</div>
				</div>
				<input type="submit" class="btn btn-success" name="EditCompromise" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<div id='CardTypeID' style="display:none">
				<form id="EditCardType_ID" autocomplete="off" name="EditCardType_FormName" method="post" action="adminEdit.php">
				<div class="row">
                    <div class="col-md-12">
						<label>Card Type Table:</label>
						<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>
							<thead>
								<tr>
								<th style='width:10%;'>Type ID</th>
								<th style='width:85%;'>Type Text</th>
								<th style='width:5%;'>Delete Row</th>
								</tr>
							</thead>
							<tbody id='tableBODY'>
<?php

for($i=0; $i < $NumberofTypes; $i++){

	print '<tr>';
	print '<input type="hidden" name="CardTypeIDs[]" value="'.$type_data[$i][0].'">';
	print '<td><input type="text" class="form-control" value="'.$type_data[$i][0].'" readonly></td>';
	print '<td><textarea class="form-control" name="EditCardTypes[]" rows="2" maxlength="255">'.$type_data[$i][1].'</textarea></td>';
	print '<td><button type="submit" class="btn btn-danger" name="DeleteCardType" value="'.$type_data[$i][0].'" onclick="return confirm(\'Click OK to confirm deletion.\')">Delete</button></td>';
	print '</tr>';
}

?>
							</tbody>
						</table>
					</div>
				</div>
				<input type="submit" class="btn btn-success" name="EditCardType" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<div id='CardStatusID' style="display:none">
				<form id="EditCardStatus_ID" autocomplete="off" name="EditCardStatus_FormName" method="post" action="adminEdit.php">
				<div class="row">
                    <div class="col-md-12">
						<label>Card Status Table:</label>
						<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>
							<thead>
								<tr>
								<th style='width:10%;'>Status ID</th>
								<th style='width:85%;'>Status Text</th>
								<th style='width:5%;'>Delete Row</th>
								</tr>
							</thead>
							<tbody id='tableBODY'>
<?php

for($i=0; $i < $NumberofStatus; $i++){

	print '<tr>';
	print '<input type="hidden" name="CardStatusIDs[]" value="'.$status_data[$i][0].'">';
	print '<td><input type="text" class="form-control" value="'.$status_data[$i][0].'" readonly></td>';
	print '<td><textarea class="form-control" name="EditCardStatuses[]" rows="2" maxlength="255">'.$status_data[$i][1].'</textarea></td>';
	print '<td><button type="submit" class="btn btn-danger" name="DeleteCardStatus" value="'.$status_data[$i][0].'" onclick="return confirm(\'Click OK to confirm deletion.\')">Delete</button></td>';
	print '</tr>';
}

?>
							</tbody>
						</table>
					</div>
				</div>
				<input type="submit" class="btn btn-success" name="EditCardStatus" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<div id='CardPossID' style="display:none">
				<form id="EditCardPoss_ID" autocomplete="off" name="EditCardPoss_FormName" method="post" action="adminEdit.php">
				<div class="row">
                    <div class="col-md-12">
						<label>Card Possession Table:</label>
						<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>
							<thead>
								<tr>
								<th style='width:10%;'>Possession ID</th>
								<th style='width:85%;'>Possession Text</th>
								<th style='width:5%;'>Delete Row</th>
								</tr>
							</thead>
							<tbody id='tableBODY'>
<?php

for($i=0; $i < $NumberofPoss; $i++){

	print '<tr>';
	print '<input type="hidden" name="CardPossessionIDs[]" value="'.$possession_data[$i][0].'">';
	print '<td><input type="text" class="form-control" value="'.$possession_data[$i][0].'" readonly></td>';
	print '<td><textarea class="form-control" name="CardPossessions[]" rows="2" maxlength="255">'.$possession_data[$i][1].'</textarea></td>';
	print '<td><button type="submit" class="btn btn-danger" name="DeleteCardPoss" value="'.$possession_data[$i][0].'" onclick="return confirm(\'Click OK to confirm deletion.\')">Delete</button></td>';
	print '</tr>';
}

?>
							</tbody>
						</table>
					</div>
				</div>
				<input type="submit" class="btn btn-success" name="EditCardPoss" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<div id='ReversalErrorsID' style="display:none">
				<form id="EditReversal_ID" autocomplete="off" name="EditReversal_FormName" method="post" action="adminEdit.php">
				<div class="row">
                    <div class="col-md-12">
						<label>Reversal Errors Table:</label>
						<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>
							<thead>
								<tr>
								<th style='width:10%;'>Possession ID</th>
								<th style='width:85%;'>Possession Text</th>
								<th style='width:5%;'>Delete Row</th>
								</tr>
							</thead>
							<tbody id='tableBODY'>
<?php

for($i=0; $i < $NumberofRever; $i++){

	print '<tr>';
	print '<input type="hidden" name="ReversalErrorIDs[]" value="'.$reversal_data[$i][0].'">';
	print '<td><input type="text" class="form-control" value="'.$reversal_data[$i][0].'" readonly></td>';
	print '<td><textarea class="form-control" name="EditReversalErrors[]" rows="2" maxlength="255">'.$reversal_data[$i][1].'</textarea></td>';
	print '<td><button type="submit" class="btn btn-danger" name="DeleteReversal" value="'.$reversal_data[$i][0].'" onclick="return confirm(\'Click OK to confirm deletion.\')">Delete</button></td>';
	print '</tr>';
}

?>
							</tbody>
						</table>
					</div>
				</div>
				<input type="submit" class="btn btn-success" name="EditReversal" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<!-- Add New Line -->
				<!-- ================================================================================================================== -->
				
				<div id='AddDisputeReasonsID' style="display:none">
				<form id="AddDispute_ID" autocomplete="off" name="AddDispute_FormName" method="post" action="adminEdit.php">
				<div class="row">
                    <div class="col-md-12">
						<label>Add New Dispute Reason:</label>
					</div>
				</div>
				<div class="row">
                    <div class="col-md-3">
						<label>Dispute Reason:</label>
						<input type="text" class="form-control" name="AddDisputeReason" maxlength="255">
					</div>
					<div class="col-md-3">
						<label>Dispute CSR Notice Text:</label>
						<input type="text" class="form-control" name="AddDisputeNoticeText" maxlength="255">
					</div>
				</div>
				<br>
				<input type="submit" class="btn btn-success" name="AddDispute" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<div id='AddAccountTypeID' style="display:none">
				<form id="AddAccount_ID" autocomplete="off" name="AddAccount_FormName" method="post" action="adminEdit.php">
				<div class="row">
                    <div class="col-md-12">
						<label>Add New Account Type:</label>
					</div>
				</div>
				<div class="row">
                    <div class="col-md-12">
						<label>Account Type Text:</label>
						<input type="text" class="form-control" name="AddAccountText" maxlength="255">
					</div>
				</div>
				<br>
				<input type="submit" class="btn btn-success" name="AddAccount" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<div id='AddCompromiseID' style="display:none">
				<form id="AddCompromise_ID" autocomplete="off" name="AddCompromise_FormName" method="post" action="adminEdit.php">
				<div class="row">
                    <div class="col-md-12">
						<label>Add New Compromise:</label>
					</div>
				</div>
				<div class="row">
                    <div class="col-md-2">
						<label>Compromise Alert Number:</label>
						<input type="text" class="form-control" name="AddCompAlertNum" maxlength="255">
					</div>
					<div class="col-md-2">
						<label>Compromise Merchant Number:</label>
						<input type="text" class="form-control" name="AddCompMerch" maxlength="255">
					</div>
					<div class="col-md-2">
						<label>Compromise Activation Date:</label>
						<input type="text" class="form-control" name="AddCompActDate" maxlength="255" onFocus="showCalendarControl(this);">
					</div>
					<div class="col-md-2">
						<label>Compromise Start Date:</label>
						<input type="text" class="form-control" name="AddCompStartDate" maxlength="255" onFocus="showCalendarControl(this);">
					</div>
					<div class="col-md-2">
						<label>Compromise End Date:</label>
						<input type="text" class="form-control" name="AddCompEndDate" maxlength="255" onFocus="showCalendarControl(this);">
					</div>
				</div>
				<div class="row">
					<div class="col-md-10">
						<label>Compromise Description:</label>
						<textarea class="form-control" name="AddCompDescrip" rows="2" maxlength="4096"></textarea>
					</div>
				</div>
				<br>
				<input type="submit" class="btn btn-success" name="AddCompromise" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<div id='AddCardTypeID' style="display:none">
				<form id="AddCardType_ID" autocomplete="off" name="AddCardType_FormName" method="post" action="adminEdit.php">
				<div class="row">
                    <div class="col-md-12">
						<label>Add New Card Type:</label>
					</div>
				</div>
				<div class="row">
                    <div class="col-md-12">
						<label>Card Type Text:</label>
						<input type="text" class="form-control" name="AddCardTypeText" maxlength="255">
					</div>
				</div>
				<br>
				<input type="submit" class="btn btn-success" name="AddCardType" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<div id='AddCardStatusID' style="display:none">
				<form id="AddCardStatus_ID" autocomplete="off" name="AddCardStatus_FormName" method="post" action="adminEdit.php">
				<div class="row">
                    <div class="col-md-12">
						<label>Add New Card Status:</label>
					</div>
				</div>
				<div class="row">
                    <div class="col-md-12">
						<label>Card Status Text:</label>
						<input type="text" class="form-control" name="AddCardStatusText" maxlength="255">
					</div>
				</div>
				<br>
				<input type="submit" class="btn btn-success" name="AddCardStatus" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<div id='AddCardPossID' style="display:none">
				<form id="AddCardPoss_ID" autocomplete="off" name="AddCardPoss_FormName" method="post" action="adminEdit.php">
				<div class="row">
                    <div class="col-md-12">
						<label>Add New Card Possession:</label>
					</div>
				</div>
				<div class="row">
                    <div class="col-md-12">
						<label>Card Possession Text:</label>
						<input type="text" class="form-control" name="AddCardPossText" maxlength="255">
					</div>
				</div>
				<br>
				<input type="submit" class="btn btn-success" name="AddCardPoss" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<div id='AddReversalErrorsID' style="display:none">
				<form id="AddReversal_ID" autocomplete="off" name="AddReversal_FormName" method="post" action="adminEdit.php">
				<div class="row">
                    <div class="col-md-12">
						<label>Add New Reversal Error:</label>
					</div>
				</div>
				<div class="row">
                    <div class="col-md-12">
						<label>Reversal Error Text:</label>
						<input type="text" class="form-control" name="AddReversalText" maxlength="255">
					</div>
				</div>
				<br>
				<input type="submit" class="btn btn-success" name="AddReversal" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				
				
				
				
				
				
				
				
				
				
				
				
				

			
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
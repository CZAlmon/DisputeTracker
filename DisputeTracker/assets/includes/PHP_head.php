<?php

require_once('assets/includes/connection.php');

session_start();


//This statement protects all pages from being accessed by non-users 
if(!isset($_SESSION['username'])){
	unset($_SESSION["WorkingDispute"]);
	unset($_SESSION["Dispute_CaseID"]);
	unset($_SESSION['FinishDispute']);
	unset($_SESSION['CaseCanBeDone']);
	header( "Location: login.php" );
	exit();
}
//This statement protects all pages from being accessed if time isn't set
if(!isset($_SESSION["time_started"])){
	unset($_SESSION["WorkingDispute"]);
	unset($_SESSION["Dispute_CaseID"]);
	unset($_SESSION['FinishDispute']);
	unset($_SESSION['CaseCanBeDone']);
	header( "Location: login.php" );
	exit();
}
else{
	//This checks the time set to see if the user needs to log in again.
	//Get current time and compare it to the time set. If the day doesnt match or time difference is greater then 1 hour
	//User must relog
	date_default_timezone_set("America/Chicago");
	$timeStarted = $_SESSION["time_started"];
	$timeNow = date("Y-m-d_H:i:s");

	$tmpExplode = explode("_", $timeStarted);

	$timeStartedDate = $tmpExplode[0] . " GMT";
	$timeStartedTime = $tmpExplode[1] . " GMT";

	$tmpExplode = explode("_", $timeNow);

	$TimeNowDate = $tmpExplode[0] . " GMT";
	$TimeNowTime = $tmpExplode[1] . " GMT";
	
	//var_dump($_SESSION);
	//var_dump($timeNow);
	
	//var_dump($timeStartedDate);
	//var_dump($TimeNowDate);
	//var_dump(strtotime($timeStartedDate));
	//var_dump(strtotime($TimeNowDate));
	
	//var_dump($TimeNowTime);
	//var_dump($timeStartedTime);
	//var_dump(strtotime($TimeNowTime));
	//var_dump(strtotime($timeStartedTime));
	//exit();
	
	if(strtotime($timeStartedDate) != strtotime($TimeNowDate)){
		
		session_start();
		$_SESSION = array();
		session_destroy();
		
		header( "Location: login.php" );
		exit();
	}

	$Temp_Diff = abs(strtotime($TimeNowTime) - strtotime($timeStartedTime));
	
	//var_dump($Temp_Diff);
	
	//print "Here";
	//exit();
	
	//3600 is an hour, 4200 is 70 minutes. The 10 extra minutes is to help avoid some weird behavior of the program not auto-logging out but won't keep you logged in when you finally come back to switch pages.

	if($Temp_Diff > 4200){
		
		session_start();
		$_SESSION = array();
		session_destroy();
		
		header( "Location: login.php" );
		exit();
		
	}
}

//Get the users username
$username = $_SESSION["username"];
//$username="zach.almon";

//Get thier information
$string_query = "SELECT * FROM users where username='" . $username . "'";

$res = $dtcon->query($string_query);

$userData = $res->fetch_all();
//If user isn't set in the Dispute Track DB.
if (empty($userData)){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use DisputeTrack, Please Contact the System Admin';
	unset($_SESSION["WorkingDispute"]);
	unset($_SESSION["Dispute_CaseID"]);
	unset($_SESSION['FinishDispute']);
	unset($_SESSION['CaseCanBeDone']);
	header( "Location: login.php" );
	exit();
}
//Get Users IP
$ipaddress = $_SERVER['REMOTE_ADDR'];

$fname = $userData[0][2];
$lname = $userData[0][3];
$accesslevel = $userData[0][4];
$location = $userData[0][5];
$inactive = $userData[0][6];

/*
var_dump($userData);

print $fname;
print $lname;
print $accesslevel;
print $location;
print $inactive;
*/

//If user is inactive from DB they can't use the program
if($inactive != '0'){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You are disabled from using DisputeTrack, Please Contact the System Admin';
	unset($_SESSION["WorkingDispute"]);
	unset($_SESSION["Dispute_CaseID"]);
	unset($_SESSION['FinishDispute']);
	unset($_SESSION['CaseCanBeDone']);
	header( "Location: login.php" );
	exit();
}

$fullname = $fname . " " . $lname;

//If a page is reloaded, overwrite time_started to keep users logged in.
date_default_timezone_set("America/Chicago");
$_SESSION["time_started"] = date("Y-m-d_H:i:s");








// ==============================================================================



// Functions:

//Used to help clean data from user.
function cleanData($data) {
	$data = trim($data);
	$data = stripslashes($data);
	return $data;
}

//Used to Validate a single date given
function validateDate($date, $format = 'Y-m-d, G:i:s'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

//Used to Validate that a given date is in the past (Date only, time of no matter)
function validatePastDate($date){
	
	/*
	$tmpDate = new DateTime($date);
	$tmpDate = $tmpDate->format($format);
	
	$compareDate = new DateTime();
	
	if ($compareDate < $tmpDate){
		return TRUE;
	}
	
	return FALSE;
	*/
	
	$today = time();
	
	//var_dump($today);
	//var_dump(strtotime($date));

	if (strtotime($date) > $today) {
		// future date
		return false;
	}
	return true;
	
}



//Used to Validate that a given date is in the past OR only a week in the future (Date only, time of no matter)
function validatePastFutureDate($date){
	
	
	$today = time();
	
	$sevendaysfromnow = $today + (7*24*60*60); //Add 1 week to time, 7 days, by 24 hours, by 60 min, by 60 seconds, to get number in seconds to add to time
	
	//var_dump($today);
	//print("<br><br>");
	//var_dump($sevendaysfromnow);
	//print("<br><br>");
	//var_dump($today + (7*24*60*60));
	//print("<br><br>");
	//var_dump(strtotime($date));
	
	//exit();

	if (strtotime($date) > $sevendaysfromnow) {
		// future date
		return false;
	}
	return true;
	
}





//Given two dates, validate Posted date is not before Transaction Date
function validatePostedDate($TransactionDate, $PostedDate){
	
	if (strtotime($TransactionDate) > strtotime($PostedDate)){
		return false;
	}
	return true;
	
}

//Validate Data given is okay to be put in DB. 
function isAscii($str) {
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
		
		//var_dump(ord($str[$i]));
		//print("      ");
		//var_dump($str[$i]);
		//print("<br><br>");
		
        if (ord($str[$i]) > 127) return false;
    }
    return true;
}

//Used to Validate State Abbreviations (Plus DC)
function isState($str){
	
	$us_state_abbrevs = array('AL', 'AK', 'AS', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FM', 'FL', 'GA', 'GU', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MH', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'MP', 'OH', 'OK', 'OR', 'PW', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VI', 'VA', 'WA', 'WV', 'WI', 'WY');
	
	if(in_array($str, $us_state_abbrevs)){
		return true;
	}
	
	return false;
	
}

//Used to Validate State Name and Abbreviations (Plus DC)
function stateCheck($StateVar){
	
	$ArrayToReturn = array();
	
	$states = array('Kentucky','Tennessee','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island','South Carolina','South Dakota','Texas','Utah','Vermont','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
	$us_state_abbrevs = array('KY', 'TN', 'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY');
	
	$AbbrCount = count($us_state_abbrevs);
	$StateCount = count($states);

	for($i=0;$i<$AbbrCount;$i++){
		
		if($StateVar == $us_state_abbrevs[$i]){
			array_push($ArrayToReturn, '<option value="'.$us_state_abbrevs[$i].'" selected="true">'.$states[$i].'</option>');
		}
		else{
			array_push($ArrayToReturn, '<option value="'.$us_state_abbrevs[$i].'">'.$states[$i].'</option>');
		}		
	}
	
	return $ArrayToReturn;
	
}

//Given 2 Arrays, Validate Data in each array matches with each other. 
//Proves Data From input is different or not from data within DB.
function editContentCheck($OldContent, $NewContent){
	
	$FUNC_tmpOldCount = count($OldContent);
	$FUNC_tmpNewCount = count($NewContent);
	
	if($FUNC_tmpOldCount != $FUNC_tmpNewCount){
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong in the Edit check Function! Please contact the System Admin.';
		header( "Location: index.php" );
		exit();
	}
	
	for($z=0;$z<$FUNC_tmpOldCount;$z++){
		
		if($OldContent[$z] != $NewContent[$z]){
			return TRUE;
		}
	}
	
	return FALSE;
}

//Convert Seconds into Days, drops hours/minutes/seconds (Basically rounds down to the day)
function convert_seconds($seconds) {
	date_default_timezone_set("America/Chicago");
	$dt1 = new DateTime("@0");
	$dt2 = new DateTime("@$seconds");
	return $dt1->diff($dt2)->format('%a day(s)'); //, %h hour(s), %i minute(s) and %s second(s)
}



function deep_in_array($needle, $haystack){
	if(in_array($needle, $haystack)) {
        return true;
    }
    foreach($haystack as $element) {
        if(is_array($element) && deep_in_array($needle, $element)){
            return true;
		}
    }
    return false;
}





// ==============================================================================









?>
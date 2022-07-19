<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');


if ($accesslevel < 7){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}




//==========================================================================
$location_query = "SELECT * FROM location";
$location_query_data = $dtcon->query($location_query);
$location_data = $location_query_data->fetch_all();

//var_dump($location_data);
$NumberofLocation = count($location_data);
//==========================================================================
$access_query = "SELECT * FROM accesslevels";
$access_query_data = $dtcon->query($access_query);
$access_data = $access_query_data->fetch_all();

//var_dump($access_data);
$Numberofaccess = count($access_data);
//==========================================================================

//==========================================================================
$users_query = "SELECT * FROM users";		// WHERE inactive=FALSE
$users_query_data = $dtcon->query($users_query);
$users_data = $users_query_data->fetch_all();

//var_dump($users_data);
$Numberofusers = count($users_data);
//===========================================================================



if ($_SERVER["REQUEST_METHOD"] == "POST"){
	
	date_default_timezone_set("America/Chicago");
	$timeofChange = date("Y-m-d, G:i:s");
	
	//Edit Rows Below
	
	//var_dump($_POST);
	
	if(isset($_POST["EditUser"])){
		
		$changedBool = FALSE;
		
		//check to see if everything is set
		if(!isset($_POST["UserIDs"]) || !isset($_POST["User_Usernames"]) || !isset($_POST["User_Firstnames"]) || !isset($_POST["User_Lastnames"]) || !isset($_POST["User_access"]) || !isset($_POST["User_location"]) || !isset($_POST["User_inactive"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Values can\'t be empty.';
			header( "Location: addUser.php" );
			exit();
		}
		
		$UserIDArr = $_POST["UserIDs"];
		$UserNameArr = $_POST["User_Usernames"];
		$UserFNameArr = $_POST["User_Firstnames"];
		$UserLNameArr = $_POST["User_Lastnames"];
		$UserAccessArr = $_POST["User_access"];
		$UserLocationArr = $_POST["User_location"];
		$UserInactiveArr = $_POST["User_inactive"];
		
		$Count = count($UserIDArr);
		
		for($i=0;$i<$Count;$i++){
			
			//check some values
			$stringGoodZERO = isAscii($UserNameArr[$i]);
			$stringGoodONE = isAscii($UserFNameArr[$i]);
			$stringGoodTWO = isAscii($UserLNameArr[$i]);
			
			if (!$stringGoodZERO || !$stringGoodONE || !$stringGoodTWO){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the name(s) were Detected! Please try again using regular characters!';
				header( "Location: addUser.php" );
				exit();
			}
			
			//escape String values
			$UserIDPost = $UserIDArr[$i];
			$UserNamePOST = $dtcon->real_escape_string($UserNameArr[$i]);
			$UserFNamePOST = $dtcon->real_escape_string($UserFNameArr[$i]);
			$UserLNamePOST = $dtcon->real_escape_string($UserLNameArr[$i]);
			$UserAccessPOST = $UserAccessArr[$i];
			$UserLocationPOST = $UserLocationArr[$i];
			$UserInactivePOST = $UserInactiveArr[$i];
			
			
			//Make sure every variable has a value
			if($UserIDPost == "" || $UserNamePOST == "" || $UserFNamePOST == "" || $UserLNamePOST == "" || $UserAccessPOST == "" || $UserLocationPOST == "" || $UserInactivePOST == ""){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Values can\'t be empty.';
				header( "Location: addUser.php" );
				exit();
			}
			if($UserIDPost == "" || $UserNamePOST == "" || $UserFNamePOST == "" || $UserLNamePOST == ""){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Values can\'t be empty.';
				header( "Location: addUser.php" );
				exit();
			}
			
			
			//Check Non-string values
			if(!is_numeric($UserAccessPOST)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Access value. Please contact the System Admin';
				header( "Location: addUser.php" );
				exit();
			}
			if(!is_numeric($UserLocationPOST)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Location value. Please contact the System Admin';
				header( "Location: addUser.php" );
				exit();
			}
			if($UserInactivePOST != "FALSE" && $UserInactivePOST != "TRUE"){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the User Active value. Please contact the System Admin';
				header( "Location: addUser.php" );
				exit();
			}
			//Check function needs 0 or 1 for False or True (Respectively), to correctly see if a value is changed.
			if($UserInactivePOST == "FALSE"){
				$UserInactivePOST = "0";
			}
			else{
				$UserInactivePOST = "1";
			}
			
			//var_dump($UserIDArr[$i], $UserNameArr[$i], $UserFNameArr[$i], $UserLNameArr[$i], $UserAccessArr[$i], $UserLocationArr[$i], $UserInactiveArr[$i]);
			//var_dump($UserIDPost, $UserNamePOST, $UserFNamePOST, $UserLNamePOST, $UserAccessPOST, $UserLocationPOST, $UserInactivePOST);
			
			//Set Strings, VAR String is without ' so that it can be inserted into the DB as a string
			$tmpUpdateString = "UPDATE users SET username='".$UserNamePOST."', fname='".$UserFNamePOST."', lname='".$UserLNamePOST."', accesslevel='".$UserAccessPOST."', locationid='".$UserLocationPOST."', inactive='".$UserInactivePOST."' WHERE id='".$UserIDPost."'";
			$tmpUpdateStringVAR = "UPDATE users SET username=".$UserNamePOST.", fname=".$UserFNamePOST.", lname=".$UserLNamePOST.", accesslevel=".$UserAccessPOST.", locationid=".$UserLocationPOST.", inactive=".$UserInactivePOST." WHERE id=".$UserIDPost."";
			
			//Changelog ID
			$constantNum = 777700000;
			$POSTtmpNum = $constantNum + $UserIDPost;
			
			$tmpChangeLogString = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '2', '".$POSTtmpNum."', 'User Updated Content from users --- New Content: ".$tmpUpdateStringVAR."')";
			
			//var_dump($tmpUpdateString, $tmpUpdateStringVAR, $tmpChangeLogString);
			
			//Check to see if a row has changed.
			$tmp_users_query = "SELECT * FROM users WHERE id='".$UserIDPost."'";
			$tmp_users_query_data = $dtcon->query($tmp_users_query);
			$tmp_users_data = $tmp_users_query_data->fetch_all();
			
			$editUserOldContent = array();
			$editUserNewContent = array();
			
			array_push($editUserOldContent, $tmp_users_data[0][0]);
			array_push($editUserOldContent, $dtcon->real_escape_string($tmp_users_data[0][1]));
			array_push($editUserOldContent, $dtcon->real_escape_string($tmp_users_data[0][2]));
			array_push($editUserOldContent, $dtcon->real_escape_string($tmp_users_data[0][3]));
			array_push($editUserOldContent, $tmp_users_data[0][4]);
			array_push($editUserOldContent, $tmp_users_data[0][5]);
			array_push($editUserOldContent, $tmp_users_data[0][6]);
			
			array_push($editUserNewContent, $UserIDPost);
			array_push($editUserNewContent, $UserNamePOST);
			array_push($editUserNewContent, $UserFNamePOST);
			array_push($editUserNewContent, $UserLNamePOST);
			array_push($editUserNewContent, $UserAccessPOST);
			array_push($editUserNewContent, $UserLocationPOST);
			array_push($editUserNewContent, $UserInactivePOST);
			
			$tmp_post_bool = editContentCheck($editUserOldContent, $editUserNewContent);
			
			//var_dump($tmp_users_data, $editUserOldContent, $editUserNewContent, $tmp_post_bool);
			
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
			$_SESSION["EditSuccess"] = "User Edited Successfully.";
		}
		else{
			$_SESSION["EditSuccess"] = "No Change Detected. If you think this is an error, please contact the System Admin.";
		}
		
	}
	else if(isset($_POST["AddUser"])){
		
		$AddUsersName = $_POST["AddUserName"];
		$AddUsersFirstName = $_POST["AddUserFirstName"];
		$AddUsersLastName = $_POST["AddUserLastName"];
		$AddUsersAccessID = $_POST["AddUserAccess"];
		$AddUsersLocationID = $_POST["AddUserLocation"];
		
		//check to make sure  items are set and are not empty.
		if(!isset($_POST["AddUserName"]) || $AddUsersName == "" || !isset($_POST["AddUserFirstName"]) || $AddUsersFirstName == "" || !isset($_POST["AddUserLastName"]) || $AddUsersLastName == "" || !isset($_POST["AddUserAccess"]) || $AddUsersAccessID == "" || !isset($_POST["AddUserLocation"]) || $AddUsersLocationID == ""){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Values can\'t be empty.';
			header( "Location: addUser.php" );
			exit();
		}
		
		//var_dump($users_data);
		//var_dump($AddUsersName);
		//var_dump($AddUsersFirstName);
		//var_dump($AddUsersLastName);
		//var_dump($AddUsersAccessID);
		//var_dump($AddUsersLocationID);
		//exit();
		
		$stringGoodONE = isAscii($AddUsersName);
		$stringGoodTWO = isAscii($AddUsersFirstName);
		$stringGoodTHR = isAscii($AddUsersLastName);
		
		if (!$stringGoodONE || !$stringGoodTWO || !$stringGoodTHR){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the name(s) were Detected! Please try again using regular characters!';
			header( "Location: addUser.php" );
			exit();
		}
		
		$AddUsersName = $dtcon->real_escape_string($AddUsersName);
		$AddUsersFirstName = $dtcon->real_escape_string($AddUsersFirstName);
		$AddUsersLastName = $dtcon->real_escape_string($AddUsersLastName);
		
		if (!is_numeric($AddUsersAccessID)){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Access Selection Number! Please contact the System Admin';
			header( "Location: addUser.php" );
			exit();
		}
		
		if (!is_numeric($AddUsersLocationID)){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'There is a problem with the Location Selection Number! Please contact the System Admin';
			header( "Location: addUser.php" );
			exit();
		}
		
		
		$InputString = "INSERT INTO users (id, username, fname, lname, accesslevel, locationid, inactive) VALUES (NULL, '".$AddUsersName."', '".$AddUsersFirstName."', '".$AddUsersLastName."', '".$AddUsersAccessID."', '".$AddUsersLocationID."', 0)";
		$tmpString = "INSERT INTO users (id, username, fname, lname, accesslevel, locationid, inactive) VALUES (NULL, ".$AddUsersName.", ".$AddUsersFirstName.", ".$AddUsersLastName.", ".$AddUsersAccessID.", ".$AddUsersLocationID.", 0)";
		
		
		if ($dtcon->query($InputString) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the users Insert! Please Contact the System Admin!';
			header( "Location: addUser.php" );
			exit();
		}
		
		$newAddID = $dtcon->insert_id;
		
		$constantNum = 777700000;
		$newAddID = $constantNum + $newAddID;
		
		$userChangeInsert = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '1', '".$newAddID."', 'users Sequence: ".$tmpString."')";
		
		if ($dtcon->query($userChangeInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin!';
			header( "Location: addUser.php" );
			exit();
		}			
		
		$_SESSION["EditSuccess"] = "New User Addition Successful.";
		
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Server POST, please contact the System Admin.';
		header( "Location: addUser.php" );
		exit();
	}
	
	//After Editing Users, Redirect to refresh the page so that any changes to the current user/users are also updated.
	header( "Location: addUser.php" );
	exit();
	
}





?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - Add User</title>
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
$PageTitle = "Dispute Track - Manage Users";

if($accesslevel >= 7){
	include("assets/includes/HTMLscript.php");
}


include("assets/includes/autologout.php");

include("assets/includes/loadingHTML.php");

?>

<script>
	
	//Hide both Edit and Add rows, show only the one selected
	function AddorEdit(){
		
		var currID = document.getElementById('AddorEdit_UserID');
		var currValue = currID.options[currID.selectedIndex].value;
		
		
		var a = document.getElementById('AddUsersID');
		a.style.display = 'none';
		
		var b = document.getElementById('CurrentUsersID');
		b.style.display = 'none';
		
		
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
                    <div class="col-md-5">
                     <h2>Add and Edit Users:</h2> 
                    </div>
					<div class="col-md-7">
<?php
 //Session Error
 
//$_SESSION["EditSuccess"] = "Test";

if(isset($_SESSION["ADD_DISPUTE_ERROR"])){

	print "<h2><span style='color:red'>" . $_SESSION["ADD_DISPUTE_ERROR"] . "</span></h2>";
	
	unset($_SESSION["ADD_DISPUTE_ERROR"]);
	unset($_SESSION["TransactionAdded"]);
	
}
else if(isset($_SESSION["EditSuccess"])){
	
	print "<h2><span style='color:blue'>" . $_SESSION["EditSuccess"] . "</span></h2>";
	
	unset($_SESSION["EditSuccess"]);
	
}


?>

					</div>
                </div>
                
				<hr />
				<div class="row">
                    <div class="col-md-4">
						<label>Do you want to Add a User or Edit Current Users:</label>
						<select id="AddorEdit_UserID" name="AddorEdit_User" class="form-control" onchange="AddorEdit();">
							<option value="select" selected="select">Select One</option>
							<option value="Add">Add a User</option>
							<option value="Edit">Edit Users</option>
						</select>
					</div>
				</div>
				<hr />
				
				<!-- ================================================================================================================== -->
				
				<div id='CurrentUsersID' style="display:none">
				<form id="EditUser_ID" autocomplete="off" name="EditUser_FormName" method="post" action="addUser.php">
					<div class="row">
						<div class="col-md-12">
							<label>Current Users Table:</label>
							<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>
								<thead>
									<tr>
									<th style='width:20%;'>Username</th>
									<th style='width:15%;'>First Name</th>
									<th style='width:15%;'>Last Name</th>
									<th style='width:15%;'>Access Level</th>
									<th style='width:20%;'>Location</th>
									<th style='width:15%;'>Inactive</th>
									</tr>
								</thead>
								<tbody id='tableBODY'>
<?php

//Print Table rows
for($i=0; $i < $Numberofusers; $i++){
	
	if($users_data[$i][6] == 0){ //User inactive = False
		print '<tr>';
	}
	else{
		print '<tr class="danger">';
	}
	
	
	print '<input type="hidden" name="UserIDs[]" value="'.$users_data[$i][0].'">';
	print '<td><input class="form-control" name="User_Usernames[]" rows="2" value="'.$users_data[$i][1].'" maxlength="255"></td>';
	print '<td><input class="form-control" name="User_Firstnames[]" rows="2" value="'.$users_data[$i][2].'" maxlength="255"></td>';
	print '<td><input class="form-control" name="User_Lastnames[]" rows="2" value="'.$users_data[$i][3].'" maxlength="255"></td>';
	print '<td><select name="User_access[]" class="form-control">';
	for ($m=0; $m < $Numberofaccess; $m++){
		
		if($access_data[$m][0] == $users_data[$i][4]){
			print '<option value="' . $access_data[$m][0] . '" selected="true">' . $access_data[$m][1] . '</option>';
		}
		else{
			print '<option value="' . $access_data[$m][0] . '">' . $access_data[$m][1] . '</option>';
		}
	}
	print '</select></td>';
	print '<td><select name="User_location[]" class="form-control">';
	for ($m=0; $m < $NumberofLocation; $m++){
		
		if($location_data[$m][0] == $users_data[$i][5]){
			print '<option value="' . $location_data[$m][0] . '" selected="true">' . $location_data[$m][1] . '</option>';
		}
		else{
			print '<option value="' . $location_data[$m][0] . '">' . $location_data[$m][1] . '</option>';
		}
	}
	print '</select></td>';
	print '<td><select name="User_inactive[]" class="form-control">';
	if($users_data[$i][6] == 0){ //User inactive = False
		print '<option value="FALSE" selected="true">Active</option>';
		print '<option value="TRUE">Inactive</option>';
	}
	else{
		print '<option value="FALSE">Active</option>';
		print '<option value="TRUE" selected="true">Inactive</option>';
	}
	
	print '</tr>';
}


?>
								</tbody>
							</table>
						</div>
					</div>
				<input type="submit" class="btn btn-success" name="EditUser" value="Submit">
				</form>
				</div>
				
				<!-- ================================================================================================================== -->
				
				<div id='AddUsersID' style="display:none">
				<form id="AddUser_ID" autocomplete="off" name="AddUser_FormName" method="post" action="addUser.php">
				<div class="row">
                    <div class="col-md-12">
						<label>Add New User:</label>
					</div>
				</div>
				<div class="row">
                    <div class="col-md-4">
						<label>Username:</label>
						<input type="text" class="form-control" name="AddUserName" maxlength="255">
					</div>
					<div class="col-md-4">
						<label>First Name:</label>
						<input type="text" class="form-control" name="AddUserFirstName" maxlength="255">
					</div>
					<div class="col-md-4">
						<label>Last Name:</label>
						<input type="text" class="form-control" name="AddUserLastName" maxlength="255">
					</div>
					<div class="col-md-4">
						<label>Access Level:</label>
						<select name="AddUserAccess" class="form-control">
							<option value="select" selected="true">Select One</option>
<?php

//Print Access Levels from DB
for ($i=0; $i < $Numberofaccess; $i++){
	
	print '<option value="' . $access_data[$i][0] . '">' . $access_data[$i][1] . '</option>';
	
}

?>
						</select>
					</div>
					<div class="col-md-4">
						<label>Location:</label>
						<select name="AddUserLocation" class="form-control">
							<option value="select" selected="true">Select One</option>
<?php

//Print Locations from DB
for ($i=0; $i < $NumberofLocation; $i++){
	
	print '<option value="' . $location_data[$i][0] . '">' . $location_data[$i][1] . '</option>';
	
}

?>
						</select>
					</div>
				</div>
				<br>
				<input type="submit" class="btn btn-success" name="AddUser" value="Submit">
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
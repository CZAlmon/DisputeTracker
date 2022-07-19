<?php

//Includes
require_once('assets/includes/connection.php');
//Use Sessions to track logins and keep someone logged in.
session_start();

require_once('assets/includes/PHP_head.php');



$CaseQuery = "";
//Check to make sure the user can access this page
if ($accesslevel < 3){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}
elseif ($accesslevel == 3){
	$CaseQuery = "SELECT id FROM checkcases WHERE casedeleted='0' AND userstarted='".$username."'";
}
elseif($accesslevel > 3){
	$CaseQuery = "SELECT id FROM checkcases WHERE casedeleted='0'";
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the access level, please contact the System Admin.';
	header( "Location: index.php" );
	exit();
}


//Get Case data, throw error if you cant query the database
$CaseQuery_Data = $dtcon->query($CaseQuery);
$CaseData = $CaseQuery_Data->fetch_all();

//All var_dumps are for testing purposes, to test values at this point in the program.
//var_dump($CaseData);

if (empty($CaseData)){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with getting the Case ID\'s! Please Contact the System Admin.';
	echo '<script> window.location = "index.php";</script>';
	//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
	exit();
}

$CaseCount = count($CaseData);







//Use same page for requests
if(($_SERVER["REQUEST_METHOD"] == "POST")){
	
	date_default_timezone_set("America/Chicago");
	$timeofChange = date("Y-m-d, G:i:s");
	
	//var_dump($_POST);
	//print "<br><br>";
	//var_dump($_FILES);
	//print "<br><br>";
	//print $_FILES["Attachments"]["error"];
	//print "<br><br>";
	//exit();
	
	if(isset($_POST["AddAttachment"])){
		
		//Add Attachments
		
		if(!isset($_POST["CaseIDValue"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Case ID Number must be entered!';
			header( "Location: addAttachments.php" );
			exit();
		}
		else{
			$CaseNumberVAR = $_POST["CaseIDValue"];
			
			if (!is_numeric($CaseNumberVAR)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Case ID Number can be Numbers only!';
				header( "Location: addAttachments.php" );
				exit();
			}
			
			$caseNum_query = "SELECT id FROM checkcases";
			$caseNum_query_data = $dtcon->query($caseNum_query);
			$caseNum_data = $caseNum_query_data->fetch_all();
			
			
			if(!deep_in_array($CaseNumberVAR, $caseNum_data)){
				
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Case ID Number wasn\'t found! Please check the ID value and contact the System Admin if you have questions.';
				header( "Location: addAttachments.php" );
				exit();
			}
			
		}
		
		if(!isset($_POST["attachmentType"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the attachment is a signed VISA dispute form or a regular attachment!';
			header( "Location: addAttachments.php" );
			exit();
		}
		else{
			$AttachmentTypeVAR = $_POST["attachmentType"];
			
			if ($AttachmentTypeVAR != 'attachment' && $AttachmentTypeVAR != 'disputeForm'){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select whether the attachment is a signed VISA dispute form or a regular attachment! Please contact the System Admin for more help.';
				header( "Location: addAttachments.php" );
				exit();
			}
			
			if($AttachmentTypeVAR == 'disputeForm'){
				$NewAccountBool = 1;
			}
			else{
				$NewAccountBool = 0;
			}
			
		}
		
		if(!isset($_POST["AttachmentDescription"])){
			$AttachmentDescriptionVAR = "";
		}
		else{
			$AttachmentDescriptionVAR = $_POST["AttachmentDescription"];
			
			$stringGood = isAscii($AttachmentDescriptionVAR);
			
			if (!$stringGood){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Some Illegal Characters in the Attachment Comments were Detected! Please try again using regular characters!';
				header( "Location: addAttachments.php" );
				exit();
			}
			
			$AttachmentDescriptionVAR = $dtcon->real_escape_string($AttachmentDescriptionVAR);
			
		}
		
		$AttachmentName = $_FILES['Attachments']['name'];
		$AttachmentError = $_FILES['Attachments']['error'];
		$AttachmentSize = $_FILES['Attachments']['size'];
		$AttachmentTmpName = $_FILES['Attachments']['tmp_name'];
		
		//var_dump($_FILES);
		
		if($AttachmentName == ""){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'You must select a document to attach!';
			header( "Location: addAttachments.php" );
			exit();
		}
		if($AttachmentError != 0){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'There is an error with the file upload! Please contact the System Admin!';
			header( "Location: addAttachments.php" );
			exit();
		}
		if($AttachmentSize > 15000000){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'There is an error with the file upload! Max upload size is 14 MB. Please contact the System Admin with any questions.';
			header( "Location: addAttachments.php" );
			exit();
		}
		
		$checkUploadAddress = "FileFolder/UserAttachedDocuments/";
		
		$attach_query = "SELECT filename FROM checkattachments WHERE caseid='".$CaseNumberVAR."'";
		$attach_query_data = $dtcon->query($attach_query);
		$attach_data = $attach_query_data->fetch_all();
		$attachnumbers = count($attach_data);
		$attachnumbers++;								//Always add 1 because if there is no attachments then it will be attachment 1, so always add 1 to the number to get the next number
		
		$pathparts = explode('.', $AttachmentName);
		$extensionVar = end((array_values($pathparts)));
		
		$newName = "";
		$newnamePATH = "";
		
		if($NewAccountBool == 1){
			$newnamePATH = $checkUploadAddress . "SignedDisputeForm_case_" . $CaseNumberVAR . "_attachment" . $attachnumbers . "." . $extensionVar;
			$newName = "SignedDisputeForm_case_" . $CaseNumberVAR . "_attachment" . $attachnumbers . "." . $extensionVar;
		}
		else{
			$newnamePATH = $checkUploadAddress . "case" . $CaseNumberVAR . "_attachment" . $attachnumbers . "." . $extensionVar;
			$newName = "case" . $CaseNumberVAR . "_attachment" . $attachnumbers . "." . $extensionVar;
		}
		
		if(rename($AttachmentTmpName, $newnamePATH)){
			//File successfully renamed
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'File Move Failed! Please Contact the System Admin.';
			header( "Location: addAttachments.php" );
			exit();
		}
		
		$checkAttachmentInsert = "INSERT INTO checkattachments(id, caseid, filename, filelocation, comments, iddeleted) VALUES (NULL, '".$CaseNumberVAR."', '".$newName."', '".$newnamePATH."', '".$AttachmentDescriptionVAR."', FALSE)";
		
		$TEMP_checkAttachmentInsert = $dtcon->real_escape_string($checkAttachmentInsert);
		
		$NewChangeLogInsert = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$timeofChange."', '".$username."', '".$ipaddress."', '1', '".$CaseNumberVAR."', '".$TEMP_checkAttachmentInsert."')";

		
		if ($dtcon->query($checkAttachmentInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with an Attachment Insert! Please Contact the System Admin!';
			header( "Location: addAttachments.php" );
			exit();
		}
		
		if ($dtcon->query($NewChangeLogInsert) === TRUE) {
			
			//print "Insert Worked";
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Change Log Insert! Please Contact the System Admin! <p hidden>'.$NewChangeLogInsert.'</p>';
			header( "Location: addAttachments.php" );
			exit();
		}
		
		$_SESSION["TransactionAdded"] = 'Attachment added Successfully!';
		
	}
	
	
}


?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - Error</title>
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
$PageTitle = "Dispute Track - Generate Letters";

if($accesslevel >= 7){
	include("assets/includes/HTMLscript.php");
}


include("assets/includes/autologout.php");

include("assets/includes/loadingHTML.php");

?>


</head>
<body>
     
           
          
    <div id="wrapper">
        
		<?php include("assets/includes/head+menu.php"); ?>
        
        <div id="page-wrapper" >
            <div id="page-inner">
                
				<div class="row">
                    <div class="col-md-3">
						<h2>Add Attachments:</h2>   
                    </div>
					<div class="col-md-9">
<?php
 //Session Error

if(isset($_SESSION["ADD_DISPUTE_ERROR"])){

	print "<h2><span style='color:red'>" . $_SESSION["ADD_DISPUTE_ERROR"] . "</span></h2>";
	
	unset($_SESSION["ADD_DISPUTE_ERROR"]);
	
}
else if(isset($_SESSION["TransactionAdded"])){
	
	print "<h2><span style='color:blue'>" . $_SESSION["TransactionAdded"] . "</span></h2>";
	
	unset($_SESSION["TransactionAdded"]);
	
}

?>
					</div>
                </div> 
				
				<hr />
				
				
				<form id="addAttachment_ID" autocomplete="off" enctype="multipart/form-data" name="addAttachment" method="post" action="addAttachments.php" onsubmit="showLoading();">
				
				<div class='row'>
					
					<div class="col-md-3">
						<label>Case Number:</label>&nbsp;<a target="_blank" title="Please select the Case Number you wish to add an attachment to. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<!-- <input type="text" class="form-control" name="CaseIDValue" maxlength="10" id="CaseID_ID" pattern="^[0-9]+$" title="'5', '171'" onblur="this.value = this.value.toUpperCase();" required> -->
						
						<select name="CaseIDValue" class="form-control" required>
							<option value="" selected="true">Select One</option>
<?php

for ($i=0; $i < $CaseCount; $i++){
	
	print '<option value="' . $CaseData[$i][0] . '">Case Number ' . $CaseData[$i][0] . '</option>';
	
}

?>
						<</select>
						
						
					</div>
					
					<div class="col-md-3">
						<label>Is this an attachment or the Signed Dispute Form?</label>&nbsp;<a target="_blank" title="Please select whether the attachment is a Case/Transaction attachment (eg. receipt, credit slip, etc) or the customer signed VISA Dispute Form."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<select name="attachmentType" class="form-control" required>
							<option value="" selected="true">Select One</option>
							<option value="attachment">Case/Transaction Attachment</option>
							<option value="disputeForm">Signed Dispute Form</option>
						</select>
					</div>
					
			    </div>
				
				<hr />
				
				<div class="row">
					
					<div class="col-md-3">
						<label>New Attachment</label>&nbsp;<a target="_blank" title="This field is for a single attachment. You can attach documents from the customer or the customer signed VISA dispute form to the case. Please scan in any documents, and attach them to this page individually. This field is required."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<input type='file' id='Attachments_ID' name='Attachments' value="Browse"></input>
						<input type="button" onclick="location.reload(true);" value="Reset Attachment Selection">
					</div>
					<div class="col-md-9">
						<label>Attachment Comments</label>&nbsp;<a target="_blank" title="This field is for comments about the attachment. These comments are not saved if you do not attach a document. This field is optional."><img src="./assets/img/help-button-icon.png" height="15px"/></a>
						<textarea class='form-control' onblur="this.value = this.value.toUpperCase();" name='AttachmentDescription' rows='2' maxlength='4096'></textarea>
					</div>
				</div>
				
				<hr />
				
				<div class="row">	
					<div class="col-md-3">
						<input type="submit" class="btn btn-primary" name="AddAttachment" value="Submit New Attachment">
					</div>

				</div>
				
				</form>
				
				
				
				
				
				
				
                
                
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

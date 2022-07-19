<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');


if ($accesslevel < 5 && $accesslevel != 1){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}




?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - Reports</title>
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
$PageTitle = "Dispute Track - View Documents";

if($accesslevel >= 7){
	include("assets/includes/HTMLscript.php");
}


include("assets/includes/autologout.php");

include("assets/includes/loadingHTML.php");

?>
   
	<script>
		
		function DocChange(){
			
			var currReasonID = document.getElementById('SelectDocID');
			var currValue = currReasonID.options[currReasonID.selectedIndex].value;
			
			if (currValue == 'select'){
				
				var s = document.getElementById('ViewLetters2');
				
				s.style.display = 'none';
				
				var t = document.getElementById('viewReports2');
				
				t.style.display = 'none';
				
				var p = document.getElementById('viewForms2');
				
				p.style.display = 'none';
				
				var h = document.getElementById('viewAttachments2');
				
				h.style.display = 'none';
				
				var x = document.getElementById('ViewLetters');
			
				x.style.display = 'none';
				
				var y = document.getElementById('viewReports');
			
				y.style.display = 'none';
				
				var o = document.getElementById('viewForms');
				
				o.style.display = 'none';
				
				var i = document.getElementById('viewAttachments');
				
				i.style.display = 'none';
				
				
			}
			else if(currValue == 'PCLetter'){
				
				var s = document.getElementById('ViewLetters2');
				
				s.style.display = 'block';
				
				var t = document.getElementById('viewReports2');
				
				t.style.display = 'none';
				
				var p = document.getElementById('viewForms2');
				
				p.style.display = 'none';
				
				var h = document.getElementById('viewAttachments2');
				
				h.style.display = 'none';
				
				var x = document.getElementById('ViewLetters');
			
				x.style.display = 'block';
				
				var y = document.getElementById('viewReports');
			
				y.style.display = 'none';
				
				var o = document.getElementById('viewForms');
				
				o.style.display = 'none';
				
				var i = document.getElementById('viewAttachments');
				
				i.style.display = 'none';
				
			}
			else if(currValue == 'Report'){
				
				var s = document.getElementById('ViewLetters2');
				
				s.style.display = 'none';
				
				var t = document.getElementById('viewReports2');
				
				t.style.display = 'block';
				
				var p = document.getElementById('viewForms2');
				
				p.style.display = 'none';
				
				var h = document.getElementById('viewAttachments2');
				
				h.style.display = 'none';
				
				var x = document.getElementById('ViewLetters');
			
				x.style.display = 'none';
				
				var y = document.getElementById('viewReports');
			
				y.style.display = 'block';
				
				var o = document.getElementById('viewForms');
				
				o.style.display = 'none';
				
				var i = document.getElementById('viewAttachments');
				
				i.style.display = 'none';
				
			}
			else if(currValue == 'Forms'){
				
				var s = document.getElementById('ViewLetters2');
				
				s.style.display = 'none';
				
				var t = document.getElementById('viewReports2');
				
				t.style.display = 'none';
				
				var p = document.getElementById('viewForms2');
				
				p.style.display = 'block';
				
				var h = document.getElementById('viewAttachments2');
				
				h.style.display = 'none';
				
				var x = document.getElementById('ViewLetters');
			
				x.style.display = 'none';
				
				var y = document.getElementById('viewReports');
			
				y.style.display = 'none';
				
				var o = document.getElementById('viewForms');
				
				o.style.display = 'block';
				
				var i = document.getElementById('viewAttachments');
				
				i.style.display = 'none';
				
			}
			else if(currValue == 'Attach'){
				
				var s = document.getElementById('ViewLetters2');
				
				s.style.display = 'none';
				
				var t = document.getElementById('viewReports2');
				
				t.style.display = 'none';
				
				var p = document.getElementById('viewForms2');
				
				p.style.display = 'none';
				
				var h = document.getElementById('viewAttachments2');
				
				h.style.display = 'block';
				
				var x = document.getElementById('ViewLetters');
			
				x.style.display = 'none';
				
				var y = document.getElementById('viewReports');
			
				y.style.display = 'none';
				
				var o = document.getElementById('viewForms');
				
				o.style.display = 'none';
				
				var i = document.getElementById('viewAttachments');
				
				i.style.display = 'block';
				
			}
			else{
				alert("Something went wrong with the selection, please contact the System Admin.");
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
                     <h2>View Documents:</h2>   
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
	
				<!--  Below H2, above HR  -->
				
                 <!-- /. ROW  -->
                  <hr />
				  
				  <div class="row">
					<div class="col-md-4">
						<label>Select to View the Documents you wish to see:</label>
						<select id="SelectDocID" name="SelectDoc" class="form-control" onchange="DocChange();"> 
							<option value="select" selected="select">Select One</option>
							<option value="PCLetter">Provisional Credit Letters</option>
							<option value="Report">Reports</option>
							<option value="Forms">Generated Visa Dispute Forms</option>
							<option value="Attach">Attachments</option>
						</select> 
					</div>
				</div>
				<hr />
				
				<div class="row" id='viewReports2' style='display:none'>
					<div class="col-md-8">
						<h4><b>Report names are in the format: 'YEAR-MONTH-DAY_HOUR-MINUTE-SECOND_report_Users_Name'</b></h4>
					</div>
				</div>
				<div class="row" id='ViewLetters2' style='display:none'>
					<div class="col-md-8">
						<h4><b>Letter names are in the format: 'case_###_PCLetter'</b></h4>
					</div>
				</div>
				<div class="row" id='viewForms2' style='display:none'>
					<div class="col-md-8">
						<h4><b>Dispute form names are in the format: 'case#_disputeform'</b></h4>
					</div>
				</div>
				<div class="row" id='viewAttachments2' style='display:none'>
					<div class="col-md-8">
						<h4><b>Attachment names are in the format: 'case###_attachment###'</b></h4>
					</div>
				</div>
				
				<hr />
				
				
				
				
				
				
				
				  
				  <!--  Main Body  -->

<?php



$ReportDirPath = './FileFolder/GeneratedReports/';
$LetterDirPath = './FileFolder/ProvisionalCreditLetters/';

$GeneratedFormPath = './FileFolder/VisaCheckCardDisputeForms/';

$AttachmentsPath = './FileFolder/UserAttachedDocuments/';

$ReportDirFiles = scandir($ReportDirPath);

$ReportDirFiles_rev = array_reverse($ReportDirFiles);

$LetterDirFiles = scandir($LetterDirPath);

$LetterDirFiles_rev = array_reverse($LetterDirFiles);

$GeneratedFormFiles = scandir($GeneratedFormPath);

$GeneratedFormFiles_rev = array_reverse($GeneratedFormFiles);

$AttachmentsFiles = scandir($AttachmentsPath);

$AttachmentsFiles_rev = array_reverse($AttachmentsFiles);

// =============================================================================

print "<div class='row' id='ViewLetters' style='display:none'>";
print "<div class='col-md-12'>";
print "<label align = 'center'>All Provision Credit Letters:</label>\n";

foreach($LetterDirFiles_rev as $fileName){
	
	if ($fileName == "."){
		
	}
	else if ($fileName == ".."){
		
	}
	else if ($fileName == "index.html"){
		
	}
	else{
		print "<p><a target='_blank' href=\"./FileFolder/ProvisionalCreditLetters/" . $fileName . "\"> " . $fileName . "</a></p>";
		//<pre><a target='_blank' href=\"./FileFolder/ProvisionalCreditLetters/" . $fileName . "\"> " . $fileName . "</a>			".date("F d Y H:i:s.", filectime('FileFolder/ProvisionalCreditLetters/'.$filename))."</a></pre>
	}
	
}
print "</div>";
print "</div>";

// ==============================================================================

print "<div class='row' id='viewReports' style='display:none'>";
print "<div class='col-md-12'>";
print "<label align = 'center'>All Reports:</label>\n";

foreach($ReportDirFiles_rev as $fileName){
	
	if ($fileName == "."){
		
	}
	else if ($fileName == ".."){
		
	}
	else if ($fileName == "index.html"){
		
	}
	else{
		print "<p><a target='_blank' href=\"./FileFolder/GeneratedReports/" . $fileName . "\"> " . $fileName . "</a></p>";
	}

}
print "</div>";
print "</div>";


// =============================================================================


print "<div class='row' id='viewForms' style='display:none'>";
print "<div class='col-md-12'>";
print "<label align = 'center'>All Dispute Forms:</label>\n";
//print count($GeneratedFormFiles);

foreach($GeneratedFormFiles_rev as $fileName){
	
	if ($fileName == "."){
		
	}
	else if ($fileName == ".."){
		
	}
	else if ($fileName == "index.html"){
		
	}
	else{
		print "<p><a target='_blank' href=\"./FileFolder/VisaCheckCardDisputeForms/" . $fileName . "\"> " . $fileName . "</a></p>";
	}

}
print "</div>";
print "</div>";


// =============================================================================


print "<div class='row' id='viewAttachments' style='display:none'>";
print "<div class='col-md-12'>";
print "<label align = 'center'>All Attachments:</label>\n";
//print count($AttachmentsFiles);

foreach($AttachmentsFiles as $fileName){
	
	if ($fileName == "."){
		
	}
	else if ($fileName == ".."){
		
	}
	else if ($fileName == "index.html"){
		
	}
	else{
		print "<p><a target='_blank' href=\"./FileFolder/UserAttachedDocuments/" . $fileName . "\"> " . $fileName . "</a></p>";
	}

}
print "</div>";
print "</div>";






// ==============================================================================


?>
					
					
					
					
					
					
					
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

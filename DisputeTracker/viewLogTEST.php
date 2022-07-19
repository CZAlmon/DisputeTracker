<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');



if ($accesslevel < 7){
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
    <title>Dispute Tracker - View Log</title>
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
	<script>
		
		function caseSearch(){
			
			var input, filter, table, tr, td, i;
			
			input = document.getElementById("caseinput");
			filter = input.value.toUpperCase();
			table = document.getElementById("myTable");
			tr = table.getElementsByTagName("tr");
			
			
			for (i = 0; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td")[1];
				if (td) {
					if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
					}
					else {
						tr[i].style.display = "none";
					}
				}
			}
			
			//Search all 6 fields at once.
			/*
			for (i = 0; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td")[0];
				td1 = tr[i].getElementsByTagName("td")[1];
				td2 = tr[i].getElementsByTagName("td")[2];
				td3 = tr[i].getElementsByTagName("td")[3];
				td4 = tr[i].getElementsByTagName("td")[4];
				td5 = tr[i].getElementsByTagName("td")[5];
				if (td || td1 || td2 || td3 || td4 || td5) {
					if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
					} 
					else if (td1.innerHTML.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
					}
					else if (td2.innerHTML.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
					}
					else if (td3.innerHTML.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
					}
					else if (td4.innerHTML.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
					}
					else if (td5.innerHTML.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
					}
					else {
						tr[i].style.display = "none";
					}
				}
			}
			*/
			
		}
		
		function dateSearch(){
			
			var input, filter, table, tr, td, i;
			
			input = document.getElementById("dateinput");
			filter = input.value.toUpperCase();
			table = document.getElementById("myTable");
			tr = table.getElementsByTagName("tr");
			
			
			for (i = 0; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td")[2];
				if (td) {
					if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
					}
					else {
						tr[i].style.display = "none";
					}
				}
			}
		}
		
		function userSearch(){
			
			var input, filter, table, tr, td, i;
			
			input = document.getElementById("userinput");
			filter = input.value.toUpperCase();
			table = document.getElementById("myTable");
			tr = table.getElementsByTagName("tr");
			
			
			for (i = 0; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td")[3];
				if (td) {
					if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
					}
					else {
						tr[i].style.display = "none";
					}
				}
			}
		}
		
	</script>
   
<?php

//This Variable is for the Title
$PageTitle = "Dispute Track - View Log";

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
                    <div class="col-md-2">
                     <h2>View Change Log</h2> 
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
				<hr />

                 <!-- /. ROW  -->
                  
<?php

// ---------------------------------------------------------------------------------------------
$log_select = "SELECT * FROM changelog ORDER BY id";
$log_fetch = $dtcon->query($log_select);

$log_items = $log_fetch->fetch_all();
// ---------------------------------------------------------------------------------------------


$NumberofLogs = count($log_items);

$AllLogsArray = array();

for ($i=0; $i < $NumberofLogs; $i++){
	
	$temparr_1 = array();
	
	$LOG_IDtemp = $log_items[$i][0];	
	$ChangeDatetmp = $log_items[$i][1];
	$UserNametmp = $log_items[$i][2];
	$IPAddrtmp = $log_items[$i][3];
	$LogTypetmp = $log_items[$i][4];
	$CaseIDtmp = $log_items[$i][5];
	$ChangeDetailTmp = $log_items[$i][6];
	
	array_push($temparr_1, $LOG_IDtemp);
	array_push($temparr_1, $CaseIDtmp);
	array_push($temparr_1, $ChangeDatetmp);
	array_push($temparr_1, $UserNametmp);
	array_push($temparr_1, $IPAddrtmp);
	
	if($LogTypetmp == '1'){
		array_push($temparr_1, 'Addition');
	}
	else if($LogTypetmp == '2'){
		array_push($temparr_1, 'Change');
	}
	else if($LogTypetmp == '3'){
		array_push($temparr_1, 'Deletion');
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = "Log Type Error! Please contact the system Admin!";
		echo '<script> window.location = "index.php";</script>';
		//header( "Location: index.php" ); //HEADER ONLY WORKS Before HTML Content
		exit();
	}
	
	array_push($temparr_1, $ChangeDetailTmp);
	
	array_push($AllLogsArray, $temparr_1);

}

$NewChunkedArray = array_chunk($AllLogsArray, 25);

?>
				
				<div class='row'>
						<div class="col-md-2">
							<input type="text" class="form-control" id="caseinput" onkeyup="caseSearch()" placeholder="Search for case IDs..">
						</div>
						<div class="col-md-2">
							<input type="text" class="form-control" id="dateinput" onkeyup="dateSearch()" placeholder="Search by Date..">
						</div>
						<div class="col-md-2">
							<input type="text" class="form-control" id="userinput" onkeyup="userSearch()" placeholder="Search for users..">
						</div>
				</div>
				
				<br>
				
				<div class="row">
					<div class='col-md-12'>
						<!-- <h5>Click on the page you want to see.</h5> -->
						
						<div class="tab-content">
							
							<table id="myTable" style='width:100%' class='order-table table table-striped table-bordered table-hover'>
							
							<tr class="header">
								<th style="width:5%;">Log ID</th>
								<th style="width:5%;">Case ID</th>
								<th style="width:10%;">Change Date</th>
								<th style="width:10%;">User</th>
								<th style="width:5%;">IP Address</th>
								<th style="width:5%;">Log Type</th>
								<th style="width:60%;">Change Detail</th>
							</tr>
							
<?php

//$NewChunkedArray = array_chunk($AllLogsArray, 25);

$LogCount = count($AllLogsArray);

for($i=0; $i < $LogCount; $i++){
	
	print "<tr>";
	print "<td>".$AllLogsArray[$i][0]."</td>";
	print "<td>".$AllLogsArray[$i][1]."</td>";
	print "<td>".$AllLogsArray[$i][2]."</td>";
	print "<td>".$AllLogsArray[$i][3]."</td>";
	print "<td>".$AllLogsArray[$i][4]."</td>";
	print "<td>".$AllLogsArray[$i][5]."</td>";
	print "<td>".$AllLogsArray[$i][6]."</td>";
	print "</tr>";
}




?>
							</table>
						</div>
						
						<!-- Old Paged style logs
						
						<ul class="nav nav-tabs">
<?php

$MainCount = 1;

foreach($NewChunkedArray as $tmpLogArray){
	
	if($MainCount == 1){
		print "<li class='active'><a href='#page".$MainCount."' data-toggle='tab'>Page ".$MainCount."</a></li>";
	}
	else{
		print "<li class=''><a href='#page".$MainCount."' data-toggle='tab'>Page ".$MainCount."</a></li>";
	}
	
	$MainCount++;
}


?>
						</ul>
						<div class="tab-content">
<?php


$pageCount = 1;

foreach($NewChunkedArray as $tmpLogArray){
	
	$tmpCount = count($tmpLogArray);
	
	if($pageCount == 1){
		print "<div class='tab-pane fade active in' id='page".$pageCount."'>";
	}
	else{
		print "<div class='tab-pane fade' id='page".$pageCount."'>";
	}
	
	print "<h4>Page ".$pageCount."</h4>";
	print "<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>";
	print "<thead>";
	print "<tr>";
	print "<th>Log ID Number</th>";
	print "<th>Case ID Number</th>";
	print "<th>Change Date</th>";
	print "<th>User</th>";
	print "<th>IP Address</th>";
	print "<th>Log Type</th>";
	print "<th>Change Detail</th>";
	print "</tr>";
	print "</thead>";
	
	print "<tbody id='tableBODY'>";

	for($i=0; $i < $tmpCount; $i++){
		
		print "<tr>";
		print "<td>".$tmpLogArray[$i][0]."</td>";
		print "<td>".$tmpLogArray[$i][1]."</td>";
		print "<td>".$tmpLogArray[$i][2]."</td>";
		print "<td>".$tmpLogArray[$i][3]."</td>";
		print "<td>".$tmpLogArray[$i][4]."</td>";
		print "<td>".$tmpLogArray[$i][5]."</td>";
		print "<td>".$tmpLogArray[$i][6]."</td>";
		print "</tr>";
	}

	print "</tbody>";
	print "</table>";
	print "</div>";
	
	$pageCount++;
	
}









?>
						</div>
						
						-->
						
					</div>
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
<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');




if ($accesslevel != 3 && $accesslevel != 9){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}





// ---------------------------------------------------------------------------------------------
$case_select = "SELECT checkcases.id, checkcases.casestartdate, checkcases.custfname, checkcases.custlname, checkcases.custphone, checkaccountnumbers.accountnumber, checkcardnumbers.id, checkcardnumbers.cardnumber, checkcases.casedeleted, checkcases.casedoneinput FROM checkcases, checkaccountnumbers, checkcardnumbers WHERE checkcases.id = checkaccountnumbers.caseid AND checkcases.id = checkcardnumbers.caseid AND checkcases.casedeleted = '0' AND userstarted='".$username."' ORDER BY checkcases.id";
$case_fetch = $dtcon->query($case_select);

$case_items = $case_fetch->fetch_all();
// ---------------------------------------------------------------------------------------------
$transaction_select = "SELECT caseid, cardid, amount, transactiondate, transactiondeleted FROM checktransactions WHERE transactiondeleted = '0' ORDER BY caseid";
$transaction_fetch = $dtcon->query($transaction_select);

$transaction_items = $transaction_fetch->fetch_all();
// ---------------------------------------------------------------------------------------------


//var_dump($case_items);

$NumberofCases = count($case_items);
$NumberofTransactions = count($transaction_items);

$AllDisputesArray = array();


for ($i=0; $i < $NumberofCases; $i++){
	
	if($case_items[$i][8] == 0){
		$temparr_1 = array();
		$caseIDtemp = $case_items[$i][0];	
		$caseDatetmp = $case_items[$i][1];
		$caseFNametmp = $case_items[$i][2];
		$caseLNametmp = $case_items[$i][3];
		$casePhonetmp = $case_items[$i][4];
		$checkAcctmp = $case_items[$i][5];
		$checkCardIDtmp = $case_items[$i][6];
		$checkCardtmp = $case_items[$i][7];
		$checkCaseDone = $case_items[$i][9];
		
		array_push($temparr_1, $caseIDtemp);
		array_push($temparr_1, $caseDatetmp);
		array_push($temparr_1, $caseFNametmp);
		array_push($temparr_1, $caseLNametmp);
		array_push($temparr_1, $casePhonetmp);
		array_push($temparr_1, $checkAcctmp);
		array_push($temparr_1, $checkCardIDtmp);
		array_push($temparr_1, $checkCardtmp);
		array_push($temparr_1, $checkCaseDone);
		
		//$transactionTmp = array();
		
		for($m=0; $m < $NumberofTransactions; $m++){
			if($transaction_items[$m][4] == 0){
				if($caseIDtemp == $transaction_items[$m][0]){
					array_push($temparr_1, $transaction_items[$m]);
				}
			}
		}
		
		//array_push($temparr_1, $transactionTmp);
		
		array_push($AllDisputesArray, $temparr_1);
		
	}

}

//var_dump($AllDisputesArray);

$DisputeCount = count($AllDisputesArray);




?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - View Disputes</title>
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
$PageTitle = "Dispute Track - View Disputes";

if($accesslevel >= 7){
	include("assets/includes/HTMLscript.php");
}


include("assets/includes/autologout.php");



?>

   
   
   
</head>
<body>
     
           
          
    <div id="wrapper">
	
        <?php include("assets/includes/head+menu.php"); ?>
		
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-5">
                     <h2>CSR View Disputes</h2>   
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
				<hr />
                
				<!-- /. ROW  -->
                  
				
				
				  <!--  Main Body  -->

<?php




// ---------------------------------------------------------------------------------------------------------


print "<div class='row' id='ViewALLTable' style='display:block'>";
print "<div class='col-md-12'>";
print "<label>All Disputes: [Red Rows means case is not done yet.]</label>";
print "<table style='width:100%' class='order-table table table-striped table-bordered table-hover'>";
print "<thead>";
print "<tr>";
print "<th style='width:5%;'>Case ID</th>";
print "<th style='width:10%;'>Case Date</th>";
print "<th style='width:10%';'>Customer Name</th>";
print "<th style='width:10%;'>Customer Phone</th>";
print "<th style='width:10%;'>Account Number</th>";
print "<th style='width:10%;'>Card Number</th>";
print "<th style='width:13%;'><div style='float:left; width:50%; text-align:left'>Transaction Date</div><div style='float:right; width:50%; text-align:right'>Amount</div></th>";
print "<th style='width:12%;'>View details</th>";
print "</tr>";
print "</thead>";
print "<tbody id='tableBODY'>";

for($i=$DisputeCount-1; $i >= 0; $i--){
	
	if($AllDisputesArray[$i][8] == "1"){
		print '<tr>';
	}
	else{
		print '<tr class="danger">';
	}
	
	print "<td>".$AllDisputesArray[$i][0]."</td>";
	print "<td>".$AllDisputesArray[$i][1]."</td>";
	print "<td>".$AllDisputesArray[$i][2]." ".$AllDisputesArray[$i][3]."</td>";
	print "<td>".$AllDisputesArray[$i][4]."</td>";
	print "<td>".$AllDisputesArray[$i][5]."</td>";
	print "<td>".$AllDisputesArray[$i][7]."</td>";
	
	$tmpNum = count($AllDisputesArray[$i]);
	
	print "<td>";
	for($m=9; $m < $tmpNum; $m++){
		print "<div style='float:left; width:50%; text-align:left'>";
		print $AllDisputesArray[$i][$m][3];
		print "</div>";
		
		print "<div style='float:right; width:50%; text-align:right'>";
		print $AllDisputesArray[$i][$m][2];
		print "</div>";
	}
	print "</td>";

	print "<td><form method='post' action='csrviewcase.php'><button type='submit' name='ViewSubmit' value='".$AllDisputesArray[$i][0]."' class='fa fa-external-link-square btn btn-primary'></button></form></td>";
		
	print "</tr>";
}

print "</tbody>";
print "</table>";
print "</div>";
print "</div>";


?>
					
					
					
					
			</div>
	<!--  Footer  -->
<p></p>
	
             <!-- /. PAGE INNER  -->
            </div>
         <!-- /. PAGE WRAPPER  -->
        </div>

		<!--  Bottom of Menu Nav  -->
		
     <!-- /. WRAPPER  -->

    
   
</body>

</html>
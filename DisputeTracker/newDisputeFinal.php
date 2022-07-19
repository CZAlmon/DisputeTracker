<?php

require_once('assets/includes/connection.php');
require('assets/includes/FPDF/fpdf.php');

require_once('assets/includes/mailheader.php');
require_once('assets/includes/PHPMailer/PHPMailerAutoload.php');

session_start();

require_once('assets/includes/PHP_head.php');



if ($accesslevel < 3){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}




//Can't access this page without that variable from newDisputetransactions.php
if(!isset($_SESSION['FinishDispute'])){
	$_SESSION["ADD_DISPUTE_ERROR"] = "You can't access that page. Please contact the System Admin.";
	unset($_SESSION["WorkingDispute"]);
	unset($_SESSION["Dispute_CaseID"]);
	unset($_SESSION['FinishDispute']);
	unset($_SESSION['CaseCanBeDone']);
	//unset($_SESSION['ADD_DISPUTE_ERROR']);
	header( "Location: index.php" );
}

//Can't access this page without that variable from newDisputetransactions.php
if(!isset($_SESSION['WorkingDispute'])){
	$_SESSION["ADD_DISPUTE_ERROR"] = "You currently aren't working on a dispute. Please contact the System Admin.";
	unset($_SESSION["WorkingDispute"]);
	unset($_SESSION["Dispute_CaseID"]);
	unset($_SESSION['FinishDispute']);
	unset($_SESSION['CaseCanBeDone']);
	//unset($_SESSION['ADD_DISPUTE_ERROR']);
	header( "Location: index.php" );
}


//-----------------------------------------------------------------

$caseID = 0;
$accountNumber = 0;
$cardNumber = 0;
$cardPossessionVar = 0;


//Currently Working Case Session
//$_SESSION["WorkingDispute"] = 'True';
//$_SESSION["Dispute_CaseID"] = $caseID;

$caseID = $_SESSION["Dispute_CaseID"];
//$caseID = 5;

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

$CardNumQuery = "SELECT id, cardnumber, cardpossession, chipcard FROM checkcardnumbers where caseid='" . $caseID . "'";
$CardNumQuery_Data = $dtcon->query($CardNumQuery);
$CardNumData = $CardNumQuery_Data->fetch_all();

//var_dump($CardNumData);

if (!empty($CardNumData)){
	if (!empty($CardNumData[0])){
		//$cardID_Variable = $CardNumData[0][0];
		$cardNumber = $CardNumData[0][1];
		$cardPossessionVar = $CardNumData[0][2];
		$chipCardBOOL = $CardNumData[0][3];
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

//var_dump($chipCardBOOL);
//exit();

//--------------------------------------------------------------------

$caseINFO_query = "SELECT casestartdate, custfname, custlname, custphone, custemail FROM checkcases WHERE id='" . $caseID . "'";
$caseINFO_Data = $dtcon->query($caseINFO_query);
$caseINFOData = $caseINFO_Data->fetch_all();

if (empty($caseINFOData)){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Case ID Was not found! Please Contact the System Admin. ';
	header( "Location: newDispute.php" );
	exit();
}



$transactionsINFO_query = "SELECT id, cardid, amount, transactiondate, dateposted, disputereason, description, merchantname, merchantcontacted, merchantcontacteddate, merchantcontactdescription, receiptstatus FROM checktransactions WHERE caseid='" . $caseID . "'";
$transactionsINFO_Data = $dtcon->query($transactionsINFO_query);
$transactionsINFOData = $transactionsINFO_Data->fetch_all();

if (empty($transactionsINFOData)){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Case ID Was not found! Please Contact the System Admin.';
	header( "Location: newDispute.php" );
	exit();
}



//Update Case Row that case is done.
$caseUpdate = "UPDATE checkcases SET casedoneinput = TRUE WHERE id='" . $caseID . "'";

if ($dtcon->query($caseUpdate) === TRUE) {
	
	//print "Insert Worked";
}
else{
	$_SESSION["ADD_DISPUTE_ERROR"] = 'Error! Something went wrong with the Case Update! Please Contact the System Admin!';
	header( "Location: newDispute.php" );
	exit();
}

/*

Add User Change Log For this statement

*/



$caseDate = explode(',', $caseINFOData[0][0]);


$PDFTitle = "Visa CheckCard Dispute Form";
$PDFCase = "Case Number: ".$caseID;

$FirstLine = "I have reviewed the transactions posted to my account and dispute the following items:";

$NameLine = "Name: ".$caseINFOData[0][1]." ".$caseINFOData[0][2]."";
$DateLine = "Date: ".$caseDate[0];
$PhoneLine = "Phone: ".$caseINFOData[0][3];

$emailBOOL = 0;

if($caseINFOData[0][4] == ''){
	$emailBOOL = 1;
}

$EmailLine = "Email: ".$caseINFOData[0][4];

$AccountLine = "Account Number: ";
$CardLine = "Card Number: ";

if($chipCardBOOL == '1'){
	$ChipCardLine = "Chip Card: YES";
}
else{
	$ChipCardLine = "Chip Card: NO";
}


$MERCHANTCONTACT_1 = "I have contacted the merchant on ";
$MERCHANTCONTACT_2 = " and requested that my account be credited";

$FirstReason = "I certify that the transaction listed above was not made by me or a person whom I gave my card.";
$SecondReason = "The amount of the transaction was increased or my sales slip was added incorrectly.";
$ThirdReason = "I have not received the merchandise which was to have been shipped to me.";
$FourthReason = "The attached credit slip was listed as a transaction (debit) to my account.";
$FifthReason = "I was issued a credit slip which was not posted on my account. A copy of my credit slip is attached.";
$SixthReason = "I certify that the transaction in question was a single item, but was posted multiple times to my account. I did not authorize the additional transaction(s).";
$SeventhReason = "I notified the merchant to cancel my reservation. My cancellation number is: _____________________";
$EighthReason = "Although I did engage in a transaction at the merchant, I was billed for ________ transaction(s) totaling \$___________ that I did not engage in, nor did anyone else authorized to use my card. ";
$EighthReason_sub1 = "I do have all my cards in my possession.";
$EighthReason_sub2 = "I do not have all my cards in my possession.";
$NinthReason = "Merchandise which was shipped to me has arrived damaged and/or defective. I returned the merchandise on _____________ and have requested the merchant to credit my account";
$TenthReason = "Other - Not Covered: (Attach additional sheets and any supporting documents as necessary)";

$ReasonsArray = array("", $FirstReason, $SecondReason, $ThirdReason, $FourthReason, $FifthReason, $SixthReason, $SeventhReason, $EighthReason, $NinthReason, $TenthReason);


// Start PDF Formatting -------------------------------------------------------------------------------------


$page_height = 286.93; //Height total of a PDF page

$pdf = new FPDF();
$pdf->SetLineWidth(.5);
$pdf->AddPage('P','Letter', 0);
$pdf->AliasNbPages();
//$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->Image('assets/img/logo.png', NULL, NULL, 50, 10, 'PNG');
$pdf->SetFont('Arial','B',15);
$pdf->Cell(80);
$pdf->Cell(30,10,$PDFTitle,0,0,'C');
$pdf->SetFont('Times','B',10);
$pdf->Cell(0,10,$PDFCase,0,0, 'R');
$pdf->Line(5,30,205,30);
$pdf->Ln(10);

$pdf->Cell(65,10,$NameLine,0,0);
$pdf->Cell(65,10,$DateLine,0,0);
$pdf->Cell(65,10,$PhoneLine,0,0);
$pdf->Ln(5);

//If 'emailbool == 0' then there is an email, so load that into the page, otherwise dont.
if ($emailBOOL == 0){
	$pdf->Cell(0,10,$EmailLine,0,0);
	$pdf->Ln(5);
}

//$pdf->Cell(0,10,$EmailLine,0,0);
//$pdf->Ln(5);


$pdf->Cell(65,10,$AccountLine." ".$accountNumber,0,0);
$pdf->Cell(65,10,$CardLine." ".$cardNumber,0,0);
$pdf->Cell(20,10,$ChipCardLine,0,0);

//$pdf->SetY(37, FALSE);
//$pdf->Cell(5,5,' ',1,0);

$pdf->Ln(8);
$pdf->Cell(0,10,$FirstLine,0,0);

if ($emailBOOL == 0){
	$pdf->Line(10,65,200,65);
	$pdf->Ln(20);
}
else{
	$pdf->Line(10,60,200,60);
	$pdf->Ln(20);
}

$pdf->SetFont('Times','',10);

$itemCount = count($transactionsINFOData);
$itemCounter = 0;

foreach($transactionsINFOData as $TransactionArray){
	
	$itemCounter++;
	
	$TmpAmount = $TransactionArray[2];
	$TmpTransDate = $TransactionArray[3];
	$TmpPostDate = $TransactionArray[4]; 
	//-----
	$TmpDisReason = $TransactionArray[5];
	$TmpDisDescrip = $TransactionArray[6];
	
	$TmpMerchName =$TransactionArray[7];
	$TmpMerchContact = $TransactionArray[8];
	$TmpMerchContactDATE = $TransactionArray[9];
	$TmpMerchDescrip = $TransactionArray[10];
	$TmpReceipt = $TransactionArray[11];
	
	
	
	$pdf->SetFont('Times','B',10);
	$pdf->Cell(0,10,'Merchant Name: '.$TmpMerchName,0,0);
	$pdf->Ln(5);
	$pdf->Cell(40,10,'Amount $'.$TmpAmount);
	$pdf->Cell(60,10,'Transaction Date: '.$TmpTransDate);
	//$pdf->Cell(0,10,'Posted Date: '.$TmpPostDate);
	
	if($TmpPostDate == ""){
		$pdf->Cell(0,10,'');
	}
	else{
		$pdf->Cell(0,10,'Posted Date: '.$TmpPostDate);
	}
	
	
	
	$pdf->SetFont('Times','',10);
	
	$pdf->Ln(8);
	
	
	$TmpREASONString = $ReasonsArray[$TmpDisReason];
	
	if($TmpDisReason == "8"){
		
		if($cardPossessionVar == "1"){
			$pdf->MultiCell(0,5,$EighthReason.$EighthReason_sub1);
			$pdf->Ln(5);
		}
		else{
			$pdf->MultiCell(0,5,$EighthReason.$EighthReason_sub2);
			$pdf->Ln(5);
		}
		
		$pdf->SetFont('Times','B',10);
		$pdf->Cell(20,5,"Description: ");
		$pdf->SetFont('Times','',10);
		
		$pdf->MultiCell(0,5,$TmpDisDescrip);
		$pdf->Ln(5);
		
		if($TmpMerchContact == "1"){ //1 == TRUE To contact
			$pdf->Cell(50,5,$MERCHANTCONTACT_1,0,0);
			$pdf->Cell(20,5,$TmpMerchContactDATE,0,0);
			$pdf->Cell(0,5,$MERCHANTCONTACT_2,0,0);
			$pdf->Ln(8);
			
			$pdf->SetFont('Times','B',10);
			$pdf->Cell(35,5,"Merchant Description:");
			$pdf->SetFont('Times','',10);
			
			$pdf->MultiCell(0,5,$TmpMerchDescrip);
			$pdf->Ln(5);
		}
		else{
			$pdf->SetFont('Times','B',10);
			$pdf->Cell(35,5,"Merchant Description:");
			$pdf->SetFont('Times','',10);
			
			$pdf->MultiCell(0,5,$TmpMerchDescrip);
			$pdf->Ln(5);
		}
	}
	else if($TmpDisReason == "9"){
		
		$pdf->MultiCell(0,5,$NinthReason);	
		
		$pdf->SetFont('Times','B',10);
		$pdf->Cell(20,5,"Description: ");
		$pdf->SetFont('Times','',10);
		
		$pdf->MultiCell(0,5,$TmpDisDescrip);
		$pdf->Ln(5);
		
		$pdf->SetFont('Times','B',10);
		$pdf->Cell(35,5,"Merchant Description:");
		$pdf->SetFont('Times','',10);
		
		$pdf->MultiCell(0,5,$TmpMerchDescrip);
		$pdf->Ln(5);
		
	}
	else{
		
		$pdf->MultiCell(0,5,$TmpREASONString);
		$pdf->Ln(5);
		
		$pdf->SetFont('Times','B',10);
		$pdf->Cell(20,5,"Description: ");
		$pdf->SetFont('Times','',10);
		
		$pdf->MultiCell(0,5,$TmpDisDescrip);
		$pdf->Ln(5);
		if($TmpMerchContact == "1"){ //1 == TRUE To contact
			$pdf->Cell(50,5,$MERCHANTCONTACT_1,0,0);
			$pdf->Cell(20,5,$TmpMerchContactDATE,0,0);
			$pdf->Cell(0,5,$MERCHANTCONTACT_2,0,0);
			$pdf->Ln(8);
			
			$pdf->SetFont('Times','B',10);
			$pdf->Cell(35,5,"Merchant Description:");
			$pdf->SetFont('Times','',10);
			
			$pdf->MultiCell(0,5,$TmpMerchDescrip);
			$pdf->Ln(5);
		}
		else{
			$pdf->SetFont('Times','B',10);
			$pdf->Cell(35,5,"Merchant Description:");
			$pdf->SetFont('Times','',10);
			
			$pdf->MultiCell(0,5,$TmpMerchDescrip);
			$pdf->Ln(5);
		}
	}
	
	$currPageHeight = $pdf->GetY();
	$space_left = $page_height - $currPageHeight;
	
	//var_dump($space_left);
	
	if($space_left < 100){		//if the space left on page is less than 50 mm minimum for a transaction
								//Adjust 50 to play with spacing
		$pdf->AddPage('P','Letter', 0);		//Page Break/New Page
	}
	
	
	if($itemCounter != $itemCount){
		$pdf->Cell(0,0,'',1,0);
		$pdf->Ln(5);
	}
	
}

$SigLine = "X______________________________________      ____________________________      ____________________________";
$SigLine2 = "   Signature                                                                   Date                                                      Telephone Number Work/Home";
$LastLine = "Please forward the completed form with and necessary supporting documents to operations: Copy of sales recipt and Bank statement(s) effected.";
$EmployeeSigLine = "______________________________________      ____________________________";
$EmployeeSigLine2 = "Employee handling request                                       Date of request";


$pdf->Ln(15);
$pdf->Cell(0,10,$SigLine,0,0);
$pdf->Ln(5);
$pdf->Cell(0,10,$SigLine2,0,0);
$pdf->Ln(10);
$pdf->MultiCell(0,5,$LastLine);
$pdf->Ln(10);
$pdf->Cell(0,10,$EmployeeSigLine,0,0);
$pdf->Ln(5);
$pdf->Cell(0,10,$EmployeeSigLine2,0,0);
$pdf->Ln(5);

$PDF_NAME = "FileFolder/VisaCheckCardDisputeForms/case_".str_pad($caseID, 3, "0", STR_PAD_LEFT)."_disputeform.pdf";

if(file_exists($PDF_NAME)){
	$PDF_NAME = "FileFolder/VisaCheckCardDisputeForms/case_".str_pad($caseID, 3, "0", STR_PAD_LEFT)."_disputeform_2.pdf";
	
	if(file_exists($PDF_NAME)){
		$PDF_NAME = "FileFolder/VisaCheckCardDisputeForms/case_".str_pad($caseID, 3, "0", STR_PAD_LEFT)."_disputeform_3.pdf";
		
		if(file_exists($PDF_NAME)){
			$PDF_NAME = "FileFolder/VisaCheckCardDisputeForms/case_".str_pad($caseID, 3, "0", STR_PAD_LEFT)."_disputeform_4.pdf";
				
			if(file_exists($PDF_NAME)){
				$PDF_NAME = "FileFolder/VisaCheckCardDisputeForms/case_".str_pad($caseID, 3, "0", STR_PAD_LEFT)."_disputeform_5.pdf";
				
				if(file_exists($PDF_NAME)){
					
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong when creating the name for the PDF. An old PDF file had to be deleted. Please contact the System Admin.';
					$PDF_NAME = "FileFolder/VisaCheckCardDisputeForms/case_".str_pad($caseID, 3, "0", STR_PAD_LEFT)."_disputeform_5.pdf";
					
					unlink($PDF_NAME);
					
				}				
			}
		}
	}
}




$pdf->Output('F', $PDF_NAME);
$VISACheckcardURL = $PDF_NAME;


// End PDF Formatting -------------------------------------------------------------------------------------


// Email Group ============================================================================================
/* //Email no longer works as part of Office 365. There would have to be settings changes and configurations. The value this email bring does not warrant the time to get ti working.

$mail = new PHPMailer;

$mail->isSMTP();                                     // Set mailer to use SMTP
//$mail->SMTPDebug = 3;
$mail->Host = $DT_smtp_server;        				 // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                              // Enable SMTP authentication
$mail->Username = $DT_smtp_username;                 // SMTP username
$mail->Password = $DT_smtp_password;                 // SMTP password
//$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 25;                                    // TCP port to connect to
$mail->smtpConnect(
	array(
		"ssl" => array(
			"verify_peer" => false,
			"verify_peer_name" => false,
			"allow_self_signed" -> true
				)
		)
);

$mail->setFrom($DT_smtp_emailaddr, 'DisputeTrack Mailer - Do Not Respond');
//$mail->addAddress('email@email.com', 'First Lastname');     // Add a recipient
//$mail->addAddress('email@email.com', 'First Lastname');	  // Add another
$mail->addAddress('DistributionGroup@email.com');			  //Add a distribution group
//$mail->addAddress('', '');
//$mail->addAddress('', '');
//$mail->addAddress('', '');
//$mail->addAddress('', '');
//$mail->addAddress('', '');
//$mail->addAddress('', '');
//$mail->addCC('');
//$mail->addBCC('');

$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'New Dispute Case Completed';
$mail->Body    = 'This is an automated email to let you know that user: <b>'.$username.'</b> has completed a new case number: <b>'.$caseID.'</b>. <br><br>If you need to be removed from this automated email list, please contact IT.';
$mail->AltBody = 'This is an automated email to let you know that user: '.$username.' has completed a new case: '.$caseID.'. If you need to be removed from this automated email list, please contact IT.';

if(!$mail->send()) {
	$_SESSION["ADD_DISPUTE_ERROR"] = "Email Message to Ops and IT Could not be sent. There was an Email Error: ".$mail->ErrorInfo;
} 
else {}

*/
// End Email Group ========================================================================================





//Add Unset here
unset($_SESSION["WorkingDispute"]);
unset($_SESSION["Dispute_CaseID"]);
unset($_SESSION['FinishDispute']);
unset($_SESSION['FinishDisputeOne']);
unset($_SESSION['CaseCanBeDone']);
//unset($_SESSION['ADD_DISPUTE_ERROR']);




?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - Finish New Disputes</title>
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
$PageTitle = "Dispute Track - New Dispute Final";

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
                    <div class="col-md-4">
                     <h2>Finish Dispute case</h2> 
                    </div>
					<div class="col-md-8">
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
				
				<div class="row">
					<div class="col-md-2">
						<input type="button" class="btn btn-primary" onclick="location='index.php'" value="Home" />
					</div>
					<div class="col-md-2">
						<input type="button" class="btn btn-success" onclick="location='newDispute.php'" value="New Dispute" />
					</div>
				
				</div>
				
				<hr />
				<!--
				<div class="row">
					<div class="col-md-8">
						<label align = "center">VISA CheckCard Dispute Form for Customers:</label>
						<embed src="<?php print $VISACheckcardURL; ?>" width="800px" height="800px" />
					</div>
				</div>
				<hr />
				-->
				<div class="row">
					<div class="col-md-8">
						<label align = "center">VISA CheckCard Dispute Form for Customers:</label>
						<a target="_blank" href="<?php print $VISACheckcardURL; ?>">Click here to View</a>
						<iframe src="<?php print $VISACheckcardURL; ?>" width="800px" height="800px" type="application/pdf"></iframe> 
					</div>
				</div>
				<hr />
				<!--
				<div class="row">
					<div class="col-md-8">
						<label align = "center">VISA CheckCard Dispute Form for Customers:</label>
						<object data="<?php print $VISACheckcardURL; ?>" type="application/pdf">
							<embed src="<?php print $VISACheckcardURL; ?>" width="800px" height="800px"  type="application/pdf" />
						</object>
					</div>
				</div>
				<hr />
				<div class="row">
					<div class="col-md-8">
						<label align = "center">VISA CheckCard Dispute Form for Customers:</label>
						<object data="<?php print $VISACheckcardURL; ?>" type="application/pdf">
							<iframe src="<?php print $VISACheckcardURL; ?>" width="800px" height="800px"  type="application/pdf" /> 
						</object>
					</div>
				</div>
				<hr />
				-->

				  <!-- Make Landing for Back End -->
				  
				
				  
				  
				  <!-- Make landing for Front End -->
				  
				  
				  
				  
              
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
<?php

require_once('assets/includes/connection.php');

require_once('assets/includes/mailheader.php');
require_once('assets/includes/PHPMailer/PHPMailerAutoload.php');

session_start();

require('assets/includes/FPDF/fpdf.php');

$case_select = 'SELECT T.id, T.caseid, T.amount, C.casestartdate, C.custfname, C.custlname, C.custaddressone, C.custaddresstwo, C.custcityaddr, C.custstateaddr, C.custzipaddr, N.cardtype, A.accountnumber, A.accoountnew
FROM checkcases C
JOIN checktransactions T on C.id = T.caseid
JOIN checkcardnumbers N on C.id = N.caseid
JOIN checkaccountnumbers A on C.id = A.caseid
WHERE C.pcletterprintflag="1" AND C.casedeleted="0" AND C.caseclosed="0" AND C.casedoneinput="1" AND T.transactiondeleted="0"';
$case_fetch = $dtcon->query($case_select);

$case_items = $case_fetch->fetch_all();

$caseCount = count($case_items);

date_default_timezone_set("America/Chicago");
$DateofGeneration = date("m-d-Y");

$otherDate = date("Y-m-d, G:i:s");

$ipaddress = $_SERVER['REMOTE_ADDR'];

$newPDFnames = array();
//$transactionIDs = array();
$caseIDs = array();
$failArray = array();

//$Auto_Username = "";

//$Auto_Username = $username;

//if($Auto_Username == ""){
//	$Auto_Username = "auto.generated";
//}
//else{
//	$Auto_Username = $Auto_Username . " - auto.generated"
//}


$newCaseItems = array();
$newCaseIDs = array();

for($i=0;$i<$caseCount;$i++){
	
	if(!in_array($case_items[$i][1],$newCaseIDs)){		//Item not in array yet
		
		array_push($newCaseIDs, $case_items[$i][1]);
	
		$temparray = array();
		$temparray2 = array();
		
		array_push($temparray2, $case_items[$i][2]);
		
		array_push($temparray, $case_items[$i][0]);
		array_push($temparray, $case_items[$i][1]);
		array_push($temparray, $temparray2);
		array_push($temparray, $case_items[$i][3]);
		array_push($temparray, $case_items[$i][4]);
		array_push($temparray, $case_items[$i][5]);
		array_push($temparray, $case_items[$i][6]);
		array_push($temparray, $case_items[$i][7]);
		array_push($temparray, $case_items[$i][8]);
		array_push($temparray, $case_items[$i][9]);
		array_push($temparray, $case_items[$i][10]);
		array_push($temparray, $case_items[$i][11]);
		array_push($temparray, $case_items[$i][12]);
		array_push($temparray, $case_items[$i][13]);
		
		array_push($newCaseItems, $temparray);
		
	}
	else{												//Item is in array
		
		$tempKey = array_search($case_items[$i][1], $newCaseIDs);
		
		if($tempKey === FALSE){
			print "Key Error! Please contact the System Admin";
			exit();
		}
		
		array_push($newCaseItems[$tempKey][2], $case_items[$i][2]);
		
	}
	
	
	
}

//var_dump($case_items);
//print "<br><br>sep<br><br>";
//var_dump($newCaseItems);
//exit();

$newCaseCount = count($newCaseItems);

for($i=0;$i<$newCaseCount;$i++){
	
	$Daycount = "45";
	
	if($case_items[$i][13] = "1"){
		$Daycount = "90";
	}
	
	$pdf = new FPDF();	
	$pdf->SetLineWidth(.5);
	$pdf->AddPage('P','Letter', 0);
	$pdf->AliasNbPages();

	//$pdf->Image('assets/img/logo.png', NULL, NULL, 50, 10, 'PNG');

	$pdf->SetFont('Times','B',10);

	$pdf->Ln(10);		//Line height is 5, so 10 is 2 lines on the PDF Sheet
	$pdf->Ln(10);
	$pdf->Ln(10);
	$pdf->Ln(10);
	$pdf->Ln(5);		//1 Extra line(s)
	
	$pdf->Cell(65,10,$DateofGeneration,0,0);
	$pdf->Ln(5);		//Line height is 5 (So one line of space here)
	$pdf->Cell(65,10,$newCaseItems[$i][4]." ".$newCaseItems[$i][5],0,0);
	$pdf->Ln(5);
	$pdf->Cell(65,10,$newCaseItems[$i][6]." ".$newCaseItems[$i][7],0,0);
	$pdf->Ln(5);
	//$pdf->Cell(65,10,$newCaseItems[$i][7],0,0);
	//$pdf->Ln(5);
	$pdf->Cell(65,10,$newCaseItems[$i][8].", ".$newCaseItems[$i][9]." ".$newCaseItems[$i][10],0,0);
	$pdf->Ln(20);
	$pdf->Ln(15);		//3 Extra line(s)
	
	$pdf->Cell(65,10,"Dear ".$newCaseItems[$i][4]." ".$newCaseItems[$i][5].",",0,0);
	$pdf->Ln(10);
	
	//array_shift/explode - use explode to seperate the string on ',', then use array_shift to always get the first item.
	
	$newStr = "$" . $newCaseItems[$i][2][0];
	$tempCount = count($newCaseItems[$i][2]);
	
	for($k=1;$k<$tempCount;$k++){
		
		$newStr = $newStr . ", $" . $newCaseItems[$i][2][$k];
		
	}
	
	$DisputeDate = DateTime::createFromFormat('Y-m-d', array_shift(explode(',', $newCaseItems[$i][3])));
	$DisputeDate = $DisputeDate->format('m-d-Y');
	
	
	if($newCaseItems[$i][11] == "3"){		//ATM
		$pdf->MultiCell(0,5,"received an ATM dispute from you on ".$DisputeDate." in the amount(s) of ".$newStr.". We have not been able to resolve the ATM dispute therefore, has given you provisional credit to your account for the amount(s) listed above as of the date of this letter while the investigation continues.  requests that any funds recovered by you be reimbursed to the bank.");
		$pdf->Ln(5);
		$pdf->MultiCell(0,5,"The dispute is still under investigation for up to ".$Daycount." days. If the investigation is not completed in ".$Daycount." days then the investigation and the provisional credit is considered final.");
		$pdf->Ln(15);
		$pdf->Cell(65,10,"Sincerely,",0,0);
		$pdf->Ln(5);
		$pdf->Cell(65,10,"Operations Clerk",0,0);
		$pdf->Ln(5);
		$pdf->Cell(65,10,"Phone # 555-555-555",0,0);
	}
	else{
		$pdf->MultiCell(0,5,"received a Check Card (POS) dispute from you on ".$DisputeDate." in the amount(s) of ".$newStr.". We have not been able to resolve the dispute therefore has given you provisional credit for the amount(s) listed above as of the date of this letter while the investigation continues.  requests that any funds recovered by you be reimbursed to the bank.");
		$pdf->Ln(5);
		$pdf->MultiCell(0,5,"The dispute is still under investigation for up to ".$Daycount." days. If the investigation is not completed in ".$Daycount." days then the investigation and the provisional credit is considered final.");
		$pdf->Ln(30);
		$pdf->Cell(65,10,"Sincerely,",0,0);
		$pdf->Ln(5);
		$pdf->Cell(65,10,"Operations Clerk",0,0);
		$pdf->Ln(5);
		$pdf->Cell(65,10,"Phone # 555-555-5555",0,0);
	}
	
	//Name check, to be able to keep previous old documents
	
	$PDF_NAME = "FileFolder/ProvisionalCreditLetters/case_".str_pad($newCaseItems[$i][1], 3, "0", STR_PAD_LEFT)."_PCLetter.pdf";
	
	if(file_exists($PDF_NAME)){
		$PDF_NAME = "FileFolder/ProvisionalCreditLetters/case_".str_pad($newCaseItems[$i][1], 3, "0", STR_PAD_LEFT)."_PCLetter_2.pdf";
		
		if(file_exists($PDF_NAME)){
			$PDF_NAME = "FileFolder/ProvisionalCreditLetters/case_".str_pad($newCaseItems[$i][1], 3, "0", STR_PAD_LEFT)."_PCLetter_3.pdf";
			
			if(file_exists($PDF_NAME)){
				$PDF_NAME = "FileFolder/ProvisionalCreditLetters/case_".str_pad($newCaseItems[$i][1], 3, "0", STR_PAD_LEFT)."_PCLetter_4.pdf";
					
				if(file_exists($PDF_NAME)){
					$PDF_NAME = "FileFolder/ProvisionalCreditLetters/case_".str_pad($newCaseItems[$i][1], 3, "0", STR_PAD_LEFT)."_PCLetter_5.pdf";
					
					if(file_exists($PDF_NAME)){
						$PDF_NAME = "FileFolder/ProvisionalCreditLetters/case_".str_pad($newCaseItems[$i][1], 3, "0", STR_PAD_LEFT)."_PCLetter_5.pdf";
						
						unlink($PDF_NAME);
						
					}				
				}
			}
		}
	}
	
	
	
	print $pdf->Output('F', $PDF_NAME);
	
	array_push($newPDFnames, $PDF_NAME);
	//array_push($transactionIDs, $newCaseItems[$i][0]);
	array_push($caseIDs, $newCaseItems[$i][1]);
	
}

//var_dump($caseIDs, $newPDFnames);
//exit();




// SQL DB Update ------------------------------------------------------------------------------------------


$caseIDcount = count($caseIDs);

for($i=0;$i<$caseIDcount;$i++){
	
	$tmpLodID = 888800000 + $caseIDs[$i];
	
	$updateStr = "UPDATE checktransactions SET procreditgiven='".$otherDate."' WHERE caseid='".$caseIDs[$i]."'";
	$case_updateStr = "UPDATE checkcases SET pcletterprintflag='0' WHERE id='".$caseIDs[$i]."'";
	
	$UpdateChangeLog = "INSERT INTO changelog(id, changedate, username, ipaddress, logtype, caseid, changedetail) VALUES (NULL, '".$otherDate."', 'auto.generated', '".$ipaddress."', '2', '".$tmpLodID."', 'Auto Generated Provisional Credit Letter for Case Number: ".$caseIDs[$i].". New Date Updated: ".$otherDate.".')";	
	
	if ($dtcon->query($UpdateChangeLog) === TRUE) {
		//print "Insert Worked";
	}
	else{
		
		$failStr = "This string ------ ".$UpdateChangeLog." ------ failed for some reason.";
		array_push($failArray, $failStr);
		
	}
	
	if ($dtcon->query($updateStr) === TRUE) {
		//print "Insert Worked";
	}
	else{
		
		$failStr = "This string ------ ".$updateStr." ------ failed for some reason.";
		array_push($failArray, $failStr);		
		
	}
	if ($dtcon->query($case_updateStr) === TRUE) {
		//print "Insert Worked";
	}
	else{
		
		$failStr = "This string ------ ".$case_updateStr." ------ failed for some reason.";
		array_push($failArray, $failStr);		
		
	}
	
}


// SQL DB Update ------------------------------------------------------------------------------------------




// Email Group ============================================================================================
//Send List of Newly Created Letters to Group
/* //Email no longer works as part of Office 365. There would have to be settings changes and configurations. The value this email bring does not warrant the time to get it working.

if($caseCount != 0){
	
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

	$mail->Subject = 'New PC Letter(s) Created';

	$bodyStr = "This is an automated email to let you know that the following case(s) and transaction(s) have had provisional credit letters made. Below is a list:<br><br>";

	for($i=0;$i<$caseIDcount;$i++){
		
		$bodyStr = $bodyStr . " Case ID: <b>".$caseIDs[$i]."</b><br>";
		
	}


	$mail->Body    = $bodyStr;
	$mail->AltBody = $bodyStr;

	if(!$mail->send()) {
		$_SESSION["ADD_DISPUTE_ERROR"] = "Email Message to Ops and IT Could not be sent. There was an Email Error: ".$mail->ErrorInfo;
		//echo 'Message could not be sent.';
		//echo 'Mailer Error: ' . $mail->ErrorInfo;
	} 
	else {}

}



*/
// End Email Group ========================================================================================




//var_dump($_SESSION);

//print "Success!";
//exit();


if(isset($_SESSION["TransactionAdded"])){
	header( "Location: viewGeneratedDocuments.php" );
	exit();
}




?>
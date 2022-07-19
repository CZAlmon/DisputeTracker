<?php

require_once('assets/includes/connection.php');

require_once('assets/includes/mailheader.php');
require_once('assets/includes/PHPMailer/PHPMailerAutoload.php');

session_start();

require('assets/includes/FPDF/fpdf.php');


//Get Reversal Reasons Text from Server
//===============================================================
$reversal_query = "SELECT * FROM reversalerrors WHERE iddeleted='0'";
$reversal_query_data = $dtcon->query($reversal_query);
$reversal_data = $reversal_query_data->fetch_all();

//var_dump($reversal_data);
$NumberofRever = count($reversal_data);
//===============================================================
$case_id_list_query = "SELECT id FROM checkcases";
$case_id_list_data = $dtcon->query($case_id_list_query);
$case_id_list = $case_id_list_data->fetch_all();

$new_case_id_list = array();

foreach($case_id_list as $tempval){
	$new_case_id_list[] = $tempval[0];
}

//var_dump($new_case_id_list);
$NumberofCases = count($new_case_id_list);
//===============================================================



//Comfirm previous page
//Need Case number and Generated Day, from previous page
//confirm case number exists, confirm case has 1 or more transactions, all or some transactions have reversal reason, PC Rescinded Set, Date Letter Sent

//with these 3, on all or some transactions on this case:
// Get Name, Address, Account Number (Filter all but last 4 digits - Set as Stars)
// Write Out:

//$dearLine = "Dear Customer,";

//$firstLine = "received a Check Card (POS) dispute(s) from you on (DATE) for (AMOUNTS+). We gave you provisional credit of the amount(s) listed above on (DATE).";
//$secondLine = "During the investigation we found there to be no error. We will be debiting your account for the amount(s) of (TOTAL AMOUNT) on (DATE). The bank will honor checks, drafts, and preauthorized transfers to third parties that would otherwise overdraw the account up to the amount(s) of the debit(s) for a period of 5 business days without charge. You also have the right to request copies of the documents that the bank used to make the determination.";

//$sincerlyLine = "Sincerly,";
//$clerkline = "Operations Clerk";
//$lastLine = "Phone # 555-555-5555";


if ($_SERVER["REQUEST_METHOD"] == "POST"){
	
	//var_dump($_POST);
	//var_dump($_SESSION);
	//var_dump($_FILES);
	//exit();
	
	if(isset($_POST["newreversalletterSubmit"])){
		date_default_timezone_set("America/Chicago");
		$DateofGeneration = date("m-d-Y");

		$otherDate = date("Y-m-d, G:i:s");

		$ipaddress = $_SERVER['REMOTE_ADDR'];
		
		$casenumber = 0;
		$generatedDay = 0;
		
		if(!isset($_POST["CaseNumber"])){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'A Case Number must be entered!';
			header( "Location: StartReversalLetter.php" );
			exit();
		}
		else{
			$casenumber = $_POST["CaseNumber"];
			
			if(!preg_match("/^[0-9]+$/", $casenumber)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Number must be digits only. Ex: \'120\'';
				header( "Location: StartReversalLetter.php" );
				exit();
			}
		}	
		
		//var_dump($casenumber);
		//print "<br><br>sep<br><br>";
		//var_dump($case_id_list);
		//print "<br><br>sep<br><br>";
		//var_dump($new_case_id_list);
		//print "<br><br>sep<br><br>";
		//exit();	
		
		if(!in_array($casenumber, $new_case_id_list)){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Number does not exist yet, please use an existing Case number.';
			header( "Location: StartReversalLetter.php" );
			exit();
		}
			
		
		$case_select = 'SELECT T.id, T.caseid, T.amount, T.reversalerror, T.pcrescinded, T.pcreverselettersent, C.casestartdate, C.custfname, C.custlname, C.custaddressone, C.custaddresstwo, C.custcityaddr, C.custstateaddr, C.custzipaddr, N.cardtype, A.accountnumber, A.accoountnew, T.procreditgiven
		FROM checkcases C
		JOIN checktransactions T on C.id = T.caseid
		JOIN checkcardnumbers N on C.id = N.caseid
		JOIN checkaccountnumbers A on C.id = A.caseid
		WHERE C.id="'.$casenumber.'" AND C.casedeleted="0" AND C.caseclosed="0" AND C.casedoneinput="1" AND T.transactiondeleted="0"';
		$case_fetch = $dtcon->query($case_select);

		$case_items = $case_fetch->fetch_all();

		$caseCount = count($case_items);
		
		//var_dump($caseCount);
		//print "<br><br>sep<br><br>";
		//var_dump($case_items);
		//print "<br><br>sep<br><br>";
		//var_dump($case_select);
		//print "<br><br>sep<br><br>";
		//exit();	
		
		$custfullname = $case_items[0][7]." ".$case_items[0][8];
		$custfulladdress = $case_items[0][9]." ".$case_items[0][10];
		$custfullcityState = $case_items[0][11].", ".$case_items[0][12]." ".$case_items[0][13];
		
		$custAccountNum = $case_items[0][15]; //Not Redacted
		
		$filteredcustAccountNum = substr_replace($custAccountNum, str_repeat('*', strlen($custAccountNum) - 4), 0, -4);
		
		$datecasestarted_temp = explode(",",$case_items[0][6]);
		$datecasestarted = $datecasestarted_temp[0];
		
		$datepcreditgiven_temp = explode(",",$case_items[0][17]);
		$datepcreditgiven = $datepcreditgiven_temp[0];
				
		//var_dump($custfullname);
		//print "<br><br>sep<br><br>";
		//var_dump($custfulladdress);
		//print "<br><br>sep<br><br>";
		//var_dump($custfullcityState);
		//print "<br><br>sep<br><br>";
		//var_dump($custAccountNum);
		//print "<br><br>sep<br><br>";
		//var_dump($filteredcustAccountNum);
		//print "<br><br>sep<br><br>";
		//var_dump($case_items[0][17]);
		//print "<br><br>sep<br><br>";
		//var_dump($datepcreditgiven_temp);
		//print "<br><br>sep<br><br>";
		//var_dump($datepcreditgiven);
		//print "<br><br>sep<br><br>";
		//exit();
		
		$Numberofresveredtransactions = 0; //Count transactions reversed from this case
		$reversedtransactionarray = array();
		$stringofAmounts = "";
		$totalamount = 0;
		
		for($i=0;$i<$caseCount;$i++){
			
			$tempReverseError = $case_items[$i][3];
			$temppcrescinded = $case_items[$i][4];
			$temppcreverselettersent = $case_items[$i][5];
			
			//var_dump($case_items[$i][3]);
			//print "<br><br>sep<br><br>";
			//var_dump($case_items[$i][4]);
			//print "<br><br>sep<br><br>";
			//var_dump($case_items[$i][5]);
			//print "<br><br>!! sep !!<br><br>";
			
			//var_dump($tempReverseError);
			//print "<br><br>sep<br><br>";
			//var_dump($temppcrescinded);
			//print "<br><br>sep<br><br>";
			//var_dump($temppcreverselettersent);
			//print "<br><br>sep<br><br>";
			
			if($tempReverseError != 0 || $temppcrescinded != "" || $temppcreverselettersent != ""){
				//Transaction Reversed
				$Numberofresveredtransactions++;
				
				$reversedtransactionarray[] = $case_items[$i];
				
				$totalamount = $totalamount + $case_items[$i][2];
				
				if($Numberofresveredtransactions == 1){
					$stringofAmounts = "$".number_format($case_items[$i][2],2);
				}
				else{
					$stringofAmounts = $stringofAmounts.", $".number_format($case_items[$i][2],2);
				}
				
				
			}
			else{
				//transaction not reversed
				//Do nothing with the transaction
			}			
			
		}
		
		if($Numberofresveredtransactions <= 0){
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Case Number must have reversed transactions.';
			header( "Location: StartReversalLetter.php" );
			exit();
		}
		
		//var_dump($Numberofresveredtransactions);
		//print "<br><br>sep<br><br>";
		//var_dump($reversedtransactionarray);
		//print "<br><br>sep<br><br>";
		//exit();
		
		//===============================================================================================================================
		
		$dateoflettertobesent = explode(",",$reversedtransactionarray[0][6]);
		
		//var_dump($dateoflettertobesent);
		//print "<br><br>sep<br><br>";
		
		$tempdate = explode("-",$dateoflettertobesent[0]);
		
		$string_dateoflettertobesent = $tempdate[1] ."-". $tempdate[2] ."-". $tempdate[0];
		
		//var_dump($tempdate);
		//print "<br><br>sep<br><br>";
		//var_dump($string_dateoflettertobesent);
		//print "<br><br>sep<br><br>";
		//exit();
		
		$tempdate = explode("-",$datecasestarted);
		$string_datecasestarted = $tempdate[1] ."-". $tempdate[2] ."-". $tempdate[0];
		
		$tempdate = explode("-",$datepcreditgiven);
		$string_datepcreditgiven = $tempdate[1] ."-". $tempdate[2] ."-". $tempdate[0];
		
		$tempdate = explode("-",$case_items[0][4]);
		$string_datereversecredit = $tempdate[1] ."-". $tempdate[2] ."-". $tempdate[0];
		
		$dearLine = "Dear Customer,";

		$firstLine = "received a Check Card (POS) dispute(s) from you on ".$string_datecasestarted." for ".$stringofAmounts.". We gave you provisional credit of the amount(s) listed above on ".$string_datepcreditgiven.".";
		$secondLine = "During the investigation we found there to be no error. We will be debiting your account for the amount(s) of $".number_format($totalamount,2)." on ".$string_datereversecredit.". The bank will honor checks, drafts, and preauthorized transfers to third parties that would otherwise overdraw the account up to the amount(s) of the debit(s) for a period of 5 business days without charge. You also have the right to request copies of the documents that the bank used to make the determination.";

		$sincerlyLine = "Sincerly,";
		$clerkline = "Operations Clerk";
		$lastLine = "Phone # 555-555-5555";
		
		//ALL PDF Stuff is pulled and adapted from generateLetters.php (PC Letters)
		//There can be multiple lines per array but the only differences are the ammounts per transaction. 
		//So any information will be pulled from first line/transaction
		
		$dateoflettertobesent = explode(",",$reversedtransactionarray[0][6]);
				
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
		$pdf->Ln(10);		//Line height is 5 (So one line of space here)
		$pdf->Cell(65,10,$custfullname,0,0);
		$pdf->Ln(5);
		$pdf->Cell(65,10,$custfulladdress,0,0);
		$pdf->Ln(5);
		$pdf->Cell(65,10,$custfullcityState,0,0);
		$pdf->Ln(5);
		$pdf->Cell(65,10,"Account #".$filteredcustAccountNum,0,0);
		$pdf->Ln(15);
		$pdf->Ln(15);		//3 Extra line(s)
		
		$pdf->Cell(65,10,"Dear ".$custfullname.",",0,0);	
		//$pdf->Cell(65,10,$dearLine,",0,0);					
		$pdf->Ln(10);
		
		
		//$pdf->Cell(65,10,$firstLine,0,0);
		$pdf->MultiCell(0,5,$firstLine);
		$pdf->Ln(5);
		$pdf->MultiCell(0,5,$secondLine);
		
		$pdf->Ln(30);
		$pdf->Cell(65,10,$sincerlyLine,0,0);
		$pdf->Ln(5);
		$pdf->Cell(65,10,$clerkline,0,0);
		$pdf->Ln(5);
		$pdf->Cell(65,10,$lastLine,0,0);
		
		$PDF_NAME = "FileFolder/ProvisionalCreditLetters/case_".str_pad($casenumber, 3, "0", STR_PAD_LEFT)."_Reversal_PCLetter.pdf";
		
		if(file_exists($PDF_NAME)){
			$PDF_NAME = "FileFolder/ProvisionalCreditLetters/case_".str_pad($casenumber, 3, "0", STR_PAD_LEFT)."_Reversal_PCLetter_2.pdf";
			
			if(file_exists($PDF_NAME)){
				$PDF_NAME = "FileFolder/ProvisionalCreditLetters/case_".str_pad($casenumber, 3, "0", STR_PAD_LEFT)."_Reversal_PCLetter_3.pdf";
				
				if(file_exists($PDF_NAME)){
					$PDF_NAME = "FileFolder/ProvisionalCreditLetters/case_".str_pad($casenumber, 3, "0", STR_PAD_LEFT)."_Reversal_PCLetter_4.pdf";
						
					if(file_exists($PDF_NAME)){
						$PDF_NAME = "FileFolder/ProvisionalCreditLetters/case_".str_pad($casenumber, 3, "0", STR_PAD_LEFT)."_Reversal_PCLetter_5.pdf";
						
						if(file_exists($PDF_NAME)){
							$PDF_NAME = "FileFolder/ProvisionalCreditLetters/case_".str_pad($casenumber, 3, "0", STR_PAD_LEFT)."_Reversal_PCLetter_5.pdf";
							
							unlink($PDF_NAME);
							
						}				
					}
				}
			}
		}
		
		print $pdf->Output('F', $PDF_NAME);
		
		//===============================================================================================================================
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
		$mail->Subject = 'New PC Reversal Letter Created';
		$bodyStr = "This is an automated email to let you know that the case number ".$casenumber." has had a provisional credit reversal letter made.";

		$mail->Body    = $bodyStr;
		$mail->AltBody = $bodyStr;

		if(!$mail->send()) {
			$_SESSION["ADD_DISPUTE_ERROR"] = "Email Message to Ops and IT Could not be sent. There was an Email Error: ".$mail->ErrorInfo;
			//echo 'Message could not be sent.';
			//echo 'Mailer Error: ' . $mail->ErrorInfo;
		} 
		else {}
		
		
		*/
		// End Email Group ========================================================================================
		
		
	}
	else{
		$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong with the Server POST, please contact the System Admin.';
		header( "Location: StartReversalLetter.php" );
		exit();
	}
		
}


$_SESSION["TransactionAdded"] = "Reversal Letter Made";
header( "Location: viewGeneratedDocuments.php" );
exit();



?>
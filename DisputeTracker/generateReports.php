<?php

require_once('assets/includes/connection.php');

session_start();

require_once('assets/includes/PHP_head.php');



if ($accesslevel < 5 && $accesslevel != 1){
	$_SESSION["ADD_DISPUTE_ERROR"] = 'You aren\'t allowed to use this page, Please Contact the System Admin.';
	header( "Location: index.php" );
	exit();
}



// ---------------------------------------------------------------------------------------------
$case_select = "SELECT checkcases.id, checkcases.casestartdate, checkcases.custfname, checkcases.custlname, checkcases.custphone, checkaccountnumbers.accountnumber, checkcardnumbers.id, checkcardnumbers.cardnumber, checkcases.casedeleted FROM checkcases, checkaccountnumbers, checkcardnumbers WHERE checkcases.id = checkaccountnumbers.caseid AND checkcases.id = checkcardnumbers.caseid AND checkcases.casedeleted = '0' ORDER BY checkcases.id";
$case_fetch = $dtcon->query($case_select);

$case_items = $case_fetch->fetch_all();
// ---------------------------------------------------------------------------------------------
$transaction_select = "SELECT caseid, cardid, amount, transactiondate, transactiondeleted FROM checktransactions WHERE transactiondeleted = '0' ORDER BY caseid";
$transaction_fetch = $dtcon->query($transaction_select);

$transaction_items = $transaction_fetch->fetch_all();
// ---------------------------------------------------------------------------------------------

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
		
		array_push($temparr_1, $caseIDtemp);
		array_push($temparr_1, $caseDatetmp);
		array_push($temparr_1, $caseFNametmp);
		array_push($temparr_1, $caseLNametmp);
		array_push($temparr_1, $casePhonetmp);
		array_push($temparr_1, $checkAcctmp);
		array_push($temparr_1, $checkCardIDtmp);
		array_push($temparr_1, $checkCardtmp);
		
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


function pullreport(){
	
	
	
	
}









if ($_SERVER["REQUEST_METHOD"] == "POST"){
	
	if(isset($_POST["SubmitSearch"])){
		
		//var_dump($_POST);
		
		$newTableArray = array();
		
		$caseID_SEARCH = "";
		$name_SEARCH = "";
		$phone_SEARCH = "";
		$accNum_SEARCH = "";
		$cardNum_SEARCH = "";
		$StartDate_SEARCH = "";
		$EndDate_SEARCH = "";
		$ReportAll = "";
		
		if(isset($_POST["SearchCaseID"])){
			$caseID_SEARCH = $_POST["SearchCaseID"];
		}
		if(isset($_POST["SearchName"])){
			$name_SEARCH = $_POST["SearchName"];
		}
		if(isset($_POST["SearchPhone"])){
			$phone_SEARCH = $_POST["SearchPhone"];
		}
		if(isset($_POST["SearchAccNum"])){
			$accNum_SEARCH = $_POST["SearchAccNum"];
		}
		if(isset($_POST["SearchCardNum"])){
			$cardNum_SEARCH = $_POST["SearchCardNum"];
		}
		if(isset($_POST["SearchSDate"])){
			$StartDate_SEARCH = $_POST["SearchSDate"];
		}
		if(isset($_POST["SearchEDate"])){
			$EndDate_SEARCH = $_POST["SearchEDate"];
		}
		if(isset($_POST["ReportALL"])){
			$ReportAll = $_POST["ReportALL"];
		}
		
		if($ReportAll == ""){
			if($caseID_SEARCH == ""){
				if($name_SEARCH == ""){
					if($phone_SEARCH == ""){
						if($accNum_SEARCH == ""){
							if($cardNum_SEARCH == ""){
								if($StartDate_SEARCH == ""){
									if($EndDate_SEARCH == ""){
										$_SESSION["ADD_DISPUTE_ERROR"] = 'To make a Report, you must input some criteria.';
										header( "Location: generateReports.php" );
										exit();
									}
									else{	//$EndDate contains info, At this point StartDate would not contain info
										$_SESSION["ADD_DISPUTE_ERROR"] = 'To make a Report by Date, you must input both Dates in the correct fields.';
										header( "Location: generateReports.php" );
										exit();
									}
								}
								else{	//$StartDate contains info
									
									if($EndDate_SEARCH == ""){
										$_SESSION["ADD_DISPUTE_ERROR"] = 'To make a Report by Date, you must input both Dates in the correct fields.';
										header( "Location: generateReports.php" );
										exit();
									}
									
									$StartdateGood = validateDate($StartDate_SEARCH, 'Y-m-d');
									$StartdateGoodVAR = validatePastDate($StartDate_SEARCH);
									
									$EnddateGood = validateDate($EndDate_SEARCH, 'Y-m-d');
									$EnddateGoodVAR = validatePastDate($EndDate_SEARCH);
									
									$dateGoodVARTWO = validatePostedDate($StartDate_SEARCH, $EndDate_SEARCH);
									
									if(($StartdateGood != true) || ($StartdateGoodVAR != true) || ($EnddateGood != true) || ($EnddateGoodVAR != true) || ($dateGoodVARTWO != true)){
										$_SESSION["ADD_DISPUTE_ERROR"] = 'To make a Report by Date, you must input both Valid Dates in the correct fields. Please contact the System Admin if you have any questions.';
										header( "Location: generateReports.php" );
										exit();
									}
									
									
									for($i=0;$i<$DisputeCount;$i++){
										
										$tempCaseDate = $AllDisputesArray[$i][1];
										
										$tempCaseDate = array_shift(explode(",", $tempCaseDate));
										
										$StartTIME = strtotime($StartDate_SEARCH);
										$EndTIME = strtotime($EndDate_SEARCH);
										$TestTIME = strtotime($tempCaseDate);
										
										if(($TestTIME >= $StartTIME) && ($TestTIME <= $EndTIME)){
											
											array_push($newTableArray, $AllDisputesArray[$i][0]);
											
										}
									}								
								}
							}
							else{	//$CardNum contains info
								
								if (!is_numeric($cardNum_SEARCH)){
									$_SESSION["ADD_DISPUTE_ERROR"] = 'Card Number can be Numbers only!';
									header( "Location: generateReports.php" );
									exit();
								}
								
								for($i=0;$i<$DisputeCount;$i++){
									
									$tempCardNum = $AllDisputesArray[$i][7];
									
									$pos = strpos($tempCardNum, $cardNum_SEARCH);
									
									if(($tempCardNum == $cardNum_SEARCH) || ($pos !== FALSE)){
										
										array_push($newTableArray, $AllDisputesArray[$i][0]);
										
									}
								}
								
								
							}
						}
						else{	//$accNum contains info
							
							if (!is_numeric($accNum_SEARCH)){
								$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number can be Numbers only!';
								header( "Location: generateReports.php" );
								exit();
							}
							
							for($i=0;$i<$DisputeCount;$i++){
								
								$tempAccNum = $AllDisputesArray[$i][5];
								
								$pos = strpos($tempAccNum, $accNum_SEARCH);
								
								if(($tempAccNum == $accNum_SEARCH) || ($pos !== FALSE)){
									
									array_push($newTableArray, $AllDisputesArray[$i][0]);
									
								}
							}
						}
					}
					else{	//$phone contains info
						
						for($i=0;$i<$DisputeCount;$i++){
							
							$tempPhoneNum = $AllDisputesArray[$i][4];
							
							$pos = strpos($tempPhoneNum, $phone_SEARCH);
							
							if(($tempPhoneNum == $phone_SEARCH) || ($pos !== FALSE)){
								
								array_push($newTableArray, $AllDisputesArray[$i][0]);
								
							}
						}
					}
				}
				else{	//$name contains info
					
					for($i=0;$i<$DisputeCount;$i++){
						
						$tempNameNum = strtolower($AllDisputesArray[$i][2]." ".$AllDisputesArray[$i][3]);
						
						$pos = strpos($tempNameNum, strtolower($name_SEARCH));
						
						if(($tempNameNum == strtolower($name_SEARCH)) || ($pos !== FALSE)){
							
							array_push($newTableArray, $AllDisputesArray[$i][0]);
							
						}
					}
				}
			}
			else{	//$caseID contains info
				
				if (!is_numeric($caseID_SEARCH)){
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Account Number can be Numbers only!';
					header( "Location: generateReports.php" );
					exit();
				}
				
				for($i=0;$i<$DisputeCount;$i++){
					
					$tempCaseIDNum = $AllDisputesArray[$i][0];
					
					$pos = strpos($tempCaseIDNum, $caseID_SEARCH);
					
					if(($tempCaseIDNum == $caseID_SEARCH) || ($pos !== FALSE)){
						
						array_push($newTableArray, $AllDisputesArray[$i][0]);
						
					}
				}
			
			}
		}
		else{
			
			for($i=0;$i<$DisputeCount;$i++){
				
				array_push($newTableArray, $AllDisputesArray[$i][0]);
				
			}
			
		}
		
		$newTableCount = count($newTableArray);
		//var_dump($newTableArray);
		
		$tmpCaseSelect = "SELECT checkcases.id, checkcases.casestartdate, checkaccountnumbers.accountnumber, checkcardnumbers.cardnumber, checkcases.redflag, checkcases.sevlev, checkcases.comments FROM checkcases, checkaccountnumbers, checkcardnumbers WHERE checkcases.id = checkaccountnumbers.caseid AND checkcases.id = checkcardnumbers.caseid ORDER BY checkcases.id";
		$tmpCaseFetch = $dtcon->query($tmpCaseSelect);

		$tmpCaseItems = $tmpCaseFetch->fetch_all();
		
		$tmpTransSelect = "SELECT caseid, amount, transactiondate, loss, procreditgiven, pclettersent, description, comments FROM checktransactions ORDER BY caseid";
		$tmpTransFetch = $dtcon->query($tmpTransSelect);

		$tmpTransItems = $tmpTransFetch->fetch_all();
		
		//print "Case Array:\n";
		//var_dump($tmpCaseItems);
		//print "Transaction Array:\n";
		//var_dump($tmpTransItems);
		
		
		$newReportArray = array();
		
		$TitleTemp = array();
		
		array_push($TitleTemp, "Case Number");
		array_push($TitleTemp, "Start Date");
		array_push($TitleTemp, "Account Number");
		array_push($TitleTemp, "Card Number");
		array_push($TitleTemp, "Red Flag");
		array_push($TitleTemp, "Sev Lev");
		array_push($TitleTemp, "Case Comments");
		
		array_push($TitleTemp, "Transaction Amounts");
		array_push($TitleTemp, "Transaction Dates");
		array_push($TitleTemp, "Transaction Losses");
		array_push($TitleTemp, "PC Given Date");
		array_push($TitleTemp, "PC Letter Sent Date");
		array_push($TitleTemp, "Dispute Description");
		array_push($TitleTemp, "Transaction Comments");
		
		array_push($TitleTemp, "Total Recovered");
		array_push($TitleTemp, "Total Loss");
		
		array_push($newReportArray, $TitleTemp);
		
		
		// $newString = str_replace(',', '', $oldString);
		
		for ($i=0; $i < $newTableCount; $i++){
			
			$caseIDTemp = $newTableArray[$i];
			$tempArray = array();
			
			$tempCount = count($tmpCaseItems);
			
			for($m=0; $m<$tempCount; $m++){
				if($caseIDTemp == $tmpCaseItems[$m][0]){
					array_push($tempArray, $tmpCaseItems[$m][0]);	//Item [0]
					array_push($tempArray, $tmpCaseItems[$m][1]);	//Item [1] //Start Date, may need formatting
					array_push($tempArray, "'".$tmpCaseItems[$m][2]."'");	//Item [2]
					array_push($tempArray, "'".$tmpCaseItems[$m][3]."'");	//Item [3]
					array_push($tempArray, $tmpCaseItems[$m][4]);	//Item [4]
					array_push($tempArray, $tmpCaseItems[$m][5]);	//Item [5]
					array_push($tempArray, $tmpCaseItems[$m][6]);	//Item [6]
					
					//array_push($tempArray, "'".$tmpCaseItems[$m][0]."'");	//Item [0]
					//array_push($tempArray, "'".$tmpCaseItems[$m][1]."'");	//Item [1] //Start Date, may need formatting
					//array_push($tempArray, "'".$tmpCaseItems[$m][2]."'");	//Item [2]
					//array_push($tempArray, "'".$tmpCaseItems[$m][3]."'");	//Item [3]
					//array_push($tempArray, "'".$tmpCaseItems[$m][4]."'");	//Item [4]
					//array_push($tempArray, "'".$tmpCaseItems[$m][5]."'");	//Item [5]
					//array_push($tempArray, "'".$tmpCaseItems[$m][6]."'");	//Item [6]
				}
			}
			
			$tempCount = count($tmpTransItems);
			
			/*
			$tmpAmountArr = array();		//Item [7]
			$tmpTDateArr = array();			//Item [8]
			$tmpLossArr = array();			//Item [9]
			$tmpPCGiveArr = array();		//Item [10]
			$tmpPCLetterArr = array();		//Item [11]
			$tmpDescripArr = array();		//Item [12]
			$tmpCommentsArr = array();		//Item [13]
			*/
			
			$tmpAmountArr = "";			//Item [7]
			$tmpTDateArr = "";			//Item [8]
			$tmpLossArr = "";			//Item [9]			-> 0 == False for Loss
			$tmpPCGiveArr = "";			//Item [10]
			$tmpPCLetterArr = "";		//Item [11]
			$tmpDescripArr = "";		//Item [12]
			$tmpCommentsArr = "";		//Item [13]
			
			$tmpBool = TRUE;
			
			$tempAmountArr = array();
			$tempLossArr = array();
			
			for($m=0; $m<$tempCount; $m++){
				if($caseIDTemp == $tmpTransItems[$m][0]){
					/*
					array_push($tmpAmountArr, $tmpTransItems[$m][1]);
					array_push($tmpTDateArr, $tmpTransItems[$m][2]);
					array_push($tmpLossArr, $tmpTransItems[$m][3]);		
					array_push($tmpPCGiveArr, $tmpTransItems[$m][4]);
					array_push($tmpPCLetterArr, $tmpTransItems[$m][5]);
					array_push($tmpDescripArr, $tmpTransItems[$m][6]);
					array_push($tmpCommentsArr, $tmpTransItems[$m][7]);
					*/		
					
					$tmpLossVal = "";
					
					array_push($tempAmountArr, $tmpTransItems[$m][1]);
					
					if($tmpTransItems[$m][3] == 0){			//If bool == 0  then  == FALSE
						array_push($tempLossArr, FALSE);
						$tmpLossVal = "No Loss";
					}
					else{									//Else bool == 1  then == TRUE
						array_push($tempLossArr, TRUE);
						$tmpLossVal = "Loss";
					}
					
					if($tmpBool){
						$tmpAmountArr = $tmpTransItems[$m][1];
						$tmpTDateArr = $tmpTransItems[$m][2];
						$tmpLossArr = $tmpLossVal;
						$tmpPCGiveArr = $tmpTransItems[$m][4];
						$tmpPCLetterArr = $tmpTransItems[$m][5];
						$tmpDescripArr = $tmpTransItems[$m][6];
						$tmpCommentsArr = $tmpTransItems[$m][7];
						
						$tmpBool = FALSE;
					}
					else{
						$tmpAmountArr = $tmpAmountArr . " \n" . $tmpTransItems[$m][1];
						$tmpTDateArr = $tmpTDateArr . " \n" . $tmpTransItems[$m][2];
						$tmpLossArr = $tmpLossArr . " \n" . $tmpLossVal;
						$tmpPCGiveArr = $tmpPCGiveArr . " \n" . $tmpTransItems[$m][4];
						$tmpPCLetterArr = $tmpPCLetterArr . " \n" . $tmpTransItems[$m][5];
						$tmpDescripArr = $tmpDescripArr . " \n" . $tmpTransItems[$m][6];
						$tmpCommentsArr = $tmpCommentsArr . " \n" . $tmpTransItems[$m][7];
					}
				}
			}
			
			
			$tempAmountCount = count($tempAmountArr);
			$tempLossCount = count($tempLossArr);
			
			if($tempAmountCount != $tempLossCount){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Error: Code Count Issue! Please contact the System Admin.';
				header( "Location: generateReports.php" );
				exit();
			}
			
			$TempTotalLOSS = 0;
			$TempTotalRECOVERED = 0;

			for($m=0; $m<$tempAmountCount; $m++){
		
				if($tempLossArr[$m] == FALSE){
					$TempTotalRECOVERED = $TempTotalRECOVERED + $tempAmountArr[$m];
				}
				else if($tempLossArr[$m] == TRUE){
					$TempTotalLOSS = $TempTotalLOSS + $tempAmountArr[$m];
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Error: Amount Count Issue! Please contact the System Admin.';
					header( "Location: generateReports.php" );
					exit();
				}
				
			}
			
			array_push($tempArray, $tmpAmountArr);
			array_push($tempArray, $tmpTDateArr);
			array_push($tempArray, $tmpLossArr);
			array_push($tempArray, $tmpPCGiveArr);
			array_push($tempArray, $tmpPCLetterArr);
			array_push($tempArray, $tmpDescripArr);
			array_push($tempArray, $tmpCommentsArr);
			
			array_push($tempArray, $TempTotalRECOVERED);
			array_push($tempArray, $TempTotalLOSS);
			
			//var_dump($tempArray); 
			
			array_push($newReportArray, $tempArray);
			
			//Comment strip Not Needed, but left here in case need arises in future.
			//fputcsv pushes each array element (which is an array itself) into a comma seperated line.
				//This comma eperated line contains quotes around columns/cells/data that is not numerical like: "1/1/2017, 8:45 pm" as a cell item.
			
			//array_push($newReportArray, str_replace(',', '', $tempArray));
			
			//print "BREAK<br>";
		}
		
		
		//print "CSV Array:\n";
		//var_dump($newReportArray);
		//exit();
		
		date_default_timezone_set("America/Chicago");
		$timeofChange = date("Y-m-d_H-i-s");
		
		$DirectoryFiles = "FileFolder/GeneratedReports/".$timeofChange."_report_".$fname."_".$lname.".csv";
		
		$File_handle = fopen($DirectoryFiles, "w");

		foreach($newReportArray as $fields){
			
			fputcsv($File_handle, $fields, $delimiter=',', $enclosure = '"');
			
		}		
		
		fclose($File_handle);
		
	}
	
	if(isset($_POST["SubmitREQUESTED"])){
		
		if(isset($_POST["preformedReport"])){
			
			//var_dump($_POST);
			//var_dump($_POST["preformedReport"]);
			
			$ValidReport_array = array("LHnormalREPORT", "LHsmallrandomREPORT", "LHsmallrandomREPORT_lastquarter", "MSallQuarterlyReport");
			
			if(in_array($_POST["preformedReport"], $ValidReport_array)){
				
				/*
				Create Title
				Pull Information
					Have information in correct format, in the new report array
				Write to file
				*/
				
				$nameofreport = "";
				
				$TitleTemp = array();
				$newReportArray = array();
				
				if($_POST["preformedReport"] == "LHnormalREPORT"){
					
					$nameofreport = "Full";
					
					array_push($TitleTemp, "Case Number");
					array_push($TitleTemp, "Start Date");
					array_push($TitleTemp, "Account Number");
					array_push($TitleTemp, "Card Number");
					//array_push($TitleTemp, "Red Flag");
					//array_push($TitleTemp, "Sev Lev");
					array_push($TitleTemp, "Transaction Number");
					array_push($TitleTemp, "Transaction Amounts");
					array_push($TitleTemp, "Transaction Losses");
					array_push($TitleTemp, "Transaction Dates");
					array_push($TitleTemp, "PC Given Date");
					array_push($TitleTemp, "PC Letter Sent Date");
					array_push($TitleTemp, "Total Recovered");
					array_push($TitleTemp, "Total Loss");
					
					array_push($newReportArray, $TitleTemp);
					
					$tmpReportSelect = "SELECT checkcases.id, checkcases.casestartdate, checkaccountnumbers.accountnumber, checkcardnumbers.cardnumber, checktransactions.id, checktransactions.amount, checktransactions.loss, checktransactions.transactiondate, checktransactions.procreditgiven, checktransactions.pclettersent FROM checkcases, checkaccountnumbers, checkcardnumbers, checktransactions WHERE checkcases.casedeleted = 0 AND checkcases.casedoneinput = 1 AND checkcases.id = checkaccountnumbers.caseid AND checkcases.id = checkcardnumbers.caseid AND checkcases.id = checktransactions.caseid AND checkcases.archive = 0 ORDER BY checkcases.id";
					$tmpReportFetch = $dtcon->query($tmpReportSelect);

					$tmpReportItems = $tmpReportFetch->fetch_all();
					
					//print "Array: <br>";
					//var_dump($tmpReportItems);
					//print "<br><br><br> Array: <br>";
					//var_dump($tmpReportItems[0]);
					//exit();
					
					$ReportItemSize = count($tmpReportItems);
					
					for ($i=0; $i < $ReportItemSize; $i++){
						
						$TempRowValue = array();
						
						array_push($TempRowValue, $tmpReportItems[$i][0]);
						array_push($TempRowValue, $tmpReportItems[$i][1]);
						array_push($TempRowValue, $tmpReportItems[$i][2]);
						array_push($TempRowValue, $tmpReportItems[$i][3]);
						array_push($TempRowValue, $tmpReportItems[$i][4]);
						array_push($TempRowValue, $tmpReportItems[$i][5]);
						
						if($tmpReportItems[$i][6] == 1){
							array_push($TempRowValue, "Loss");
						}
						else{
							array_push($TempRowValue, "No Loss");
						}
						
						array_push($TempRowValue, $tmpReportItems[$i][7]);
						array_push($TempRowValue, $tmpReportItems[$i][8]);
						array_push($TempRowValue, $tmpReportItems[$i][9]);
						
						if($tmpReportItems[$i][6] == 1){
							array_push($TempRowValue, 0);
							array_push($TempRowValue, $tmpReportItems[$i][5]);
						}
						else{
							array_push($TempRowValue, $tmpReportItems[$i][5]);
							array_push($TempRowValue, 0);
						}
						
						//At this point TempRowValue is complete for the given row
						
						array_push($newReportArray, $TempRowValue);
						
					}
					
					//print "Array:\n";
					//var_dump($newReportArray);
					//exit();
					
					//print "CSV Array:\n";
					//var_dump($newReportArray);
					//exit();
					
					date_default_timezone_set("America/Chicago");
					$timeofChange = date("Y-m-d_H-i-s");
					
					$DirectoryFiles = "FileFolder/GeneratedReports/".$timeofChange."_".$nameofreport."Report_".$fname."_".$lname.".csv";
					
					$File_handle = fopen($DirectoryFiles, "w");

					foreach($newReportArray as $fields){
						
						fputcsv($File_handle, $fields, $delimiter=',', $enclosure = '"');
						
					}		
					
					fclose($File_handle);
					
					
				}
				elseif($_POST["preformedReport"] == "LHsmallrandomREPORT"){
					
					$nameofreport = "Sample";
					
					array_push($TitleTemp, "Case Number");
					array_push($TitleTemp, "Start Date");
					array_push($TitleTemp, "Account Number");
					array_push($TitleTemp, "Card Number");
					//array_push($TitleTemp, "Red Flag");
					//array_push($TitleTemp, "Sev Lev");
					array_push($TitleTemp, "Transaction Number");
					array_push($TitleTemp, "Transaction Amounts");
					array_push($TitleTemp, "Transaction Losses");
					array_push($TitleTemp, "Transaction Dates");
					array_push($TitleTemp, "PC Given Date");
					array_push($TitleTemp, "PC Letter Sent Date");
					array_push($TitleTemp, "Total Recovered");
					array_push($TitleTemp, "Total Loss");
					
					array_push($newReportArray, $TitleTemp);
					
					/*
					//Unneeded code See below:
					
					$tempcaseidselect = "SELECT id FROM checkcases";
					$tmpcaseidFetch = $dtcon->query($tempcaseidselect);

					$tmpcaseItems = $tmpcaseidFetch->fetch_all();
					
					$CaseID_ItemSize = count($tmpcaseItems);
					
					$Numberofcasesdesired = $CaseID_ItemSize % 4;
					
					$caseidsChoosen = array();
					*/
					
					
					$tmpReportSelect = "SELECT checkcases.id, checkcases.casestartdate, checkaccountnumbers.accountnumber, checkcardnumbers.cardnumber, checktransactions.id, checktransactions.amount, checktransactions.loss, checktransactions.transactiondate, checktransactions.procreditgiven, checktransactions.pclettersent, checktransactions.transactiondeleted FROM checkcases, checkaccountnumbers, checkcardnumbers, checktransactions WHERE checkcases.casedeleted = 0 AND checktransactions.transactiondeleted = 0 AND checkcases.casedoneinput = 1 AND checkcases.id = checkaccountnumbers.caseid AND checkcases.id = checkcardnumbers.caseid AND checkcases.id = checktransactions.caseid AND checkcases.archive = 0 ORDER BY checkcases.id";
					$tmpReportFetch = $dtcon->query($tmpReportSelect);
					$tmpReportItems = $tmpReportFetch->fetch_all();
					
					$ReportItemSize = count($tmpReportItems);
					
					$tmpCseIDSelect = "SELECT checkcases.id FROM checkcases, checkaccountnumbers, checkcardnumbers, checktransactions WHERE checkcases.casedeleted = 0 AND checkcases.casedoneinput = 1 AND checkcases.id = checkaccountnumbers.caseid AND checkcases.id = checkcardnumbers.caseid AND checkcases.id = checktransactions.caseid AND checkcases.archive = 0 ORDER BY checkcases.id";
					$tmpCseIDFetch = $dtcon->query($tmpCseIDSelect);
					$tmpCseIDItems =$tmpCseIDFetch->fetch_all();
					
					
					$CaseIDItemSize = count($tmpCseIDItems);
					
					$tempArrayforIDs = array();
					
					foreach($tmpCseIDItems as $value){
						
						array_push($tempArrayforIDs, $value[0]);
						
					}
					
					$tmpCseIDItems = $tempArrayforIDs;
					
					$CaseIDItemSize = count($tmpCseIDItems);	
					
					$Numberofcasesdesired = $CaseIDItemSize / 5;
					
					$cutdownReportItems = array();
					
					$cutdownCaseIDItems = array();
					
					shuffle($tmpCseIDItems);
					
					$cutdownCaseIDItems = array_slice($tmpCseIDItems, 0, $Numberofcasesdesired);
					
					for($i=0; $i < $ReportItemSize; $i++){
						
						if (in_array($tmpReportItems[$i][0], $cutdownCaseIDItems)){
							
							array_push($cutdownReportItems, $tmpReportItems[$i]);
							
						}
						
					}
					
					$NewReportItemSize = count($cutdownReportItems);
					
					for ($i=0; $i < $NewReportItemSize; $i++){
						
						$TempRowValue = array();
						
						array_push($TempRowValue, $cutdownReportItems[$i][0]);
						array_push($TempRowValue, $cutdownReportItems[$i][1]);
						array_push($TempRowValue, $cutdownReportItems[$i][2]);
						array_push($TempRowValue, $cutdownReportItems[$i][3]);
						array_push($TempRowValue, $cutdownReportItems[$i][4]);
						array_push($TempRowValue, $cutdownReportItems[$i][5]);
						
						if($cutdownReportItems[$i][6] == 1){
							array_push($TempRowValue, "Loss");
						}
						else{
							array_push($TempRowValue, "No Loss");
						}
						
						array_push($TempRowValue, $cutdownReportItems[$i][7]);
						array_push($TempRowValue, $cutdownReportItems[$i][8]);
						array_push($TempRowValue, $cutdownReportItems[$i][9]);
						
						if($cutdownReportItems[$i][6] == 1){
							array_push($TempRowValue, 0);
							array_push($TempRowValue, $cutdownReportItems[$i][5]);
						}
						else{
							array_push($TempRowValue, $cutdownReportItems[$i][5]);
							array_push($TempRowValue, 0);
						}
						
						//At this point TempRowValue is complete for the given row
						
						array_push($newReportArray, $TempRowValue);
						
					}
					
					//print "CSV Array:\n";
					//var_dump($newReportArray);
					//exit();
					
					date_default_timezone_set("America/Chicago");
					$timeofChange = date("Y-m-d_H-i-s");
					
					$DirectoryFiles = "FileFolder/GeneratedReports/".$timeofChange."_".$nameofreport."Report_".$fname."_".$lname.".csv";
					
					$File_handle = fopen($DirectoryFiles, "w");

					foreach($newReportArray as $fields){
						
						fputcsv($File_handle, $fields, $delimiter=',', $enclosure = '"');
						
					}		
					
					fclose($File_handle);
					
					
					
				}
				elseif($_POST["preformedReport"] == "LHsmallrandomREPORT_lastquarter"){
					
					$nameofreport = "QuarterSample";
					
					array_push($TitleTemp, "Case Number");
					array_push($TitleTemp, "Start Date");
					array_push($TitleTemp, "Account Number");
					array_push($TitleTemp, "Card Number");
					//array_push($TitleTemp, "Red Flag");
					//array_push($TitleTemp, "Sev Lev");
					array_push($TitleTemp, "Transaction Number");
					array_push($TitleTemp, "Transaction Amounts");
					array_push($TitleTemp, "Transaction Losses");
					array_push($TitleTemp, "Transaction Dates");
					array_push($TitleTemp, "PC Given Date");
					array_push($TitleTemp, "PC Letter Sent Date");
					array_push($TitleTemp, "Total Recovered");
					array_push($TitleTemp, "Total Loss");
					
					array_push($newReportArray, $TitleTemp);
					
					
					//$tmpReportSelect = "SELECT checkcases.id, checkcases.casestartdate, checkaccountnumbers.accountnumber, checkcardnumbers.cardnumber, checktransactions.id, checktransactions.amount, checktransactions.loss, checktransactions.transactiondate, checktransactions.procreditgiven, checktransactions.pclettersent, checktransactions.transactiondeleted FROM checkcases, checkaccountnumbers, checkcardnumbers, checktransactions WHERE checkcases.casedeleted = 0 AND checktransactions.transactiondeleted = 0 AND checkcases.casedoneinput = 1 AND checkcases.id = checkaccountnumbers.caseid AND checkcases.id = checkcardnumbers.caseid AND checkcases.id = checktransactions.caseid AND checkcases.archive = 0 ORDER BY checkcases.id";
					//$tmpReportFetch = $dtcon->query($tmpReportSelect);
					//$tmpReportItems = $tmpReportFetch->fetch_all();
					
					//$ReportItemSize = count($tmpReportItems);
					
					//$tmpCseIDSelect = "SELECT checkcases.id FROM checkcases, checkaccountnumbers, checkcardnumbers, checktransactions WHERE checkcases.casedeleted = 0 AND checkcases.casedoneinput = 1 AND checkcases.id = checkaccountnumbers.caseid AND checkcases.id = checkcardnumbers.caseid AND checkcases.id = checktransactions.caseid AND checkcases.archive = 0 ORDER BY checkcases.id";
					//$tmpCseIDFetch = $dtcon->query($tmpCseIDSelect);
					//$tmpCseIDItems =$tmpCseIDFetch->fetch_all();
					
					
					//print "Case ID Array: <br>";
					//var_dump($tmpCseIDItems);
					//print "<br><br>number of IDs: <br>";
					//var_dump(count($tmpCseIDItems));
					//print "<br><br>Full report Array: <br>";
					//var_dump($tmpReportItems);
					//print "<br><br>count: <br>";
					//var_dump($ReportItemSize);
					//print "<br><br>_____________________________________________________ <br><br><br>";
					
					//$CaseIDItemSize = count($tmpCseIDItems);
					
					//Dates are in form "YYYY-mm-dd_HH:ii:ss"
					
					date_default_timezone_set("America/Chicago");
					$monthNow = date("m");
					$YearNow = date("Y");
					
					//print "<br><br>month now <br>";
					//var_dump($monthNow);
					//print "<br><br>year now: <br>";
					//var_dump($YearNow);
					//var_dump(($YearNow-1));
					//exit();
					
					
					$quarterone = array("01", "02", "03");
					$quartertwo = array("04", "05", "06");
					$quarterthr = array("07", "08", "09");
					$quarterfou = array("10", "11", "12");
					
					$datetostart_new = "";
					$datetoend_new = "";
					
					if(in_array($monthNow, $quarterone)){
						//User wants quarterfour
						
						$YearBefore = $YearNow - 1;
						
						$datetostart_new = $YearBefore . "-10-01";
						$datetoend_new = $YearBefore . "-12-31";
					}
					elseif(in_array($monthNow, $quartertwo)){
						//User wants quarterone
						
						$datetostart_new = $YearNow . "-01-01";
						$datetoend_new = $YearNow . "-03-31";
					}
					elseif(in_array($monthNow, $quarterthr)){
						//User wants quartertwo
						
						$datetostart_new = $YearNow . "-04-01";
						$datetoend_new = $YearNow . "-06-31";
					}
					elseif(in_array($monthNow, $quarterfou)){
						//User wants quarterthr
						
						$datetostart_new = $YearNow . "-07-01";
						$datetoend_new = $YearNow . "-09-31";
					}
					else{
						$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong, please select a report to print or contact the system admin.';
						header( "Location: generateReports.php" );
						exit();
					}
										
					$newReportSelect = "SELECT checkcases.id, checkcases.casestartdate, checkaccountnumbers.accountnumber, checkcardnumbers.cardnumber, checktransactions.id, checktransactions.amount, checktransactions.loss, checktransactions.transactiondate, checktransactions.procreditgiven, checktransactions.pclettersent, checktransactions.transactiondeleted FROM checkcases, checkaccountnumbers, checkcardnumbers, checktransactions WHERE checkcases.casedeleted = 0 AND checktransactions.transactiondeleted = 0 AND checkcases.casedoneinput = 1 AND checkcases.id = checkaccountnumbers.caseid AND checkcases.id = checkcardnumbers.caseid AND checkcases.id = checktransactions.caseid AND checkcases.archive = 0 AND checkcases.casestartdate >='".$datetostart_new." 00:00:00' AND checkcases.casestartdate <='".$datetoend_new." 00:00:00' ORDER BY checkcases.id";				
					$newReportFetch = $dtcon->query($newReportSelect);
					$newReportitems =$newReportFetch->fetch_all();
					
					$newReportitems_count = count($newReportitems);
					
					//print "<br><br>SQL: <br>";
					//var_dump($newReportSelect);
					
					//print "<br><br>Reports: <br>";
					//var_dump($newReportitems);
					//print "<br><br>number of IDs: <br>";
					//var_dump($newReportitems_count);
					//exit();
					
					$tempArrayforIDs = array();
					$uniqueArrayforIDs = array();
					
					foreach($newReportitems as $value){
						
						array_push($tempArrayforIDs, $value[0]);
						
					}
					
					//tempArrayforIDs same size as newReportitems
					//tempArrayforIDs only has IDs
					
					$uniqueArrayforIDs = array_unique($tempArrayforIDs);
					
					//Gets rid of duplicates
					
					$unquieIDcount = count($uniqueArrayforIDs);
					
					$Numberofcasesdesired = floor($unquieIDcount / 5);
					
					$cutdownReportItems = array();
					
					$cutdownCaseIDItems = array();
					
					shuffle($uniqueArrayforIDs);
					
					$cutdownCaseIDItems = array_slice($uniqueArrayforIDs, 0, $Numberofcasesdesired);
					
					//print "<br><br> Number of Cut down Cases: <br>";
					//var_dump(count($cutdownCaseIDItems));
					//print "<br><br> Cut down Cases: <br>";
					//var_dump($cutdownCaseIDItems);
					
					for($i=0; $i < $newReportitems_count; $i++){
						
						//print "<br><br> Loop count <br>";
						//var_dump($i);
						//print "<br><br>Case Number: <br>";
						//var_dump($newReportitems[$i][0]);
						//print "<br><br> True/False: <br>";
						//var_dump(in_array($newReportitems[$i][0], $cutdownCaseIDItems));
						//print "<br><br><br>";
						
						if (in_array($newReportitems[$i][0], $cutdownCaseIDItems)){
														
							array_push($cutdownReportItems, $newReportitems[$i]);
							
						}
						
					}
					
					$NewReportItemSize = count($cutdownReportItems);
					
					//print "<br><br>Array: <br>";
					//var_dump($newReportitems);
					//print "<br><br><br> Array: <br>";
					//var_dump($newReportitems[0]);
					//print "<br><br><br> Total Cases: <br>";
					//var_dump($newReportitems_count);
					//print "<br>Unique ID Count: <br>";
					//var_dump($unquieIDcount);
					//print "<br>Number of Cut Down Cases: <br>";
					//var_dump($Numberofcasesdesired);
					//print "<br><br><br>Array: <br>";
					//var_dump($cutdownCaseIDItems);
					//print "<br><br><br>Array: <br>";
					//var_dump($cutdownReportItems);
					//print "<br><br><br>Count: <br>";
					//var_dump($NewReportItemSize);
					//exit();
					
					for ($i=0; $i < $NewReportItemSize; $i++){
						
						$TempRowValue = array();
						
						array_push($TempRowValue, $cutdownReportItems[$i][0]);
						array_push($TempRowValue, $cutdownReportItems[$i][1]);
						array_push($TempRowValue, $cutdownReportItems[$i][2]);
						array_push($TempRowValue, $cutdownReportItems[$i][3]);
						array_push($TempRowValue, $cutdownReportItems[$i][4]);
						array_push($TempRowValue, $cutdownReportItems[$i][5]);
						
						if($cutdownReportItems[$i][6] == 1){
							array_push($TempRowValue, "Loss");
						}
						else{
							array_push($TempRowValue, "No Loss");
						}
						
						array_push($TempRowValue, $cutdownReportItems[$i][7]);
						array_push($TempRowValue, $cutdownReportItems[$i][8]);
						array_push($TempRowValue, $cutdownReportItems[$i][9]);
						
						if($cutdownReportItems[$i][6] == 1){
							array_push($TempRowValue, 0);
							array_push($TempRowValue, $cutdownReportItems[$i][5]);
						}
						else{
							array_push($TempRowValue, $cutdownReportItems[$i][5]);
							array_push($TempRowValue, 0);
						}
						
						//At this point TempRowValue is complete for the given row
						
						array_push($newReportArray, $TempRowValue);
						
					}
					
					//print "CSV Array:\n";
					//var_dump($newReportArray);
					//exit();
					
					date_default_timezone_set("America/Chicago");
					$timeofChange = date("Y-m-d_H-i-s");
					
					$DirectoryFiles = "FileFolder/GeneratedReports/".$timeofChange."_".$nameofreport."Report_".$fname."_".$lname.".csv";
					
					$File_handle = fopen($DirectoryFiles, "w");

					foreach($newReportArray as $fields){
						
						fputcsv($File_handle, $fields, $delimiter=',', $enclosure = '"');
						
					}		
					
					fclose($File_handle);
					
					
				}
				elseif($_POST["preformedReport"] == "MSallQuarterlyReport"){
					
					//$nameofreport = "MS_QuarterFull";
					
					$CustomerFRAUDlosses = array();
					$CustomerOTHERlosses = array();
					
					//$BusinessFRAUDlosses = array();
					//$BusinessOTHERlosses = array();
					
					//checkaccountnumbers.businessaccount == 0
					
					array_push($CustomerFRAUDlosses, "Case Number");
					array_push($CustomerFRAUDlosses, "Start Date");
					array_push($CustomerFRAUDlosses, "Gross Amount");
					array_push($CustomerFRAUDlosses, "Recoveries");
					array_push($CustomerFRAUDlosses, "Net");
					
					array_push($CustomerOTHERlosses, "Case Number");
					array_push($CustomerOTHERlosses, "Start Date");
					array_push($CustomerOTHERlosses, "Gross Amount");
					array_push($CustomerOTHERlosses, "Recoveries");
					array_push($CustomerOTHERlosses, "Net");
					
					//checkaccountnumbers.businessaccount == 1
					
					//array_push($BusinessFRAUDlosses, "Case Number");
					//array_push($BusinessFRAUDlosses, "Start Date");
					//array_push($BusinessFRAUDlosses, "Gross Amount");
					//array_push($BusinessFRAUDlosses, "Recoveries");
					//array_push($BusinessFRAUDlosses, "Net");
					
					//array_push($BusinessOTHERlosses, "Case Number");
					//array_push($BusinessOTHERlosses, "Start Date");
					//array_push($BusinessOTHERlosses, "Gross Amount");
					//array_push($BusinessOTHERlosses, "Recoveries");
					//array_push($BusinessOTHERlosses, "Net");
					
					//array_push($newReportArray, $TitleTemp);
					
					
					$tmpReportSelect = "SELECT checkcases.id, checkcases.casestartdate, checkcases.redflag, checkcardnumbers.cardnumber, checkaccountnumbers.businessaccount, checktransactions.amount, checktransactions.loss, checktransactions.transactiondeleted FROM checkcases, checkaccountnumbers, checkcardnumbers, checktransactions WHERE checkcases.casedeleted = 0 AND checktransactions.transactiondeleted = 0 AND checkcases.casedoneinput = 1 AND checkcases.id = checkaccountnumbers.caseid AND checkcases.id = checkcardnumbers.caseid AND checkcases.id = checktransactions.caseid AND checkcases.archive = 0 ORDER BY checkcases.id";
					$tmpReportFetch = $dtcon->query($tmpReportSelect);
					$tmpReportItems = $tmpReportFetch->fetch_all();
					
					$ReportItemSize = count($tmpReportItems);
					
					$tmpCseIDSelect = "SELECT checkcases.id FROM checkcases, checkaccountnumbers, checkcardnumbers, checktransactions WHERE checkcases.casedeleted = 0 AND checkcases.casedoneinput = 1 AND checkcases.id = checkaccountnumbers.caseid AND checkcases.id = checkcardnumbers.caseid AND checkcases.id = checktransactions.caseid AND checkcases.archive = 0 ORDER BY checkcases.id";
					$tmpCseIDFetch = $dtcon->query($tmpCseIDSelect);
					$tmpCseIDItems =$tmpCseIDFetch->fetch_all();
					
					
					//print "Case ID Array: <br>";
					//var_dump($tmpCseIDItems);
					//print "<br><br>number of IDs: <br>";
					//var_dump(count($tmpCseIDItems));
					//print "<br><br>Full report Array: <br>";
					//var_dump($tmpReportItems);
					//print "<br><br>count: <br>";
					//var_dump($ReportItemSize);
					//exit();
					
					
					//$CaseIDItemSize = count($tmpCseIDItems);
					
					date_default_timezone_set("America/Chicago");
					$monthNow = date("m");
					
					$quarterone = array("01", "02", "03");
					$quartertwo = array("04", "05", "06");
					$quarterthr = array("07", "08", "09");
					$quarterfou = array("10", "11", "12");
					
					
					if(in_array($monthNow, $quarterone)){
						//User wants quarterfou
						
						for($i=0; $i < $ReportItemSize; $i++){
							
							$tempStartDate = $tmpReportItems[$i][1]; 			// "YYYY-mm-dd_HH:ii:ss"
							
							$tempStartDate = substr($tempStartDate, 5, 2); 		// "mm"
							
							if(!in_array($tempStartDate, $quarterfou)){
								unset($tmpCseIDItems[$i]);
							}
						}						
					}
					elseif(in_array($monthNow, $quartertwo)){
						//User wants quarterone
						
						for($i=0; $i < $ReportItemSize; $i++){
							
							$tempStartDate = $tmpReportItems[$i][1]; 			// "YYYY-mm-dd_HH:ii:ss"
							
							$tempStartDate = substr($tempStartDate, 5, 2); 		// "mm"
							
							//var_dump($quarterone);
							//print "<br>";
							//var_dump($tempStartDate);
							//print "<br>";
							
							if(!in_array($tempStartDate, $quarterone)){
								unset($tmpCseIDItems[$i]);
							}
						}
					}
					elseif(in_array($monthNow, $quarterthr)){
						//User wants quartertwo
						
						for($i=0; $i < $ReportItemSize; $i++){
							
							$tempStartDate = $tmpReportItems[$i][1]; 			// "YYYY-mm-dd_HH:ii:ss"
							
							$tempStartDate = substr($tempStartDate, 5, 2); 		// "mm"
							
							if(!in_array($tempStartDate, $quartertwo)){
								unset($tmpCseIDItems[$i]);
							}
						}
					}
					elseif(in_array($monthNow, $quarterfou)){
						//User wants quarterthr
						
						for($i=0; $i < $ReportItemSize; $i++){
							
							$tempStartDate = $tmpReportItems[$i][1]; 			// "YYYY-mm-dd_HH:ii:ss"
							
							$tempStartDate = substr($tempStartDate, 5, 2); 		// "mm"
							
							if(!in_array($tempStartDate, $quarterthr)){
								unset($tmpCseIDItems[$i]);
							}
						}
					}
					else{
						$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong, please select a report to print or contact the system admin.';
						header( "Location: generateReports.php" );
						exit();
					}
					
					//print "<br><br>All Cases Array: <br>";
					//var_dump($tmpReportItems);
					//print "<br><br>Case ID Array: <br>";
					//var_dump($tmpCseIDItems);
					//print "<br><br>number of IDs: <br>";
					//var_dump(count($tmpCseIDItems));
					//exit();
					
					$CaseIDItemSize = count($tmpCseIDItems);
					
					$tempArrayforIDs = array();
					
					foreach($tmpCseIDItems as $value){
						
						array_push($tempArrayforIDs, $value[0]);
						
					}
					
					//print "<br><br>Case ID Array: <br>";
					//var_dump($tmpCseIDItems);
					//print "<br><br>number of IDs: <br>";
					//var_dump($tempArrayforIDs);
					//exit();
					
					$tmpCseIDItems = $tempArrayforIDs;
					
					$CaseIDItemSize = count($tmpCseIDItems);	
					
					$cutdownReportItems = array();
					
					for($i=0; $i < $ReportItemSize; $i++){
						
						if (in_array($tmpReportItems[$i][0], $tmpCseIDItems)){
							
							array_push($cutdownReportItems, $tmpReportItems[$i]);
							
						}
						
					}
					
					$NewReportItemSize = count($cutdownReportItems);
					
					//print "<br><br>Array: <br>";
					//var_dump($tmpReportItems);
					//print "<br><br><br> Array: <br>";
					//var_dump($tmpReportItems[0]);
					//print "<br><br><br> Total Cases:: <br>";
					//var_dump($CaseIDItemSize);
					//print "<br>Cut Down Cases: <br>";
					//var_dump($tmpCseIDItems);
					//print "<br><br><br>Array: <br>";
					//var_dump($cutdownReportItems);
					//print "<br><br><br>Count: <br>";
					//var_dump($NewReportItemSize);
					//exit();
					
					//So right now, cutdownReportItems is the full list of cases that are within the desired quarter.
					//NewReportItemSize is the count of that array.
					
					$FRAUDlosses = array();
					$OTHERlosses = array();
					
					//cutdownReportItems is the full list, Fraud and Other items. 
					//Business and Consumer accounts.
					//Business/consumer doesnt matter now -ZA 10-2-2016
					
					//cutdownReportItems:
					//[0] = ID
					//[1] = Start Date (YYYY-mm-dd_HH)
					//[2] = Red Flag
					//[3] = Card Number
					//[4] = Business Account = 1 || Customers = 0
					//[5] = Transaction Amount
					//[6] = Loss = 1 || No Loss = 0
					
					$tempCASEid = $cutdownReportItems[0][0];
					$runningTotalLOST = 0;
					$runningTotalRECOVERED = 0;	
					
					//var_dump($cutdownReportItems[0]);
					//print("<br>Start of Loop: <br><br>");
					
					for($i=0; $i < $NewReportItemSize; $i++){
						
						//print("<br>LoopBeginning ");
						//var_dump($cutdownReportItems[$i][0]);
						//print "<br>";
						//var_dump($tempCASEid);
						//print "<br>";
						//var_dump($i);
						//print "<br>";
						
						if($tempCASEid == $cutdownReportItems[$i][0]){
							
							if($cutdownReportItems[$i][6] == "1"){
								//amount is a loss add it up
								$runningTotalLOST = $runningTotalLOST + $cutdownReportItems[$i][5];
							}
							else{
								//not a loss add it up
								$runningTotalRECOVERED = $runningTotalRECOVERED + $cutdownReportItems[$i][5];
							}							
						}
						else{
							
							//print "<br>InsideElse<br>";
							//var_dump($cutdownReportItems[$i][0]);
							//print "<br>";
							//var_dump($cutdownReportItems[$i][2]);
							
							//New Case line, so write down old information, then start adding up new information
							if($cutdownReportItems[$i-1][2] == "1"){
								//Fraud
								$TOTALAMOUNT = $runningTotalLOST + $runningTotalRECOVERED;
								$FRAUDlosses[] = array($cutdownReportItems[$i-1][0], $TOTALAMOUNT, $runningTotalRECOVERED, $runningTotalLOST);
									
							}
							else{
								//Other
								$TOTALAMOUNT = $runningTotalLOST + $runningTotalRECOVERED;
								$OTHERlosses[] = array($cutdownReportItems[$i-1][0], $TOTALAMOUNT, $runningTotalRECOVERED, $runningTotalLOST);
								
							}
							
							$tempCASEid = $cutdownReportItems[$i][0]; //This line is for the new case number
							
							$runningTotalLOST = 0;
							$runningTotalRECOVERED = 0;	
							
							//New Case number means reset values to 0 and start counting up again
							if($cutdownReportItems[$i][6] == "1"){
								//amount is a loss add it up
								$runningTotalLOST = 0 + $cutdownReportItems[$i][5];
							}
							else{
								//not a loss add it up
								$runningTotalRECOVERED = 0 + $cutdownReportItems[$i][5];
							}
							
						}
						
						if($i+1 == $NewReportItemSize){
							
							//LAST LINE
							if($cutdownReportItems[$i][2] == "1"){
								//Fraud
								$TOTALAMOUNT = $runningTotalLOST + $runningTotalRECOVERED;
								$FRAUDlosses[] = array($cutdownReportItems[$i][0], $TOTALAMOUNT, $runningTotalRECOVERED, $runningTotalLOST);
									
							}
							else{
								//Other
								$TOTALAMOUNT = $runningTotalLOST + $runningTotalRECOVERED;
								$OTHERlosses[] = array($cutdownReportItems[$i][0], $TOTALAMOUNT, $runningTotalRECOVERED, $runningTotalLOST);
								
							}
							
						}
						else{
							//pass
						}
						
						
					}
					
					//print "<br><br><br>OLD Array: <br>";
					//var_dump($cutdownReportItems);
					//print "<br><br><br>OLD Count: <br>";
					//var_dump($NewReportItemSize);
					//print "<br><br>FRAUD Array: <br>";
					//var_dump($FRAUDlosses);
					//print "<br><br><br> Array: <br>";
					//var_dump($OTHERlosses);
					//print "<br><br>FRAUD Array count: <br>";
					//var_dump(count($FRAUDlosses));
					//print "<br><br><br>OTHER Array count : <br>";
					//var_dump(count($OTHERlosses));
					//exit();
					
					array_unshift($FRAUDlosses,array("FRAUD LOSSES","GROSS AMOUNT","RECOVERIES","NET"));
					array_unshift($FRAUDlosses,array("VISA CHECK CARD LOSSES - FRAUD LOSSES"));
					
					array_unshift($OTHERlosses,array("OTHER LOSSES","GROSS AMOUNT","RECOVERIES","NET"));
					array_unshift($OTHERlosses,array("VISA CHECK CARD LOSSES - OTHER LOSSES"));
					
					//print "<br><br>FRAUD Array: <br>";
					//var_dump($FRAUDlosses);
					//print "<br><br><br> Array: <br>";
					//var_dump($OTHERlosses);
					//print "<br><br>FRAUD Array count: <br>";
					//var_dump(count($FRAUDlosses));
					//print "<br><br><br>OTHER Array count : <br>";
					//var_dump(count($OTHERlosses));
					//exit();
					
					date_default_timezone_set("America/Chicago");
					$timeofChange = date("Y-m-d_H-i-s");
					
					$fraud_DirectoryFiles = "FileFolder/GeneratedReports/".$timeofChange."_FraudLossesQuarterlyReport_".$fname."_".$lname.".csv";
					$other_DirectoryFiles = "FileFolder/GeneratedReports/".$timeofChange."_OtherLossesQuarterlyReport_".$fname."_".$lname.".csv";
					
					//Fraud Losses CSV
					$File_handle = fopen($fraud_DirectoryFiles, "w");
					foreach($FRAUDlosses as $fields){
						
						fputcsv($File_handle, $fields, $delimiter=',', $enclosure = '"');
						
					}		
					fclose($File_handle);
					
					//Other Losses CSV
					$File_handle = fopen($other_DirectoryFiles, "w");
					foreach($OTHERlosses as $fields){
						
						fputcsv($File_handle, $fields, $delimiter=',', $enclosure = '"');
						
					}		
					fclose($File_handle);
					
					$DirectoryFiles = "viewGeneratedDocuments.php";
					
				}
				else{
					$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong, please select a report to print or contact the system admin.';
					header( "Location: generateReports.php" );
					exit();

				}
				
				
				
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong, please select a report to print or contact the system admin.';
				header( "Location: generateReports.php" );
				exit();
			}
		}
		else{
			$_SESSION["ADD_DISPUTE_ERROR"] = 'Something went wrong, please select a report to print or contact the system admin.';
			header( "Location: generateReports.php" );
			exit();
		}
	}
	
	
	
}





?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - Generate Reports</title>
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
$PageTitle = "Dispute Track - Generate Reports";

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
                    <div class="col-md-5">
						<h2>Generate Reports:</h2>
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



?>

					</div>
                </div>
				
				<!--  Below H2, above HR  -->
				
				<hr />
				
				
				<form id="generateREQUESTEDreportID" autocomplete="off" name="generateREQUESTEDreport" method="post" action="generateReports.php">
					
					<div class='row'>
						<div class="col-md-4">
							<label>Select Preformed Report:</label>
							<select name="preformedReport" class="form-control" required>
								<option value="" selected="true">Select One</option>
								<option value="LHnormalREPORT">Normal Report for Linda hunter</option>
								<option value="LHsmallrandomREPORT">Small Random Report for Linda hunter</option>
								<option value="LHsmallrandomREPORT_lastquarter">Small Random Report from Last Quarter for Linda hunter</option>
								<option value="MSallQuarterlyReport">Quarterly Report for Mike Selfe/Raquel Rios</option>
							</select>	
						</div>
					</div>
					<br>
					<div class='row'>
						<div class="col-md-2">
							<button type="submit" class="btn btn-success" id="SubmitREQUESTEDid" name="SubmitREQUESTED" value="Create">Create</button>
						</div>
					</div>
					
				</form>
				
				
				<hr />
				
				
                
				<!-- /. ROW  -->
                
				<form id="generateReportID" autocomplete="off" name="generateReport" method="post" action="generateReports.php">
				
				<div class='row'>
					<div class="col-md-2">
						<label>Case ID</label>
						<input type="text" class="form-control" name="SearchCaseID" placeholder="Search">
					</div>
					<div class="col-md-2">
						<label>Name</label>
						<input type="text" class="form-control" name="SearchName" placeholder="Search">
					</div>
					<div class="col-md-2">
						<label>Phone</label>
						<input type="text" class="form-control" name="SearchPhone" placeholder="Search">
					</div>
					<div class="col-md-2">
						<label>Account Number</label>
						<input type="text" class="form-control" name="SearchAccNum" placeholder="Search">
					</div>
					<div class="col-md-2">
						<label>Card Number</label>
						<input type="text" class="form-control" name="SearchCardNum" placeholder="Search">
					</div>
				</div>
				<br>
				<div class='row'>
					<div class="col-md-2">
						<label>Case Start Date</label>
						<input type="text" class="form-control" name="SearchSDate" placeholder="Search" onFocus="showCalendarControl(this);">
					</div>
					<div class="col-md-2">
						<label>Case End Date</label>
						<input type="text" class="form-control" name="SearchEDate" placeholder="Search" onFocus="showCalendarControl(this);">
					</div>
					<div class="col-md-2">
						<label>Report All</label><br>
						<input type="checkbox" name="ReportALL" value="ReportALL"> Check box to make a report with all case data.</input><br>
					</div>
				</div>
				<br>
				<div class='row'>
					<div class="col-md-2">
						<button type="submit" class="btn btn-primary" id="SubmitSearchID" name="SubmitSearch" value="Search">Search</button>
					</div>
				</div>
				</form>
				
				<hr />
				
				<!--  Main Body  -->
				
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	
	if(isset($_POST["SubmitSearch"]) || isset($_POST["SubmitREQUESTED"])){
		
		print "<div class='row'>\n";
		print "<div class='col-md-4'>\n";
		
		print "<label align = 'center'>Your New Report:</label>\n";
		
		print "<a href=".$DirectoryFiles.">Click here to View</a>";
		
		print "</div>\n</div>";
		
	}






}


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
<?php

//Get the count for Visa Dispute Forms and "Print", 
//in this case printing is returning the value to the calling function.
$GeneratedFormPath = '../../FileFolder/VisaCheckCardDisputeForms/';

$GeneratedFormFiles = scandir($GeneratedFormPath);

$Count = count($GeneratedFormFiles);

//var_dump($GeneratedFormPath, $GeneratedFormFiles, count($GeneratedFormFiles));

print ($Count-2);
//print 50;

?>
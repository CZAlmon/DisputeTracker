<?php
//session_start();
error_reporting(0);
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"


$hostname_dtcon = "localhost";
$database_dtcon = "databasename";
$username_dtcon = "databasename";
$password_dtcon = "databasename";

/*
//Old connection Data
$dtcon = mysql_pconnect($hostname_dtcon, $username_dtcon, $password_dtcon) or trigger_error(mysql_error(),E_USER_ERROR); 

//print "i am here";

mysql_select_db($database_dtcon, $dtcon);
*/

$dtcon = new mysqli($hostname_dtcon, $username_dtcon, $password_dtcon, $database_dtcon);

if ($dtcon->connect_errno) {
    print "Failed to connect to MySQL: (" . $dtcon->connect_errno . ") " . $dtcon->connect_error;
	exit();
}



?>
<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once('./assets/includes/connection.php');
session_start();

//log them out
$logout="yes";
if ($logout=="yes"){ //destroy the session
	session_start();
	$_SESSION = array();
	session_destroy();
}

?>

<html>
<head>
<title>Card Dispute Tracking System</title>
</head>

<body>

<?php

print "<script type='text/javascript'>alert('You have logged out!')</script>";
//header("Location: index.php");
print "<meta http-equiv=\"refresh\" content=\"0;url= login.php\">";


//<p>You have successfully logged out!</p><br >
?>

</body>

</html>
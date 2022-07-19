<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once('assets/includes/connection.php');

session_start();
$_SESSION = array();
session_destroy();

session_start();

$username=strtolower($_POST["username"]); //remove case sensitivity on the username
$password=$_POST["password"];
$formage=$_POST["formage"];

//var_dump($_SESSION);

if ($_POST["oldform"]){ //prevent null bind

	//var_dump($_POST);
	//exit();
	
	//print "Success";

	if ($username!=NULL && $password!=NULL){
		//include the class and create a connection
		include ("assets/includes/adLDAP.php");
        try {
		    $adldap = new adLDAP();
        }
        catch (adLDAPException $e) {
            echo $e; exit();   
        }
		
		//authenticate the user
		if ($adldap -> authenticate($username,$password)){
			//establish your session and redirect
			session_start();
			$_SESSION["username"]=$username;
			
			date_default_timezone_set("America/Chicago");
			$_SESSION["time_started"]= date("Y-m-d_H:i:s");
			$user_ip=@$REMOTE_ADDR; 
			
			
			$string_query = "SELECT * FROM users where username='" . $username . "'";

			$res = $dtcon->query($string_query);

			$userData = $res->fetch_all();
			
			if (empty($userData)){
				$_SESSION["ADD_DISPUTE_ERROR"] = 'User Data Failed! Please Contact the System Admin.';
				unset($_SESSION["username"]);
				header( "Location: login.php" );
				exit();
			}
			
			//print "Success 2";
			
			$Fname=$userData[0][2];
			$Lname=$userData[0][3];
			$Location=$userData[0][5];
			$AccessLevel=$userData[0][4];
			$InActiveBOOL=$userData[0][6];
			
			//var_dump($userData);
			
			
			
			if ($InActiveBOOL=='0'){
				header("Location: index.php");
				exit;
			}
			else{
				$_SESSION["ADD_DISPUTE_ERROR"] ="<br><strong>You are inactive. Contact the System Admin<strong><br><br><br>";
			}
		}
	}
	$failed=1;
}


$fullname = ""; 
$accesslevel = 0;


?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispute Tracker - Login</title>
	<!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
     <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
   <!-- Favicon -->
   <link rel="icon" type="image/x-icon" href="./assets/img/favicon.ico" />
</head>
<body>
     
           
          
    <div id="wrapper">
        
		<?php include("assets/includes/head+menu.php"); ?>
		
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-2">
                     <h2>Login</h2> 
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
				
				
				
				This area is restricted.<br>
				Please login with your Windows/Network Credentials to continue.<br><br>
				
				<form method='post' action='<?php echo $_SERVER["PHP_SELF"]; ?>'>
				<input type='hidden' name='oldform' value='1'>
				
				<div class="row">
					<div class="col-md-6">
						<label>Username:</label>
						<input type='text' name='username' value='<?php echo ($username); ?>'><br>
						<label>Password:</label>
						<input type='password' name='password'><br>
					</div>
				</div>
					
				<br>

				<input type='submit' name='submit' value='Submit'><br>
				<?php if ($failed){ echo ("<br><font color='red'>Login Failed!</font><br><br>\n"); } ?>
				</form>
				
				
				
				
				
				
              
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


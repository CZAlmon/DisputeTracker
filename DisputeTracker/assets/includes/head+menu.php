<?php

//This file is called on every page load. It simply shows the header bar, and side panel with links.
//Links are hard coded to show up, or not, based on access level.

?>
		<div class="navbar navbar-inverse navbar-fixed-top">
            <div class="adjust-nav">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php"><i class=""></i>Dispute Tracker</a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
						<li><a href="#">Welcome: <?php print $fullname; ?></a></li>	<!--  Fullname or Fname?  -->
                        <li><a href="#">Contact IT - ####</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </div>

            </div>
        </div>
        <!-- /. NAV TOP  -->
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li class="text-center user-image-back">
                        <img src="assets/img/logo.png" class="img-responsive" />
                     
                    </li>


<?php

if ($accesslevel == 0){
	print '<li>';
	print '<a href="login.php"><i class=""></i>Login</a>';
	print '</li>';
}
if ($accesslevel >= 1){
	print '<li>';
	print '<a href="index.php"><i class=""></i>Home</a>';
	print '</li>';
}
if ($accesslevel >= 5){
	print '<li>';
	print '<a href="checkDisputes.php"><i class=""></i>Check Disputes</a>';
	print '</li>';
}
if ($accesslevel >= 3){
	print '<li>';
	print '<a href="newDispute.php"><i class=""></i>Add Dispute</a>';
	print '</li>';
}
if ($accesslevel >= 3){
	print '<li>';
	print '<a href="addAttachments.php"><i class=""></i>Add Attachments</a>';
	print '</li>';
}
if ($accesslevel >= 5){
	print '<li>';
	print '<a href="viewDisputes.php"><i class=""></i>View Disputes</a>';
	print '</li>';
}
if ($accesslevel == 3 || $accesslevel == 9){
	print '<li>';
	print '<a href="CSRViewDisputes.php"><i class=""></i>CSR View Disputes</a>';
	print '</li>';
}
if ($accesslevel == 1 || $accesslevel == 9){
	print '<li>';
	print '<a href="AuditViewDisputes.php"><i class=""></i>Audit View Disputes</a>';
	print '</li>';
}
if ($accesslevel >= 50000000){
	print '<li>';
	print '<a href="editDisputeSelect.php"><i class=""></i>Edit Disputes</a>';
	print '</li>';
}
if ($accesslevel >= 5 || $accesslevel == 1){
	print '<li>';
	print '<a href="generateReports.php"><i class=""></i>Generate Reports</a>';
	print '</li>';
}
if ($accesslevel >= 5 || $accesslevel == 1){
	print '<li>';
	print '<a href="viewGeneratedDocuments.php"><i class=""></i>View Documents</a>';
	print '</li>';
}
if ($accesslevel >= 5){
	print '<li>';
	print '<a href="ManualGenerateLetters.php"><i class=""></i>Manually Generate PC Letters</a>';
	print '</li>';
}
if ($accesslevel >= 5){
	print '<li>';
	print '<a href="StartReversalLetter.php"><i class=""></i>Generate Reversal Letters</a>';
	print '</li>';
}
if ($accesslevel >= 7){
	print '<li>';
	print '<a href="adminEdit.php"><i class=""></i>Admin Edit</a>';
	print '</li>';
}
if ($accesslevel >= 50000000){
	print '<li>';
	print '<a href="viewLog.php"><i class=""></i>View Change Log</a>';
	print '</li>';
}
if ($accesslevel >= 7){
	print '<li>';
	print '<a href="viewLogTEST.php"><i class=""></i>View Change Log</a>';
	print '</li>';
}
if ($accesslevel >= 7){
	print '<li>';
	print '<a href="addUser.php"><i class=""></i>Manage Users</a>';
	print '</li>'; 
}
if ($accesslevel >= 50000000){
	print '<li>';
	print '<a href="AdminViewDisputes.php"><i class=""></i>Admin View Disputes</a>';
	print '</li>';
}


?>

					
                </ul>

            </div>

        </nav>
		<!-- /. NAV SIDE  -->
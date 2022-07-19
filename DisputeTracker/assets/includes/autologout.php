<?php

//Auto-Logout - Get time at page load, then check every 5 minutes to see if you should log out. At 5 minutes, auto-'click' logout.php

?>


<script>

//Global First Load Time. Set on Page first Load, and only then.
var FirstLoadTime = new Date;

//One Hour constant
var ONE_HOUR = 60 * 60 * 1000;


//Always running Ajax Call. Always checking to see an hour has past.
//Runs every 300000 MilliSeconds, or 5 minutes. 
var InfiniteRequest = function () {
	
	setInterval(function(){
		
		var temptime = (new Date);
		var tempDiff = temptime - FirstLoadTime;
		
		if(tempDiff >= ONE_HOUR){
			
			window.location.href = "logout.php";
			
		}
		
	}, 300000); //Time Set Here.
};


InfiniteRequest (); //Inital Ajax Call.


</script>


















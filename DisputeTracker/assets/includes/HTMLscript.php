
<?php

//$PageTitle is the variable for the Title Data, Variable Set within each Page.

?>

<script>

//Global First Load count. Set on Page first Load, and only then.
var FirstLoadCount = 0;

var timeofLoad = performance.timing.domLoading;

//https://www.w3schools.com/xml/ajax_php.asp for reference
function getFirstCount(){
	
	var xmlhttp = new XMLHttpRequest();
	
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			FirstLoadCount = this.responseText;
		}
	};
	
	xmlhttp.open("POST", "assets/includes/titleTab.php", true);
	xmlhttp.send();
	
}

getFirstCount();

//http://stackoverflow.com/questions/5052543/how-to-fire-ajax-request-periodically for reference
//Always running Ajax Call. Always checking to see if a new dispute is set.
//Runs every 30000 MilliSeconds, or 30 Seconds. 
var InfiniteAjaxRequest = function (uri) {
	
	setInterval(function(){
		$.ajax({
			url: uri,	//URL of PHP file to call
			success: function(data) {	//If PHP File successfully Returns. 'Data' is what is Returned.
				
				var tmpDiff = data - FirstLoadCount;	//See if new data is greater than old
				var tmpText = "";
				
				if(tmpDiff > 0){
					tmpText = tmpDiff.toString();
					
					tmpText = "["+ tmpText + "] <?php print $PageTitle; ?>";
					
					var findlink = document.getElementById("pageFavicon");
					findlink.href = "./assets/img/faviconGIF.gif";
				}
				else{
					tmpText = "<?php print $PageTitle; ?>";
					var findlink = document.getElementById("pageFavicon");
					findlink.href = "./assets/img/favicon.ico";
				}
				
				document.title = tmpText;
				
			},
			cache: false,		//This is NEEDED. Since we always call the same URL, some browsers (IE), will just use the previous values (Cached Values) if the URL is the same. This prevents that, and makes it always call the PHP function every loop.
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError);
			}
		});
	}, 30000); //Time Set Here.
};





InfiniteAjaxRequest ("assets/includes/titleTab.php"); //Inital Ajax Call.


</script>

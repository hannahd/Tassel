<?php
require_once ("constants/constants.php");
require_once (ROOT."/constants/dbconnect.php"); //Includes database connection
require_once (ROOT."/constants/functions.php"); //Includes functions
require_once (ROOT."/constants/access-functions.php"); //Includes functions to control user privileges

if(isset($_SESSION['user_id'])){ secure_page(); }
?>
<!DOCTYPE html>
<head>
	<?php echo get_head_meta("Directory"); ?>
	
	<script type="text/javascript">
		$(function()
		{
			// Display directory table
			$('#directory-profiles').load("<?php echo BASE; ?>/constants/process.php?action=get");
			
			var auto_refresh = setInterval(function(){
					$('#directory-profiles').load("<?php echo BASE; ?>/constants/process.php?action=get");
			}, 500000); //Refresh every 10 minutes 
			
		});
	</script>
</head>
<body>
	<?php include ROOT.'/constants/navbar.php'; ?>
	<div class="container">
		<h2>Directory</h2>
		<noscript><p class="error alert"Javascript must be enabled to view this directory.</p></noscript>
		<span id="message"

></span>
		
		<!-- Directory Entries-->
		<div id="directory-profiles"></div>
	</div>	
</body>
</html>
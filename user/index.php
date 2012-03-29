<?php
/*Secured user only page*/
require_once ("../constants/constants.php");
require_once (ROOT."/constants/functions.php"); //Includes functions
require_once (ROOT."/constants/access-functions.php"); //Includes functions to control user privileges
require_once (ROOT."/constants/dbconnect.php"); //Includes database connection
require_once (ROOT."/constants/validation-functions.php"); //Includes functions for form validation

secure_page();

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<head>
	<?php echo get_head_meta("Welcome"); ?>
</head>
<body>
	
	<?php include ROOT.'/constants/navbar.php'; ?>
	<div class="container">
		<h1>Welcome, <?php echo $_SESSION['fullname']; ?>!</h1>
		
		<?php
		$qry = mysql_query("SELECT num_logins, last_login FROM ".TBL_PROFILE." WHERE id = '".$_SESSION['user_id']."' LIMIT 1") or die(mysql_error());
		list($num_logins, $last_login) = mysql_fetch_row($qry);
		?>
		
		<p>You last logged in <?php echo contextualTime($last_login); ?></p>
		
		<p>You have logged in <?php echo $num_logins; ?> times.</p>
		
		<p>Noticed something out of date on your profile? Change it <a href="<?php echo BASE. "/user/edit-profile.php?p=". $_SESSION['encrypted_id']; ?>">here</a></p>
		
	</div>
	
</body>
</html>
<?php
/**
 * Login to Administrator for Tassel.
 *
 * This page contains the login page for the administrator tools.
 * 
 * Error codes ending in li. 
 * 
 * TODO: Open access, so users can edit their profiles
 * 
 * @author Hannah Deering
 * @package Tassel
 **/
require_once ("constants/constants.php");
require_once ("constants/functions.php"); //Includes functions
require_once ("constants/access_functions.php"); //Includes functions to control user privileges
require_once ("constants/dbconnect.php"); //Includes database connection
require_once ("constants/validation_functions.php"); //Includes functions for form validation

//Pre-assign our variables to avoid undefined indexes
$username = NULL;
$pass2 = NULL;
$msg = NULL;
$errors = array();

if(isset($_POST['login'])) {
	//Assigning vars and sanitizing user input
	$email = sanitize($_POST['email']);
	$pass2 = sanitize($_POST['pass']);

	if(empty($email) || strlen($email) < 3) {
		$errors[] = "Please enter your username.";
	}
	if(empty($pass2) || strlen($pass2) < 8) {
		$errors[] = "Please enter your password.";
	}
	
	// Find the password of this user in the database.
	$qry = mysql_query("SELECT password, id, md5_id, user_level FROM ".TBL_PROFILE." WHERE en_email = AES_ENCRYPT('$email', '". SALT ."')") or die(error_message("Could not access database", mysql_error(), "1li"));

	// Get materials if email matched. 
	list($pass, $id, $md5_id, $user_level) = mysql_fetch_row($qry);
	
	// Check for errors
	if(empty($errors)) {
		
		if(mysql_num_rows($qry) > 0) {
			// If someone was found, check that the passwords match and that the user is an admin
			if(hash_pass($pass2) === $pass && $user_level >= 5) {
				
				$user_details = mysql_query("SELECT first_name, last_name FROM ".TBL_DETAILS." WHERE profile_id = '$id' LIMIT 1") or die(error_message("Could not access database", mysql_error(), "2li"));
				list($first_name, $last_name) = mysql_fetch_row($user_details);
				
				session_start();
				// REALLY start new session (wiping all prior data).
	   			session_regenerate_id(true);
		
				$session_time = time();
				$session_key = generate_key();
		
				// Update user's session data in the database
				mysql_query("UPDATE ".TBL_PROFILE." SET `session_time`='$session_time', `session_key` = '$session_key', `num_logins` = num_logins+1, `last_login` = now() WHERE id='$id'") or die(error_message("Could not access database", mysql_error(), "3li"));

				//Assign session variables to information specific to user
				$_SESSION['user_id'] = $id;
				$_SESSION['encrypted_id'] = $md5_id;
				$_SESSION['fullname'] = $first_name." ".$last_name;
				$_SESSION['user_level'] = $user_level;
				$_SESSION['stamp'] = $session_time;
				$_SESSION['key'] = $session_key;
				$_SESSION['logged'] = true;
				//And some added encryption for session security
				$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);

				//Build a message for display where we want it
				$msg = "Welcome, " . $first_name." ".$last_name . "! Logging in...";

				header("Location: ".BASE."/admin/manage_profiles.php");
			} else {
				//Passwords don't match, issue an error
				$errors[] = "The email or password you entered is incorrect.";
			}
		
		} 
		else {
			//If there were no matches to the username, export an error.
			$errors[] = 'Sorry, you don\'t have permission to access the admin functions. <a href="'. BASE .'">Return to directory?</a>';
		}
	} 
}
?>
<!DOCTYPE html>
<head>
	<?php echo get_head_meta("Login"); ?>
	
	<script>
	$(document).ready(function(){
		$("#login_form").validate();
	});
	</script>
</head>
<body>

	<?php include "constants/navbar.php"; ?>
	
	<div class="container">
		<div class="row">
			<div class="span7 offset4">
				<?php
				//Show message if isset
				if(isset($msg) || !empty($_GET['msg']))
				{
					if(!empty($_GET['msg']))
					{
						$msg = $_GET['msg'];
					}
					echo '<div class="success alert">'.$msg.'</div>';
				}
				//Show errorsor message if isset
				if(!empty($errors))
				{
					echo '<div class="error alert">';
					foreach($errors as $e)
					{
						echo $e.'<br />';
					}
					echo '</div>';
				}
				?>

				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="login_form">
					<fieldset>
						<h3>Admin Log In</h3>
						<label for="email">Email</label>
						<input type="text" name="email" id="email" value="<?php echo stripslashes($username); ?>" class="required span4" /></td>
						
						<label for="pass">Password</label>
						<input type="password" name="pass" id="pass" value="<?php echo stripslashes($pass2); ?>" class="required span4" /></td>
						
						<input type="submit" name="login" value="Log In" class="span4 btn btn-primary btn-large"/>
					</fieldset>
				</form>
			</div>
		</div>

	</div>
</body>
</html>
<?php
require_once ("constants/constants.php");
require_once (ROOT."/constants/functions.php"); //Includes functions
require_once (ROOT."/constants/access-functions.php"); //Includes functions to control user privileges
require_once (ROOT."/constants/dbconnect.php"); //Includes database connection
require_once (ROOT."/constants/validation-functions.php"); //Includes functions for form validation

//Pre-assign our variables to avoid undefined indexes
$username = NULL;
$pass2 = NULL;
$msg = NULL;
$errors = array();

if(isset($_POST['login']))
{

	//Assigning vars and sanitizing user input
	$username = sanitize($_POST['user']);
	$pass2 = sanitize($_POST['pass']);

	if(empty($username) || strlen($username) < 3)
	{
		$errors[] = "Please enter your username.";
	}
	if(empty($pass2) || strlen($pass2) < 8)
	{
		$errors[] = "Please enter your password.";
	}
	
	// Find password of this user in the database
	$qry = mysql_query("SELECT password, id, enabled FROM ".TBL_PROFILE." WHERE username = '$username'") or die(error_message("Could not access database", mysql_error(),21));

	// Get materials if user matched. 
	list($pass, $id, $approved) = mysql_fetch_row($qry);
	
	if(empty($errors))
	{
		
		if(mysql_num_rows($qry) > 0)
		{
			// Check if the profile has been approved by the admin. 
			if($approved == 0)
			{
				$errors[] = 'Your profile is not enabled.  If you think there is an error, please <a href="'. BASE . '/contact.php">contact us</a>.';
			} else {
				// If someone was found, check that the passwords match
				if(hash_pass($pass2) === $pass)
				{
					$user_info = mysql_query("SELECT id, md5_id, first_name, last_name, username, user_level FROM ".TBL_PROFILE." WHERE id = '$id' LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
					list($id, $md5_id, $first_name, $last_name, $username, $user_level) = mysql_fetch_row($user_info);

					session_start();
					// REALLY start new session (wiping all prior data).
		   			session_regenerate_id(true);
				
					$session_time = time();
					$session_key = generate_key();
				
					// Update user's session data in the database
					mysql_query("UPDATE ".TBL_PROFILE." SET `session_time`='$session_time', `session_key` = '$session_key', `num_logins` = num_logins+1, `last_login` = now() WHERE id='$id'") or die(mysql_errorsor());

					//Assign session variables to information specific to user
					$_SESSION['user_id'] = $id;
					$_SESSION['encrypted_id'] = $md5_id;
					$_SESSION['fullname'] = $first_name." ".$last_name;
					$_SESSION['user_name'] = $username;
					$_SESSION['user_level'] = $user_level;
					$_SESSION['stamp'] = $session_time;
					$_SESSION['key'] = $session_key;
					$_SESSION['logged'] = true;
					//And some added encryption for session security
					$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);

					//Build a message for display where we want it
					$msg = "Welcome, " . $first_name." ".$last_name . "! Logging in...";


					header("Location: ".BASE."/user");
				}
				else
				{
					//Passwords don't match, issue an error
					$errors[] = "The username or password you entered is incorrect.";
				}
			}
		} 
		else
		{
			//If there were no matches to the username, export an error.
			$errors[] = 'This username does not exist. Please head <a href="'. BASE .'/register.php">here</a> to register for a profile.';
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

	<?php include ROOT."/constants/navbar.php"; ?>
	
	<div class="container">
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
				<h3>Log In</h3>
				<div class="row">
					<div class="span8">
						<label for="username">Username</label>
						<input type="text" name="user" value="<?php echo stripslashes($username); ?>" class="required span4" /></td>
					</div>
				</div>
				<div class="row">
					<div class="span8">
						<label for="pass">Password</label>
						<input type="password" name="pass" value="<?php echo stripslashes($pass2); ?>" class="required span4" /></td>
					</div>
				</div>
				<div class="row">
					<div class="span8">
						<input type="submit" name="login" value="Log In" class="span4 btn btn-primary btn-large"/>
					</div>
				</div>
			</fieldset>
		</form>

	</div>
</body>
</html>
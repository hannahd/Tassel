<?php
/* Navigation Bar that goes accross the top of each page*/
?>
<div class="header">
	<div class="navbar navbar-fixed-top">
	<!--<div id="logo">
		<img src="<?php echo BASE; ?>/images/logo.png">
		<a href="">xyz college</a>
	</div>-->
		<div class="navbar-inner">
	    	<div class="container">
				<a class="brand" href="<?php echo BASE; ?>">XYZ University</a>
				<ul class="nav">
					<li <?php if(strpos($_SERVER['SCRIPT_NAME'], '/index.php')!==FALSE && strpos($_SERVER['SCRIPT_NAME'], 'user')===FALSE && basename($_SERVER['SCRIPT_NAME']) == 'index.php') { ?>class="active"<?php } ?>><a href="<?php echo BASE; ?>">Home</a></li>
					<li <?php if(strpos($_SERVER['SCRIPT_NAME'], '/directory.php')!==FALSE && basename($_SERVER['SCRIPT_NAME']) == 'directory.php') { ?>class="active"<?php } ?>><a href="<?php echo BASE; ?>/directory.php">Directory</a></li>
					<li <?php if(strpos($_SERVER['SCRIPT_NAME'], '/contact.php')!==FALSE && basename($_SERVER['SCRIPT_NAME']) == 'contact.php') { ?>class="active"<?php } ?>><a href="<?php echo BASE; ?>/contact.php">Contact Us</a></li>
				</ul>
				<ul class="nav pull-right">
					<?php if(!isset($_SESSION['user_id']))
					{
					?>
					<li <?php if(basename($_SERVER['SCRIPT_NAME']) == 'register.php') { ?>class="active"<?php } ?>><a href="<?php echo BASE; ?>/register.php">Register</a></li>
					<li <?php if(basename($_SERVER['SCRIPT_NAME']) == 'login.php') { ?>class="active"<?php } ?>><a href="<?php echo BASE; ?>/login.php">Login</a></li>
					<?php
					}
					else
					{
					?>
					<li <?php if(strpos($_SERVER['SCRIPT_NAME'], 'user/index.php')!==FALSE) { ?>class="active"<?php } ?>><a href="<?php echo BASE; ?>/user/">Secure Home</a></li>
					<li <?php if(basename($_SERVER['SCRIPT_NAME']) == 'profile.php') { ?>class="active"<?php } ?>><a href="<?php echo BASE. "/user/edit-profile.php?p=". $_SESSION['encrypted_id']; ?>">Edit Profile</a></li>
						<?php
						if(is_admin())
						{
						?>
							<!-- Admin Page link will go here -->
							<!--<li <?php if(strpos($_SERVER['SCRIPT_NAME'], 'users/admin.php')) { ?>class="active"<?php } ?>><a href="<?php echo BASE; ?>/user/admin.php">Manage Users</a></li>-->
						<?php
						}
						?>
					<li><a href="<?php echo BASE; ?>/logout.php">Logout</a></li>
					<?php
					}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>

<?php
/* Navigation Bar that goes accross the top of each page*/
?>
<div class="header">
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
	    	<div class="container">
				<a class="brand" href="<?php echo BASE; ?>">Tassel Directory</a>
				<ul class="nav pull-right">
					<?php if(!isset($_SESSION['user_id']))
					{
					?>
					<li <?php if(basename($_SERVER['SCRIPT_NAME']) == 'login.php') { ?>class="active"<?php } ?>><a href="<?php echo BASE; ?>/login.php">Login</a></li>
					<?php
					}
					else
					{
					?>
					<li <?php if(strpos($_SERVER['SCRIPT_NAME'], 'user/index.php')!==FALSE) { ?>class="active"<?php } ?>><a href="<?php echo BASE. "/user/index.php?p=". $_SESSION['encrypted_id']; ?>">Edit Profile</a></li>
					<li <?php if(basename($_SERVER['SCRIPT_NAME']) == 'add-profile') { ?>class="active"<?php } ?>><a href="<?php echo BASE. "/user/add-profile.php"; ?>">Add Person</a></li>
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

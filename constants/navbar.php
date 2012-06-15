<?php
/**
 * Navigation Bar for Tassel.
 * 
 * This navigation bar goes across the top of every page.
 *
 * @author Hannah Deering
 * @package Tassel
 **/
require_once ("access_functions.php"); //Includes functions to control user privileges
?>
<div class="header">
	<div class="navbar">
		<div class="navbar-inner">
	    	<div class="container">
				<a class="brand" href="<?php echo BASE; ?>">Tassel Directory</a>
				<ul class="nav pull-right">
				   <?php if(is_admin()){ ?>
					<li <?php if(strpos($_SERVER['SCRIPT_NAME'], 'admin/manage_profiles.php')!==FALSE) { ?>class="active"<?php } ?>><a href="<?php echo BASE;?>/admin/manage_profiles.php">Manage Profiles</a></li>
					<li <?php if(strpos($_SERVER['SCRIPT_NAME'], 'admin/add_profile.php')!==FALSE) { ?>class="active"<?php } ?>><a href="<?php echo BASE. "/admin/add_profile.php"; ?>">Add Person</a></li>
					<li><a href="<?php echo BASE; ?>/logout.php">Logout</a></li>
				   <?php }	?>
				</ul>
			</div>
		</div>
	</div>
</div>

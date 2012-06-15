<?php
/**
 * Main Directory of Tassel.
 *
 * This page allows users to view and link to an individual
 * profile. 
 *
 * Query values:
 * 			- p: can be profile id or last name and first name 
 *				 separated by underscores
 *			- b: m, if set, changes the back button to direct to
 *				 manage profiles
 *
 * TODO: Add links & groups
 * 
 * @author Hannah Deering
 * @package Tassel
 **/
require_once ("constants/constants.php");
require_once ("constants/dbconnect.php"); //Includes database connection
require_once ("constants/controls.php"); //Includes functions

// Determine what values should be tied to the back button
$back_url = BASE;
$back_text = "Back to Search";

// Get the profile to show
if (isset($_GET['p'])) {
	if(is_numeric( $_GET['p'])){
		$id = $_GET['p'];
	} else{
		$name = explode("_", $_GET['p'], 2);
		list($last_name, $first_name) = $name;
		$first_name = str_replace("_", " ", $first_name);
	}
	
	if(isset($_GET['b']) && $_GET['b'] === 'm'){
		$back_url = 'http://localhost:8888/Tassel/admin/manage_profiles.php';
		$back_text = 'Back to Manage Profiles';
	}
} else {
	header("Location: ".$back_url);
}

?>
<!DOCTYPE html>
<head>
	<?php 
	if(isset($first_name) && isset($last_name)){
		echo get_head_meta("$first_name $last_name");
	}else {
		echo get_head_meta("Directory"); 
	}?>
	
	<script type="text/javascript">
		
	</script>
</head>
<body>
	<?php include 'constants/navbar.php'; ?>
	<noscript><div class="container"><div class="row"><div class="span4 offset4"><p class="error alert"Javascript must be enabled to view this directory.</p></div></div></div></noscript>
	<div class="container">
		<div class="row">
			<div class="span2"> &nbsp;</div>
			<div class="span9">
				<div class="well" id="view-option">
					<a href="<?php echo $back_url;?>" class="btn"><i class="icon-arrow-left"></i> <?php echo $back_text;?></a>
				</div>
				
				<!-- Directory Entry-->
				<div id="directory-profiles">
					<?php
						$_GET['action'] = 'get-one';
						if(isset($first_name) && isset($last_name)){
							$_GET['first_name'] = $first_name;
							$_GET['last_name'] = $last_name;
						} elseif(isset($id)){
							$_GET['id'] = $id;
						} 
						include("constants/get_profile.php");
					?>
				</div>
				
				<div id="footer"></div>
			</div>
		</div>
	</div>	
</body>
</html>
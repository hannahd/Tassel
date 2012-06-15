<?php
/**
 * Front End for for Managing Profiles in Tassel.
 *
 * This page lists the profiles in the Tassel database and
 * allows the administrator to add, delete, edit, or block 
 * these profiles.
 * 
 * Access limited to admins.
 * 
 * Error codes ending in mp. 
 * 
 * TODO: Sort profiles
 * TODO: Search profiles
 * TODO: Checkboxes to bulk delete
 * TODO: Bulk change permissions, or positions
 * TODO: Duplicate profile
 * 
 * @author Hannah Deering
 * @package Tassel
 **/

require_once ("../constants/constants.php");
require_once ("../constants/dbconnect.php"); //Includes database connection
require_once ("../constants/functions.php"); //Includes functions
require_once ("../constants/access_functions.php"); //Includes functions to control user privileges
require_once ("../constants/validation_functions.php"); //Includes functions to control user privileges

secure_page();
if(!is_admin()){
	header("Location: ".BASE."/index.php");
}
if(isset($_GET['added'])){
	$success = "Added profile!";
}
if(isset($_GET['updated'])){
	if(USER_TEST){
		$success = "Updated profile! <small>(In order to preserve data for other testers, the profile was not changed.)</small>";
	} else {
		$success = "Updated profile!";
	}
}

if(isset($_GET['action']) && ($_GET['action'] == "delete" || $_GET['action'] == "enable" || $_GET['action'] == "disable") && isset($_POST['id']) && isset($_POST['name']) ) {
	$id = sanitize($_POST['id']);
	$errors = validate_input($id, "id", true, "num");
	
	$name = sanitize($_POST['name']);

	if(count($errors) == 0  && !USER_TEST){
			$delete = mysql_query("DELETE FROM ".TBL_DETAILS." WHERE `profile_id` = '". $id ."' LIMIT 1") or die(error_message("Unable to delete $name", mysql_error(), "1mp"));		
			$delete = mysql_query("DELETE FROM ".TBL_PROFILE." WHERE `id` = '". $id ."' LIMIT 1") or die(error_message("Unable to delete $name", mysql_error(), "2mp"));
			
			if($delete) {
				$success = "Deleted $name!";
			} else {
				$errors[] = "Unable to delete $name!";
			}
	} elseif(USER_TEST){
		$success = "Deleted $name! <small>(In order to preserve data for other testers, $name was not deleted.)</small>";
	} else{
		$errors[] = "Unable to delete $name!";
	}
}
?>
<!DOCTYPE html>
<head>
	<?php echo get_head_meta("Manage Profiles"); ?>
	<script type="text/javascript">
		
		$(document).ready(function(){
			$('#confirm-delete').modal({
			  show: false
			});
			
			$('.delete').click(function (){
				// Extract id & name
				var id = $(this).attr('id').split('-');
				var name = $(this).parents('tr').attr('id').split('_');
				var last = name[0];
				var first = "";
				for (var i=1, n=name.length; i < n; i++) {
					first += name[i] + " ";
				}
				$('#delete-name').html("<b>" + first + last + "</b>");
				$('#form-delete-name').val(first + last);
				$('#form-delete-id').val(id[1]);
				$('#confirm-delete').modal('show');
			});
			
			$(".disable").click(disable_profile);
			$(".enable").click(enable_profile);
			
			function disable_profile(e){
				// Extract id & name
				var id = $(e.currentTarget).attr('id').split('-');
				var name = $(e.currentTarget).parents('tr').attr('id').split('_');
				var last = name[0];
				var first = "";
				for (var i=1, n=name.length; i < n; i++) {
					first += name[i] + " ";
				}
				
				// Build datastring to pass through query
				var datastring = "id=" + id[1] +   
								 "&name=" + first + last;
				
				$.ajax({
					type: "POST",
					url: "<?php echo BASE; ?>/constants/process.php?action=disable",
					data: datastring,
					success: function(response) {
						$('#msg').show().html(response);
						$(e.currentTarget).attr("src", "<?php echo BASE;?>/images/disabled.png");
						$(e.currentTarget).attr("id", "enable-"+id[1]);
						$(e.currentTarget).attr("alt", "Enable Profile");
						$(e.currentTarget).removeClass("disable");
						$(e.currentTarget).addClass("enable");
						$(e.currentTarget).unbind("click");
						$(e.currentTarget).click(enable_profile);
					}
				});
			}
			
			function enable_profile(e){
				// Extract id & name
				var id = $(e.currentTarget).attr('id').split('-');
				var name = $(e.currentTarget).parents('tr').attr('id').split('_');
				var last = name[0];
				var first = "";
				for (var i=1, n=name.length; i < n; i++) {
					first += name[i] + " ";
				}
				
				// Build datastring to pass through query
				var datastring = "id=" + id[1] +   
								 "&name=" + first + last;
				
				$.ajax({
					type: "POST",
					url: "<?php echo BASE; ?>/constants/process.php?action=enable",
					data: datastring,
					success: function(response) {
						$('#msg').show().html(response);
						$(e.currentTarget).attr("src", "<?php echo BASE;?>/images/enabled.png");
						$(e.currentTarget).attr("id", "disable-"+id[1]);
						$(e.currentTarget).attr("alt", "Disable Profile");
						$(e.currentTarget).removeClass("enable");
						$(e.currentTarget).addClass("disable");
						$(e.currentTarget).unbind("click");
						$(e.currentTarget).click(disable_profile);
					}
				});
			}
			
			
			$('.close-modal').click(function (){
				$('#confirm-delete').modal('hide');
			});
			
			$('.profile-row').click(function (){
				window.location = "<?php echo BASE;?>/profile.php?p=" + $(this).attr('id') + "&b=m";
			}).find('a, img').hover(function (){ 
			        $(this).parents('tr').unbind('click'); 
			  }, function() { 
				$(this).parents('tr').click( function() { 
					window.location = "<?php echo BASE;?>/profile.php?p=" + $(this).attr('id');
				})
			  });
			
			$(".delayed-fade").delay(3000).fadeOut(400);
		 });
	</script>
</head>
<body>
	<div class="modal hide fade" id="confirm-delete">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?action=delete" method="post">  
			<div class="modal-header">
			    <a class="close close-modal" data-dismiss="modal">×</a>
			    <h3>Confirm Delete</h3>
			  </div>
			  <div class="modal-body">
			    <p>Are you sure you want to permanently delete <span id="delete-name"></span>? This cannot be undone.</p>
			  	<input type="hidden" value="" name="name" id="form-delete-name" />
				<input type="hidden" value="" name="id" id="form-delete-id" />
			  </div>
			  <div class="modal-footer">
			    <a href="#" data-dismiss="modal" class="btn close-modal">No, go back.</a>
			    <input type="submit" class="btn btn-danger close-modal" value="Yes, delete this profile."/>
			  </div>
		</form>
	</div>
	
	
	<?php include '../constants/navbar.php'; ?>
	<div class="container">	
		<div class="row">
			<div class="span12">
				<h2>Manage Profiles</h2>
				<noscript><p class="error alert"Javascript must be enabled to use this form.</p></noscript>
				<span id="msg">
					<?php
					if(!empty($success)) {
						echo '<div class="success alert delayed-fade">';
						echo $success;
						echo '</div>';
					}
					if(!empty($errors)) {
						echo '<ul class="error alert"><span>Please correct the following:</span>';
						foreach($errors as $e) {
							echo "<li>".$e ."</li>";
						}
						echo '</ul>';
					}
					?>
				</span>
				<a href="http://localhost:8888/Tassel/admin/add_profile.php" class="btn btn-success pull-right" id="add"><i class="icon-plus icon-white"></i> New Profile</a>
			</div>
		</div>
		<div class="row">
			<div class="span12">
				<div id="profile-table">
					<table class="table table-striped table-condensed">
					<thead>
						<tr>
							<th>Name</th>
							<th>&nbsp;</th>
							<th>Position</th>
							<th>Department</th>
							<th>Grad Date</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php
					// Get all profiles
					$detail_qry = mysql_query("SELECT *, AES_DECRYPT(en_email, '".SALT."') AS email FROM ".TBL_DETAILS."") or die(error_message("Unable to find profiles", mysql_error(), "3mp"));
					
					// Get all programs
					$program_qry = mysql_query("SELECT `id`, `name`, `abbreviation`, `online` FROM ". TBL_PROGRAM) or die(error_message("Could not access programs", mysql_error(),"4mp"));
					$programs = qry_to_array($program_qry);
					
					// Get all departments
					$department_qry = mysql_query("SELECT `id`, `name` FROM ". TBL_DEPARTMENT) or die(error_message("Could not access departments", mysql_error(),"5mp"));
					$departments = qry_to_array($department_qry);
					
					// Print row for each profile
					while($profile = mysql_fetch_assoc($detail_qry)) {
						$profile_qry = mysql_query("SELECT `enabled`, `user_level`, `md5_id` FROM ".TBL_PROFILE." WHERE id=". $profile['profile_id'] ." LIMIT 1" ) or die(error_message("Unable to retrieve profile", mysql_error(), "6mp"));
						$profile = array_merge($profile, mysql_fetch_assoc($profile_qry));
					?>
					<tr class="profile-row" id="<?php echo $profile['last_name']."_".str_replace(" ", "_", $profile['first_name']);?>">
						<td>
							<?php 
							// Show name
							echo "<a href=\"".BASE."/profile.php?p=".$profile['last_name']."_".str_replace(" ", "_", $profile['first_name'])."&b=m\">".$profile['first_name']. " " .$profile['last_name']."</a>"; 
							?>
						</td>
						<td>
							<?php 
							// Show icon for pending admission status 
							if(!empty($profile['admission_status']) && $profile['admission_status']){
								echo "<img src=\"".BASE."/images/pending.png\" alt=\"Admission Pending\" class=\"blank\"/>";
							}
							// Show icon for admins
							if($profile['user_level'] >= 5){
								echo "<img src=\"".BASE."/images/admin3.png\" alt=\"Admin\" class=\"blank\"/>";
							}
							?>
						</td>
						<td>
							<?php 
								// Show position and degree (for students and alumni)
								echo ucwords($profile['position']); 
								if($profile['position'] === "student" || $profile['position'] === "alumni"){
									echo " (";
									if(!empty($profile['program_id'])){
										if($programs[$profile['program_id']]['online']) {
											echo "Online ";
										}
										if(!empty($programs[$profile['program_id']]['abbreviation'])) {
											echo $programs[$profile['program_id']]['abbreviation'];
										} else{
											echo $programs[$profile['program_id']]['name'];
										}
									}
									echo ")";
								}
							?>
						</td>
						<td>
							<?php 
								// Show department
								if(!empty($profile['department_id'])){
									echo $departments[$profile['department_id']]['name'];
								}
							?>
						</td>
						<td>
							<?php 
								// Show grad date (for students and alumni)
								if($profile['position'] === "student" || $profile['position'] === "alumni"){
									echo "<em>";
									if(!empty($profile['grad_date'])){
										$grad_date = explode("-", $profile['grad_date']);
										echo month_to_season($grad_date[1], true) ." ". $grad_date[0];
									}
									echo "</em>";
								}
							?>
						</td>
						
						<td class="actions">
							<a href="mailto:<?php echo $profile['email'];?>" target="_blank"><img src="<?php echo BASE;?>/images/email.png" class="blank" alt="Email <?php echo $profile['first_name'];?>"/></a>
							<?php
								  // Show icon for disabled profiles
								  if(!$profile['enabled']){?>
									<img class="enable blank" id="enable-<?php echo $profile['profile_id'];?>" src="<?php echo BASE;?>/images/disabled.png" alt="Enable Profile"/>
							<?php } else { ?>
									<img class="disable blank" id="disable-<?php echo $profile['profile_id'];?>" src="<?php echo BASE;?>/images/enabled.png" alt="Disable Profile"/>
							<?php } ?>
							<a href="<?php echo BASE."/admin/edit_profile.php?p=".$profile['md5_id']; ?>"><img src="<?php echo BASE;?>/images/edit.png" class="blank" alt="Edit Profile"/></a>
							<a href="#" class="delete" id="delete-<?php echo $profile['profile_id'];?>"><img src="<?php echo BASE;?>/images/delete.png" class="blank" alt="Delete Profile"/></a>
						</td>
					</tr>
					<?php 
					}
					?>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
		
</body>
</html>
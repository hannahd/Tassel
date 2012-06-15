<?php
/**
 * Front End for for Editing Profiles to Tassel.
 *
 * This page contains the form for editing profiles in the
 * directory.
 * 
 * Required query values:
 * 			- p: md5 hash of the profile's id
 * 
 * Access limited to admins.
 * 
 * Error codes ending in ep. 
 * 
 * TODO: Open access, so users can edit their profiles
 * TODO: Make title autofill
 * TODO: Add groups, links & related people
 * 
 * @author Hannah Deering
 * @package Tassel
 **/

require_once ("../constants/constants.php");
require_once ("../constants/dbconnect.php"); //Includes database connection
require_once ("../constants/functions.php"); //Includes functions
require_once ("../constants/controls.php"); //Includes functions
require_once ("../constants/access_functions.php"); //Includes functions to control user privileges

secure_page();

if(!is_admin()){
	header("Location: ".BASE."/index.php");
}

if(isset($_GET['p'])) {
	$md5_id = sanitize($_GET['p']);
	
	// Get id from database
	$profile_qry = mysql_query("SELECT `id` FROM `". TBL_PROFILE ."` WHERE `md5_id` = '$md5_id' LIMIT 1") or die(error_message("Could not access profile", mysql_error(), "1ep"));
	$profile_array = mysql_fetch_row($profile_qry);
	$profile_id = $profile_array[0];
	
	// Get details from database
	$details_qry = mysql_query("SELECT *, AES_DECRYPT(en_email, '". SALT ."') AS email FROM `". TBL_DETAILS ."` WHERE `profile_id` = '$profile_id'") or die(error_message("Could not access profile details", mysql_error(),"2ep"));
	list(, $first_name, $last_name, $photo, $position, $program, $department, $comajor_program, $comajor_department, , $phone, $office_location, $title, $company, $city, $state, $country, $dissertation, $education, $bio, $start_date_str, $grad_date_str, ,$admission_status, $email) = mysql_fetch_row($details_qry);
	
	// Set dates
	$start_date = array();
	$grad_date  = array();
	
	if(!empty($start_date_str)){ $start_date = explode("-", $start_date_str); }
	if(!empty($grad_date_str)) { $grad_date  = explode("-", $grad_date_str);  }
	
	// Get college id from database
	if(!empty($department)){
		$college = mysql_query("SELECT `college_id` FROM `". TBL_DEPARTMENT ."` WHERE `id` = '$department' LIMIT 1") or die(error_message("Could not access departments", mysql_error(),"3ep"));
		$college_array = mysql_fetch_row($college);
		$college = $college_array[0];
	}

	// Get comajorcollege id from database
	if(!empty($comajor_department)){
		$college = mysql_query("SELECT `college_id` FROM `". TBL_DEPARTMENT ."` WHERE `id` = '$comajor_department' LIMIT 1") or die(error_message("Could not access departments", mysql_error(),"4ep"));
		$college_array = mysql_fetch_row($college);
		$comajor_college = $college_array[0];
	}
	
	// See if program is online
	$online = false;
	if(!empty($comajor_department_id)){
		$program = mysql_query("SELECT `online` FROM `". TBL_PROGRAM ."` WHERE id = '$program_id' LIMIT 1") or die(error_message("Could not access programs", mysql_error(),"5ep"));
		$program_array = mysql_fetch_row($college);
		$online = $college_array[0];
		$online = (!empty($college_array[0]) && $college_array[0] == 1) ? true : false ;
	}
	
	// Get interests id from database
	$interest_qry = mysql_query("SELECT `interest_id` FROM `". TBL_PROFILE_INTEREST_MAP ."` WHERE `profile_id` = '$profile_id'") or die(error_message("Could not access interests", mysql_error(),"6ep"));
	$interests = array();
	while($row = mysql_fetch_row($interest_qry)){
		$interests[] = $row[0];
	}
	
	// Strip slashes from input fields for values that are set
	if(isset($first_name))		{ $first_name 		= stripslashes($first_name); }
	if(isset($last_name))		{ $last_name 		= stripslashes($last_name); }
	if(isset($photo))			{ $photo 			= trim(stripslashes($photo)); }
	if(isset($phone))			{ $phone 			= stripslashes($phone); }
	if(isset($office_location))	{ $office_location 	= stripslashes(html_entity_decode($office_location)); } 
	if(isset($title))			{ $title 			= stripslashes(html_entity_decode($title)); }  
	if(isset($company))			{ $company 			= stripslashes(html_entity_decode($company)); }
	if(isset($city))			{ $city 			= stripslashes($city); }
	if(isset($dissertation))	{ $dissertation		= stripslashes(html_entity_decode($dissertation)); }
	
	// Add slashes for the text area input fields
	if(isset($bio)){ 
		$bio = html_entity_decode($bio);
		$bio = addslashes($bio); 
		$bio = str_replace("\n", "\\n", $bio);
		$bio = str_replace("\r", "\\r", $bio);
	}
	if(isset($education)){ 
		$education = html_entity_decode($education);
		$education = addslashes($education); 
		$education = str_replace("\n", "\\n", $education);
		$education = str_replace("\r", "\\r", $education);
	}
} else{
	// No profile was set
	header("Location: ".BASE."/admin/manage_profiles.php");
}

?>
<!DOCTYPE html>
<head>
	<?php echo get_head_meta("Edit $first_name $last_name"); ?>
	<script type="text/javascript">
		$(document).ready(function(){
			// Set focus orginally on the name
			$("#name").focus();
			
			//Reset form to saved values
			function reset_form_to_saved() {
				$('#id').val('<?php echo $profile_id;?>');
				$('#name').val('<?php echo $first_name." ".$last_name;?>');
				$('#email').val('<?php echo $email;?>');
				$('#phone').val('<?php echo $phone;?>');
				$('#office-location').val('<?php echo $office_location;?>');
				$('#title').val('<?php echo $title;?>');
				$('#company').val('<?php echo $company;?>');
				$('#city').val('<?php echo $city;?>');
				$('#start-y').val('<?php echo $start_date[0];?>');
				$('#start-m').val('<?php echo $start_date[1];?>');
				$('#bio').val('<?php echo $bio;?>');
				$('#education').val('<?php echo $education;?>');
				$('#position').val('<?php echo $position;?>');
				$('#dissertation').val('<?php echo $dissertation;?>');
				
				<?php if(!empty($photo)){?>
						$('#photo').val('<?php echo $photo;?>');
						$('#preview-image').attr("src", "<?php echo $photo;?>");
						$('#photo-preview').show();
				<?php } else{ ?>
						$('#photo-preview').hide();
				<?php } 
					  if(!empty($program)){?>
						$('#program').val('<?php echo $program;?>');
				<?php } 
					  if(!empty($college)){?>
						$('#college').val('<?php echo $college;?>');
						update_departments(<?php echo $college;?>, 'false', true);
				<?php } 		
					  if(!empty($comajor_program)){?>
						$('#comajor').val(["yes"]);
						$("#comajor-fields").show();
						$('#comajor-program').val('<?php echo $comajor_program;?>');
				<?php } 
					  if(!empty($comajor_college)){?>
						$('#comajor').val(["yes"]);
						$("#comajor-fields").show();
						$('#comajor-college').val('<?php echo $comajor_college;?>');
						update_departments(<?php echo $comajor_college;?>, 'true', true);
				<?php } 
				 	  if(!empty($comajor_department)){?>
						$('#comajor').val(["yes"]);
						$("#comajor-fields").show();
						$('#comajor-program').val('<?php echo $comajor_program;?>');
				<?php } 
					  if(!empty($state)){?>
						$('#state').val('<?php echo $state;?>');
				<?php } 
					  if(!empty($country)){?>
						$('#country').val('<?php echo $country;?>');
				<?php } 
					  if(!empty($grad_date)){?>
						$('#grad-y').val('<?php echo $grad_date[0];?>');
						$('#grad-m').val('<?php echo $grad_date[1];?>');
				<?php } 
					  if(!empty($admission_status) && $admission_status > 0){?>
						$('#admission-status').val(["yes"]);
				<?php } ?>
				
				// Show correct details
				$(".alumni, .faculty, .visitor, .staff, .student").not($(".<?php echo $position;?>")).hide();
				$(".<?php echo $position;?>").show();
				<?php if(!$online){ ?>
						 $(".online").hide();
				<?php } ?>
				
				// Set checked interests
				$("#interests input").val([
					<?php
						$interest_str = "";
						foreach ($interests as $interest_id) {
							if($interest_str !== ""){
								$interest_str .= ", ";
							}
							$interest_str .= '"'. $interest_id . '"';
						}
						echo $interest_str;
					?>
				]);
			}
			
			// Clear interests search
			$("#clear-search").click(function(event) {
				$("#interests .checkbox").show();
				$("#search-interests").val("");
				event.preventDefault();
			});
			
			// Hide values that do not contain search term
			$("#search-interests").keyup(function() {
				if($('#search-interests').val() != "") {
				     $("#interests .checkbox").each(function(index, item) {
				         if($(this).text().indexOf($('#search-interests').val()) != -1){
				            $(this).show();                     
				         } else{
				            $(this).hide();                 
				         }
				       });
				} else{  
			    	$("#interests .checkbox").show();
			   	}
			});
			
			// Submit data via ajax
			$("#submit").click(function() {
				// Build datastring to pass through query
				var datastring = $('#form-contact').serialize() + 
								 "&" + $('#form-position').serialize() +
								 "&" + $('#form-personal').serialize();
				datastring += '&enabled=' + <?php if(is_admin()){ echo 1; } else { echo 0; } ?>;
								
				$.ajax({
					type: "POST",
					url: "<?php echo BASE; ?>/constants/set_profile.php?action=update",
					data: datastring,
					success: function(response) {
						// Check if entry was successful
						if(response){
							$('#message').show().html(response);
						} else {
							window.location.href = "<?php echo BASE;?>/admin/manage_profiles.php?updated=t";
						}
					}
				});
				
				return false;
			});
			
			// Show more options based on the person's position
			$("#position").change(function() {
				var fields = $(".alumni, .faculty, .student, .visitor, .staff");
				switch ($("#position option:selected").val()) {
					case "student":
						fields.not($(".student")).hide();
						$(".student").show();
						$(".online").hide();
						break;
					case "alumni":
						fields.not($(".alumni")).hide();
						$(".alumni").show();
						break;
					case "faculty":
						fields.not($(".faculty")).hide();
						$(".faculty").show();
						break;
					case "staff":
						fields.not($(".staff")).hide();
						$(".staff").show();
						break;
					case "visitor":
						fields.not($(".visitor")).hide();
						$(".visitor").show();
						break;
					case "":
						fields.not($(".staff")).hide();
						$(".staff").show();
						break;
				}
				// Remove content from hidden fields
				$('#form-position input[type="checkbox"]').filter(":hidden").prop("checked", false);
				$('#form-position input[type="text"]').filter(":hidden").val('');
				$('#form-position input[type="password"]').filter(":hidden").val('');
				$('#form-position textarea').filter(":hidden").val('');
				$('#form-position select').filter(":hidden").val("");
			});
			
			$("#program").change(function() {
				if($("#position option:selected").val() == "student" 
					&& $("#program option:selected").text().indexOf('Online') != -1){
					$(".online").show();
				} else if($("#position option:selected").val() == "student") {
					$(".online").hide();
				}
			});
			
			// Update department based on college selection
			$("#college").change(function(e) {
				update_departments($(this).val(), "false", false);
			});
			
			// Update comajor department based on college selection
			$("#comajor-college").change(function(e) {
				update_departments($(this).val(), "true", false);
			});
			
			// Update department dropdown based on college selection
			function update_departments(college, comajor, first){
				var datastring = "college_id=" + college;
				$.ajax({
				type: "POST",
				url: "<?php echo BASE; ?>/constants/process.php?action=update_departments&comajor="+comajor,
				data: datastring,
				success: function(response) {
					// Fill department menu with appropriate departments
					if(comajor == "true"){
						$('#comajor-department-menu').html(response);
					} else {
						$('#department-menu').html(response);
					}
					
					// Check if this is the first time this dropdown has been generated
					if(first){
						if(comajor == "true"){
							<?php if(!empty($comajor_department)){?>
									$('#comajor-department').val('<?php echo $comajor_department;?>');
							<?php } ?>
						} else {
							<?php if(!empty($department)){?>
									$('#department').val('<?php echo $department;?>');
							<?php } ?>
						}
					}
				}
				});
			}
			
			
			// Show and hide the comajor college and department menus
			$("#comajor").change(function () {
				if($("#comajor").attr('checked')){
			        $("#comajor-fields").slideDown();
				} else {
				    $("#comajor-fields").slideUp();
				}
			});
		
			// Show preview of profile photo
			$("#photo").focusout(function () {
				if($(this).val() != ""){
					$('#preview-image').attr("src", $(this).val());
					$('#photo-preview').show();
				} else{
					$('#photo-preview').hide();
				}
			});
			
			// Clear changed values, rever to saved
			$("#clear").click(function () {
				reset_form_to_saved();
			});
			
			// Start form out set to saved values
			reset_form_to_saved();
		});
	</script>
</head>
<body>
	<?php include '../constants/navbar.php'; ?>
	<div class="container">	
		<div class="row">
			<div class="span12">
				<h2>Edit Profile</h2>
			</div>
		</div>
		<div class="row">
			<div class="span12">
				<span id="message"></span>
			</div>
		</div>
		<div class="row new-profile">
			<!--HEADERS-->
			<div class="span4">
				<h4>1: Contact Details</h4>
			</div>
			<div class="span4">
				<h4>2: Position Details</h4>
			</div>
			<div class="span4">
				<h4>3: Personal Details</h4>
			</div>
		</div>
		<div class="row">
			<div class="span4 well">
				<form id="form-contact">
					<input type="hidden" name="id" id="id" value=""/>
					
					<label for="name">Name</label>  
					<input class="span4 required" type="text" maxlength="220" name="name" id="name" value=""/>
					
					<label for="span4 email">Email</label>
					<input class="span4 required email active-entry" type="text" maxlength="256" name="email" id="email"/>
					
					<label for="phone">Phone</label> 
					<input type="text" class="span4" maxlength="220" name="phone" id="phone" value=""/>
		
					<label for="office-location">Office</label>  
					<input type="text" class="span4" maxlength="220" name="office_location" id="office-location" value=""/>
					
					<label for="photo">Photo URL</label>
					<input class="span4 url active-entry" type="text" maxlength="220" name="photo" id="photo" placeholder="http://..."/>
					<div class="profile hide" id="photo-preview">
						<h5>Preview</h5>
						<img id="preview-image" src="http://www.jlmc.iastate.edu/sites/default/files/imagecache/Profile-Image/images/faculty-staff/Abbott_1.jpg">
					</div>
				</form>
			</div>
			<div class="span4 well">
				<form id="form-position">
					<label for="position">Position</label>
					<select name="position" id="position" class="span4">
						<option value="alumni">Alumni</option>
						<option value="faculty">Faculty</option>
						<option value="staff">Staff</option>
						<option value="student" selected="selected">Student</option>
						<option value="visitor">Visitor</option>
					</select>
					
					<label for="program" class="student alumni">Program</label>
					<select name="program" id="program" class="span4 student alumni">
						<option value="">Select...</option>
						<?php
							echo program_control("dropdown", "", "", "");
						?>
					</select>
					
					<label for="college" class="faculty student alumni visitor">Home College</label>
					<select name="college" id="college" class="span4 faculty student alumni visitor">
						<option value="">Select...</option>
						<?php
							echo college_control("dropdown", "", "", "");
						?>
					</select>
		
					<label for="department" class="faculty student alumni visitor">Home Department</label>
					<span id="department-menu">
						<input class="span4 disabled faculty student alumni visitor" name="department" id="department" disabled="disabled" value="select a college first" />
					</span>
					
					<label for="comajor" class="checkbox student alumni">
						<input type="checkbox" name="comajor" id="comajor" value="yes">
						<span>Seeking Co-major</span>
					</label>
					
					<div id="comajor-fields" class="hide">
						<label for="comajor-program">Comajor Program</label>
						<select name="comajor_program" id="comajor-program" class="span4">
							<option value="">Select...</option>
							<?php
								echo program_control("dropdown", "", "", "");
							?>
						</select>
					
						<label for="comajor-college">Comajor College</label>
						<select name="comajor_college" id="comajor-college" class="span4">
							<option value="">Select...</option>
							<?php
								echo college_control("dropdown", "", "", "");
							?>
						</select>
		
						<label for="department">Comajor Department</label>
						<span id="comajor-department-menu">
							<input class="span4 disabled" name="comajor_department" id="comajor-department" disabled="disabled" value="select a college first" />
						</span>
					</div>
					<label for="dissertation" class="alumni">Dissertation Title</label>  
					<input type="text" class="span4 alumni" maxlength="220" name="dissertation" id="dissertation" value=""/>
					
					<label for="title" class="student faculty staff visitor alumni online">Title</label>  
					<input type="text" class="span4 student faculty staff visitor alumni online" maxlength="220" name="title" id="title" value=""/>
				
					<label for="company" class="student alumni online">Company</label>  
					<input type="text" class="span4 student alumni online" maxlength="220" name="company" id="company" value=""/>
					
					<label for="city" class="student alumni visitor online">City</label>  
					<input type="text" class="span4 student alumni visitor online" maxlength="220" name="city" id="city" value=""/>
		
					<label for="state" class="student alumni visitor online">State</label>
					<select name="state" id="state" class="span4 student alumni visitor online">
						<option value="">Select...</option>
						<?php
							echo states_control("dropdown", "", "", "");
						?>
					</select>
					
					<label for="country" class="student alumni visitor online">Country</label>
					<select name="country" id="country" class="span4 student alumni visitor online">
						<option value="">Select...</option>
						<?php
							echo country_control("dropdown", "", "", "United States");
						?>
					</select>
					
					<label for="start-y" class="student faculty staff visitor alumni">Start Date</label>
					<select name="start_m" id="start-m" class="small student faculty staff visitor alumni">
						<option value="01">January</option>
						<option value="02">February</option>
						<option value="03">March</option>
						<option value="04">April</option>
						<option value="05">May</option>
						<option value="06">June</option>
						<option value="07">July</option>
						<option value="08">August</option>
						<option value="09">September</option>
						<option value="10">October</option>
						<option value="11">November</option>
						<option value="12">December</option>
					</select>
					<select name="start_y" id="start-y" class="small student faculty staff visitor alumni">
						<?php
							/*Populate years going backwards from current year*/
							echo year_control("dropdown", "", "", "", PROG_START_YEAR, CUR_YEAR);
						?>
					</select>
					
					<label for="grad-y" class="student alumni">Graduation Date</label>
					<select name="grad_m" id="grad-m" class="small student alumni">
						<option value="01">January</option>
						<option value="02">February</option>
						<option value="03">March</option>
						<option value="04">April</option>
						<option value="05">May</option>
						<option value="06">June</option>
						<option value="07">July</option>
						<option value="08">August</option>
						<option value="09">September</option>
						<option value="10">October</option>
						<option value="11">November</option>
						<option value="12">December</option>
					</select>
					<select name="grad_y" id="grad-y" class="small student alumni">
						<?php
							/*Populate years going backwards from current year*/
							echo year_control("dropdown", "", "", "", PROG_START_YEAR, CUR_YEAR+4);
						?>
					</select>
					
					<label for="admission-status" class="checkbox student">
						<input type="checkbox" name="admission_status" id="admission-status" value="yes">
						<span>Admission Status Pending</span>
					</label>
				</form>
			</div>
			<div class="span4 well">
				<form id="form-personal">
					<label for="education">Education</label>  
					<textarea class="span4" id="education" name="education"></textarea>
					<p class="help-block">Format: Degree, Department, University (Year)</p>
					<label for="bio">Bio</label>  
					<textarea class="span4 tall" name="bio" id="bio" ></textarea>
					
					<label for="interests">Interests</label>
					<input type="text" name="search_interests" value="" class="span4 search-query" id="search-interests" placeholder="Search Interests" style="width: 175px;">
					<small style="margin-left:5px;"><a href="#" id="clear-search">show all</a></small>
					<div id="interests">
						<?php
							echo interest_control("", false, "checkbox", "interest", "", "");
						?>
					</div>
				</form>
			</div>
		</div>
		<div class="row">
			<div class="span12" style="margin-bottom:50px">
				<hr/>
				<button class="btn btn-large btn-info pull-right" id="submit">Save Changes</button>
				<button class="btn btn-large pull-right" id="clear">Clear Changes</button>
				
			</div>
		</div>
	</div>
		
</body>
</html>
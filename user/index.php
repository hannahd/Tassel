<?php
require_once ("../constants/constants.php");
require_once (ROOT."/constants/dbconnect.php"); //Includes database connection
require_once (ROOT."/constants/functions.php"); //Includes functions
require_once (ROOT."/constants/validation-functions.php");
require_once (ROOT."/constants/access-functions.php"); //Includes functions to control user privileges

secure_page();

if(isset($_GET['p'])) {
	//Check that either the person owns this profile or this is an admin
	if(isset($_SESSION['encrypted_id']) && ($_GET['p'] === $_SESSION['encrypted_id'] || is_admin())){
		$md5_id = sanitize($_GET['p']);
		$profile = mysql_query("SELECT *, AES_DECRYPT(email, '". SALT ."') AS usr_email FROM ". TBL_PROFILE ." WHERE md5_id = '$md5_id'") or die(error_message("Could not access database", mysql_error(),21));
		list($id, $username, , , , $first_name, $last_name, $photo, $enabled, $user_level, $position, $date_created, $ip_address, , , , $num_logins, , $last_login, $email) = mysql_fetch_row($profile);
		switch ($position) {
		    case 'faculty':
		        $faculty = mysql_query("SELECT * FROM ". TBL_FACULTY ." WHERE profile_id = '$id'") or die(error_message("Could not access database", mysql_error(),21));	
				list(, $title, $department_id, $phone, $office_location, $education, $bio, $start_date_str, $last_update) = mysql_fetch_row($faculty);
				$start_date = explode("-", $start_date_str);
				if(!empty($department_id)){
					$college = mysql_query("SELECT college_id FROM ". TBL_DEPARTMENT ." WHERE id = '$department_id' LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
					$college_array = mysql_fetch_row($college);
					$college_id = $college_array[0];
				}
				break;
		    case 'student':
				$student = mysql_query("SELECT * FROM ". TBL_STUDENT ." WHERE profile_id = '$id'") or die(error_message("Could not access database", mysql_error(),21));	
				list(, $program_id, $department_id, $comajor_department_id, $phone, $office_location, $title, $company, $home_city, $state_id, $country_id, $education,  $bio, $start_date_str, $grad_date_str, $last_update, $admission_status) = mysql_fetch_row($student);
				$start_date = explode("-", $start_date_str);
				$grad_date = explode("-", $grad_date_str);
				if(!empty($department_id)){
					$college = mysql_query("SELECT college_id FROM ". TBL_DEPARTMENT ." WHERE id = '$department_id' LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
					$college_array = mysql_fetch_row($college);
					$college_id = $college_array[0];
				}
				if(!empty($comajor_department_id)){
					$college = mysql_query("SELECT college_id FROM ". TBL_DEPARTMENT ." WHERE id = '$comajor_department_id' LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
					$college_array = mysql_fetch_row($college);
					$comajor_college_id = $college_array[0];
				}
				break;
		    case 'staff':
		        $staff = mysql_query("SELECT * FROM ". TBL_STAFF ." WHERE profile_id = '$id'") or die(error_message("Could not access database", mysql_error(),21));
				list(, $title, $phone, $office_location, $bio, $start_date_str, $last_update) = mysql_fetch_row($staff);
				$start_date = explode("-", $start_date_str);
				break;
			case 'alumni':
				$alumni = mysql_query("SELECT * FROM ". TBL_ALUMNI ." WHERE profile_id = '$id'") or die(error_message("Could not access database", mysql_error(),21));	
				list(, $program_id, $department_id, $comajor_department_id, $title, $company, $company_city, $state_id, $country_id, $dissertation_title, $education,  $bio, $start_date_str, $grad_date_str, $last_update) = mysql_fetch_row($alumni);
				$start_date = explode("-", $start_date_str);
				$grad_date = explode("-", $grad_date_str);
				if(!empty($department_id)){
					$college = mysql_query("SELECT college_id FROM ". TBL_DEPARTMENT ." WHERE id = '$department_id' LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
					$college_array = mysql_fetch_row($college);
					$college_id = $college_array[0];
				}
				if(!empty($comajor_department_id)){
					$college = mysql_query("SELECT college_id FROM ". TBL_DEPARTMENT ." WHERE id = '$comajor_department_id' LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
					$college_array = mysql_fetch_row($college);
					$comajor_college_id = $college_array[0];
				}
	break;
			case 'visitor':
		        $visitor = mysql_query("SELECT * FROM ". TBL_VISITOR ." WHERE profile_id = '$id'") or die(error_message("Could not access database", mysql_error(),21));
				list(, $title, $department_id, $phone, $office_location, $education, $bio, $start_date_str, $last_update) = mysql_fetch_row($visitor);
				$start_date = explode("-", $start_date_str);
				if(!empty($department_id)){
					$college = mysql_query("SELECT college_id FROM ". TBL_DEPARTMENT ." WHERE id = '$department_id' LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
					$college_array = mysql_fetch_row($college);
					$college_id = $college_array[0];
				}
				break;
		}
	
		// Strip slashes from input fields for values that are set
		if(isset($phone)){ $phone = stripslashes($phone); }  
		if(isset($first_name)){ $first_name = stripslashes($first_name); }
		if(isset($last_name)){ $last_name = stripslashes($last_name); }
		if(isset($photo)){ $photo = trim(stripslashes($photo)); }
		if(isset($office_location)){ $office_location = stripslashes(html_entity_decode($office_location)); } 
		if(isset($title)){ $title = stripslashes(html_entity_decode($title)); }  
		if(isset($company)){ $company = stripslashes(html_entity_decode($company)); }
		if(isset($home_city)){ $home_city = stripslashes($home_city); }
		if(isset($company_city)){ $home_city = stripslashes($company_city); }
		if(isset($dissertation_title)){ $dissertation_title = stripslashes(html_entity_decode($dissertation_title)); }
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
?>
<!DOCTYPE html>
<head>
	<?php echo get_head_meta($first_name ." ". $last_name ." | Edit Profile"); ?>
	<script type="text/javascript">
		$(function()
		{
			//Reset form to saved values
			function reset_form_to_saved() {
				$('#first-name').val('<?php echo $first_name;?>');
				$('#last-name').val('<?php echo $last_name;?>');
				$('#username').val('<?php echo $username;?>');
				$('#email').val('<?php echo $email;?>');
				$('#photo').val('<?php echo $photo;?>');
				$('#position').val('<?php echo $position;?>');
				$('#user_level').val('<?php echo $user_level;?>');
				
				<?php
				// Modify fields to set based on position
				switch ($position) {
				    case 'faculty':?>
							$("#faculty-set").show();
							$("#faculty-set input").addClass("active-entry");
							$("#faculty-set select").addClass("active-entry");
							$("#faculty-set textarea").addClass("active-entry");
                                
					        $('#faculty-title').val('<?php echo $title;?>');
							$('#faculty-phone').val('<?php echo $phone;?>');
							$('#faculty-office-location').val('<?php echo $office_location;?>');
							$('#faculty-bio').val('<?php echo $bio;?>');
							$('#faculty-education').val('<?php echo $education;?>');
							$('#faculty-start-y').val('<?php echo $start_date[0];?>');
							$('#faculty-start-m').val('<?php echo $start_date[1];?>');
							<?php if(!empty($department_id)){ ?>
								$('#faculty-college').val('<?php echo $college_id;?>');
								$('#faculty-department').val('<?php echo $department_id;?>');
							<?php } ?>
				<?php	break;
				    case 'student':?>
							$("#student-set").show();
							$("#student-set input").addClass("active-entry");
							$("#student-set select").addClass("active-entry");
							$("#student-set textarea").addClass("active-entry");
                                
							$('#student-phone').val('<?php echo $phone;?>');
							$('#student-office-location').val('<?php echo $office_location;?>');
							$('#student-title').val('<?php echo $title;?>');
							$('#student-company').val('<?php echo $company;?>');
							$('#student-home-city').val('<?php echo $home_city;?>');
							$('#student-bio').val('<?php echo $bio;?>');
							$('#student-education').val('<?php echo $education;?>');
							$('#student-start-y').val('<?php echo $start_date[0];?>');
							$('#student-start-m').val('<?php echo $start_date[1];?>');
							$('#student-grad-y').val('<?php echo $grad_date[0];?>');
							$('#student-grad-m').val('<?php echo $grad_date[1];?>');
							$('#student-program').val('<?php echo $program_id;?>');
							$('#student-states').val('<?php echo $state_id;?>');
							$('#student-countries').val('<?php echo $country_id;?>');
							<?php if(!empty($department_id)){ ?>
									$('#student-college').val('<?php echo $college_id;?>');
									$('#student-department').val('<?php echo $department_id;?>');
							<?php } 
							 	  if(!empty($comajor_department_id)){ ?>
									$('#student-comajor').val(["yes"]);
									$("#student-comajor-fields").show();
									$('#student-comajor-college').val('<?php echo $comajor_college_id;?>');
									$('#student-comajor-department').val('<?php echo $comajor_department_id;?>');
							<?php } 
								  if($admission_status > 0){?>
									$('#student-admission-status').val(["yes"]	);
							<?php } ?>
				<?php	break;
				    case 'staff':?>
						$("#staff-set").show();
						$("#staff-set input").addClass("active-entry");
						$("#staff-set select").addClass("active-entry");
						$("#staff-set textarea").addClass("active-entry");
						
				        $('#staff-title').val('<?php echo $title;?>');
						$('#staff-phone').val('<?php echo $phone;?>');
						$('#staff-office-location').val('<?php echo $office_location;?>');
						$('#staff-bio').val('<?php echo $bio;?>');
						$('#staff-start-y').val('<?php echo $start_date[0];?>');
						$('#staff-start-m').val('<?php echo $start_date[1];?>');
				<?php	break;
					case 'alumni':?>
							$("#alumni-set").show();
							$("#alumni-set input").addClass("active-entry");
							$("#alumni-set select").addClass("active-entry");
							$("#alumni-set textarea").addClass("active-entry");
                                
							$('#alumni-dissertation-title').val('<?php echo $dissertation_title;?>');
							$('#alumni-title').val('<?php echo $title;?>');
							$('#alumni-company').val('<?php echo $company;?>');
							$('#alumni-company-city').val('<?php echo $company_city;?>');
							$('#alumni-bio').val('<?php echo $bio;?>');
							$('#alumni-education').val('<?php echo $education;?>');
							$('#alumni-start-y').val('<?php echo $start_date[0];?>');
							$('#alumni-start-m').val('<?php echo $start_date[1];?>');
							$('#alumni-grad-y').val('<?php echo $grad_date[0];?>');
							$('#alumni-grad-m').val('<?php echo $grad_date[1];?>');
							$('#alumni-program').val('<?php echo $program_id;?>');
							$('#alumni-states').val('<?php echo $state_id;?>');
							$('#alumni-countries').val('<?php echo $country_id;?>');
							<?php if(!empty($department_id)){ ?>
									$('#alumni-college').val('<?php echo $college_id;?>');
									$('#alumni-department').val('<?php echo $department_id;?>');
							<?php } 
							 	  if(!empty($comajor_department_id)){ ?>
									$('#alumni-comajor').val(["yes"]);
									$("#alumni-comajor-fields").show();
									$('#alumni-comajor-college').val('<?php echo $comajor_college_id;?>');
									$('#alumni-comajor-department').val('<?php echo $comajor_department_id;?>');
							<?php } ?>
				<?php 	break;
					case 'visitor':	?>
							$("#visitor-set").show();
							$("#visitor-set input").addClass("active-entry");
							$("#visitor-set select").addClass("active-entry");
							$("#visitor-set textarea").addClass("active-entry");

					        $('#visitor-title').val('<?php echo $title;?>');
							$('#visitor-phone').val('<?php echo $phone;?>');
							$('#visitor-office-location').val('<?php echo $office_location;?>');
							$('#visitor-bio').val('<?php echo $bio;?>');
							$('#visitor-education').val('<?php echo $education;?>');
							$('#visitor-start-y').val('<?php echo $start_date[0];?>');
							$('#visitor-start-m').val('<?php echo $start_date[1];?>');
							<?php if(!empty($department_id)){ ?>
								$('#visitor-college').val('<?php echo $college_id;?>');
								$('#visitor-department').val('<?php echo $department_id;?>');
							<?php } ?>
				<?php 	break;
				}
				?>
				
			}
			
			// Start form out set to saved values
			reset_form_to_saved();
			
			// Set minimum lengths for important values
			$("#profile-form").validate({
				  rules: {
				    first_name: {
					  minlength: 2
				    },
					last_name: {
					  minlength: 2
				    },
					username: {
					  minlength: 3
				    },
					email: {
					  minlength: 4
				    },
					password: {
					  minlength: 8
				    }
				  }
			});
			
			// Submit data via ajax
			$("#profile-submit").click(function()
			{
				// Determines if all the fields are valid
				var data_valid = $("#profile-form").valid();
					
				// Build datastring to pass through query
				var datastring = $('.active-entry').serialize();
				datastring += "&id=<?php echo $id; ?>";
				datastring += "&position=" + $("#position").val();		
				if(data_valid)
				{
					$.ajax({
					type: "POST",
					url: "<?php echo BASE; ?>/constants/process.php?action=update",
					data: datastring,
					success: function(response) {
						// Display errors or success
						$('body,html').animate({scrollTop: 0}, 800);
						$('#message').show().html(response);
					}
					});
				} else {
					$('html, body').animate({
					         scrollTop: $(".error").offset().top - 80
					     }, 1000);
				}
				return false;
			});
			
			// Update department based on college selection
			$(".college").change(function() {
				
				var position = $(this).attr('id');
				if(position.indexOf("comajor") > 0){
					position = position.substring(0, position.indexOf("-")) + "-comajor";
				} else {
					position = position.substring(0, position.indexOf("-"));
				}
				
				
				var datastring = "college_id=" + $(this).val()
								 + "&position=" + position;
				$.ajax({
				type: "POST",
				url: "<?php echo BASE; ?>/constants/process.php?action=update_departments",
				data: datastring,
				success: function(response) {
					// Fill department menu with appropriate departments
					switch (position) {
						case "student":
							$('#student-department-menu').html(response);
							$("#student-department-menu select").addClass("active-entry");
							break;
						case "alumni":
							$('#alumni-department-menu').html(response);
							$("#alumni-department-menu select").addClass("active-entry");
							break;
						case "faculty":
							$('#faculty-department-menu').html(response);
							$("#faculty-department-menu select").addClass("active-entry");
							break;
						case "staff":
							$('#staff-department-menu').html(response);
							$("#staff-department-menu select").addClass("active-entry");
							break;
						case "visitor":
							$('#visitor-department-menu').html(response);
							$("#visitor-department-menu select").addClass("active-entry");
							break;
						case "student-comajor":
							$('#student-comajor-department-menu').html(response);
							$("#student-comajor-department-menu select").addClass("active-entry");
							break;
						case "alumni-comajor":
							$('#alumni-comajor-department-menu').html(response);
							$("#alumni-comajor-department-menu select").addClass("active-entry");
							break;
					}
				}
				});
			});
			
			// Show and hide the comajor college and department menus
			$("#alumni-comajor").change(function () {
				if($("#alumni-comajor").attr('checked')){
			        $("#alumni-comajor-fields").slideDown();
				} else {
				    $("#alumni-comajor-fields").slideUp();
				}
			});
			
			// Show and hide the comajor college and department menus
			$("#student-comajor").change(function () {
				if($("#student-comajor").attr('checked')){
			        $("#student-comajor-fields").slideDown();
				} else {
				    $("#student-comajor-fields").slideUp();
				}
			});
		});
	</script>
</head>
<body>
	<?php include ROOT.'/constants/navbar.php'; ?>
	<div class="container">
		<div class="row">
			<div class="span7 offset4">
				<h1>Edit Profile</h1>
				<noscript><p class="error alert span4"Javascript must be enabled to use this form.</p></noscript>
				<div id="message" class="span4"></div>
				<form method="post" name="profile-form" id="profile-form">
					<fieldset id="profile-set">
						<label for="first_name">First Name</label>  
						<input class="required active-entry span4" type="text" maxlength="220" name="first_name" id="first-name" value=""/>
				
						<label for="last_name">Last Name</label> 
						<input class="required active-entry span4" type="text" maxlength="220" name="last_name" id="last-name"/>
				
						<label for="username">Username</label>  
						<input class="required active-entry span4" type="text" maxlength="220" name="username" id="username" value=""/>
				
						<label for="password">Password</label>  
						<input class="active-entry span4" type="password" maxlength="50"  name="password" id="password" value=""/>
				
						<label for="email">Email</label>
						<input class="required email active-entry span4" type="text" maxlength="256" name="email" id="email"/>
				
						<label for="photo">Photo (URL)</label>
						<input class="url active-entry span4" type="text" maxlength="220" name="photo" id="photo"/>

						<label for="user_level">Privilege Level</label>
						<select class="required active-entry span4" name="user_level" id="user-level">
							<option value="1" SELECTED>User</option>
							<option value="5">Admin</option>
						</select>
				
						<label for="position">Position</label>
						<select class="required active-entry span4" name="position" id="position" disabled="disabled">
							<option value="">--Select--</option>
							<option value="faculty">Faculty</option>
							<option value="staff">Staff</option>
							<option value="student">Student</option>
							<option value="alumni">Alumni</option>
							<option value="visitor">Visiting Scholar</option>
						</select>
					</fieldset>
			
					<fieldset id="staff-set" style="display:none;">
						<label for="staff_title">Title</label>  
						<input class="span4" type="text" maxlength="220" name="staff_title" id="staff-title" value=""/>
				
						<label for="staff_phone">Phone</label> 
						<input class="span4" type="text" maxlength="220" name="staff_phone" id="staff-phone" value=""/>
				
						<label for="staff_office_location">Office</label>  
						<input class="span4" type="text" maxlength="220" name="staff_office_location" id="staff-office-location" value=""/>
				
						<label for="staff_bio">Bio</label>  
						<textarea class="span4 xtall" name="staff_bio" id="staff-bio" ></textarea>
				
						<label for="staff_start_y">Start Date</label>
						<select class="span2" name="staff_start_m" id="staff-start-m">
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
						<select class="span2" name="staff_start_y" id="staff-start-y">
							<?php
							/*Populate years going backwards from current year*/
							echo get_year_options(PROG_START_YEAR);
							?>
						</select>
					</fieldset>
			
					<fieldset id="visitor-set" style="display:none;">
						<label for="visitor_title">Title</label>  
						<input class="span4" type="text" maxlength="220" name="visitor_title" id="visitor-title" value=""/>
				
						<label for="visitor_college">College</label>
						<?php
							echo get_college_dropdown("visitor");
						?>
				
						<label for="visitor_department">Department</label>
						<span id="visitor-department-menu">
							<?php if(!empty($department_id)){
								echo get_department_dropdown($college_id, "visitor");
						    } else { ?>
							<select class="span4" name="visitor_department" id="visitor-department">
								<option value="">--Select a college first--</option>
							</select>
							<?php }?>
						</span>
				
						<label for="visitor_phone">Phone</label> 
						<input class="span4" type="text" maxlength="220" name="visitor_phone" id="visitor-phone" value=""/>
				
						<label for="visitor_office_location">Office</label>  
						<input class="span4" type="text" maxlength="220" name="visitor_office_location" id="visitor-office-location" value=""/>
				
						<label for="visitor_education">Education</label>  
						<textarea class="span4 tall" id="visitor-education" name="visitor_education"></textarea>
				
						<label for="visitor_bio">Bio</label>  
						<textarea class="span4 xtall" id="visitor-bio" name="visitor_bio"></textarea>
				
						<label for="visitor_start_y">Start Date</label>
						<select class="span2" name="visitor_start_m" id="visitor-start-m">
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
						<select class="span2" name="visitor_start_y" id="visitor-start-y">
							<?php
							/*Populate years going backwards from current year*/
							echo get_year_options(PROG_START_YEAR);
							?>
						</select>
					</fieldset>
			
					<fieldset id="faculty-set" style="display:none;">
						<label for="faculty_title">Title</label>  
						<input class="span4" type="text" maxlength="220" name="faculty_title" id="faculty-title" value=""/>
				
						<label for="faculty_college">College</label>
						<?php
							echo get_college_dropdown("faculty");
						?>
				
						<label for="faculty_department">Department</label>
						<span id="faculty-department-menu">
							<?php if(!empty($department_id)){
									echo get_department_dropdown($college_id, "faculty");
							    } else { ?>
								<select class="span4" name="faculty_department" id="faculty-department">
									<option value="">--Select a college first--</option>
								</select>
							<?php }?>
						</span>
				
						<label for="faculty_phone">Phone</label> 
						<input class="span4" type="text" maxlength="220" name="faculty_phone" id="faculty-phone" value=""/>
				
						<label for="faculty_office_location">Office</label>  
						<input class="span4" type="text" maxlength="220" name="faculty_office_location" id="faculty-office-location" value=""/>
				
						<label for="faculty_education">Education</label>  
						<textarea class="span4 tall" id="faculty-education" name="faculty_education"></textarea>
				
						<label for="faculty_bio">Bio</label>  
						<textarea class="span4 xtall" id="faculty-bio" name="faculty_bio"></textarea>
				
						<label for="faculty_start_y">Start Date</label>
						<select class="span2" name="faculty_start_m" id="faculty-start-m">
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
						<select class="span2" name="faculty_start_y" id="faculty-start-y">
							<?php
							/*Populate years going backwards from current year*/
							echo get_year_options(PROG_START_YEAR);
							?>
						</select>
					</fieldset>
			
					<fieldset id="student-set" style="display:none;">
						<label for="student_program">Program</label>
						<?php
							echo get_program_dropdown("student");
						?>
				
						<label for="student_college">College</label>
						<?php
							echo get_college_dropdown("student");
						?>
				
						<label for="student_department">Department</label>
						<span id="student-department-menu">
							<?php if(!empty($department_id)){
								echo get_department_dropdown($college_id, "student");
						    } else { ?>
							<select class="span4" name="student_department" id="student-department">
								<option value="">--Select a college first--</option>
							</select>
							<?php }?>
						</span>
				
						<label for="student_comajor" class="checkbox">
							<input type="checkbox" name="student_comajor" id="student-comajor" value="yes">
							<span>Seeking Co-major</span>
						</label>
				
						<span id="student-comajor-fields" style="display:none;">
							<label for="student_comajor_college">Comajor College</label>
							<?php
								echo get_college_dropdown("student_comajor");
							?>

							<label for="student_comajor_department">Comajor Department</label>
							<span id="student-comajor-department-menu">
									<?php if(!empty($comajor_department_id)){
										echo get_department_dropdown($college_id, "student_comajor");
								    } else { ?>
									<select class="span4" name="student_comajor_department" id="student-comajor-department">
										<option value="">--Select a college first--</option>
									</select>
									<?php }?>
							</span>
						</span>
				
						<label for="student_phone">Phone</label> 
						<input class="span4" type="text" maxlength="220" name="student_phone" id="student-phone" value=""/>
				
						<label for="student_office_location">Office <small>(if on-campus student)</small></label>  
						<input class="span4" type="text" maxlength="220" name="student_office_location" id="student-office-location" value=""/>
				
						<label for="student_title">Title <small>(if online student)</small></label>  
						<input class="span4" type="text" maxlength="220" name="student_title" id="student-title" value=""/>

						<label for="student_company">Company <small>(if online student)</small></label>  
						<input class="span4" type="text" maxlength="220" name="student_company" id="student-company" value=""/>

				
						<label for="student_home_city">City</label>  
						<input class="span4" type="text" maxlength="220" name="student_home_city" id="student-home-city" value=""/>
				
						<label for="student_states">State</label>
						<?php
							echo get_states_dropdown("student");
						?>
				
						<label for="student_countries">Country</label>
						<?php
							echo get_countries_dropdown("student");
						?>
				
						<label for="student_education">Education</label>  
						<textarea class="span4 tall" id="student-education" name="student_education"></textarea>
				
						<label for="student_bio">Bio</label>  
						<textarea class="span4 xtall" id="student-bio" name="student_bio"></textarea>
				
						<label for="student_start_y">Start Date</label>
						<select class="span2" name="student_start_m" id="student-start-m">
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
						<select class="span2" name="student_start_y" id="student-start-y">
							<?php
							/*Populate years going backwards from current year*/
							echo get_year_options(PROG_START_YEAR);
							?>
						</select>
				
						<label for="student_grad_y">Expected Graduation Date</label>
						<select class="span2" name="student_grad_m" id="student-grad-m">
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
						<select class="span2" name="student_grad_y" id="student-grad-y">
							<?php
							/*Populate years going backwards from four years in the future*/
							echo get_year_options(CUR_YEAR, CUR_YEAR + 4);
							?>
						</select>
				
						<label for="student_admission_status" class="checkbox">
							<input type="checkbox" name="student_admission_status" id="student-admission-status" value="yes">
							<span>Admission Status Pending</span>
						</label>
				
					</fieldset>
			
					<fieldset id="alumni-set" style="display:none;">
						<label for="alumni_program">Program</label>
						<?php
							echo get_program_dropdown("alumni");
						?>
				
						<label for="alumni_college">College</label>
						<?php
							echo get_college_dropdown("alumni");
						?>
				
						<label for="alumni_department">Department</label>
						<span id="alumni-department-menu">
							<?php if(!empty($department_id)){
								echo get_department_dropdown($college_id, "alumni");
						    } else { ?>
							<select class="span4" name="alumni_department" id="alumni-department">
								<option value="">--Select a college first--</option>
							</select>
							<?php }?>
						</span>
				
						<label for="alumni_comajor" class="checkbox">
							<input type="checkbox" name="alumni_comajor" id="alumni-comajor" value="yes">
							<span>Earned Co-major</span>
						</label>
				
						<span id="alumni-comajor-fields" style="display:none;">
							<label for="alumni_comajor_college">Co-major College</label>
							<?php
								echo get_college_dropdown("alumni_comajor");
							?>

							<label for="alumni_comajor_department">Co-major Department</label>
							<span id="alumni-comajor-department-menu">
								<?php if(!empty($comajor_department_id)){
									echo get_department_dropdown($college_id, "alumni_comajor");
							    } else { ?>
								<select class="span4" name="alumni_comajor_department" id="alumni-comajor-department">
									<option value="">--Select a college first--</option>
								</select>
								<?php }?>
							</span>
						</span>
				
						<label for="alumni_dissertation_title">Dissertation Title</label> 
						<input class="span4" type="text" maxlength="220" name="alumni_dissertation_title" id="alumni-dissertation-title" value=""/>
				
						<label for="alumni_title">Title</small></label>  
						<input class="span4" type="text" maxlength="220" name="alumni_title" id="alumni-title" value=""/>
				
						<label for="alumni_company">Company</label>  
						<input class="span4" type="text" maxlength="220" name="alumni_company" id="alumni-company" value=""/>
				
						<label for="alumni_company_city">City</label>  
						<input class="span4" type="text" maxlength="220" name="alumni_company_city" id="alumni-company-city" value=""/>
				
						<label for="alumni_states">State</label>
						<?php
							echo get_states_dropdown("alumni");
						?>
				
						<label for="alumni_countries">Country</label>
						<?php
							echo get_countries_dropdown("alumni");
						?>
				
						<label for="alumni_education">Education</label>  
						<textarea  class="span4 tall"  class="span4" id="alumni-education" name="alumni_education"></textarea>
				
						<label for="alumni_bio">Bio</label>  
						<textarea  class="span4 xtall" id="alumni-bio" name="alumni_bio"></textarea>
				
						<label for="alumni_start_y">Start Date</label>
						<select class="span2" name="alumni_start_m" id="alumni-start-m">
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
						<select class="span2" name="alumni_start_y" id="alumni-start-y">
							<?php
							/*Populate years going backwards from current year*/
							echo get_year_options(PROG_START_YEAR);
							?>
						</select>
				
						<label for="alumni_grad_y">Graduation Date</label>
						<select class="span2" name="alumni_grad_m" id="alumni-grad-m">
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
						<select class="span2" name="alumni_grad_y" id="alumni-grad-y">
							<?php
							/*Populate years going backwards from four years in the future*/
							echo get_year_options(PROG_START_YEAR);
							?>
						</select>
					</fieldset>
			
			
					<input type="submit" value="Save Changes" id="profile-submit" class="submit btn btn-primary span4" />
			
				</form>
			</div>
		</div>
	</div>
</body>
</html>

<?php 
	} 
	// The user did not have sufficient priveleges to edit this profile
	else {
	?>
		<!DOCTYPE html>
		<head>
			<?php echo get_head_meta("Edit Profile"); ?>
		</head>
		<body>
			<?php include ROOT.'/constants/navbar.php'; ?>
			<div class="container">
				<p class="alert
 no-data">You do not have permission to edit this profile.</p>
			</div>
		</body>
		</html>
	<?php
	}
	
} 
// Could not find the listed profile.
else {
?>
	<!DOCTYPE html>
	<head>
		<?php echo get_head_meta("Edit Profile"); ?>
	</head>
	<body>
		<?php include ROOT.'/constants/navbar.php'; ?>
		<div class="container">
			<p class="alert
 no-data">Sorry, we couldn't find this profile.</p>
		</div>
	</body>
	</html>
<?php
}

?>
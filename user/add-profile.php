<?php
require_once ("../constants/constants.php");
require_once (ROOT."/constants/dbconnect.php"); //Includes database connection
require_once (ROOT."/constants/functions.php"); //Includes functions

// TODO: Make title autofill
// TODO: Add groups, links, interests and people fieldsets
?>
<!DOCTYPE html>
<head>
	<?php echo get_head_meta("Add person"); ?>
	<script type="text/javascript">
		$(function()
		{
			//Reset form to blank values
			function reset_form() {
				$('input[type="text"]').val('');
				$('input[type="password"]').val('');
				$('textarea').val('');
				$('.error').hide(); //If showing error, fade out
				$('select').val("");
				
				$("fieldset").not($("#profile-set")).slideUp();
				$("input").not($("#profile-set input")).removeClass("active-entry");
				$("select").not($("#profile-set select")).removeClass("active-entry");
				$("textarea").not($("#profile-set textarea")).removeClass("active-entry");
			}
			
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
						
				if(data_valid)
				{
					$.ajax({
					type: "POST",
					url: "<?php echo BASE; ?>/constants/process.php?action=add",
					data: datastring,
					success: function(response) {
						// Check if entry was successful
						if(response.indexOf("success") != -1) {
							$('#message').show().html(response);
							//reset_form();
						} else {
							// Display errors
							$('#message').show().html(response);
						}
					}
					});
				}
				
				return false;
			});
			
			// Show more options based on the person's position
			$("#position").change(function() {
				
			   	$("fieldset").not($("#profile-set")).slideUp();
				$("input").not($("#profile-set input")).removeClass("active-entry");
				$("select").not($("#profile-set select")).removeClass("active-entry");
				$("textarea").not($("#profile-set textarea")).removeClass("active-entry");
				
				switch ($("#position option:selected").val()) {
					case "student":
						$("#student-set").slideDown();
						$("#student-set input").addClass("active-entry");
						$("#student-set select").addClass("active-entry");
						$("#student-set textarea").addClass("active-entry");
						break;
					case "alumni":
						$("#alumni-set").slideDown();
						$("#alumni-set input").addClass("active-entry");
						$("#alumni-set select").addClass("active-entry");
						$("#alumni-set textarea").addClass("active-entry");
						break;
					case "faculty":
						$("#faculty-set").slideDown();
						$("#faculty-set input").addClass("active-entry");
						$("#faculty-set select").addClass("active-entry");
						$("#faculty-set textarea").addClass("active-entry");
						break;
					case "staff":
						$("#staff-set").slideDown();
						$("#staff-set input").addClass("active-entry");
						$("#staff-set select").addClass("active-entry");
						$("#staff-set textarea").addClass("active-entry");
						break;
					case "visitor":
						$("#visitor-set").slideDown();
						$("#visitor-set input").addClass("active-entry");
						$("#visitor-set select").addClass("active-entry");
						$("#visitor-set textarea").addClass("active-entry");
						break;
				}
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
	<noscript><p class="error message"Javascript must be enabled to use this form.</p></noscript>
		<span id="message"></span>
		<form method="post" name="profile-form" id="profile-form">
			<fieldset id="profile-set">
				<label for="first_name">First Name</label>  
				<input class="required active-entry" type="text" maxlength="220" name="first_name" id="first-name" value=""/>
				
				<label for="last_name">Last Name</label> 
				<input class="required active-entry" type="text" maxlength="220" name="last_name" id="last-name"/>
				
				<label for="username">Username</label>  
				<input class="required active-entry" type="text" maxlength="220" name="username" id="username" value=""/>
				
				<label for="password">Password</label>  
				<input class="required active-entry" type="password" maxlength="50"  name="password" id="password" value=""/>
				
				<label for="email">Email</label>
				<input class="required email active-entry" type="text" maxlength="256" name="email" id="email"/>
				
				<label for="photo">Photo (URL)</label>
				<input class="url active-entry" type="text" maxlength="220" name="photo" id="photo"/>

				<label for="user_level">Privilege Level</label>
				<select class="required active-entry" name="user_level" id="user-level">
					<option value="1" SELECTED>User</option>
					<option value="5">Admin</option>
				</select>
				
				<label for="position">Position</label>
				<select class="required active-entry" name="position" id="position">
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
				<input type="text" maxlength="220" name="staff_title" id="staff-title" value=""/>
				
				<label for="staff_phone">Phone</label> 
				<input type="text" maxlength="220" name="staff_phone" id="staff-phone" value=""/>
				
				<label for="staff_office_location">Office</label>  
				<input type="text" maxlength="220" name="staff_office_location" id="staff-office-location" value=""/>
				
				<label for="staff_bio">Bio</label>  
				<textarea name="staff_bio" id="staff-bio" ></textarea>
				
				<label for="staff_start_y">Start Date</label>
				<select name="staff_start_m" id="staff-start-m">
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
				<select name="staff_start_y" id="staff-start-y">
					<?php
					/*Populate years going backwards from current year*/
					echo get_year_options(PROG_START_YEAR);
					?>
				</select>
			</fieldset>
			
			<fieldset id="visitor-set" style="display:none;">
				<label for="visitor_title">Title</label>  
				<input type="text" maxlength="220" name="visitor_title" id="visitor-title" value=""/>
				
				<label for="visitor_college">College</label>
				<?php
					echo get_college_dropdown("visitor");
				?>
				
				<label for="visitor_department">Department</label>
				<span id="visitor-department-menu">
					<select name="visitor_department" id="visitor-department">
						<option value="">--Select a college first--</option>
					</select>
				</span>
				
				<label for="visitor_phone">Phone</label> 
				<input type="text" maxlength="220" name="visitor_phone" id="visitor-phone" value=""/>
				
				<label for="visitor_office_location">Office</label>  
				<input type="text" maxlength="220" name="visitor_office_location" id="visitor-office-location" value=""/>
				
				<label for="visitor_education">Education</label>  
				<textarea id="visitor-education" name="visitor_education"></textarea>
				
				<label for="visitor_bio">Bio</label>  
				<textarea id="visitor-bio" name="visitor_bio"></textarea>
				
				<label for="visitor_start_y">Start Date</label>
				<select name="visitor_start_m" id="visitor-start-m">
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
				<select name="visitor_start_y" id="visitor-start-y">
					<?php
					/*Populate years going backwards from current year*/
					echo get_year_options(PROG_START_YEAR);
					?>
				</select>
			</fieldset>
			
			<fieldset id="faculty-set" style="display:none;">
				<label for="faculty_title">Title</label>  
				<input type="text" maxlength="220" name="faculty_title" id="faculty-title" value=""/>
				
				<label for="faculty_college">College</label>
				<?php
					echo get_college_dropdown("faculty");
				?>
				
				<label for="faculty_department">Department</label>
				<span id="faculty-department-menu">
					<select name="faculty_department" id="faculty-department">
						<option value="">--Select a college first--</option>
					</select>
				</span>
				
				<label for="faculty_phone">Phone</label> 
				<input type="text" maxlength="220" name="faculty_phone" id="faculty-phone" value=""/>
				
				<label for="faculty_office_location">Office</label>  
				<input type="text" maxlength="220" name="faculty_office_location" id="faculty-office-location" value=""/>
				
				<label for="faculty_education">Education</label>  
				<textarea id="faculty-education" name="faculty_education"></textarea>
				
				<label for="faculty_bio">Bio</label>  
				<textarea id="faculty-bio" name="faculty_bio"></textarea>
				
				<label for="faculty_start_y">Start Date</label>
				<select name="faculty_start_m" id="faculty-start-m">
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
				<select name="faculty_start_y" id="faculty-start-y">
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
					<select name="student_department" id="student-department">
						<option value="">--Select a college first--</option>
					</select>
				</span>
				
				<label for="student_comajor">
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
						<select name="student_comajor_department" id="student-comajor-department">
							<option value="">--Select a college first--</option>
						</select>
					</span>
				</span>
				
				<label for="student_phone">Phone</label> 
				<input type="text" maxlength="220" name="student_phone" id="student-phone" value=""/>
				
				<label for="student_office_location">Office <small>(if on-campus student)</small></label>  
				<input type="text" maxlength="220" name="student_office_location" id="student-office-location" value=""/>
				
				<label for="student_title">Title <small>(if online student)</small></label>  
				<input type="text" maxlength="220" name="student_title" id="student-title" value=""/>

				<label for="student_company">Company <small>(if online student)</small></label>  
				<input type="text" maxlength="220" name="student_company" id="student-company" value=""/>

				
				<label for="student_home_city">City</label>  
				<input type="text" maxlength="220" name="student_home_city" id="student-home-city" value=""/>
				
				<label for="student_states">State</label>
				<?php
					echo get_states_dropdown("student");
				?>
				
				<label for="student_countries">Country</label>
				<?php
					echo get_countries_dropdown("student");
				?>
				
				<label for="student_education">Education</label>  
				<textarea id="student-education" name="student_education"></textarea>
				
				<label for="student_bio">Bio</label>  
				<textarea id="student-bio" name="student_bio"></textarea>
				
				<label for="student_start_y">Start Date</label>
				<select name="student_start_m" id="student-start-m">
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
				<select name="student_start_y" id="student-start-y">
					<?php
					/*Populate years going backwards from current year*/
					echo get_year_options(PROG_START_YEAR);
					?>
				</select>
				
				<label for="student_grad_y">Expected Graduation Date</label>
				<select name="student_grad_m" id="student-grad-m">
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
				<select name="student_grad_y" id="student-grad-y">
					<?php
					/*Populate years going backwards from four years in the future*/
					echo get_year_options(CUR_YEAR, CUR_YEAR + 4);
					?>
				</select>
				
				<label for="student_admission_status">
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
					<select name="alumni_department" id="alumni-department">
						<option value="">--Select a college first--</option>
					</select>
				</span>
				
				<label for="alumni_comajor">
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
						<select name="alumni_comajor_department" id="alumni-comajor-department">
							<option value="">--Select a college first--</option>
						</select>
					</span>
				</span>
				
				<label for="alumni_dissertation_title">Dissertation Title</label> 
				<input type="text" maxlength="220" name="alumni_dissertation_title" id="alumni-dissertation-title" value=""/>
				
				<label for="alumni_title">Title</small></label>  
				<input type="text" maxlength="220" name="alumni_title" id="alumni-title" value=""/>
				
				<label for="alumni_company">Company</label>  
				<input type="text" maxlength="220" name="alumni_company" id="alumni-company" value=""/>
				
				<label for="alumni_company_city">City</label>  
				<input type="text" maxlength="220" name="alumni_company_city" id="alumni-company-city" value=""/>
				
				<label for="alumni_states">State</label>
				<?php
					echo get_states_dropdown("alumni");
				?>
				
				<label for="alumni_countries">Country</label>
				<?php
					echo get_countries_dropdown("alumni");
				?>
				
				<label for="alumni_education">Education</label>  
				<textarea id="alumni-education" name="alumni_education"></textarea>
				
				<label for="alumni_bio">Bio</label>  
				<textarea id="alumni-bio" name="alumni_bio"></textarea>
				
				<label for="alumni_start_y">Start Date</label>
				<select name="alumni_start_m" id="alumni-start-m">
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
				<select name="alumni_start_y" id="alumni-start-y">
					<?php
					/*Populate years going backwards from current year*/
					echo get_year_options(PROG_START_YEAR);
					?>
				</select>
				
				<label for="alumni_grad_y">Graduation Date</label>
				<select name="alumni_grad_m" id="alumni-grad-m">
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
				<select name="alumni_grad_y" id="alumni-grad-y">
					<?php
					/*Populate years going backwards from four years in the future*/
					echo get_year_options(PROG_START_YEAR);
					?>
				</select>
			</fieldset>
			
			
			<input type="submit" value="Add Person" class="submit" id="profile-submit" />
			
		</form>
		
		
</body>
</html>
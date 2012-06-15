<?php
/**
 * Front End for for Adding Profiles to Tassel.
 *
 * This page contains the form for adding profiles to the
 * directory.
 * 
 * Access limited to admins.
 *
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
?>
<!DOCTYPE html>
<head>
	<?php echo get_head_meta("New Profile"); ?>
	<script type="text/javascript">
		$(document).ready(function(){
			// Set focus orginally on the name
			$("#name").focus();
			
			//Reset form to blank values
			function reset_form() {
				$('body,html').animate({scrollTop: 0}, 800);
				$('input[type="text"]').val('');
				$('input[type="password"]').val('');
				$('input[type="checkbox"]').prop("checked", false);
				$('textarea').val('');
				$('.error').hide(); //If showing error, fade out
				$('select').val("");
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
					url: "<?php echo BASE; ?>/constants/set_profile.php?action=add",
					data: datastring,
					success: function(response) {
						// Check if entry was successful
						if(response){
							$('#message').show().html(response);
						} else {
							window.location.href = "<?php echo BASE;?>/admin/manage_profiles.php?added=t";
						}
					}
				});
				
				return false;
			});
			
			// Initially show student details
			$(".alumni, .faculty, .visitor, .staff").not($(".student")).hide();
			$(".student").show();
			$(".online").hide();
			
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
			
			// Show more options based on the program
			$("#program").change(function() {
				if($("#position option:selected").val() == "student" 
					&& $("#program option:selected").text().indexOf('Online') != -1){
					$(".online").show();
				} else if($("#position option:selected").val() == "student") {
					$(".online").hide();
				}
			});
			
			// Update department based on college selection
			$("#college").change(function() {
				var datastring = "college_id=" + $(this).val();
				
				$.ajax({
				type: "POST",
				url: "<?php echo BASE; ?>/constants/process.php?action=update_departments&comajor=false",
				data: datastring,
				success: function(response) {
					// Fill department menu with appropriate departments
					$('#department-menu').html(response);
				}
				});
			});
			
			// Update comajor department based on college selection
			$("#comajor-college").change(function() {
				var datastring = "college_id=" + $(this).val();
				
				$.ajax({
				type: "POST",
				url: "<?php echo BASE; ?>/constants/process.php?action=update_departments&comajor=true",
				data: datastring,
				success: function(response) {
					// Fill department menu with appropriate departments
					$('#comajor-department-menu').html(response);
				}
				});
			});
			
			// Show and hide the comajor college and department menus
			$("#comajor").change(function () {
				if($("#comajor").attr('checked')){
			        $("#comajor-fields").slideDown();
				} else {
				    $("#comajor-fields").slideUp();
				}
			});
		
			// Show preview of the profile photo
			$("#photo").focusout(function () {
				if($(this).val() != ""){
					$('#preview-image').attr("src", $(this).val());
					$('#photo-preview').show();
				} else{
					$('#photo-preview').hide();
				}
			});
		});
	</script>
</head>
<body>
	<?php include '../constants/navbar.php'; ?>
	<div class="container">	
		<div class="row">
			<div class="span12">
				<h2>Create New Profile</h2>
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
				<button class="btn btn-large btn-success pull-right" id="submit">Create Profile</button>
			</div>
		</div>
	</div>
		
</body>
</html>
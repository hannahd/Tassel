<?php
/**
 * Data processor for Tassel.
 * 
 * This file provides an interface between the forms and 
 * the database. Includes adding, updating and deleting 
 * profiles, interests, and links.
 *
 * Error codes starting with 2. 
 *
 * @author Hannah Deering
 * @package Tassel
 **/

// TODO: Make all errors appear first round (instead of profile errors then position errors)
// TODO: Remove username, have users log in with email and password
// TODO: Auto generate password & email it to user
// TODO: Normalize format on phone num

require_once ("constants.php");
require_once (ROOT."/constants/dbconnect.php"); //Includes database connection details
require_once (ROOT."/constants/functions.php"); //Includes functions
require_once (ROOT."/constants/validation-functions.php"); //Includes data sanitation & validation functions

/* Add Details Functions
 * =================================================================== */

/** Add faculty profile details. */
function set_faculty($profile_qry, $data, $action){
	$errors = array();
	
	// Check that all the necessary data is present
	if(isset($data['faculty_title']) && isset($data['faculty_phone']) && isset($data['faculty_office_location']) && isset($data['faculty_bio']) && isset($data['faculty_education']) && isset($data['faculty_start_m']) && isset($data['faculty_start_y']) && isset($data['faculty_department']) ) {
		$title = sanitize($data['faculty_title']);
		$department_id = sanitize($data['faculty_department']);
		$phone = sanitize($data['faculty_phone']);
		$office_location = sanitize($data['faculty_office_location']);
		$bio = sanitize($data['faculty_bio']);
		$education = sanitize($data['faculty_education']);
		$start_date = sanitize($data['faculty_start_y']) . "-" . sanitize($data['faculty_start_m']) . "-01";
	
		// Validate the title
		$result = validate_input($title, "Title", true, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the phone number
		$result = validate_input($phone, "Phone", false, "phone", 20);
		$errors = array_merge($errors, $result);
		
		// Validate the office location
		$result = validate_input($office_location, "Office", false, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the start date
		$result = validate_input($start_date, "Start Date", false, "date");
		$errors = array_merge($errors, $result);
		
		// Validate the department
		$result = validate_input($department_id, "Department", true, "num");
		$errors = array_merge($errors, $result);
		
		// Check if there were errors
		if (count($errors) == 0){
			
			// Check if this is an add or update
			if($action === "add"){
				$profile_id = set_profile($profile_qry, "add");
			
				// Create faculty profile
				$add_faculty = mysql_query("INSERT INTO `". TBL_FACULTY ."`  
					(`profile_id`, `title`, `department_id`, `phone`, `office_location`, `education`, `bio`, `start_date`) 
					VALUES ('$profile_id', '$title', '$department_id', '$phone', '$office_location', '$education', '$bio', '$start_date')") 
					or die(error_message("The profile could not be created", mysql_error(), 24));
				
				if($data["enabled"]){
					echo '<p class="success alert">Profile created!</p>';
				} else {
					echo '<p class="success alert">Your profile was submitted. An admin will review it and it will be up on the site shortly.</p>';
				}
			} elseif ($action === "update"){
				
				set_profile($profile_qry, "update");
				$profile_id = $data["id"];
				
				$update_faculty = mysql_query("UPDATE ". TBL_FACULTY ." SET title='$title', department_id='$department_id', phone='$phone', office_location='$office_location', education='$education', bio='$bio', start_date='$start_date' WHERE profile_id='$profile_id'") or die(error_message("The visitor profile could not be updated", mysql_error(), "22b"));;
				
				echo '<p class="success alert">Profile updated!</p>';
			}
			 
		} else {
			// Print errors
			echo '<ul class="error alert"><span>Please correct the following:</span>';
			foreach($errors as $e) {
				echo "<li>".$e ."</li>";
			}
			echo '</ul>';
		}
		
	} else {
		// If all the necessary data wasn't present
		echo '<p class="error alert">There was a problem with your request. Please contact an admin.</p>';
	}
}

/** Add staff profile details. */
function set_staff($profile_qry, $data, $action){
	$errors = array();
	
	// Check that all the necessary data is present
	if(isset($data['staff_title']) && isset($data['staff_phone']) && isset($data['staff_office_location']) && isset($data['staff_bio']) && isset($data['staff_start_m']) && isset($data['staff_start_y'])) {
		$title = sanitize($data['staff_title']);
		$phone = sanitize($data['staff_phone']);
		$office_location = sanitize($data['staff_office_location']);
		$bio = sanitize($data['staff_bio']);
		$start_date = sanitize($data['staff_start_y']) . "-" . sanitize($data['staff_start_m']) . "-01";
	
		// Validate the title
		$result = validate_input($title, "Title", true, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the phone number
		$result = validate_input($phone, "Phone", false, "phone", 20);
		$errors = array_merge($errors, $result);
		
		// Validate the office location
		$result = validate_input($office_location, "Office", false, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the start date
		$result = validate_input($start_date, "Start Date", false, "date");
		$errors = array_merge($errors, $result);
		
		// Check if there were errors
		if (count($errors) == 0){
			
			// Check if this is an add or update
			if($action === "add"){
				
				$profile_id = set_profile($profile_qry, "add");
			
				// Create staff profile
				$add_staff = mysql_query("INSERT INTO `". TBL_STAFF ."`  
					(`profile_id`, `title`, `phone`, `office_location`, `bio`, `start_date`) 
					VALUES ('$profile_id', '$title', '$phone', '$office_location', '$bio', '$start_date')") 
					or die(error_message("The staff profile could not be created", mysql_error(), 26));
			 
				if($data["enabled"]){
					echo '<p class="success alert">Profile created!</p>';
				} else {
					echo '<p class="success alert">Your profile was submitted. An admin will review it and it will be up on the site shortly.</p>';
				}
			} elseif ($action === "update"){

				set_profile($profile_qry, "update");
				$profile_id = $data["id"];

				$update_staff = mysql_query("UPDATE ". TBL_STAFF ." SET title='$title', phone='$phone', office_location='$office_location', bio='$bio', start_date='$start_date' WHERE profile_id='$profile_id'") or die(error_message("The staff profile could not be updated", mysql_error(), "22b"));;

				echo '<p class="success alert">Profile update!</p>';
			}
			
		} else {
			// Print errors
			echo '<ul class="error alert"><span>Please correct the following:</span>';
			foreach($errors as $e) {
				echo "<li>".$e ."</li>";
			}
			echo '</ul>';
		}
		
	} else {
		// If all the necessary data wasn't present
		echo '<p class="error alert">There was a problem with your request. Please contact an admin.</p>';
	}
}

/** Add student profile details. */
function set_student($profile_qry, $data, $action){
	$errors = array();
	
	// Check that all the necessary data is present
	if(isset($data['student_program']) && isset($data['student_department']) && isset($data['student_comajor_department']) && isset($data['student_phone']) && isset($data['student_office_location']) && isset($data['student_home_city']) && isset($data['student_states']) && isset($data['student_countries']) && isset($data['student_title']) && isset($data['student_company']) && isset($data['student_bio']) && isset($data['student_education']) && isset($data['student_start_m']) && isset($data['student_start_y']) && isset($data['student_grad_m']) && isset($data['student_grad_y']) ) {
		
		$program_id = sanitize($data['student_program']);
		$department_id = sanitize($data['student_department']);
		$comajor_department_id = sanitize($data['student_comajor_department']);
		$phone = sanitize($data['student_phone']);
		$office_location = sanitize($data['student_office_location']);
		$home_city = sanitize($data['student_home_city']);
		$state_id = sanitize($data['student_states']);
		$country_id = sanitize($data['student_countries']);
		$title = sanitize($data['student_title']);
		$company = sanitize($data['student_company']);
		$bio = sanitize($data['student_bio']);
		$education = sanitize($data['student_education']);
		$start_date = sanitize($data['student_start_y']) . "-" . sanitize($data['student_start_m']) . "-01";
		$grad_date = sanitize($data['student_grad_y']) . "-" . sanitize($data['student_grad_m']) . "-01";
		
		$admission_status = (isset($data['student_admission_status'])) ? true : false ;
	
		// Validate the program
		$result = validate_input($program_id, "Program", true, "num");
		$errors = array_merge($errors, $result);
		
		// Validate the department
		$result = validate_input($department_id, "Department", true, "num");
		$errors = array_merge($errors, $result);
		
		// Validate the comajor department if it exists
		$result = validate_input($comajor_department_id, "Comajor Department", false, "num");
		$errors = array_merge($errors, $result);
		
		// Validate the phone number
		$result = validate_input($phone, "Phone", false, "phone", 20);
		$errors = array_merge($errors, $result);
		
		// Validate the office location
		$result = validate_input($office_location, "Office Location", false, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the city
		$result = validate_input($home_city, "City", false, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the state
		$result = validate_input($state_id, "State", false, "num");
		$errors = array_merge($errors, $result);
		
		// Validate the country
		$result = validate_input($country_id, "Country", true, "num");
		$errors = array_merge($errors, $result);
		
		// Validate the title
		$result = validate_input($title, "Title", false, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the company
		$result = validate_input($company, "Company", false, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the start date
		$result = validate_input($start_date, "Start Date", false, "date");
		$errors = array_merge($errors, $result);
		
		// Validate the grad date
		$result = validate_input($grad_date, "Graduation Date", false, "date");
		$errors = array_merge($errors, $result);
		
		// Check if there were errors
		if (count($errors) == 0){
		
			// Check if this is an add or update
			if($action === "add"){
				$profile_id = set_profile($profile_qry, "add");
			
				// Check if the student does not have a comajor department
				if(empty($comajor_department_id)){
					// Check if the student does not have a state
					if(empty($state_id)){
						// Create student profile without state or comajor
						$add_student = mysql_query("INSERT INTO `". TBL_STUDENT ."`  
						    (`profile_id`, `program_id`, `department_id`, `phone`, `office_location`, `title`, `company`, `home_city`, `country_id`, `education`, `bio`, `start_date`, `grad_date`, `admission_status`) 
							VALUES ('$profile_id', '$program_id', '$department_id', '$phone', '$office_location', '$title', '$company', '$home_city', '$country_id', '$education', '$bio', '$start_date', '$grad_date', '$admission_status')") 
							or die(error_message("The profile could not be created", mysql_error(), 28));
					} else {
						// Create student profile without comajor
						$add_student = mysql_query("INSERT INTO `". TBL_STUDENT ."`  
						    (`profile_id`, `program_id`, `department_id`, `phone`, `office_location`, `title`, `company`, `home_city`, `state_id`, `country_id`, `education`, `bio`, `start_date`, `grad_date`, `admission_status`) 
							VALUES ('$profile_id', '$program_id', '$department_id', '$phone', '$office_location', '$title', '$company', '$home_city', '$state_id', '$country_id', '$education', '$bio', '$start_date', '$grad_date', '$admission_status')") 
							or die(error_message("The profile could not be created", mysql_error(), 28));
					}
				
				} else {
					// Check if the student does not have a state
					if(empty($state_id)){
						// Create student profile without state or comajor
						$add_student = mysql_query("INSERT INTO `". TBL_STUDENT ."`  
						    (`profile_id`, `program_id`, `department_id`, `comajor_department_id`, `phone`, `office_location`, `title`, `company`, `home_city`, `country_id`, `education`, `bio`, `start_date`, `grad_date`, `admission_status`) 
							VALUES ('$profile_id', '$program_id', '$department_id', '$comajor_department_id', '$phone', '$office_location', '$title', '$company', '$home_city', '$country_id', '$education', '$bio', '$start_date', '$grad_date', '$admission_status')") 
							or die(error_message("The profile could not be created", mysql_error(), 28));
					} else {
						// Create student profile without comajor
						$add_student = mysql_query("INSERT INTO `". TBL_STUDENT ."`  
						    (`profile_id`, `program_id`, `department_id`, `comajor_department_id`, `phone`, `office_location`, `title`, `company`, `home_city`, `state_id`, `country_id`, `education`, `bio`, `start_date`, `grad_date`, `admission_status`) 
							VALUES ('$profile_id', '$program_id', '$department_id', '$comajor_department_id', '$phone', '$office_location', '$title', '$company', '$home_city', '$state_id', '$country_id', '$education', '$bio', '$start_date', '$grad_date', '$admission_status')") 
							or die(error_message("The profile could not be created", mysql_error(), 28));
					}
				}
			
			
				if($data["enabled"]){
					echo '<p class="success alert">Profile created!</p>';
				} else {
					echo '<p class="success alert">Your profile was submitted. An admin will review it and it will be up on the site shortly.</p>';
				}
			 } elseif ($action === "update"){

					set_profile($profile_qry, "update");
				$profile_id = $data["id"];

					$update_student = mysql_query("UPDATE ". TBL_STUDENT ." SET program_id='$program_id', department_id='$department_id', phone='$phone', office_location='$office_location', title='$title', company='$company', home_city='$home_city', country_id='$country_id', education='$education', bio='$bio', start_date='$start_date', grad_date='$grad_date', admission_status='$admission_status' WHERE profile_id='$profile_id'") or die(error_message("The student profile could not be updated", mysql_error(), "22b"));;
					
					if(!empty($comajor_department_id)){
						$update_student = mysql_query("UPDATE ". TBL_STUDENT ." SET comajor_department_id='$comajor_department_id' WHERE profile_id='$profile_id'") or die(error_message("The student profile could not be updated", mysql_error(), "22b"));;
					}
					
					if(!empty($state_id)){
						$update_student = mysql_query("UPDATE ". TBL_STUDENT ." SET state_id='$state_id' WHERE profile_id='$profile_id'") or die(error_message("The student profile could not be updated", mysql_error(), "22b"));;
					}
					
					echo '<p class="success alert">Profile updated!</p>';
					
			}
		} else {
			// Print errors
			echo '<ul class="error alert"><span>Please correct the following:</span>';
			foreach($errors as $e) {
				echo "<li>".$e ."</li>";
			}
			echo '</ul>';
		}
		
	} else {
		// If all the necessary data wasn't present
		echo '<p class="error alert">There was a problem with your request. Please contact an admin.</p>';
	}
}

/** Add alumni profile details. */
function set_alumni($profile_qry,  $data, $action){
	$errors = array();
	
	// Check that all the necessary data is present
	if(isset($data['alumni_program']) && isset($data['alumni_department']) && isset($data['alumni_comajor_department']) && isset($data['alumni_dissertation_title']) && isset($data['alumni_company_city']) && isset($data['alumni_states']) && isset($data['alumni_countries']) && isset($data['alumni_title']) && isset($data['alumni_company']) && isset($data['alumni_bio']) && isset($data['alumni_education']) && isset($data['alumni_start_m']) && isset($data['alumni_start_y']) && isset($data['alumni_grad_m']) && isset($data['alumni_grad_y']) ) {
		
		$program_id = sanitize($data['alumni_program']);
		$department_id = sanitize($data['alumni_department']);
		$comajor_department_id = sanitize($data['alumni_comajor_department']);
		$dissertation_title = sanitize($data['alumni_dissertation_title']);
		$company_city = sanitize($data['alumni_company_city']);
		$state_id = sanitize($data['alumni_states']);
		$country_id = sanitize($data['alumni_countries']);
		$title = sanitize($data['alumni_title']);
		$company = sanitize($data['alumni_company']);
		$bio = sanitize($data['alumni_bio']);
		$education = sanitize($data['alumni_education']);
		$start_date = sanitize($data['alumni_start_y']) . "-" . sanitize($data['alumni_start_m']) . "-01";
		$grad_date = sanitize($data['alumni_grad_y']) . "-" . sanitize($data['alumni_grad_m']) . "-01";
		
		// Validate the program
		$result = validate_input($program_id, "Program", true, "num");
		$errors = array_merge($errors, $result);
		
		// Validate the department
		$result = validate_input($department_id, "Department", true, "num");
		$errors = array_merge($errors, $result);
		
		// Validate the comajor department if it exists
		$result = validate_input($comajor_department_id, "Comajor Department", false, "num");
		$errors = array_merge($errors, $result);
		
		// Validate the title
		$result = validate_input($dissertation_title, "Dissertation Title", false, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the city
		$result = validate_input($company_city, "City", false, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the state
		$result = validate_input($state_id, "State", false, "num");
		$errors = array_merge($errors, $result);
		
		// Validate the country
		$result = validate_input($country_id, "Country", true, "num");
		$errors = array_merge($errors, $result);
		
		// Validate the title
		$result = validate_input($title, "Title", false, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the company
		$result = validate_input($company, "Company", false, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the start date
		$result = validate_input($start_date, "Start Date", false, "date");
		$errors = array_merge($errors, $result);
		
		// Validate the grad date
		$result = validate_input($grad_date, "Graduation Date", false, "date");
		$errors = array_merge($errors, $result);
		
		// Check if there were errors
		if (count($errors) == 0){
			
			// Check if this is an add or update
			if($action === "add"){
			
				$profile_id = set_profile($profile_qry, "add");
			
				// Check if the student does not have a comajor department
				if(empty($comajor_department_id)){
					// Check if the student does not have a state
					if(empty($state_id)){
						// Create student profile without state or comajor
						$add_alumni = mysql_query("INSERT INTO `". TBL_ALUMNI ."`  
						    (`profile_id`, `program_id`, `department_id`, `dissertation_title`, `title`, `company`, `company_city`, `country_id`, `education`, `bio`, `start_date`, `grad_date`) 
							VALUES ('$profile_id', '$program_id', '$department_id', '$dissertation_title', '$title', '$company', '$company_city', '$country_id', '$education', '$bio', '$start_date', '$grad_date')") 
							or die(error_message("The profile could not be created", mysql_error(), "28a"));
					} else {
						// Create student profile without comajor
						$add_alumni = mysql_query("INSERT INTO `". TBL_ALUMNI ."`  
						    (`profile_id`, `program_id`, `department_id`, `dissertation_title`, `title`, `company`, `company_city`, `state_id`, `country_id`, `education`, `bio`, `start_date`, `grad_date`) 
							VALUES ('$profile_id', '$program_id', '$department_id', '$dissertation_title', '$title', '$company', '$company_city', '$state_id', '$country_id', '$education', '$bio', '$start_date', '$grad_date')") 
							or die(error_message("The profile could not be created", mysql_error(), "28b"));
					}
				
				} else {
					// Check if the student does not have a state
					if(empty($state_id)){
						// Create student profile without state or comajor
						$add_alumni = mysql_query("INSERT INTO `". TBL_ALUMNI ."`  
						    (`profile_id`, `program_id`, `department_id`, `comajor_department_id`, `dissertation_title`, `title`, `company`, `company_city`, `country_id`, `education`, `bio`, `start_date`, `grad_date`) 
							VALUES ('$profile_id', '$program_id', '$department_id', '$comajor_department_id', '$dissertation_title', '$title', '$company', '$company_city', '$country_id', '$education', '$bio', '$start_date', '$grad_date')") 
							or die(error_message("The profile could not be created", mysql_error(), "28c"));
					} else {
						// Create student profile without comajor
						$add_alumni = mysql_query("INSERT INTO `". TBL_ALUMNI ."`  
						    (`profile_id`, `program_id`, `department_id`, `comajor_department_id`, `dissertation_title`, `title`, `company`, `company_city`, `state_id`, `country_id`, `education`, `bio`, `start_date`, `grad_date`) 
							VALUES ('$profile_id', '$program_id', '$department_id', '$comajor_department_id', '$dissertation_title', '$title', '$company', '$company_city', '$state_id', '$country_id', '$education', '$bio', '$start_date', '$grad_date')") 
							or die(error_message("The profile could not be created", mysql_error(), "28d"));
					}
				}
				if($data["enabled"]){
					echo '<p class="success alert">Profile created!</p>';
				} else {
					echo '<p class="success alert">Your profile was submitted. An admin will review it and it will be up on the site shortly.</p>';
				}
			} elseif ($action === "update"){

				set_profile($profile_qry, "update");
				$profile_id = $data["id"];

				$update_alumni = mysql_query("UPDATE ". TBL_ALUMNI ." SET program_id='$program_id', department_id='$department_id', dissertation_title='$dissertation_title', title='$title', company='$company', company_city='$company_city', country_id='$country_id', education='$education', bio='$bio', start_date='$start_date', grad_date='$grad_date' WHERE profile_id='$profile_id'") or die(error_message("The student profile could not be updated", mysql_error(), "22b"));;

				if(!empty($comajor_department_id)){
					$update_alumni = mysql_query("UPDATE ". TBL_ALUMNI ." SET comajor_department_id='$comajor_department_id' WHERE profile_id='$profile_id'") or die(error_message("The student profile could not be updated", mysql_error(), "22b"));;
				}

				if(!empty($state_id)){
					$update_alumni = mysql_query("UPDATE ". TBL_ALUMNI ." SET state_id='$state_id' WHERE profile_id='$profile_id'") or die(error_message("The student profile could not be updated", mysql_error(), "22b"));;
				}
				
				echo '<p class="success alert">Profile updated!</p>';
			}
			
		} else {
			// Print errors
			echo '<ul class="error alert"><span>Please correct the following:</span>';
			foreach($errors as $e) {
				echo "<li>".$e ."</li>";
			}
			echo '</ul>';
		}
		
	} else {
		// If all the necessary data wasn't present
		echo '<p class="error alert">There was a problem with your request. Please contact an admin.</p>';
	}
}

/** Add visitor profile details. */
function set_visitor($profile_qry, $data, $action){
	$errors = array();
	
	// Check that all the necessary data is present
	if(isset($data['visitor_title']) && isset($data['visitor_phone']) && isset($data['visitor_office_location']) && isset($data['visitor_bio']) && isset($data['visitor_education']) && isset($data['visitor_start_m']) && isset($data['visitor_start_y']) && isset($data['visitor_department']) ) {
		$title = sanitize($data['visitor_title']);
		$department_id = sanitize($data['visitor_department']);
		$phone = sanitize($data['visitor_phone']);
		$office_location = sanitize($data['visitor_office_location']);
		$bio = sanitize($data['visitor_bio']);
		$education = sanitize($data['visitor_education']);
		$start_date = sanitize($data['visitor_start_y']) . "-" . sanitize($data['visitor_start_m']) . "-01";
	
		// Validate the title
		$result = validate_input($title, "Title", true, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the phone number
		$result = validate_input($phone, "Phone", false, "phone", 20);
		$errors = array_merge($errors, $result);
		
		// Validate the office location
		$result = validate_input($office_location, "Office", false, "name", 220);
		$errors = array_merge($errors, $result);
		
		// Validate the start date
		$result = validate_input($start_date, "Start Date", false, "date");
		$errors = array_merge($errors, $result);
		
		// Validate the department if it exists
		$result = validate_input($department_id, "Department", false, "num");
		$errors = array_merge($errors, $result);
		
		// Check if there were errors
		if (count($errors) == 0){
			
			// Check if this is an add or update
			if($action === "add"){
				
				// Create profile
				$profile_id = set_profile($profile_qry, "add");
			
				// Check if the visitor does not have a department
				if(empty($department_id)){
					// Create visitor profile
					$add_visitor = mysql_query("INSERT INTO `". TBL_VISITOR ."`  
						(`profile_id`, `title`, `phone`, `office_location`, `education`, `bio`, `start_date`) 
						VALUES ('$profile_id', '$title', '$phone', '$office_location', '$education', '$bio', '$start_date')") 
						or die(error_message("The profile for could not be created", mysql_error(), 28));
				} else {
					// Create visitor profile
					$add_visitor = mysql_query("INSERT INTO `". TBL_VISITOR ."` 
						(`profile_id`, `title`, `department_id`, `phone`, `office_location`, `education`, `bio`, `start_date`) 
						VALUES ('$profile_id', '$title', '$department_id', '$phone', '$office_location', '$education', '$bio', '$start_date')") 
						or die(error_message("The profile could not be created", mysql_error(), 29));
				}
			
				if($data["enabled"]){
					echo '<p class="success alert">Profile created!</p>';
				} else {
					echo '<p class="success alert">Your profile was submitted. An admin will review it and it will be up on the site shortly.</p>';
				}
			} elseif ($action === "update"){
				
				set_profile($profile_qry, "update");
				$profile_id = $data["id"];
				
				$update_visitor = mysql_query("UPDATE ". TBL_VISITOR ." SET title='$title', phone='$phone', office_location='$office_location', education='$education', bio='$bio', start_date='$start_date' WHERE profile_id='$profile_id'") or die(error_message("The visitor profile could not be updated", mysql_error(), "22b"));;
				
				if(!empty($department_id)){
					$update_visitor = mysql_query("UPDATE ". TBL_VISITOR ." SET department_id='$department_id' WHERE profile_id='$profile_id'") or die(error_message("The visitor profile could not be updated", mysql_error(), "22b"));;
				}
				
				echo '<p class="success alert">Profile updated!</p>';
			}	
			
		} else {
			// Print errors
			echo '<ul class="error alert"><span>Please correct the following:</span>';
			foreach($errors as $e) {
				echo "<li>".$e ."</li>";
			}
			echo '</ul>';
		}
		
	} else {
		// If all the necessary data wasn't present
		echo '<p class="error alert">There was a problem with your request. Please contact an admin.</p>';
	}
}

function set_profile($qry, $action){
	$profile_id = -1;
	if ($action === "add"){
		// Create profile
		$add_profile = mysql_query($qry) 
			or die(error_message("The profile could not be created", mysql_error(), 22));

		// Get profile id from last insert
		$profile_id = mysql_insert_id();

		// Generate a rough hash based on the user id
		$md5_id = md5($profile_id);
		$update_profile = mysql_query("UPDATE ". TBL_PROFILE ." SET md5_id='$md5_id' WHERE id='$profile_id'");
				
	} elseif ($action === "update"){
		$update_profile = mysql_query($qry)
			or die(error_message("The profile could not be updated", mysql_error(), "22b"));
		
		$profile_id = mysql_insert_id();
	}
	
	return $profile_id;
}


/* POST Action Handlers
 * =================================================================== */
if($_POST && isset($_GET['action'])) {
	if( $_GET['action'] == "add" || $_GET['action'] == "update" ) {
		$errors = array(); 
		// Check that all the necessary data is present
		if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['user_level']) && isset($_POST['position']) && isset($_POST['photo'])) {
			$username = sanitize($_POST['username']);
			$password = sanitize($_POST['password']);
			$first_name = sanitize($_POST['first_name']);
			$last_name = sanitize($_POST['last_name']);
			$photo = sanitize($_POST['photo']);
			$email = sanitize($_POST['email']);
			$user_level = sanitize($_POST['user_level']);
			$position = sanitize($_POST['position']);
			$ip_address = $_SERVER['REMOTE_ADDR'];
			$activation_code = rand(1000,9999);
			
			if(!isset($_POST['enabled'])){
				$enabled = 1;
			} else {
				$enabled = $_POST['enabled'];
			}
			
			
			if(isset($_POST['id'])){
				$id = $_POST['id'];
			}
		
			// Check if user already exists
			if($_GET['action'] == "add"){
				$check_exists = mysql_query("SELECT username, position FROM ". TBL_PROFILE ." WHERE username = '$username' AND position = '$position'") or die(error_message("Could not access database", mysql_error(),21));
	
				if(mysql_num_rows($check_exists) > 0)
				{
					$errors[] = 'A profile with that username already exists.';
				} 
			}
			
			
			// Validate the username
			$result = validate_input($username, "Username", true, "alphanum", 220, 3);
			$errors = array_merge($errors, $result);
		
			// Validate the password for all new profiles and if it's changed for the updated profile
			if($_GET['action'] == "add" || ($_GET['action'] == "update" && !empty($password))) {
				$result = validate_input($password, "Password", true, "", 50, 8);
				$errors = array_merge($errors, $result);
			}
		
			// Validate the first name
			$result = validate_input($first_name, "First Name", true, "name", 220, 2);
			$errors = array_merge($errors, $result);
		
			// Validate the last name
			$result = validate_input($last_name, "Last Name", true, "name", 220, 2);
			$errors = array_merge($errors, $result);
		
			// Validate the photo
			$result = validate_input($photo, "Photo", false, "url", 220);
			$errors = array_merge($errors, $result);
		
			// Validate the email
			$result = validate_input($email, "Email", true, "email");
			$errors = array_merge($errors, $result);
		
			// Validate the user_level
			$result = validate_input($user_level, "Privilege Level", true, "num");
			$errors = array_merge($errors, $result);
		
			// Validate the position
			$result = validate_input($position, "Position", true, "position");
			$errors = array_merge($errors, $result);

			// Check if there were errors
			if (count($errors) == 0){
				if ($_GET['action'] == "add"){
					// Hash the password
					$password = hash_pass($password);
				
					// Create profile query
					$add_profile_qry = "INSERT INTO ". TBL_PROFILE ." 
						(`username`, `password`, `email`, `first_name`, `last_name`,  `user_level`, `photo`, `enabled`, `position`, `date_created`, `ip_address`, `activation_code`) 
						VALUES ('$username', '$password', AES_ENCRYPT('$email', '". SALT ."'), '$first_name', '$last_name', '$user_level', '$photo', '$enabled', '$position', now(), '$ip_address', '$activation_code')"; 
				
				
				
					// Add corresponding details based on position.
					switch ($position) {
					    case 'faculty':
					        set_faculty($add_profile_qry, $_POST, "add");
					        break;
					    case 'student':
							set_student($add_profile_qry, $_POST, "add");
					        break;
					    case 'staff':
					        set_staff($add_profile_qry, $_POST, "add");
					        break;
						case 'alumni':
					        set_alumni($add_profile_qry, $_POST, "add");
					        break;
						case 'visitor':
					        set_visitor($add_profile_qry, $_POST, "add");
					        break;
					}
				
				} elseif ($_GET['action'] == "update"){
					
					
					// Update profile
					$update_profile_qry = "UPDATE ". TBL_PROFILE ." SET username='$username', email=AES_ENCRYPT('$email', '". SALT ."'), first_name='$first_name', last_name='$last_name', user_level='$user_level', photo='$photo '";
					
					// Check if password has changed
					if(!empty($password)){
						$password = hash_pass($password);
						$update_profile_qry .= ", password='$password' ";
					}
					
					$update_profile_qry .= "WHERE id='$id'"; 
					
					// Update corresponding details based on position.
					switch ($position) {
					    case 'faculty':
					        set_faculty($update_profile_qry, $_POST, "update");
					        break;
					    case 'student':
							set_student($update_profile_qry, $_POST, "update");
					        break;
					    case 'staff':
					        set_staff($update_profile_qry, $_POST, "update");
					        break;
						case 'alumni':
					        set_alumni($update_profile_qry, $_POST, "update");
					        break;
						case 'visitor':
					        set_visitor($update_profile_qry, $_POST, "update");
					        break;
					}
				}
				
			
			} else {
				// Print errors
				echo '<ul class="error alert"><span>Please correct the following:</span>';
				foreach($errors as $e) {
					echo "<li>".$e ."</li>";
				}
				echo '</ul>';
			}
		} else {
			// If all the necessary data wasn't present
			if($debug){ 
				print_r($_POST);
			}
			echo '<p class="error alert">There was a problem with your request. Please contact an admin.</p>';

		}
	} elseif(isset($_GET['action']) && $_GET['action'] == "update_departments") {
		// Check that all the necessary data is present
		if(isset($_POST['college_id']) && isset($_POST['position'])) {
			echo get_department_dropdown($_POST['college_id'], $_POST['position']);
		} else {
			// If all the necessary data wasn't present
			echo '<p class="error alert">There was a problem with your request. Please contact an admin.</p>';
		}
		
	}
}

if($_POST && $_GET['action'] == "alert"){
	$errors = array();
	
	$from_name = sanitize($_POST['from_name']);
	$from_email = sanitize($_POST['from_email']);
	$to_email = sanitize($_POST['to_email']);
	$content = sanitize($_POST['content']);
	
	$result = validate_input($from_name, "Your Name", true, "name");
	$errors = array_merge($errors, $result);
	
	$result = validate_input($from_email, "Your Email", true, "email");
	$errors = array_merge($errors, $result);
	
	$result = validate_input($to_email, "Profile Email", true, "email");
	$errors = array_merge($errors, $result);
	
	$result = validate_input($content, "Your Message", true, "");
	$errors = array_merge($errors, $result);
	
	// Check if there were errors
	if (count($errors) == 0){
		
		//Build the message
		$subject = "New Message from " . $from_name;

		$message = "<p>You have a new message from ". $from_name ." (<a href=\"mailto:".$from_email."\">". $from_email ."</a>):</p>";
		$message .= "<hr />". $content;
		$message .= "<hr /><p><small>Sent to ".$to_email. " from <a href=\"".BASE."\">".BASE."</a></small></p>";

		// Send message to GLOBAL_EMAIL for debugging purposes even though this would
		// end up being sent to $to_email
		send_msg(GLOBAL_EMAIL, $subject, $message);
		
		echo '<p class="success alert">Message sent!</p>';
		
	} else {
		// Print errors
		echo '<ul class="error alert"><span>Please correct the following:</span>';
		foreach($errors as $e) {
			echo "<li>".$e ."</li>";
		}
		echo '</ul>';
	}
}

if($_GET['action'] == "get" ){
	//TODO: Add filters & search
	
	$profiles = mysql_query("SELECT *, AES_DECRYPT(email, '". SALT ."') AS user_email FROM ".TBL_PROFILE ." ORDER BY last_name ASC");
	
	if(mysql_num_rows($profiles) == 0)
	{
		echo "<p class=\"alert
 no-data\">Sorry, there are no people in this directory.</p>";
	}
	else
	{ 
		// echo "<pre>";
		// 		print_r($staff);
		// 		echo "</pre>";
		
		// Build profile entry
		while($row = mysql_fetch_assoc($profiles))
		{
			
			// Only display enabled profile
			if($row['enabled']){
				
				echo '<div class="profile" id="profile-'. $row['id'] .'">';
				
				// Set details based on position
				$row_details = NULL;
				switch ($row['position']) {
					case "student":
						$detail_qry = mysql_query("SELECT * FROM ". TBL_STUDENT ." WHERE profile_id=".$row['id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
						$row_details = mysql_fetch_assoc($detail_qry);
						break;
					case "alumni":
						$detail_qry = mysql_query("SELECT * FROM ". TBL_ALUMNI ." WHERE profile_id=".$row['id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
						$row_details = mysql_fetch_assoc($detail_qry);
						break;
					case "faculty":
						$detail_qry = mysql_query("SELECT * FROM ". TBL_FACULTY ." WHERE profile_id=".$row['id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
						$row_details = mysql_fetch_assoc($detail_qry);
						break;
					case "staff":
						$detail_qry = mysql_query("SELECT * FROM ". TBL_STAFF ." WHERE profile_id=".$row['id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
						$row_details = mysql_fetch_assoc($detail_qry);
						break;
					case "visitor":
						$detail_qry = mysql_query("SELECT * FROM ". TBL_VISITOR ." WHERE profile_id=".$row['id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
						$row_details = mysql_fetch_assoc($detail_qry);
						break;
				}	
				
				// Check if there is a photo
				if(empty($row['photo'])){
					// Add empty photo
					echo '<img src="'. BASE .'/images/clear.gif" class="blank"/> ';
				} else {
					// Add profile photo
					echo '<img src="'. $row['photo'] .'"/>';
				}
			
				echo '<div class="profile-content">';
			
				// Add First and Last Name
				echo '<h3>'.$row['first_name'].' '.$row['last_name'].'</h3>';
			
				// Add Title
				if(!empty($row_details)){
					switch ($row['position']) {
						case "student":
							// Add program title & role (i.e. Undergraduate Researcher)
							$program_qry = mysql_query("SELECT name, role FROM ". TBL_PROGRAM ." WHERE id=".$row_details['program_id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
							$program = mysql_fetch_assoc($program_qry);
							echo '<h4>'. ucfirst($program['name']).' '. ucfirst($program['role']);
							
							// Add department
							$department_qry = mysql_query("SELECT name FROM ". TBL_DEPARTMENT ." WHERE id=".$row_details['department_id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
							$department = mysql_fetch_assoc($department_qry);
							
							echo ', '. ucfirst($department['name']);
							
							// Add comajor (if it exists)
							if(!empty($row_details['comajor_department_id'])){
								echo '<br/>';
								$department_qry = mysql_query("SELECT name FROM ". TBL_DEPARTMENT ." WHERE id=".$row_details['comajor_department_id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
								$department = mysql_fetch_assoc($department_qry);
								
								// TODO: Make different degrees possible with comajor

								echo ucfirst($program['name']).' '. ucfirst($program['role']) . ', '. ucfirst($department['name']);
							}
							
							// Add title and company details (if it exists)
							if(!empty($row_details['title']) && !empty($row_details['company'])){
								echo '<br/>';
								
								echo ucfirst(html_entity_decode($row_details['title'])).', '. ucfirst(html_entity_decode($row_details['company']));
								
								// Add city, state, country details (if they exist)
								if(!empty($row_details['home_city'])){
									echo ' (' . ucfirst($row_details['home_city']);
									if(!empty($row_details['state_id'])){
										$state_qry = mysql_query("SELECT name FROM ". TBL_US_STATE ." WHERE id=".$row_details['state_id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
										$state = mysql_fetch_assoc($state_qry);
										
										echo ', '. ucfirst($state['name']);
									} elseif (!empty($row_details['country_id'])){
										$country_qry = mysql_query("SELECT name FROM ". TBL_COUNTRY ." WHERE id=".$row_details['country_id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
										$country = mysql_fetch_assoc($country_qry);
										
										echo ', '. ucfirst($country['name']);
									}
									echo ')';
								}
							}
							 
							echo '</h4>';
							break;
						case "alumni":
							echo '<h4>';
							// Add title and company details (if it exists)
							if(!empty($row_details['title']) && !empty($row_details['company'])){
								echo ucfirst(html_entity_decode($row_details['title'])).', '. ucfirst(html_entity_decode($row_details['company']));
								
								// Add city, state, country details (if they exist)
								if(!empty($row_details['company_city'])){
									echo ' (' . ucfirst(html_entity_decode($row_details['company_city']));
									if(!empty($row_details['state_id'])){
										$state_qry = mysql_query("SELECT name FROM ". TBL_US_STATE ." WHERE id=".$row_details['state_id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
										$state = mysql_fetch_assoc($state_qry);
										
										echo ', '. ucfirst($state['name']);
									} elseif (!empty($row_details['country_id'])){
										$country_qry = mysql_query("SELECT name FROM ". TBL_COUNTRY ." WHERE id=".$row_details['country_id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
										$country = mysql_fetch_assoc($country_qry);
										
										echo ', '. ucfirst($country['name']);
									}
									echo ')';
								}
								echo '<br/>';
							}
							
							// Add program title & role (i.e. Undergraduate Researcher)
							$program_qry = mysql_query("SELECT name, abbreviation FROM ". TBL_PROGRAM ." WHERE id=".$row_details['program_id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
							$program = mysql_fetch_assoc($program_qry);
							
							echo "Alum, ";
							
							if(!empty($program['abbreviation'])){
								echo ucfirst($program['abbreviation']).', ';
							} else {
								echo ucfirst($program['name']).', ';
							}
							
							// Add department
							$department_qry = mysql_query("SELECT name FROM ". TBL_DEPARTMENT ." WHERE id=".$row_details['department_id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
							$department = mysql_fetch_assoc($department_qry);
							
							echo ucfirst($department['name']);
							
							// Add graduation term and year
							echo ' (';
							$grad_date = explode("-", $row_details['grad_date']);
							
							// Get grad term
							echo month_to_season($grad_date[1]);
							
							echo $grad_date[0] . ')';
							 
							echo '</h4>';
							break;
						case "faculty":
							echo '<h4>';
							// Add title
							echo ucfirst(html_entity_decode($row_details['title'])).', ';
								
							// Add department
							$department_qry = mysql_query("SELECT name FROM ". TBL_DEPARTMENT ." WHERE id=".$row_details['department_id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
							$department = mysql_fetch_assoc($department_qry);
							
							echo ucfirst($department['name']);
							
							echo '</h4>';
							break;
						case "staff":
							// Add title
							echo '<h4>'. ucfirst(html_entity_decode($row_details['title'])) .'</h4>';
							break;
						case "visitor":
							// Add title
							echo '<h4>'. ucfirst(html_entity_decode($row_details['title']));
								
							// Add department (if one exists)
							if(!empty($row_details['department_id'])){
								$department_qry = mysql_query("SELECT name FROM ". TBL_DEPARTMENT ." WHERE id=".$row_details['department_id']. " LIMIT 1") or die(error_message("Could not access database", mysql_error(),21));
								$department = mysql_fetch_assoc($department_qry);
							
								echo ', '. ucfirst($department['name']);
							}
							echo '</h4>';
							break;
					}
				}
				
				
				// Add Contact Information
				echo '<span class="contact-info"><h6>Email:</h6> <a href="mailto:'. $row['user_email'] . '">'. $row['user_email'] .'</a></span>';
				if(!empty($row_details['phone'])){
					echo '<span class="contact-info"><h6>Phone:</h6> <a href="tel:'. $row_details['phone'] . '" class="phone">'. $row_details['phone'] .'</a></span>';
				}
				if(!empty($row_details['office_location'])){
					echo '<span class="contact-info"><h6>Office:</h6> '. $row_details['office_location'].'</span>';
				}
				
				// Add collapsable details
				//echo '<a href="#" class="button" id="'.$row['id'].'">+ more</a>'; 
				echo '<div class="expand-profile" id="expand-profile-'. $row['id'] .'">';
				
				// TODO: Related People
				
				// Add Expected Graduation (for students)
				if($row['position']=== 'student' && !empty($row_details['grad_date'])){
					$grad_date = explode("-", $row_details['grad_date']);
					
					echo '<h6>Expected Graduation:</h6><p>';
					echo month_to_season($grad_date[1]);
					echo $grad_date[0].'</p>';
				}
				
				// Add Expected Graduation (for students)
				if($row['position']=== 'alumni' && !empty($row_details['dissertation_title'])){
					echo '<h6>Dissertation Title:</h6><p>'. html_entity_decode($row_details['dissertation_title']) . '</p>';
				}
				
				// Add Education
				if(!empty($row_details['education'])){
					echo '<h6>Education:</h6> <p class="profile-education">'. html_entity_decode(nl2br($row_details['education'])) .'</p>';
				}
				
				// Add Bio
				if(!empty($row_details['bio'])){
					echo '<p class="profile-bio">'. html_entity_decode(nl2br($row_details['bio'])) .'</p>';
				}
				
				// TODO: Interests
				// TODO: Groups
				// TODO: Links
				
				echo '<small class="update">Last updated <span>'. contextualTime(strtotime($row_details['last_update'])) .'</span>.</small>';	
				
				echo '</div></div></div>';
			}
		}
		//echo "</tbody></table>";
	}
	
}
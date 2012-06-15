<?php
/**
 * Profile Setter for Tassel.
 * 
 * This file provides an interface between the forms and 
 * the database. Includes adding new profiles and updating existing.
 *
 * Error codes ending with sp. 
 *
 * @author Hannah Deering
 * @package Tassel
 **/

require_once ("constants.php");
require_once ("dbconnect.php"); //Includes database connection details
require_once ("functions.php"); //Includes functions
require_once ("validation_functions.php"); //Includes data sanitation & validation functions

if($_POST && isset($_GET['action']) && ( $_GET['action'] == "add" || $_GET['action'] == "update" )) {
	$errors = array(); 

	// Get & sanitize inputs
	$id 				= (!isset($_POST['id']))				? ""		: sanitize($_POST['id']);
	$name 				= (!isset($_POST['name']))				? " "		: sanitize($_POST['name']);
	$email 				= (!isset($_POST['email']))				? ""		: sanitize($_POST['email']);
	$phone 				= (!isset($_POST['phone']))				? ""		: sanitize($_POST['phone']);
	$office_location 	= (!isset($_POST['office_location']))	? ""		: sanitize($_POST['office_location']);
	$photo 				= (!isset($_POST['photo']))				? ""		: sanitize($_POST['photo']);
	$position			= (!isset($_POST['position']))			? "unknown"	: sanitize($_POST['position']);
	$program 			= (!isset($_POST['program']))			? ""		: sanitize($_POST['program']);
	$department			= (!isset($_POST['department'])) 		? ""		: sanitize($_POST['department']);
	$comajor			= (!isset($_POST['comajor']))			? false		: sanitize($_POST['comajor']);
	$comajor_program	= (!isset($_POST['comajor_program']))	? ""		: sanitize($_POST['comajor_program']);
	$comajor_department	= (!isset($_POST['comajor_department'])) ? ""		: sanitize($_POST['comajor_department']);
	$dissertation 		= (!isset($_POST['dissertation']))		? ""		: sanitize($_POST['dissertation']);
	$title 				= (!isset($_POST['title']))				? ""		: sanitize($_POST['title']);
	$company 			= (!isset($_POST['company']))			? ""		: sanitize($_POST['company']);
	$city 				= (!isset($_POST['city']))				? ""		: sanitize($_POST['city']);
	$state 				= (!isset($_POST['state']))				? ""		: sanitize($_POST['state']);
	$country			= (!isset($_POST['country']))			? ""		: sanitize($_POST['country']);
	$start_m			= (!isset($_POST['start_m']))			? ""		: sanitize($_POST['start_m']);
	$start_y			= (!isset($_POST['start_y']))			? ""		: sanitize($_POST['start_y']);
	$grad_m				= (!isset($_POST['grad_m']))			? ""		: sanitize($_POST['grad_m']);
	$grad_y				= (!isset($_POST['grad_y']))			? ""		: sanitize($_POST['grad_y']);
	$admission_status	= (!isset($_POST['admission_status']))	? ""		: sanitize($_POST['admission_status']);
	$education			= (!isset($_POST['education']))			? ""		: sanitize($_POST['education']);
	$bio 				= (!isset($_POST['bio']))				? ""		: sanitize($_POST['bio']);
	$interests 			= (!isset($_POST['interest']))			? array()	: sanitize($_POST['interest']);
	$enabled			= (!isset($_POST['enabled']))			? 1			: sanitize($_POST['enabled']);
	$user_level			= (!isset($_POST['user_level'])) 		? 1			: sanitize($_POST['user_level']);
	$ip_address 		= "";
	$activation_code 	= rand(1000,9999);
	
	// Extract first & last name
	$name_arr = explode(" ", $name);
	if(count($name_arr) >= 2){
		$last_name = $name_arr[count($name_arr)-1]; 
		unset($name_arr[count($name_arr)-1]);
		$first_name = "";
		foreach ($name_arr as $n) {
			if(!empty($first_name)) {
				$first_name .= " ";
			}
			$first_name .= $n;
		}
		
		// Validate names
		$result = validate_input($first_name, "first name", true, "name");
		$errors = array_merge($errors, $result);

		$result = validate_input($last_name, "last name", true, "name");
		$errors = array_merge($errors, $result);
		
	} else{
		$errors[] = 'A first and last name is required.';
	}

	// Check comajor 
	if($comajor){
		if($comajor === "yes"){
			$comajor = true;
		} else{
			$comajor = false;
		}
	}
	
	// Check admission status 
	if($admission_status){
		if($admission_status === "yes"){
			$admission_status = 1;
		} else{
			$admission_status = 0;
		}
	}
	
	// Validate id
	$result = validate_input($id, "id", false, "num");
	$errors = array_merge($errors, $result);
	
	// Validate position
	$result = validate_input($position, "position", true, "position");
	$errors = array_merge($errors, $result);
	
	// Validate start date
	$result = validate_input($start_m, "start month", true, "num");
	$errors = array_merge($errors, $result);
	
	$result = validate_input($start_y, "start year", true, "num");
	$errors = array_merge($errors, $result);
	
	// Validate email
	$result = validate_input($email, "email", true, "email");
	$errors = array_merge($errors, $result);

	// Validate phone
	$result = validate_input($phone, "phone", false, "phone");
	$errors = array_merge($errors, $result);
	
	// Validate office location
	$result = validate_input($office_location , "office location ", false, "name");
	$errors = array_merge($errors, $result);
	
	// Validate photo
	$result = validate_input($photo , "photo", false, "url");
	$errors = array_merge($errors, $result);
	
	// Validate program
	$result = validate_input($program, "program", false, "num");
	$errors = array_merge($errors, $result);
	
	// Validate department
	$result = validate_input($department, "department", false, "num");
	$errors = array_merge($errors, $result);
	
	// Validate comajor department
	$result = validate_input($comajor_department, "comajor department", $comajor, "num");
	$errors = array_merge($errors, $result);
	
	// Validate comajor program
	$result = validate_input($comajor_program, "comajor program", $comajor, "num");
	$errors = array_merge($errors, $result);
	
	// Validate dissertation
	$result = validate_input($dissertation, "dissertation", false, "name");
	$errors = array_merge($errors, $result);
	
	// Validate title
	$result = validate_input($title, "title", false, "name");
	$errors = array_merge($errors, $result);
	
	// Validate company
	$result = validate_input($company, "company", false, "name");
	$errors = array_merge($errors, $result);
	
	// Validate city
	$result = validate_input($city, "city", false, "name");
	$errors = array_merge($errors, $result);
	
	// Validate state
	$result = validate_input($state, "state", false, "num");
	$errors = array_merge($errors, $result);
	
	// Validate country
	$result = validate_input($country, "country", false, "num");
	$errors = array_merge($errors, $result);
	
	// Validate graduation date
	$result = validate_input($grad_m, "graduation month", false, "num");
	$errors = array_merge($errors, $result);

	$result = validate_input($grad_y, "graduation year", false, "num");
	$errors = array_merge($errors, $result);
	
	// Validate interests
	$result = validate_input($interests, "interests", false, "name");
	$errors = array_merge($errors, $result);
	
	// Validate enabled
	$result = validate_input($enabled, "enabled", false, "num");
	$errors = array_merge($errors, $result);
	
	// Validate user level
	$result = validate_input($user_level, "user level", false, "num");
	$errors = array_merge($errors, $result);	
	
	// Validate interests
	$result = validate_input($interests, "interests", false, "num");
	$errors = array_merge($errors, $result);
	
	// Format inputs
	$start_date = $start_y. "-" . $start_m . "-01";
	$grad_date = $grad_y. "-" . $grad_m . "-01";
	$phone = formatPhone($phone);
	
	// Check for required fields and ensure fields are empty that should be
	switch ($position) {
	    case 'faculty':
			if(empty($department)) {
				$errors[] = 'A department is required.';
			}
			if(empty($title)) {
				$errors[] = 'A title is required.';
			}
			
			$program = "";
			$comajor_program = "";
			$comajor_department = "";
			$company = "";
			$city = "";
			$state = "";
			$country = "";
			$dissertation = "";
			$grad_date = "";
			
	        break;
	    case 'student':
			if(empty($program)) {
				$errors[] = 'A program is required.';
			}
			if(empty($department)) {
				$errors[] = 'A department is required.';
			}
			
			$dissertation = "";
	        break;
	    case 'staff':
			if(empty($title)) {
				$errors[] = 'A title is required.';
			}
			
			$program = "";
			$department = "";
			$comajor_program = "";
			$comajor_department = "";
			$company = "";
			$city = "";
			$state = "";
			$country = "";
			$dissertation = "";
			$grad_date = "";
			$admission_status = "";
	        break;
		case 'alumni':
	        if(empty($program)) {
				$errors[] = 'A program is required.';
			}
			if(empty($department)) {
				$errors[] = 'A department is required.';
			}
			if(empty($title)) {
				$errors[] = 'A title is required.';
			}
			if(empty($dissertation)) {
				$errors[] = 'A dissertation title is required.';
				$admission_status = "";
			}
			$admission_status = "";
	        break;
		case 'visitor':
			if(empty($title)) {
				$errors[] = 'A title is required.';
			}
			
			$program = "";
			$comajor_program = "";
			$comajor_department = "";
			$company = "";
			$dissertation = "";
			$grad_date = "";
			$admission_status = "";
	        break;
	}
	
	// Prepare inputs for query string
	$program 			= ($program == "")				? "NULL"	: "'".$program."'";           	
	$department			= ($department == "") 			? "NULL"	: "'".$department."'";        	
	$comajor_program	= ($comajor_program == "")		? "NULL"	: "'".$comajor_program."'";   	
	$comajor_department	= ($comajor_department == "") 	? "NULL"	: "'".$comajor_department."'";	
	$dissertation 		= ($dissertation == "")			? "NULL"	: "'".$dissertation."'";
	$title 				= ($title == "")				? "NULL"	: "'".$title."'";            
	$company 			= ($company == "")				? "NULL"	: "'".$company."'";           	
	$city 				= ($city == "")					? "NULL"	: "'".$city."'";             
	$state 				= ($state == "")				? "NULL"	: "'".$state."'";            
	$country			= ($country == "")				? "NULL"	: "'".$country."'";           	
	$grad_date			= ($grad_date == "")			? "NULL"	: "'".$grad_date."'";            	
	$admission_status	= ($admission_status == "")		? "NULL"	: "'".$admission_status."'"; 

	 if(empty($errors)){
		if ($_GET['action'] == "add"){
			// Insert new profile into database
			$add_profile = mysql_query("INSERT INTO `". TBL_PROFILE ."` 
			 	(`en_email`, `enabled`, `user_level`, `date_created`) 
				VALUES (AES_ENCRYPT('$email', '". SALT ."'), '$enabled', '$user_level', now())") 
				or die(error_message("The profile could not be created", mysql_error(), "1sp"));
	
			// Get profile id from last insert
			$id = mysql_insert_id();
    
			// Generate a hashed id based on the user id
			$md5_id = md5($profile_id);
			$update_profile = mysql_query("UPDATE `". TBL_PROFILE ."` SET `md5_id`='$md5_id' WHERE `id`='$id'");
	 	
			// Insert new details into database
			$add_details = mysql_query("INSERT INTO `". TBL_DETAILS ."` 
				(`profile_id`, `first_name`, `last_name`, `photo`, `position`, `program_id`, `department_id`, `comajor_program_id`, `comajor_department_id`, `en_email`, `phone`, `office_location`, `title`, `company`, `city`, `state_id`, `country_id`, `dissertation_title`, `education`, `bio`, `start_date`, `grad_date`, `admission_status`) 
				VALUES ('$id', '$first_name', '$last_name', '$photo', '$position', $program, $department, $comajor_program, $comajor_department, AES_ENCRYPT('$email', '". SALT ."'), '$phone', '$office_location', $title, $company, $city, $state, $country, $dissertation, '$education', '$bio', '$start_date', $grad_date, $admission_status)") 
				or die(error_message("The details could not be created", mysql_error(), "1sp"));
	 	
			// Check if details were added successfully
			if(!$add_details){
				// Delete the profile if the details weren't added successfully
				$delete = mysql_query("DELETE FROM ".TBL_PROFILE." WHERE `id` = '". $id ."' LIMIT 1");
			}
		} elseif($_GET['action'] == "update" && !USER_TEST){
			// Create update query
			$update_profile_qry = "UPDATE `". TBL_PROFILE ."` SET
				`en_email`=AES_ENCRYPT('$email', '". SALT ."'),
				`enabled`='$enabled',
				`user_level`='$user_level'";
			
			// Check if password has changed
			if(!empty($password)){
				$password = hash_pass($password);
				$update_profile_qry .= ", `password`='$password'";
			}
			
			$update_profile_qry .= " WHERE `id`='$id'";
			
			// Update profile
			$update_profile = mysql_query($update_profile_qry) 
				or die(error_message("The profile could not be updated", mysql_error(), "3sp"));
			
			// Update details 
			$update_details = mysql_query("UPDATE `". TBL_DETAILS ."` SET 
				`first_name`='$first_name', 
				`last_name`='$last_name', 
				`photo`='$photo', 
				`position`='$position',
				`program_id`=$program,
				`department_id`=$department, 
				`comajor_program_id`=$comajor_program, 
				`comajor_department_id`=$comajor_department, 
				`en_email`=AES_ENCRYPT('$email', '". SALT ."'), 
				`phone`='$phone',
				`office_location`='$office_location',  
				`title`=$title,
				`company`=$company, 
				`city`=$city, 
				`state_id`=$state, 
				`country_id`=$country, 
				`dissertation_title`=$dissertation, 
				`education`='$education', 
				`bio`='$bio', 
				`start_date`='$start_date', 
				`grad_date`=$grad_date,
				`admission_status`=$admission_status
				WHERE `profile_id`='$id'") 
				or die(error_message("The details could not be updated", mysql_error(), "4sp"));			
		}
		// Link interests to this profile
		if(!empty($interests)) {
			if($_GET['action'] === "update" && !USER_TEST){
				$del_interest = mysql_query("DELETE FROM ". TBL_PROFILE_INTEREST_MAP ." WHERE `profile_id`=".$id) 
					or die(error_message("Interests could not be updated", mysql_error(), "5sp"));
			}
			foreach ($interests as $interest_id) {
				$add_interest = mysql_query("INSERT INTO `". TBL_PROFILE_INTEREST_MAP ."` 
		       		(`profile_id`, `interest_id`) 
					VALUES ('$id', '$interest_id')") 
					or die(error_message("Interest could not be updated", mysql_error(), "6sp"));
			}
		}
	} else{
	
		// Print errors to string
		$error_string = '<ul class="error alert"><span>Please correct the following:</span>';
		foreach($errors as $e) {
			$error_string .= "<li>".$e ."</li>";
		}
		$error_string .= '</ul>';

		echo $error_string;
	}
}
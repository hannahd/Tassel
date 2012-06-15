<?php
	/**
	 * Generates HTML forms from the Tassel database.
	 *
	 * Error suffix 'cf'
	 *
	 * @author Hannah Deering
	 * @package Tassel
	 **/
	
	include_once "validation_functions.php";
	$max_char_length = 39;
	
	/** Creates dropdown and checkbox controls based on array of values. */
	function create_control($values, $type, $name, $selected="", $class=""){
		global $max_char_length;
		$control = "";
		
		foreach ($values as $value => $val_name) {
			
			if(strlen ($val_name) > $max_char_length){
				$val_name = substr($val_name, 0, $max_char_length-3)."...";
			}
			// Check if the control is a dropdown list
			if($type === "dropdown"){
				$control .= "<option value=\"$value\" id=\"$name-$value\"";
				if($selected !== "" && $val_name === $selected) { 
					$control .= " selected=\"selected\"";
				}
				if($class !== "") { 
					$control .= " class=\"$class\"";
				}
				$control .= ">$val_name</option> ";
			} 
			// Check if the control is a checkbox 
			elseif($type === "checkbox") {
				$control .= "<label class=\"checkbox\" for=\"$name-$value\">
							<input type=\"checkbox\" name=\"$name"."[]\" value=\"$value\" id=\"$name-$value\" class=\"not-all $name\"";
				if($selected !== "" && $val_name === $selected) { 
					$control .= " checked=\"checked\"";
				}
				if($class !== "") { 
					$control .= " class=\"$class\"";
				}
				$control .= "/>$val_name</label> ";
			} 
			// Check if the control is a radio button 
			elseif($type === "radio") {
				$control .= "<label class=\"radio\" for=\"$name-$value\">
							<input type=\"radio\" name=\"$name\" value=\"$value\" id=\"$name-$value\"";
				if($selected !== "" && $val_name === $selected) { 
					$control .= " checked=\"checked\"";
				}
				if($class !== "") { 
					$control .= " class=\"$class\"";
				}
				$control .= "/>$val_name</label> ";
			}
		}
		return $control;
	}
	
	/** Creates a dropdown or checkbox control based on a range of years. */
	function year_control($type, $name, $class, $selected, $start, $end=CUR_YEAR) {
		$years = array();
		
		// Loop through years in range and create array.
		for ($i = $end; $i >= $start; $i--) {
			$years[$i] = $i;
		}
		
		// Output controls using array of years
		return create_control($years, $type, $name, $selected, $class);
	}
	
	/** Creates a dropdown or checkbox control using states database data. */
	function states_control($type, $name, $class, $selected) {
		$states = array();
		
		// Select all state rows
		$state_qry = mysql_query("SELECT `id`, `name` FROM `". TBL_US_STATE ."`") or die(error_message("Couldn't find US states", mysql_error(), "1cf"));
		
		// Check that states were found.
		if(mysql_num_rows($state_qry) > 0) {
			// Loop through states and create array.
			while($row = mysql_fetch_array($state_qry)) {
				$states[$row['id']] = $row['name'];
			}
		}
		// Output controls using array of states
		return create_control($states, $type, $name, $selected, $class);
	}
	
	/** Creates a dropdown or checkbox control using countries database data. */
	function country_control($type, $name, $class, $selected) {
		$countries = array();
		
		// Select all country rows
		$country_qry = mysql_query("SELECT `id`, `name` FROM `". TBL_COUNTRY ."`") or die(error_message("Couldn't find countries", mysql_error(), "2cf"));
		
		// Check that countries were found.
		if(mysql_num_rows($country_qry) > 0) {
			// Loop through countries and create array.
			while($row = mysql_fetch_array($country_qry)) {
				$countries[$row['id']] = $row['name'];
			}
		}
		// Output controls using array of countries
		return create_control($countries, $type, $name, $selected, $class);
	}
	
	/** Creates a dropdown or checkbox control using programs database data. */
	function program_control($type, $name, $class, $selected) {
		$programs = array();
		
		// Select all program rows
		$program_qry = mysql_query("SELECT `id`, `name`, `online` FROM `". TBL_PROGRAM ."`") or die(error_message("Couldn't find programs", mysql_error(), "3cf"));
		
		// Check that programs were found.
		if(mysql_num_rows($program_qry) > 0) {
			// Loop through programs and create array.
			while($row = mysql_fetch_array($program_qry)) {
				if($row['online'] == 1){
					$programs[$row['id']] = "Online ". $row['name'];
				} else{
					$programs[$row['id']] = $row['name'];
				}
			}
		}
		// Output controls using array of programs
		return create_control($programs, $type, $name, $selected, $class);
	}
	
	/** Creates a dropdown or checkbox control using colleges database data. */
	function college_control($type, $name, $class, $selected) {
		$colleges = array();
		
		// Select all college rows
		$college_qry = mysql_query("SELECT `id`, `name` FROM `". TBL_COLLEGE ."`") or die(error_message("Couldn't find colleges", mysql_error(), "4cf"));
		
		// Check that colleges were found.
		if(mysql_num_rows($college_qry) > 0) {
			// Loop through colleges and create array.
			while($row = mysql_fetch_array($college_qry)) {
				$colleges[$row['id']] = $row['name'];
			}
		}
		// Output controls using array of colleges
		return create_control($colleges, $type, $name, $selected, $class);
	}
	
	/** Creates a dropdown or checkbox control using departments database data. */
	function department_control($college_id, $has_profile, $type, $name, $class, $selected) {
		$departments = array();
		
		// Validate college id
		$college_id = preg_replace("/[^0-9]/", "", $college_id);
		
		// Check if we should query all rows or just those with profile
		if($has_profile){
			// Select all department rows in the given college
			$department_qry = mysql_query("SELECT ". TBL_DEPARTMENT .".id , ". TBL_DEPARTMENT .".name 
											 FROM `". TBL_DEPARTMENT ."` 
											 JOIN `". TBL_DETAILS ."` 
											   ON  ". TBL_DEPARTMENT .".id = ". TBL_DETAILS .".department_id 
											WHERE ". TBL_DEPARTMENT .".college_id = '$college_id'") or die(error_message("Couldn't find departments", mysql_error(), "5cf"));
		} else {
			// Select all department rows in the given college
			$department_qry = mysql_query("SELECT `id`, `name` FROM `". TBL_DEPARTMENT ."` WHERE college_id = '$college_id'") or die(error_message("Couldn't find departments", mysql_error(), "6cf"));
		}
	
		// Check that departments were found.
		if(mysql_num_rows($department_qry) > 0) {
			// Loop through departments and create array.
			while($row = mysql_fetch_array($department_qry)) {
				$departments[$row['id']] = $row['name'];
			}
		}
		// Output controls using array of departments
		return create_control($departments, $type, $name, $selected, $class);
	}
	
	/** Creates a dropdown or checkbox control using departments database data. */
	function all_departments_control($has_profile, $type, $name, $class, $selected) {
		$control = "";
		
		// Select all college rows
		$college_qry = mysql_query("SELECT `id`, `name` FROM `". TBL_COLLEGE ."`") or die(error_message("Couldn't find colleges", mysql_error(), "7cf"));
		
		// Check that colleges were found.
		if(mysql_num_rows($college_qry) > 0) {
			if($type === "dropdown"){
				while($college = mysql_fetch_assoc($college_qry)){
					$departments = department_control($college['id'], $has_profile, $type, $name, $class, $selected);
					if(!empty($departments)) {
						$control .= "<optgroup label=\"".$college['name']."\">$departments</optgroup>";
					}
				}
			} elseif($type === "checkbox") {
				while($college = mysql_fetch_assoc($college_qry)){					
					// Get department control
					$departments = department_control($college['id'], $has_profile, $type, $name, $class, $selected);

					if(!empty($departments)) {
						$control .= "<h5>".$college['name']."</h5>$departments";
					}
					
				}
			} elseif($type === "radio") {
				while($college = mysql_fetch_assoc($college_qry)){
					// Get department control
					$departments = department_control($college['id'], $has_profile, $type, $name, $class, $selected);
					
					if(!empty($departments)) {
						$control .= "<fieldset class=\"radio\"> <legend>".$college['name']."</legend>$departments</fieldset>";
					}
				}
			}
		}
		
		// Output control
		return $control;
	}

	/** Creates a dropdown or checkbox control using companys database data. */
	function company_control($type, $name, $class, $selected) {
		$companys = array();
		
		// Select all company rows
		$company_qry = mysql_query("SELECT DISTINCT `company` FROM `". TBL_DETAILS ."` ORDER BY `company` ASC") or die(mysql_error(error_message("Couldn't find companies", mysql_error(), "8cf")));
		
		// Check that companys were found.
		if(mysql_num_rows($company_qry) > 0) {
			// Loop through companys and create array.
			while($row = mysql_fetch_array($company_qry)) {
				if(!empty($row['company'])){
					$companys[$row['company']] = $row['company'];
				}
			}
		}
		// Output controls using array of companys
		return create_control($companys, $type, $name, $selected, $class);
	}
	
	/** Creates a dropdown or checkbox control using interest database data. */
	function interest_control($search, $has_profile, $type, $name, $class, $selected) {
		$interests = array();
		
		$search = sanitize($search);
		$search = purge_invalid($search);
		$where_string = "";
		
		// Check if there are any search terms
		if(!empty($search)){
				$where_string = "WHERE `name` LIKE '%$search.%' ";
		}
		
		// Check if we should query all rows or just those with profile
		if($has_profile){
			$q = "SELECT ". TBL_INTEREST .".id , ". TBL_INTEREST .".name 
											 FROM `". TBL_INTEREST ."` 
											 JOIN `". TBL_PROFILE_INTEREST_MAP ."` 
											   ON  ". TBL_INTEREST .".id = ". TBL_PROFILE_INTEREST_MAP .".interest_id ";
			// Select all interest rows
			$interest_qry = mysql_query($q . $where_string . "ORDER BY `name` ASC") or die(error_message("Couldn't find interests", mysql_error(), "9cf"));
		} else {
			// Select all interest rows
			$interest_qry = mysql_query("SELECT `id`,`name` FROM `". TBL_INTEREST ."` ". $where_string. "ORDER BY `name` ASC") or die(mysql_error(error_message("Couldn't find interests", mysql_error(), "10cf")));
		}
		
		// Check that interest were found.
		if(mysql_num_rows($interest_qry) > 0) {
			
			// Loop through interests and create array.
			while($row = mysql_fetch_array($interest_qry)) {
				$interests[$row['id']] = $row['name'];
			}
		}
		
		// Output controls using array of interests
		return create_control($interests, $type, $name, $selected, $class);
	}
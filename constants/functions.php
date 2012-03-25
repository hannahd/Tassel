<?php
	/**
	 * Contains helpful functions for Tassel.
	 *
	 * @author Hannah Deering
	 * @package Tassel
	 **/
	
	/** Generates an error message based on if the development variable is turned on. */
	function error_message($plain_error, $tech_error, $error_num) {
		global $debug;
		return ($debug) ? $plain_error ." : ". $tech_error ." <small>[Tassel Error ". $error_num ."]</small>" : "<p class=\"error message\">". $plain_error .". <small>[Tassel Error ". $error_num ."]</small></p>";
	}
	
	/** Checks if a table has values */
	function check_empty($table)
	{
		$num_rows = mysql_result(mysql_query("SELECT COUNT(*) FROM `$table`"), 0); 
		return !$num_rows ? true : false ;
	}
	
	/** Converts arrays used in config file to values used in mysql calls.
	  * If use_index is true, the index will be added as the final value to the row.*/
	function array_to_mysql_vals($array, $use_index = false) {
		$retval = "";
		foreach ($array as $key => $value){
		    
			// Check if there is an imbedded array
			if (is_array($value)){
				
				if($use_index)
				{
					// Create rows based on value and array's key number.
					foreach ($value as $v){
						$retval .= "('".$v."', '".($key+1)."'),";
					}
				} else {
					// Create rows based on values in the array.
					$retval .= "(";

					foreach ($value as $v){
						$retval .= "'".$v."',";
					}
					
					// Removes final comma and adds final parenthesis
					$retval = substr($retval, 0, -1) ."),";
				}
				
			} else{
				$retval .= "('".$value."'),";
			}
		}
		// Removes the final comma
		return substr($retval, 0, -1);
	}
	
	/** Creates options for a select field from the start to the end date. */
	function get_year_options($start, $end=CUR_YEAR) {
		$year_options = "";
		for ($i = $end; $i >= $start; $i--) {
			$year_options .= '<option value="'.$i.'">'.$i.'</option>';
		}
		return $year_options;
	}
	
	/** Creates states dropdown menu from database. */
	function get_states_dropdown($category=""){
		$category_name = ($category==="") ? $category : $category."_" ;
		$category_id = str_replace("_", "-", $category_name);
		$dropdown = "";
		$states = mysql_query("SELECT * FROM `". TBL_US_STATE ."`") or die(error_message("No states found", mysql_error(), "33"));
		$num_rows = mysql_num_rows($states);
		if( $num_rows > 0 ) {
			$dropdown .= '<select name="'. $category_name .'states" class="states" id="'. $category_id .'states">';
			$dropdown .= '<option value="">--Select-- </option>';
			while($r = mysql_fetch_array($states))
			{
				$dropdown .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
			}
			
			$dropdown .= '</select>';
		}
		else {
			$dropdown = '<p class="error">No states found.</p>';
		}
		return $dropdown;
	}
	
	/** Creates countries dropdown menu from database. */
	function get_countries_dropdown($category=""){
		$category_name = ($category==="") ? $category : $category."_" ;
		$category_id = str_replace("_", "-", $category_name);
		$dropdown = "";
		$countries = mysql_query("SELECT * FROM `". TBL_COUNTRY ."`") or die(error_message("No countries found", mysql_error(), "34"));
		$num_rows = mysql_num_rows($countries);
		if( $num_rows > 0 ) {
			$dropdown .= '<select name="'. $category_name .'countries" class="countries" id="'. $category_id .'countries">';
			$dropdown .= '<option value="">--Select-- </option>';
			while($r = mysql_fetch_array($countries))
			{	
				$dropdown .= '<option value="'.$r['id'].'"';
				// Select US by default
				if($r['name'] == "United States") { 
					$dropdown .= " selected=\"selected\"";
				}
				$dropdown .= '>'.$r['name'].'</option>';
			}
			
			$dropdown .= '</select>';
		}
		else {
			$dropdown = '<p class="error">No countries found.</p>';
		}
		return $dropdown;
	}
	
	/** Creates program dropdown menu from database. */
	function get_program_dropdown($category=""){
		$category_name = ($category==="") ? $category : $category."_" ;
		$category_id = str_replace("_", "-", $category_name);
		$dropdown = "";
		$programs = mysql_query("SELECT * FROM `". TBL_PROGRAM ."`") or die(error_message("No programs found", mysql_error(), "30"));
		$num_rows = mysql_num_rows($programs);
		if( $num_rows > 0 ) {
			$dropdown .= '<select name="'. $category_name .'program" id="'. $category_id .'program" class="program" >';
			$dropdown .= '<option value="">--Select-- </option>';
			while($r = mysql_fetch_array($programs))
			{
				$dropdown .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
			}
			
			$dropdown .= '</select>';
		}
		else {
			$dropdown = '<p class="error">No programs found.</p>';
		}
		return $dropdown;
	}
	
	/** Creates college dropdown menu from database. */
	function get_college_dropdown($category=""){
		$category_name = ($category==="") ? $category : $category."_" ;
		$category_id = str_replace("_", "-", $category_name);
		$dropdown = "";
		$colleges = mysql_query("SELECT * FROM `". TBL_COLLEGE ."`") or die(error_message("No colleges found", mysql_error(), "31"));
		$num_rows = mysql_num_rows($colleges);
		if( $num_rows > 0 ) {
			$dropdown .= '<select name="'. $category_name .'college" class="college" id="'. $category_id .'college">';
			$dropdown .= '<option value="">--Select-- </option>';
			while($r = mysql_fetch_array($colleges))
			{
				$dropdown .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
			}
			
			$dropdown .= '</select>';
		}
		else {
			$dropdown = '<p class="error">No colleges found.</p>';
		}
		return $dropdown;
	}
	
	/** Creates department dropdown menu from database. */
	function get_department_dropdown($college_id, $category=""){
		$category = str_replace("-", "_", $category);
		$category_name = ($category==="") ? $category : $category."_" ;
		$category_id = str_replace("_", "-", $category_name);
		$dropdown = "";
		$departments = mysql_query("SELECT * FROM `". TBL_DEPARTMENT ."` WHERE college_id = '$college_id'") or die(mysql_error(error_message("No departments found", mysql_error(), "32")));
		$num_rows = mysql_num_rows($departments);
		if( $num_rows > 0 ) {
			$dropdown .= '<select name="'. $category_name .'department" class="department" id="'. $category_id .'department">';
			$dropdown .= '<option value="">--Select-- </option>';
			while($r = mysql_fetch_array($departments))
			{
				$dropdown .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
			}
			
			$dropdown .= '</select>';
		}
		else {
			$dropdown = '<p class="error">No departments found.</p>';
		}
		return $dropdown;
	}
	
	/** Hashes the given password*/
	function hash_pass($password) {
		$hashed = md5(sha1($password));
		$hashed = crypt($hashed, PASSSALT);
		$hashed = sha1(md5($hashed));
		return $hashed;
	}
	
	/** Output all the head information: meta, css, js, etc...*/
	function get_head_meta($title = "", $keywords = "", $description = "") {
		$title .= " | " . TITLE;

		$head = '<title>'.$title.'</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="keywords" content="'.$keywords.'" />
		<meta name="description" content="'.$description.'" />
		<meta name="language" content="en-us" />
		<meta name="robots" content="index,follow" />
		<meta name="googlebot" content="index,follow" />
		<meta name="msnbot" content="index,follow" />
		<meta name="revisit-after" content="7 Days" />
		<meta name="url" content="'.BASE.'" />
		<meta name="copyright" content="Copyright '.date("Y").' Your site name here. All rights reserved." />
		<meta name="author" content="'. SITE_AUTHOR .'" />
		<meta name="viewport" content="width=device-width" />
		
		<!-- SCRIPTS
		=============================================================== -->
		<script language="JavaScript" type="text/javascript" src="'. BASE .'/scripts/jquery-1.7.1.min.js"></script>
		<script language="JavaScript" type="text/javascript" src="'. BASE .'/scripts/jquery.validate.js"></script>

		<!-- STYLESHEETS
		=============================================================== -->
		<!--[if lt IE 9]>
			<link rel="stylesheet" href="'. BASE .'/styles/ie.css">
		<![endif]-->
		<link rel="stylesheet" href="'. BASE .'/styles/default.css">

		<!-- IE Fix for HTML5 Tags -->
		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico" />';

		echo $head;
	}
	
?>
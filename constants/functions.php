<?php
	/**
	 * Contains helpful functions for Tassel.
	 *
	 * TODO: Find way to merge some of the select functions
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
			$dropdown .= '<select name="'. $category_name .'states" class="states span4" id="'. $category_id .'states">';
			$dropdown .= '<option value="">--Select-- </option>';
			while($r = mysql_fetch_array($states))
			{
				$dropdown .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
			}
			
			$dropdown .= '</select>';
		}
		else {
			$dropdown = '<p class="error alert">No states found.</p>';
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
			$dropdown .= '<select name="'. $category_name .'countries" class="countries span4" id="'. $category_id .'countries">';
			$dropdown .= '<option value="">--Select-- </option>';
			while($r = mysql_fetch_array($countries))
			{	
				$dropdown .= '<option value="'.$r['id'].'"';
				// Select US by default
				if($r['name'] == "United States") { 
					$dropdown .= " selected=\"true\"";
				}
				$dropdown .= '>'.$r['name'].'</option>';
			}
			
			$dropdown .= '</select>';
		}
		else {
			$dropdown = '<p class="error alert">No countries found.</p>';
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
			$dropdown .= '<select name="'. $category_name .'program" id="'. $category_id .'program" class="span4 program" >';
			$dropdown .= '<option value="">--Select-- </option>';
			while($r = mysql_fetch_array($programs))
			{
				$dropdown .= '<option value="'.$r['id'].'">';
				if($r['online'] == 1){
					$dropdown .= 'Online ';
				}
				$dropdown .= $r['name'].'</option>';
			}
			
			$dropdown .= '</select>';
		}
		else {
			$dropdown = '<p class="error alert">No programs found.</p>';
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
			$dropdown .= '<select name="'. $category_name .'college" class="college span4" id="'. $category_id .'college">';
			$dropdown .= '<option value="">--Select-- </option>';
			while($r = mysql_fetch_array($colleges))
			{
				$dropdown .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
			}
			
			$dropdown .= '</select>';
		}
		else {
			$dropdown = '<p class="error alert">No colleges found.</p>';
		}
		return $dropdown;
	}
	
	
	/** Creates emails dropdown menu from database. */
	function get_email_dropdown($category=""){
		$category_name = ($category==="") ? $category : $category."_" ;
		$category_id = str_replace("_", "-", $category_name);
		$dropdown = "";
		$profiles = mysql_query("SELECT `first_name`, `last_name`, `email`, AES_DECRYPT(email, '". SALT ."') AS user_email FROM `". TBL_PROFILE ."`") or die(error_message("No profiles found", mysql_error(), "31"));
		$num_rows = mysql_num_rows($profiles);
		if( $num_rows > 0 ) {
			$dropdown .= '<select name="'. $category_name .'email" class="email" id="'. $category_id .'email">';
			$dropdown .= '<option value="">--Select-- </option>';
			while($r = mysql_fetch_array($profiles))
			{
				$dropdown .= '<option value="'.$r['user_email'].'">'.$r['first_name'].' '.$r['last_name'].'</option>';
			}
			
			$dropdown .= '</select>';
		}
		else {
			$dropdown = '<p class="error alert">No people were found.</p>';
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
			$dropdown .= '<select name="'. $category_name .'department" class="department span4" id="'. $category_id .'department">';
			$dropdown .= '<option value="">--Select-- </option>';
			while($r = mysql_fetch_array($departments))
			{
				$dropdown .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
			}
			
			$dropdown .= '</select>';
		}
		else {
			$dropdown = '<p class="error alert">No departments found.</p>';
		}
		return $dropdown;
	}
	
	/** Populates selection with all departments from database. */
	function get_all_department_options(){
		// TODO: Only show departments with people in them
		$options = "";
		$college_qry = mysql_query("SELECT `id`,`name` FROM `". TBL_COLLEGE ."`") or die(mysql_error(error_message("No colleges found", mysql_error(), "32")));
		while($college = mysql_fetch_assoc($college_qry)){
			$options .= '<optgroup label="'. $college['name'] .'">';
			$department_qry = mysql_query("SELECT `id`,`name` FROM `". TBL_DEPARTMENT ."` WHERE college_id = '".$college['id']."'") or die(mysql_error(error_message("No departments found", mysql_error(), "32")));
			while($department = mysql_fetch_assoc($department_qry)){
				$options .= '<option value="'.$department['id'].'">'.$department['name'].'</option>';
			}
			$options .= '</optgroup>';
		}
		return $options;
	}
	
	/** Populates selection with all companies from database. */
	function get_all_company_options(){
		$options = "";
		$company_qry = mysql_query("SELECT DISTINCT `company` FROM `". TBL_ALUMNI ."` ORDER BY `company` ASC") or die(mysql_error(error_message("No companies found", mysql_error(), "32")));
		while($company = mysql_fetch_assoc($company_qry)){
			$options .= '<option value="'.$company['company'].'">'.$company['company'].'</option>';
		}
		return $options;
	}
	
	/** Populates selection with all programs from database. */
	function get_all_program_options(){
		$options = "";
		$program_qry = mysql_query("SELECT `id`,`name`, `online` FROM `". TBL_PROGRAM ."`") or die(mysql_error(error_message("No programs found", mysql_error(), "32")));
		while($program = mysql_fetch_assoc($program_qry)){
			$options .= '<option value="'.$program['id'].'">';
			if($program['online'] == 1){
				$options .= 'Online ';
			}
			$options .= $program['name'].'</option>';
		}
		return $options;
	}
	
	/** Populates selection with all companies from database. */
	function get_all_interest_options(){
		$options = "";
		$interest_qry = mysql_query("SELECT `name`, `id` FROM `". TBL_INTEREST ."` ORDER BY `name` ASC") or die(mysql_error(error_message("No companies found", mysql_error(), "32")));
		while($interest = mysql_fetch_assoc($interest_qry)){
			$options .= '<option value="'.$interest['id'].'">'.$interest['name'].'</option>';
		}
		return $options;
	}
	
	/** Populates selection with all programs from database. */
	function get_interests($search=""){
		$checkboxes = "";
		
		$where_string = "";
		$search= sanitize($search);
		$search = purge_invalid($search);
		
		// Check if there are already WHERE constraints
		if(!empty($search)){
				$where_string = "WHERE `name` LIKE '%".$search."%' ";
		}
		
		$interest_qry = mysql_query("SELECT `id`,`name` FROM `". TBL_INTEREST ."` ". $where_string. "ORDER BY `name` ASC") or die(mysql_error(error_message("No interests found", mysql_error(), "32")));
		while($interest = mysql_fetch_assoc($interest_qry)){
			$checkboxes .= '<label class="checkbox" for="interest-'. $interest['id'] .'">
			<input type="checkbox" name="interest[]" class="active-entry" value="'. strtolower($interest['id']) .'" id="interest-'. $interest['id'] .'" />'. strtolower($interest['name']) .'</label>';
		}
		if(empty($checkboxes)) {
			echo "<em>Sorry, no interests match that search.</em>";
		}
		return $checkboxes;
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
		
		<!-- Uses Bootstrap, by Twitter -->
		<link rel="stylesheet" href="'. BASE .'/styles/bootstrap.css">
		
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
	
	/** Change month into academic term */
	function month_to_season($month){
		if($month > 8 && $month <= 12){
			return 'Fall ';
		} elseif($month > 5 && $month <= 8){
			return 'Summer ';
		} elseif($month > 0 && $month <= 5){
			return 'Spring ';
		}
		return '';
	}
	
	/** Converts a timestamp into a more human readable form.
	  * From: Pete Karl http://pkarl.com/articles/contextual-user-friendly-time-and-dates-php/
	  */
	function contextualTime($small_ts, $large_ts=false) {
	  if(!$large_ts) $large_ts = time();
	  $n = $large_ts - $small_ts;
	  if($n <= 1) return 'less than 1 second ago';
	  if($n < (60)) return $n . ' seconds ago';
	  if($n < (60*60)) { $minutes = round($n/60); return 'about ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago'; }
	  if($n < (60*60*16)) { $hours = round($n/(60*60)); return 'about ' . $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago'; }
	  if($n < (time() - strtotime('yesterday'))) return 'yesterday';
	  if($n < (60*60*24)) { $hours = round($n/(60*60)); return 'about ' . $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago'; }
	  if($n < (60*60*24*6.5)) return 'about ' . round($n/(60*60*24)) . ' days ago';
	  if($n < (time() - strtotime('last week'))) return 'last week';
	  if(round($n/(60*60*24*7))  == 1) return 'about a week ago';
	  if($n < (60*60*24*7*3.5)) return 'about ' . round($n/(60*60*24*7)) . ' weeks ago';
	  if($n < (time() - strtotime('last month'))) return 'last month';
	  if(round($n/(60*60*24*7*4))  == 1) return 'about a month ago';
	  if($n < (60*60*24*7*4*11.5)) return 'about ' . round($n/(60*60*24*7*4)) . ' months ago';
	  if($n < (time() - strtotime('last year'))) return 'last year';
	  if(round($n/(60*60*24*7*52)) == 1) return 'about a year ago';
	  if($n >= (60*60*24*7*4*12)) return 'about ' . round($n/(60*60*24*7*52)) . ' years ago'; 
	  return false;
	}
	
	/** Function to send consistent system emails
	  * From: Bennett Stone, HCI 573X Course, Iowa State University
	  */
	define("MAIL_TOP", "<html>
	<body bgcolor=\"#FFFFFF\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
	<table id=\"Table_01\" width=\"100%\" height=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border: 30px solid #000;\">
	<tr>
	<td align=\"left\" valign=\"top\" style=\"border-bottom: 1px solid #ccc; padding:10px;\">
	<img src=\"http://static.php.net/www.php.net/images/php.gif\" width=\"120\" height=\"67\" alt=\"PHP Logo\">
	</td>
	</tr>
	<tr>
	<td align=\"left\" valign=\"top\" style=\"width:435px;height:100%;padding:20px;font-family: Helvetica,Arial,verdana sans-serif;font-size: 10pt;\">");

	define("MAIL_BOTTOM", "</td>
	</tr>
	<tr>
	<td align=\"left\" valign=\"bottom\" style=\"border-top: 1px solid #ccc;\">
	<p style=\"color: #999; font-size: 10px; padding: 10px;\">
	&copy; Tassel</p>
	</td>
	</tr>
	</table>
	</body>
	</html>");

	function send_msg($to_email, $subject, $message)
	{
		$message_body = MAIL_TOP;
		$message_body .= $message;
		$message_body .= MAIL_BOTTOM;

		$headers = "From: Hannah Deering  <".GLOBAL_EMAIL.">\r\n";
		$headers.= "Return-Path: " . GLOBAL_EMAIL . "\r\n";
		$headers.= "Message-ID: <" . gettimeofday(true) . " TheSystem@yourwebsite.com>\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		$send = mail($to_email, $subject, $message_body, $headers);
	}
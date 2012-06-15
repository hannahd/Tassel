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
	function check_empty($table) {
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
	
	/** Converts query to an array of associative arrays */
	function qry_to_array($qry){
		$ret_arr = array();
		
		while($row = mysql_fetch_assoc($qry)){
			if(isset($row['id'])){
				$ret_arr[$row['id']] = $row;
			} else {
				$ret_arr[] = $row;
			}
		}
		
		return $ret_arr;
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
		<script language="JavaScript" type="text/javascript" src="'. BASE .'/scripts/bootstrap-modal.js"></script>
		
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
	function month_to_season($month, $abbr = false){
		if($month > 8 && $month <= 12){
			if($abbr){
				return 'F';
			} else {
				return 'Fall';
			}
		} elseif($month > 5 && $month <= 8){
			if($abbr){
				return 'SS';
			} else {
				return 'Summer';
			}
		} elseif($month > 0 && $month <= 5){
			if($abbr){
				return 'S';
			} else {
				return 'Spring';
			}
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
	
	/** Print arrays inside pre tags. For debugging.  */
	function print_arr($array){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
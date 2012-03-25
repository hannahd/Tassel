<?php
	/**
	 * Contains functions to validate input for the Tassel database.
	 *
	 * @author Hannah Deering
	 * @package Tassel
	 **/
	
/* Data Validators
 * =================================================================== */
	
	/** Checks if the input is valid. */
	function validate_input($input, $name, $is_required, $format, $max_char = -1, $min_char = -1) {
		$errors = array();
		
		// Check if a required field is empty
		if($is_required && empty($input)) {
			$errors[] = $name .' is required.';
		} elseif(!empty($input)){
			// Checks length and format of non-empty fields
			if($min_char > 0 && strlen($input) < $min_char) {
				$errors[] = $name.' must be at least '. $min_char . ' characters.';
			} elseif($max_char > 0 && strlen($input) > $max_char) {
				$errors[] = $name.' can be no more than '.$max_char.' characters.';
			} elseif($format === "alphanum" && !valid_alphanum($input)) {
				$errors[] = $name.' can only include alpha-numeric characters (a-z, A-Z, 0-9) and underscores.';
			} elseif($format === "email" && !valid_email($input)) {
				$errors[] = 'Please enter a valid email address (ex. "joe@shmo.com").';
			} elseif($format === "url" && !valid_url($input)) {
				$errors[] = 'Please enter a valid link (ex. "http://www.google.com").';
			} elseif($format === "phone" && !valid_phone($input)) {
				$errors[] = 'Please enter a valid phone number. (ex. "515-515-5151")';
			} elseif($format === "position" && !valid_position($input)) {
				$errors[] = 'Please select a valid position.';
			} elseif($format === "bool" && !filter_var($input, FILTER_VALIDATE_BOOLEAN)) {
				$errors[] = 'Please enter a valid '.$name.'.';
			} elseif($format === "num" && !is_numeric($input)) {
				$errors[] = 'Please enter a valid '.$name.'.';
			} elseif($format === "date" && !valid_date($input)) {
				$errors[] = 'Please enter a valid '.$name.'.';
			}
			
		}
		return $errors;
	}
	
	
/* Formatting Checkers
 * =================================================================== */

	/** Checks if a string is an valid format for an email address. */
	function valid_email($email) {
	    return (filter_var($email, FILTER_VALIDATE_EMAIL)) ? TRUE : FALSE;
	}
	
	/** Checks if a string is an valid format for a date. */
	function valid_date($date) {
	    return (strtotime($date) !== false) ? TRUE : FALSE;
	}
	
	/** Checks if a string is an valid format for a url. */
	function valid_url($url) {
	    return (preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url)) ? TRUE : FALSE;
	}
	
	/** Checks if a string is an valid format for US phone number. */
	function valid_phone($phone) {
	    return (preg_match('/\(?\d{3}\)?[-\s.]?\d{3}[-\s.]\d{4}/x', $phone)) ? TRUE : FALSE;
	}
	
	/** Checks that a string only contains alpha numeric values or underscores. */
	function valid_alphanum($string) {
	    return (preg_match('/^[a-z\d_]{2,220}$/i', $string)) ? TRUE : FALSE;
	}
	
	/** Checks that the position is unknown, faculty, staff, student, alumni, or visitor. */
	function valid_position($position) {
		return ($position === 'faculty' || $position === 'staff' || $position === 'student'
				 || $position === 'alumni'|| $position === 'visitor' || $position === 'unknown') ? TRUE : FALSE;
	}
	
	
/* Data Sanitization
 * =================================================================== */
	
	/** Removes coding elements from form field.
	  * From http://css-tricks.com/snippets/php/sanitize-database-inputs/ */
	function remove_code($input) {
	  $search = array(
	    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
	    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
	    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
	    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
	  );

	    $output = preg_replace($search, '', $input);
	    return $output;
	}
	
	/** Sanitizes a string to enter into a database.
	  * From http://css-tricks.com/snippets/php/sanitize-database-inputs/ */
	function sanitize($input) {
		if(empty($input)) {
			$output = $input;
		} elseif (is_array($input)) {
	        foreach($input as $var=>$val) {
	            $output[$var] = sanitize($val);
	        }
	    } else {
			$input = trim(htmlentities(strip_tags($input)));
	        if (get_magic_quotes_gpc()) {
	            $input = stripslashes($input);
	        }
	        $input  = remove_code($input);
	        $output = mysql_real_escape_string($input);
	    }

	    return $output;
	}
	
	
?>
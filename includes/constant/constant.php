<?php
	// Variables
	define ("BASE", "http://".$_SERVER['HTTP_HOST']."/HW8_hjhunt_7957");
	define ("ROOT", $_SERVER['DOCUMENT_ROOT']."/HW8_hjhunt_7957");
	define ("TITLE", "Homework 8");
	$debug = false;
	
	// Functions
	function validate_email($email)	{
	    return (filter_var($email, FILTER_VALIDATE_EMAIL)) ? TRUE : FALSE;
	}
	
	function validate_username($username)	{
	    return (preg_match('/^[a-z\d_]{3,220}$/i', $username)) ? TRUE : FALSE;
	}
	
	// From http://css-tricks.com/snippets/php/sanitize-database-inputs/
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
	
	// From http://css-tricks.com/snippets/php/sanitize-database-inputs/
	function sanitize($input) {
	    if (is_array($input)) {
	        foreach($input as $var=>$val) {
	            $output[$var] = sanitize($val);
	        }
	    }
	    else {
	        if (get_magic_quotes_gpc()) {
	            $input = stripslashes($input);
	        }
	        $input  = remove_code($input);
	        $output = mysql_real_escape_string($input);
	    }
	    return $output;
	}
	
	
?>
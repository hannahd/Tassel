<?php
/**
 * Contains functions to manage access rights content for Tassel.
 *
 * @author Hannah Deering
 * @package Tassel
 **/

require_once ("dbconnect.php");

/** Function to secure pages and check users 
  * From: Bennett Stone, HCI 573X Course, Iowa State University
  */
function secure_page() {
	session_start();

	// Secure against Session Hijacking by checking user agent
	if(isset($_SESSION['HTTP_USER_AGENT'])) {
		// Make sure values match!
		if($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']) || $_SESSION['logged'] != true) {
			logout();
			exit;
		}

		// We can only check the DB IF the session has specified a user id
		if(isset($_SESSION['user_id'])) {
			$details = mysql_query("SELECT session_key, session_time FROM ".TBL_PROFILE." WHERE id ='".$_SESSION['user_id']."'") or die(mysql_error());
			list($session_key, $session_time) = mysql_fetch_row($details);

			// We know that we've declared the variables below, so if they aren't set, or don't match the DB values, force exit
			if(!isset($_SESSION['stamp']) && $_SESSION['stamp'] != $session_time || !isset($_SESSION['key']) && $_SESSION['key'] != $session_key) {
				logout();
				exit;
			}
		}
	}
	// If we get to this, then the $_SESSION['HTTP_USER_AGENT'] was not set and the user cannot be validated
	else {
		logout();
		exit;
	}
}


/** Function to log out users securely
  * From: Bennett Stone, HCI 573X Course, Iowa State University
  */
function logout($message = NULL) {
	if(!isset($_SESSION)) {
		session_start();
	}

	// If the user is 'partially' set for some reason, unset the db session vars
	if(isset($_SESSION['user_id'])){
		mysql_query("UPDATE `".TBL_PROFILE."` SET `session_key`='', `session_time`='' WHERE `id`='".$_SESSION['user_id']."'") or die(mysql_error());
		unset($_SESSION['user_id']);
	}
		unset($_SESSION['user_name']);
		unset($_SESSION['user_level']);
		unset($_SESSION['HTTP_USER_AGENT']);
		unset($_SESSION['stamp']);
		unset($_SESSION['key']);
		unset($_SESSION['fullname']);
		unset($_SESSION['logged']);
		session_unset();
		session_destroy();

	if(isset($message)) {
		header("Location: ".BASE."/admin.php?msg=".$message);
	}
else {
		header("Location: ".BASE);
	}
}

/** Function to check if a user is an admin
  * From: Bennett Stone, HCI 573X Course, Iowa State University
  */
function is_admin() {
	if(isset($_SESSION['user_level']) && $_SESSION['user_level'] >= 5) {
		return true;
	} else {
		return false;
	}
}

/** Function to generate key for log in 
  * From: Bennett Stone, HCI 573X Course, Iowa State University
  */
function generate_key($length = 7) {
	$password = "";
	$possible = "0123456789abcdefghijkmnopqrstuvwxyz";

	$i = 0;
	while ($i < $length) {
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		if (!strstr($password, $char)) {
			$password .= $char;
			$i++;
		}
	}
	return $password;
}

?>
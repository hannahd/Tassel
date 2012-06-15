<?php
/**
 * Data processor for Tassel.
 * 
 * This file provides an interface between miscellaneous forms and 
 * the database. Includes generating department drop downs and 
 * enabling/disabling profiles.
 *
 * Error codes ending with p. 
 *
 * @author Hannah Deering
 * @package Tassel
 **/

require_once ("constants.php");
require_once ("dbconnect.php"); //Includes database connection details
require_once ("controls.php"); //Includes functions

if(isset($_GET['action']) && $_GET['action'] == "update_departments") {
	// Check that all the necessary data is present
	if(isset($_POST['college_id']) && is_numeric($_POST['college_id']) && isset($_GET['comajor'])) {
		if($_GET['comajor'] === "true"){
			echo  '<select name="comajor_department" id="comajor-department" class="span4">
				<option value="">Select...</option>';
		} else {
			echo  '<select name="department" id="department" class="span4 faculty student alumni visitor">
				<option value="">Select...</option>';
		}
		echo department_control($_POST['college_id'], false, "dropdown", "", "", "");
		echo '</select>';
	} else {
		// If all the necessary data wasn't present
		if(isset($_GET['comajor']) && $_GET['comajor'] === "true"){
			echo '<input class="span4 disabled" name="comajor_department" id="comajor-department" disabled="disabled" value="select a college first" />';
		} else {
			echo '<input class="span4 disabled faculty student alumni visitor" name="department" id="department" disabled="disabled" value="select a college first" />';
		}
	}	
}

if(isset($_GET['action']) && ($_GET['action'] == "enable" || $_GET['action'] == "disable") && isset($_POST['id']) && isset($_POST['name'])) {
	$id = sanitize($_POST['id']);
	$errors = validate_input($id, "id", true, "num");
	
	$name = sanitize($_POST['name']);

	if(count($errors) == 0){
		if($_GET['action'] == "enable"){
			$update_profile = mysql_query("UPDATE `". TBL_PROFILE ."` SET `enabled`='1' WHERE `id`='$id'") 
				or die(error_message("The profile could not be updated", mysql_error(), "1p"));
			
			if($update_profile) {
				echo "<div class=\"success alert delayed-fade\">Enabled $name!</div>";
			} else {
				echo "<div class=\"error alert delayed-fade\">Unable to enable $name!</div>";
			}
		} elseif($_GET['action'] == "disable"){
			$update_profile = mysql_query("UPDATE `". TBL_PROFILE ."` SET `enabled`='0' WHERE `id`='$id'")
				or die(error_message("The profile could not be updated", mysql_error(), "2p"));
			if($update_profile) {
				echo "<div class=\"success alert delayed-fade\">Disabled $name!</div>";
			} else {
				echo "<div class=\"error alert delayed-fade\">Unable to disable $name!</div>";
			}
		}
	}
}
<?php
/**
 * Redirect to index or profile management based on privileges
 * Access limited to admins.
 *
 * @author Hannah Deering
 * @package Tassel
 **/

require_once ("../constants/constants.php");
require_once ("../constants/dbconnect.php"); //Includes database connection
require_once ("../constants/access_functions.php"); //Includes functions to control user privileges

secure_page();
if(!is_admin()){
	header("Location: ".BASE."/admin.php");
}else{
	header("Location: ".BASE."/admin/manage_profiles.php");
}
?>
<?php
/**
 * Logout of Administrator for Tassel.
 *
 * Redirect to login page on successful logout
 * 
 * @author Hannah Deering
 * @package Tassel
 **/

require 'constants/access_functions.php';
$message = urlencode("You have logged out successfully!");
logout($message);
<?php
	include_once ("constant.php");
// DATABASE CONSTANTS
	define ("DB", "localhost");
	define ("DB_USER", "root");
	define ("DB_PASS", "root");
	define ("DB_NAME", "hjhunt_hw8");
	define ("USER_TABLE", "users");
	
// DATABASE FUNCTIONS
	
// DATABASE CONNECTION
	// Connect to MYSQL
	$connect = mysql_connect(DB, DB_USER, DB_PASS) or die("<p class=\"error\">".mysql_error()."</p>") or die(($debug) ? mysql_error() : "<p class=\"error\">Database Error 1: Please contact an admin.</p>");
	
	// Create the DB (if it doesn't already exist)
	$create_db = mysql_query("CREATE DATABASE IF NOT EXISTS ". DB_NAME) or die(($debug) ? mysql_error() : "<p class=\"error\">Database Error 2: Please contact an admin.</p>");
	
	// Select the DB
	$select = mysql_select_db(DB_NAME, $connect) or die("<p class=\"error\">".mysql_error()."</p>") or die(($debug) ? mysql_error() : "<p class=\"error\">Database Error 3: Please contact an admin.</p>");
	
	// Create the table (if it doesn't already exist)
	$create_tbl = mysql_query("CREATE TABLE IF NOT EXISTS ".USER_TABLE."(
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `username` varchar(220) DEFAULT NULL,
	  `password` varchar(220) DEFAULT NULL,
	  `email` varbinary(256) DEFAULT NULL,
	  `newsletter` bool DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8") or die(($debug) ? mysql_error() : "<p class=\"error\">Database Error 4: Please contact an admin.</p>");

?>
<?php
	/**
	 * Database connection for Tassel.
	 * 
	 * This file creates connection with database.
	 * Error codes starting with 0. 
	 *
	 * @author Hannah Deering
	 * @package Tassel
	 **/
	require_once ("constants.php");
	include_once ("functions.php");
	
	/** Connect to MYSQL */
	$connect = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(error_message("Error connecting to database", mysql_error(), "01"));

	/** Create the DB (if it doesn't already exist) */
	$create_db = mysql_query("CREATE DATABASE IF NOT EXISTS ". DB_NAME) or die(error_message("Error creating database", mysql_error(), "02"));

	/** Select the DB */
	$select = mysql_select_db(DB_NAME, $connect) or die(error_message("Error accessing database", mysql_error(), "03"));


?>
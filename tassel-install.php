<?php
/**
 * Installer for Tassel.
 *
 * This file sets up the MySQL database and fills it with values from 
 * the config file. You can change options by adding values to the get 
 * string or changing the variables below. 
 * 
 * Options:
 * 		reset : clears all existing values tables in the database (include "reset=t")
 *		demo : adds profiles for demonstration purposes (include "demo=t")
 *		uninstall : removes all values and tables from database (include "uninstall=t")
 * 
 * Error codes ending in i. 
 * 
 * @author Hannah Deering
 * @package Tassel
 **/

require_once ('constants/constants.php'); // Includes database details
require_once (ROOT.'/tassel-config.php');
require_once (ROOT.'/constants/functions.php');
require_once (ROOT.'/constants/dbconnect.php');
require_once (ROOT."/constants/access-functions.php"); //Includes functions to control user privileges

// Deletes the existing tables if true
$reset_all = false;

// Fills with demo values if true
$demo = false;

// Only deletes tables if true (no install)
$uninstall = false;

if(isset($_GET['reset']) && $_GET['reset'] == "t" ){ $reset_all = true; }
if(isset($_GET['demo']) && $_GET['demo'] == "t" ){ $demo = true; }
if(isset($_GET['uninstall']) && $_GET['uninstall'] == "t" ){ $uninstall = true; }



/* Reset Database
 * ===================================================================*/

if($reset_all || $uninstall){                                     
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_PROFILE_GROUP_MAP) 	 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_PROFILE_INTEREST_MAP) or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_PROFILE_LINK_MAP) 	 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_PROFILE_PROFILE_MAP)  or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_ALUMNI)				 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_FACULTY) 			 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_STAFF) 				 or die(error_message("Failed to drop table", mysql_error(), '1i'));	
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_STUDENT) 			 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_VISITOR) 			 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_DEPARTMENT) 			 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_COLLEGE) 			 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_COUNTRY) 			 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_GROUP) 				 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_INTEREST) 			 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_LINK) 				 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_PROGRAM) 			 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_RELATIONSHIP) 		 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_UPDATE) 				 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_US_STATE) 			 or die(error_message("Failed to drop table", mysql_error(), '1i'));
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_PROFILE) 			 or die(error_message("Failed to drop table", mysql_error(), '1i'));
}

// Check if this is only an uninstall
if(!$uninstall){

	/* Create Core Tables
	 * ===================================================================*/

	/** College Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_COLLEGE ."` (
	  	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(220) NOT NULL DEFAULT '',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create college table", mysql_error(), '2i'));

	/** Country Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_COUNTRY ."` (
	  	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(220) NOT NULL DEFAULT '',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create country table", mysql_error(), '3i'));

	/** Department Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_DEPARTMENT ."` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(220) NOT NULL DEFAULT '',
	  `college_id` int(11) unsigned NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `department-college` (`college_id`),
	  CONSTRAINT `department-college` FOREIGN KEY (`college_id`) REFERENCES `". TBL_COLLEGE ."` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create department table", mysql_error(), '4i'));

	/** Group Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_GROUP ."` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(220) NOT NULL DEFAULT '',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create group table", mysql_error(), '5i'));

	/** Interest Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_INTEREST ."` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(220) NOT NULL DEFAULT '',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create interest table", mysql_error(), '5i'));

	/** Link Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_LINK ."` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(220) NOT NULL DEFAULT '',
		  `url` varchar(220) NOT NULL DEFAULT '',
		  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create link table", mysql_error(), '6i'));

	/** Program Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_PROGRAM ."` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
		  `abbreviation` varchar(11) CHARACTER SET latin1 DEFAULT NULL,
		  `online` tinyint(1) unsigned DEFAULT '0',
		  `role` enum('student','researcher') NOT NULL DEFAULT 'student',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create program table", mysql_error(), '7i'));

	/** Relationship Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_RELATIONSHIP ."` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(220) CHARACTER SET latin1 NOT NULL DEFAULT '',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create relationship table", mysql_error(), '8i'));

	/** US State Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_US_STATE ."` (
	  	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(220) NOT NULL,
		  `abbreviation` varchar(2) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create US state table", mysql_error(), '9i'));



	/* Create People Tables
	 * ===================================================================*/

	/** Profile Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_PROFILE ."` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `username` varbinary(256) NOT NULL DEFAULT '',
		  `md5_id` varchar(200) NOT NULL DEFAULT '',
		  `email` longblob NOT NULL,
		  `password` varchar(220) NOT NULL DEFAULT '',
		  `first_name` varchar(220) NOT NULL,
		  `last_name` varchar(220) NOT NULL,
		  `photo` varchar(220) DEFAULT NULL,
		  `enabled` tinyint(1) NOT NULL DEFAULT '1',
		  `user_level` tinyint(4) NOT NULL DEFAULT '1',
		  `position` enum('unknown','faculty','staff','student','alumni','visitor') NOT NULL DEFAULT 'unknown',
		  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `ip_address` varchar(200) NOT NULL DEFAULT '',
		  `activation_code` int(10) NOT NULL DEFAULT '0',
		  `session_key` varchar(220) NOT NULL DEFAULT '',
		  `session_time` varchar(220) NOT NULL DEFAULT '',
		  `num_logins` int(11) NOT NULL DEFAULT '0',
		  `date_disabled` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `last_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile table", mysql_error(), '10i'));


	/** Faculty Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_FACULTY ."` (
		  `profile_id` bigint(20) unsigned NOT NULL,
		  `title` varchar(220) DEFAULT NULL,
		  `department_id` int(11) unsigned NOT NULL,
		  `phone` varchar(20) DEFAULT NULL,
		  `office_location` varchar(220) DEFAULT NULL,
		  `education` text,
		  `bio` text,
		  `start_date` date NOT NULL,
		  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`profile_id`),
		  KEY `faculty-department` (`department_id`),
		  CONSTRAINT `faculty-department` FOREIGN KEY (`department_id`) REFERENCES `". TBL_DEPARTMENT ."` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
		  CONSTRAINT `faculty-profile` FOREIGN KEY (`profile_id`) REFERENCES `". TBL_PROFILE ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create faculty table", mysql_error(), '11i'));

	/** Staff Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_STAFF ."` (
		  `profile_id` bigint(20) unsigned NOT NULL,
		  `title` varchar(220) DEFAULT NULL,
		  `phone` varchar(20) DEFAULT NULL,
		  `office_location` varchar(220) DEFAULT NULL,
		  `bio` text,
		  `start_date` date NOT NULL,
		  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`profile_id`),
		  CONSTRAINT `staff-profile` FOREIGN KEY (`profile_id`) REFERENCES `". TBL_PROFILE ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create staff table", mysql_error(), '12i'));

	/** Student Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_STUDENT ."` (
	 	  `profile_id` bigint(20) unsigned NOT NULL,
		  `program_id` int(11) unsigned NOT NULL,
		  `department_id` int(11) unsigned NOT NULL,
		  `comajor_department_id` int(11) unsigned DEFAULT NULL,
		  `phone` varchar(20) DEFAULT NULL,
		  `office_location` varchar(220) DEFAULT NULL,
		  `title` varchar(220) DEFAULT NULL,
		  `company` varchar(220) DEFAULT NULL,
		  `home_city` varchar(220) DEFAULT NULL,
		  `state_id` int(11) unsigned DEFAULT NULL,
		  `country_id` int(11) unsigned DEFAULT NULL,
		  `education` text,
		  `bio` text,
		  `start_date` date NOT NULL,
		  `grad_date` date DEFAULT NULL,
		  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  `admission_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`profile_id`),
		  KEY `student-program` (`program_id`),
		  KEY `student-department` (`department_id`),
		  KEY `student-comajor` (`comajor_department_id`),
		  KEY `student-state` (`state_id`),
		  KEY `student-country` (`country_id`),
		  CONSTRAINT `student-country` FOREIGN KEY (`country_id`) REFERENCES `". TBL_COUNTRY ."` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
		  CONSTRAINT `student-comajor` FOREIGN KEY (`comajor_department_id`) REFERENCES `". TBL_DEPARTMENT ."` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
		  CONSTRAINT `student-department` FOREIGN KEY (`department_id`) REFERENCES `". TBL_DEPARTMENT ."` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
		  CONSTRAINT `student-profile` FOREIGN KEY (`profile_id`) REFERENCES `". TBL_PROFILE ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
		  CONSTRAINT `student-program` FOREIGN KEY (`program_id`) REFERENCES `". TBL_PROGRAM ."` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
		  CONSTRAINT `student-state` FOREIGN KEY (`state_id`) REFERENCES `". TBL_US_STATE ."` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create student table", mysql_error(), '13i'));

	/** Visitor Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_VISITOR ."` (
		  `profile_id` bigint(20) unsigned NOT NULL,
		  `title` varchar(220) DEFAULT NULL,
		  `department_id` int(11) unsigned DEFAULT NULL,
		  `phone` varchar(20) DEFAULT NULL,
		  `office_location` varchar(220) DEFAULT NULL,
		  `education` text,
		  `bio` text,
		  `start_date` date NOT NULL,
		  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`profile_id`),
		  KEY `visitor-department` (`department_id`),
		  CONSTRAINT `visitor-department` FOREIGN KEY (`department_id`) REFERENCES `". TBL_DEPARTMENT ."` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
		  CONSTRAINT `visitor-profile` FOREIGN KEY (`profile_id`) REFERENCES `". TBL_PROFILE ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create update table", mysql_error(), '14i'));


	/** Alumni Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_ALUMNI ."` (
	 	  `profile_id` bigint(20) unsigned NOT NULL,
		  `program_id` int(11) unsigned NOT NULL,
		  `department_id` int(11) unsigned NOT NULL,
		  `comajor_department_id` int(11) unsigned DEFAULT NULL,
		  `title` varchar(220) DEFAULT NULL,
		  `company` varchar(220) DEFAULT NULL,
		  `company_city` varchar(220) DEFAULT NULL,
		  `state_id` int(11) unsigned DEFAULT NULL,
		  `country_id` int(11) unsigned DEFAULT NULL,
		  `dissertation_title` varchar(220) DEFAULT NULL,
		  `education` text,
		  `bio` text,
		  `start_date` date NOT NULL,
		  `grad_date` date DEFAULT NULL,
		  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`profile_id`),
		  KEY `alumni-program` (`program_id`),
		  KEY `alumni-department` (`department_id`),
		  KEY `alumni-comajor` (`comajor_department_id`),
		  KEY `alumni-state` (`state_id`),
		  KEY `alumni-country` (`country_id`),
		  CONSTRAINT `alumni-country` FOREIGN KEY (`country_id`) REFERENCES `". TBL_COUNTRY ."` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
		  CONSTRAINT `alumni-comajor` FOREIGN KEY (`comajor_department_id`) REFERENCES `". TBL_DEPARTMENT ."` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
		  CONSTRAINT `alumni-department` FOREIGN KEY (`department_id`) REFERENCES `". TBL_DEPARTMENT ."` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
		  CONSTRAINT `alumni-profile` FOREIGN KEY (`profile_id`) REFERENCES `". TBL_PROFILE ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
		  CONSTRAINT `alumni-program` FOREIGN KEY (`program_id`) REFERENCES `". TBL_PROGRAM ."` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
		  CONSTRAINT `alumni-state` FOREIGN KEY (`state_id`) REFERENCES `". TBL_US_STATE ."` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create alumni table", mysql_error(), '15i'));



	/* Create Mapping Tables
	 * ===================================================================*/

	/** Profile/Group Map Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_PROFILE_GROUP_MAP ."` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `group_id` int(11) unsigned NOT NULL,
		  `profile_id` bigint(20) unsigned NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `map-pg-group` (`group_id`),
		  KEY `map-pg-profile` (`profile_id`),
		  CONSTRAINT `map-pg-group` FOREIGN KEY (`group_id`) REFERENCES `". TBL_GROUP ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
		  CONSTRAINT `map-pg-profile` FOREIGN KEY (`profile_id`) REFERENCES `". TBL_PROFILE ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile-group map table", mysql_error(), '16i'));

	/** Profile/Interest Map Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_PROFILE_INTEREST_MAP ."` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `profile_id` bigint(20) unsigned NOT NULL,
		  `interest_id` int(11) unsigned NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `map-pi-profile` (`profile_id`),
		  KEY `map-pi-interest` (`interest_id`),
		  CONSTRAINT `map-pi-interest` FOREIGN KEY (`interest_id`) REFERENCES `". TBL_INTEREST ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
		  CONSTRAINT `map-pi-profile` FOREIGN KEY (`profile_id`) REFERENCES `". TBL_PROFILE ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile-interest map table", mysql_error(), '17i'));

	/** Profile/Link Map Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_PROFILE_LINK_MAP ."` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `profile_id` bigint(20) unsigned NOT NULL,
		  `link_id` int(11) unsigned NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `map-pl-profile` (`profile_id`),
		  KEY `map-pl-link` (`link_id`),
		  CONSTRAINT `map-pl-link` FOREIGN KEY (`link_id`) REFERENCES `". TBL_LINK ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
		  CONSTRAINT `map-pl-profile` FOREIGN KEY (`profile_id`) REFERENCES `". TBL_PROFILE ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile-link map table", mysql_error(), '18i'));
	
	/** Profile/Profile Map Table (if it doesn't already exist) */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_PROFILE_PROFILE_MAP ."` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `profile_a_id` bigint(20) unsigned NOT NULL,
		  `profile_b_id` bigint(20) unsigned NOT NULL,
		  `relationship_id` int(11) unsigned NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `map-pp-profile-a` (`profile_a_id`),
		  KEY `map-pp-profile-b` (`profile_b_id`),
		  KEY `map-pp-relationship` (`relationship_id`),
		  CONSTRAINT `map-pp-relationship` FOREIGN KEY (`relationship_id`) REFERENCES `". TBL_RELATIONSHIP ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
		  CONSTRAINT `map-pp-profile-a` FOREIGN KEY (`profile_a_id`) REFERENCES `". TBL_PROFILE ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
		  CONSTRAINT `map-pp-profile-b` FOREIGN KEY (`profile_b_id`) REFERENCES `". TBL_PROFILE ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile-profile map table", mysql_error(), '19i'));


	/* Create Logging Tables
	 * ===================================================================*/

	/** Update Table. */
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_UPDATE ."` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `profile_id` bigint(20) unsigned NOT NULL,
		  `date_submitted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_field` varchar(220) NOT NULL DEFAULT '',
		  `content` text NOT NULL,
		  `notification` tinyint(1) DEFAULT '0',
		  PRIMARY KEY (`id`),
		  KEY `update-profile` (`profile_id`),
		  CONSTRAINT `update-profile` FOREIGN KEY (`profile_id`) REFERENCES `". TBL_PROFILE ."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create update table", mysql_error(), '20i'));

/*
	/* Fill Tables
	 * ===================================================================*/

	/** Fill colleges with values from config file. */
	if( check_empty(TBL_COLLEGE) ) { 
		$fill_table = mysql_query("INSERT INTO `". TBL_COLLEGE ."` (`name`)
			VALUES ". array_to_mysql_vals($colleges)) or die(error_message("Failed to fill colleges", mysql_error(), '21i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled colleges!</p>"; }
	}

	/** Fill departments with values from config file. */
	if( check_empty(TBL_DEPARTMENT) ) { 
		$fill_table = mysql_query("INSERT INTO `". TBL_DEPARTMENT. "` (`name`, `college_id`)
			VALUES ". array_to_mysql_vals($departments, true)) or die(error_message("Failed to fill departments", mysql_error(), '22i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled departments!</p>"; }
	}

	/** Fill US States. */
	if( check_empty(TBL_US_STATE) ) { 
		$fill_table = mysql_query("INSERT INTO `". TBL_US_STATE ."` (`name`, `abbreviation`)
			VALUES
			('Alabama','AL'), ('Alaska','AK'), ('Arizona','AZ'), ('Arkansas','AR'), ('California','CA'), ('Colorado','CO'), 
			('Connecticut','CT'), ('Delaware','DE'), ('Florida','FL'), ('Georgia','GA'), ('Hawaii','HI'), ('Idaho','ID'), ('Illinois','IL'), ('Indiana','IN'), 
			('Iowa','IA'), ('Kansas','KS'), ('Kentucky','KY'), ('Louisiana','LA'), ('Maine','ME'), ('Maryland','MD'), ('Massachusetts','MA'), ('Michigan','MI'), 
			('Minnesota','MN'), ('Mississippi','MS'), ('Missouri','MO'), ('Montana','MT'), ('Nebraska','NE'), ('Nevada','NV'), ('New Hampshire','NH'), 
			('New Jersey','NJ'), ('New Mexico','NM'), ('New York','NY'), ('North Carolina','NC'), ('North Dakota','ND'), ('Ohio','OH'), ('Oklahoma','OK'), 
			('Oregon','OR'), ('Pennsylvania','PA'), ('Rhode Island','RI'), ('South Carolina','SC'), ('South Dakota','SD'), ('Tennessee','TN'), ('Texas','TX'), 
			('Utah','UT'), ('Vermont','VT'), ('Virginia','VA'), ('Washington','WA'), ('West Virginia','WV'), ('Wisconsin','WI')") or die(error_message("Failed to fill states", mysql_error(), '23i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled states!</p>"; }
	}

	/** Fill Countries. */
	if( check_empty(TBL_COUNTRY) ) { 
		$fill_table = mysql_query("INSERT INTO `". TBL_COUNTRY ."` (`name`)
			VALUES
			('Afghanistan'), ('Akrotiri'), ('Albania'), ('Algeria'), ('American Samoa'), ('Andorra'), ('Angola'), ('Anguilla'), ('Antarctica'), 
			('Antigua and Barbuda'), ('Argentina'), ('Armenia'), ('Aruba'), ('Ashmore and Cartier Islands'), ('Australia'), ('Austria'), ('Azerbaijan'), 
			('Bahamas, The'), ('Bahrain'), ('Bangladesh'), ('Barbados'), ('Bassas da India'), ('Belarus'), ('Belgium'), ('Belize'), ('Benin'), ('Bermuda'), 
			('Bhutan'), ('Bolivia'), ('Bosnia and Herzegovina'), ('Botswana'), ('Bouvet Island'), ('Brazil'), ('British Indian Ocean Territory'), 
			('British Virgin Islands'), ('Brunei'), ('Bulgaria'), ('Burkina Faso'), ('Burma'), ('Burundi'), ('Cambodia'), ('Cameroon'), ('Canada'), 
			('Cape Verde'), ('Cayman Islands'), ('Central African Republic'), ('Chad'), ('Chile'), ('China'), ('Christmas Island'), ('Clipperton Island'), 
			('Cocos (Keeling) Islands'), ('Colombia'), ('Comoros'), ('Congo, Democratic Republic of the'), ('Congo, Republic of the'), ('Cook Islands'), 
			('Coral Sea Islands'), ('Costa Rica'), ('Cote d''Ivoire'), ('Croatia'), ('Cuba'), ('Cyprus'), ('Czech Republic'), ('Denmark'), ('Dhekelia'), 
			('Djibouti'), ('Dominica'), ('Dominican Republic'), ('Ecuador'), ('Egypt'), ('El Salvador'), ('Equatorial Guinea'), ('Eritrea'), ('Estonia'), 
			('Ethiopia'), ('Europa Island'), ('Falkland Islands (Islas Malvinas)'), ('Faroe Islands'), ('Fiji'), ('Finland'), ('France'), ('French Guiana'), 
			('French Polynesia'), ('French Southern and Antarctic Lands'), ('Gabon'), ('Gambia, The'), ('Gaza Strip'), ('Georgia'), ('Germany'), ('Ghana'), 
			('Gibraltar'), ('Glorioso Islands'), ('Greece'), ('Greenland'), ('Grenada'), ('Guadeloupe'), ('Guam'), ('Guatemala'), ('Guernsey'), ('Guinea'), 
			('Guinea-Bissau'), ('Guyana'), ('Haiti'), ('Heard Island and McDonald Islands'), ('Holy See (Vatican City)'), ('Honduras'), ('Hong Kong'), 
			('Hungary'), ('Iceland'), ('India'), ('Indonesia'), ('Iran'), ('Iraq'), ('Ireland'), ('Isle of Man'), ('Israel'), ('Italy'), ('Jamaica'), 
			('Jan Mayen'), ('Japan'), ('Jersey'), ('Jordan'), ('Juan de Nova Island'), ('Kazakhstan'), ('Kenya'), ('Kiribati'), ('Korea, North'), 
			('Korea, South'), ('Kuwait'), ('Kyrgyzstan'), ('Laos'), ('Latvia'), ('Lebanon'), ('Lesotho'), ('Liberia'), ('Libya'), ('Liechtenstein'), 
			('Lithuania'), ('Luxembourg'), ('Macau'), ('Macedonia'), ('Madagascar'), ('Malawi'), ('Malaysia'), ('Maldives'), ('Mali'), ('Malta'), 
			('Marshall Islands'), ('Martinique'), ('Mauritania'), ('Mauritius'), ('Mayotte'), ('Mexico'), ('Micronesia, Federated States of'), ('Moldova'), 
			('Monaco'), ('Mongolia'), ('Montserrat'), ('Morocco'), ('Mozambique'), ('Namibia'), ('Nauru'), ('Navassa Island'), ('Nepal'), ('Netherlands'), 
			('Netherlands Antilles'), ('New Caledonia'), ('New Zealand'), ('Nicaragua'), ('Niger'), ('Nigeria'), ('Niue'), ('Norfolk Island'), 
			('Northern Mariana Islands'), ('Norway'), ('Oman'), ('Pakistan'), ('Palau'), ('Panama'), ('Papua New Guinea'), ('Paracel Islands'), 
			('Paraguay'), ('Peru'), ('Philippines'), ('Pitcairn Islands'), ('Poland'), ('Portugal'), ('Puerto Rico'), ('Qatar'), ('Reunion'), 
			('Romania'), ('Russia'), ('Rwanda'), ('Saint Helena'), ('Saint Kitts and Nevis'), ('Saint Lucia'), ('Saint Pierre and Miquelon'), 
			('Saint Vincent and the Grenadines'), ('Samoa'), ('San Marino'), ('Sao Tome and Principe'), ('Saudi Arabia'), ('Senegal'), 
			('Serbia and Montenegro'), ('Seychelles'), ('Sierra Leone'), ('Singapore'), ('Slovakia'), ('Slovenia'), ('Solomon Islands'), ('Somalia'), 
			('South Africa'), ('South Georgia and the South Sandwich Islands'), ('Spain'), ('Spratly Islands'), ('Sri Lanka'), ('Sudan'), ('Suriname'), 
			('Svalbard'), ('Swaziland'), ('Sweden'), ('Switzerland'), ('Syria'), ('Taiwan'), ('Tajikistan'), ('Tanzania'), ('Thailand'), ('Timor-Leste'), 
			('Togo'), ('Tokelau'), ('Tonga'), ('Trinidad and Tobago'), ('Tromelin Island'), ('Tunisia'), ('Turkey'), ('Turkmenistan'), ('Turks and Caicos Islands'), 
			('Tuvalu'), ('Uganda'), ('Ukraine'), ('United Arab Emirates'), ('United Kingdom'), ('United States'), ('Uruguay'), ('Uzbekistan'), ('Vanuatu'), 
			('Venezuela'), ('Vietnam'), ('Virgin Islands'), ('Wake Island'), ('Wallis and Futuna'), ('West Bank'), ('Western Sahara'), ('Yemen'), ('Zambia'), 
			('Zimbabwe')") or die(error_message("Failed to fill colleges", mysql_error(), '24i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled countries!</p>"; }
	}

	/** Fill groups with values from config file. */
	if( check_empty(TBL_GROUP) ) { 
		$fill_table = mysql_query("INSERT INTO `". TBL_GROUP ."` (`name`)
			VALUES ". array_to_mysql_vals($groups)) or die(error_message("Failed to fill groups", mysql_error(), '25i')); 
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled groups!</p>"; }
	}

	/** Fill programs with values from config file. */
	if( check_empty(TBL_PROGRAM) ) { 
		$fill_table = mysql_query("INSERT INTO `". TBL_PROGRAM ."` (`name`, `abbreviation`, `online`, `role`)
			VALUES ". array_to_mysql_vals($programs)) or die(error_message("Failed to fill programs", mysql_error(), '26i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled programs!</p>"; }
	}

	/** Fill relationships with values from config file. */
	if( check_empty(TBL_RELATIONSHIP) ) { 
		$fill_table = mysql_query("INSERT INTO `". TBL_RELATIONSHIP ."` (`name`)
			VALUES ". array_to_mysql_vals($relationships)) or die(error_message("Failed to fill relationships", mysql_error(), '27i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled relationships!</p>"; }
	}

	/** Fill profiles if this is a demo. */
	if( $demo && check_empty(TBL_PROFILE) ) { 
		$fill_table = mysql_query("INSERT INTO `". TBL_PROFILE ."` (`id`, `username`, `md5_id`, `email`, `password`, `first_name`, `last_name`, `photo`, `enabled`, `user_level`, `position`, `date_created`, `ip_address`, `activation_code`, `session_key`, `session_time`, `num_logins`, `date_disabled`, `last_login`)
			VALUES
				(1,X'686A68756E74','c4ca4238a0b923820dcc509a6f75849b',X'A724D4F662393DCC302476FA714EE0EEB5518850FEA0D9C6B92DF45FBA9E274B','5af176b303c33d3ab88e38b6d0dae2deb919dba2','Hannah','Deering','http://www.vrac.iastate.edu//people/images/thm/hjhunt-thm.jpg',1,1,'student','2012-03-26 15:37:14','127.0.0.1',1593,'','',0,'0000-00-00 00:00:00','2012-03-26 15:41:17'),
				(2,X'707368696C6C','c81e728d9d4c2f636f067f89cc14862c',X'59A349361EEF038CA1B339250F373321B5518850FEA0D9C6B92DF45FBA9E274B','5af176b303c33d3ab88e38b6d0dae2deb919dba2','Pam','Shill','http://hci.iastate.edu/People/images/staff/pam_shill.jpg',1,5,'staff','2012-03-26 15:41:57','127.0.0.1',3601,'','',0,'0000-00-00 00:00:00','2012-03-26 15:41:57'),
				(3,X'7265696E68617274','eccbc87e4b5ce2fe28308fd9f2a7baf3',X'801C36FA0AA3BB8980BDD4F1F9B65AD426C156BE6B60CE38659C39D5AC103468','5af176b303c33d3ab88e38b6d0dae2deb919dba2','Jodi','Reinhart','http://hci.iastate.edu/People/images/staff/jodi_reinhart.jpg',1,1,'staff','2012-03-26 15:43:36','127.0.0.1',9704,'','',0,'0000-00-00 00:00:00','2012-03-26 15:43:36'),
				(4,X'67696C62657274','a87ff679a2f3e71d9181a67b7542122c',X'52C80B661E34F72844CA00A00BE5A0A30CC6E09F53779DE8617DBA7F1E24D48F','5af176b303c33d3ab88e38b6d0dae2deb919dba2','Stephen','Gilbert','http://hci.iastate.edu/media/People/images/faculty/gilbert.jpg',1,1,'faculty','2012-03-26 15:50:25','127.0.0.1',3982,'','',0,'0000-00-00 00:00:00','2012-03-26 15:50:25'),
				(5,X'6F6C69766572','e4da3b7fbbce2345d7772b0674a318d5',X'6422909922C86D995C4FA355AB8CCEC9B5518850FEA0D9C6B92DF45FBA9E274B','5af176b303c33d3ab88e38b6d0dae2deb919dba2','James','Oliver','http://hci.iastate.edu/media/People/images/faculty/oliver.jpg',1,1,'faculty','2012-03-26 15:53:27','127.0.0.1',2326,'','',0,'0000-00-00 00:00:00','2012-03-26 15:53:27'),
				(6,X'6577696E6572','1679091c5a880faf6fb5e6087eb1b2dc',X'C7AB55318E36A3E83194F8D2D94E881CB5518850FEA0D9C6B92DF45FBA9E274B','5af176b303c33d3ab88e38b6d0dae2deb919dba2','Eliot','Winer','http://hci.iastate.edu/media/People/images/faculty/ewiner.jpg',1,1,'faculty','2012-03-26 15:58:03','127.0.0.1',9761,'','',0,'0000-00-00 00:00:00','2012-03-26 15:58:03'),
				(7,X'6C75646D696C6172','8f14e45fceea167a5a36dedd4bea2543',X'0BAE4BB9AB054AAE235F364A467B7DBE26C156BE6B60CE38659C39D5AC103468','5af176b303c33d3ab88e38b6d0dae2deb919dba2','Ludmila','Rizshsky','http://www.vrac.iastate.edu/people/images/thm/ludmilar-thm.jpg',1,1,'visitor','2012-03-26 16:00:12','127.0.0.1',1776,'','',0,'0000-00-00 00:00:00','2012-03-26 16:00:12'),
				(8,X'677569646F7265','c9f0f895fb98ab9159f51fd0297e236d',X'1C34AA22C79A3A49F71F692087430D2C0CC6E09F53779DE8617DBA7F1E24D48F','5af176b303c33d3ab88e38b6d0dae2deb919dba2','Guido','Maria Re','',1,1,'visitor','2012-03-26 16:16:53','127.0.0.1',4540,'','',0,'0000-00-00 00:00:00','2012-03-26 16:16:53'),
				(9,X'6D6B6E69676874','45c48cce2e2d7fbdea1afc51c7c6ad26',X'A724D4F662393DCC302476FA714EE0EEB5518850FEA0D9C6B92DF45FBA9E274B','5af176b303c33d3ab88e38b6d0dae2deb919dba2','Melinda Cerney','Knight','http://hci.iastate.edu/People/images/alumni/Melinda--Cerney_Knight-40mecerney_216.jpg',1,1,'alumni','2012-03-26 16:25:55','127.0.0.1',8039,'','',0,'0000-00-00 00:00:00','2012-03-26 18:11:14'),
				(10,X'727369646861727461','d3d9446802a44259755d38e6d163e820',X'A724D4F662393DCC302476FA714EE0EEB5518850FEA0D9C6B92DF45FBA9E274B','5af176b303c33d3ab88e38b6d0dae2deb919dba2','Ronald','Sidharta','http://hci.iastate.edu/People/images/alumni/Ronald--Sidharta-11ronalds_417.jpg',1,1,'alumni','2012-03-26 16:30:27','127.0.0.1',3845,'','',0,'0000-00-00 00:00:00','2012-03-26 16:30:27'),
				(11,X'636777696E','6512bd43d9caa6e02c990b0a82652dca',X'C994B8CB03B0F184716F056A115F45A0DE60723BCC3CB114D1AA7E74DCC5492D','5af176b303c33d3ab88e38b6d0dae2deb919dba2','Corey','Gwin','http://www.vrac.iastate.edu/people/images/detail/cgwin-detail.jpg',1,1,'student','2012-03-26 16:42:24','127.0.0.1',9352,'','',0,'0000-00-00 00:00:00','2012-03-26 16:42:24'),
				(12,X'77696C6C69616D73','c20ad4d76fe97759aa27a0c99bff6710',X'CBA44657FC483CE3B68BE8FDB605072126C156BE6B60CE38659C39D5AC103468','5af176b303c33d3ab88e38b6d0dae2deb919dba2','William','Schneller','http://www.vrac.iastate.edu/people/images/detail/williams-detail.jpg',1,1,'student','2012-03-26 16:48:45','127.0.0.1',8516,'','',0,'0000-00-00 00:00:00','2012-03-26 16:48:45')") 
			or die(error_message("Failed to fill profiles", mysql_error(), '28i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled profiles!</p>"; }
		
		$fill_table = mysql_query("INSERT INTO `". TBL_ALUMNI ."` (`profile_id`, `program_id`, `department_id`, `comajor_department_id`, `title`, `company`, `company_city`, `state_id`, `country_id`, `dissertation_title`, `education`, `bio`, `start_date`, `grad_date`, `last_update`)
			VALUES
				(9,5,40,NULL,'Usability Engineer','Microsoft','Redmond',47,244,'From gesture recognition to functional motion analysis: Quantitative techniques for the application and evaluation of human motion','','Area of PhD research:  \r\nThe quantification and analysis of human motion is a central focus of many studies in biology, anthropology, biomechanics, human factors and ergonomics. These works are primarily concerned with describing the relationships between structure and function from a quantitative perspective. Recent work has seen the application of functional analysis techniques and their associated models to such diverse areas as surveillance, human computer interaction, and game development by researchers in the areas of computer graphics, robotics, computer vision, and machine learning The work in this dissertation is motivated by the study and quantification of gesture and human motion. This research explores the characteristics of gesture-based interactions, the development of a gesture recognition tool for virtual reality environments, the quantification and analysis of a sequence of postures as a complete motion, and the application of the motion analysis methods to a lifting and fatigue study. The result is a look at gesture and motion analysis from its role in interaction to its ability to quantify relationships between structure and function.','2004-08-01','2005-05-01','2012-03-26 17:13:16'),
				(10,3,52,NULL,'Equity Derivates Trading Developer','Goldman Sachs','Tokyo',NULL,121,'Augmented Tangible Interface for Design Review','','Area of MS research: \r\nTraditional design review uses \"old\" 2D interfaces such as a mouse and a keyboard to manipulate 3D objects. These 2D to 3D limitation is not desirable, especially in a design review meeting. I proposed a set of 3D tangible interfaces using augmented reality to help with CAD design review.\r\n\r\nWhat am I doing now? \r\nI am a developer in the Fixed Income division, developing application for Interest Rate Product trading.\r\n\r\nWhat HCI classes were invaluable to my success? \r\nHCI 575X, Virtual Reality Class, Open GL Class\r\n\r\nFavorite graduate school memory:\r\nVRAC potluck, Late night coding, Fun Co-workers / Staffs. Great bosses, etc.\r\n\r\nHCI issues that interest me today:\r\nCurrently I am interested in Finance, equities trading, so things related to that.\r\n\r\nA website I recommend: \r\nFinance.google.com \r\n\r\nWhy?\r\ngood HCI design for lots of information','2004-08-01','2005-05-01','2012-03-26 18:37:14')") 
			or die(error_message("Failed to fill alumni", mysql_error(), '29i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled alumni!</p>"; }
	
		$fill_table = mysql_query("INSERT INTO `". TBL_FACULTY ."` (`profile_id`, `title`, `department_id`, `phone`, `office_location`, `education`, `bio`, `start_date`, `last_update`)
			VALUES
				(4,'HCI &amp; VRAC Associate Director',67,'515.294.6782','1620 Howe Hall','PhD, Brain &amp; Cognitive Sciences,  Massachusetts Institute of Technology (1997)\r\nBSE, Civil Engineering &amp; Operations Research,  Princeton University (1992)','Interests and/or Research Focus: E-learning design and development, software usability, user-centered design, instructional technologies, distance education','2007-08-01','2012-03-26 15:50:25'),
				(5,'HCI &amp; VRAC Director',40,'','','PhD, Michigan State University (1986)','A wide array of human computer interaction technologies, encompassing computer graphics, geometric modeling, virtual reality, and collaborative networks for applications in product development and complex system operation.','2004-09-01','2012-03-26 15:55:05'),
				(6,'HCI &amp;amp; VRAC Associate Director',40,'515-322-1120','1620 Howe Hall','PhD, Mechanical Engineering, State University of New York at Buffalo (1999)\r\nMS, Mechanical Engineering, State University of New York at Buffalo (1994)\r\nBS, Aeronautical and Astronautical Engineering, The Ohio State University (1992)','Interests and/or Research Focus:Internet Technology for Large-Scale Collaborative Design, Medical Imaging, Analysis, and Visualization, Multidisciplinary Design Synthesis, Computer Aided Design and Graphics, Applications in Optimal Design, Scientific Visualization and Virtual Reality Modeling for Large-Scale Design.','2007-09-01','2012-03-26 15:58:03')") 
			or die(error_message("Failed to fill faculty", mysql_error(), '30i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled faculty!</p>"; }

		$fill_table = mysql_query("INSERT INTO `". TBL_STAFF ."` (`profile_id`, `title`, `phone`, `office_location`, `bio`, `start_date`, `last_update`)
		VALUES
			(2,'Program Coordinator','515-294-2089','1620 Howe Hall','','2004-01-01','2012-03-26 15:41:57'),
			(3,'Program Coordinator','515-294-3093','1620 Howe Hall','','2008-05-01','2012-03-26 15:43:36')") 
			or die(error_message("Failed to fill staff", mysql_error(), '31i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled staff!</p>"; }
		
		$fill_table = mysql_query("INSERT INTO `". TBL_STUDENT ."` (`profile_id`, `program_id`, `department_id`, `comajor_department_id`, `phone`, `office_location`, `title`, `company`, `home_city`, `state_id`, `country_id`, `education`, `bio`, `start_date`, `grad_date`, `last_update`, `admission_status`)
		VALUES
			(1,3,28,NULL,'612-207-0700','2nd Floor VRAC, southeast corner','','','Minneapolis',23,244,'BS, Computer Science, Iowa State University (2011)\r\nBA, Art &amp; Design, Iowa State University (2011)','Originally from Minneapolis, Hannah received two Bachelor\'s degrees in Computer Science and Art &amp; Design from Iowa State. She worked in the VRAC\'s multitouch group during her undergraduate studies, and is now returning to ISU to obtain a Master\'s degree in HCI. Her research interests include software interface &amp; interaction design, usability, requirements gathering, and digital instruction.','2011-08-01','2013-05-01','2012-03-26 17:14:41',0),
			(11,4,67,NULL,'','','','Northrop Grumman Tactical Unmanned Systems','San Diego',5,244,'BS, Mechanical Engineering, California Polytechnic State University (2009)','Corey is from the small Central Valley dairy town of Hilmar, California. He received his BS in Mechanical Engineering from the California Polytechnic State University, San Luis Obispo in 2009 focusing primarily in mechatronics. Currently, he is employed by Northrop Grumman Tactical Unmanned Systems in San Diego, California. There he works as a payload and weapon systems integration engineer providing state-of-the-art reconnaissance,  surveillance, and target acquisition solutions for unmanned platforms. With his passion for new technology, Corey hopes to integrate new computer and sensor technologies into the human experience, advancing our capabilities, ensuring our safety, and allowing us to further interact with the world in ways we previously could not. His aim is to move technology away from one\'s fingertips and instead into one \"natural existence\" where the smarts are not in a device, but instead in one\'s self. Corey will complete his MS in Human Computer Interaction at Iowa State University in 2013. He enjoys playing his guitar, running, backpacking, hiking, and any given weekend you can find him atop a peak hunting treasure with this GPS receiver geocaching.','2012-01-01','2013-05-01','2012-03-26 18:32:03',0),
			(12,1,30,NULL,'','','','','',NULL,244,'','William works with Eve Wurtele. He is expecting to graduate in 2010 with his BFA in Integrated Studio Art with a focus on computer animation and modeling. He has chosen this degree because he has a passion for both art and technology. Integrated Studio Art provides him with a chance to blend his own creativity with cutting edge technology. ','2009-04-01','2012-01-01','2012-03-26 17:14:11',0)") 
			or die(error_message("Failed to fill students", mysql_error(), '32i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled students!</p>"; }

		$fill_table = mysql_query("INSERT INTO `". TBL_VISITOR ."` (`profile_id`, `title`, `department_id`, `phone`, `office_location`, `education`, `bio`, `start_date`, `last_update`)
		VALUES
			(7,'Assistant Scientist II',50,'','','','','2012-01-01','2012-03-26 16:00:12'),
			(8,'Visiting Scholar',NULL,'','','','','2012-01-01','2012-03-26 16:16:53')") 
			or die(error_message("Failed to fill visitors", mysql_error(), '33i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled visitors!</p>"; }

		if($debug){ echo "<p class=\"success message\">Finished install.</p>"; }
	}

} else { // If this is an uninstall
	if($debug){ echo "<p class=\"success message\">Finished uninstall.</p>"; }
}


?>
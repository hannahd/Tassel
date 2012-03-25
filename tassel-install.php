<?php
/**
 * Installer for Tassel.
 *
 * This file sets up the MySQL database and fills it with values from 
 * the config file. 
 *
 * Error codes starting with 1. 
 *
 * @author Hannah Deering
 * @package Tassel
 **/

require_once ('constants/constants.php'); // Includes database details
require_once (ROOT.'/tassel-config.php');
require_once (ROOT.'/constants/functions.php');
require_once (ROOT.'/constants/dbconnect.php');

$reset_all = true;


/* Reset Database
 * ===================================================================*/

if($reset_all){                                     
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_PROFILE_GROUP_MAP);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_PROFILE_INTEREST_MAP);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_PROFILE_LINK_MAP);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_PROFILE_PROFILE_MAP);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_ALUMNI);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_FACULTY);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_STAFF);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_STUDENT);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_VISITOR);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_DEPARTMENT);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_COLLEGE);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_COUNTRY);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_GROUP);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_INTEREST);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_LINK);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_PROFILE);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_PROGRAM);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_RELATIONSHIP);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_UPDATE);
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_US_STATE);
}


/* Create Core Tables
 * ===================================================================*/

/** College Table. */
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_COLLEGE ."` (
  	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(220) NOT NULL DEFAULT '',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create college table", mysql_error(), 10));

/** Country Table. */
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_COUNTRY ."` (
  	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(220) NOT NULL DEFAULT '',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create country table", mysql_error(), 11));

/** Department Table. */
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_DEPARTMENT ."` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(220) NOT NULL DEFAULT '',
  `college_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `department-college` (`college_id`),
  CONSTRAINT `department-college` FOREIGN KEY (`college_id`) REFERENCES `". TBL_COLLEGE ."` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create department table", mysql_error(), 12));

/** Group Table. */
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_GROUP ."` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(220) NOT NULL DEFAULT '',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create group table", mysql_error(), 13));

/** Interest Table. */
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_INTEREST ."` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(220) NOT NULL DEFAULT '',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create interest table", mysql_error(), 14));

/** Link Table. */
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_LINK ."` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(220) NOT NULL DEFAULT '',
	  `url` varchar(220) NOT NULL DEFAULT '',
	  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create link table", mysql_error(), 15));

/** Program Table. */
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_PROGRAM ."` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
	  `abbreviation` varchar(11) CHARACTER SET latin1 DEFAULT NULL,
	  `online` tinyint(1) unsigned DEFAULT '0',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create program table", mysql_error(), 16));

/** Relationship Table. */
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_RELATIONSHIP ."` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(220) CHARACTER SET latin1 NOT NULL DEFAULT '',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create relationship table", mysql_error(), 17));

/** US State Table. */
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `". TBL_US_STATE ."` (
  	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(220) NOT NULL,
	  `abbreviation` varchar(2) NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create US state table", mysql_error(), 18));



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
	  `approved` int(1) NOT NULL DEFAULT '0',
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
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile table", mysql_error(), 19));


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
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create faculty table", mysql_error(), 111));

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
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create staff table", mysql_error(), 112));

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
	  `last_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
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
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create student table", mysql_error(), 113));

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
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create update table", mysql_error(), 114));


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
	  `last_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
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
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create alumni table", mysql_error(), 110));



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
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile-group map table", mysql_error(), 115));

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
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile-interest map table", mysql_error(), 116));

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
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile-link map table", mysql_error(), 117));
	
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
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile-profile map table", mysql_error(), 118));


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
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create update table", mysql_error(), 119));

/*
/* Fill Tables
 * ===================================================================*/

/** Fill colleges with values from config file. */
if( check_empty(TBL_COLLEGE) ) { 
	$fill_table = mysql_query("INSERT INTO `". TBL_COLLEGE ."` (`name`)
		VALUES ". array_to_mysql_vals($colleges)) or die(error_message("Failed to fill colleges", mysql_error(), 120));
	if($debug){ echo "<p class=\"success message\">Filled colleges!</p>"; }
}

/** Fill departments with values from config file. */
if( check_empty(TBL_DEPARTMENT) ) { 
	$fill_table = mysql_query("INSERT INTO `". TBL_DEPARTMENT. "` (`name`, `college_id`)
		VALUES ". array_to_mysql_vals($departments, true)) or die(error_message("Failed to fill departments", mysql_error(), 121));
	if($debug){ echo "<p class=\"success message\">Filled departments!</p>"; }
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
		('Utah','UT'), ('Vermont','VT'), ('Virginia','VA'), ('Washington','WA'), ('West Virginia','WV'), ('Wisconsin','WI')") or die(error_message("Failed to fill states", mysql_error(), 122));
	if($debug){ echo "<p class=\"success message\">Filled states!</p>"; }
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
		('Zimbabwe')") or die(error_message("Failed to fill colleges", mysql_error(), 123));
	if($debug){ echo "<p class=\"success message\">Filled countries!</p>"; }
}

/** Fill groups with values from config file. */
if( check_empty(TBL_GROUP) ) { 
	$fill_table = $fill_table = mysql_query("INSERT INTO `". TBL_GROUP ."` (`name`)
		VALUES ". array_to_mysql_vals($groups)) or die(error_message("Failed to fill groups", mysql_error(), 124)); 
	if($debug){ echo "<p class=\"success message\">Filled group!</p>"; }
}

/** Fill programs with values from config file. */
if( check_empty(TBL_PROGRAM) ) { 
	$fill_table = $fill_table = mysql_query("INSERT INTO `". TBL_PROGRAM ."` (`name`, `abbreviation`, `online`)
		VALUES ". array_to_mysql_vals($programs)) or die(error_message("Failed to fill programs", mysql_error(), 125));
	if($debug){ echo "<p class=\"success message\">Filled programs!</p>"; }
}

/** Fill relationships with values from config file. */
if( check_empty(TBL_RELATIONSHIP) ) { 
	$fill_table = $fill_table = mysql_query("INSERT INTO `". TBL_RELATIONSHIP ."` (`name`)
		VALUES ". array_to_mysql_vals($relationships)) or die(error_message("Failed to fill relationships", mysql_error(), 126));
	if($debug){ echo "<p class=\"success message\">Filled relationships!</p>"; }
}

if($debug){ echo "<p class=\"success message\">Finished install.</p>"; }
?>
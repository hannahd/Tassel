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
global $local;
if($local){
	require_once (ROOT.'/tassel_config.php');
	require_once (ROOT.'/constants/functions.php');
	require_once (ROOT.'/constants/dbconnect.php');
	require_once (ROOT."/constants/access_functions.php"); //Includes functions to control user privileges
} else{
	require_once ('tassel_config.php');
	require_once ('constants/functions.php');
	require_once ('constants/dbconnect.php');
	require_once ('constants/access_functions.php'); //Includes functions to control user privileges
}

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
	$drop_table = mysql_query("DROP TABLE IF EXISTS ". TBL_DETAILS)				 or die(error_message("Failed to drop table", mysql_error(), '1i'));
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
		  `en_email` longblob NOT NULL DEFAULT '',
		  `md5_id` varchar(200) NOT NULL DEFAULT '',
		  `password` varchar(220) NOT NULL DEFAULT '',
		  `enabled` tinyint(1) NOT NULL DEFAULT '1',
		  `user_level` tinyint(4) NOT NULL DEFAULT '1',
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

	
	/** Details Table. */
	$create_table = mysql_query("CREATE TABLE  IF NOT EXISTS `". TBL_DETAILS ."` (
	  `profile_id` bigint(20) unsigned NOT NULL,
	  `first_name` varchar(220) NOT NULL,
	  `last_name` varchar(220) NOT NULL,
	  `photo` varchar(220) DEFAULT '',
	  `position` enum('unknown','faculty','staff','alumni','student','visitor') NOT NULL DEFAULT 'unknown',
	  `program_id` int(11) unsigned DEFAULT NULL,
	  `department_id` int(11) unsigned DEFAULT NULL,
	  `comajor_program_id` int(11) unsigned DEFAULT NULL,
	  `comajor_department_id` int(11) unsigned DEFAULT NULL,
	  `en_email` longblob,
	  `phone` varchar(20) DEFAULT NULL,
	  `office_location` varchar(220) DEFAULT NULL,
	  `title` varchar(220) DEFAULT NULL,
	  `company` varchar(220) DEFAULT NULL,
	  `city` varchar(220) DEFAULT NULL,
	  `state_id` int(11) unsigned DEFAULT NULL,
	  `country_id` int(11) unsigned DEFAULT NULL,
	  `dissertation_title` varchar(220) DEFAULT NULL,
	  `education` text,
	  `bio` text,
	  `start_date` date NOT NULL,
	  `grad_date` date DEFAULT NULL,
	  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	  `admission_status` tinyint(1) DEFAULT NULL,
	  PRIMARY KEY (`profile_id`),
	  KEY `program` (`program_id`),
	  KEY `comajor-program` (`comajor_program_id`),
	  KEY `department` (`department_id`),
	  KEY `comajor-department` (`comajor_department_id`),
	  KEY `state` (`state_id`),
	  KEY `country` (`country_id`),
	  CONSTRAINT `comajor-department` FOREIGN KEY (`comajor_department_id`) REFERENCES `tassel_program` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
	  CONSTRAINT `comajor-program` FOREIGN KEY (`comajor_program_id`) REFERENCES `tassel_program` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
	  CONSTRAINT `country` FOREIGN KEY (`country_id`) REFERENCES `tassel_country` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
	  CONSTRAINT `department` FOREIGN KEY (`department_id`) REFERENCES `tassel_department` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
	  CONSTRAINT `profile-details` FOREIGN KEY (`profile_id`) REFERENCES `tassel_profile` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
	  CONSTRAINT `program` FOREIGN KEY (`program_id`) REFERENCES `tassel_program` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
	  CONSTRAINT `state` FOREIGN KEY (`state_id`) REFERENCES `tassel_us_state` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create faculty table", mysql_error(), '11i'));

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
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile-group map table", mysql_error(), '12i'));

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
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile-interest map table", mysql_error(), '13i'));

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
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile-link map table", mysql_error(), '14i'));
	
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
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create profile-profile map table", mysql_error(), '15i'));


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
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die(error_message("Failed to create update table", mysql_error(), '16i'));

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
		$fill_table = mysql_query("INSERT INTO `".TBL_INTEREST."` (`id`, `name`)
			VALUES
				(1,'communication technology'),
				(2,'biomedical imaging'),
				(3,'signal & image processing'),
				(4,'pattern recognition'),
				(5,'data visualization'),
				(6,'software development'),
				(7,'social psychology'),
				(8,'personality psychology'),
				(9,'cognitive psychology'),
				(10,'visual communication'),
				(11,'user interface design'),
				(12,'product/ industrial design'),
				(13,'web design'),
				(14,'web development'),
				(15,'music composition tools'),
				(16,'animation'),
				(17,'3D modeling'),
				(18,'instructional & curriculum design'),
				(19,'education'),
				(20,'virtual reality'),
				(21,'augmented reality'),
				(22,'artificial intelligence'),
				(23,'requirements engineering'),
				(24,'project management'),
				(25,'collaborative technology'),
				(26,'multi-touch technology'),
				(27,'object recognition'),
				(28,'face recognition'),
				(29,'user centered design'),
				(30,'usability'),
				(31,'experience design'),
				(32,'instructional technology'),
				(33,'illustration'),
				(34,'mobile application development'),
				(35,'information technology'),
				(36,'new media journalism'),
				(37,'haptics'),
				(38,'advertising & marketing'),
				(39,'developmental psychology'),
				(40,'health care & medicine'),
				(41,'game design'),
				(42,'new media psychology'),
				(43,'online learning & distance education'),
				(44,'geostatistics'),
				(45,'statistics'),
				(46,'science education'),
				(47,'design education'),
				(48,'agriculture education'),
				(49,'language education'),
				(50,'mathmatics education'),
				(51,'history education'),
				(52,'interaction design'),
				(53,'environmental studies'),
				(54,'photography'),
				(55,'digital imaging'),
				(56,'computer graphics'),
				(57,'computational biology'),
				(58,'machine learning'),
				(59,'data mining'),
				(60,'neuroscience'),
				(62,'robotics'),
				(63,'distributed systems'),
				(64,'parallel computing'),
				(65,'sound synthesis design'),
				(66,'music analysis'),
				(67,'typography'),
				(68,'print design'),
				(69,'identity & branding'),
				(70,'navigation'),
				(71,'textile design'),
				(72,'functional clothing'),
				(73,'gerontology (elderly)'),
				(74,'sustainable design'),
				(75,'materials theory'),
				(76,'controls'),
				(77,'behavioral psychology'),
				(78,'ethics'),
				(79,'law'),
				(80,'virtual worlds'),
				(81,'group dynamics'),
				(82,'organizational memory'),
				(83,'database design'),
				(84,'accessible/ universal design'),
				(85,'entrepreneurship'),
				(86,'participatory design'),
				(87,'pedology (children)'),
				(88,'assistive technology'),
				(89,'aerospace/ aviation'),
				(90,'privacy'),
				(91,'security'),
				(92,'ubiquitous computing'),
				(93,'social filtering'),
				(94,'safety/ risk management'),
				(95,'help systems'),
				(96,'e-books & e-readers'),
				(97,'social media'),
				(98,'gender studies'),
				(99,'globalization/ internationalization'),
				(100,'information retrieval'),
				(101,'input devices'),
				(102,'sociology'),
				(103,'library studies'),
				(104,'music'),
				(105,'prototyping'),
				(106,'survey/ questionnaire design'),
				(107,'adaptive systems'),
				(108,'animal health'),
				(109,'food safety'),
				(110,'nutrition'),
				(111,'geometric modeling'),
				(112,'information design'),
				(113,'heuristics'),
				(114,'politics'),
				(115,'machine perception'),
				(116,'computer vision'),
				(117,'agricultural automation'),
				(118,'economics'),
				(119,'economic networks'),
				(120,'social networks'),
				(121,'matematics'),
				(122,'econometrics'),
				(123,'environmental modeling'),
				(124,'geographic information systems'),
				(125,'forensics'),
				(126,'e-commerce'),
				(127,'human resource information systems'),
				(128,'medical imaging'),
				(129,'multidisciplinary collaboration'),
				(130,'computer aided design'),
				(131,'performance evaluation'),
				(132,'operating systems'),
				(133,'genetics'),
				(134,'materials dynamics'),
				(135,'nondestructive evaluation'),
				(136,'interior design'),
				(137,'architecture'),
				(138,'communication'),
				(139,'information architecture'),
				(140,'gesture recognition'),
				(141,'journalism')")
			or die(error_message("Failed to fill profiles", mysql_error(), '28i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled interests!</p>"; }
	
		 
		$fill_table = mysql_query("INSERT INTO `". TBL_PROFILE ."` (`id`, `md5_id`, `en_email`, `password`, `enabled`, `user_level`, `date_created`, `ip_address`, `activation_code`, `session_key`, `session_time`, `num_logins`, `date_disabled`, `last_login`)
		VALUES
			(1,'c4ca4238a0b923820dcc509a6f75849b',X'A724D4F662393DCC302476FA714EE0EEB5518850FEA0D9C6B92DF45FBA9E274B','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-26 15:37:14','127.0.0.1',1593,'','',3,'0000-00-00 00:00:00','2012-03-30 18:06:44'),
			(2,'c81e728d9d4c2f636f067f89cc14862c',X'59A349361EEF038CA1B339250F373321B5518850FEA0D9C6B92DF45FBA9E274B','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,5,'2012-03-26 15:41:57','127.0.0.1',3601,'zg5u100','1333149109',1,'0000-00-00 00:00:00','2012-03-30 18:11:49'),
			(3,'eccbc87e4b5ce2fe28308fd9f2a7baf3',X'801C36FA0AA3BB8980BDD4F1F9B65AD426C156BE6B60CE38659C39D5AC103468','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-26 15:43:36','127.0.0.1',9704,'','',0,'0000-00-00 00:00:00','2012-03-26 15:43:36'),
			(4,'a87ff679a2f3e71d9181a67b7542122c',X'52C80B661E34F72844CA00A00BE5A0A30CC6E09F53779DE8617DBA7F1E24D48F','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-26 15:50:25','127.0.0.1',3982,'','',0,'0000-00-00 00:00:00','2012-03-26 15:50:25'),
			(5,'e4da3b7fbbce2345d7772b0674a318d5',X'6422909922C86D995C4FA355AB8CCEC9B5518850FEA0D9C6B92DF45FBA9E274B','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-26 15:53:27','127.0.0.1',2326,'','',0,'0000-00-00 00:00:00','2012-03-26 15:53:27'),
			(6,'1679091c5a880faf6fb5e6087eb1b2dc',X'C7AB55318E36A3E83194F8D2D94E881CB5518850FEA0D9C6B92DF45FBA9E274B','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-26 15:58:03','127.0.0.1',9761,'','',0,'0000-00-00 00:00:00','2012-03-26 15:58:03'),
			(7,'8f14e45fceea167a5a36dedd4bea2543',X'0BAE4BB9AB054AAE235F364A467B7DBE26C156BE6B60CE38659C39D5AC103468','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-26 16:00:12','127.0.0.1',1776,'','',0,'0000-00-00 00:00:00','2012-03-26 16:00:12'),
			(8,'c9f0f895fb98ab9159f51fd0297e236d',X'1C34AA22C79A3A49F71F692087430D2C0CC6E09F53779DE8617DBA7F1E24D48F','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-26 16:16:53','127.0.0.1',4540,'','',0,'0000-00-00 00:00:00','2012-03-26 16:16:53'),
			(9,'45c48cce2e2d7fbdea1afc51c7c6ad26',X'5A64AB878F3B5C0A1905073255F69C5D0CC6E09F53779DE8617DBA7F1E24D48F','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-26 16:25:55','127.0.0.1',8039,'','',1,'0000-00-00 00:00:00','2012-03-30 19:01:03'),
			(10,'d3d9446802a44259755d38e6d163e820',X'52984F8809860BD1DAA624AE8AF82CB51927E0B2D233D1FF541918A31A50E0FE','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-26 16:30:27','127.0.0.1',3845,'pbx85u9','1333152078',1,'0000-00-00 00:00:00','2012-03-30 19:01:28'),
			(11,'6512bd43d9caa6e02c990b0a82652dca',X'C994B8CB03B0F184716F056A115F45A0DE60723BCC3CB114D1AA7E74DCC5492D','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-26 16:42:24','127.0.0.1',9352,'','',0,'0000-00-00 00:00:00','2012-03-26 16:42:24'),
			(12,'c20ad4d76fe97759aa27a0c99bff6710',X'CBA44657FC483CE3B68BE8FDB605072126C156BE6B60CE38659C39D5AC103468','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-26 16:48:45','127.0.0.1',8516,'','',0,'0000-00-00 00:00:00','2012-03-26 16:48:45'),
			(13,'c51ce410c124a10e0db5e4b97fc2af39',X'8E3A58A8781B2D60FF126C1BDAF178F00CC6E09F53779DE8617DBA7F1E24D48F','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-30 18:15:40','127.0.0.1',1430,'','',0,'0000-00-00 00:00:00','2012-03-30 18:15:40'),
			(14,'aab3238922bcc25a6f606eb525ffdc56',X'7439F688854A62EBB3B9CAC4F130D79AB5518850FEA0D9C6B92DF45FBA9E274B','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-30 18:18:08','127.0.0.1',8130,'','',0,'0000-00-00 00:00:00','2012-03-30 18:18:08'),
			(15,'9bf31c7ff062936a96d3c8bd1f8f2ff3',X'5AB02E7A534C314630E1A5628FA526D026C156BE6B60CE38659C39D5AC103468','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-30 18:21:24','127.0.0.1',8381,'','',0,'0000-00-00 00:00:00','2012-03-30 18:21:24'),
			(16,'c74d97b01eae257e44aa9d5bade97baf',X'257670902398CFBEC62D2C78C56E911726C156BE6B60CE38659C39D5AC103468','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-30 18:24:41','127.0.0.1',9234,'','',0,'0000-00-00 00:00:00','2012-03-30 18:24:41'),
			(17,'70efdf2ec9b086079795c442636b55fb',X'6E16D3338BB1BE2CAE22FEF967D6823BB5518850FEA0D9C6B92DF45FBA9E274B','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-30 18:34:08','127.0.0.1',2642,'','',0,'0000-00-00 00:00:00','2012-03-30 18:34:08'),
			(18,'6f4922f45568161a8cdf4ad2299f6d23',X'A97D08477377953E5585D8553213C00B26C156BE6B60CE38659C39D5AC103468','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-30 18:38:15','127.0.0.1',6003,'','',0,'0000-00-00 00:00:00','2012-03-30 18:38:15'),
			(19,'1f0e3dad99908345f7439f8ffabdffc4',X'FD9D3A3C084E996A6D0BABCC8CEBDA760CC6E09F53779DE8617DBA7F1E24D48F','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-30 18:45:58','127.0.0.1',8894,'','',0,'0000-00-00 00:00:00','2012-03-30 18:45:58'),
			(20,'98f13708210194c475687be6106a3b84',X'5FE916103CB01F3CC8FA4716311A2E8E26C156BE6B60CE38659C39D5AC103468','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-30 18:49:05','127.0.0.1',6684,'','',0,'0000-00-00 00:00:00','2012-03-30 18:49:05'),
			(21,'3c59dc048e8850243be8079a5c74d079',X'3DFE7E35C097F109F96F3E75F6167539168C728995C2800F5F4DC64673E142E3','5af176b303c33d3ab88e38b6d0dae2deb919dba2',1,1,'2012-03-30 18:52:29','127.0.0.1',1066,'','',0,'0000-00-00 00:00:00','2012-03-30 18:52:29')") 
			or die(error_message("Failed to fill profiles", mysql_error(), '29i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled profiles!</p>"; }
		
		$fill_table = mysql_query("INSERT INTO `". TBL_DETAILS ."` (`profile_id`, `first_name`, `last_name`, `photo`, `position`, `program_id`, `department_id`, `comajor_program_id`, `comajor_department_id`, `en_email`, `phone`, `office_location`, `title`, `company`, `city`, `state_id`, `country_id`, `dissertation_title`, `education`, `bio`, `start_date`, `grad_date`, `last_update`, `admission_status`)
		VALUES
			(1,'Hannah','Deering','http://www.vrac.iastate.edu/people/images/thm/hjhunt-thm.jpg ','student',3,28,NULL,NULL,X'A724D4F662393DCC302476FA714EE0EEB5518850FEA0D9C6B92DF45FBA9E274B','612-207-0700','2nd Floor VRAC, southeast corner','','','Minneapolis',23,244,NULL,'BS, Computer Science, Iowa State University (2011)\r\nBA, Art &amp; Design, Iowa State University (2011)','Originally from Minneapolis, Hannah received two Bachelor\'s degrees in Computer Science and Art &amp; Design from Iowa State. She worked in the VRAC\'s multitouch group during her undergraduate studies, and is now returning to ISU to obtain a Master\'s degree in HCI. Her research interests include software interface &amp; interaction design, usability, requirements gathering, and digital instruction.','2011-08-01','2013-05-01','2012-04-03 21:54:50',0),
			(2,'Pam','Shill','http://hci.iastate.edu/People/images/staff/pam_shill.jpg','staff',NULL,NULL,NULL,NULL,X'59A349361EEF038CA1B339250F373321B5518850FEA0D9C6B92DF45FBA9E274B','515-294-2089','1620 Howe Hall','Program Coordinator',NULL,NULL,NULL,NULL,NULL,NULL,'','2004-01-01',NULL,'2012-04-03 22:02:53',NULL),
			(3,'Jodi','Reinhart','http://hci.iastate.edu/People/images/staff/jodi_reinhart.jpg','staff',NULL,NULL,NULL,NULL,X'801C36FA0AA3BB8980BDD4F1F9B65AD426C156BE6B60CE38659C39D5AC103468','515-294-3093','1620 Howe Hall','Program Coordinator',NULL,NULL,NULL,NULL,NULL,NULL,'','2008-05-01',NULL,'2012-04-03 22:02:53',NULL),
			(4,'Stephen','Gilbert','http://hci.iastate.edu/media/People/images/faculty/gilbert.jpg','faculty',NULL,67,NULL,NULL,X'52C80B661E34F72844CA00A00BE5A0A30CC6E09F53779DE8617DBA7F1E24D48F','515.294.6782','1620 Howe Hall','HCI &amp; VRAC Associate Director',NULL,NULL,NULL,NULL,NULL,'PhD, Brain &amp; Cognitive Sciences,  Massachusetts Institute of Technology (1997)\r\nBSE, Civil Engineering &amp; Operations Research,  Princeton University (1992)','Interests and/or Research Focus: E-learning design and development, software usability, user-centered design, instructional technologies, distance education','2007-08-01',NULL,'2012-04-03 22:02:53',NULL),
			(5,'James','Oliver','http://hci.iastate.edu/media/People/images/faculty/oliver.jpg','faculty',NULL,40,NULL,NULL,X'6422909922C86D995C4FA355AB8CCEC9B5518850FEA0D9C6B92DF45FBA9E274B','','','HCI &amp; VRAC Director',NULL,NULL,NULL,NULL,NULL,'PhD, Michigan State University (1986)','A wide array of human computer interaction technologies, encompassing computer graphics, geometric modeling, virtual reality, and collaborative networks for applications in product development and complex system operation.','2004-09-01',NULL,'2012-04-03 22:02:53',NULL),
			(6,'Eliot','Winer','http://hci.iastate.edu/media/People/images/faculty/ewiner.jpg','faculty',NULL,40,NULL,NULL,X'C7AB55318E36A3E83194F8D2D94E881CB5518850FEA0D9C6B92DF45FBA9E274B','515-322-1120','1620 Howe Hall','HCI &amp;amp; VRAC Associate Director',NULL,NULL,NULL,NULL,NULL,'PhD, Mechanical Engineering, State University of New York at Buffalo (1999)\r\nMS, Mechanical Engineering, State University of New York at Buffalo (1994)\r\nBS, Aeronautical and Astronautical Engineering, The Ohio State University (1992)','Interests and/or Research Focus:Internet Technology for Large-Scale Collaborative Design, Medical Imaging, Analysis, and Visualization, Multidisciplinary Design Synthesis, Computer Aided Design and Graphics, Applications in Optimal Design, Scientific Visualization and Virtual Reality Modeling for Large-Scale Design.','2007-09-01',NULL,'2012-04-03 22:02:53',NULL),
			(7,'Ludmila','Rizshsky','http://www.vrac.iastate.edu/people/images/thm/ludmilar-thm.jpg','visitor',NULL,50,NULL,NULL,X'0BAE4BB9AB054AAE235F364A467B7DBE26C156BE6B60CE38659C39D5AC103468','','','Assistant Scientist II',NULL,NULL,NULL,NULL,NULL,'','','2012-01-01',NULL,'2012-04-03 22:02:53',NULL),
			(8,'Guido','Maria Re','','visitor',NULL,NULL,NULL,NULL,X'1C34AA22C79A3A49F71F692087430D2C0CC6E09F53779DE8617DBA7F1E24D48F','','','Visiting Scholar',NULL,NULL,NULL,NULL,NULL,'','','2012-01-01',NULL,'2012-04-03 22:02:53',NULL),
			(9,'Melinda Cerney','Knight','http://hci.iastate.edu/People/images/alumni/Melinda--Cerney_Knight-40mecerney_216.jpg ','alumni',5,40,NULL,NULL,X'5A64AB878F3B5C0A1905073255F69C5D0CC6E09F53779DE8617DBA7F1E24D48F',NULL,NULL,'Usability Engineer','Microsoft','Redmond',47,244,'From gesture recognition to functional motion analysis: Quantitative techniques for the application and evaluation of human motion','','Area of PhD research:  \r\nThe quantification and analysis of human motion is a central focus of many studies in biology, anthropology, biomechanics, human factors and ergonomics. These works are primarily concerned with describing the relationships between structure and function from a quantitative perspective. Recent work has seen the application of functional analysis techniques and their associated models to such diverse areas as surveillance, human computer interaction, and game development by researchers in the areas of computer graphics, robotics, computer vision, and machine learning The work in this dissertation is motivated by the study and quantification of gesture and human motion. This research explores the characteristics of gesture-based interactions, the development of a gesture recognition tool for virtual reality environments, the quantification and analysis of a sequence of postures as a complete motion, and the application of the motion analysis methods to a lifting and fatigue study. The result is a look at gesture and motion analysis from its role in interaction to its ability to quantify relationships between structure and function.','2004-08-01','2005-05-01','2012-04-03 22:02:53',NULL),
			(10,'Ronald','Sidharta','http://hci.iastate.edu/People/images/alumni/Ronald--Sidharta-11ronalds_417.jpg ','alumni',3,52,NULL,NULL,X'52984F8809860BD1DAA624AE8AF82CB51927E0B2D233D1FF541918A31A50E0FE',NULL,NULL,'Equity Derivates Trading Developer','Goldman Sachs','Tokyo',NULL,121,'Augmented Tangible Interface for Design Review','','Area of MS research: \r\nTraditional design review uses &quot;old&quot; 2D interfaces such as a mouse and a keyboard to manipulate 3D objects. These 2D to 3D limitation is not desirable, especially in a design review meeting. I proposed a set of 3D tangible interfaces using augmented reality to help with CAD design review.\r\n\r\nWhat am I doing now? \r\nI am a developer in the Fixed Income division, developing application for Interest Rate Product trading.\r\n\r\nWhat HCI classes were invaluable to my success? \r\nHCI 575X, Virtual Reality Class, Open GL Class\r\n\r\nFavorite graduate school memory:\r\nVRAC potluck, Late night coding, Fun Co-workers / Staffs. Great bosses, etc.\r\n\r\nHCI issues that interest me today:\r\nCurrently I am interested in Finance, equities trading, so things related to that.\r\n\r\nA website I recommend: \r\nFinance.google.com \r\n\r\nWhy?\r\ngood HCI design for lots of information','2004-08-01','2005-05-01','2012-04-03 22:02:53',NULL),
			(11,'Corey','Gwin','http://www.vrac.iastate.edu/people/images/detail/cgwin-detail.jpg','student',4,67,NULL,NULL,X'C994B8CB03B0F184716F056A115F45A0DE60723BCC3CB114D1AA7E74DCC5492D','','','payload and weapon systems integration engineer','Northrop Grumman Tactical Unmanned Systems','San Diego',5,244,NULL,'BS, Mechanical Engineering, California Polytechnic State University (2009)','Corey is from the small Central Valley dairy town of Hilmar, California. He received his BS in Mechanical Engineering from the California Polytechnic State University, San Luis Obispo in 2009 focusing primarily in mechatronics. Currently, he is employed by Northrop Grumman Tactical Unmanned Systems in San Diego, California. There he works as a payload and weapon systems integration engineer providing state-of-the-art reconnaissance,  surveillance, and target acquisition solutions for unmanned platforms. With his passion for new technology, Corey hopes to integrate new computer and sensor technologies into the human experience, advancing our capabilities, ensuring our safety, and allowing us to further interact with the world in ways we previously could not. His aim is to move technology away from one\'s fingertips and instead into one \"natural existence\" where the smarts are not in a device, but instead in one\'s self. Corey will complete his MS in Human Computer Interaction at Iowa State University in 2013. He enjoys playing his guitar, running, backpacking, hiking, and any given weekend you can find him atop a peak hunting treasure with this GPS receiver geocaching.','2012-01-01','2013-05-01','2012-04-03 22:02:53',0),
			(12,'William','Schneller','http://www.vrac.iastate.edu/people/images/detail/williams-detail.jpg','student',1,30,NULL,NULL,X'CBA44657FC483CE3B68BE8FDB605072126C156BE6B60CE38659C39D5AC103468','','','','','',NULL,244,NULL,'','William works with Eve Wurtele. He is expecting to graduate in 2010 with his BFA in Integrated Studio Art with a focus on computer animation and modeling. He has chosen this degree because he has a passion for both art and technology. Integrated Studio Art provides him with a chance to blend his own creativity with cutting edge technology. ','2009-04-01','2012-01-01','2012-04-03 22:02:53',0),
			(13,'Eric','Abbott','http://www.jlmc.iastate.edu/sites/default/files/imagecache/Profile-Image/images/faculty-staff/Abbott_1.jpg','faculty',NULL,58,NULL,NULL,X'8E3A58A8781B2D60FF126C1BDAF178F00CC6E09F53779DE8617DBA7F1E24D48F','515-294-0492','204C Hamilton Hall','Professor',NULL,NULL,NULL,NULL,NULL,'Ph.D., Mass Communication, University of Wisconsin (1974)\r\nM.S., Agricultural Journalism, University of Wisconsin (1970)\r\nB.S., Science Journalism, Iowa State University, (1967)','Teaching and Research\r\nReporting; editing; theory; communication technologies; communication and international development.\r\n\r\nExperience\r\nWorked as a reporter, technical editor and communication strategist, and in the field of communication campaign design and implementation.\r\n\r\nCurrent Projects\r\nPre-testing communication materials to be used in support of agricultural and health activities in Kamuli district, Uganda. (with Brandie Martin). Uses of social networking sites by young voters (with Zhengjia Liu and Kuan-Ju Chen). Agenda setting and climate change (with Michael Dahlstrom).','2012-01-01',NULL,'2012-04-03 22:02:20',NULL),
			(14,'Anson','Call','http://www.design.iastate.edu/FACULTY/PICS/ansonc.jpg','faculty',NULL,28,NULL,NULL,X'7439F688854A62EBB3B9CAC4F130D79AB5518850FEA0D9C6B92DF45FBA9E274B','(515) 294-7855','488 Design','Associate Professor',NULL,NULL,NULL,NULL,NULL,'BFA, Utah State University (2000)\r\nMFA, Utah State University (2003)','RESEARCH INTERESTS\r\n3D modeling, texturing; 3D animation; character animation and rigging; computer lighting and rendering. Curriculum and course development for digital media. 3D animated timepieces.\r\n\r\nCURRENT PROJECTS\r\nIowa State University Game Developement Competition. A two semester competition designed to excite student interest in game making.','2012-01-01',NULL,'2012-04-03 22:02:53',NULL),
			(15,'Debra','Satterfield','http://hci.iastate.edu/media/People/images/faculty/debra815.jpg','faculty',NULL,28,NULL,NULL,X'5AB02E7A534C314630E1A5628FA526D026C156BE6B60CE38659C39D5AC103468','(515) 294-1667','277 Design','Associate Professor',NULL,NULL,NULL,NULL,NULL,'BS, Morningside College (1986) \r\nMFA, Iowa State University (1991)','RESEARCH INTERESTS\r\nExperience Design for Health Care, Design for Behavioral Change, Information Design, Design for Social Inclusion, the Design of Learning Experiences for Children with Developmental Disabilities, Multicultural Communication, Human Interaction Design, Design for Autism, Epilepsy and Cerebral Palsy, Virtual Product Design','2012-01-01',NULL,'2012-04-03 22:02:53',NULL),
			(16,'Wutthigrai','Boonsuk','http://hci.iastate.edu/People/images/alumni/Wutthigrai--Boonsuk-000120wut-hires.jpg','alumni',3,67,NULL,NULL,X'257670902398CFBEC62D2C78C56E911726C156BE6B60CE38659C39D5AC103468',NULL,NULL,'Instructor','Northern Illinois University','Sycamore',13,244,'Evaluation of desktop interface displays for 360-degree video.','','Area of MS research: \r\nMy research investigated the necessary display characteristics for a system such as mobile surveillance that allow observers to correctly interpret 360-degree video images displayed on a desktop screen. This research will assist designers of 360-degree video systems to design optimal user interface for remote navigation and observation in unfamiliar environments.\r\n\r\nWhat HCI classes were invaluable to my success? \r\nHCI 521 - The Cognitive Psychology of HCI.\r\n\r\nFavorite graduate school memory:\r\nI had an opportunity to work and contribute on several challenging projects.\r\n\r\nHCI issues that interest me today:\r\nCombining HCI and GIS technology.\r\n\r\nA website I recommend: \r\nwww.youtube.com.\r\n\r\nWhy?\r\nYou may be surprised by what you may find.','2009-09-01','2011-08-01','2012-04-03 22:02:53',NULL),
			(17,'Zayira Jordan','Conde','http://hci.iastate.edu/People/images/alumni/Zayira--Jordan_Conde-000079zjordan-detail.jpg','alumni',5,21,NULL,NULL,X'6E16D3338BB1BE2CAE22FEF967D6823BB5518850FEA0D9C6B92DF45FBA9E274B',NULL,NULL,'Associate Professor','Polytechnic University of Puerto Rico','San Juan',NULL,189,'Adolescents\' Cyberconnections: Identity Definition and Intimacy Disclosure on a Social Networking Site','','Area of PhD research: \r\nMy study investigates the relationship between the disclosure of intimate information by adolescents aged 18-21 through Facebook and the developmental stage they are in as per Erikson\'s (1968) psychosocial development theory.\r\n\r\nWhat HCI classes were invaluable to my success? \r\nComputational Perception, Ethical Implications\r\n\r\nFavorite graduate school memory:\r\nMy work with REU 2009 interns.\r\n\r\nHCI issues that interest me today:\r\nSocial networks, brain computer interfaces, haptics','2006-09-01','2010-05-01','2012-04-05 20:12:55',NULL),
			(18,'Regis','Fauquet','','student',2,67,NULL,NULL,X'A97D08477377953E5585D8553213C00B26C156BE6B60CE38659C39D5AC103468','','','Linux Support Engineer','Novell','Orange County',5,244,NULL,'','Regis lives in Orange County, California. He is currently employed at Novell as a Linux Support Engineer, providing L2 technical support for four major accounts. He chose the HCI Graduate Certificate Program because it provides a strong path into the field of HCI while offering opportunities to further study at the Master degree level and beyond. Among all the programs offered at different institutions, Regis found the HCI program at ISU offers many courses beyond what is traditionally included in Computer Science related programs. Regis has a broad academic and professional background in Design, Architecture, Human Factors and Computer Science. His goal is to combine his design skills and his computer skills to produce effective UI designs with the next generation of application software. With the advent of smart devices like phones and tablets, computer systems have become more ubiquitous and human computer interaction is no longer confined to a desktop experience. With ambient computer systems coming of age new challenges arise, and the need to be more careful and thoughtful in the design of user interfaces for safety, security, performance, and business reasons among other things is increasingly evident. Regis will be leveraging the knowledge and skills gained with the Certificate program to transition his career from a support role to a design role.','2011-01-01','2013-08-01','2012-04-03 22:02:53',0),
			(19,'Ahmad','Al-Kofahi','http://www.vrac.iastate.edu/people/images/thm/kofahia-thm.jpg','student',3,21,NULL,NULL,X'FD9D3A3C084E996A6D0BABCC8CEBDA760CC6E09F53779DE8617DBA7F1E24D48F','','','','','',NULL,244,NULL,'','','2011-01-01','2013-01-01','2012-04-03 22:02:26',0),
			(20,'Patrick','Carlson','http://www.vrac.iastate.edu/people/images/detail/carlsonp-detail.jpg','student',5,52,NULL,NULL,X'5FE916103CB01F3CC8FA4716311A2E8E26C156BE6B60CE38659C39D5AC103468','','VRAC, Haptics Lab','','','',23,244,NULL,'BS, Computer Science, Simpson College\r\nBS, Psychology, Simpson College','Patrick is originally from Minnesota where he grew up. He attended Simpson College in Iowa where he majored in Computer Science and Psychology. Patrick is working with Dr. Judy Vance on haptics and virtual reality applications. His thesis interests revolve around Open Source software communities and improving collaboration. Patrick expects to graduate from the HCI program in 2013. After graduating, he would like to work in industry.','2009-09-01','2013-05-01','2012-04-05 20:11:38',0),
			(21,'Vijay','Kalivarapu','http://www.vrac.iastate.edu/people/images/detail/vkk2-detail.jpg','student',6,40,NULL,NULL,X'3DFE7E35C097F109F96F3E75F6167539168C728995C2800F5F4DC64673E142E3','','','','','',NULL,244,NULL,'','Vijay enjoys challenging research and a growth-oriented, dynamic work environment. As a result, he envisions himself in an organization\'s R&amp;D department. His two primary research interests are real-time visualization and platform independent web-based collaborative visualization. \r\n\r\nCurrently, Vijay is working on two projects. The first project focuses on geo-physical mass flow visualization over active volcanoes. Specifically, the research team (which includes Dr. Eliot Winer from ISU and Dr. Michael Sheridan from SUNY Buffalo) is developing a platform for web-enabled independent visual simulations. The second project investigates ground water flow visualization. On this project (conducted in collaboration with Dr. Winer and Dr. Igor Jankovic from SUNY Buffalo), Vijay is simulating groundwater imperfections over conducting/non-conducting inhomogeneities in groundwater.\r\n\r\nEssentially, Vijay believes that real-time visualization has long been a major issue in collaborative environments, and that his research has the potential to improve visual collaboration between geographically distributed designers at an enormously increased rate, where the clients need not have much infrastructure to work with. In fact, Vijay believes that the Internet\'s the stumbling block for real-time collaboration of large amounts of visual data would no longer be an issue and it could potentially enable the designers toward faster design/product cycles.','2012-01-01','2016-01-01','2012-04-05 20:11:32',0)") 
			or die(error_message("Failed to fill details", mysql_error(), '30i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled details!</p>"; }
		
		$fill_table = mysql_query("INSERT INTO `". TBL_PROFILE_INTEREST_MAP ."` (`id`, `profile_id`, `interest_id`)
			VALUES
				(1,1,13),
				(2,1,84),
				(3,1,31),
				(4,1,112),
				(5,1,86),
				(6,1,105),
				(7,1,29),
				(8,1,11),
				(9,1,14),
				(10,13,141),
				(11,13,138),
				(12,4,43),
				(13,4,29),
				(14,4,30),
				(15,4,32),
				(16,5,56),
				(17,5,111),
				(18,5,20)") 
			or die(error_message("Failed to fill details", mysql_error(), '31i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled profile's interests!</p>"; }
		
		$fill_table = mysql_query("INSERT INTO `". TBL_PROFILE_PROFILE_MAP ."` (`profile_a_id`, `profile_b_id`, `relationship_id`)
			VALUES
				(1,15,1),
				(1,14,2),
				(1,4,2),
				(12,14,1),
				(4,18,1),
				(4,16,1),
				(6,16,2),
				(4,9,1),
				(6,9,2),
				(4,11,1)")
			or die(error_message("Failed to fill details", mysql_error(), '32i'));
		if($debug && $fill_table!==FALSE){ echo "<p class=\"success message\">Filled profile's related profiles!</p>"; }
	}

} else { // If this is an uninstall
	if($debug){ echo "<p class=\"success message\">Finished uninstall.</p>"; }
}


?>
<?php
/**
 * Contains required constants for Tassel,
 * including MySQL Settings, development tools, secret keys.
 *
 * @author Hannah Deering
 * @package Tassel
 **/
	
//Determine if we're running on localhost or live
if(stristr($_SERVER['HTTP_HOST'], 'local') || (substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168')) {
	$local = true;
} else {
	$local = false;
}

/* MySQL settings
 * ===================================================================*/
if($local) {

	/** The name of the database for Tassel */
	define('DB_NAME', 'tassel');

	/** MySQL database username */
	define('DB_USER', 'root');

	/** MySQL database password */
	define('DB_PASSWORD', 'root');

	/** MySQL hostname */
	define('DB_HOST', 'localhost');
	
	/**
	 * Absolute Paths to Tassel Files.
	 */
	define ("BASE", "http://".$_SERVER['HTTP_HOST']."/Tassel");
	define ("ROOT", $_SERVER['DOCUMENT_ROOT']."/Tassel");
} else {
	/** The name of the database for Tassel */
	define('DB_NAME', 'a8618040_tassel');

	/** MySQL database username */
	define('DB_USER', 'a8618040_monarch');

	/** MySQL database password */
	define('DB_PASSWORD', 'f4L0rDjH');

	/** MySQL hostname */
	define('DB_HOST', 'mysql13.000webhost.com');
	
	/**
	 * Absolute Paths to Tassel Files.
	 */
	define ("BASE", "http://".$_SERVER['HTTP_HOST']);
	define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/home/a8618040/public_html');
}

/** Prefix for MySQL tabels */
define('TBL_PREFIX', 'tassel_');

/** Table Names */
define('TBL_PROFILE_GROUP_MAP', TBL_PREFIX.'map_profile_group');
define('TBL_PROFILE_INTEREST_MAP', TBL_PREFIX.'map_profile_interest');
define('TBL_PROFILE_LINK_MAP', TBL_PREFIX.'map_profile_link');
define('TBL_PROFILE_PROFILE_MAP', TBL_PREFIX.'map_profile_profile');
define('TBL_DETAILS', TBL_PREFIX.'details');
define('TBL_DEPARTMENT', TBL_PREFIX.'department');
define('TBL_COLLEGE', TBL_PREFIX.'college');
define('TBL_COUNTRY', TBL_PREFIX.'country');
define('TBL_GROUP', TBL_PREFIX.'group');
define('TBL_INTEREST', TBL_PREFIX.'interest');
define('TBL_LINK', TBL_PREFIX.'link');
define('TBL_PROFILE', TBL_PREFIX.'profile');
define('TBL_PROGRAM', TBL_PREFIX.'program');
define('TBL_RELATIONSHIP', TBL_PREFIX.'relationship');
define('TBL_UPDATE', TBL_PREFIX.'update');
define('TBL_US_STATE', TBL_PREFIX.'us_state');


/* Constants
 * ===================================================================//

/** Year your program started.  This determines available start dates. */
define ("PROG_START_YEAR", 2004);

/** This year. Don't change this. */
define ("CUR_YEAR", date('Y'));

/** Email that automated emails should come from. */
define ("GLOBAL_EMAIL", "hjhunt11@gmail.com");

/** Is this currently being user-tested?
  * This modifies how data is stored in the database.
  */
define ("USER_TEST", FALSE);

/**
 * Authentication Unique Keys and Salts.
 * @access private
 */
define('SALT', ',I3+<aJeQpzZ&CoeH1 ZXd}=)maTK*^>kb1wUT%|{>67U>QixJ:bST(o`_(Al9:S');
define('PASSSALT', 'A-Kzl4wlSdVGGpq?- XS-DT^W+vr0(]JKz4>P`}-:3%PN~[LyS=KHsBeOC>NSPYr');

/**
 * For developers: Debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 */
if($local){
	$debug = true;
} else {
	$debug = false;
}

if($debug){ error_reporting(E_ALL | E_STRICT);}


/**
 * Base Title 
 */
define ("TITLE", "Academic Directory");

/**
 * Author of Site. 
 */
define ("SITE_AUTHOR", "Hannah Deering");

?>
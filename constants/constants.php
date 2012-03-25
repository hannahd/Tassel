<?php
	/**
	 * Contains required constants for Tassel,
	 * including MySQL Settings, development tools, secret keys.
	 *
	 * @author Hannah Deering
	 * @package Tassel
	 **/

/* MySQL settings
 * ===================================================================*/
/** The name of the database for Tassel */
define('DB_NAME', 'tassel');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Table Names */
define('TBL_PROFILE_GROUP_MAP', 'map_profile_group');
define('TBL_PROFILE_INTEREST_MAP', 'map_profile_interest');
define('TBL_PROFILE_LINK_MAP', 'map_profile_link');
define('TBL_PROFILE_PROFILE_MAP', 'map_profile_profile');
define('TBL_ALUMNI', 'alumni');
define('TBL_FACULTY', 'faculty');
define('TBL_STAFF', 'staff');
define('TBL_STUDENT', 'student');
define('TBL_VISITOR', 'visitor');
define('TBL_DEPARTMENT', 'department');
define('TBL_COLLEGE', 'college');
define('TBL_COUNTRY', 'country');
define('TBL_GROUP', 'group');
define('TBL_INTEREST', 'interest');
define('TBL_LINK', 'link');
define('TBL_PROFILE', 'profile');
define('TBL_PROGRAM', 'program');
define('TBL_RELATIONSHIP', 'relationship');
define('TBL_UPDATE', 'update');
define('TBL_US_STATE', 'us_state');


/* Constants
 * ===================================================================//

/** Year your program started.  This determines available start dates. */
define ("PROG_START_YEAR", 2004);

/** This year. Don't change this. */
define ("CUR_YEAR", date('Y'));

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
$debug = true;
if($debug){ error_reporting(E_ALL | E_STRICT);}

/**
 * Absolute Paths to Tassel Files.
 */
define ("BASE", "http://".$_SERVER['HTTP_HOST']."/Tassel/Tassel");
define ("ROOT", $_SERVER['DOCUMENT_ROOT']."/Tassel/Tassel");


/**
 * Base Title 
 */
define ("TITLE", "Academic Directory");

/**
 * Author of Site. 
 */
define ("SITE_AUTHOR", "Hannah Deering");

?>
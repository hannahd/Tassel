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

/** Prefix for MySQL tabels */
define('TBL_PREFIX', 'tassel_');

/** Table Names */
define('TBL_PROFILE_GROUP_MAP', TBL_PREFIX.'map_profile_group');
define('TBL_PROFILE_INTEREST_MAP', TBL_PREFIX.'map_profile_interest');
define('TBL_PROFILE_LINK_MAP', TBL_PREFIX.'map_profile_link');
define('TBL_PROFILE_PROFILE_MAP', TBL_PREFIX.'map_profile_profile');
define('TBL_ALUMNI', TBL_PREFIX.'alumni');
define('TBL_FACULTY', TBL_PREFIX.'faculty');
define('TBL_STAFF', TBL_PREFIX.'staff');
define('TBL_STUDENT', TBL_PREFIX.'student');
define('TBL_VISITOR', TBL_PREFIX.'visitor');
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
define ("USER_TEST", TRUE);

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
define ("BASE", "http://".$_SERVER['HTTP_HOST']."/Tassel");
define ("ROOT", $_SERVER['DOCUMENT_ROOT']."/Tassel");


/**
 * Base Title 
 */
define ("TITLE", "Academic Directory");

/**
 * Author of Site. 
 */
define ("SITE_AUTHOR", "Hannah Deering");

?>
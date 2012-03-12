<?php
/*
	Create the DB and and tables
*/

$messages_on = true;

function check_empty($table)
{
	$num_rows = mysql_result(mysql_query("SELECT COUNT(*) FROM `$table`"), 0); 
	
	if( !$num_rows )
	{
	 	return true;
	}
	else
	{
		return false;
	}
}

// Fill College Table with Data from Iowa State
function fill_college()
{
	$vals = mysql_query("INSERT INTO `college` (`name`)
		VALUES
		('Agriculture and Life Sciences'),
		('Business'),
		('Design'),
		('Engineering'),
		('Human Sciences'),
		('Liberal Arts and Sciences'),
		('Veterinary Medicine')") or die("<p class=\"error message\">Failed to fill colleges: ".mysql_error()."</p>");
}

// Fill Department Table with Data from Iowa State
function fill_department()
{
	$vals = mysql_query("INSERT INTO `department` (`name`, `fk_college_id`)
		VALUES
		('Agricultural and Biosystems Engineering', '1'),
		('Agricultural Education and Studies', '1'),
		('Agronomy', '1'),
		('Animal Science', '1'),
		('Biochemistry, Biophysics and Molecular Biology', '1'),
		('Ecology, Evolution and Organismal Biology', '1'),
		('Economics', '1'),
		('Entomology', '1'),
		('Food Science and Human Nutrition', '1'),
		('Genetics, Development and Cell Biology', '1'),
		('Horticulture', '1'),
		('Natural Resource Ecology and Management', '1'),
		('Plant Pathology and Microbiology', '1'),
		('Sociology', '1'),
		('Statistics', '1'),
		('Accounting', '2'),
		('Business Economics', '2'),
		('Finance', '2'),
		('Marketing', '2'),
		('Management', '2'),
		('Management Information Systems', '2'),
		('Supply Chain Management', '2'),
		('Architecture', '3'),
		('Art and Design', '3'),
		('Biological/Pre-Medical Illustration', '3'),
		('Community and Regional Planning', '3'),
		('Design (interdisciplinary)', '3'),
		('Graphic Design', '3'),
		('Industrial Design', '3'),
		('Integrated Studio Arts', '3'),
		('Interior Design', '3'),
		('Landscape Architecture', '3'),
		('Aerospace Engineering', '4'),
		('Agricultural and Biosystems Engineering', '4'),
		('Chemical and Biological Engineering', '4'),
		('Civil, Construction, and Environmental Engineering', '4'),
		('Electrical and Computer Engineering', '4'),
		('Industrial and Manufacturing Systems Engineering', '4'),
		('Materials Science and Engineering', '4'),
		('Mechanical Engineering', '4'),
		('Software Engineering', '4'),
		('Apparel, Events, and Hospitality Management', '5'),
		('Curriculum and Instruction', '5'),
		('Educational Leadership and Policy Studies', '5'),
		('Food Science and Human Nutrition', '5'),
		('Human Development and Family Studies', '5'),
		('Kinesiology', '5'),
		('Air Force Aerospace Studies', '6'),
		('Anthropology', '6'),
		('Biochemistry, Biophysics and Molecular Biology', '6'),
		('Chemistry', '6'),
		('Computer Science', '6'),
		('Ecology, Evolution and Organismal Biology', '6'),
		('Economics', '6'),
		('English', '6'),
		('Genetics, Development and Cell Biology', '6'),
		('Geological and Atmospheric Sciences', '6'),
		('Journalism and Mass Communication', '6'),
		('History', '6'),
		('Mathematics', '6'),
		('Military Science', '6'),
		('Music and Theatre', '6'),
		('Naval Science', '6'),
		('Philosophy and Religious Studies', '6'),
		('Physics and Astronomy', '6'),
		('Political Science', '6'),
		('Psychology', '6'),
		('Sociology', '6'),
		('Statistics', '6'),
		('World Languages and Cultures', '6'),
		('African and African American Studies', '6'),
		('American Indian Studies', '6'),
		('Biological/Pre-Medical Illustration', '6'),
		('Classical Studies', '6'),
		('Communication Studies', '6'),
		('Criminal Justice Studies', '6'),
		('International Studies', '6'),
		('Linguistics', '6'),
		('Technical Communication', '6'),
		('U.S. Latino/a Studies', '6'),
		('Women''s Studies', '6'),
		('Liberal Studies', '6'),
		('Bioinformatics and Computational Biology', '6'),
		('Biology', '6'),
		('Emerging Global Disease', '6'),
		('Environmental Science', '6'),
		('Gerontology', '6'),
		('Interdisciplinary Studies', '6'),
		('Premedical and Preprofessional Health', '6'),
		('Software Engineering', '6'),
		('Community Leadership and Public Service', '6'),
		('Latin American Studies', '6'),
		('Asian American Studies', '6'),
		('Public Administration', '6'),
		('Theatre', '6'),
		('Biomedical Sciences', '7'),
		('Veterinary Clinical Sciences', '7'),
		('Veterinary Diagnostic and Production Animal Medicine', '7'),
		('Veterinary Microbiology and Preventive Medicine', '7'),
		('Veterinary Pathology', '7')") or die("<p class=\"error message\">Failed to fill departments: ".mysql_error()."</p>");
}

// Fill State Table with Data
function fill_state()
{
	$vals = mysql_query("INSERT INTO `state` (`name`, `abbreviation`)
		VALUES
		('Not Applicable','NA'),
		('Alabama','AL'),
		('Alaska','AK'),
		('Arizona','AZ'),
		('Arkansas','AR'),
		('California','CA'),
		('Colorado','CO'),
		('Connecticut','CT'),
		('Delaware','DE'),
		('Florida','FL'),
		('Georgia','GA'),
		('Hawaii','HI'),
		('Idaho','ID'),
		('Illinois','IL'),
		('Indiana','IN'),
		('Iowa','IA'),
		('Kansas','KS'),
		('Kentucky','KY'),
		('Louisiana','LA'),
		('Maine','ME'),
		('Maryland','MD'),
		('Massachusetts','MA'),
		('Michigan','MI'),
		('Minnesota','MN'),
		('Mississippi','MS'),
		('Missouri','MO'),
		('Montana','MT'),
		('Nebraska','NE'),
		('Nevada','NV'),
		('New Hampshire','NH'),
		('New Jersey','NJ'),
		('New Mexico','NM'),
		('New York','NY'),
		('North Carolina','NC'),
		('North Dakota','ND'),
		('Ohio','OH'),
		('Oklahoma','OK'),
		('Oregon','OR'),
		('Pennsylvania','PA'),
		('Rhode Island','RI'),
		('South Carolina','SC'),
		('South Dakota','SD'),
		('Tennessee','TN'),
		('Texas','TX'),
		('Utah','UT'),
		('Vermont','VT'),
		('Virginia','VA'),
		('Washington','WA'),
		('West Virginia','WV'),
		('Wisconsin','WI')") or die("<p class=\"error message\">Failed to fill states: ".mysql_error()."</p>");
}

// Fill Country Table with Data
function fill_country()
{
	$vals = mysql_query("INSERT INTO `country` (`name`)
		VALUES
		('Afghanistan'),
		('Akrotiri'),
		('Albania'),
		('Algeria'),
		('American Samoa'),
		('Andorra'),
		('Angola'),
		('Anguilla'),
		('Antarctica'),
		('Antigua and Barbuda'),
		('Argentina'),
		('Armenia'),
		('Aruba'),
		('Ashmore and Cartier Islands'),
		('Australia'),
		('Austria'),
		('Azerbaijan'),
		('Bahamas, The'),
		('Bahrain'),
		('Bangladesh'),
		('Barbados'),
		('Bassas da India'),
		('Belarus'),
		('Belgium'),
		('Belize'),
		('Benin'),
		('Bermuda'),
		('Bhutan'),
		('Bolivia'),
		('Bosnia and Herzegovina'),
		('Botswana'),
		('Bouvet Island'),
		('Brazil'),
		('British Indian Ocean Territory'),
		('British Virgin Islands'),
		('Brunei'),
		('Bulgaria'),
		('Burkina Faso'),
		('Burma'),
		('Burundi'),
		('Cambodia'),
		('Cameroon'),
		('Canada'),
		('Cape Verde'),
		('Cayman Islands'),
		('Central African Republic'),
		('Chad'),
		('Chile'),
		('China'),
		('Christmas Island'),
		('Clipperton Island'),
		('Cocos (Keeling) Islands'),
		('Colombia'),
		('Comoros'),
		('Congo, Democratic Republic of the'),
		('Congo, Republic of the'),
		('Cook Islands'),
		('Coral Sea Islands'),
		('Costa Rica'),
		('Cote d''Ivoire'),
		('Croatia'),
		('Cuba'),
		('Cyprus'),
		('Czech Republic'),
		('Denmark'),
		('Dhekelia'),
		('Djibouti'),
		('Dominica'),
		('Dominican Republic'),
		('Ecuador'),
		('Egypt'),
		('El Salvador'),
		('Equatorial Guinea'),
		('Eritrea'),
		('Estonia'),
		('Ethiopia'),
		('Europa Island'),
		('Falkland Islands (Islas Malvinas)'),
		('Faroe Islands'),
		('Fiji'),
		('Finland'),
		('France'),
		('French Guiana'),
		('French Polynesia'),
		('French Southern and Antarctic Lands'),
		('Gabon'),
		('Gambia, The'),
		('Gaza Strip'),
		('Georgia'),
		('Germany'),
		('Ghana'),
		('Gibraltar'),
		('Glorioso Islands'),
		('Greece'),
		('Greenland'),
		('Grenada'),
		('Guadeloupe'),
		('Guam'),
		('Guatemala'),
		('Guernsey'),
		('Guinea'),
		('Guinea-Bissau'),
		('Guyana'),
		('Haiti'),
		('Heard Island and McDonald Islands'),
		('Holy See (Vatican City)'),
		('Honduras'),
		('Hong Kong'),
		('Hungary'),
		('Iceland'),
		('India'),
		('Indonesia'),
		('Iran'),
		('Iraq'),
		('Ireland'),
		('Isle of Man'),
		('Israel'),
		('Italy'),
		('Jamaica'),
		('Jan Mayen'),
		('Japan'),
		('Jersey'),
		('Jordan'),
		('Juan de Nova Island'),
		('Kazakhstan'),
		('Kenya'),
		('Kiribati'),
		('Korea, North'),
		('Korea, South'),
		('Kuwait'),
		('Kyrgyzstan'),
		('Laos'),
		('Latvia'),
		('Lebanon'),
		('Lesotho'),
		('Liberia'),
		('Libya'),
		('Liechtenstein'),
		('Lithuania'),
		('Luxembourg'),
		('Macau'),
		('Macedonia'),
		('Madagascar'),
		('Malawi'),
		('Malaysia'),
		('Maldives'),
		('Mali'),
		('Malta'),
		('Marshall Islands'),
		('Martinique'),
		('Mauritania'),
		('Mauritius'),
		('Mayotte'),
		('Mexico'),
		('Micronesia, Federated States of'),
		('Moldova'),
		('Monaco'),
		('Mongolia'),
		('Montserrat'),
		('Morocco'),
		('Mozambique'),
		('Namibia'),
		('Nauru'),
		('Navassa Island'),
		('Nepal'),
		('Netherlands'),
		('Netherlands Antilles'),
		('New Caledonia'),
		('New Zealand'),
		('Nicaragua'),
		('Niger'),
		('Nigeria'),
		('Niue'),
		('Norfolk Island'),
		('Northern Mariana Islands'),
		('Norway'),
		('Oman'),
		('Pakistan'),
		('Palau'),
		('Panama'),
		('Papua New Guinea'),
		('Paracel Islands'),
		('Paraguay'),
		('Peru'),
		('Philippines'),
		('Pitcairn Islands'),
		('Poland'),
		('Portugal'),
		('Puerto Rico'),
		('Qatar'),
		('Reunion'),
		('Romania'),
		('Russia'),
		('Rwanda'),
		('Saint Helena'),
		('Saint Kitts and Nevis'),
		('Saint Lucia'),
		('Saint Pierre and Miquelon'),
		('Saint Vincent and the Grenadines'),
		('Samoa'),
		('San Marino'),
		('Sao Tome and Principe'),
		('Saudi Arabia'),
		('Senegal'),
		('Serbia and Montenegro'),
		('Seychelles'),
		('Sierra Leone'),
		('Singapore'),
		('Slovakia'),
		('Slovenia'),
		('Solomon Islands'),
		('Somalia'),
		('South Africa'),
		('South Georgia and the South Sandwich Islands'),
		('Spain'),
		('Spratly Islands'),
		('Sri Lanka'),
		('Sudan'),
		('Suriname'),
		('Svalbard'),
		('Swaziland'),
		('Sweden'),
		('Switzerland'),
		('Syria'),
		('Taiwan'),
		('Tajikistan'),
		('Tanzania'),
		('Thailand'),
		('Timor-Leste'),
		('Togo'),
		('Tokelau'),
		('Tonga'),
		('Trinidad and Tobago'),
		('Tromelin Island'),
		('Tunisia'),
		('Turkey'),
		('Turkmenistan'),
		('Turks and Caicos Islands'),
		('Tuvalu'),
		('Uganda'),
		('Ukraine'),
		('United Arab Emirates'),
		('United Kingdom'),
		('United States'),
		('Uruguay'),
		('Uzbekistan'),
		('Vanuatu'),
		('Venezuela'),
		('Vietnam'),
		('Virgin Islands'),
		('Wake Island'),
		('Wallis and Futuna'),
		('West Bank'),
		('Western Sahara'),
		('Yemen'),
		('Zambia'),
		('Zimbabwe')") or die("<p class=\"error message\">Failed to fill countries: ".mysql_error()."</p>");
}

// Fill People Table with Some Initial Data
function fill_person()
{
	$vals = mysql_query("INSERT INTO `".PERSON_TABLE."` 
			(`user_name`, `first_name`, `last_name`, `privelege`, `photo`, `position`, `title`, `email`, `phone`, `office_location`, `home_city`, `thesis_title`, `bio`, `start_date`, `end_date`, `last_update`, `supervisory_committee`, `fk_college_id`, `fk_department_id`, `fk_state_id`, `fk_country_id`) 
			VALUES 
			('oliver', 'James', 'Oliver', 'Subject', 'http://www.vrac.iastate.edu//people/images/thm/oliver-thm.jpg', 'Faculty', 'Professor of Mechanical Engineering, Director, VRAC', 'oliver@iastate.edu', '515-294-2649', 'Room 1620, 2274 Howe Hall', '', '', 'Oliver received his PhD in Mechanical Engineering from Michigan State University and is widely recognized for his software development and technical contributions in automation of design and manufacturing processes via applied geometric modeling and computer visualization. In 1991, he was recruited by Iowa State to help organize the Iowa Center for Emerging Manufacturing Technology, predecessor of the Virtual Reality Applications Center. From 1993 to 1997, Oliver served as Associate Director of ICEMT.', '2005-08-16', '', now(), '1', '4', '40', '16', '244'),
			('gilbert', 'Stephen', 'Gilbert', 'Subject', 'http://hci.iastate.edu/media/People/images/faculty/gilbert.jpg', 'Faculty', 'Research Assistant Professor of Psychology, Associate Director, VRAC', 'gilbert@iastate.edu', '515-294-6782', '1620 Howe Hall', '', '', '', '2008-08-16', '', now(), '1', '6', '67', '16', '244'),
			('ewiner', 'Eliot', 'Winer', 'Subject', 'http://hci.iastate.edu/media/People/images/faculty/gilbert.jpg', 'Faculty', 'Associate Professor, Associate Director, VRAC', 'ewiner@iastate.edu', '', '1620 Howe Hall', '', '', 'When asked about the primary goal of his research, Dr. Winer responded, \"I love being told it can\'t be done; it\'s a challenge.\" Thus, it should come as no surprise that he primarily deals with the design of complex systems. According to Dr. Winer, \"we will continue to gather more data than we know what to do with.\" As a result, he is motivated to design systems that render complex data in such a way that insights can be drawn. Dr. Winer believes that the better we can describe and represent complex systems the better designed products and processes will become.', '2007-08-16', '', now(), '1', '4', '40', '16', '244'),
			('debra815', 'Debra', 'Satterfield', 'Subject', 'http://www.vrac.iastate.edu//people/images/thm/hjhunt-thm.jpg', 'Faculty', 'Associate Professor, Graphic Design; Program Director, Graphic Design', 'debra815@iastate.edu', '515-294-1667', '277 Design', '', '', '', '2008-08-16', '', now(), '1', '3', '28', '16', '244'),
			('hjhunt', 'Hannah', 'Deering', 'Subject', 'http://www.vrac.iastate.edu//people/images/thm/hjhunt-thm.jpg', 'On-campus MS', 'HCI Master\'s Student', 'hjhunt@iastate.edu', '612-207-0700', '2nd Floor VRAC, SE Corner', 'Minneapolis', '', 'Originally from Minneapolis, Hannah received two Bachelor\'s degrees in Computer Science and Art & Design from Iowa State. She worked in the VRAC’s multitouch group during her undergraduate studies, and is now returning to ISU to obtain a Master\'s degree in HCI. Her research interests include software interface & interaction design, usability, requirements gathering, and digital instruction.', '2011-08-16', '2013-05-01', now(), '0', '3', '28', '24', '244'),
			('jpking', 'Joshua', 'King', 'Subject', 'http://hci.iastate.edu/media/People/images/certificate/jpking.iastate-thm.jpg', 'Certificate', 'HCI Certificate Student', 'jpking@iastate.edu', '', '', 'Littleton', '', 'Joshua earned is BA in MIS and Economics from the University of Northern Iowa in 1995. He lives in Littleton, Colorado and works for SpatialInfo as a Senior Developer. He is interested in HCI so he can expand on what he already knows about UX and take his development skills to the next level. He is interested in learning new technologies and advancing his technology knowledge and skills.', '2011-08-16', '2013-05-01', now(), '0', '6', '67', '7', '244'),
			('clwiley', 'Cynthia', 'Wiley', 'Subject', 'http://www.vrac.iastate.edu//people/images/thm/clwiley-thm.jpg', 'PhD', 'HCI PhD Student, Graphic Design Master\'s Student', 'clwiley@iastate.edu', '', '', 'Des Moines', '', 'Cyndi started freelancing while working full time as a creative director for Nutraceutics Corp. in St. Louis. While her business grew and she moved from St. Louis to Iowa in 2007, she decided to go solo full time. After getting bored working with clients and having ignored the call to teach for numerous years, she decided to attend graduate school at Iowa State University to pursue a PhD in Human Computer Interaction and a MFA in graphic design. Her hope is to teach emerging media and graphic design at the university level. Her research interests include using information communication technologies to better human existence. ', '2010-08-16', '2013-05-01', now(), '0', '3', '28', '16', '244'),
			('rgwilson', 'Ryan', 'Wilson', 'Subject', 'http://www.vrac.iastate.edu/people/images/detail/rgwilson-detail.jpg', 'Online MS', 'HCI Master\'s Student', 'rgwilson@iastate.edu', '', '', 'Mansfield', '', 'Ryan is from Mansfield, Ohio. He received his B.S. in Interactive Multimedia from Ohio University’s School of Visual Communication. Since graduation, Ryan has been an Art Director working in design and marketing with a focus on 2D animations for use in trials and litigation. He is interested in the design aspects of user interfacing and user experience.', '2010-08-16', '2013-05-01', now(), '0', '6', '67', '36', '244'),
			('pshill', 'Pam', 'Shill', 'Monarch', 'http://hci.iastate.edu/People/images/staff/pam_shill.jpg', 'Staff', 'Program Coordinator', 'pshill@iastate.edu', '515-294-2089', '', '', '', '', '2010-08-16', '', now(), '0', '6', '67', '16', '244')") or die("<p class=\"error message\">Failed to fill person: ".mysql_error()."</p>");
}


// =========================================================
// 		CREATE TABLES

//Create the person table (if it doesn't already exist)
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `college` (
	`college_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(220) NOT NULL,
	PRIMARY KEY (`college_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die("<p class=\"error message\">Failed to create college table: ".mysql_error()."</p>");

//Create the department table (if it doesn't already exist)
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `department` (
	`department_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(220) NOT NULL,
	`fk_college_id` int(11) unsigned NOT NULL,
	FOREIGN KEY (`fk_college_id`) REFERENCES `college`(`college_id`),
	PRIMARY KEY (`department_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die("<p class=\"error message\">Failed to create department table: ".mysql_error()."</p>");

//Create the country table (if it doesn't already exist)
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `country` (
	`country_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(220) NOT NULL,
	PRIMARY KEY (`country_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die("<p class=\"error message\">Failed to create country table: ".mysql_error()."</p>");

//Create the state table (if it doesn't already exist)
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `state` (
	`state_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(220) NOT NULL,
	`abbreviation` varchar(2) NOT NULL,
	PRIMARY KEY (`state_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;")or die("<p class=\"error message\">Failed to create state table: ".mysql_error()."</p>");
	
//Create the person table (if it doesn't already exist)
$create_table = mysql_query("CREATE TABLE IF NOT EXISTS `person` (
	`person_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_name` varchar(220) NOT NULL,
	`first_name` varchar(220) NOT NULL,
	`last_name` varchar(220) NOT NULL,
	`privelege` enum('Subject', 'Monarch') NOT NULL,
	`photo` varchar(220),
	`position` enum('Unknown', 'Faculty', 'Staff', 'Undergrad', 'Certificate', 'On-campus MS', 'Online MS', 'PhD', 'PostDoc', 'Alumni') NOT NULL,
	`title` varchar(220),
	`email` varchar(220) NOT NULL,
	`phone` varchar(220),
	`office_location` varchar(220),
	`home_city` varchar(220),
	`thesis_title` varchar(220),
	`bio` text,
	`start_date` date NOT NULL,
	`end_date` date,
	`last_update` datetime,
	`supervisory_committee` bool,
	`fk_college_id` int(11) unsigned NOT NULL,
	`fk_department_id` int(11) unsigned NOT NULL,
	`fk_state_id` int(11) unsigned NOT NULL,
	`fk_country_id` int(11) unsigned NOT NULL,
	`fk_major_prof_id` int(11) unsigned,
	FOREIGN KEY (`fk_college_id`) REFERENCES `college`(`college_id`),
	FOREIGN KEY (`fk_department_id`) REFERENCES `department`(`department_id`),
	FOREIGN KEY (`fk_state_id`) REFERENCES `state`(`state_id`),
	FOREIGN KEY (`fk_country_id`) REFERENCES `country`(`country_id`),
	FOREIGN KEY (`fk_major_prof_id`) REFERENCES `person`(`person_id`),
	PRIMARY KEY (`person_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;") or die("<p class=\"error message\">Failed to create person table: ".mysql_error()."</p>");

// =========================================================
// 		FILL TABLES

//Fill tables with values only if they are empty
if( check_empty("college") ) { 
	fill_college(); 
	if($messages_on){ echo "<p class=\"success message\">Filled colleges!</p>"; }
}
if( check_empty("department") ) { 
	fill_department(); 
	if($messages_on){ echo "<p class=\"success message\">Filled departments!</p>"; }
}

if( check_empty("country") ) { 
	fill_country(); 
	if($messages_on){ echo "<p class=\"success message\">Filled countries!</p>"; }
}

if( check_empty("state") ) { 
	fill_state(); 
	if($messages_on){ echo "<p class=\"success message\">Filled states!</p>"; }
}

if( check_empty("person") ) { 
	fill_person(); 
	if($messages_on){ echo "<p class=\"success message\">Filled some people!</p>"; }
}

?>
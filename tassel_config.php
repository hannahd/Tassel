<?php
/**
 * The base configurations for Tassel. 
 *
 * Includes information about the departments, college and programs
 * for people in the directory. 
 *
 * This file is used only during the installation. 
 *
 * @author Hannah Deering
 * @package Tassel
 **/

/* Program Details
 * =================================================================== */

/** Colleges in your program. 
  *
  * Colleges provide a way to narrow large lists of departments. 
  * Each department must have a college.  The order these are listed 
  * should match the order to the corresponding departments.
  */
$colleges =		array(	'Agriculture and Life Sciences', 
						'Business', 
						'Design', 
						'Engineering', 
						'Human Sciences', 
						'Liberal Arts and Sciences',
						'Veterinary Medicine');

/** Departments in your program.
  *
  * Faculty, staff and alumni must be a part of a department. These 
  * departments are placed in arrays where they will be asscociated 
  * with a college of the same index. 
  * For example the departments in the first array should be a part of
  * the first college listed.
  */
$departments =	array( 	array ('Agricultural and Biosystems Engineering', 'Agricultural Education and Studies', 'Agronomy', 'Animal Science', 'Biochemistry, Biophysics and Molecular Biology', 'Ecology, Evolution and Organismal Biology', 'Economics', 'Entomology', 'Food Science and Human Nutrition', 'Genetics, Development and Cell Biology', 'Horticulture', 'Natural Resource Ecology and Management', 'Plant Pathology and Microbiology', 'Sociology', 'Statistics'), 
						array ('Accounting', 'Business Economics', 'Finance', 'Marketing', 'Management', 'Management Information Systems', 'Supply Chain Management', 'Architecture'), 
						array ('Art and Design', 'Biological/Pre-Medical Illustration', 'Community and Regional Planning', 'Design interdisciplinary', 'Graphic Design', 'Industrial Design', 'Integrated Studio Arts', 'Interior Design', 'Landscape Architecture', 'Aerospace Engineering'), 
						array ('Agricultural and Biosystems Engineering', 'Chemical and Biological Engineering', 'Civil, Construction, and Environmental Engineering', 'Electrical and Computer Engineering', 'Industrial and Manufacturing Systems Engineering', 'Materials Science and Engineering', 'Mechanical Engineering', 'Software Engineering'), 
						array ('Apparel, Events, and Hospitality Management', 'Curriculum and Instruction', 'Educational Leadership and Policy Studies', 'Food Science and Human Nutrition', 'Human Development and Family Studies', 'Kinesiology'), 
						array ('Air Force Aerospace Studies', 'Anthropology', 'Biochemistry, Biophysics and Molecular Biology', 'Chemistry', 'Computer Science', 'Ecology, Evolution and Organismal Biology', 'Economics', 'English', 'Genetics, Development and Cell Biology', 'Geological and Atmospheric Sciences', 'Journalism and Mass Communication', 'History', 'Mathematics', 'Military Science', 'Music and Theatre', 'Naval Science', 'Philosophy and Religious Studies', 'Physics and Astronomy', 'Political Science', 'Psychology', 'Sociology', 'Statistics', 'World Languages and Cultures', 'African and African American Studies', 'American Indian Studies', 'Biological/Pre-Medical Illustration', 'Classical Studies', 'Communication Studies', 'Criminal Justice Studies', 'International Studies', 'Linguistics', 'Technical Communication', 'U.S. Latino/a Studies', 'Women\'\'s Studies', 'Liberal Studies', 'Bioinformatics and Computational Biology', 'Biology', 'Emerging Global Disease', 'Environmental Science', 'Gerontology', 'Interdisciplinary Studies', 'Premedical and Preprofessional Health', 'Software Engineering', 'Community Leadership and Public Service', 'Latin American Studies', 'Asian American Studies', 'Public Administration', 'Theatre'), 
						array ('Biomedical Sciences', 'Veterinary Clinical Sciences', 'Veterinary Diagnostic and Production Animal Medicine', 'Veterinary Microbiology and Preventive Medicine', 'Veterinary Pathology'));

/** Degree programs in your program.
  *
  * Students and alumni are a part of programs.  For example a program may be 
  * Bachelor's of Science, Certificate, or Post-Doc.
  * Each array includes the name of the program, an abbreviation, 
  * whether it is conducted online (0 is no, 1 is yes), and whether it is a 
  * student or researcher program (for example, you may not have an academic 
  * program for undergrads but they may be able to do research).
  */
$programs = 	array( 	array ('Undergraduate', '', 0, 'researcher'),
						array ('Certificate', '', 1, 'student'),
						array ('Master\'\'s', 'MS', 0, 'student'),
						array ('Master\'\'s', 'MS', 1, 'student'),
						array ('PhD', 'PhD', 0, 'student'),
						array ('Post-Doc', '', 0, 'student'));
						
/** Groups in your program.
  *
  * Any individual can be a part of a group. Groups may be committees,
  * clubs or organizations within your program. For example you may have a group
  * of alumni that go into academia, or a leadership board.
  */						
$groups = 		array( 	'VRAC', 
						'HCI', 
						'Supervisory Committee', 
						'Academia Alumni', 
						'Industry Alumni');
						
/** Relationship types in your program.
  *
  * Relationship link two individuals in your programs.  Example relationships
  * can be employor, major professor, committee member, etc.
  */						
$relationships = array( 'major professor', 
						'committee member');

?>
<?php
/**
 * Profile Getter for Tassel.
 * 
 * This file retrieves profiles from the database based on
 * provided search or filters. It also formats the profile
 * for the directory page.
 *
 * Error codes ending with gp. 
 *
 * @author Hannah Deering
 * @package Tassel
 **/

require_once ("constants.php");
require_once ("dbconnect.php"); //Includes database connection details
require_once ("functions.php"); //Includes functions
require_once ("validation_functions.php"); //Includes data sanitation & validation functions

if($_GET['action'] == "get-one"){
	$return_content = "";
	$where_string = "";
	$errors = array();
	if((isset($_GET['id']) && !empty($_GET['id'])) ){
		$id = sanitize($_GET['id']);
		$result = validate_input($id, "id", true, "num");
		$errors = array_merge($errors, $result);
		
		$where_string = "WHERE profile_id = $id";
	} else if(isset($_GET['first_name']) && isset($_GET['last_name']) && !empty($_GET['first_name']) && !empty($_GET['last_name'])){
		$first = sanitize($_GET['first_name']);
		$result = validate_input($first, "first name", true, "name");
		$errors = array_merge($errors, $result);
		
		$last = sanitize($_GET['last_name']);
		$result = validate_input($last, "last name", true, "name");
		$errors = array_merge($errors, $result);
		
		$where_string = "WHERE `first_name`=\"$first\" AND `last_name`=\"$last\"";
	}
	
	// Check if there are errors			
	if (count($errors) > 0){
		// Print errors
		$return_content .= '<ul class="error alert"><span>Please correct the following:</span>';
		foreach($errors as $e) {
			$return_content .= "<li>".$e ."</li>";
		}
		$return_content .= '</ul>';
	} 
	// Check that there are profiles for the current search/filters
	else {
		$profiles_qry = mysql_query("SELECT *, AES_DECRYPT(en_email, '". SALT ."') AS email 
								   	   FROM ". TBL_DETAILS .
							     " $where_string LIMIT 1") 
					or die(error_message("Could get profile", mysql_error(),"1gp"));
		
		if(mysql_num_rows($profiles_qry) == 0) {
		// No matches
		$return_content .= "<p class=\"alert\">Sorry, there are no matches for this profile.</p>";
		} else { 
			// Build profile entry
			$profile = mysql_fetch_assoc($profiles_qry);		
			$return_content .= get_profile($profile, true);
		}
	}
	
	echo $return_content;
}

elseif($_GET['action'] == "get" ){
	$return_content = "";
	$errors = array();
	$all = array("all");
	$all_two = array("all", "all");
	
	// WHERE query string to add filters to the MySQL query
	// Only retrieve enabled profiles
	$where_string = "WHERE `profile_id` IN (
						SELECT `id`
						  FROM `". TBL_PROFILE ."` WHERE `enabled`='1') ";
	$now_showing = "";
	
	// Add search keywords to query
	if((isset($_POST['search']) && !empty($_POST['search'])) ){
		$searches = sanitize($_POST['search']);
		$searches = purge_invalid($searches);
		
		// Check if there is only one search group to filter
		if(!is_array($searches)){
			$searches = array($searches);
		}
		// Add each position filter
		foreach ($searches as $search) {
			// Check if it's empty
			if(trim($search) !== ""){
				// Delimit search on spaces
				$search_terms = explode(" ", $search);
			
				// Add new WHERE constraint
				$where_string .= "AND (";
		
				$search_string = "";
		
				// Add each search to constraints
				foreach ($search_terms as $keyword) {
					// Check if there are already searches
					if(!empty($search_string)){
						$search_string .= "AND ";
					}
			
					// Check first names
					$search_string .= "(`first_name` LIKE '%$keyword%' OR ";
					// Check last names
					$search_string  .= "`last_name` LIKE '%$keyword%' OR ";
					// Check emails
					$search_string  .= "AES_DECRYPT(`en_email`, '". SALT ."') LIKE '%$keyword%' OR ";
					// Check title
					$search_string  .= "`title` LIKE '%$keyword%' OR ";
					// Check company
					$search_string  .= "`company` LIKE '%$keyword%' OR ";
					// Check interests
					$search_string .= "`profile_id` IN (
										SELECT `profile_id`
										  FROM `". TBL_PROFILE_INTEREST_MAP ."`
										  JOIN `". TBL_INTEREST ."`
										    ON `". TBL_PROFILE_INTEREST_MAP ."`.`interest_id` = `". TBL_INTEREST ."`.`id`
										 WHERE `". TBL_INTEREST ."`.`name` LIKE '%$keyword%') OR ";				
					// Check departments
					$search_string .= "`profile_id` IN (
										SELECT `profile_id`
										  FROM `". TBL_DETAILS ."`
										  JOIN `". TBL_DEPARTMENT ."`
										    ON `". TBL_DETAILS ."`.`department_id` = `". TBL_DEPARTMENT ."`.`id`
										 WHERE `". TBL_DEPARTMENT ."`.`name` LIKE '%$keyword%'))";				
		
					// Add filters to the showing string
					if(!empty($now_showing)){ $now_showing .= ", "; }
					$now_showing .= '"'.$keyword.'"';
				}
		
				$where_string .= $search_string. ")";
			}
		}
	}
	
	// Add position filters to query
	if(isset($_POST['position']) && !empty($_POST['position']) && ($_POST['position'] !== "all" && $_POST['position'] !== $all && $_POST['position'] !== $all_two)){
		$position = $_POST['position'];
		
		// Remove all from the array
		$index_all = array_search('all', $position);
		if($index_all !== FALSE) {
			unset($position[$index_all]);
		}
		
		$result = validate_input($position, "Position", true, "position");
		$errors = array_merge($errors, $result);
		
		// Check for errors
		if(count($errors) == 0){
			
			// Add new WHERE constraint
			$where_string .= "AND ";
			
			// Check if there is only one position to filter
			if(!is_array($position)){
				$position = array($position);
			}
			
			$filter_string = "";
		
			// Add each position filter
			foreach ($position as $filter) {
				// Check if there are already filters
				if(empty($filter_string)){
					$filter_string = "(";
				} else{
					$filter_string .= "OR ";
				}
				$filter_string .= "`position`='".$filter."' ";
				
				// Add filters to the showing string
				if(!empty($now_showing)){ $now_showing .= ", "; }
				$now_showing .= $filter;
				// Add plural if needed
				if($filter === "student" || $filter === "visitor"){ $now_showing .= "s"; }
			}

			$filter_string .= ") ";
			$where_string .= $filter_string;
		}
	}
	
	// Add department filters to query
	if(isset($_POST['department']) && !empty($_POST['department']) && ($_POST['department'] !== "all" && $_POST['department'] !== $all && $_POST['department'] !== $all_two)) {
		$department = $_POST['department'];
		
		// Remove all from the array
		$index_all = array_search('all', $department);
		if($index_all !== FALSE) {
			unset($department[$index_all]);
		}
		
		$result = validate_input($department, "department", true, "num");
		$errors = array_merge($errors, $result);
		
		// Check for errors
		if(count($errors) == 0){

			
			// Add new WHERE constraint
			$where_string .= "AND (";
			$filter_string = "";
			
			// Check if there is only one department to filter
			if(!is_array($department)){
				$department = array($department);
			}
					
			// Add each position filter
			foreach ($department as $filter) {
				if(!empty($filter_string)){ $filter_string .= "OR "; }
				// Check departments
				$filter_string .= "`department_id`=$filter ";				
				
				// Add filters to the showing string
				if(!empty($now_showing)){ $now_showing .= ", "; }
				$department_qry = mysql_query("SELECT `name` FROM ". TBL_DEPARTMENT ." WHERE id=$filter LIMIT 1") or die(error_message("Could not access departments", mysql_error(),"2gp"));
				$dept_name = mysql_fetch_assoc($department_qry);
				$now_showing .= $dept_name['name'];
			}
			
			$where_string .= $filter_string . ") ";
		}
	}
	
	// Add interest filters to query
	if(isset($_POST['interest']) && !empty($_POST['interest']) && ($_POST['interest'] !== "all" && $_POST['interest'] !== $all && $_POST['interest'] !== $all_two)) {
		$interests = $_POST['interest'];
		
		// Remove all from the array
		$index_all = array_search('all', $interests);
		if($index_all !== FALSE) {
			unset($interests[$index_all]);
		}
		
		$result = validate_input($interests, "interests", true, "num");
		$errors = array_merge($errors, $result);
		
		// Check for errors
		if(count($errors) == 0){
			// Check if there are already WHERE constraints
			if(empty($where_string)){
				$where_string = "WHERE ";
			} else{
				$where_string .= "AND ";
			}
			

			
			$where_string .= "`profile_id` IN (";
			$filter_string = "";
			
			// Check if there is only one department to filter
			if(!is_array($interests)){
				$interests = array($interests);
			}
					
			// Add each interest filter
			foreach ($interests as $filter) {
				// Check if there are already filters
				if(!empty($filter_string)){ $filter_string .= "UNION "; }
				$filter_string .= "SELECT `profile_id` FROM `". TBL_PROFILE_INTEREST_MAP ."` WHERE `interest_id`='".$filter."' ";
				
				// Add filters to the showing string
				if(!empty($now_showing)){ $now_showing .= ", "; }
				$interest_qry = mysql_query("SELECT `name` FROM ". TBL_INTEREST ." WHERE `id`=".$filter. " LIMIT 1") or die(error_message("Could not access interests", mysql_error(),"3gp"));
				$interest_name = mysql_fetch_assoc($interest_qry);
				$now_showing .= $interest_name['name'];
			}
			
			$where_string .= $filter_string . ") ";
		}
	}

	// Add program filters to query
	if(isset($_POST['program']) && !empty($_POST['program']) && ($_POST['program'] !== "all" && $_POST['program'] !== $all && $_POST['program'] !== $all_two)){
		$program = $_POST['program'];
		
		// Remove all from the array
		$index_all = array_search('all', $program);
		if($index_all !== FALSE) {
			unset($program[$index_all]);
		}
		
		$result = validate_input($program, "program", true, "num");
		$errors = array_merge($errors, $result);
		
		// Check for errors
		if(count($errors) == 0){

			
			// Add new WHERE constraint
			$where_string .= "AND (";
			$filter_string = "";
			
			// Check if there is only one department to filter
			if(!is_array($program)){
				$program = array($program);
			}
					
			// Add each position filter
			foreach ($program as $filter) {
				if(!empty($filter_string)){ $filter_string .= "OR "; }
				// Check departments
				$filter_string .= "`program_id`=$filter ";				
				
				// Add filters to the showing string
				if(!empty($now_showing)){ $now_showing .= ", "; }
				$program_qry = mysql_query("SELECT `name`, `online` FROM ". TBL_PROGRAM ." WHERE id=$filter LIMIT 1") or die(error_message("Could not access programs", mysql_error(),"4gp"));
				$program_name = mysql_fetch_assoc($program_qry);
				if($program_name['online']){ $now_showing .= "Online "; }
				$now_showing .= $program_name['name'];
			}
			
			$where_string .= $filter_string . ") ";
		}
		
	}
	
	// Add company filters to query
	if(isset($_POST['company']) && !empty($_POST['company']) && ($_POST['company'] !== "all" && $_POST['company'] !== $all && $_POST['company'] !== $all_two)) {
		$company = $_POST['company'];
		
		// Remove all from the array
		$index_all = array_search('all', $company);
		if($index_all !== FALSE) {
			unset($company[$index_all]);
		}
		
		$result = validate_input($company, "company", true, "name");
		$errors = array_merge($errors, $result);
		
		// Check for errors
		if(count($errors) == 0){
			
			// Check if there are already WHERE constraints
			$where_string .= "AND ";
			

			
			$filter_string = "";
			
			// Check if there is only one department to filter
			if(!is_array($company)){
				$company = array($company);
			}
					
			// Add each position filter
			foreach ($company as $filter) {
				// Check if there are already filters
				if(empty($filter_string)){
					$filter_string = "(";
				} else{
					$filter_string .= "OR ";
				}
				$filter_string .= "`company`='$filter' ";
				
				// Add filters to the showing string
				if(!empty($now_showing)){ $now_showing .= ", "; }
				$now_showing .= $filter;
			}

			$where_string .= $filter_string . ") ";
		}
	}
	
	// ORDER BY query string to add sorts to the MySQL query
	$sort_string = "ORDER BY last_name ASC ";
	
	// Change sort options
	if(isset($_POST['sort']) && !empty($_POST['sort']) && $_POST['sort'] !== "last_asc"){
		switch ($_POST['sort']) {
			case 'first_asc':
				$sort_string = "ORDER BY first_name ASC ";
				break;
			case 'grad_asc':
				$sort_string = "ORDER BY grad_date ASC ";
				$where_string .= "AND `grad_date` IS NOT NULL ";
				$now_showing .= " (with graduation dates)";
				break;
			case 'grad_desc':
				$sort_string = "ORDER BY grad_date DESC ";
				$where_string .= "AND `grad_date` IS NOT NULL ";
				$now_showing .= " (with graduation dates)";
				break;
		}
	}
	
	// Remove extra spacing
	//$where_string = preg_replace('!\s+!', ' ', $where_string);
	//$return_content .= "<pre>$where_string</pre>";
		
	// Check if any filters have been added to the showing string
	if($now_showing === ""){
		$now_showing = "all";
	}	
	
	if(count($errors) == 0){	
		// Count the total number of rows 
		$count_qry = mysql_query("SELECT COUNT(`profile_id`) FROM ". TBL_DETAILS . " $where_string") 
					or die(error_message("Could not access profile", mysql_error(),"5gp"));
		$count = mysql_fetch_array($count_qry);
		$num_results = $count[0];
	
		// LIMIT query string to paginate the results
		$limit_string = "";
		$cur_page = 1;
		$num_pages = 1;

		// Change page number
		if(isset($_POST['show']) && !empty($_POST['show']) && isset($_POST['page']) && !empty($_POST['page'])){		
			$cur_page = $_POST['page'];
			$result = validate_input($cur_page, "page", true, "num");
			$errors = array_merge($errors, $result);

			$items_per_page = $_POST['show'];
			$result = validate_input($items_per_page, "profiles per page", true, "num");
			$errors = array_merge($errors, $result);

			// Check for errors
			if(count($errors) == 0){
				$num_pages = ceil($num_results/$items_per_page);
			
				$offset = ($cur_page-1) * $items_per_page;
				$limit_string = "LIMIT $offset, $items_per_page";
			}
		}
		
		if(count($errors) == 0){
			$profiles_qry = mysql_query("SELECT *, AES_DECRYPT(en_email, '". SALT ."') AS email 
								   	   FROM ". TBL_DETAILS .
							     " $where_string $sort_string $limit_string") 
					or die(error_message("Could not access profile", mysql_error(),"6gp"));
		}
	}
	// Check if there are errors			
	if (count($errors) > 0){
		// Print errors
		$return_content .= '<ul class="error alert"><span>Please correct the following:</span>';
		foreach($errors as $e) {
			$return_content .= "<li>".$e ."</li>";
		}
		$return_content .= '</ul>';
	} 
	// Check that there are profiles for the current search/filters
	elseif(mysql_num_rows($profiles_qry) == 0) {
		// No matches
		$return_content .= "<p class=\"alert\">Sorry, there are no matches for this search.</p>";
	} else { 
		
		// Print now showing message
		$return_content .= '<div class="alert" id="now-showing">Showing: '.ucwords($now_showing).'<span class="badge badge-warning" id="num-results">'.$num_results.'</span></div>';
		
		// Build profile entry
		while($profile = mysql_fetch_assoc($profiles_qry)) {		
			$return_content .= get_profile($profile, false);
		}
		
		// Add page numbers
		if($num_pages > 1) {
			$return_content .= "<div class=\"pagination pagination-centered\">
			  		<ul>";
		
			$next_page = (($cur_page + 1)>$num_pages) ? -1 : $cur_page + 1 ;
			$prev_page = (($cur_page - 1)<1) ? -1 : $cur_page - 1 ;
		
			// Add previous page button
			$page_string = "<li";
			if($prev_page < 0) { $page_string .= " class=\"disabled\""; }
			$page_string .= " onclick=\"page_link($prev_page)\"";
			$page_string .= "><a href=\"#\">&lt;</a></li>";
		
			// Add current block of pages
			$cur_block = "<li class=\"active\" onclick=\"page_link(-1)\"><a href=\"#\">$cur_page</a></li>";
			if($prev_page > 0) { $cur_block = "<li onclick=\"page_link($prev_page)\"><a href=\"#\">$prev_page</a></li>" . $cur_block; }
			if($next_page > 0) { $cur_block .= "<li onclick=\"page_link($next_page)\"><a href=\"#\">$next_page</a></li>"; }
		
			// Add pages before
			if($prev_page > (1+2)){
				$cur_block = "<li onclick=\"page_link(-1)\" class=\"disabled\"><a href=\"#\">...</a></li>" . $cur_block;
			}
			if($prev_page >= (1+2)){
				$cur_block = "<li onclick=\"page_link(2)\"><a href=\"#\">2</a></li>" . $cur_block;
			}
			if($prev_page >= (1+1)){
				$cur_block = "<li onclick=\"page_link(1)\"><a href=\"#\">1</a></li>" . $cur_block;
			}
			// Add pages after
			if($next_page < ($num_pages-2) && $next_page > 0){
				$cur_block .= "<li onclick=\"page_link(-1)\" class=\"disabled\"><a href=\"#\">...</a></li>";
			}
			if($next_page <= ($num_pages-2) && $next_page > 0){
				$cur_block .= "<li onclick=\"page_link(".($num_pages-1).")\"><a href=\"#\">".($num_pages-1)."</a></li>";
			}
			if($next_page <= ($num_pages-1) && $next_page > 0){
				$cur_block .= "<li onclick=\"page_link($num_pages)\"><a href=\"#\">$num_pages</a></li>";
			}
		
			$page_string .= $cur_block;
		
			// Add next page button
			$page_string .= "<li";
			if($next_page < 0) { $page_string .= " class=\"disabled\""; } 
			$page_string .= " onclick=\"page_link($next_page)\"";
			$page_string .= "><a href=\"#\">&gt;</a></li>";
				
			$return_content .= $page_string;
			$return_content .= "</ul>
					</div> ";
		}
		
	}
	
	$return = array(
	    "content" => utf8_encode($return_content) . " ",
	    "num_pages" => "$num_pages"
	);
	
	$json_return = json_encode($return);
	
	echo $json_return;
}

/* Returns HTML profile from a profile array. */
function get_profile($profile, $expanded){
	$return_content = '<div class="profile" id="profile-'. $profile['profile_id'] .'">';
	
	// Add profile photo
	if(!empty($profile['photo'])){
		// Add profile photo
		$return_content .= '<img src="'. $profile['photo'] .'"/>';
	} else {
		// Add empty photo
		$return_content .= '<img src="'. BASE .'/images/clear.gif" class="blank"/> ';
	}

	$return_content .= '<div class="profile-content">';

	// Add first and last name
	$return_content .= '<h3>'.$profile['first_name'].' '.$profile['last_name'].'</h3>';
	
	$return_content .= '<h4>';
	
	// Add student program title & role (i.e. Undergraduate Researcher)
	if(!empty($profile['program_id']) && $profile['position'] === "student"){
		$program_qry = mysql_query("SELECT `name`, `role` FROM ". TBL_PROGRAM ." WHERE id=".$profile['program_id']. " LIMIT 1") or die(error_message("Could not access programs", mysql_error(),"7gp"));
		$program = mysql_fetch_assoc($program_qry);
		$return_content .= ucwords($program['name']).' '. ucwords($program['role']);
	}
	
	// Add title for non students (if it exists)
	if(!empty($profile['title']) && $profile['position'] !== "student"){
		$return_content .= ucwords(html_entity_decode($profile['title']));
	}
	
	// Add alumni company (if it exists)
	if($profile['position'] === "alumni" && !empty($profile['company'])){
		$return_content .= ', '. ucwords(html_entity_decode($profile['company']));
		
		// Add city, state, country details (if they exist)
		if(!empty($profile['city'])){
			$return_content .= ' (' . ucwords($profile['city']);
			if(!empty($profile['state_id'])){
				$state_qry = mysql_query("SELECT `name` FROM ". TBL_US_STATE ." WHERE id=".$profile['state_id']. " LIMIT 1") or die(error_message("Could not access states", mysql_error(),"8gp"));
				$state = mysql_fetch_assoc($state_qry);
				
				$return_content .= ', '. ucwords($state['name']);
			} elseif (!empty($profile['country_id'])){
				$country_qry = mysql_query("SELECT `name` FROM ". TBL_COUNTRY ." WHERE id=".$profile['country_id']. " LIMIT 1") or die(error_message("Could not access countries", mysql_error(),"9gp"));
				$country = mysql_fetch_assoc($country_qry);
				
				$return_content .= ', '. ucwords($country['name']);
			}
			$return_content .= ')';
		}
	}
	
	// Add alumni program title
	if(!empty($profile['program_id']) && $profile['position'] === "alumni"){
		$program_qry = mysql_query("SELECT `abbreviation`, `name`  FROM ". TBL_PROGRAM ." WHERE id=".$profile['program_id']. " LIMIT 1") or die(error_message("Could not access programs", mysql_error(),"10gp"));
		$program = mysql_fetch_assoc($program_qry);
		$return_content .=  '<br/>Alum, ';
		
		if(!empty($program['abbreviation'])){
			$return_content .= ucwords($program['abbreviation']);
		} else {
			$return_content .= ucwords($program['name']);
		}
	}
	
	// Add department (if it exists)
	if(!empty($profile['department_id'])){
		$department_qry = mysql_query("SELECT `name` FROM ". TBL_DEPARTMENT ." WHERE id=".$profile['department_id']. " LIMIT 1") or die(error_message("Could not access departments", mysql_error(),"11gp"));
		$department = mysql_fetch_assoc($department_qry);
		$return_content .= ', '. $department['name'];
	}
	
	// Add alumni grad term and year
	if(!empty($profile['program_id']) && $profile['position'] === "alumni"){
		$return_content .= ' (';
		$grad_date = explode("-", $profile['grad_date']);
		$return_content .= month_to_season($grad_date[1])." ". $grad_date[0] . ')';
	}
	
	// Add comajor (if it exists)
	if(!empty($profile['comajor_department_id']) ){
		$return_content .= '<br/>';
		$department_qry = mysql_query("SELECT `name` FROM ". TBL_DEPARTMENT ." WHERE id=".$profile['comajor_department_id']. " LIMIT 1") or die(error_message("Could not access departments", mysql_error(),"12gp"));
		$department = mysql_fetch_assoc($department_qry);
		
		$program_qry = mysql_query("SELECT `name`, `abbreviation` FROM ". TBL_PROGRAM ." WHERE id=".$profile['comajor_program_id']. " LIMIT 1") or die(error_message("Could not access programs", mysql_error(),"13gp"));
		$program = mysql_fetch_assoc($program_qry);
		
		$return_content .= $department['name'] .", ";
		if(!empty($program['abbreviation'])){
			$return_content .= ucwords($program['abbreviation']);
		} else {
			$return_content .= ucwords($program['name']);
		}
		$return_content .= " Comajor";
	}
	 
	// Add title for students (if it exists)
	if($profile['position'] === "student" && !empty($profile['title'])){
		$return_content .= '<br/>';
		$return_content .= ucwords(html_entity_decode($profile['title']));
	}
	
	// Add student's company (if it exists)
	if($profile['position'] === "student" && !empty($profile['company'])){
		$return_content .= ', '. ucwords(html_entity_decode($profile['company']));
		
		// Add city, state, country details (if they exist)
		if(!empty($profile['city'])){
			$return_content .= ' (' . ucwords($profile['city']);
			if(!empty($profile['state_id'])){
				$state_qry = mysql_query("SELECT `name` FROM ". TBL_US_STATE ." WHERE id=".$profile['state_id']. " LIMIT 1") or die(error_message("Could not access states", mysql_error(),"14gp"));
				$state = mysql_fetch_assoc($state_qry);
				
				$return_content .= ', '. ucwords($state['name']);
			} elseif (!empty($profile['country_id'])){
				$country_qry = mysql_query("SELECT `name` FROM ". TBL_COUNTRY ." WHERE id=".$profile['country_id']. " LIMIT 1") or die(error_message("Could not access countries", mysql_error(),"15gp"));
				$country = mysql_fetch_assoc($country_qry);
				
				$return_content .= ', '. ucwords($country['name']);
			}
			$return_content .= ')';
		}
	}
	
	$return_content .= '</h4>';
	
	
	// Add email address (if it exists)
	if(!empty($profile['email'])){
		$return_content .= '<span class="contact-info"><h6>Email:</h6> <a href="mailto:'. $profile['email'] . '">'. $profile['email'] .'</a></span>';
	}
	
	// Add phone (if it exists)
	if(!empty($profile['phone'])){
		$return_content .= '<span class="contact-info"><h6>Phone:</h6> <a href="tel:'. $profile['phone'] . '" class="phone">'. $profile['phone'] .'</a></span>';
	}
	
	// Add office (if it exists)
	if(!empty($profile['office_location'])){
		$return_content .= '<span class="contact-info"><h6>Office:</h6> '. $profile['office_location'].'</span>';
	}
	
	// Add collapsable details
	if(!$expanded){
		$return_content .= '<a href="#" class="expand-profile btn" onclick="toggle_details('.$profile['profile_id'].')" id="toggle-'.$profile['profile_id'].'">+ more</a>'; 
		$return_content .= '<div class="expand-profile hidden" id="expand-profile-'. $profile['profile_id'] .'">';
	}else{
		$return_content .= '<div class="full-profile">';
	}
	
	// Add Major Professor(s)
	$major_prof_qry = mysql_query("SELECT ". TBL_DETAILS .".first_name, ". TBL_DETAILS .".last_name, ". TBL_DETAILS .".profile_id, ". TBL_DETAILS .".position    
									FROM `". TBL_DETAILS ."` 
								   	JOIN `". TBL_PROFILE_PROFILE_MAP ."`
									  ON (". TBL_DETAILS .".profile_id=". TBL_PROFILE_PROFILE_MAP .".profile_a_id OR ". TBL_DETAILS .".profile_id=". TBL_PROFILE_PROFILE_MAP .".profile_b_id)  
								   WHERE (".TBL_PROFILE_PROFILE_MAP.".profile_a_id=".$profile['profile_id']. " OR ".TBL_PROFILE_PROFILE_MAP.".profile_b_id=".$profile['profile_id']. ") AND ".TBL_PROFILE_PROFILE_MAP.".relationship_id=1 ORDER BY ". TBL_DETAILS .".last_name ASC") or die(error_message("Could not access related profiles", mysql_error(),"16gp"));
	if(mysql_num_rows($major_prof_qry) > 0) {
		$return_content .= '<h6>Major Professor';
		if(mysql_num_rows($major_prof_qry) > 1 && ($profile['position'] == "student" || $profile['position'] == "alumni") ){
			$return_content .= 's';
		} elseif($profile['position'] == "faculty"){
			$return_content .= ' for';
		}
		$return_content .= ':</h6><div class="paragraph">';
		
		$cur_name_string = "";
		$past_name_string = "";
		// Build interests string
		while($related_profile = mysql_fetch_assoc($major_prof_qry)) {
			if($related_profile['profile_id'] !== $profile['profile_id']){
				if(($profile['position'] == "faculty" && $related_profile['position'] == "student") || $profile['position'] == "student" || $profile['position'] == "alumni"){
					if(!empty($cur_name_string)){ $cur_name_string .= "<br/>"; }
					$cur_name_string .= '<a href="'. BASE .'/profile.php?p='.$related_profile['last_name']."_".str_replace(" ", "_", $related_profile['first_name']).'" class="profile-link" id="profile-'. $profile['profile_id'] .'-'. $related_profile['profile_id'] .'">'. $related_profile['first_name'] .' '. $related_profile['last_name'] .'</a>';
				} elseif($profile['position'] == "faculty" && $related_profile['position'] == "alumni") {
					if(!empty($past_name_string)){ $past_name_string .= "<br/>"; }
					$past_name_string .= '<a href="'. BASE .'/profile.php?p='.$related_profile['last_name']."_".str_replace(" ", "_", $related_profile['first_name']).'" class="profile-link past-related-profile" id="profile-'. $profile['profile_id'] .'-'. $related_profile['profile_id'] .'">'. $related_profile['first_name'] .' '. $related_profile['last_name'] .'</a>';
				}
			}
		}
		$return_content .= $cur_name_string;
		
		if(!empty($past_name_string)){
			$return_content .= '<br/><small><a href="#" class="show-past-profile" id="show-past-profile-mp-'.$profile['profile_id'].'" onclick="show_past_profiles('.$profile['profile_id'].', \'mp\')">[ Show Graduated Students ]</a></small><div id="past-mp-'.$profile['profile_id'].'" class="hide">'.$past_name_string.'</div>';
		}
		
		$return_content .= '</div>';
	}
	
	// Add Committee Member(s)
	$committee_qry = mysql_query("SELECT ". TBL_DETAILS .".first_name, ". TBL_DETAILS .".last_name, ". TBL_DETAILS .".profile_id, ". TBL_DETAILS .".position  
									FROM `". TBL_DETAILS ."` 
								   	JOIN `". TBL_PROFILE_PROFILE_MAP ."`
									  ON (". TBL_DETAILS .".profile_id=". TBL_PROFILE_PROFILE_MAP .".profile_a_id OR ". TBL_DETAILS .".profile_id=". TBL_PROFILE_PROFILE_MAP .".profile_b_id)  
								   WHERE (".TBL_PROFILE_PROFILE_MAP.".profile_a_id=".$profile['profile_id']. " OR ".TBL_PROFILE_PROFILE_MAP.".profile_b_id=".$profile['profile_id']. ") AND ".TBL_PROFILE_PROFILE_MAP.".relationship_id=2 ORDER BY ". TBL_DETAILS .".last_name ASC") or die(error_message("Could not access related profiles", mysql_error(),"17gp"));
	if(mysql_num_rows($committee_qry) > 0) {
		$return_content .= '<h6>Committee Member';
		if(mysql_num_rows($committee_qry) > 1 && ($profile['position'] == "student" || $profile['position'] == "alumni") ){
			$return_content .= 's';
		} elseif($profile['position'] == "faculty"){
			$return_content .= ' for';
		}
		$return_content .= ':</h6><div class="paragraph">';
		
		$cur_name_string = "";
		$past_name_string = "";
		// Build interests string
		while($related_profile = mysql_fetch_assoc($committee_qry)) {
			if($related_profile['profile_id'] !== $profile['profile_id']){
				if(($profile['position'] == "faculty" && $related_profile['position'] == "student") || $profile['position'] == "student" || $profile['position'] == "alumni"){
					if(!empty($cur_name_string)){ $cur_name_string .= "<br/>"; }
					$cur_name_string .= '<a href="'. BASE .'/profile.php?p='.$related_profile['last_name']."_".str_replace(" ", "_", $related_profile['first_name']).'" class="profile-link" id="profile-'. $profile['profile_id'] .'-'. $related_profile['profile_id'] .'">'. $related_profile['first_name'] .' '. $related_profile['last_name'] .'</a>';
				} elseif($profile['position'] == "faculty" && $related_profile['position'] == "alumni") {
					if(!empty($past_name_string)){ $past_name_string .= "<br/>"; }
					$past_name_string .= '<a href="'. BASE .'/profile.php?p='.$related_profile['last_name']."_".str_replace(" ", "_", $related_profile['first_name']).'" class="profile-link past-related-profile" id="profile-'. $profile['profile_id'] .'-'. $related_profile['profile_id'] .'">'. $related_profile['first_name'] .' '. $related_profile['last_name'] .'</a>';
				}
			}
		}
		$return_content .= $cur_name_string;
		if(!empty($past_name_string)){
			$return_content .= '<br/><small><a href="#" class="show-past-profile" id="show-past-profile-c-'.$profile['profile_id'].'" onclick="show_past_profiles('.$profile['profile_id'].', \'c\')">[ Show Graduated Students ]</a></small><div id="past-c-'.$profile['profile_id'].'" class="hide">'.$past_name_string.'</div>';
		}
		$return_content .= '</div>';
	}
	
	// Add expected graduation (for students)
	if($profile['position']=== 'student' && !empty($profile['grad_date'])){
		$grad_date = explode("-", $profile['grad_date']);
		
		$return_content .= '<h6>Expected Graduation:</h6><p>';
		$return_content .= month_to_season($grad_date[1]). " ";
		$return_content .= $grad_date[0].'</p>';
	}
	
	// Add dissertation title (for alumni)
	if($profile['position']=== 'alumni' && !empty($profile['dissertation_title'])){
		$return_content .= '<h6>Dissertation Title:</h6><p>'. html_entity_decode($profile['dissertation_title']) . '</p>';
	}
	
	// Add education (if exists)
	if(!empty($profile['education'])){
		$return_content .= '<h6>Education:</h6> <p class="profile-education">'. html_entity_decode(nl2br($profile['education'])) .'</p>';
	}
	
	// Add bio (if exists)
	if(!empty($profile['bio'])){
		$return_content .= '<h6>Bio:</h6> <p class="profile-bio">'. html_entity_decode(nl2br($profile['bio'])) .'</p>';
	}
	
	// Add Interests
	$interests_qry = mysql_query("SELECT ". TBL_INTEREST .".name, ". TBL_INTEREST .".id 
									FROM `". TBL_INTEREST ."` 
								   	JOIN `". TBL_PROFILE_INTEREST_MAP ."`
									  ON ". TBL_INTEREST .".id=". TBL_PROFILE_INTEREST_MAP .".interest_id
								   WHERE ".TBL_PROFILE_INTEREST_MAP.".profile_id=".$profile['profile_id']. " ORDER BY ". TBL_INTEREST .".name ASC") or die(error_message("Could not access interests", mysql_error(),"18gp"));
	if(mysql_num_rows($interests_qry) > 0) {
		$return_content .= '<h6 class="inline">Interests:</h6><span>';
		$interests_string = "";
		// Build interests string
		while($interest = mysql_fetch_assoc($interests_qry)) {
			if(!empty($interests_string)){ $interests_string .= ", "; }
			if(!$expanded){
				$interests_string .= '<a href="#" onclick="interest_link('.$interest['id'].')" class="interest-link" id="interest-'. $profile['profile_id'] .'-'. $interest['id'] .'">'. $interest['name'] .'</a>';
			} else{
				$interests_string .= '<a href="'. BASE .'#&s=&fp=all&fd=all&fi='.$interest['id'].'&fpr=all&fc=all&p=1&st=last_asc&sh=25'. $profile['profile_id'] .'" class="interest-link" id="interest-'. $profile['profile_id'] .'-'. $interest['id'] .'">'. $interest['name'] .'</a>';
				
			}
		}
		$return_content .= $interests_string . '</span>';
	}
	
	// TODO: Groups
	// TODO: Links
	
	$return_content .= '<small class="update">Last updated <span>'. contextualTime(strtotime($profile['last_update'])) .'</span>.</small>';	
	
	$return_content .= '</div></div></div>';
	
	return $return_content;
	
}
?>
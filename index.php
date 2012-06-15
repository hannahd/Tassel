<?php
/**
 * Main Directory of Tassel.
 *
 * This page allows users to view the directory, as well 
 * as search, filter, and sort the profiles.
 * 
 * TODO: Add expand all, collapse all
 * TODO: Add link from profile to individual profile page
 * TODO: Add links & groups
 * @author Hannah Deering
 * @package Tassel
 **/
require_once ("constants/constants.php");
require_once ("constants/dbconnect.php"); //Includes database connection
require_once ("constants/controls.php"); //Includes functions
?>
<!DOCTYPE html>
<head>
	<?php echo get_head_meta("Directory"); ?>
	
	<script type="text/javascript">
	
		// Updates the profiles shown based on filters, search, sort and view
		function update_results(){
			var datastring = $('#filters').serialize() + 						
							 "&" + $("#filter-search").serialize() +
						 	 "&" + $("#view").serialize() +
							 "&" + $("#page").serialize() +
							 "&" + $.trim($('#advanced-search-params').text()); 
			$.ajax({
				type: "POST",
				url: "<?php echo BASE; ?>/constants/get_profile.php?action=get",
				data: datastring,
				success: function(response) {
					// Display directory table
					var obj = jQuery.parseJSON(response);
					$('#directory-profiles').show().html(obj.content);
					update_page_dd(obj.num_pages);
				}
			});
		}
		
		// Updates the url after ajax calls
		function update_url(){
			var datastring = "";
			// Add search
			datastring += "&s=" + $('#filter-search').val();
			
			// Add position
			datastring += "&fp=" + $('#filter-position').val();
			
			// Add department
			datastring += "&fd=" + $('#filter-department').val();
			
			// Add interests
			datastring += "&fi=" + $('#filter-interest').val();
			
			// Add program
			datastring += "&fpr=" + $('#filter-program').val();
			
			// Add employer
			datastring += "&fc=" + $('#filter-company').val();
			
			// Add page
			datastring += "&p=" + $('#top-page').val();
			
			// Add sort
			datastring += "&st=" + $('#sort-by').val();
			
			// Add show
			datastring += "&sh=" + $('#num-profiles').val();
			
			window.location.hash = "#"+datastring;
		}
		
		// Interprets the url into selected options on the page
		function decode_url(){
			var datastring = window.location.hash;
			var pairs = datastring.split('&');
			
			// Change values for any values set
			for (var i=0, n=pairs.length; i < n; i++) {
				p = pairs[i].split('=');
				switch(p[0]) {
					case "s":
					  // Change search
					  $('#filter-search').val(p[1]);
					  break;
					case "fp":
					  // Change position
					  $('#filter-position').val(p[1]);
					  break;
					case "fd":
					  // Change department
					  $('#filter-department').val(p[1]);
					  break;
					case "fi":
					  // Change interests
					  $('#filter-interest').val(p[1]);
					  break;
					case "fpr":
					  // Change program
					  $('#filter-program').val(p[1]);
					  break;
					case "fc":
					  // Change employer
					  $('#filter-company').val(p[1]);
					  break;
					case "p":
					  // Change page
					  $('#top-page').val(p[1]);
					  break;
					case "st":
					  // Change sort
					  $('#sort-by').val(p[1]);
					  break;
					case "sh":
					  // Change show
					  $('#num-profiles').val(p[1]);
					  break;
				}
			}
		}
		
		// Updates the number of pages in the dropdown menu
		function update_page_dd(num_pages){
			var selected = $('#top-page :selected').val();
			if(num_pages > 1){
				var ret_val = "";
				for (var i=1; i <= num_pages; i++) {
					ret_val += '<option value="'+ i + '">' + i + '</option>';
				};
				$('#top-page-label').show();
				$('#top-page').show().html(ret_val);
			} else {
				$('#top-page-label').hide();
				$('#top-page').hide();
			}
			$('#top-page').val(selected);
		}
		
		// Displays profiles with certain interest (id)
		function interest_link(id){
			window.event.preventDefault();
			clear_filters(false);
			$("#filter-interest").val(id);
			update_url();
		}
		
		// Shows block of former students
		function show_past_profiles(id, preface){
			window.event.preventDefault();
			var div_id = "#past-" + preface + "-" + id;
			var link_id = "#show-past-profile-" + preface + "-" + id;
			$(div_id).show();
			$(link_id).hide();
		}
		
		// Displays a given page of results
		function page_link(pg){
			if(pg > 0){
				$("#top-page").val(pg);
				$('body,html').animate({scrollTop: 0}, 400);
				update_url();
			}
			window.event.preventDefault();
			return false;
		}
		
		// Shows or hides the expanded profile
		function toggle_details(id){
			$('#expand-profile-'+id).toggle('fast', function() {
			    if($(this).css('display') == "none") {
					$('#toggle-'+id).html("+ more");
				} else {
					$('#toggle-'+id).html("- less");
				}
			  });
			window.event.preventDefault();
			return false;
		}
		
		// Clears all filter, searches and view options
		function clear_filters(refresh){
			$("#filter-position").val("all");
			$("#filter-department").val("all");
			$("#filter-interest").val("all");
			$("#filter-program").val("all");
			$("#filter-company").val("all");
			$("#filter-search").val("");
			$('#advanced-search-params').text("");
			if(refresh){
				window.location.hash = "";
			}
		}
		
		// Watches for a url change and makes the necessary changes to the page
		// Necessary to keep back and forward working through ajax calls
		$(window).bind( 'hashchange', function(e) {
			decode_url();
			update_results();
		});
		
		
		$(document).ready(function(){
			// Initially set page
			$('#top-page-label').hide();
			$('#top-page').hide();
			decode_url();
			update_results();
			
			// Updates results based on changed values
			$(".update").change(function() {
				if($(this).attr('id') != 'top-page') {
					$('#top-page').val(1);
				}
				update_url();	
			});
			
			// Clears all filters
			$("#clear").click(function(event) {
				event.preventDefault();
				clear_filters(true);
			});
			
			// Updates results as user types
			$("#filter-search").keyup(function() {
				update_results();	
			});
			
			// Updates url when done typing in search box
			$("#filter-search").focusout(function() {
				update_url();	
			});
			
			// Show spinning wheel when ajax calls are processing
			var showLoader;
			
			$('#loading')
				  .hide()  // hide it initially
		    .ajaxStart(function() {
				showLoader = window.setTimeout("$('#loading').fadeIn('fast')",50);
		    })
		    .ajaxStop(function() {
				window.clearTimeout(showLoader);
		        $(this).fadeOut("fast");
		    });
		});
		
	</script>
</head>
<body>
	<?php include 'constants/navbar.php'; ?>
	<noscript><div class="container"><div class="row"><div class="span4 offset4"><p class="error alert"Javascript must be enabled to view this directory.</p></div></div></div></noscript>
	<div class="container">
		<div class="row">
			<div class="span3 well sidebar"> 
				<h4>Search</h4>
				<input class="span3 search-query" id="filter-search" name="search[]" size="16" type="text" placeholder="Search">
				<hr/>
				<h4>Browse</h4>
				<form name="filters" id="filters">
					<label for="filter_position">Position</label>
					<select name="position[]" id="filter-position" class="span3 update filter">
						<option value="all">All</option>
						<option value="alumni">Alumni</option>
						<option value="faculty">Faculty</option>
						<option value="staff">Staff</option>
						<option value="student">Student</option>
						<option value="visitor">Visitor</option>
					</select>
					
					<label for="filter-program">Program</label>
					<select name="program[]" id="filter-program" class="span3 update filter">
						<option value="all">Any</option>
						<?php
							/*Populate programs*/
							echo program_control("dropdown", "program", "", "");
						?>
					</select>
				
					<label for="filter-department">Department</label>
					<select name="department[]" id="filter-department" class="span3 update filter">
						<option value="all">Any</option>
						<?php
							/*Populate departments*/
							echo all_departments_control(true, "dropdown", "department", "", "");
						?>
					</select>
					
					<label for="filter-interest">Interests</label>
					<select name="interest[]" id="filter-interest" class="span3 update filter">
						<option value="all">Any</option>
						<?php
							/*Populate interest*/
							echo interest_control("", true, "dropdown", "interest", "", "");
						?>
					</select>
				
					<label for="filter-company" class="filter">Employer</label>
					<select name="company[]" id="filter-company" class="span3 update filter">
						<option value="all">Any</option>
						<?php
							/*Populate companies*/
							echo company_control("dropdown", "company", "", "");
						?>
					</select>
				</form>
				<hr />
				<small><a href="#" id="clear">clear</a> | <a href="<?echo BASE;?>/advanced_search.php">advanced options</a></small>
			</div>
			<div class="span9">
				<div class="well" id="view-option">
					<form name="page" id="page" class="form-inline">
						<label for="top-page" id="top-page-label" class="view-controls first">Page</label>
						<select name="page" id="top-page" class="view-controls mini update">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
						</select>
						<label id="loading" style="display: none; "><img class="blank" src="<? echo BASE;?>/images/ajax-loader.gif" alt="Updating..."></label>
					</form>
					
					<form name="view" id="view" class="form-inline">
						<label for="sort-by" class="view-controls first">Sort by</label>
						<select name="sort" id="sort-by" class="view-controls update">
							<option value="last_asc">Last Name</option>
							<option value="first_asc">First Name</option>
							<option value="grad_asc">Earliest Graduation</option>
							<option value="grad_desc">Latest Graduation</option>
						</select>
						
						<label for="num-profiles" class="view-controls">Show</label>
						<select name="show" id="num-profiles" class="mini view-controls update">
							<option value="10">10</option>
							<option value="25" selected="selected">25</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="200">200</option>
						</select>
					</form>
				</div>
				
				<!-- Directory Entries-->
				<div id="directory-profiles"></div>
				
				<div id="footer"></div>
				<div class="hidden" id="advanced-search-params">
					<?php
						if ($_POST) {
						  $kv = array();
						  foreach ($_POST as $key => $value) {
						    if(is_array($value)){
								foreach ($value as $val_value) {
									$kv[] = "$key"."[]=$val_value";	
								}
							} else{
								$kv[] = "$key=$value";
							}
						  }
						  $query_string = join("&", $kv);
						  echo $query_string;
						}
					?>
				</div>
			</div>
		</div>
	</div>	
</body>
</html>
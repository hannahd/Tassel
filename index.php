<?php
require_once ("constants/constants.php");
require_once (ROOT."/constants/dbconnect.php"); //Includes database connection
require_once (ROOT."/constants/functions.php"); //Includes functions
require_once (ROOT."/constants/access-functions.php"); //Includes functions to control user privileges

if(isset($_SESSION['user_id'])){ secure_page(); }
?>
<!DOCTYPE html>
<head>
	<?php echo get_head_meta("Directory"); ?>
	
	<script type="text/javascript">
		$(document).ready(function(){
			// If none of the filters are set, don't display any profiles.
			$('#directory-profiles').hide();
			$('#directory-summary').show();
			$('.filter-program').hide();
			$('.filter-company').hide();
			
			$(".filter").change(function() {
				
				if($("#filter-position option:selected").val() == "student"){
					$(".filter-department").show();
					$(".filter-program").show();
					$(".filter-company").hide();
					$("#filter-company").val("all");
				} else if($("#filter-position option:selected").val() == "alumni"){
					$(".filter-department").show();
					$(".filter-program").show();
					$(".filter-company").show();
				} else if($("#filter-position option:selected").val() == "faculty"){
					$(".filter-department").show();
					$(".filter-program").hide();
					$("#filter-program").val("all");
					$(".filter-company").hide();
					$("#filter-company").val("all");
				} else if($("#filter-position option:selected").val() == "staff"){
					$(".filter-department").hide();
					$("#filter-department").val("all");
					$(".filter-program").hide();
					$("#filter-program").val("all");
					$(".filter-company").hide();
					$("#filter-company").val("all");
				} else if($("#filter-position option:selected").val() == "visitor"){
					$(".filter-department").show();
					$(".filter-program").hide();
					$("#filter-program").val("all");
					$(".filter-company").hide();
					$("#filter-company").val("all");
				}
				
					
				var datastring = $('#filters').serialize();
			
				$.ajax({
					type: "POST",
					url: "<?php echo BASE; ?>/constants/process.php?action=get",
					data: datastring,
					success: function(response) {
						$('#directory-summary').hide();
						
						// Display directory table
						$('#directory-profiles').show().html(response);
					}
				});
			});
			
			$("#clear").click(function() {
				$('#directory-profiles').hide();
				$('#directory-summary').show();
				$('.filter-department').show();
				$('.filter-program').hide();
				$('.filter-company').hide();
				$("#filter-position").val("all");
				$("#filter-department").val("all");
				$("#filter-program").val("all");
				$("#filter-company").val("all");
				$("#filter-search").val("");
			});
			
			$("#filter-search").keyup(function() {
				
				var datastring = $('#filters').serialize() 
								 + "&" + $("#filter-search").serialize();
				
				$.ajax({
					type: "POST",
					url: "<?php echo BASE; ?>/constants/process.php?action=get",
					data: datastring,
					success: function(response) {
						$('#directory-summary').hide();
						
						// Display directory table
						$('#directory-profiles').show().html(response);
					}
				});
			});
			
			
		
			/*var auto_refresh = setInterval(function(){
					$('#directory-profiles').load("<?php echo BASE; ?>/constants/process.php?action=get");
			}, 500000); //Refresh every 10 minutes */
			
		});
	</script>
</head>
<body>
	<?php include ROOT.'/constants/navbar.php'; ?>
	<noscript><div class="container"><div class="row"><div class="span4 offset4"><p class="error alert"Javascript must be enabled to view this directory.</p></div></div></div></noscript>
	<div class="container">
		<div class="row">
			<div class="span2 well sidebar"> 
				<h4>Search</h4>
				<input class="span2 search-query" id="filter-search" name="search" size="16" type="text" placeholder="Search">
				<hr/>
				<h4>Browse</h4>
				<form name="filters" id="filters">
					<label for="filter_position" class="filter-position">Position</label>
					<select name="filter_position" id="filter-position" class="span2 filter filter-position">
						<option value="all">All</option>
						<option value="faculty">Faculty</option>
						<option value="staff">Staff</option>
						<option value="alumni">Alumni</option>
						<option value="student">Student</option>
						<option value="visitor">Visitor</option>
					</select>
				
					<label for="filter_department" class="filter-department">Department</label>
					<select name="filter_department" id="filter-department" class="span2 filter filter-department">
						<option value="all">Any</option>
						<?php
						/*Populate departments*/
						echo get_all_department_options();
						?>
					</select>
					
					<label for="filter_program" class="filter-program">Program</label>
					<select name="filter_program" id="filter-program" class="span2 filter filter-program">
						<option value="all">Any</option>
						<?php
						/*Populate programs*/
						echo get_all_program_options();
						?>
					</select>
				
					<label for="filter_company" class="filter-company">Employer</label>
					<select name="filter_company" id="filter-company" class="span2 filter filter-company">
						<option value="all">Any</option>
						<?php
						/*Populate companies*/
						echo get_all_company_options();
						?>
					</select>
				</form>
				<hr />
				<small><a href="#" id="clear">clear</a> | <a href="#">advanced options</a></small>
			</div>
			<div class="span9">
				<p class="well" id="directory-summary">With more than 70 faculty members from 37 departments, the HCI Graduate Program is truly an interdisciplinary degree program. Led by James H. Oliver, the Director of Graduate Education, and Stephen Gilbert, the Associate Director of Graduate Education, graduates of our program are well prepared for careers in business and industry, academia, or to continue their studies. Our graduates are employed in companies, including Microsoft, Boeing, Lockheed Martin, Deere and Co., Rockwell Collins, EA Interactive, Google, Garmin, and many others.
					<br /><br /><strong>Please use the search or menus on the left to find the people that make up this great program.</strong></p>
				
				<!-- Directory Entries-->
				<div id="directory-profiles"></div>
			</div>
		</div>
	</div>	
</body>
</html>
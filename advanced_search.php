<?php
/**
 * Advanced Search and Filter for Tassel.
 *
 * This page allows users to filter the directory with 
 * multiple constraints.
 * 
 * TODO: Add search restriction options
 * 
 * @author Hannah Deering
 * @package Tassel
 **/
require_once ("constants/constants.php");
require_once ("constants/dbconnect.php"); //Includes database connection
require_once ("constants/controls.php"); //Includes functions

?>
<!DOCTYPE html>
<head>
	<?php echo get_head_meta("Advanced Search"); ?>
	
	<script type="text/javascript">
		// Returns all clicked values to default
		function clear(){
			$(".not-all").prop("checked", false);
			$(".all").prop("checked", true);
			$("#search").val("");
		}
		
		$(document).ready(function(){
			// Returns all clicked values to default
			$(".clear").click(function(event) {
				event.preventDefault();
				clear();
			});
			
			// Unchecks all values when all is selected
			$(".all").change(function(){
				var myClass = $(this).attr("class");
				var classes = myClass.split(' ');
				
				if($(this).is(':checked')){
					$(".not-all." + classes[1]).prop("checked", false);
				}
			});
			
			// Unchecks all when any other values are selected
			$(".not-all").change(function(){
				var myClass = $(this).attr("class");
				var classes = myClass.split(' ');
				
				if($(this).is(':checked')){
					$(".all." + classes[1]).prop("checked", false);
				}
			});
		});
	</script>
</head>
<body>
	<?php include 'constants/navbar.php'; ?>
	<noscript><div class="container"><div class="row"><div class="span4 offset4"><p class="error alert"Javascript must be enabled to view this directory.</p></div></div></div></noscript>
	<div class="container">
		<div class="row">
			<div class="span12"> 
				<form name="advanced" id="advanced-search" action="index.php" method="post">
					<h2 class="pull-left">Advanced Options</h2>
					<input type="submit" class="btn btn-primary pull-right search" value="Search"/> <button class="btn pull-right clear" href="#">Clear</button>
					<hr/>
					<div class="input-append">
						<label for="search">Keywords</label>
					  	<input class="span9 search-query" id="search" name="search[]" size="16" type="text"><button class="btn" href="#">Keyword Search</button>
						<small style="display:block; color: #999">Try entering names, emails, departments, or even interests to a find specific person.</small>
					</div>
					<hr/>
				
					<h3>Position</h3>
					<label class="checkbox">
						<input type="checkbox" name="position[]" value="all" class="all position" checked="checked"> Any
					</label>
					<label class="checkbox">
						<input type="checkbox" name="position[]" value="faculty" class="not-all position"> Faculty
					</label>
					<label class="checkbox">
						<input type="checkbox" name="position[]" value="staff" class="not-all position"> Staff
					</label>
					<label class="checkbox">
						<input type="checkbox" name="position[]" value="alumni" class="not-all position"> Alumni
					</label>
					<label class="checkbox">
						<input type="checkbox" name="position[]" value="student" class="not-all position"> Student
					</label>
					<label class="checkbox">
						<input type="checkbox" name="position[]" value="visitor" class="not-all position"> Visitor
					</label>
					<hr/>
					
					<h3>Program</h3>
					<label class="checkbox">
						<input type="checkbox" name="program[]" value="all" class="all program" checked="checked"> Any
					</label>
					<?php
						/*Populate programs*/
						echo program_control("checkbox", "program", "", "");
					?>	
					<hr/>
					
					<h3>Department</h3>
					<label class="checkbox">
						<input type="checkbox" name="department[]" value="all" class="all department" checked="checked"> Any
					</label>
					<?php
						/*Populate departments*/
						echo all_departments_control(true, "checkbox", "department", "", "");
					?>
					<hr/>
					
					<h3>Interest</h3>
					<label class="checkbox">
						<input type="checkbox" name="interest[]" value="all" class="all interest" checked="checked"> Any
					</label>
					<?php
						/*Populate interest*/
						echo interest_control("", true, "checkbox", "interest", "", "");
					?>
					<hr/>
					
					<h3>Employer</h3>
					<label class="checkbox">
						<input type="checkbox" name="company[]" value="all" class="all company" checked="checked"> Any
					</label>
					<?php
						/*Populate companies*/
						echo company_control("checkbox", "company", "", "");
					?>
					
					<hr />
					<input type="submit" class="btn btn-primary pull-right search btn-large" value="Search"/> <button class="btn pull-right clear btn-large" id="btm-clear" href="#">Clear</button>
				</form>
			</div>
			
			<div id="footer"></div>
		</div>
	</div>	
</body>
</html>
<?php
	include_once 'includes/constant/constant.php';
	require_once ROOT.'/includes/constant/dbc.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Tassel Directory</title>
	<meta name="author" content="Hannah">
	
	<!-- SCRIPTS
	=============================================================== -->
	<script language="JavaScript" type="text/javascript" src="<?php echo BASE; ?>/includes/js/jquery-1.7.1.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo BASE; ?>/includes/js/jquery.validate.js"></script>
	
	<!-- STYLESHEETS
	=============================================================== -->
	<link rel="stylesheet" href="<?php echo BASE; ?>/includes/styles/base.css">
	<link rel="stylesheet" href="<?php echo BASE; ?>/includes/styles/layout.css">
	<link rel="stylesheet" href="<?php echo BASE; ?>/includes/styles/skeleton.css">
	<link rel="stylesheet" href="<?php echo BASE; ?>/includes/styles/default.css">
	
</head>
<body>
	<nav class="breadcrumb">
		<a href="#">This is a link</a> /
		<a href="#">Lorem link</a> / 
		<a href="#">Dolor link two</a> /
		<a href="#">Here is a link</a> /
		<strong>Active Link</strong>
	</nav>
	<h1>People</h1>
	<p id="description">With more than 69 faculty members from 36 departments, the HCI Graduate Program is truly an interdisciplinary degree program. Led by James H. Oliver, the Director of Graduate Education, and Stephen Gilbert, the Associate Director of Graduate Education, graduates of our program are well prepared for careers in business and industry, academia, or to continue their studies.</p>
	<input type="text" name="search" value="" id="search" />
	<a href="#">Advanced Search</a>
	<div class="browse">
	<h2>Browse</h2>
		<a href="#">Faculty <span>##</span></a>
		<a href="#">Graduate Students <span>##</span></a>
		<a href="#">Certificate Students <span>##</span></a>
		<a href="#">Post-Docs <span>##</span></a>
		<a href="#">Staff <span>##</span></a>
		<a href="#">Undergraduates <span>##</span></a>
		<a href="#">Visiting Scholars <span>##</span></a>
		<a href="#">Alumni <span>##</span></a>
	</div>
	
</body>
</html>

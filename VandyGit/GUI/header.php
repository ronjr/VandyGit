<!DOCTYPE html>
<html lang="en">
<head>
	<title><?=SITENAME?><?php if(isset($pageTitle)) echo ": $pageTitle"; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?=SITEROOT?>includes/css/site.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


<?php 
if(isset($headerHeadContent))
{
	echo $headerHeadContent;
}
?>

</head>

<body>

<div class="jumbotron container-fluid vandygit-header">
	<h1>Vandy Git Project</h1>
</div>

<nav class="navbar navbar-inverse">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span> 
			</button>
		</div>
		<div class="collapse navbar-collapse" id="myNavbar">
			<ul class="nav navbar-nav">
			    <li><a href="<?=SITEROOT?>index.php">&nbsp; Projects</a></li>
			    <li><a href="<?=SITEROOT?>database.php">&nbsp; Database Admin</a></li>
			</ul>
    </div>
  </div>
</nav>




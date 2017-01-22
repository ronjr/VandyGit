<?php 
require_once "../settings.inc.php";
require_once FILEROOT."BO/Project.php";
require_once FILEROOT."BLL/ProjectManager.php";

$pageTitle = "";

if(!isset($_GET['id'])) // no id, forward to home
{
	header('Location: '.SITEROOT);
}

$pm = new ProjectManager();
$project = $pm->GetById($_GET['id']);

if($project == null) // id not found in database, forward to home
{
	header('Location: '.SITEROOT);
}

include(FILEROOT . "GUI/header.php");

?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-10">
			<div class="well well-sm"><h3><?=$project->name?></h3></div>
		</div>
		<div class="col-sm-1"></div>
	</div>
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-5">
			<table class="table table-condensed table-bordered table-responsive table-striped">
				<tr><td>Project id:</td><td><?=$project->id?></td></tr>
				<tr><td>Name:</td><td><?=$project->name?></td></tr>
				<tr><td>Stars:</td><td><?=$project->stargazers_count?></td></tr>
				<tr><td>Created:</td><td><?=date(DATE_ISO8601, $project->created_at)?></td></tr>
				<tr><td>Updated:</td><td><?=date(DATE_ISO8601, $project->updated_at)?></td></tr>
				<tr><td>Pushed:</td><td><?=date(DATE_ISO8601, $project->pushed_at)?></td></tr>
			</table>
		</div>
		<div class="col-sm-5">
			<div class="well well-sm">
				<?=$project->description?>
			</div>
		</div>
		<div class="col-sm-1"></div>
	</div>
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-10">
			<a href="<?=SITEROOT?>" class="btn btn-default">Return to Projects</a>
		</div>
		<div class="col-sm-1"></div>
	</div>
</div>

<?php 

include(FILEROOT . "GUI/footer.php");

?>
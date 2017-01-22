<?php 
require_once "../settings.inc.php";
require_once FILEROOT."BO/Project.php";
require_once FILEROOT."BLL/ProjectManager.php";
require_once FILEROOT."BO/Setting.php";
require_once FILEROOT."BLL/SettingManager.php";

$pageTitle = "";

$sm = new SettingManager();
$updated = $sm->GetByName('time_imported');
$lastUpdated = "Not Imported Yet.  Please Reset Data.";
if($updated->value > "")
{
	$lastUpdated = date('m/d/Y H:m:s', $updated->value);
}

$message = "";
$pm = new ProjectManager();
if(isset($_GET['update']))
{
	$pm->UpdateGitObjectsByStars(10000);
	$message = "Data updated";
}
if(isset($_GET['reset']))
{
	$pm->ImportGitObjectsByStars(10000);
	$message = "Data imported";
}

include(FILEROOT . "GUI/header.php");

?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-10">
<?php 
if($message > "")
{
?>
			<div class="alert alert-success"><?=$message?></div>			
<?php 
}
?>
			<div class="alert alert-info">Database last updated: <?=$lastUpdated?></div>
			<p>
				This project pulls public projects with a Star Gazer Count of 10,000 or more.  (10,000 was chosen to help keep the queries under 
				the limit imposed by GitHub for efficiency.)  It could easily be modified to dynamic input and authenticated queries if needed.
			</p>
			<p>
				If no data has been pulled yet, please Reset Data.  If you want to pull the latest information, please Update Data. 
			</p>
			<div class="well">
				<a href="<?=SITEROOT?>database.php?update=yes" class="btn btn-primary">Update Data</a>  Use this to update the data from GitHub.
			</div>
			<div class="well">
				<a href="<?=SITEROOT?>database.php?reset=yes" class="btn btn-primary">Reset Data</a>  Use this to clear all data and download a fresh set from GitHub or initial import.
			</div>
			<a href="<?=SITEROOT?>" class="btn btn-default">Return to Projects</a>
		</div>
		<div class="col-sm-1"></div>
	</div>
</div>

<?php 

include(FILEROOT . "GUI/footer.php");

?>
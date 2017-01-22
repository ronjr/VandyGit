<?php 
require_once "../settings.inc.php";
require_once FILEROOT."BO/Project.php";
require_once FILEROOT."BLL/ProjectManager.php";

$pageTitle = "";


$pm = new ProjectManager();
//$error = $pm->UpdateGitObjectsByStars(10000);
$projects = $pm->GetAll();

$headerHeadContent = '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">';
$headerHeadContent .= '<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>';

include(FILEROOT . "GUI/header.php");

?>

<div class="container-fluid">
	<div class="well well-sm"><h3>GitHub public projects with Star Gazer Count >= 10000</h3></div>

	<table class="table table-striped table-bordered table-responsive table-condensed" id="projects">
		<thead><tr><th>Star Count</th><th>Name</th><th>Description</th></tr></thead>
		<tfoot><tr><th>Star Count</th><th>Name</th><th>Description</th></tr></tfoot><tbody>
<?php 
foreach($projects as $project)
{
?>
		<tr><td><?=$project->stargazers_count?></td><td><a href="<?=SITEROOT?>detail.php?id=<?=$project->id?>"><?=$project->name?></a></td><td><?=$project->description?></td></tr>
<?php 	
}
?>
	</tbody></table>
</div>

<?php 

include(FILEROOT . "GUI/footer.php");

?>
<script>
$(document).ready(function() {
	$('#projects').DataTable( {
		"iDisplayLength": 25	
	} );
} );
</script>
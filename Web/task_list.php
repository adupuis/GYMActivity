<?php
// Variable to configure global behaviour
$header_title = 'GENYMOBILE - Liste tâches';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$geny_task = new GenyTask();

?>

<script>
	jQuery(document).ready(function(){
	
		var oTable = $('#task_list').dataTable( {
			"bJQueryUI": true,
			"bAutoWidth": false,
			"sPaginationType": "full_numbers",
			"oLanguage": {
				"sSearch": "Recherche :",
				"sLengthMenu": "Tâche par page _MENU_",
				"sZeroRecords": "Aucun résultat",
				"sInfo": "Aff. _START_ à _END_ de _TOTAL_ enregistrements",
				"sInfoEmpty": "Aff. 0 à 0 de 0 enregistrements",
				"sInfoFiltered": "(filtré de _MAX_ enregistrements)",
				"oPaginate":{ 
					"sFirst":"Début",
					"sLast": "Fin",
					"sNext": "Suivant",
					"sPrevious": "Précédent"
				}
			}
		} );
	});
</script>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/task_generic.png"/><p>Tâche</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="task_list">
			Liste des tâches
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des tâches dans la base des tâches.
		</p>
		<style>
			@import 'styles/default/task_list.css';
		</style>
		<div class="table_container">
		<p>
			<table id="task_list">
			<thead>
				<tr>
					<th>Nom</th>
					<th>Description</th>
					<th>Éditer</th>
					<th>Supprimer</th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach( $geny_task->getAllTasks() as $task ){
					echo "<tr><td>$task->name</td><td>$task->description</td><td><a href='task_edit.php?load_task=true&task_id=$task->id' title='Éditer la tâche'><img src='images/$web_config->theme/task_edit_small.png' alt='Éditer la tâche' ></a></td><td><a href='task_remove.php?task_id=$task->id' title='Supprimer définitivement la tâche'><img src='images/$web_config->theme/task_remove_small.png' alt='Supprimer définitivement la tâche'></a></td></tr>";
				}
			?>
			</tbody>
			</table>
		</p>
		</div>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php
			include 'backend/widgets/task_add.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

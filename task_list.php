<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Liste tâches';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$geny_task = new GenyTask();

?>

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
		<p>
			<table class="object_list">
			<tr><th>Nom</th><th>Description</th><th>Éditer</th><th>Supprimer</th></tr>
			<?php
				foreach( $geny_task->getAllTasks() as $task ){
					echo "<tr><td>$task->name</td><td>$task->description</td><td><a href='task_edit.php?load_task=true&task_id=$task->id' title='Éditer la tâche'><img src='images/$web_config->theme/task_edit_small.png' alt='Éditer la tâche' ></a></td><td><a href='task_remove.php?task_id=$task->id' title='Supprimer définitivement la tâche'><img src='images/$web_config->theme/task_remove_small.png' alt='Supprimer définitivement la tâche'></a></td></tr>";
				}
			?>
			</table>
		</p>
	</p>
</div>

<?php
include_once 'footer.php';
?>

<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Edition tâche';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$geny_task = new GenyTask();
$db_status = '';

if( isset($_POST['create_task']) && $_POST['create_task'] == "true" ){
	if( isset($_POST['task_name']) && $_POST['task_name'] != "" ){
		if( $geny_task->insertNewTask('NULL',$_POST['task_name'],$_POST['task_description']) ){
			$db_status .= "<li class=\"status_message_success\">Tâche créée avec succès.</li>\n";
			$geny_task->loadTaskByName($_POST['task_name']);
		}
		else{
			$db_status .= "<li class=\"status_message_error\">Erreur lors de la création de la tâche.</li>\n";
		}
	}
	else {
		$db_status .= "<li class=\"status_message_error\">Certains champs obligatoires sont manquant. Merci de les remplir.</li>\n";
	}
}
else if( isset($_POST['load_task']) && $_POST['load_task'] == "true" ){
	if(isset($_POST['task_id'])){
		$geny_task->loadTaskById($_POST['task_id']);
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de charger la tâche : id non spécifié.</li>\n";
	}
}
else if( isset($_GET['load_task']) && $_GET['load_task'] == "true" ){
	if(isset($_GET['task_id'])){
		$geny_task->loadTaskById($_GET['task_id']);
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de charger la tâche : id non spécifié.</li>\n";
	}
}
else if( isset($_POST['edit_task']) && $_POST['edit_task'] == "true" ){
	if(isset($_POST['task_id'])){
		$geny_task->loadTaskById($_POST['task_id']);
		if( isset($_POST['task_name']) && $_POST['task_name'] != "" && $geny_task->login != $_POST['task_name'] ){
			$geny_task->updateString('task_name',$_POST['task_name']);
		}
		if( isset($_POST['task_description']) && $_POST['task_description'] != "" && $geny_task->description != $_POST['task_description'] ){
			$geny_task->updateString('task_description',$_POST['task_description']);
		}
		if($geny_task->commitUpdates()){
			$db_status .= "<li class=\"status_message_success\">Tâche mis à jour avec succès.</li>\n";
			$geny_task->loadTaskById($_POST['task_id']);
		}
		else{
			$db_status .= "<li class=\"status_message_error\">Erreur durant la mise à jour du tâche.</li>\n";
		}
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de modifier la tâche : id non spécifié.</li>\n";
	}
}
else{
// 	$db_status .= "<li class=\"status_message_error\">Aucune action spécifiée.</li>\n";
}


?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/task_generic.png"/><p>Tâche</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="task_edit">
			Modifier une tâche
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de modifier une tâche de la base des tâches.
		</p>
		<?php
			if( isset($db_status) && $db_status != "" ){
				echo "<ul class=\"status_message\">\n$db_status\n</ul>";
			}
		?>
		<script>
			$(".status_message").click(function () {
			$(".status_message").fadeOut("slow");
			});
		</script>
		
		<form id="select_login_form" action="task_edit.php" method="post">
			<input type="hidden" name="load_task" value="true" />
			<p>
				<label for="task_id">Séléction tâche</label>

				<select name="task_id" id="task_id" onChange="submit()">
					<?php
						$tasks = $geny_task->getAllTasks();
						foreach( $tasks as $task ){
							if( (isset($_POST['task_id']) && $_POST['task_id'] == $task->id) || (isset($_GET['task_id']) && $_GET['task_id'] == $task->id) )
								echo "<option value=\"".$task->id."\" selected>".$task->name."</option>\n";
							else if( isset($_POST['task_name']) && $_POST['task_name'] == $task->name )
								echo "<option value=\"".$task->id."\" selected>".$task->name."</option>\n";
							else
								echo "<option value=\"".$task->id."\">".$task->name."</option>\n";
						}
						if( $geny_task->id < 0 )
							$geny_task->loadTaskById( $tasks[0]->id );
					?>
				</select>
			</p>
		</form>

		<form id="start" action="task_edit.php" method="post">
			<input type="hidden" name="edit_task" value="true" />
			<input type="hidden" name="task_id" value="<?php echo $geny_task->id ?>" />
			<p>
				<label for="task_name">Nom</label>
				<input name="task_name" id="task_name" type="text" class="validate[required] text-input" value="<?php echo $geny_task->name ?>"/>
			</p>
			<p>
				<label for="task_description">Description</label>
				<textarea name="task_description" id="task_description" class="validate[required] text-input"><?php echo $geny_task->description ?></textarea>
			</p>
			<p>
				<input type="submit" value="Modifier" /> ou <a href="#form">annuler</a>
			</p>
		</form>
	</p>
</div>

<?php
include_once 'footer.php';
?>

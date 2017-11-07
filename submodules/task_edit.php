<?php

//  Copyright (C) 2011 by GENYMOBILE & Arnaud Dupuis
//  adupuis@genymobile.com
//  http://www.genymobile.com
// 
//  This program is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 3 of the License, or
//  (at your option) any later version.
// 
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
// 
//  You should have received a copy of the GNU General Public License
//  along with this program; if not, write to the
//  Free Software Foundation, Inc.,
//  59 Temple Place - Suite 330, Boston, MA  02111-1307, USA

// Variable to configure global behaviour


$geny_task = new GenyTask();
$gritter_notifications = array();

if( isset($_POST['create_task']) && $_POST['create_task'] == "true" ){
	if( isset($_POST['task_name']) && $_POST['task_name'] != "" ){
		$new_task_id = $geny_task->insertNewTask('NULL',$_POST['task_name'],$_POST['task_description']);
		if( $new_task_id > -1 ){
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Tâche créée avec succès.");
			$geny_task->loadTaskById($new_task_id);
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de la création de la tâche.");
		}
	}
	else {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir.");
	}
}
else if( isset($_POST['load_task']) && $_POST['load_task'] == "true" ){
	if(isset($_POST['task_id'])){
		$geny_task->loadTaskById($_POST['task_id']);
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de charger la tâche ','msg'=>"id non spécifié.");
	}
}
else if( isset($_GET['load_task']) && $_GET['load_task'] == "true" ){
	if(isset($_GET['task_id'])){
		$geny_task->loadTaskById($_GET['task_id']);
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de charger la tâche ','msg'=>"id non spécifié.");
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
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Tâche mis à jour avec succès.");
			$geny_task->loadTaskById($_POST['task_id']);
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour du tâche.");
		}
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de modifier la tâche ','msg'=>"id non spécifié.");
	}
}
else{
// 	$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Aucune action spécifiée.");
}


?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/task_edit.png"></img>
		<span class="task_edit">
			Modifier une tâche
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de modifier une tâche de la base des tâches.
		</p>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
		</script>
		
		<form id="select_login_form" action="loader.php?module=task_edit" method="post">
			<input type="hidden" name="load_task" value="true" />
			<p>
				<label for="task_id">Sélection tâche</label>

				<select name="task_id" id="task_id" onChange="submit()" class="chzn-select">
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

		<form id="start" action="loader.php?module=task_edit" method="post">
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
				<input type="submit" value="Modifier" /> ou <a href="loader.php?module=task_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/task_list.dock.widget.php','backend/widgets/task_add.dock.widget.php');
?>

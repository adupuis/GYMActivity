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
$header_title = 'GENYMOBILE - Edition projet';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$db_status = "";

$geny_project = new GenyProject();
$geny_ptr = new GenyProjectTaskRelation();
$geny_profile = new GenyProfile();
$geny_assignement = new GenyAssignement();

if( isset($_POST['create_project']) && $_POST['create_project'] == "true" ){
	if( isset($_POST['project_name']) && isset($_POST['project_start_date']) && isset($_POST['project_end_date']) ){
		if( $geny_project->insertNewProject($_POST['project_name'],$_POST['project_description'],$_POST['project_client'],$_POST['project_location'],$_POST['project_start_date'],$_POST['project_end_date'],$_POST['project_type'],$_POST['project_status']) > -1 ){
			$db_status .= "<li class=\"status_message_success\">Projet créé avec succès.</li>\n";
			$geny_project->loadProjectByName( $_POST['project_name'] );
			foreach ($_POST['project_tasks'] as $key => $value){
				$geny_task = new GenyTask( $value );
				if ($geny_ptr->insertNewProjectTaskRelation('NULL', $geny_project->id, $geny_task->id) ) {
					$db_status .= "<li class=\"status_message_success\">Tâche $geny_task->name ajoutée au projet.</li>\n";
				}
				else
					$db_status .= "<li class=\"status_message_error\">Erreur lors de l'ajout de la tâche $geny_task->name.</li>\n";
			}
			foreach ($_POST['project_profiles'] as $key => $value){
				$tmp_profile = new GenyProfile( $value );
				$overtime_allowed = 'false';
				if(isset($_POST['project_allow_overtime']) && $_POST['project_allow_overtime'] == 'true' )
					$overtime_allowed = 'true';
				if ($geny_assignement->insertNewAssignement('NULL', $tmp_profile->id, $geny_project->id, $overtime_allowed) ) {
					$db_status .= "<li class=\"status_message_success\">Profil $tmp_profile->login ajoutée au projet.</li>\n";
				}
				else
					$db_status .= "<li class=\"status_message_error\">Erreur lors de l'ajout du profil $tmp_profile->login.</li>\n";
			}
		}
		else{
			$db_status .= "<li class=\"status_message_error\">Erreur lors de la création du projet.</li>\n";
		}
	}
	else {
		$db_status .= "<li class=\"status_message_error\">Certains champs obligatoires sont manquant. Merci de les remplir.</li>\n";
	}
}
else if( isset($_POST['load_project']) && $_POST['load_project'] == "true" ){
	if(isset($_POST['project_id'])){
		$geny_project->loadProjectById($_POST['project_id']);
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de charger le profil utilisateur : id non spécifié.</li>\n";
	}
}
else if( isset($_GET['load_project']) && $_GET['load_project'] == "true" ){
	if(isset($_GET['project_id'])){
		$geny_project->loadProjectById($_GET['project_id']);
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de charger le profil utilisateur : id non spécifié.</li>\n";
	}
}
else if( isset($_POST['edit_project']) && $_POST['edit_project'] == "true" ){
	if(isset($_POST['project_id'])){
		$geny_project->loadProjectById($_POST['project_id']);
		if( isset($_POST['project_name']) && $_POST['project_name'] != "" && $geny_project->name != $_POST['project_name'] ){
			$geny_project->updateString('project_name',$_POST['project_name']);
		}
		if( isset($_POST['project_description']) && $_POST['project_description'] != "" && $geny_project->description != $_POST['project_description'] ){
			$geny_project->updateString('project_description',$_POST['project_description']);
		}
		if( isset($_POST['project_client']) && $_POST['project_client'] != "" && $geny_project->client_id != $_POST['project_client'] ){
			$geny_project->updateint('client_id',$_POST['project_client']);
		}
		if( isset($_POST['project_location']) && $_POST['project_location'] != "" ){
			$geny_project->updateString('project_location',$_POST['project_location']);
		}
		if( isset($_POST['project_start_date']) && $_POST['project_start_date'] != "" ){
			$geny_project->updateString('project_start_date',$_POST['project_start_date']);
		}
		if( isset($_POST['project_end_date']) && $_POST['project_end_date'] != "" ){
			$geny_project->updateString('project_end_date',$_POST['project_end_date']);
		}
		if( isset($_POST['project_type']) && $_POST['project_type'] != "" ){
			$geny_project->updateInt('project_type_id',$_POST['project_type']);
		}
		if( isset($_POST['project_status']) && $_POST['project_status'] != "" ){
			$geny_project->updateInt('project_status_id',$_POST['project_status']);
		}
		if( isset($_POST['project_tasks']) && count($_POST['project_tasks']) > 0 ){
			if($geny_ptr->deleteAllProjectTaskRelationsByProjectId( $geny_project->id )){
				$err_string = '';
				foreach ($_POST['project_tasks'] as $key => $value){
					$geny_task = new GenyTask( $value );
					if ($geny_ptr->insertNewProjectTaskRelation('NULL', $geny_project->id, $geny_task->id) ) {
						//$db_status .= "<li class=\"status_message_success\">Tâche $geny_task->name ajoutée au projet.</li>\n";
					}
					else{
						$err_string .= "<li class=\"status_message_error\">Erreur lors de l'ajout de la tâche $geny_task->name.</li>\n";
					}
				}
				if( $err_string != '' ){
					$db_status .= $err_string;
				}
				else{
					$db_status .= "<li class=\"status_message_success\">Les tâches ont été mis à jour avec succès.</li>\n";
				}
			}
		}
		if( isset($_POST['project_profiles']) && count($_POST['project_profiles']) > 0 ){
			$old_assignements = $geny_assignement->getAssignementsListByProjectId($geny_project->id);
			$old_overtime_states = array();
			foreach( $old_assignements as $tmp_ass ){
				if(isset($tmp_ass->overtime_allowed) && $tmp_ass->overtime_allowed)
					$old_overtime_states[$tmp_ass->profile_id] = 'true' ;
				else
					$old_overtime_states[$tmp_ass->profile_id] = 'false' ;
			}
			
			$assignements = $geny_assignement->getAssignementsListByProjectId( $geny_project->id );
			$assigned_profile_id = array();
			$assignements_by_profile_id = array();
			foreach( $assignements as $ass){
				$assigned_profile_id[] = $ass->profile_id;
				$assignements_by_profile_id[$ass->profile_id] = $ass;
			}
			$new_profile_id = array();
			foreach ($_POST['project_profiles'] as $key => $value){
				$new_profile_id[] = $value;
			}
			
			$notif = new GenyNotification();
			
			foreach( array_diff($assigned_profile_id,$new_profile_id) as $value ){
				$tmp_profile = new GenyProfile( $value );
				if( isset($assignements_by_profile_id[$value]) ){
					if($geny_assignement->deleteAssignement( $assignements_by_profile_id[$value]->id ) > 0){
						$db_status .= "<li class=\"status_message_success\">Profil $tmp_profile->login supprimé(e) du projet.</li>\n";
						$notif->insertNewNotification( $tmp_profile->id, "Vous avez été supprimé(e) du projet ".$geny_project->name );
					}
					else
						$db_status .= "<li class=\"status_message_error\">Erreur lors de la suppression du profil $tmp_profile->login, aucune affectation pré-existante pour ce projet.</li>\n";
				}
				else
					$db_status .= "<li class=\"status_message_error\">Erreur : lors de la suppression du profil $tmp_profile->login du projet $geny_project->name.</li>\n";
			}
			foreach( array_diff($new_profile_id,$assigned_profile_id) as $value ){
				$tmp_profile = new GenyProfile( $value );
				$tmp_overtime_allowed = 'false';
				if( isset( $old_overtime_states[$tmp_profile->id] ) )
					$tmp_overtime_allowed = $old_overtime_states[$tmp_profile->id];
				if ($geny_assignement->insertNewAssignement('NULL', $tmp_profile->id, $geny_project->id,$tmp_overtime_allowed) ) {
					$db_status .= "<li class=\"status_message_success\">Profil $tmp_profile->login ajouté(e) au projet.</li>\n";
					$notif->insertNewNotification( $tmp_profile->id, "Vous avez été ajouté(e) au projet ".$geny_project->name );
				}
				else
					$db_status .= "<li class=\"status_message_error\">Erreur lors de l'ajout du profil $tmp_profile->login.</li>\n";
			}
			
// 			WARNING: Cet ancien code est une cause de bug majeur !!!
// 			if($geny_assignement->deleteAllAssignementsByProjectId( $geny_project->id )){
// 				foreach ($_POST['project_profiles'] as $key => $value){
// 					$tmp_profile = new GenyProfile( $value );
// 					$tmp_overtime_allowed = 'false';
// 					if( isset( $old_overtime_states[$tmp_profile->id] ) )
// 						$tmp_overtime_allowed = $old_overtime_states[$tmp_profile->id];
// 					if ($geny_assignement->insertNewAssignement('NULL', $tmp_profile->id, $geny_project->id,$tmp_overtime_allowed) ) {
// 						$db_status .= "<li class=\"status_message_success\">Profil $tmp_profile->login ajoutée au projet.</li>\n";
// 					}
// 					else
// 						$db_status .= "<li class=\"status_message_error\">Erreur lors de l'ajout du profil $tmp_profile->login.</li>\n";
// 				}
// 			}
		}
		if($geny_project->commitUpdates()){
			$db_status .= "<li class=\"status_message_success\">Projet mis à jour avec succès.</li>\n";
			$geny_project->loadProjectById($_POST['project_id']);
		}
		else {
			$db_status .= "<li class=\"status_message_error\">Erreur durant la mise à jour du projet.</li>\n";
		}
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de modifier le projet : id non spécifié.</li>\n";
	}
}

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/project_generic.png"/><p>Projet</p>
</div>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="project_edit">
			Modifier un projet
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'éditer un projet existant. Tous les champs doivent être remplis.
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			$(document).ready(function(){
				$(".taskslistselect").listselect({listTitle: "Tâches disponibles",selectedTitle: "Tâches séléctionnées"});
			});
			$(document).ready(function(){
				$(".profileslistselect").listselect({listTitle: "Profiles disponibles",selectedTitle: "Profiles séléctionnées"});
			});
			$(function() {
				var availableTags = [
					<?php
						$tags = '';
						$project = new GenyProject();
						foreach( $project->getLocationsList() as $loc ){
							$tags .= '"'.$loc.'",';
						}
						echo rtrim($tags, ",");
					?>
				];
				$( "#project_location" ).autocomplete({
					source: availableTags
				});
			});
		</script>
		<?php
			if( isset($db_status) && $db_status != "" ){
				echo "<ul class=\"status_message\">\n$db_status\n</ul>";
			}
		?>
		<form id="select_project_form" action="project_edit.php" method="post">
			<input type="hidden" name="load_project" value="true" />
			<p>
				<label for="project_id">Séléction projet</label>

				<select name="project_id" id="project_id" onChange="submit()">
					<?php
						$projects = $geny_project->getAllProjects();
						foreach( $projects as $project ){
							if( (isset($_POST['project_id']) && $_POST['project_id'] == $project->id) || (isset($_GET['project_id']) && $_GET['project_id'] == $project->id) )
								echo "<option value=\"".$project->id."\" selected>".$project->name."</option>\n";
							else if( isset($_POST['project_name']) && $_POST['project_name'] == $project->name)
								echo "<option value=\"".$project->id."\" selected>".$project->name."</option>\n";
							else
								echo "<option value=\"".$project->id."\">".$project->name."</option>\n";
						}
						if( $geny_project->id < 0 )
							$geny_project->loadProjectById( $projects[0]->id );
					?>
				</select>
			</p>
		</form>
		<form id="formID" action="project_edit.php" method="post">
			<input type="hidden" name="edit_project" value="true" />
			<input type="hidden" name="project_id" value="<?php echo $geny_project->id ?>" />
			 <p>
				<label for="project_name">Nom du projet</label>
				<input name="project_name" id="project_name" type="text" value="<?php echo $geny_project->name ?>" class="validate[required,length[2,100]] text-input" />
			</p> 
			<p>
				<label for="project_client">Client</label>
				<select name="project_client" id="project_client">
				<?php
					$geny_client = new GenyClient();
					foreach( $geny_client->getAllClients() as $client ){
						if( $geny_project->client_id == $client->id )
							echo "<option value=\"".$client->id."\" selected>".$client->name."</option>\n";
						else
							echo "<option value=\"".$client->id."\">".$client->name."</option>\n";
					}
				?>
				</select>
			</p>
			<p>
				<label for="project_type">Type</label>
				<select name="project_type" id="project_type">
				<?php
					$geny_pt = new GenyProjectType();
					foreach ($geny_pt->getAllProjectTypes() as $pt){
						if( $geny_project->type_id == $pt->id )
							echo "<option value=\"".$pt->id."\" title=\"".$pt->description."\" selected>".$pt->name."</option>\n";
						else
							echo "<option value=\"".$pt->id."\" title=\"".$pt->description."\">".$pt->name."</option>\n";
					}
				?>
				</select>
			</p>
			<p>
				<label for="project_status">Status</label>
				<select name="project_status" id="project_status">
				<?php
					$geny_ps = new GenyProjectStatus();
					foreach ($geny_ps->getAllProjectStatus() as $ps){
						if( $geny_project->status_id == $ps->id )
							echo "<option value=\"".$ps->id."\" title=\"".$ps->description."\" selected>".$ps->name."</option>\n";
						else
							echo "<option value=\"".$ps->id."\" title=\"".$ps->description."\">".$ps->name."</option>\n";
					}
				?>
				</select>
			</p>
			<p>
				<label for="project_location">Localisation</label>
				<input name="project_location" id="project_location" type="text" value="<?php echo $geny_project->location ?>" class="validate[required,length[2,100]] text-input" />
			</p>
			<script type="text/javascript">
				$(function() {
					$( "#project_start_date" ).datepicker();
					$( "#project_start_date" ).datepicker('setDate', new Date());
					$( "#project_start_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#project_start_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#project_start_date" ).datepicker( "option", "defaultDate", "<?php echo $geny_project->start_date ?>" );
					$( "#project_start_date" ).datepicker( "setDate", "<?php echo $geny_project->start_date ?>" );
					$( "#project_start_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#project_start_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#project_start_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#project_start_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#project_start_date" ).datepicker( "option", "firstDay", 1 );
					
					$( "#project_end_date" ).datepicker();
					$( "#project_end_date" ).datepicker('setDate', new Date());
					$( "#project_end_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#project_end_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#project_end_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#project_end_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#project_end_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#project_end_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#project_end_date" ).datepicker( "option", "firstDay", 1 );
					$( "#project_end_date" ).datepicker( "option", "defaultDate", "<?php echo $geny_project->end_date ?>" );
					$( "#project_end_date" ).datepicker( "setDate", "<?php echo $geny_project->end_date ?>" );
					
				});
			</script>
			<p>
				<label for="project_start_date">Date de début</label>
				<input name="project_start_date" id="project_start_date" type="text" value="<?php echo $geny_project->start_date ?>" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="project_end_date">Date de fin</label>
				<input name="project_end_date" id="project_end_date" type="text" value="<?php echo $geny_project->end_date ?>" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="project_description">Description</label>
				<textarea name="project_description" id="project_description" class="validate[required] text-input"><?php echo $geny_project->description ?></textarea>
			</p>
			<p>
				<label for="tasks_checkboxgroup">Tâches</label>
				<?php
					$ptrs = $geny_ptr->getProjectTaskRelationsListByProjectId( $geny_project->id );
					$selected_tasks = '';
					foreach( $ptrs as $ptr ){
						$selected_tasks .= "$ptr->task_id,";
					}
					$selected_tasks = rtrim($selected_tasks, ",");
				?>
				<select class="taskslistselect" name="project_tasks[]" selected="<?php echo $selected_tasks ?>">
				<?php
					$geny_task = new GenyTask();
					foreach( $geny_task->getAllTasks() as $t ){
						echo "<option value=\"$t->id\" title=\"$t->description\">$t->name</input></option>";
					}
				?>
				</select>
			</p>
			<p>
				<label for="profiles_checkboxgroup">Attributions</label>
				<?php
					$assignements = $geny_assignement->getAssignementsListByProjectId( $geny_project->id );
					$selected_profiles = '';
					foreach( $assignements as $assignement ){
						$selected_profiles .= "$assignement->profile_id,";
					}
					$selected_profiles = rtrim($selected_profiles, ",");
				?>
				<select class="profileslistselect" name="project_profiles[]" selected="<?php echo $selected_profiles ?>">
				<?php
					foreach( $geny_profile->getAllProfiles() as $p ){
						echo "<option value=\"$p->id\" title=\"$p->firstname $p->lastname\">$p->login</input></option>";
					}
				?>
				</select>
			</p>
			<p>
				<input type="submit" value="Modifier" /> ou <a href="#formID">annuler</a>
			</p>
		</form>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php include 'backend/widgets/project_list.dock.widget.php'; ?>
		<?php include 'backend/widgets/project_add.dock.widget.php'; ?>
	</ul>
</div>
<?php
include_once 'footer.php';
?>

<?php
//  Copyright (C) 2011 by GENYMOBILE & Quentin Désert
//  qdesert@genymobile.com
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


$gritter_notifications = array();

$geny_daily_rate = new GenyDailyRate();
$geny_project = new GenyProject();
$geny_task = new GenyTask();
$geny_profile = new GenyProfile();

$create_daily_rate = GenyTools::getParam( 'create_daily_rate', 'NULL' );
$load_daily_rate = GenyTools::getParam( 'load_daily_rate', 'NULL' );
$edit_daily_rate = GenyTools::getParam( 'edit_daily_rate', 'NULL' );

if( $create_daily_rate == "true" ) {
	$project_id = GenyTools::getParam( 'project_id', 'NULL' );
	$task_id = GenyTools::getParam( 'task_id', 'NULL' );
	$profile_id = GenyTools::getParam( 'profile_id', 'NULL' );
	$daily_rate_start_date = GenyTools::getParam( 'daily_rate_start_date', 'NULL' );
	$daily_rate_end_date = GenyTools::getParam( 'daily_rate_end_date', 'NULL' );
	$daily_rate_value = GenyTools::getParam( 'daily_rate_value', 'NULL' );

	if( $project_id != 'NULL' && $task_id != 'NULL' && $daily_rate_start_date != 'NULL' && $daily_rate_end_date != 'NULL' && $daily_rate_value != 'NULL' ) {
		$insert_id = $geny_daily_rate->insertNewDailyRate( 'NULL', $project_id, $task_id, $profile_id, $daily_rate_start_date, $daily_rate_end_date, $daily_rate_value );
		if( $insert_id != -1 ) {
			$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"TJM ajouté avec succès." );
			$geny_daily_rate->loadDailyRateById( $insert_id );
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout du TJM." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir." );
	}
}
else if( $load_daily_rate == 'true' ) {
	$daily_rate_id = GenyTools::getParam( 'daily_rate_id', 'NULL' );
	if( $daily_rate_id != 'NULL' ) {
		$tmp_geny_daily_rate = new GenyDailyRate();
		$tmp_geny_daily_rate->loadDailyRateById( $daily_rate_id );
		// TODO: restrict admin or not ?
		if( $profile->rights_group_id == 1  || /* admin */
		    $profile->rights_group_id == 2  || /* superuser */
		    $profile->rights_group_id == 4     /* superreporter */ ) {
			$geny_daily_rate->loadDailyRateById( $daily_rate_id );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger le TJM",'msg'=>"Vous n'êtes pas autorisé.");
			header( 'Location: error.php?category=daily_rate&backlinks=daily_rate_list,daily_rate_add' );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Impossible de charger le TJM','msg'=>"id non spécifié." );
	}
}
else if( $edit_daily_rate == "true" ) {
	$daily_rate_id = GenyTools::getParam( 'daily_rate_id', 'NULL' );
	if( $daily_rate_id != 'NULL' ) {
		$geny_daily_rate->loadDailyRateById( $daily_rate_id );
		
		if( $profile->rights_group_id == 1  || /* admin */
		    $profile->rights_group_id == 2  || /* superuser */
		    $profile->rights_group_id == 4     /* superreporter */ ) {

			$project_id = GenyTools::getParam( 'project_id', 'NULL' );
			$task_id = GenyTools::getParam( 'task_id', 'NULL' );
			$profile_id = GenyTools::getParam( 'profile_id', 'NULL' );
			$daily_rate_start_date = GenyTools::getParam( 'daily_rate_start_date', 'NULL' );
			$daily_rate_end_date = GenyTools::getParam( 'daily_rate_end_date', 'NULL' );
			$daily_rate_value = GenyTools::getParam( 'daily_rate_value', 'NULL' );

			if( $project_id != 'NULL' && $geny_daily_rate->project_id != $project_id ) {
				$geny_daily_rate->updateInt( 'project_id', $project_id );
			}
			if( $task_id != 'NULL' && $geny_daily_rate->task_id != $task_id ) {
				$geny_daily_rate->updateInt( 'task_id', $task_id );
			}
			if( $profile_id && $geny_daily_rate->profile_id != $profile_id ) {
				$geny_daily_rate->updateInt( 'profile_id', $profile_id );
			}
			if( $daily_rate_start_date != 'NULL' && $geny_daily_rate->start_date != $daily_rate_start_date ) {
				$geny_daily_rate->updateString( 'daily_rate_start_date', $daily_rate_start_date );
			}
			if( $daily_rate_end_date != 'NULL' && $geny_daily_rate->end_date != $daily_rate_end_date ) {
				$geny_daily_rate->updateString( 'daily_rate_end_date', $daily_rate_end_date );
			}
			if( $daily_rate_value != 'NULL' && $geny_daily_rate->value != $daily_rate_value ) {
				$geny_daily_rate->updateString( 'daily_rate_value', $daily_rate_value );
			}
		}
		if( $geny_daily_rate->commitUpdates() ) {
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"TJM mis à jour avec succès.");
			$geny_daily_rate->loadDailyRateById( $daily_rate_id );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour du TJM.");
		}

	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de modifier le TJM ','msg'=>"id non spécifié.");
	}
}

?>

<style>
	@import "styles/genymobile-2012/chosen_override.css";
</style>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="daily_rate_edit">
			Modifier un TJM
		</span>
	</p>
	<p class="mainarea_content">

		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
		</script>

		<p class="mainarea_content_intro">
		Ce formulaire permet d'éditer un TJM existant. Tous les champs doivent être remplis.
		</p>
		
		<form id="select_daily_rate_form" action="loader.php?module=daily_rate_edit" method="post">
			<input type="hidden" name="load_daily_rate" value="true" />
			<p>
				<label for="daily_rate_id">Sélection TJM</label>

				<select name="daily_rate_id" id="daily_rate_id" class="chzn-select" onChange="submit()">
					<?php
						$daily_rates = $geny_daily_rate->getAllDailyRates();

						$concat_array = array();
						$i = 0;
						foreach( $daily_rates as $daily_rate ) {


							foreach( $geny_project->getAllProjects() as $proj ) {
								if( $daily_rate->project_id == $proj->id ) {
									$project = $proj->name;
								}
							}

							foreach( $geny_task->getAllTasks() as $tsk ) {
								if( $daily_rate->task_id == $tsk->id ) {
									$task = $tsk->name;
								}
							}

							$prof_scr_name = '';
							foreach( $geny_profile->getAllProfiles() as $prof ) {
								if( $daily_rate->profile_id == $prof->id ) {
									if( $prof->firstname && $prof->lastname ) {
										$prof_scr_name = $prof->firstname.' '.$prof->lastname;
									}
									else {
										$prof_scr_name = $prof->login;
									}
									break;
								}
							}

							if( $geny_daily_rate->id == $daily_rate->id ) {
								$concat1 = "<option value=\"".$daily_rate->id."\" selected>";
							}
							else {
								$concat1 = "<option value=\"".$daily_rate->id."\">";
							}
							if( $prof_scr_name != '' ) {
								$concat2 = $project.' - '.$task.' - '.$prof_scr_name.' - du '.$daily_rate->start_date.' au '.$daily_rate->end_date."</option>\n";
							}
							else {
								$concat2 = $project.' - '.$task.' - du '.$daily_rate->start_date.' au '.$daily_rate->end_date."</option>\n";
							}
							$concat_array2 = array();
							$concat_array2["first"] = $concat1;
							$concat_array2["second"] = $concat2;
							$concat_array[$i] = $concat_array2;
							$i++;
						}
						$concat_array = GenyTools::sortMultiArrayCaseInsensitive( $concat_array, "second" );

						foreach( $concat_array as $concat ) {
							echo $concat["first"].$concat["second"];
						}

						if( $geny_daily_rate->id < 0 ) {
							$geny_daily_rate->loadDailyRateById( $daily_rates[0]->id );
						}
					?>
				</select>
			</p>
		</form>
		<form id="formID" action="loader.php?module=daily_rate_edit" method="post">
			<input type="hidden" name="edit_daily_rate" value="true" />
			<input type="hidden" name="daily_rate_id" value="<?php echo $geny_daily_rate->id ?>" />
			

			<p>
				<label for="project_id">Projet</label>
				<select name="project_id" id="project_id" class="chzn-select" data-placeholder="Choisissez un projet...">
					<option value=""></option>
					<?php
						foreach( $geny_project->getAllProjects() as $project ) {
							if( $geny_daily_rate->project_id == $project->id ) {
								echo "<option value=\"".$project->id."\" selected>".$project->name."</option>\n";
							}
							else {
								echo "<option value=\"".$project->id."\">".$project->name."</option>\n";
							}
						}
					?>
				</select>
			</p>

			<p>
				<label for="task_id">Tâche</label>
				<select name="task_id" id="task_id" class="chzn-select" data-placeholder="Choisissez d'abord un projet...">
					<option value=""></option>
				</select>
			</p>

			<p>
				<label for="profile_id">Profil</label>
				<select name="profile_id" id="profile_id" class="chzn-select" data-placeholder="Choisissez aussi un projet...">
				</select>
			</p>

			<script type="text/javascript">
				
				// WARNING: the default value of the task list and project should not be updated when we modify the project
				// TODO: we could have a global variable telling if the 'selected' tag must be inserted.

				function getTasks() {
					var project_id = $("#project_id").val();
					var geny_daily_rate_task_id = <?php echo $geny_daily_rate->task_id ?>;
					$.get('backend/api/get_project_tasks_list.php?project_id='+project_id, function( data ) {
						$('.tasks_options').remove();
						$.each( data, function( key, val ) {
							if( val[0] == geny_daily_rate_task_id ) {
								$("#task_id").append('<option class="tasks_options" value="' + val[0] + '" title="' + val[2] + '" selected>' + val[1] + '</option>');
							}
							else {
								$("#task_id").append('<option class="tasks_options" value="' + val[0] + '" title="' + val[2] + '">' + val[1] + '</option>');
							}
						});
						$("#task_id").attr('data-placeholder','Choisissez une tâche...');
						$("#task_id").trigger("liszt:updated");
						$("span:contains('Choisissez d'abord un projet...')").text('Choisissez une tâche...');
					},'json');
				}
				$("#project_id").change( getTasks );
				getTasks();

				function getProfiles(){
					var project_id = $("#project_id").val();
					var geny_daily_rate_profile_id = <?php echo ( $geny_daily_rate->profile_id ) ? $geny_daily_rate->profile_id : -1 ?>;
					// TODO: pas bon !!! car la liste n'est pas rechargée en ajax !
					if( geny_daily_rate_profile_id != -1 ) {
						$.get('backend/api/get_project_profiles_list.php?project_id='+project_id, function( data ) {
							$('.profiles_options').remove();
							$( "#profile_id" ).append( '<option class="profiles_options" value="NULL">- Pas de profil associé -</option>' );
							$.each( data, function( key, val ) {
								if( val.id == geny_daily_rate_profile_id ) {
									if( val.firstname && val.lastname ) {
										$( "#profile_id" ).append( '<option class="profiles_options" value="' + val.id + '" title="' + val.id + '"selected>' + val.firstname +' '+ val.lastname + '</option>' );
									}
									else {
										$( "#profile_id" ).append( '<option class="profiles_options" value="' + val.id + '" title="' + val.id + '"selected>' + val.login + '</option>' );
									}
								}
								else {
									if( val.firstname && val.lastname ) {
										$( "#profile_id" ).append( '<option class="profiles_options" value="' + val.id + '" title="' + val.id + '">' + val.firstname +' '+ val.lastname + '</option>' );
									}
									else {
										$( "#profile_id" ).append( '<option class="profiles_options" value="' + val.id + '" title="' + val.id + '">' + val.login + '</option>' );
									}
								}
							});
							$("#profile_id").attr('data-placeholder','Choisissez un profil...');
							$("#profile_id").trigger("liszt:updated");
							$("span:contains('Choisissez d'abord un projet...')").text('Choisissez un profil...');
						},'json');
					}
					else {
						$('.profiles_options').remove();
						$( "#profile_id" ).append( '<option class="profiles_options" value="NULL">- Pas de profil associé -</option>' );
					}
				}
				$("#project_id").change( getProfiles );
				getProfiles();

				$(function() {

					$( "#daily_rate_start_date" ).datepicker();
					$( "#daily_rate_start_date" ).datepicker('setDate', new Date());
					$( "#daily_rate_start_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#daily_rate_start_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#daily_rate_start_date" ).datepicker( "option", "defaultDate", "<?php echo $geny_daily_rate->start_date ?>" );
					$( "#daily_rate_start_date" ).datepicker( "setDate", "<?php echo $geny_daily_rate->start_date ?>" );
					$( "#daily_rate_start_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#daily_rate_start_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#daily_rate_start_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#daily_rate_start_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#daily_rate_start_date" ).datepicker( "option", "firstDay", 1 );
					
					$( "#daily_rate_end_date" ).datepicker();
					$( "#daily_rate_end_date" ).datepicker('setDate', new Date());
					$( "#daily_rate_end_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#daily_rate_end_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#daily_rate_end_date" ).datepicker( "option", "defaultDate", "<?php echo $geny_daily_rate->end_date ?>" );
					$( "#daily_rate_end_date" ).datepicker( "setDate", "<?php echo $geny_daily_rate->end_date ?>" );
					$( "#daily_rate_end_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#daily_rate_end_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#daily_rate_end_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#daily_rate_end_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#daily_rate_end_date" ).datepicker( "option", "firstDay", 1 );
				});
			</script>
			<p>
				<label for="daily_rate_start_date">Début de période</label>
				<input name="daily_rate_start_date" id="daily_rate_start_date" type="text" value="<?php echo $geny_daily_rate->start_date ?>" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="daily_rate_end_date">Fin de période</label>
				<input name="daily_rate_end_date" id="daily_rate_end_date" type="text" value="<?php echo $geny_daily_rate->end_date ?>" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="daily_rate_value">Valeur</label>
				<input name="daily_rate_value" id="daily_rate_value" type="text" value="<?php echo $geny_daily_rate->value ?>" class="validate[required,length[2,100]] text-input" />
			</p>

			<p>
				<input type="submit" value="Modifier" /> ou <a href="loader.php?module=daily_rate_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/daily_rate_list.dock.widget.php','backend/widgets/daily_rate_add.dock.widget.php');
?>

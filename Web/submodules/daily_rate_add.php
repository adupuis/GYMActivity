<?php
//  Copyright (C) 2011 by GENYMOBILE

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


$geny_project = new GenyProject();
$geny_profile = new GenyProfile();

?>
<div id="mainarea">
	<p class="mainarea_title">
		<span class="daily_rate_add">
			Ajouter un TJM
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter un TJM. Tous les champs doivent être remplis.
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			$(document).ready(function(){
				$(".profileslistselect").listselect({listTitle: "Profils disponibles",selectedTitle: "Profils sélectionnés"});
			});
		</script>
		<form id="formID" action="loader.php?module=daily_rate_edit" method="post">
			<input type="hidden" name="create_daily_rate" value="true" />
			<p>
				<label for="project_id">Projet</label>
				<select name="project_id" id="project_id" class="chzn-select" data-placeholder="Choisissez un projet...">
					<option value=""></option>
					<?php
						foreach( $geny_project->getAllProjects() as $project ) {
							echo "<option value=\"".$project->id."\">".$project->name."</option>\n";
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
				<select name="profile_id" id="profile_id" class="chzn-select" data-placeholder="Choisissez d'abord un projet...">
					<option value=""></option>
				</select>
			</p>

			<script type="text/javascript">

				function getTasks(){
					var project_id = $("#project_id").val();
					if( project_id > 0 ) {
						$.get('backend/api/get_project_tasks_list.php?project_id='+project_id, function( data ) {
							$('.tasks_options').remove();
							$.each( data, function( key, val ) {
								$("#task_id").append('<option class="tasks_options" value="' + val[0] + '" title="' + val[2] + '">' + val[1] + '</option>');
							});
							$("#task_id").attr('data-placeholder','Choisissez une tâche...');
							$("#task_id").trigger("liszt:updated");
							$("span:contains('Choisissez d'abord un projet...')").text('Choisissez une tâche...');

						},'json');
					}
				}
				$("#project_id").change( getTasks );
				getTasks();

				function getProfiles(){
					var project_id = $("#project_id").val();
					if( project_id > 0 ) {
						$.get('backend/api/get_project_profiles_list.php?project_id='+project_id, function( data ) {
							$('.profiles_options').remove();
							$( "#profile_id" ).append( '<option class="profiles_options" value="NULL">- Pas de profil associé -</option>' );
							$.each( data, function( key, val ) {
								if( val.firstname && val.lastname ) {
									$( "#profile_id" ).append( '<option class="profiles_options" value="' + val.id + '" title="' + val.id + '">' + val.firstname +' '+ val.lastname + '</option>' );
								}
								else {
									$( "#profile_id" ).append( '<option class="profiles_options" value="' + val.id + '" title="' + val.id + '">' + val.login + '</option>' );
								}
							});
							$("#profile_id").attr('data-placeholder','Choisissez un profil...');
							$("#profile_id").trigger("liszt:updated");
							$("span:contains('Choisissez d'abord un projet...')").text('Choisissez un profil...');

						},'json');
					}
				}
				$("#project_id").change( getProfiles );
				getProfiles();

				$(function() {
					$( "#daily_rate_start_date" ).datepicker();
					$( "#daily_rate_start_date" ).datepicker('setDate', new Date());
					$( "#daily_rate_start_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#daily_rate_start_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#daily_rate_start_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#daily_rate_start_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#daily_rate_start_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#daily_rate_start_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#CREATE TABLE DailyRatesdaily_rate_start_date" ).datepicker( "option", "firstDay", 1 );
					
					$( "#daily_rate_end_date" ).datepicker();
					$( "#daily_rate_end_date" ).datepicker('setDate', new Date());
					$( "#daily_rate_end_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#daily_rate_end_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#daily_rate_end_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#daily_rate_end_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#daily_rate_end_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#daily_rate_end_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#daily_rate_end_date" ).datepicker( "option", "firstDay", 1 );
				});

				$(function() {
					var availableTags = [
						<?php
							$tags = '';
							$daily_rate = new GenyDailyRate();
							foreach( $daily_rate->getValuesList() as $value ) {
								$tags .= '"'.$value.'",';
							}
							echo rtrim( $tags, "," );
						?>
					];
					$( "#daily_rate_value" ).autocomplete({
						source: availableTags
					});
				});

			</script>

			<p>
				<label for="daily_rate_start_date">Début de période</label>
				<input name="daily_rate_start_date" id="daily_rate_start_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="daily_rate_end_date">Fin de période</label>
				<input name="daily_rate_end_date" id="daily_rate_end_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="daily_rate_value">Valeur</label>
				<input name="daily_rate_value" id="daily_rate_value" type="text" class="validate[required,length[2,100]] text-input" />
			</p>

				<input type="submit" value="Ajouter" /> ou <a href="loader.php?module=daily_rate_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/daily_rate_list.dock.widget.php');
?>

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
$header_title = '%COMPANY_NAME% - Ajout Coût journalier';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$geny_project = new GenyProject();
$geny_profile = new GenyProfile();

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/daily_rate_generic.png"/><p>Coût journalier</p>
</div>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="daily_rate_add">
			Ajouter un coût journalier
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter un coût journalier. Tous les champs doivent être remplis.
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
		<form id="formID" action="daily_rate_edit.php" method="post">
			<input type="hidden" name="create_daily_rate_summary" value="true" />
			<p>
				<label for="project_id">Projet</label>
				<select name="project_id" id="project_id">
				<?php
					foreach( $geny_project->getAllProjects() as $project ) {
						echo "<option value=\"".$project->id."\">".$project->name."</option>\n";
					}
				?>
				</select>
			</p>
			<p>
				<label for="task_id">Tâche</label>
				<select name="task_id" id="task_id">
				</select>
			</p>
			<p>
				<label for="profile_id">Profil</label>
				<select name="profile_id" id="profile_id">
				</select>
			</p>

			<script type="text/javascript">

				function getTasks(){
					var project_id = $("#project_id").val();
					$.get('backend/api/get_project_tasks_list.php?project_id='+project_id, function( data ) {
						$('.tasks_options').remove();
						$.each( data, function( key, val ) {
							$("#task_id").append('<option class="tasks_options" value="' + val[0] + '" title="' + val[2] + '">' + val[1] + '</option>');
						});

					},'json');
				}
				$("#project_id").change( getTasks );
				getTasks();

				function getProfiles(){
					var project_id = $("#project_id").val();
					$.get('backend/api/get_project_profiles_list.php?project_id='+project_id, function( data ) {
						$('.profiles_options').remove();
						$.each( data, function( key, val ) {
							if( val.firstname && val.lastname ) {
								$( "#profile_id" ).append( '<option class="profiles_options" value="' + val.id + '" title="' + val.id + '">' + val.firstname +' '+ val.lastname + '</option>' );
							}
							else {
								$( "#profile_id" ).append( '<option class="profiles_options" value="' + val.id + '" title="' + val.id + '">' + val.login + '</option>' );
							}
						});

					},'json');
				}
				$("#project_id").change( getProfiles );
				getProfiles();

				$(function() {
					$( "#daily_rate_period_start" ).datepicker();
					$( "#daily_rate_period_start" ).datepicker('setDate', new Date());
					$( "#daily_rate_period_start" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#daily_rate_period_start" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#daily_rate_period_start" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#daily_rate_period_start" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#daily_rate_period_start" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#daily_rate_period_start" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#daily_rate_period_start" ).datepicker( "option", "firstDay", 1 );
					
					$( "#daily_rate_period_end" ).datepicker();
					$( "#daily_rate_period_end" ).datepicker('setDate', new Date());
					$( "#daily_rate_period_end" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#daily_rate_period_end" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#daily_rate_period_end" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#daily_rate_period_end" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#daily_rate_period_end" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#daily_rate_period_end" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#daily_rate_period_end" ).datepicker( "option", "firstDay", 1 );
				});
			</script>
			<p>
				<label for="daily_rate_period_start">Début de période</label>
				<input name="daily_rate_period_start" id="daily_rate_period_start" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="daily_rate_period_end">Fin de période</label>
				<input name="daily_rate_period_end" id="daily_rate_period_end" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="value">Valeur</label>
				<input name="value_id" id="value_id"/>
			</p>

				<input type="submit" value="Ajouter" /> ou <a href="daily_rate_list.php">annuler</a>
			</p>
		</form>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php 
 			include 'backend/widgets/daily_rate_list.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

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


$geny_client = new GenyClient();
$geny_profile = new GenyProfile();

?>
<div id="mainarea">
	<p class="mainarea_title">
		<span class="project_add">
			Ajouter un projet
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter un projet dans la base des projets. Tous les champs doivent être remplis.
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
		<form id="formID" action="loader.php?module=project_edit" method="post">
			<input type="hidden" name="create_project" value="true" />
			<p>
				<label for="project_name">Nom du projet</label>
				<input name="project_name" id="project_name" type="text" class="validate[required,length[2,100]] text-input" />
			</p>
			<p>
				<label for="project_client">Client</label>
				<select name="project_client" id="project_client">
				<?php
					foreach( $geny_client->getAllClients() as $client ){
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
						echo "<option value=\"".$ps->id."\" title=\"".$ps->description."\">".$ps->name."</option>\n";
					}
				?>
				</select>
			</p>
			<p>
				<label for="project_location">Localisation</label>
				<input name="project_location" id="project_location" type="text" class="validate[required,length[2,100]] text-input" />
			</p>
			<script type="text/javascript">
				$(function() {
					$( "#project_start_date" ).datepicker();
					$( "#project_start_date" ).datepicker('setDate', new Date());
					$( "#project_start_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#project_start_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
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
				});
			</script>
			<p>
				<label for="project_start_date">Date de début</label>
				<input name="project_start_date" id="project_start_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="project_end_date">Date de fin</label>
				<input name="project_end_date" id="project_end_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="project_description">Description</label>
				<textarea name="project_description" id="project_description" class="validate[required] text-input"></textarea>
			</p>
			<p>
				<label for="tasks_checkboxgroup">Tâches</label>
				<select class="taskslistselect" name="project_tasks[]">
				<?php
					$geny_task = new GenyTask();
					foreach( $geny_task->getAllTasks() as $t ){
						echo "<option value=\"$t->id\" >$t->name</input></option>";
					}
				?>
				</select>
			</p>
			<p>
				<label for="profiles_checkboxgroup">Attributions</label>
				<select class="profileslistselect" name="project_profiles[]">
				<?php
					foreach( $geny_profile->getAllProfiles() as $p ){
						echo "<option value=\"$p->id\" title=\"$p->firstname $p->lastname\">$p->login</input></option>";
					}
				?>
				</select>
			</p>
			<p>
				<input type="checkbox" name="project_allow_overtime" value="true" /> Autoriser les heures supplémentaires pour tout les collaborateurs. Cette opération autorisera tous les collaborateurs ajoutés au projet à ce moment. C'est un mode de groupe afin de faciliter une opération de masse, pour autoriser les heures supplémentaires par collaborateur rendez vous sur la page de <a href="/assignement_list.php">gestion des affectactions</a>. 
			</p>
			<p>
				<input type="submit" value="Créer" /> ou <a href="loader.php?module=project_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/project_list.dock.widget.php');
?>

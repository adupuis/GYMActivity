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


$geny_ptr = new GenyProjectTaskRelation();
$geny_tools = new GenyTools();
$geny_client = new GenyClient();
date_default_timezone_set('Europe/Paris');
$clients = array();

foreach( $geny_client->getAllClients() as $client ){
	$clients[$client->id] = $client;
}

?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/cra_add.png"></img>
		<span class="cra_add">
			Ajouter un CRA
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter un rapport d'activité.<br />
		<strong class="important_note">Important :</strong> La charge journalière est <u>une charge répartie par jour</u>. <br />
		C'est à dire que si vous remplissez un CRA pour une semaine (5 jours), vous positionnez les dates de début et de fin avec une charge moyenne de 8 heures.<br />
		Une charge supérieure signifie des heures supplémentaires et l'accord préalable du manager est nécessaire.
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
		</script>

		<form id="formID" action="loader.php?module=cra_validation" method="post">
			<input type="hidden" name="create_cra" value="true" />
			<p>
				<label for="assignement_id">Projet</label>
				<select name="assignement_id" id="assignement_id" class="chzn-select" data-placeholder="Choisissez un projet..." >
					<option value=""></option>
					<?php
						$geny_assignements = new GenyAssignement();
						$pns = array();
						foreach( $geny_assignements->getActiveAssignementsListByProfileId( $profile->id ) as $assignement ){
							$p = new GenyProject( $assignement->project_id );
							if( $p->type_id != 5 && $p->status_id != 2 && $p->status_id != 3 && $p->status_id != 8 ){
								// WARNING: Il n'y a pas de protection contre les doublons
								$key = $clients[$p->client_id]->name." - $p->name";
								$pns["$key"] = array("id" => $assignement->id, "description" => $p->description );
							}
						}
						$keysu = array_keys($pns);
						sort($keysu);
						foreach( $keysu as $pn_key ){
							echo "<option value=\"".$pns[$pn_key]['id']."\" title=\"".$pns[$pn_key]['description']."\">$pn_key</input></option>";
						}
					?>
				</select>
			</p>
			<p>
				<label for="task_id">Tâche</label>
				<select name="task_id" id="task_id" class="chzn-select" data-placeholder="Choisissez d'abord un projet..." >
					<option value=""></option>
				</select>
				<script>
					function getTasks(){
						var project_id = $("#assignement_id").val();
						if( project_id > 0 ) {
							$.get('backend/api/get_project_tasks_list.php?assignement_id='+project_id, function(data){
								$('.tasks_options').remove();
								$("#task_id").append('<option value="" class="tasks_options"></option>');
								$.each(data, function(key, val) {
									$("#task_id").append('<option class="tasks_options" value="' + val[0] + '" title="' + val[2] + '">' + val[1] + '</option>');
								});
								$("#task_id").attr('data-placeholder','Choisissez une tâche...');
								$("#task_id").trigger("liszt:updated");
								$("span:contains('Choisissez d'abord un projet...')").text('Choisissez une tâche...');
								

							},'json');
						}
					}
					$("#assignement_id").change(getTasks);
					getTasks();
					$(function() {
						$( "#assignement_start_date" ).datepicker();
						$( "#assignement_start_date" ).datepicker('setDate', new Date());
						$( "#assignement_start_date" ).datepicker( "option", "showAnim", "slideDown" );
						$( "#assignement_start_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
						$( "#assignement_start_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
						$( "#assignement_start_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
						$( "#assignement_start_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
						$( "#assignement_start_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
						$( "#assignement_start_date" ).datepicker( "option", "firstDay", 1 );
						$( "#assignement_end_date" ).datepicker();
						$( "#assignement_end_date" ).datepicker('setDate', new Date());
						$( "#assignement_end_date" ).datepicker( "option", "showAnim", "slideDown" );
						$( "#assignement_end_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
						$( "#assignement_end_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
						$( "#assignement_end_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
						$( "#assignement_end_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
						$( "#assignement_end_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
						$( "#assignement_end_date" ).datepicker( "option", "firstDay", 1 );
						
						$( "#assignement_start_date" ).change( function(){ $( "#assignement_end_date" ).val( $( "#assignement_start_date" ).val() ) } );
						
					});
				</script>
			</p>
			<p>
				<label for="date_selection_type">Type de séléction</label>
				<select name="date_selection_type" id="date_selection_type" class="chzn-select">
					<option value="interval">Intervalle de dates</option>
					<option value="days_list">Liste de jours</option>
				</select>
			</p>
			<p id="interval_start_date">
				<label for="assignement_start_date">Date de début</label>
				<input name="assignement_start_date" id="assignement_start_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p id="interval_stop_date">
				<label for="assignement_end_date">Date de fin</label>
				<input name="assignement_end_date" id="assignement_end_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p id="days_list_date">
				<label for="assignement_date_list">Liste des dates</label>
				<style type="text/css">
					@import "styles/default/jquery.datepick.css";
					@import "styles/default/smoothness.datepick.css";
				</style>
				<input name="assignement_date_list" id="assignement_date_list" type="text" class="validate[required text-input" />
			</p>
			<script>
				function showCalendar(){
					if( $("#date_selection_type").val() == "interval" ){
						$("#interval_start_date").show();
						$("#interval_stop_date").show();
						$("#days_list_date").hide();
					}
					else {
						$("#interval_start_date").hide();
						$("#interval_stop_date").hide();
						$("#days_list_date").show();
					}
				}
				$("#date_selection_type").change(showCalendar);
				showCalendar();
				$('#assignement_date_list').datepick({ 
					renderer: $.datepick.themeRollerRenderer,
					multiSelect: 999
					}); 
			</script>
			<p>
				<label for="assignement_load">Charge journalière</label>
				<select name="assignement_load" id="assignement_load" class="chzn-select">
					<option value="1">1 Heure</option>
					<option value="2">2 Heures</option>
					<option value="3">3 Heures</option>
					<option value="4">4 Heures (1/2 journée)</option>
					<option value="5">5 Heures</option>
					<option value="6">6 Heures</option>
					<option value="7">7 Heures</option>
					<option value="8" selected="selected">8 Heures (1 journée)</option>
					<!--<option value="9">9 Heures (1 heure supp.)</option>
					<option value="10">10 Heures (2 heure supp.)</option>
					<option value="11">11 Heures (3 heure supp.)</option>
					<option value="12">12 Heures (4 heure supp.)</option>-->
					
				</select>
			</p>
			<p>
				<input type="submit" value="Créer" /> ou <a href="loader.php?module=cra_list">annuler</a>
			</p>
		</form>
	</p>
</div>



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
$header_title = 'GENYMOBILE - Ajout CRA';
$required_group_rights = 6;

include_once 'header.php';
include_once 'menu.php';

$geny_ptr = new GenyProjectTaskRelation();
$geny_tools = new GenyTools();
date_default_timezone_set('Europe/Paris');

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/cra.png"/><p>CRA</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
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

		<form id="formID" action="cra_validation.php" method="post">
			<input type="hidden" name="create_cra" value="true" />
			<p>
				<label for="assignement_id">Projet</label>
				<select name="assignement_id" id="assignement_id" />
					<?php
						$geny_assignements = new GenyAssignement();
						foreach( $geny_assignements->getAssignementsListByProfileId( $profile->id ) as $assignement ){
							$p = new GenyProject( $assignement->project_id );
							if( strripos($p->name,'congés') === false )
								echo "<option value=\"$assignement->id\" title=\"$p->description\">$p->name</input></option>";
						}
					?>
				</select>
			</p>
			<p>
				<label for="task_id">Tâche</label>
				<select name="task_id" id="task_id" />
				</select>
				<script>
					function getTasks(){
						var project_id = $("#assignement_id").val();
						$.get('backend/api/get_project_tasks_list.php?assignement_id='+project_id, function(data){
							$('.tasks_options').remove();
							$.each(data, function(key, val) {
								$("#task_id").append('<option class="tasks_options" value="' + val[0] + '" title="' + val[2] + '">' + val[1] + '</option>');
							});

						},'json');
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
				<label for="assignement_start_date">Date de début</label>
				<input name="assignement_start_date" id="assignement_start_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="assignement_end_date">Date de fin</label>
				<input name="assignement_end_date" id="assignement_end_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="assignement_load">Charge journalière</label>
				<select name="assignement_load" id="assignement_load">
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
				<input type="submit" value="Créer" /> ou <a href="#formID">annuler</a>
			</p>
		</form>
	</p>
</div>

<?php
include_once 'footer.php';
?>

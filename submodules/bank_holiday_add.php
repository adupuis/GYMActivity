<?php
//  Copyright (C) 2011 by GENYMOBILE & Arnaud Dupuis
//  adupuist@genymobile.com
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
$geny_country = new GenyCountry();

?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/bank_holiday_add.png"></img>
		<span class="bank_holiday_add">
			Ajouter un jour férié (bank holiday)
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter un jour férié. Tous les champs doivent être remplis.
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			$(document).ready(function(){
				$(".projectlistselect").listselect({listTitle: "Profils disponibles",selectedTitle: "Profils sélectionnés"});
			});
		</script>
		<form id="formID" action="loader.php?module=bank_holiday_edit" method="post">
			<input type="hidden" name="create_bank_holiday" value="true" />
			<p>
				<label for="project_id">Projet</label>
				<select name="project_id" id="project_id" class="chzn-select" data-placeholder="Choisissez un projet...">
					<option value=""></option>
					<?php
                        // Get only projects that are type "Congés"
                        // TODO: This should be a Property
						foreach( $geny_project->getProjectsByTypeId(5) as $project ) {
                            echo "<option value=\"".$project->id."\">".$project->name."</option>\n";
						}
					?>
				</select>
			</p>
			<p>
				<label for="bank_holiday_type">Type</label>
				<select name="bank_holiday_type" id="bank_holiday_type" class="chzn-select" data-placeholder="Choisissez un type de congé...">
					<option value=""></option>
				</select>
			</p>
			<script type="text/javascript">
                function getTasks(){
						var project_id = $("#project_id").val();
						if( project_id > 0 ) {
							$.get('backend/api/get_project_tasks_list.php?no_task_blacklist=1&project_id='+project_id, function(data){
								$('.bank_holiday_options').remove();
								$("#bank_holiday_type").append('<option value="" class="bank_holiday_options"></option>');
								$.each(data, function(key, val) {
									$("#bank_holiday_type").append('<option class="bank_holiday_options" value="' + val[0] + '" title="' + val[2] + '">' + val[1] + '</option>');
								});
								$("#bank_holiday_type").attr('data-placeholder','Choisissez un type de congé...');
								$("#bank_holiday_type").trigger("liszt:updated");
								$("span:contains('Choisissez d'abord un projet...')").text('Choisissez un type de congé...');
								

							},'json');
						}
					}
					$("#project_id").change(getTasks);
					getTasks();
				$(function() {
					$( "#bank_holiday_start_date" ).datepicker();
					$( "#bank_holiday_start_date" ).datepicker('setDate', new Date());
					$( "#bank_holiday_start_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#bank_holiday_start_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#bank_holiday_start_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#bank_holiday_start_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#bank_holiday_start_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#bank_holiday_start_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#bank_holiday_start_date" ).datepicker( "option", "firstDay", 1 );
					
					$( "#bank_holiday_stop_date" ).datepicker();
					$( "#bank_holiday_stop_date" ).datepicker('setDate', new Date());
					$( "#bank_holiday_stop_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#bank_holiday_stop_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#bank_holiday_stop_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#bank_holiday_stop_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#bank_holiday_stop_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#bank_holiday_stop_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#bank_holiday_stop_date" ).datepicker( "option", "firstDay", 1 );
				});
			</script>
			<p>
				<label for="bank_holiday_name">Nom</label>
				<input name="bank_holiday_name" id="bank_holiday_name" type="text" class="validate[required] text-input" />
			</p>
			<p>
				<label for="bank_holiday_start_date">Début de période</label>
				<input name="bank_holiday_start_date" id="bank_holiday_start_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="bank_holiday_stop_date">Fin de période</label>
				<input name="bank_holiday_stop_date" id="bank_holiday_stop_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			
			<p>
				<label for="country_id">Pays concerné</label>
				<select name="country_id" id="country_id" class="chzn-select" data-placeholder="Choisissez un pays...">
					<option value=""></option>
					<?php
						foreach( $geny_country->getAllCountries() as $c ) {
                            echo "<option value=\"".$c->id."\">".$c->name."</option>\n";
						}
					?>
				</select>
			</p>
			
			<p>
				<input type="submit" value="Ajouter" /> ou <a href="loader.php?module=bank_holiday_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/bank_holiday_list.dock.widget.php');
?>

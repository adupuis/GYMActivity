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


$geny_profile = new GenyProfile();

?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/holiday_summary_generic.png"></img>
		<span class="holiday_summary_add">
			Ajouter solde congés
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter un solde de congés. Tous les champs doivent être remplis.
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
// 			const length = 600;
//             const range = new Array(length).fill();
//             const default_values = range.map(e => range.map(e => range.map(e => e)));
		</script>
		<form id="formID" action="loader.php?module=holiday_summary_edit" method="post">
			<input type="hidden" name="create_holiday_summary" value="true" />
			<p>
				<label for="profile_id">Profil</label>
				<select name="profile_id" id="profile_id" class="chzn-select" data-placeholder="Choisissez un profil...">
					<option value=""></option>
					<?php
						foreach( $geny_profile->getProfileByActivation(1) as $profile ) {
							if( $profile->firstname && $profile->lastname ) {
								echo "<option value=\"".$profile->id."\">".$profile->firstname." ".$profile->lastname."</option>\n";
							}
							else {
								echo "<option value=\"".$profile->id."\">".$profile->login."</option>\n";
							}
						}
					?>
				</select>
			</p>
			
			<p>
				<label for="project_id">Projet de congés</label>
				<select name="project_id" id="project_id" class="chzn-select" data-placeholder="Choisissez un projet...">
					<option value=""></option>
				</select>
			</p>
			<p>
				<label for="task_id">Tâche</label>
				<select name="task_id" id="task_id" class="chzn-select" data-placeholder="Choisissez d'abord un projet...">
					<option value=""></option>
				</select>
			</p>
			
			<!--<p>
				<label for="holiday_summary_type">Type</label>
				<select name="holiday_summary_type" id="holiday_summary_type" class="chzn-select" data-placeholder="Choisissez un type de congé...">
					<option value=""></option>
					<option value="CP">CP</option>
					<option value="RTT">RTT</option>
					<option value="PTO">PTO</option>
					<option value="PD">Personal Day</option>
					<option value="SL">Sick Leave</option>
				</select>
			</p>-->
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
				
				function getAssignements(){
                    var profile_id = $("#profile_id").val();
                    if( profile_id > 0 ) {
						$.get('backend/api/get_assignements_list.php?profile_id='+profile_id+'&project_type_id=5', function( data ) {
							$('.project_options').remove();
							$.each( data, function( key, val ) {
                                console.log("key="+key+' val[project_name]='+val['project_name']);
								$("#project_id").append('<option class="project_options" value="' + val['project_id'] + '" title="' + val['project_name'] + '">' + val['project_name'] + '</option>');
							});
							$("#project_id").attr('data-placeholder','Choisissez une tâche...');
							$("#project_id").trigger("liszt:updated");
							$("span:contains('Choisissez d'abord un projet...')").text('Choisissez une tâche...');

						},'json');
					}
				}
				$("#profile_id").change( getAssignements );
				getAssignements();
				
				function setDefaultCounts(){
                    // Init everything back to no values
                    $( "#holiday_summary_period_start" ).datepicker('setDate', '1979-01-01' );
                    $( "#holiday_summary_period_end" ).datepicker('setDate', '1979-12-31');
                    $("#holiday_summary_count_acquired").val('0.00');
                    $("#holiday_summary_count_taken").val('0.00');
                    $("#holiday_summary_count_taken").change();
                    var project_id = $("#project_id").val();
                    console.log("project_id="+project_id);
                    var task_id = $("#task_id").val();
                    console.log("task_id="+task_id);
                    
                    const default_values = [[[]]];
                    
//                     var default_values = [[],[],[]];
//                     default_values[project_id] = [[],[]];
//                     default_values[project_id][task_id] = [];
                    var date = new Date();
                    <?php
                        $geny_property = new GenyProperty();
                        $geny_p_v = new GenyPropertyValue();
                        $prop_array = $geny_property->searchProperties("HOLIDAY_SUMMARY_DEFAULT_");
                        $is_init = array();
                        foreach($prop_array as $tmp_prop){
                            error_log("!! DEBUG !! geny_property found with name $tmp_prop->name",0);
                            $prop_name_exploded = explode('_',$tmp_prop->name);
                            $property_values = $geny_p_v->getPropertyValuesByPropertyId($tmp_prop->id);
                            if( count($property_values) == 1 ){
                                error_log("\tValue: ".$property_values[0]->content,0);
                                $tmp_values = explode(';',$property_values[0]->content);
                                //YEAR == 3 & 5
                                $year_start = explode('+',$tmp_values[3]);
                                if(!isset($year_start[1])){
                                    $year_start[1]=0;
                                }
                                error_log("\tyear_start: ".$year_start[0]." + ".$year_start[1],0);
                                $year_end = explode('+',$tmp_values[5]);
                                if(!isset($year_end[1])){
                                    $year_end[1]=0;
                                }
                                if($is_init[$prop_name_exploded[3]] != 1){
                                    echo "default_values[".$prop_name_exploded[3]."] = [[],[]];\n";
                                    $is_init[$prop_name_exploded[3]] = 1;
                                }
                                echo "default_values[".$prop_name_exploded[3]."][".$prop_name_exploded[4]."] = ['".$tmp_values[0]."','".$tmp_values[1]."',(date.getFullYear()+".$year_start[1].")+\"-".$tmp_values[2]."\",(date.getFullYear()+".$year_end[1].")+\"-".$tmp_values[4]."\"];\n";
                                
                            }
                            else{
                                // TODO: take care of the error
                            }
                        }
                    ?>
//                     default_values[2][11] = ['25.00','0.00',(date.getFullYear()+1)+"-06-01",(date.getFullYear()+2)+"-05-31"];
//                     default_values[2][17] = ['11.00','0.00',(date.getFullYear()+1)+"-01-01",(date.getFullYear()+2)+"-12-31"];
//                     default_values[450] = [[],[]];
//                     default_values[450][80] = ['24.00','0.00',(date.getFullYear()+1)+"-01-01",(date.getFullYear()+2)+"-12-31"];
//                     default_values[450][81] = ['3.00','0.00',(date.getFullYear()+1)+"-01-01",(date.getFullYear()+2)+"-12-31"];
//                     default_values[450][82] = ['14.00','0.00',(date.getFullYear()+1)+"-01-01",(date.getFullYear()+2)+"-12-31"];
                    if(default_values[project_id][task_id] !== null){
                        $( "#holiday_summary_period_start" ).datepicker('setDate', default_values[project_id][task_id][2] );
						$( "#holiday_summary_period_end" ).datepicker('setDate', default_values[project_id][task_id][3]);
						$("#holiday_summary_count_acquired").val(default_values[project_id][task_id][0]);
						$("#holiday_summary_count_taken").val(default_values[project_id][task_id][1]);
						$("#holiday_summary_count_taken").change();
                    }
				}
				$("#task_id").change( setDefaultCounts );
                
				$(function() {
					$( "#holiday_summary_period_start" ).datepicker();
					$( "#holiday_summary_period_start" ).datepicker('setDate', new Date());
					$( "#holiday_summary_period_start" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#holiday_summary_period_start" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#holiday_summary_period_start" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#holiday_summary_period_start" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#holiday_summary_period_start" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#holiday_summary_period_start" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#holiday_summary_period_start" ).datepicker( "option", "firstDay", 1 );
					
					$( "#holiday_summary_period_end" ).datepicker();
					$( "#holiday_summary_period_end" ).datepicker('setDate', new Date());
					$( "#holiday_summary_period_end" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#holiday_summary_period_end" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#holiday_summary_period_end" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#holiday_summary_period_end" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#holiday_summary_period_end" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#holiday_summary_period_end" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#holiday_summary_period_end" ).datepicker( "option", "firstDay", 1 );
				});
			</script>
			<p>
				<label for="holiday_summary_period_start">Début de période</label>
				<input name="holiday_summary_period_start" id="holiday_summary_period_start" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="holiday_summary_period_end">Fin de période</label>
				<input name="holiday_summary_period_end" id="holiday_summary_period_end" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="holiday_summary_count_acquired">Acquis</label>
				<input name="holiday_summary_count_acquired" id="holiday_summary_count_acquired" type="text" class="validate[required,custom[onlyFloatNumber]] text-input" />
			</p>
			<p>
				<label for="holiday_summary_count_taken">Pris</label>
				<input name="holiday_summary_count_taken" id="holiday_summary_count_taken" type="text" class="validate[required,custom[onlyFloatNumber]] text-input" />
			</p>
			<script>
				$("#holiday_summary_count_taken").change(function(){
					var remaining = $('#holiday_summary_count_acquired').val() - $('#holiday_summary_count_taken').val();
					$('#holiday_summary_count_remaining').val( remaining.toFixed(2) );
				});
				$("#holiday_summary_count_acquired").change(function(){
					$("#holiday_summary_count_taken").change();
				});
				$("#holiday_summary_type").chosen().change( function(){
					var value = $("#holiday_summary_type").val();
					var date = new Date();
					if( value == "CP" ){
						$( "#holiday_summary_period_start" ).datepicker('setDate', date.getFullYear()+"-06-01");
						$( "#holiday_summary_period_end" ).datepicker('setDate', (date.getFullYear()+1)+"-05-31");
						$("#holiday_summary_count_acquired").val('25.00');
						$("#holiday_summary_count_taken").val('0.00');
						$("#holiday_summary_count_taken").change();
					}
					else if( value == "RTT" ){
						$( "#holiday_summary_period_start" ).datepicker('setDate', date.getFullYear()+"-01-01");
						$( "#holiday_summary_period_end" ).datepicker('setDate', date.getFullYear()+"-12-31");
						$("#holiday_summary_count_acquired").val('11.00');
						$("#holiday_summary_count_taken").val('0.00');
						$("#holiday_summary_count_taken").change();
					}
					else if( value == "PTO" ){
						$( "#holiday_summary_period_start" ).datepicker('setDate', date.getFullYear()+"-01-01");
						$( "#holiday_summary_period_end" ).datepicker('setDate', date.getFullYear()+"-12-31");
						$("#holiday_summary_count_acquired").val('24.00');
						$("#holiday_summary_count_taken").val('0.00');
						$("#holiday_summary_count_taken").change();
					}
					else if( value == "PD" ){
						$( "#holiday_summary_period_start" ).datepicker('setDate', date.getFullYear()+"-01-01");
						$( "#holiday_summary_period_end" ).datepicker('setDate', date.getFullYear()+"-12-31");
						$("#holiday_summary_count_acquired").val('3.00');
						$("#holiday_summary_count_taken").val('0.00');
						$("#holiday_summary_count_taken").change();
					}
					else if( value == "SL" ){
						$( "#holiday_summary_period_start" ).datepicker('setDate', date.getFullYear()+"-01-01");
						$( "#holiday_summary_period_end" ).datepicker('setDate', date.getFullYear()+"-12-31");
						$("#holiday_summary_count_acquired").val('14.00');
						$("#holiday_summary_count_taken").val('0.00');
						$("#holiday_summary_count_taken").change();
					}
					
				});
			</script>
			<p>
				<label for="holiday_summary_count_remaining">Restant</label>
				<input name="holiday_summary_count_remaining" id="holiday_summary_count_remaining" type="text" class="validate[required,custom[onlyFloatNumber]] text-input" />
			</p>
			<p>
				<input type="submit" value="Ajouter" /> ou <a href="loader.php?module=holiday_summary_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/holiday_summary_list.dock.widget.php');
?>

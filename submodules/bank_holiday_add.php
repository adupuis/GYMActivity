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


$geny_profile = new GenyProfile();

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
				$(".profileslistselect").listselect({listTitle: "Profils disponibles",selectedTitle: "Profils sélectionnés"});
			});
		</script>
		<form id="formID" action="loader.php?module=bank_holiday_edit" method="post">
			<input type="hidden" name="create_bank_holiday" value="true" />
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
				<label for="bank_holiday_type">Type</label>
				<select name="bank_holiday_type" id="bank_holiday_type" class="chzn-select" data-placeholder="Choisissez un type de congé...">
					<option value=""></option>
					<option value="CP">CP</option>
					<option value="RTT">RTT</option>
				</select>
			</p>
			<script type="text/javascript">
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
				<label for="bank_holiday_start_date">Début de période</label>
				<input name="bank_holiday_start_date" id="bank_holiday_start_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="bank_holiday_stop_date">Fin de période</label>
				<input name="bank_holiday_stop_date" id="bank_holiday_stop_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="bank_holiday_count_acquired">Acquis</label>
				<input name="bank_holiday_count_acquired" id="bank_holiday_count_acquired" type="text" class="validate[required,custom[onlyFloatNumber]] text-input" />
			</p>
			<p>
				<label for="bank_holiday_count_taken">Pris</label>
				<input name="bank_holiday_count_taken" id="bank_holiday_count_taken" type="text" class="validate[required,custom[onlyFloatNumber]] text-input" />
			</p>
			<script>
				$("#bank_holiday_count_taken").change(function(){
					var remaining = $('#bank_holiday_count_acquired').val() - $('#bank_holiday_count_taken').val();
					$('#bank_holiday_count_remaining').val( remaining.toFixed(2) );
				});
				$("#bank_holiday_count_acquired").change(function(){
					$("#bank_holiday_count_taken").change();
				});
				$("#bank_holiday_type").chosen().change( function(){
					var value = $("#bank_holiday_type").val();
					var date = new Date();
					if( value == "CP" ){
						$( "#bank_holiday_start_date" ).datepicker('setDate', date.getFullYear()+"-06-01");
						$( "#bank_holiday_stop_date" ).datepicker('setDate', (date.getFullYear()+1)+"-05-31");
					}
					if( value == "RTT" ){
						$( "#bank_holiday_start_date" ).datepicker('setDate', date.getFullYear()+"-01-01");
						$( "#bank_holiday_stop_date" ).datepicker('setDate', date.getFullYear()+"-12-31");
					}
					$("#bank_holiday_count_acquired").val('0.00');
					$("#bank_holiday_count_taken").val('0.00');
					$("#bank_holiday_count_taken").change();
				});
			</script>
			<p>
				<label for="bank_holiday_count_remaining">Restant</label>
				<input name="bank_holiday_count_remaining" id="bank_holiday_count_remaining" type="text" class="validate[required,custom[onlyFloatNumber]] text-input" />
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

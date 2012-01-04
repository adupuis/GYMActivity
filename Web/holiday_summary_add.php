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
$header_title = '%COMPANY_NAME% - Ajout Solde de congés';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';

$geny_profile = new GenyProfile();

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/conges_admin_generic.png"/><p>Solde de congés</p>
</div>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="holiday_summary_add">
			Ajouter un solde de congés
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
		</script>
		<form id="formID" action="holiday_summary_edit.php" method="post">
			<input type="hidden" name="create_holiday_summary" value="true" />
			<p>
				<label for="profile_id">Profil</label>
				<select name="profile_id" id="profile_id">
				<?php
					foreach( $geny_profile->getAllProfiles() as $profile ) {
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
				<label for="holiday_summary_type">Type</label>
				<select name="holiday_summary_type" id="holiday_summary_type">
					<option value="CP">CP</option>
					<option value="RTT">RTT</option>
				</select>
			</p>
			<script type="text/javascript">
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
			<p>
				<label for="holiday_summary_count_remaining">Restant</label>
				<input name="holiday_summary_count_remaining" id="holiday_summary_count_remaining" type="text" class="validate[required,custom[onlyFloatNumber]] text-input" />
			</p>
			<p>
				<input type="submit" value="Ajouter" /> ou <a href="holiday_summary_list.php">annuler</a>
			</p>
		</form>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php 
// 			include 'backend/widgets/idea_list.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

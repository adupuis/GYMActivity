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

$geny_holiday_summary = new GenyHolidaySummary();
$geny_profile = new GenyProfile();
$geny_tools = new GenyTools();

if( isset( $_POST['create_holiday_summary'] ) && $_POST['create_holiday_summary'] == "true" ) {
	if( isset( $_POST['holiday_summary_count_acquired'] ) && isset( $_POST['holiday_summary_count_taken'] ) && isset( $_POST['holiday_summary_count_remaining'] ) ) {
		$insert_id = $geny_holiday_summary->insertNewHolidaySummary( 'NULL', $_POST['profile_id'], $_POST['holiday_summary_type'], $_POST['holiday_summary_period_start'], $_POST['holiday_summary_period_end'], $_POST['holiday_summary_count_acquired'], $_POST['holiday_summary_count_taken'], $_POST['holiday_summary_count_remaining'] );
		error_log( "[GYMActivity::DEBUG] holiday_summary_edit insert_id : $insert_id", 0 );
		if( $insert_id != -1 ) {
			$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"Solde de congés ajouté avec succès." );
			$geny_holiday_summary->loadHolidaySummaryById( $insert_id );
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout du solde de congés." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir." );
	}
}
else if( isset( $_POST['load_holiday_summary'] ) && $_POST['load_holiday_summary'] == "true" ) {
	if( isset( $_POST['holiday_summary_id'] ) ) {
		$geny_holiday_summary->loadHolidaySummaryById( $_POST['holiday_summary_id'] );
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Impossible de charger le solde de congés','msg'=>"id non spécifié." );
	}
}
else if( isset( $_GET['load_holiday_summary'] ) && $_GET['load_holiday_summary'] == "true" ) {
	if( isset( $_GET['holiday_summary_id'] ) ) {
		$tmp_geny_holiday_summary = new GenyHolidaySummary();
		$tmp_geny_holiday_summary->loadHolidaySummaryById( $_GET['holiday_summary_id'] );
		if( $profile->rights_group_id == 1  || /* admin */
		    $profile->rights_group_id == 2     /* superuser */ ) {
			$geny_holiday_summary->loadHolidaySummaryById( $_GET['holiday_summary_id'] );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger le solde de congés ",'msg'=>"Vous n'êtes pas autorisé.");
			header( 'Location: error.php?category=holiday_summary&backlinks=holiday_summary_list,holiday_summary_add' );
		}
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger le solde de congés",'msg'=>"id non spécifié.");
	}
}
else if( isset( $_POST['edit_holiday_summary'] ) && $_POST['edit_holiday_summary'] == "true" ) {
	if( isset( $_POST['holiday_summary_id'] ) ) {
		$geny_holiday_summary->loadHolidaySummaryById( $_POST['holiday_summary_id'] );
		
		if( $profile->rights_group_id == 1 /* admin */       ||
		    $profile->rights_group_id == 2 /* superuser */ ) {
			if( isset( $_POST['profile_id'] ) && $_POST['profile_id'] != "" && $geny_holiday_summary->profile_id != $_POST['profile_id'] ) {
				$geny_holiday_summary->updateInt( 'profile_id', $_POST['profile_id'] );
			}
			if( isset( $_POST['holiday_summary_type'] ) && $_POST['holiday_summary_type'] != "" && $geny_holiday_summary->type != $_POST['holiday_summary_type'] ) {
				$geny_holiday_summary->updateString( 'holiday_summary_type', $_POST['holiday_summary_type'] );
			}
			if( isset( $_POST['holiday_summary_period_start'] ) && $_POST['holiday_summary_period_start'] != "" && $geny_holiday_summary->period_start != $_POST['holiday_summary_period_start'] ) {
				$geny_holiday_summary->updateString( 'holiday_summary_period_start', $_POST['holiday_summary_period_start'] );
			}
			if( isset( $_POST['holiday_summary_period_end'] ) && $_POST['holiday_summary_period_end'] != "" && $geny_holiday_summary->period_end != $_POST['holiday_summary_period_end'] ) {
				$geny_holiday_summary->updateString( 'holiday_summary_period_end', $_POST['holiday_summary_period_end'] );
			}
			if( isset( $_POST['holiday_summary_count_acquired'] ) && $_POST['holiday_summary_count_acquired'] != "" && $geny_holiday_summary->count_acquired != $_POST['holiday_summary_count_acquired'] ) {
				$geny_holiday_summary->updateString( 'holiday_summary_count_acquired', $_POST['holiday_summary_count_acquired'] );
			}
			if( isset( $_POST['holiday_summary_count_taken'] ) && $_POST['holiday_summary_count_taken'] != "" && $geny_holiday_summary->count_taken != $_POST['holiday_summary_count_taken'] ) {
				$geny_holiday_summary->updateString( 'holiday_summary_count_taken', $_POST['holiday_summary_count_taken'] );
			}
			if( isset( $_POST['holiday_summary_count_remaining'] ) && $_POST['holiday_summary_count_remaining'] != "" && $geny_holiday_summary->count_remaining != $_POST['holiday_summary_count_remaining'] ) {
				$geny_holiday_summary->updateString( 'holiday_summary_count_remaining', $_POST['holiday_summary_count_remaining'] );
			}
		}
		if( $geny_holiday_summary->commitUpdates() ) {
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Solde de congés mis à jour avec succès.");
			$geny_holiday_summary->loadHolidaySummaryById( $_POST['holiday_summary_id'] );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour du solde de congés.");
		}

	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de modifier le solde de congés ','msg'=>"id non spécifié.");
	}
}

?>
<div id="mainarea">
	<p class="mainarea_title">
		<span class="holiday_summary_edit">
			Modifier un solde de congés
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
		Ce formulaire permet d'éditer un solde de congés existant. Tous les champs doivent être remplis.
		</p>
		
		<form id="select_holiday_summary_form" action="loader.php?module=holiday_summary_edit" method="post">
			<input type="hidden" name="load_holiday_summary" value="true" />
			<p>
				<label for="holiday_summary_id">Sélection solde de congés</label>

				<select name="holiday_summary_id" id="holiday_summary_id" onChange="submit()">
					<?php
						$holiday_summaries = $geny_holiday_summary->getAllHolidaySummaries();

						$concat_array = array();
						$i = 0;
						foreach( $holiday_summaries as $holiday_summary ) {
							foreach( $geny_profile->getAllProfiles() as $prof ) {
								if( $holiday_summary->profile_id == $prof->id ) {
									if( $prof->firstname && $prof->lastname ) {
										$prof_scr_name = $prof->firstname.' '.$prof->lastname;
									}
									else {
										$prof_scr_name = $prof->login;
									}
									break;
								}
							}
							if( $geny_holiday_summary->id == $holiday_summary->id ) {
								$concat1 = "<option value=\"".$holiday_summary->id."\" selected>";
							}
							else {
								$concat1 = "<option value=\"".$holiday_summary->id."\">";
							}
							$concat2 = $prof_scr_name.' - '.$holiday_summary->type.' - du '.$holiday_summary->period_start.' au '.$holiday_summary->period_end."</option>\n";
							$concat_array2 = array();
							$concat_array2["first"] = $concat1;
							$concat_array2["second"] = $concat2;
							$concat_array[$i] = $concat_array2;
							$i++;
						}
						$concat_array = $geny_tools->sortMultiArrayCaseInsensitive( $concat_array, "second" );

						foreach( $concat_array as $concat ) {
							echo $concat["first"].$concat["second"];
						}

						if( $geny_holiday_summary->id < 0 ) {
							$geny_holiday_summary->loadHolidaySummaryById( $holiday_summaries[0]->id );
						}
					?>
				</select>
			</p>
		</form>
		<form id="formID" action="loader.php?module=holiday_summary_edit" method="post">
			<input type="hidden" name="edit_holiday_summary" value="true" />
			<input type="hidden" name="holiday_summary_id" value="<?php echo $geny_holiday_summary->id ?>" />
			

			<p>
				<label for="profile_id">Profil</label>
				<select name="profile_id" id="profile_id">
				<?php
					foreach( $geny_profile->getAllProfiles() as $profile ) {
						if( $geny_holiday_summary->profile_id == $profile->id ) {
							if( $profile->firstname && $profile->lastname ) {
								echo "<option value=\"".$profile->id."\" selected>".$profile->firstname." ".$profile->lastname."</option>\n";
							}
							else {
								echo "<option value=\"".$profile->id."\" selected>".$profile->login."</option>\n";
							}
						}
						else {
							if( $profile->firstname && $profile->lastname ) {
								echo "<option value=\"".$profile->id."\">".$profile->firstname." ".$profile->lastname."</option>\n";
							}
							else {
								echo "<option value=\"".$profile->id."\">".$profile->login."</option>\n";
							}
						}
					}
				?>
				</select>
			</p>
			<p>
				<label for="holiday_summary_type">Type</label>
				<select name="holiday_summary_type" id="holiday_summary_type">
					<?php
					if( $geny_holiday_summary->type == "RTT" ) {
						echo "<option value=\"CP\">CP</option>";
						echo "<option value=\"RTT\" selected>RTT</option>";
					}
					else {
						echo "<option value=\"CP\">CP</option>";
						echo "<option value=\"RTT\">RTT</option>";
					}
					?>
				</select>
			</p>
			<script type="text/javascript">
				$(function() {
					$( "#holiday_summary_period_start" ).datepicker();
					$( "#holiday_summary_period_start" ).datepicker('setDate', new Date());
					$( "#holiday_summary_period_start" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#holiday_summary_period_start" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#holiday_summary_period_start" ).datepicker( "option", "defaultDate", "<?php echo $geny_holiday_summary->period_start ?>" );
					$( "#holiday_summary_period_start" ).datepicker( "setDate", "<?php echo $geny_holiday_summary->period_start ?>" );
					$( "#holiday_summary_period_start" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#holiday_summary_period_start" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#holiday_summary_period_start" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#holiday_summary_period_start" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#holiday_summary_period_start" ).datepicker( "option", "firstDay", 1 );
					
					$( "#holiday_summary_period_end" ).datepicker();
					$( "#holiday_summary_period_end" ).datepicker('setDate', new Date() );
					$( "#holiday_summary_period_end" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#holiday_summary_period_end" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#holiday_summary_period_end" ).datepicker( "option", "defaultDate", "<?php echo $geny_holiday_summary->period_end ?>" );
					$( "#holiday_summary_period_end" ).datepicker( "setDate", "<?php echo $geny_holiday_summary->period_end ?>" );
					$( "#holiday_summary_period_end" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#holiday_summary_period_end" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#holiday_summary_period_end" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#holiday_summary_period_end" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#holiday_summary_period_end" ).datepicker( "option", "firstDay", 1 );
				});
			</script>
			<p>
				<label for="holiday_summary_period_start">Début de période</label>
				<input name="holiday_summary_period_start" id="holiday_summary_period_start" type="text" value="<?php echo $geny_holiday_summary->period_start ?>" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="holiday_summary_period_end">Fin de période</label>
				<input name="holiday_summary_period_end" id="holiday_summary_period_end" type="text" value="<?php echo $geny_holiday_summary->period_end ?>" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="holiday_summary_count_acquired">Acquis</label>
				<input name="holiday_summary_count_acquired" id="holiday_summary_count_acquired" type="text" value="<?php echo $geny_holiday_summary->count_acquired ?>" class="validate[required,custom[onlyFloatNumber]] text-input" />
			</p>
			<p>
				<label for="holiday_summary_count_taken">Pris</label>
				<input name="holiday_summary_count_taken" id="holiday_summary_count_taken" type="text" value="<?php echo $geny_holiday_summary->count_taken ?>" class="validate[required,custom[onlyFloatNumber]] text-input" />
			</p>
			<p>
				<label for="holiday_summary_count_remaining">Restant</label>
				<input name="holiday_summary_count_remaining" id="holiday_summary_count_remaining" type="text" value="<?php echo $geny_holiday_summary->count_remaining ?>" class="validate[required,custom[onlyFloatNumber]] text-input" />
			</p>



			<p>
				<input type="submit" value="Modifier" /> ou <a href="loader.php?module=holiday_summary_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/holiday_summary_list.dock.widget.php');
?>

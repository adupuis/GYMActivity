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

$handle = mysql_connect( $web_config->db_host, $web_config->db_user, $web_config->db_password );
mysql_select_db( $web_config->db_name );
mysql_query( "SET NAMES 'utf8'" );

$remove_holiday_summary = GenyTools::getParam( 'remove_holiday_summary', 'NULL' );
if( $remove_holiday_summary == "true" ) {
	$holiday_summary_id = GenyTools::getParam( 'holiday_summary_id', 'NULL' );
	if( $holiday_summary_id != 'NULL' ) {
		$force_remove_holiday_summary = GenyTools::getParam( 'force_remove', 'NULL' );
		if( $force_remove_holiday_summary == "true" ) {
			$id = GenyTools::getParam( 'holiday_summary_id', 'NULL' );
			$geny_holiday_summary->loadHolidaySummaryById( $id );
			if( $profile->rights_group_id == 1 /* admin */       ||
			    $profile->rights_group_id == 2 /* superuser */)  {
				$query = "DELETE FROM HolidaySummaries WHERE holiday_summary_id=$id";
				if( !mysql_query( $query ) ) {
					$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression du solde de congés de la table HolidaySummaries." );
				}
				else
					$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"Solde de congés supprimé avec succès." );
			}
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => "Impossible de supprimer le solde de congés ",'msg'=>"id non spécifié." );
	}
}
else {
// 	$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Aucune action spécifiée.");
}


?>

<style>
	@import "styles/genymobile-2012/chosen_override.css";
</style>

<div id="mainarea">
	<p class="mainarea_title">
	<img src="images/<?php echo $web_config->theme; ?>/holiday_summary_remove.png"></img>
		<span class="holiday_summary_remove">
			Supprimer un solde de congés
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de <strong>supprimer définitivement</strong> un solde de congés.
		</p>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications( $gritter_notifications, $web_config->theme );
			?>
		</script>
		<script>
			jQuery(document).ready(function(){
				$("#select_login_form").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#select_login_form").validationEngine('attach');
			});
			
		</script>
		<form id="select_login_form" action="loader.php?module=holiday_summary_remove" method="post">
			<input type="hidden" name="remove_holiday_summary" value="true" />
			<p>
				<label for="holiday_summary_id">Sélection solde de congés</label>

				<select name="holiday_summary_id" id="holiday_summary_id" class="chzn-select">
					<?php
					$holiday_summaries = $geny_holiday_summary->getAllHolidaySummaries();

					$holiday_summary_id = GenyTools::getParam( 'holiday_summary_id', 'NULL' );

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
						if( $holiday_summary_id == $holiday_summary->id ) {
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
					$concat_array = GenyTools::sortMultiArrayCaseInsensitive( $concat_array, "second" );

					foreach( $concat_array as $concat ) {
						echo $concat["first"].$concat["second"];
					}

					if( $geny_holiday_summary->id < 0 ) {
						$geny_holiday_summary->loadHolidaySummaryById( $holiday_summaries[0]->id );
					}
					?>
				</select>
			</p>
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression du solde de congés. <strong>La suppression est définitive et ne pourra pas être annulée.</strong>
			</p>
			<p>
				<input type="submit" value="Supprimer" /> ou <a href="loader.php?module=holiday_summary_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/holiday_summary_list.dock.widget.php','backend/widgets/holiday_summary_add.dock.widget.php');
?>

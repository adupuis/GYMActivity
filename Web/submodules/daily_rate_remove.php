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

$geny_daily_rate = new GenyDailyRate();
$geny_project = new GenyProject();
$geny_task = new GenyTask();
$geny_profile = new GenyProfile();

$handle = mysql_connect( $web_config->db_host, $web_config->db_user, $web_config->db_password );
mysql_select_db( $web_config->db_name );
mysql_query( "SET NAMES 'utf8'" );

$remove_daily_rate = GenyTools::getParam( 'remove_daily_rate', 'NULL' );
if( $remove_daily_rate == "true" ) {
	$daily_rate_id = GenyTools::getParam( 'daily_rate_id', 'NULL' );
	if( $daily_rate_id != 'NULL' ) {
		$force_remove_daily_rate = GenyTools::getParam( 'force_remove', 'NULL' );
		if( $force_remove_daily_rate == "true" ) {
			$id = GenyTools::getParam( 'daily_rate_id', 'NULL' );
			$geny_daily_rate->loadDailyRateById( $id );
			if( $profile->rights_group_id == 1 /* admin */       ||
			    $profile->rights_group_id == 2 /* superuser */)  {
				$query = "DELETE FROM DailyRates WHERE daily_rate_id=$id";
				if( !mysql_query( $query ) ) {
					$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression du TJM de la table DailyRates." );
				}
				else
					$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"TJM supprimé avec succès." );
			}
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => "Impossible de supprimer le TJM",'msg'=>"id non spécifié." );
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
		<img src="images/<?php echo $web_config->theme; ?>/daily_rate_remove.png"></img>
		<span class="daily_rate_remove">
			Supprimer un TJM
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de <strong>supprimer définitivement</strong> un TJM.
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
		<form id="select_login_form" action="loader.php?module=daily_rate_remove" method="post">
			<input type="hidden" name="remove_daily_rate" value="true" />
			<p>
				<label for="daily_rate_id">Sélection TJM</label>

				<select name="daily_rate_id" id="daily_rate_id" class="chzn-select">
					<?php
					$daily_rates = $geny_daily_rate->getAllDailyRates();

					$daily_rate_id = GenyTools::getParam( 'daily_rate_id', 'NULL' );

					$concat_array = array();
						$i = 0;
						foreach( $daily_rates as $daily_rate ) {


							foreach( $geny_project->getAllProjects() as $proj ) {
								if( $daily_rate->project_id == $proj->id ) {
									$project = $proj->name;
								}
							}

							foreach( $geny_task->getAllTasks() as $tsk ) {
								if( $daily_rate->task_id == $tsk->id ) {
									$task = $tsk->name;
								}
							}

							$prof_scr_name = '';
							foreach( $geny_profile->getAllProfiles() as $prof ) {
								if( $daily_rate->profile_id == $prof->id ) {
									if( $prof->firstname && $prof->lastname ) {
										$prof_scr_name = $prof->firstname.' '.$prof->lastname;
									}
									else {
										$prof_scr_name = $prof->login;
									}
									break;
								}
							}

							if( $daily_rate_id == $daily_rate->id ) {
								$concat1 = "<option value=\"".$daily_rate->id."\" selected>";
							}
							else {
								$concat1 = "<option value=\"".$daily_rate->id."\">";
							}
							if( $prof_scr_name != '' ) {
								$concat2 = $project.' - '.$task.' - '.$prof_scr_name.' - du '.$daily_rate->start_date.' au '.$daily_rate->end_date."</option>\n";
							}
							else {
								$concat2 = $project.' - '.$task.' - du '.$daily_rate->start_date.' au '.$daily_rate->end_date."</option>\n";
							}
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

						if( $geny_daily_rate->id < 0 ) {
							$geny_daily_rate->loadDailyRateById( $daily_rates[0]->id );
						}
					?>
				</select>
			</p>
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression du TJM. <strong>La suppression est définitive et ne pourra pas être annulée.</strong>
			</p>
			<p>
				<input type="submit" value="Supprimer" /> ou <a href="loader.php?module=daily_rate_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/daily_rate_list.dock.widget.php','backend/widgets/daily_rate_add.dock.widget.php');
?>

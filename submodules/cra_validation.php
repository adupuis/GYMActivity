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
date_default_timezone_set('Europe/Paris');
$gritter_notifications = array();

function processDaysList( $list_as_string ){
	$dates = array();
	foreach( explode(",",$list_as_string) as $date ){
		$tmp_date = explode("-", $date);
		if( checkdate( $tmp_date[1],$tmp_date[2],$tmp_date[0] ) ){
			// Il faut vérifier que le jour est un jour ouvré !
			$time = strtotime($date);
			$tmp_t = GenyTools::getWorkedDaysList($time, $time );
			// Si il y a plus d'un jour dans ce tableau... bah capu.
			if( count($tmp_t) == 1 ){
				$dates[] = $tmp_t[0];
			}
		}
	}
	return $dates;
}

if(isset($_POST['create_cra']) && $_POST['create_cra'] == "true" ){
	$html_worked_days_table = '';
	if( isset($_POST['date_selection_type']) && ( ($_POST['date_selection_type'] == "interval" && isset($_POST['assignement_start_date']) && isset($_POST['assignement_end_date'])) || ($_POST['date_selection_type'] == "days_list" && isset($_POST['assignement_date_list']) ) ) && isset($_POST['assignement_id']) && isset($_POST['task_id']) && isset($_POST['assignement_load']) && isset($_POST['task_id']) ){
		$time_assignement_start_date = 0;
		$time_assignement_end_date = 0;
		$time_assignement_days_list = array();
		if( $_POST['date_selection_type'] == "interval" ){
			$time_assignement_start_date = strtotime($_POST['assignement_start_date']);
			$time_assignement_end_date = strtotime($_POST['assignement_end_date']);
		}
		else if( $_POST['date_selection_type'] == "days_list" ){
			$time_assignement_days_list = processDaysList( $_POST['assignement_date_list'] );
		}
		$tmp_input_ga = new GenyAssignement( $_POST['assignement_id'] );
		$tmp_project = new GenyProject( $tmp_input_ga->project_id );
		$time_project_start_date = strtotime( $tmp_project->start_date );
		$time_project_end_date = strtotime( $tmp_project->end_date );
		if( $tmp_project->status_id == 2 || $tmp_project->status_id == 3 || $tmp_project->status_id == 8 ){ // Projet dans le status en pause, perdu ou fermé.
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"il n'est pas possible de remplir un rapport d'activité pour ce projet car il est soit fermé, soit en pause, soit perdu.");
		}
		else if( ($_POST['date_selection_type'] == "interval" && $time_assignement_start_date >= $time_project_start_date && $time_assignement_end_date <= $time_project_end_date) || ( $_POST['date_selection_type'] == "days_list" && count($time_assignement_days_list) > 0 ) ){
			$ok_count=0;
			$list = array();
			if( $_POST['date_selection_type'] == "interval" ){
				$list = GenyTools::getWorkedDaysList($time_assignement_start_date, $time_assignement_end_date );
			}
			else if( $_POST['date_selection_type'] == "days_list" ){
				// Fixe le problème de la vérification des dates projet dans le cas de liste de jours.
// 				$list = $time_assignement_days_list;
				foreach($time_assignement_days_list as $day){
					if( strtotime($day) >= $time_project_start_date && strtotime($day) <= $time_project_end_date ){
						$list[] = $day;
					}
					else{
						$gritter_notifications[] = array('status'=>'warning', 'title' => 'Erreur','msg'=>"le $day est en dehors des bornes temporelles du projet (du $time_project_start_date au $time_project_end_date).");
					}
				}
			}
			$created_reports = 0;
			foreach( $list as $day ){
				$geny_activity = new GenyActivity();
				$geny_ar = new GenyActivityReport();
				$day_load = $geny_ar->getDayLoad($profile->id,$day)+$_POST['assignement_load'];
				$create_report = false;
				if($day_load <= 8){
					$create_report = true;
				}
				else{
					if( $day_load > 12 ){
						$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur ','msg'=>"Le $day, vous déclaré plus de 12 heures journalière (maximum d'heures par jour : 8h + 4h sup.).");
					}
					else{
						$day_work_load_by_assignement = $geny_ar->getDayLoadByAssignement($profile->id,$day);
						// TODO: gérer la vérification de l'autorisation des heures sup. Il faut vérifier que l'assignement en cours autorise les heures sup puis vérifier la sommes des heures travaillés (<= 8 + 4 heures sup au maximum).
						// Quand nous sommes ici le total des heures travaillés H est compris obligatoirement entre 8h et 12h (8 > H < 12).
						$extra = '';
						if( $tmp_input_ga->overtime_allowed )
							$create_report = true;
						else{
							for($k=0; $k < count($day_work_load_by_assignement); $k++ ){
								$extra .= $day_work_load_by_assignement[$k]['activity_date']."|".$day_work_load_by_assignement[$k]['sum_activity_load']."|".$day_work_load_by_assignement[$k]['assignement_id'].",";
								$tmp_ga = new GenyAssignement( $day_work_load_by_assignement[$k]['assignement_id'] );
								// Ici nous ne nous soucions que de savoir si les heures supplémentaires sont autorisées sur une des assignations (soit une assignation dans la base ou l'assignation en cours de traitement les autorise).
								// Si ce n'est pas le cas aucun rapport ne sera créé, autrement nous créons un rapport.
								// Dans le process une heure supplémentaire doit être entrée avec la tâche spéciale 'Heures supplémentaires', la validation est ensuite humaine.
								if( $tmp_ga->overtime_allowed ){
									$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Heures supplémentaires autorisées sur l'assignation $day_work_load_by_assignement[$k]['assignement_id'].");
									$create_report = true;
								}
							}
						}
						if( !$create_report )
							$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur ','msg'=>"Le $day, vous déclaré plus de 8 heures journalière et vous n'êtes pas autorisé à saisir des heures supplémentaires ($extra).");
					}
				}
				if( $create_report ){
					$ass_id = $_POST['assignement_id'];
					$tmp_ass = new GenyAssignement();
					$tmp_ass->loadAssignementById($ass_id);
					if($tmp_ass->profile_id == $profile->id) {
						#TODO check if task is consistent with assignement
						$geny_activity_id = $geny_activity->insertNewActivity('NULL',$day,$_POST['assignement_load'],date('Y-m-j'),$ass_id,$_POST['task_id']);
						if( $geny_activity_id > -1 ){
							$geny_ars = new GenyActivityReportStatus();
							$geny_ars->loadActivityReportStatusByShortName('P_USER_VALIDATION');
							$geny_ar_id = $geny_ar->insertNewActivityReport('NULL',-1,$geny_activity_id,$profile->id,$geny_ars->id );
							if( $geny_ar_id > -1 )
								$created_reports++;
							else
								$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'enregistrement du rapport du $day.");
							$ok_count++;
						}
						else {
							$geny_activity->deleteActivity($geny_activity_id);
							$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout d'une activité pour le $day.");
						}
					}
				}
			}
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$created_reports rapport(s) enregistré(s) (en attente de validation utilisateur).<br/><strong>N'oubliez pas de les valider.</strong>");
// 			WARNING: Ne surtout pas envoyer de notification à la création !!
// 			if($ok_count > 0){
// 				$notif = new GenyNotification();
// 				// Notification des admins
// 				$notif->insertNewGroupNotification(1,"$screen_name viens de créer $ok_count rapport(s) d'activité, merci de faire le nécessaire.");
// 				// Notification des superusers
// 				$notif->insertNewGroupNotification(2,"$screen_name viens de créer $ok_count rapport(s) d'activité, merci de faire le nécessaire.");
// 			}
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Au moins une des dates saisies est en dehors des bornes du projet.");
		}
	}
	else{
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur ','msg'=>"certaines informations sont manquantes.");
	}
}
else if(isset($_POST['cra_action']) && ($_POST['cra_action'] == "validate_cra" || $_POST['cra_action'] == "delete_cra") ){
	if( $_POST['cra_action'] == "validate_cra" ){
		if( isset( $_POST['activity_report_id'] ) ){
			$tmp_ars = new GenyActivityReportStatus();
			$tmp_ars->loadActivityReportStatusByShortName('P_APPROVAL');
			$ok_count=0;
			foreach( $_POST['activity_report_id'] as $tmp_ar_id ){
				$tmp_ass = new GenyActivityReport( $tmp_ar_id );
				if( $tmp_ass->profile_id == $profile->id ) {
					$tmp_ass->updateInt('activity_report_status_id',$tmp_ars->id);
					if($tmp_ass->commitUpdates()){
						$ok_count++;
					}
					else{
						$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur ','msg'=>"impossible de valider le rapport ".$tmp_ass->id.".");
					}
				}
			}
			if($ok_count > 0 ){
				$notif = new GenyNotification();
				// Notification des admins
				$notif->insertNewGroupNotification(1,"$screen_name viens de créer $ok_count rapport(s) d'activité, merci de faire le nécessaire.");
				// Notification des superusers
				$notif->insertNewGroupNotification(2,"$screen_name viens de créer $ok_count rapport(s) d'activité, merci de faire le nécessaire.");
				if($ok_count == 1)
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count rapport est désormais en attente de validation par le management.");
				else
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count rapports sont désormais en attente de validation par le management.");
			}
		}
	}
	else if( $_POST['cra_action'] == "delete_cra" ){
		if( isset( $_POST['activity_report_id'] ) ){
			$ok_count=0;
			$tmp_activity = new GenyActivity();
			foreach( $_POST['activity_report_id'] as $tmp_ar_id ){
				$tmp_ar = new GenyActivityReport($tmp_ar_id);
				if($tmp_ar->profile_id == $profile->id) {
					if($tmp_activity->deleteActivity($tmp_ar->activity_id) == 1){
						$ok_count++;
					}
					else{
						$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur ','msg'=>"impossible de supprimer le rapport ".$tmp_ar_id.".");
					}
				}
			}
			if($ok_count > 0 ){
				$notif = new GenyNotification();
				// Notification des admins
				$notif->insertNewGroupNotification(1,"$screen_name viens de supprimer $ok_count rapport(s) d'activité non validé(s).");
				// Notification des superusers
				$notif->insertNewGroupNotification(2,"$screen_name viens de supprimer $ok_count rapport(s) d'activité non validé(s).");
				if($ok_count == 1)
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count rapport a été correctement supprimé.");
				else
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count rapports ont été correctement supprimés.");
			}
		}
	}
}
else if(isset($_POST['validate_cra']) && $_POST['validate_cra'] == "true"){
	if( isset( $_POST['activity_report_id'] ) ){
		$tmp_ars = new GenyActivityReportStatus();
		$tmp_ars->loadActivityReportStatusByShortName('P_APPROVAL');
		$ok_count=0;
		foreach( $_POST['activity_report_id'] as $tmp_ar_id ){
			$tmp_ass = new GenyActivityReport( $tmp_ar_id );
			if($tmp_ass->profile_id == $profile->id) {
				$tmp_ass->updateInt('activity_report_status_id',$tmp_ars->id);
				if($tmp_ass->commitUpdates()){
					$ok_count++;
				}
				else{
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur ','msg'=>"impossible de valider le rapport ".$tmp_ass->id.".");
				}
			}
		}
		if($ok_count > 0 ){
			$notif = new GenyNotification();
			// Notification des admins
			$notif->insertNewGroupNotification(1,"$screen_name viens de créer $ok_count rapport(s) d'activité, merci de faire le nécessaire.");
			// Notification des superusers
			$notif->insertNewGroupNotification(2,"$screen_name viens de créer $ok_count rapport(s) d'activité, merci de faire le nécessaire.");
			if($ok_count == 1)
				$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count rapport est désormais en attente de validation par le management.");
			else
				$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count rapports sont désormais en attente de validation par le management.");
		}
	}
}

$data_array = array();
$data_array_filters = array( 2 => array(), 3 => array(), 6 => array() );
$geny_ar = new GenyActivityReport();
$geny_ars = new GenyActivityReportStatus();
$geny_ars->loadActivityReportStatusByShortName('P_USER_VALIDATION');
foreach( $geny_ar->getActivityReportsListWithRestrictions( array("activity_report_status_id=".$geny_ars->id,"profile_id=".$profile->id) ) as $ar ){
	$tmp_activity = new GenyActivity( $ar->activity_id );
	$tmp_ars = new GenyActivityReportStatus( $ar->status_id );
	$tmp_task = new GenyTask( $tmp_activity->task_id );
	$tmp_assignement = new GenyAssignement( $tmp_activity->assignement_id );
	$tmp_project = new GenyProject( $tmp_assignement->project_id );
	if( $tmp_project->type_id != 5 ){
		$data_array[] = array( $ar->id, $tmp_activity->activity_date, $tmp_project->name, $tmp_task->name, $tmp_activity->load, $geny_ar->getDayLoad($profile->id,$tmp_activity->activity_date), GenyTools::getActivityReportStatusAsColoredHtml($geny_ars) );
		
		if( ! in_array($tmp_project->name,$data_array_filters[2]) )
			$data_array_filters[2][] = $tmp_project->name;
		if( ! in_array($tmp_task->name,$data_array_filters[3]) )
			$data_array_filters[3][] = $tmp_task->name;
		if( ! in_array($geny_ars->name,$data_array_filters[6]) )
			$data_array_filters[6][] = $geny_ars->name;
	}
}

?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/cra_add.png"></img>
		<span class="cra_add">
			Valider des CRA
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de valider des rapports d'activité.<br />
		<strong class="important_note">Important :</strong> Ce formulaire contient tous les rapports que vous n'avez pas validé (y compris les plus anciens que vous n'auriez pas soumis).<br />
		</p>
		<script>
			var indexData = new Array();
			<?php
				if(array_key_exists("GYMActivity_cra_validation_table_loader_php", $_COOKIE)){
					$cookie = json_decode($_COOKIE["GYMActivity_cra_validation_table_loader_php"]);
				}
				
				$data_array_filters_html = array();
				foreach( $data_array_filters as $idx => $data ){
					$data_array_filters_html[$idx] = '<select><option value=""></option>';
					foreach( $data as $d ){
						if( isset($cookie) && htmlspecialchars_decode(urldecode($cookie->aaSearchCols[$idx][0]),ENT_QUOTES) == htmlspecialchars_decode($d,ENT_QUOTES) )
							$data_array_filters_html[$idx] .= '<option selected="selected" value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';
						else
							$data_array_filters_html[$idx] .= '<option value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';
					}
					$data_array_filters_html[$idx] .= '</select>';
				}
				foreach( $data_array_filters_html as $idx => $html ){
					echo "indexData[$idx] = '$html';\n";
				}
			?>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
				
				var oTable = $('#cra_validation_table').dataTable( {
					"bDeferRender": true,
					"bJQueryUI": true,
					"bStateSave": true,
					"bAutoWidth": false,
					"sCookiePrefix": "GYMActivity_",
					"iCookieDuration": 60*60*24*365, // 1 year
					"sPaginationType": "full_numbers",
					"oLanguage": {
						"sSearch": "Recherche :",
						"sLengthMenu": "Rapport par page _MENU_",
						"sZeroRecords": "Aucun résultat",
						"sInfo": "Aff. _START_ à _END_ de _TOTAL_ enregistrements",
						"sInfoEmpty": "Aff. 0 à 0 de 0 enregistrements",
						"sInfoFiltered": "(filtré de _MAX_ enregistrements)",
						"oPaginate":{ 
							"sFirst":"Début",
							"sLast": "Fin",
							"sNext": "Suivant",
							"sPrevious": "Précédent"
						}
					}
				} );
				/* Add a select menu for each TH element in the table footer */
				/* i+1 is to avoid the first row wich contains a <input> tag without any informations */
				$("tfoot th").each( function ( i ) {
					if( i == 2 || i == 3 || i == 6){
						this.innerHTML = indexData[i];
						$('select', this).change( function () {
							oTable.fnFilter( $(this).val(), i );
						} );
					}
				} );
			
			});
			$(function() {
				$( "#radio" ).buttonset();
				$( "#chkBoxSelectAll" ).button();
			});
		</script>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
		</script>
		<form id="formID" action="loader.php?module=cra_validation" method="post" class="table_container">
			<style>
				@import 'styles/<?php echo $web_config->theme ?>/cra_validation.css';
			</style>
			<ul>
				<li>
					<input type="checkbox" id="chkBoxSelectAll" onClick="toggleCheck('#cra_validation_table')" /><label for="chkBoxSelectAll"> Tout (dé)séléctionner</label>
				</li>
				<li id="radio">
					<input type="radio" id="radio1" name="cra_action" value="validate_cra" /><label for="radio1">Valider</label>
					<input type="radio" id="radio2" name="cra_action" value="delete_cra" /><label for="radio2">Supprimer</label>
				</li>
			</ul>
			<p>
				<table id="cra_validation_table" style="color: black; width: 100%;">
					<thead>
						<th>Sel.</th>
						<th>Date</th>
						<th>Projet</th>
						<th>Tâche</th>
						<th>Charge (tâche)</th>
						<th>Charge (total jour)</th>
						<th>Status</th>
					</thead>
					<tbody>
					<?php
						foreach( $data_array as $da ){
							echo "<tr><td><input type='checkbox' name='activity_report_id[]' value=".$da[0]." /></td><td>".$da[1]."</td><td>".$da[2]."</td><td>".$da[3]."</td><td>".$da[4]."</td><td>".$da[5]."</td><td>".$da[6]."</td></tr>";
						}
					?>
					</tbody>
					<tfoot>
						<th>Sel.</th>
						<th class="filtered">Date</th>
						<th class="filtered">Projet</th>
						<th class="filtered">Tâche</th>
						<th class="filtered">Charge (tâche)</th>
						<th class="filtered">Charge (total jour)</th>
						<th class="filtered">Status</th>
					</tfoot>
				</table>
			</p>
			<p>
				<input type="submit" value="Appliquer" /> ou <a href="loader.php?module=cra_list">annuler</a>
			</p>
		</form>
	</p>
</div>

<?php
	$bottomdock_items = array('backend/widgets/cra_add.dock.widget.php','backend/widgets/cra_list.dock.widget.php');
?>

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

if(isset($_POST['cra_action']) && ($_POST['cra_action'] == "validate_cra" || $_POST['cra_action'] == "delete_cra" || $_POST['cra_action'] == "user_validate_cra" || $_POST['cra_action'] == "bill_cra" || $_POST['cra_action'] == "pay_cra" || $_POST['cra_action'] == "close_cra" || $_POST['cra_action'] == "deletion_cra" || $_POST['cra_action'] == "refuse_cra" ) ){
	if( $_POST['cra_action'] == "validate_cra" ){
		if( isset( $_POST['activity_report_id'] ) ){
			$tmp_ars = new GenyActivityReportStatus();
			$tmp_ars->loadActivityReportStatusByShortName('APPROVED');
			$ok_count=0;
			$count_by_profile = array();
			foreach( $_POST['activity_report_id'] as $tmp_ar_id ){
				$tmp_ass = new GenyActivityReport( $tmp_ar_id );
				$tmp_ass->updateInt('activity_report_status_id',$tmp_ars->id);
				if($tmp_ass->commitUpdates()){
					$ok_count++;
					$tmp_activity = new GenyActivity( $tmp_ar_id );
					$tmp_assignement = new GenyAssignement( $tmp_activity->assignement_id );
					$tmp_project = new GenyProject( $tmp_assignement->project_id );
					if(isset($count_by_profile[$tmp_ass->profile_id])){
						if( $tmp_project->type_id == 5 )
							$count_by_profile[$tmp_ass->profile_id]['conges']++;
						else
							$count_by_profile[$tmp_ass->profile_id]['cra']++;
					}
					else{
						$count_by_profile[$tmp_ass->profile_id]= array('cra' => 0, 'conges' => 0);
						if( $tmp_project->type_id == 5 )
							$count_by_profile[$tmp_ass->profile_id]['conges']++;
						else
							$count_by_profile[$tmp_ass->profile_id]['cra']++;
					}
				}
				else{
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur ','msg'=>"impossible de valider le rapport ".$tmp_ass->id.".");
				}
			}
			if($ok_count > 0 ){
				$notif = new GenyNotification();
				foreach ($count_by_profile as $id => $value){
					if( $value['conges'] > 0 )
						$notif->insertNewNotification($id,"Vos ".$value['conges']." jour(s) de congés viennent d'être acceptés.","ok");
					if( $value['cra'] > 0 )
						$notif->insertNewNotification($id,"Vos ".$value['cra']." rapport(s) d'activité ont été validés.","ok");
				}
				if($ok_count == 1)
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Le rapport a été correctement validé.");
				else
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count rapports correctement validés.");
			}
		}
	}
	else if( $_POST['cra_action'] == "delete_cra" ){
		if( isset( $_POST['activity_report_id'] ) ){
			$ok_count=0;
			$tmp_ar = new GenyActivityReport();
			$count_by_profile = array();
			foreach( $_POST['activity_report_id'] as $tmp_ar_id ){
				$tmp_ar->loadActivityReportById($tmp_ar_id);
				if( ! isset($count_by_profile[$tmp_ar->profile_id]) )
					$count_by_profile[$tmp_ar->profile_id]=0;
				if($tmp_ar->deleteActivityReport($tmp_ar_id) == 1){
					$ok_count++;
					$count_by_profile[$tmp_ar->profile_id]++;
				}
				else{
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur ','msg'=>"impossible de supprimer le rapport ".$tmp_ar_id.".");
				}
			}
			if($ok_count > 0 ){
				$notif = new GenyNotification();
				// Notification des users
				foreach( $count_by_profile as $id => $total ){
					if($total == 1)
						$notif->insertNewNotification($id,"$total rapport d'activité a été supprimé par un manager.","warning");
					else if($total > 1)
						$notif->insertNewNotification($id,"$total rapports d'activité ont été supprimés par un manager.","warning");
				}
				if($ok_count == 1)
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count rapport a été correctement supprimé.");
				else
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count rapports ont été correctement supprimés.");
			}
		}
	}
	else if( $_POST['cra_action'] == "user_validate_cra" ){
		if( isset( $_POST['activity_report_id'] ) ){
			$tmp_ars = new GenyActivityReportStatus();
			$tmp_ars->loadActivityReportStatusByShortName('P_USER_VALIDATION');
			$ok_count=0;
			$count_by_profile = array();
			foreach( $_POST['activity_report_id'] as $tmp_ar_id ){
				$tmp_ass = new GenyActivityReport( $tmp_ar_id );
				$tmp_ass->updateInt('activity_report_status_id',$tmp_ars->id);
				if($tmp_ass->commitUpdates()){
					$ok_count++;
					$tmp_activity = new GenyActivity( $tmp_ar_id );
					$tmp_assignement = new GenyAssignement( $tmp_activity->assignement_id );
					$tmp_project = new GenyProject( $tmp_assignement->project_id );
					if(isset($count_by_profile[$tmp_ass->profile_id])){
						if( $tmp_project->type_id == 5 )
							$count_by_profile[$tmp_ass->profile_id]['conges']++;
						else
							$count_by_profile[$tmp_ass->profile_id]['cra']++;
					}
					else{
						$count_by_profile[$tmp_ass->profile_id]= array('cra' => 0, 'conges' => 0);
						if( $tmp_project->type_id == 5 )
							$count_by_profile[$tmp_ass->profile_id]['conges']++;
						else
							$count_by_profile[$tmp_ass->profile_id]['cra']++;
					}
				}
				else{
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur ','msg'=>"impossible de renvoyer le rapport ".$tmp_ass->id." en validation utilisateur.");
				}
			}
			if($ok_count > 0 ){
				$notif = new GenyNotification();
				foreach ($count_by_profile as $id => $value){
					if( $value['conges'] > 0 )
						$notif->insertNewNotification($id,"Vos ".$value['conges']." jour(s) de congés viennent d'être renvoyés à votre validation.","nok");
					if( $value['cra'] > 0 )
						$notif->insertNewNotification($id,"Vos ".$value['cra']." rapport(s) d'activité ont été renvoyés à votre validation.","nok");
				}
				if($ok_count == 1)
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Le rapport a été correctement renvoyé en validation utilisateur.");
				else
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count rapports correctement renvoyés en validation utilisateur.");
			}
		}
	}
	else if( $_POST['cra_action'] == "refuse_cra" ){
		if( isset( $_POST['activity_report_id'] ) ){
			$tmp_ars = new GenyActivityReportStatus();
			$tmp_ars->loadActivityReportStatusByShortName('REFUSED');
			$ok_count=0;
			$count_by_profile = array();
			foreach( $_POST['activity_report_id'] as $tmp_ar_id ){
				$tmp_ass = new GenyActivityReport( $tmp_ar_id );
				$tmp_ass->updateInt('activity_report_status_id',$tmp_ars->id);
				if($tmp_ass->commitUpdates()){
					$ok_count++;
					$tmp_activity = new GenyActivity( $tmp_ar_id );
					$tmp_assignement = new GenyAssignement( $tmp_activity->assignement_id );
					$tmp_project = new GenyProject( $tmp_assignement->project_id );
					if(isset($count_by_profile[$tmp_ass->profile_id])){
						if( $tmp_project->type_id == 5 )
							$count_by_profile[$tmp_ass->profile_id]['conges']++;
						else
							$count_by_profile[$tmp_ass->profile_id]['cra']++;
					}
					else{
						$count_by_profile[$tmp_ass->profile_id]= array('cra' => 0, 'conges' => 0);
						if( $tmp_project->type_id == 5 )
							$count_by_profile[$tmp_ass->profile_id]['conges']++;
						else
							$count_by_profile[$tmp_ass->profile_id]['cra']++;
					}
				}
				else{
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur ','msg'=>"impossible de refuser le rapport ".$tmp_ass->id." à l'utilisateur.");
				}
			}
			if($ok_count > 0 ){
				$notif = new GenyNotification();
				foreach ($count_by_profile as $id => $value){
					if( $value['conges'] > 0 )
						$notif->insertNewNotification($id,"Vos ".$value['conges']." jour(s) de congés viennent d'être refusés.","nok");
					if( $value['cra'] > 0 )
						$notif->insertNewNotification($id,"Vos ".$value['cra']." rapport(s) d'activité ont été refusés.","nok");
				}
				if($ok_count == 1)
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Le rapport a été correctement refusé.");
				else
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count rapports correctement refusés.");
			}
		}
	}
}

$data_array = array();
$data_array_filters = array( 1 => array(), 3 => array(), 4 => array(), 7 => array() );
$geny_ar = new GenyActivityReport();
$geny_ars = new GenyActivityReportStatus();
$geny_ars->loadActivityReportStatusByShortName('P_APPROVAL');

$tmp_activity = new GenyActivity();
$tmp_ars = new GenyActivityReportStatus();
$tmp_task = new GenyTask();
$tmp_assignement = new GenyAssignement();
$tmp_project = new GenyProject();
$tmp_profile = new GenyProfile();

foreach( $geny_ar->getActivityReportsByReportStatusId( $geny_ars->id ) as $ar ){
	$tmp_activity->loadActivityById( $ar->activity_id );
	$tmp_ars->loadActivityReportStatusById( $ar->status_id );
	$tmp_task->loadTaskById( $tmp_activity->task_id );
	$tmp_assignement->loadAssignementById( $tmp_activity->assignement_id );
	$tmp_project->loadProjectById( $tmp_assignement->project_id );
	$tmp_profile->loadProfileById( $tmp_assignement->profile_id );
	$tmp_profile_name = GenyTools::getProfileDisplayName($tmp_profile);
	$data_array[] = array( $ar->id, $tmp_profile_name, $tmp_activity->activity_date, $tmp_project->name, $tmp_task->name, $tmp_activity->load, $geny_ar->getDayLoad($tmp_profile->id,$tmp_activity->activity_date), GenyTools::getActivityReportStatusAsColoredHtml($geny_ars) );
	
	if( ! in_array($tmp_profile_name,$data_array_filters[1]) )
		$data_array_filters[1][] = $tmp_profile_name;
	if( ! in_array($tmp_project->name,$data_array_filters[3]) )
		$data_array_filters[3][] = $tmp_project->name;
	if( ! in_array($tmp_task->name,$data_array_filters[4]) )
		$data_array_filters[4][] = $tmp_task->name;
	if( ! in_array($geny_ars->name,$data_array_filters[7]) )
		$data_array_filters[7][] = $geny_ars->name;
}

?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/cra_admin_generic.png"></img>
		<span class="cra_admin_generic">
			Validation d'activité
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
				if(array_key_exists("GYMActivity_cra_validation_admin_table_loader_php", $_COOKIE)){
					$cookie = json_decode($_COOKIE["GYMActivity_cra_validation_admin_table_loader_php"]);
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
				
				var oTable = $('#cra_validation_admin_table').dataTable( {
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
					if( i == 1 || i == 3 || i  == 4 || i == 7){
						this.innerHTML = indexData[i];
						$('select', this).change( function () {
							oTable.fnFilter( $(this).val(), i );
						} );
					}
				} );
				
			});
			
			function onCheckBoxSelectAll(){
				$("#cra_validation_admin_table").find(':checkbox').attr('checked', $('#chkBoxSelectAll').attr('checked'));
			}
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
		<style>
			@import 'styles/<?php echo $web_config->theme ?>/cra_validation_admin.css';
		</style>
		<form id="formID" action="loader.php?module=cra_validation_admin" method="post" class="table_container">
<!-- 			<input type="hidden" name="validate_cra" value="true" /> -->
			<ul style="display: inline; color: black;">
				<li>
					<input type="checkbox" id="chkBoxSelectAll" onClick="onCheckBoxSelectAll()" /><label for="chkBoxSelectAll"> Tout (dé)séléctionner</label>
				</li>
				<li id="radio">
					<input type="radio" id="radio0" name="cra_action" value="user_validate_cra" /><label for="radio0">Validation utilisateur</label>
					<input type="radio" id="radio1" name="cra_action" value="validate_cra" /><label for="radio1">Validé</label>
					<input type="radio" id="radio6" name="cra_action" value="delete_cra" /><label for="radio6">Supprimer</label>
					<input type="radio" id="radio7" name="cra_action" value="refuse_cra" /><label for="radio7">Refusé</label>
				</li>
			</ul>
			<p>
				<table id="cra_validation_admin_table" style="color: black; width: 100%;">
					<thead>
						<th>Sel.</th>
						<th>Collab.</th>
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
							echo "<tr><td><input type='checkbox' name='activity_report_id[]' value=".$da[0]." /></td><td>".$da[1]."</td><td class='centered'>".$da[2]."</td><td class='centered'>".$da[3]."</td><td class='centered'>".$da[4]."</td><td class='centered'>".$da[5]."</td><td class='centered'>".$da[6]."</td><td>".$da[7]."</td></tr>";
						}
					?>
					</tbody>
					<tfoot>
						<th>Sel.</th>
						<th>Collab.</th>
						<th>Date</th>
						<th>Projet</th>
						<th>Tâche</th>
						<th>Charge (tâche)</th>
						<th>Charge (total jour)</th>
						<th>Status</th>
					</tfoot>
				</table>
			</p>
			<p id="extra_info">
			</p>
			<p>
				<input type="submit" value="Appliquer" /> ou <a href="loader.php?module=home_cra">annuler</a>
			</p>
		</form>
	</p>
</div>

<?php
	$bottomdock_items = array('backend/widgets/cra_post_validation_workflow.dock.widget.php');
?>

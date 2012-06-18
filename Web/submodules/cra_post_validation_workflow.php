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
date_default_timezone_set('Europe/Paris');
$gritter_notifications = array();

if(isset($_POST['cra_action']) && ($_POST['cra_action'] == "delete_cra" || $_POST['cra_action'] == "bill_cra" || $_POST['cra_action'] == "pay_cra" || $_POST['cra_action'] == "close_cra" || $_POST['cra_action'] == "deletion_cra" ) ){
	if( isset( $_POST['activity_report_id'] ) ){
		// L'état initial dans lequel le CRA est censé être
		$init_ars = new GenyActivityReportStatus();
		
		// Le nouvel état dans lequel le CRA est censé être après opérations
		$new_ars = new GenyActivityReportStatus();
		
		if( $_POST['cra_action'] == "bill_cra" ){
			$init_ars->loadActivityReportStatusByShortName('APPROVED');
			$new_ars->loadActivityReportStatusByShortName('BILLED');
		}
		else if( $_POST['cra_action'] == "pay_cra" ){
			$init_ars->loadActivityReportStatusByShortName('BILLED');
			$new_ars->loadActivityReportStatusByShortName('PAID');
		}
		else if( $_POST['cra_action'] == "close_cra" ){
			$init_ars->loadActivityReportStatusByShortName('PAID');
			$new_ars->loadActivityReportStatusByShortName('CLOSE');
		}
		else if( $_POST['cra_action'] == "deletion_cra" ){
			$init_ars->loadActivityReportStatusByShortName('APPROVED');
			$new_ars->loadActivityReportStatusByShortName('P_REMOVAL');
		}
		else if( $_POST['cra_action'] == "delete_cra" ){
			$init_ars->loadActivityReportStatusByShortName('P_REMOVAL');
			$new_ars->loadActivityReportStatusByShortName('REMOVED');
		}
		$ok_count=0;
		$count_by_project = array();
		$count_by_profile = array();
		foreach( $_POST['activity_report_id'] as $tmp_ar_id ){
			$tmp_ass = new GenyActivityReport( $tmp_ar_id );
			if( $tmp_ass->status_id == $init_ars->id ){
				$tmp_ass->updateInt('activity_report_status_id',$new_ars->id);
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
					if(isset($count_by_project[$tmp_assignement->project_id])){
						$count_by_project[$tmp_assignement->project_id]++;
					}
					else{
						$count_by_project[$tmp_assignement->project_id]= 1;
					}
				}
				else{
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur durant la mise à jour du rapport','msg'=>"Erreur : impossible de passer le rapport ".$tmp_ass->id." au status ".$new_ars->name);
				}
			}
			else {
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur durant la mise à jour du rapport','msg'=>"Erreur : impossible de passer le rapport ".$tmp_ass->id." au status ".$new_ars->name." car son status actuel n'est pas ".$init_ars->name);
			}
		}
		if($ok_count > 0 ){
			$notif = new GenyNotification();
			foreach ($count_by_project as $id => $value){
				$tmp_p = new GenyProject($id);
				$tmp_c = new GenyClient( $tmp_p->client_id );
				// Notification des administrateurs, des SuperUsers et des SuperReporters
				if( $new_ars->shortname == "BILLED" ){
					$notif->insertNewGroupNotification(1,"Notification au groupe Admins: $value rapport(s) ont été facturés à ".$tmp_c->name." sur le projet ".$tmp_p->name,"ok");
					$notif->insertNewGroupNotification(2,"Notification au groupe SuperUsers: $value rapport(s) ont été facturés à ".$tmp_c->name." sur le projet ".$tmp_p->name,"ok");
					$notif->insertNewGroupNotification(4,"Notification au groupe SuperReporters: $value rapport(s) ont été facturés à ".$tmp_c->name." sur le projet ".$tmp_p->name,"ok");
				}
				else if( $new_ars->shortname == "PAID" ){
					$notif->insertNewGroupNotification(1,"Notification au groupe Admins: $value rapport(s) ont été payé(s) par ".$tmp_c->name." à ".$web_config->company_name." sur le projet ".$tmp_p->name,"ok");
					$notif->insertNewGroupNotification(2,"Notification au groupe SuperUsers: $value rapport(s) ont été payé(s) par ".$tmp_c->name." à ".$web_config->company_name." sur le projet ".$tmp_p->name,"ok");
					$notif->insertNewGroupNotification(4,"Notification au groupe SuperReporters: $value rapport(s) ont été payé(s) par ".$tmp_c->name." à ".$web_config->company_name." sur le projet ".$tmp_p->name,"ok");
				}
				else if( $new_ars->shortname == "CLOSE" ){
					$notif->insertNewGroupNotification(1,"Notification au groupe Admins: $value rapport(s) ont été fermé(s) par $screen_name (".$tmp_c->name." / ".$tmp_p->name.")","ok");
					$notif->insertNewGroupNotification(2,"Notification au groupe SuperUsers: $value rapport(s) ont été fermé(s) par $screen_name (".$tmp_c->name." / ".$tmp_p->name.")","ok");
					$notif->insertNewGroupNotification(4,"Notification au groupe SuperReporters: $value rapport(s) ont été fermé(s) par $screen_name (".$tmp_c->name." / ".$tmp_p->name.")","ok");
				}
				else if( $new_ars->shortname == "REMOVED" ){
					$notif->insertNewGroupNotification(1,"Notification au groupe Admins: $value rapport(s) ont été supprimé(s) par $screen_name (".$tmp_c->name." / ".$tmp_p->name.")","ok");
					$notif->insertNewGroupNotification(2,"Notification au groupe SuperUsers: $value rapport(s) ont été supprimé(s) par $screen_name (".$tmp_c->name." / ".$tmp_p->name.")","ok");
					$notif->insertNewGroupNotification(4,"Notification au groupe SuperReporters: $value rapport(s) ont été supprimé(s) par $screen_name (".$tmp_c->name." / ".$tmp_p->name.")","ok");
				}
				// TODO: il reste à gérer les notifications pour les status P_REMOVAL.
			}
			foreach ($count_by_profile as $id => $value){
				if( $new_ars->shortname == "REMOVED" ){
					if( $value['conges'] > 0 )
						$notif->insertNewNotification($id,"Vos ".$value['conges']." jour(s) de congés viennent d'être supprimés par votre manager.","warning");
					if( $value['cra'] > 0 )
						$notif->insertNewNotification($id,"Vos ".$value['cra']." rapport(s) d'activité viennent d'être supprimés par votre manager.","warning");
				}
			}
			if($ok_count == 1){
				$gritter_notifications[] = array('status'=>'success', 'title' => 'Rapport mis à jour avec succès','msg'=>"Le rapport a été correctement passé au status ".$new_ars->name);
			}
			else{
				$gritter_notifications[] = array('status'=>'success', 'title' => 'Rapports mis à jour avec succès','msg'=>"$ok_count rapports ont été correctement passés au status ".$new_ars->name);
			}
		}
	}
	
}

// Now we create an array that contains all data that will be displayed in our table
$data_array = array();
// We also create a table that contains the filters data (but only for required data).
$data_array_filters = array( 1 => array(), 3 => array(), 4 => array(),5 => array(), 8 => array());
$geny_ar = new GenyActivityReport();
$geny_ars = new GenyActivityReportStatus();
$geny_ars_approval = new GenyActivityReportStatus();
$geny_ars_approval->loadActivityReportStatusByShortName('P_APPROVAL');
$geny_ars_user_validation = new GenyActivityReportStatus();
$geny_ars_user_validation->loadActivityReportStatusByShortName('P_USER_VALIDATION');
$geny_client = new GenyClient();
foreach( $geny_ar->getActivityReportsListWithRestrictions( array("activity_report_status_id != ".$geny_ars_approval->id,"activity_report_status_id != ".$geny_ars_user_validation->id) ) as $ar ){
	// Let's use some server load here...
	$tmp_activity = new GenyActivity( $ar->activity_id );
	$geny_ars->loadActivityReportStatusById( $ar->status_id );
	$tmp_task = new GenyTask( $tmp_activity->task_id );
	$tmp_assignement = new GenyAssignement( $tmp_activity->assignement_id );
	$tmp_project = new GenyProject( $tmp_assignement->project_id );
	$tmp_profile = new GenyProfile( $tmp_assignement->profile_id );
	$geny_client->loadClientById($tmp_project->client_id);
	$data_array[] = array( $ar->id,GenyTools::getProfileDisplayName($tmp_profile),$tmp_activity->activity_date,$tmp_project->name,$tmp_task->name,$geny_client->name,$tmp_activity->load,$geny_ar->getDayLoad($tmp_profile->id,$tmp_activity->activity_date),GenyTools::getActivityReportStatusAsColoredHtml($geny_ars) );
	
	if( ! in_array(GenyTools::getProfileDisplayName($tmp_profile),$data_array_filters[1]) )
		$data_array_filters[1][] = GenyTools::getProfileDisplayName($tmp_profile);
	if( ! in_array($tmp_project->name,$data_array_filters[3]) )
		$data_array_filters[3][] = $tmp_project->name;
	if( ! in_array($tmp_task->name,$data_array_filters[4]) )
		$data_array_filters[4][] = $tmp_task->name;
	if( ! in_array($geny_client->name,$data_array_filters[5]) )
		$data_array_filters[5][] = $geny_client->name;
	if( ! in_array($geny_ars->name,$data_array_filters[8]) )
		$data_array_filters[8][] = $geny_ars->name;
}

?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/cra_admin_generic.png"></img>
		<span class="cra_admin_generic">
			Workflow CRA
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de modifier l'état des CRAs dans le workflow.<br />
		<strong class="important_note">Important :</strong> Ce formulaire contient tous les rapports déjà validés (validation utilisateur et management) et affiche leurs états d'avancement dans le workflow.<br />
		</p>
		<script>
			
			var indexData = new Array();
			<?php
				if(array_key_exists("GYMActivity_cra_post_validation_workflow_table_loader_php", $_COOKIE)) {
					$cookie = json_decode($_COOKIE["GYMActivity_cra_post_validation_workflow_table_loader_php"]);
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
				
				var oTable = $('#cra_post_validation_workflow_table').dataTable( {
					"bDeferRender": true,
					"bJQueryUI": true,
					"bStateSave": true,
					"bAutoWidth": false,
					"bProcessing": true,
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
				$("tfoot th").each( function ( i ) {
					if( i == 1 || i == 3 || i  == 4 || i == 5 || i == 8){
						this.innerHTML = indexData[i];
						$('select', this).change( function () {
							oTable.fnFilter( $(this).val(), i );
						} );
					}
				} );
				
				
			});
			
			function onCheckBoxSelectAll(){
				$("#cra_post_validation_workflow_table").find(':checkbox').attr('checked', $('#chkBoxSelectAll').attr('checked'));
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
		<form id="formID" action="loader.php?module=cra_post_validation_workflow" method="post" class="table_container">
<!-- 			<input type="hidden" name="validate_cra" value="true" /> -->
			<ul style="display: inline; color: black;">
				<li>
					<input type="checkbox" id="chkBoxSelectAll" onClick="onCheckBoxSelectAll()" /><label for="chkBoxSelectAll"> Tout (dé)séléctionner</label>
				</li>
				<li id="radio">
					<input type="radio" id="radio2" name="cra_action" value="bill_cra" /><label for="radio2">Facturé</label>
					<input type="radio" id="radio3" name="cra_action" value="pay_cra" /><label for="radio3">Payé</label>
					<input type="radio" id="radio4" name="cra_action" value="close_cra" /><label for="radio4">Fermé</label>
					<input type="radio" id="radio5" name="cra_action" value="deletion_cra" /><label for="radio5">Suppression</label>
					<input type="radio" id="radio6" name="cra_action" value="delete_cra" /><label for="radio6">Supprimé</label>
				</li>
			</ul>
			<p>
				<table id="cra_post_validation_workflow_table" style="color: black; width: 100%;">
					<thead>
						<th>Sel.</th>
						<th>Collab.</th>
						<th>Date</th>
						<th>Projet</th>
						<th>Tâche</th>
						<th>Client</th>
						<th>Charge (tâche)</th>
						<th>Charge (total jour)</th>
						<th>Status</th>
					</thead>
					<tbody>
					<?php
						foreach( $data_array as $da ){
							echo "<tr><td><input type='checkbox' name='activity_report_id[]' value=".$da[0]." /></td><td>".$da[1]."</td><td class='centered'>".$da[2]."</td><td class='centered'>".$da[3]."</td><td class='centered'>".$da[4]."</td><td class='centered'>".$da[5]."</td><td class='centered'>".$da[6]."</td><td class='centered'>".$da[7]."</td><td>".$da[8]."</td></tr>";
						}
					?>
					</tbody>
					<tfoot>
						<th>Sel.</th>
						<th>Collab.</th>
						<th>Date</th>
						<th>Projet</th>
						<th id="task">Tâche</th>
						<th>Client</th>
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


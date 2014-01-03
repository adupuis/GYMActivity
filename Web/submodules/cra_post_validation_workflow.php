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

include_once 'backend/api/ajax_toolbox.php';

// Variable to configure global behaviour
$geny_ptr = new GenyProjectTaskRelation();
date_default_timezone_set('Europe/Paris');
$gritter_notifications = array();

// récupération du paramètre rentré par l'utilisateur (filtre sur l'annee)
$param_year = getParam( 'year', date( "Y" ) );

// si l'année donnée par l'utilisateur est correcte, on initialise $year avec les valeurs de l'utilisateur
if( is_numeric( $param_year )&& strlen( $param_year ) == 4 && intval( $param_year ) > 0 ) {
	$year = intval( $param_year );
}
// sinon par défaut on met le mois et l'année courante
else {
	$year = intval( date( "Y" ) );
}


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


$data_array = array(); // Now we create an array that contains all data that will be displayed in our table
$data_array_filters = array( 1 => array(), 3 => array(), 4 => array(),5 => array(), 8 => array()); // We also create a table that contains the filters data (but only for required data).
$geny_ar = new GenyActivityReport();
$geny_ars = new GenyActivityReportStatus();
$tmp_profile = new GenyProfile( );
$activity_report_workflow = new GenyActivityReportWorkflow();
$status = $geny_ars->getAllActivityReportStatus();
$geny_ars = array();

foreach($status as $s){
	$geny_ars["$s->id"] = $s;
}

$activity_report_workflow->setDebug(true);
$workflow = $activity_report_workflow->getActivityReportsWorkflowFromTo(intval($year)."-01-01", intval($year)."-12-31");
$activity_report_workflow->setDebug(false);

foreach($workflow as $row) {
	
	$tmp_profile->login = $row->profile_login;
	$tmp_profile->lastname = $row->profile_lastname;
	$tmp_profile->firstname = $row->profile_firstname;
	
	$data_array[] = array(  $row->activity_report_id,
				GenyTools::getProfileDisplayName($tmp_profile),
				$row->activity_date,
				$row->project_name,
				$row->task_name,
				$row->client_name,
				$row->activity_load,
				$geny_ar->getDayLoad($row->profile_id,$row->activity_date),
				GenyTools::getActivityReportStatusAsColoredHtml($geny_ars["$row->activity_report_status_id"]) );

	if( ! in_array(GenyTools::getProfileDisplayName($tmp_profile),$data_array_filters[1]) )
	$data_array_filters[1][] = GenyTools::getProfileDisplayName($tmp_profile);
	if( ! in_array($row->project_name,$data_array_filters[3]) )
		$data_array_filters[3][] = $row->project_name;
	if( ! in_array($row->task_name,$data_array_filters[4]) )
		$data_array_filters[4][] = $row->task_name;
	if( ! in_array($row->client_name,$data_array_filters[5]) )
		$data_array_filters[5][] = $row->client_name;
	if( ! in_array($geny_ars["$row->activity_report_status_id"]->name,$data_array_filters[8]) )
		$data_array_filters[8][] = $geny_ars["$row->activity_report_status_id"]->name;
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
		<strong class="important_note">Important :</strong> Ce formulaire ne contient que les rapports depuis le <strong>1er janvier <?php echo $year; ?></strong> au <strong>31 décembre <?php echo $year; ?></strong><br />
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
		
		<form id="select_cra_year" action="loader.php?module=cra_post_validation_workflow" method="post">
			<p>
				<label for="year">Année</label>
				<input name="year" id="year" type="text" value="<?php echo $year;?>"/>
			</p>
			<input type="submit" value="Ajuster le workflow CRA" />
		</form>
		
		<form id="formID" action="loader.php?module=cra_post_validation_workflow" method="post" class="table_container">
<!-- 			<input type="hidden" name="validate_cra" value="true" /> -->
			<input  type="hidden" name="year" id="year" value="<?php echo $year;?>"/>
			<ul style="display: inline; color: black;">
				<li>
				<input type="checkbox" id="chkBoxSelectAll" onClick="toggleCheck('#cra_post_validation_workflow_table')" /><label for="chkBoxSelectAll"> Tout (dé)séléctionner</label>
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


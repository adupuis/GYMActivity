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
$header_title = '%COMPANY_NAME% - Validation congés';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';

$geny_ptr = new GenyProjectTaskRelation();
$geny_tools = new GenyTools();
date_default_timezone_set('Europe/Paris');
$gritter_notifications = array();

if(isset($_POST['create_conges']) && $_POST['create_conges'] == "true" ){
	$html_worked_days_table = '';
	if( isset($_POST['assignement_start_date']) && isset($_POST['assignement_end_date']) && isset($_POST['assignement_id']) && isset($_POST['task_id']) && isset($_POST['assignement_load']) && isset($_POST['task_id']) ){
		$time_assignement_start_date = strtotime($_POST['assignement_start_date']);
		$time_assignement_end_date = strtotime($_POST['assignement_end_date']);
		$tmp_input_ga = new GenyAssignement( $_POST['assignement_id'] );
		$tmp_project = new GenyProject( $tmp_input_ga->project_id );
		$time_project_start_date = strtotime( $tmp_project->start_date );
		$time_project_end_date = strtotime( $tmp_project->end_date );
		if( $tmp_project->status_id == 2 || $tmp_project->status_id == 3 ){ // Projet dans le status en pause ou fermé.
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur.','msg'=>"il n'est pas possible de remplir un rapport d'activité pour ce projet car il est soit fermé soit en pause.");
		}
		if( $time_assignement_start_date >= $time_project_start_date && $time_assignement_end_date <= $time_project_end_date ){
			foreach( GenyTools::getWorkedDaysList(strtotime($_POST['assignement_start_date']), strtotime($_POST['assignement_end_date']) ) as $day ){
				$geny_activity = new GenyActivity();
				$geny_ar = new GenyActivityReport();
				$day_load = $geny_ar->getDayLoad($profile->id,$day)+$_POST['assignement_load'];
				$create_report = false;
				if($day_load <= 8){
					$create_report = true;
				}
				else{
					
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur .','msg'=>"Le $day, vous déclaré plus de 8 heures journalière et vous n'êtes pas autorisé à saisir des heures supplémentaires sur des jours de congés.");
				}
				if( $create_report ){
					$geny_activity_id = $geny_activity->insertNewActivity('NULL',$day,$_POST['assignement_load'],date('Y-m-j'),$_POST['assignement_id'],$_POST['task_id']);
					if( $geny_activity_id > -1 ){
						$geny_ars = new GenyActivityReportStatus();
						$geny_ars->loadActivityReportStatusByShortName('P_USER_VALIDATION');
						$geny_ar_id = $geny_ar->insertNewActivityReport('NULL',-1,$geny_activity_id,$profile->id,$geny_ars->id );
						if( $geny_ar_id > -1 )
							$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Congés enregistrés pour le $day (en attente de validation utilisateur).");
						else
							$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'enregistrement des congés du $day.");
					}
					else {
						$geny_activity->deleteActivity($geny_activity_id);
						$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout d'une activité pour le $day.");
					}
				}
			}
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"les dates saisies sont en dehors des bornes du projet.");
		}
	}
	else{
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"certaines informations sont manquantes.");
	}
}
else if(isset($_POST['conges_action']) && ($_POST['conges_action'] == "validate_conges" || $_POST['conges_action'] == "delete_conges") ){
	if( $_POST['conges_action'] == "validate_conges" ){
		if( isset( $_POST['activity_report_id'] ) ){
			$tmp_ars = new GenyActivityReportStatus();
			$tmp_ars->loadActivityReportStatusByShortName('P_APPROVAL');
			$ok_count=0;
			foreach( $_POST['activity_report_id'] as $tmp_ar_id ){
				$tmp_ass = new GenyActivityReport( $tmp_ar_id );
				$tmp_ass->updateInt('activity_report_status_id',$tmp_ars->id);
				if($tmp_ass->commitUpdates()){
					$ok_count++;
				}
				else{
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"impossible de valider le rapport ".$tmp_ass->id.".");
				}
			}
			if($ok_count > 0 ){
				$notif = new GenyNotification();
				// Notification des admins
				$notif->insertNewGroupNotification(1,"$screen_name viens de déposer une demande de $ok_count jour(s) de congés, merci de faire le nécessaire.");
				// Notification des superusers
				$notif->insertNewGroupNotification(2,"$screen_name viens de déposer une demande de $ok_count jour(s) de congés, merci de faire le nécessaire.");
				if($ok_count == 1)
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count jour de congés est désormais en attente de validation par le management.");
				else
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count jours de congés sont désormais en attente de validation par le management.");
			}
		}
	}
	else if( $_POST['conges_action'] == "delete_conges" ){
		if( isset( $_POST['activity_report_id'] ) ){
			$ok_count=0;
			$tmp_ass = new GenyActivityReport();
			foreach( $_POST['activity_report_id'] as $tmp_ar_id ){
				if($tmp_ass->deleteActivityReport($tmp_ar_id) == 1){
					$ok_count++;
				}
				else{
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"impossible de supprimer le congés ".$tmp_ar_id.".");
				}
			}
			if($ok_count > 0 ){
				$notif = new GenyNotification();
				// Notification des admins
				$notif->insertNewGroupNotification(1,"$screen_name viens de supprimer $ok_count congés non validé(s).");
				// Notification des superusers
				$notif->insertNewGroupNotification(2,"$screen_name viens de supprimer $ok_count congés non validé(s).");
				if($ok_count == 1)
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count jour de congés a été correctement supprimé.");
				else
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count jours de congés ont été correctement supprimés.");
			}
		}
	}
}
else if(isset($_POST['validate_conges']) && $_POST['validate_conges'] == "true"){
	if( isset( $_POST['activity_report_id'] ) ){
		$tmp_ars = new GenyActivityReportStatus();
		$tmp_ars->loadActivityReportStatusByShortName('P_APPROVAL');
		$ok_count=0;
		foreach( $_POST['activity_report_id'] as $tmp_ar_id ){
			$tmp_ass = new GenyActivityReport( $tmp_ar_id );
			$tmp_ass->updateInt('activity_report_status_id',$tmp_ars->id);
			if($tmp_ass->commitUpdates()){
				$ok_count++;
			}
			else{
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur .','msg'=>"impossible de valider le congé ".$tmp_ass->id.".");
			}
		}
		if($ok_count > 0 ){
			$notif = new GenyNotification();
			// Notification des admins
			$notif->insertNewGroupNotification(1,"$screen_name viens de déposer une demande de $ok_count jour(s) de congés, merci de faire le nécessaire.");
			// Notification des superusers
			$notif->insertNewGroupNotification(2,"$screen_name viens de déposer une demande de $ok_count jour(s) de congés, merci de faire le nécessaire.");
			if($ok_count == 1)
				$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count jour de congés est désormais en attente de validation par le management.");
			else
				$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count jours de congés sont désormais en attente de validation par le management.");
		}
	}
}

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/conges.png"/><p>Congés</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="conges_add">
			Valider des congés
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de valider des vos congés.<br />
		<strong class="important_note">Important :</strong> Ce formulaire contient tous les congés que vous n'avez pas validé (y compris les plus anciens que vous n'auriez pas soumis).<br />
		</p>
		<script>
			
		
		
		
		
		
		
			(function($) {
			/*
			 * Function: fnGetColumnData
			 * Purpose:  Return an array of table values from a particular column.
			 * Returns:  array string: 1d data array 
			 * Inputs:   object:oSettings - dataTable settings object. This is always the last argument past to the function
			 *           int:iColumn - the id of the column to extract the data from
			 *           bool:bUnique - optional - if set to false duplicated values are not filtered out
			 *           bool:bFiltered - optional - if set to false all the table data is used (not only the filtered)
			 *           bool:bIgnoreEmpty - optional - if set to false empty values are not filtered from the result array
			 * Author:   Benedikt Forchhammer <b.forchhammer /AT\ mind2.de>
			 */
			$.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty ) {
				// check that we have a column id
				if ( typeof iColumn == "undefined" ) return new Array();
				
				// by default we only wany unique data
				if ( typeof bUnique == "undefined" ) bUnique = true;
				
				// by default we do want to only look at filtered data
				if ( typeof bFiltered == "undefined" ) bFiltered = true;
				
				// by default we do not wany to include empty values
				if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;
				
				// list of rows which we're going to loop through
				var aiRows;
				
				// use only filtered rows
				if (bFiltered == true) aiRows = oSettings.aiDisplay; 
				// use all rows
				else aiRows = oSettings.aiDisplayMaster; // all row numbers
			
				// set up data array	
				var asResultData = new Array();
				
				for (var i=0,c=aiRows.length; i<c; i++) {
					iRow = aiRows[i];
					var aData = this.fnGetData(iRow);
					var sValue = aData[iColumn];
					
					// ignore empty values?
					if (bIgnoreEmpty == true && sValue.length == 0) continue;
			
					// ignore unique values?
					else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;
					
					// else push the value onto the result data array
					else asResultData.push(sValue);
				}
				
				return asResultData;
			}}(jQuery));


			function fnCreateSelect( aData )
			{
				var r='<select><option value=""></option>', i, iLen=aData.length;
				for ( i=0 ; i<iLen ; i++ )
				{
					r += '<option value="'+aData[i]+'">'+aData[i]+'</option>';
				}
				return r+'</select>';
			}

		
		
		
		
		
		
		
		
		
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
				
				var oTable = $('#cra_validation_table').dataTable( {
					"bJQueryUI": true,
					"bStateSave": true,
					"bAutoWidth": false,
					"sPaginationType": "full_numbers",
					"oLanguage": {
						"sSearch": "Recherche :",
						"sLengthMenu": "Congés par page _MENU_",
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
						this.innerHTML = fnCreateSelect( oTable.fnGetColumnData(i) );
						$('select', this).change( function () {
							oTable.fnFilter( $(this).val(), i );
						} );
					}
				} );
			
			});
			
			function onCheckBoxSelectAll(){
				$("#cra_validation_table").find(':checkbox').attr('checked', $('#chkBoxSelectAll').attr('checked'));
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
		<form id="formID" action="conges_validation.php" method="post" class="table_container">
			<style>
				@import 'styles/<?php echo $web_config->theme ?>/cra_validation.css';
			</style>
			<ul>
				<li>
					<input type="checkbox" id="chkBoxSelectAll" onClick="onCheckBoxSelectAll()" /><label for="chkBoxSelectAll"> Tout (dé)séléctionner</label>
				</li>
				<li id="radio">
					<input type="radio" id="radio1" name="conges_action" value="validate_conges" /><label for="radio1">Valider</label>
					<input type="radio" id="radio2" name="conges_action" value="delete_conges" /><label for="radio2">Supprimer</label>
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
						$geny_ar = new GenyActivityReport();
						$geny_ars = new GenyActivityReportStatus();
						$geny_ars->loadActivityReportStatusByShortName('P_USER_VALIDATION');
						foreach( $geny_ar->getActivityReportsListWithRestrictions( array("activity_report_status_id=".$geny_ars->id,"profile_id=".$profile->id) ) as $ar ){
							$tmp_activity = new GenyActivity( $ar->activity_id );
							$tmp_ars = new GenyActivityReportStatus( $ar->status_id );
							$tmp_task = new GenyTask( $tmp_activity->task_id );
							$tmp_assignement = new GenyAssignement( $tmp_activity->assignement_id );
							$tmp_project = new GenyProject( $tmp_assignement->project_id );
							if( $tmp_project->type_id == 5 ){
								echo "<tr><td><input type='checkbox' name='activity_report_id[]' value=".$ar->id." /></td><td>".$tmp_activity->activity_date."</td><td>".$tmp_project->name."</td><td>".$tmp_task->name."</td><td>".$tmp_activity->load."</td><td>".$geny_ar->getDayLoad($profile->id,$tmp_activity->activity_date)."</td><td>".$geny_ars->name."</td></tr>";
							}
						}
					?>
					</tbody>
					<tfoot>
						<th>Sel.</th>
						<th>Date</th>
						<th>Projet</th>
						<th>Tâche</th>
						<th>Charge (tâche)</th>
						<th>Charge (total jour)</th>
						<th>Status</th>
					</tfoot>
				</table>
			</p>
			<p>
				<input type="submit" value="Ok" /> ou <a href="#formID">annuler</a>
			</p>
		</form>
	</p>
</div>

<div id="bottomdock">
	<ul>
		<?php 
			include 'backend/widgets/conges_add.dock.widget.php';
			include 'backend/widgets/conges_list.dock.widget.php';
		?>
	</ul>
</div>


<?php
include_once 'footer.php';
?>

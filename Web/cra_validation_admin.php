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
$header_title = '%COMPANY_NAME% - Validation CRA';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

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

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/cra.png"/><p>CRA</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
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
		<?php
			if( isset($db_status) && $db_status != "" ){
				echo "<ul class=\"status_message\">\n$db_status\n</ul>";
			}
		?>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
		</script>
		<style>
			@import 'styles/<?php echo $web_config->theme ?>/cra_validation_admin.css';
		</style>
		<form id="formID" action="cra_validation_admin.php" method="post" class="table_container">
<!-- 			<input type="hidden" name="validate_cra" value="true" /> -->
			<ul style="display: inline; color: black;">
				<li>
					<input type="checkbox" id="chkBoxSelectAll" onClick="onCheckBoxSelectAll()" /><label for="chkBoxSelectAll"> Tout (dé)séléctionner</label>
				</li>
				<li id="radio">
					<input type="radio" id="radio0" name="cra_action" value="user_validate_cra" /><label for="radio0">Validation utilisateur</label>
					<input type="radio" id="radio1" name="cra_action" value="validate_cra" /><label for="radio1">Validé</label>
					<!--<input type="radio" id="radio2" name="cra_action" value="bill_cra" /><label for="radio2">Facturé</label>
					<input type="radio" id="radio3" name="cra_action" value="pay_cra" /><label for="radio3">Payé</label>
					<input type="radio" id="radio4" name="cra_action" value="close_cra" /><label for="radio4">Fermé</label>
					<input type="radio" id="radio5" name="cra_action" value="deletion_cra" /><label for="radio5">Suppression</label>-->
					<input type="radio" id="radio6" name="cra_action" value="delete_cra" /><label for="radio6">Supprimer</label>
					<input type="radio" id="radio7" name="cra_action" value="refuse_cra" /><label for="radio7">Refusé</label>
				</li>
			</ul>
			<p>
				<table id="cra_validation_table" style="color: black; width: 100%;">
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
						$geny_ar = new GenyActivityReport();
						$geny_ars = new GenyActivityReportStatus();
						$geny_ars->loadActivityReportStatusByShortName('P_APPROVAL');
						foreach( $geny_ar->getActivityReportsByReportStatusId( $geny_ars->id ) as $ar ){
							$tmp_activity = new GenyActivity( $ar->activity_id );
							$tmp_ars = new GenyActivityReportStatus( $ar->status_id );
							$tmp_task = new GenyTask( $tmp_activity->task_id );
							$tmp_assignement = new GenyAssignement( $tmp_activity->assignement_id );
							$tmp_project = new GenyProject( $tmp_assignement->project_id );
							$tmp_profile = new GenyProfile( $tmp_assignement->profile_id );
							
							echo "<tr><td><input type='checkbox' name='activity_report_id[]' value=".$ar->id." /></td><td>".GenyTools::getProfileDisplayName($tmp_profile)."</td><td class='centered'>".$tmp_activity->activity_date."</td><td class='centered'>".$tmp_project->name."</td><td class='centered'>".$tmp_task->name."</td><td class='centered'>".$tmp_activity->load."</td><td class='centered'>".$geny_ar->getDayLoad($profile->id,$tmp_activity->activity_date)."</td><td>".$geny_ars->name."</td></tr>";
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
				<input type="submit" value="Appliquer" /> ou <a href="#formID">annuler</a>
			</p>
		</form>
	</p>
</div>

<?php
include_once 'footer.php';
?>

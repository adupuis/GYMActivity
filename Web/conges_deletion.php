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
$header_title = '%COMPANY_NAME% - Suppression congés';
$required_group_rights = 6;

include_once 'header.php';
include_once 'menu.php';

$geny_ptr = new GenyProjectTaskRelation();
$geny_tools = new GenyTools();
date_default_timezone_set('Europe/Paris');
$gritter_notifications = array();

if(isset($_POST['conges_action']) && ($_POST['conges_action'] == "conges_deletion") ){
	if( $_POST['conges_action'] == "conges_deletion" ){
		if( isset( $_POST['activity_report_id'] ) ){
			$tmp_ars = new GenyActivityReportStatus();
			$tmp_ars->loadActivityReportStatusByShortName('P_REMOVAL');
			$ok_count=0;
			foreach( $_POST['activity_report_id'] as $tmp_ar_id ){
				$tmp_ass = new GenyActivityReport( $tmp_ar_id );
				if( $tmp_ass->profile_id == $profile->id ) {
					$tmp_ass->updateInt('activity_report_status_id',$tmp_ars->id);
					if($tmp_ass->commitUpdates()){
						$ok_count++;
					}
					else{
						$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur ','msg'=>"impossible de demander la suppression du rapport ".$tmp_ass->id.".");
					}
				}else{
					$access_loger->insertNewAccessLog(
						$profile->id,
						$_SERVER['REMOTE_ADDR'],
						'false',
						"conges_deletion.php",
						UNAUTHORIZED_ACCESS,
						",user_agent=".$_SERVER['HTTP_USER_AGENT']);
				}
			}
			if($ok_count > 0 ){
				$notif = new GenyNotification();
				// Notification des admins
				$notif->insertNewGroupNotification(1,"$screen_name viens de demander la suppression de $ok_count jour(s) de congés, merci de faire le nécessaire.");
				// Notification des superusers
				$notif->insertNewGroupNotification(2,"$screen_name viens de demander la suppression de $ok_count jour(s) de congés, merci de faire le nécessaire.");
				if($ok_count == 1)
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count jour(s) de congés sont désormais en attente de suppression par le management.");
				else
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"$ok_count jour(s) de congés sont désormais en attente de suppression par le management.");
			}
		}
	}
}

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/conges.png"/><p>CRA</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="conges_remove">
			Suppression congés
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de demander la suppression de congés déjà validés.<br />
		<strong class="important_note">Important :</strong> Ce formulaire contient tous les congés validés par le management.<br />
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
				
				var oTable = $('#conges_validation_table').dataTable( {
					"bJQueryUI": true,
					"bStateSave": true,
					"bAutoWidth": false,
					"sCookiePrefix": "GYMActivity_",
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
						this.innerHTML = fnCreateSelect( oTable.fnGetColumnData(i) );
						$('select', this).change( function () {
							oTable.fnFilter( $(this).val(), i );
						} );
					}
				} );
			
			});
			
			function onCheckBoxSelectAll(){
				$("#conges_validation_table").find(':checkbox').attr('checked', $('#chkBoxSelectAll').attr('checked'));
			}
		</script>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
			
			$(function() {
				$( "#chkBoxSelectAll" ).button();
			});
		</script>
		<form id="formID" action="conges_deletion.php" method="post" class="table_container">
			<input type="hidden" name="conges_action" value="conges_deletion" />
			<style>
				@import 'styles/<?php echo $web_config->theme ?>/conges_validation.css';
			</style>
			<ul>
				<li>
					<input type="checkbox" id="chkBoxSelectAll" onClick="onCheckBoxSelectAll()" /><label for="chkBoxSelectAll"> Tout (dé)séléctionner</label>
				</li>
			</ul>
			<p>
				<table id="conges_validation_table" style="color: black; width: 100%;">
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
						$geny_ars->loadActivityReportStatusByShortName('APPROVED');
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
				<input type="submit" value="Appliquer" /> ou <a href="conges_list.php">annuler</a>
			</p>
		</form>
	</p>
</div>

<div id="bottomdock">
	<ul>
		<?php 
			include 'backend/widgets/conges_add.dock.widget.php';
			include 'backend/widgets/conges_list.dock.widget.php';
			include 'backend/widgets/conges_validation.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

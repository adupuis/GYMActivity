<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Validation CRA';
$required_group_rights = 6;

include_once 'header.php';
include_once 'menu.php';

$geny_ptr = new GenyProjectTaskRelation();
$geny_tools = new GenyTools();
date_default_timezone_set('Europe/Paris');
$db_status = "";

if(isset($_POST['create_cra']) && $_POST['create_cra'] == "true" ){
	$html_worked_days_table = '';
	if( isset($_POST['assignement_start_date']) && isset($_POST['assignement_end_date']) && isset($_POST['assignement_id']) && isset($_POST['task_id']) && isset($_POST['assignement_load']) && isset($_POST['task_id']) ){
		foreach( GenyTools::getWorkedDaysList(strtotime($_POST['assignement_start_date']), strtotime($_POST['assignement_end_date']) ) as $day ){
			$geny_activity = new GenyActivity();
			$geny_ar = new GenyActivityReport();
			$day_load = $geny_ar->getDayLoad($profile->id,$day)+$_POST['assignement_load'];
			if($day_load <= 8){
				$geny_activity_id = $geny_activity->insertNewActivity('NULL',$day,$_POST['assignement_load'],date('Y-m-j'),$_POST['assignement_id'],$_POST['task_id']);
				echo "<!-- Geny Activity ID: $geny_activity_id -->\n";
				if( $geny_activity_id > -1 ){
					$geny_ars = new GenyActivityReportStatus();
					$geny_ars->loadActivityReportStatusByShortName('P_USER_VALIDATION'); 
					echo "<!-- Inserting new activity report -->\n";
					$geny_ar_id = $geny_ar->insertNewActivityReport('NULL',-1,$geny_activity_id,$profile->id,$geny_ars->id );
					echo "<!-- Geny Activity Report ID: $geny_ar_id -->\n";
					if( $geny_ar_id > -1 )
						$db_status .= "<li class=\"status_message_success\">Rapport enregistré pour le $day (en attente de validation utilisateur).</li>\n";
					else
						$db_status .= "<li class=\"status_message_error\">Erreur lors de l'enregistrement du rapport du $day.</li>\n";
				}
				else {
					$geny_activity->removeActivity($geny_activity_id);
					$db_status .= "<li class=\"status_message_error\">Erreur lors de l'ajout d'une activité pour le $day.</li>\n";
				}
			}
			else{
				if( $day_load > 12 ){
					$db_status .= "<li class=\"status_message_error\">Erreur : Le $day, vous déclaré plus de 12 heures journalière (maximum d'heures par jour : 8h + 4h sup.).</li>\n";
				}
				else{
					$day_work_load_by_project = $geny_ar->getDayLoadByAssignement($profile->id,$day);
					// TODO: gérer la vérification de l'autorisation des heures sup. Il faut vérifier l'assignement en cours récupérer le project_id, vérifier que le projet autorise les heures sup puis vérifier la sommes des heures travaillés (<= 8 + 4 heures sup au maximum)
					$extra = '';
					for($k=0; $k < count($day_work_load_by_project); $k++ ){
						$extra .= $day_work_load_by_project[$k]['activity_date']."|".$day_work_load_by_project[$k]['sum_activity_load']."|".$day_work_load_by_project[$k]['assignement_id'].",";
					}
					$db_status .= "<li class=\"status_message_error\">Erreur : Le $day, vous déclaré plus de 8 heures journalière ($extra).</li>\n";
				}
			}
		}
	}
	else{
		$db_status .= "<li class=\"status_message_error\">Erreur : certaines informations sont manquantes.</li>\n";
	}
}
else if(isset($_POST['validate_cra']) && $_POST['validate_cra'] == "true"){
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
				$db_status .= "<li class=\"status_message_error\">Erreur : impossible de valider le rapport ".$tmp_ass->id.".</li>\n";
			}
		}
		if($ok_count > 0 ){
			if($ok_count == 1)
				$db_status .= "<li class=\"status_message_success\">$ok_count rapport est désormais en attente de validation par le management.</li>\n";
			else
				$db_status .= "<li class=\"status_message_success\">$ok_count rapports sont désormais en attente de validation par le management.</li>\n";
		}
	}
}

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/cra.png"/><p>CRA</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
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
					if( i == 2 || i == 3 || i == 6){
						this.innerHTML = fnCreateSelect( oTable.fnGetColumnData(i+1) );
						$('select', this).change( function () {
							oTable.fnFilter( $(this).val(), i+1 );
						} );
					}
				} );
				
				
				$("#chkBoxSelectAll").click(function() {
					alert("ok");
// 					$(".chkAllItem input:checkbox").attr('checked',this.checked);
				});
				
// 				$(".chkAllItem input:checkbox").click(function(){
// 				if($("#chkBoxSelectAll").attr('checked') == true && this.checked == false)
// 				$("#chkBoxSelectAll").attr('checked',false);
// 					
// 				if(this.checked == true)
// 					CheckSelectAll();
// 				});
// 				
// 				function CheckSelectAll()
// 				{
// 					var flag = true;
// 					$(".chkAllItem input:checkbox").each(function() {
// 						if(this.checked == false)
// 						flag = false;
// 					});
// 					$("#chkBoxSelectAll").attr('checked',flag);
// 				}
				
			});
			
			
		</script>
		<?php
			if( isset($db_status) && $db_status != "" ){
				echo "<ul class=\"status_message\">\n$db_status\n</ul>";
			}
		?>
		<script>
			$(".status_message").click(function () {
			$(".status_message").fadeOut("slow");
			});
		</script>
		<form id="formID" action="cra_validation.php" method="post" class="table_container">
			<input type="hidden" name="validate_cra" value="true" />
			<ul style="display: inline; color: black;">
				<li>
					<input type="checkbox" id="chkBoxSelectAll"> Tout (dé)séléctionner
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
							
							echo "<tr><td><input type='checkbox' name='activity_report_id[]' value=".$ar->id." class='chkAllItem'/></td><td>".$tmp_activity->activity_date."</td><td>".$tmp_project->name."</td><td>".$tmp_task->name."</td><td>".$tmp_activity->load."</td><td>".$geny_ar->getDayLoad($profile->id,$tmp_activity->activity_date)."</td><td>".$geny_ars->name."</td></tr>";
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
				<input type="submit" value="Valider" /> ou <a href="#formID">annuler</a>
			</p>
		</form>
	</p>
</div>

<?php
include_once 'footer.php';
?>

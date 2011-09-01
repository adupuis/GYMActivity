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
$header_title = '%COMPANY_NAME% - Liste des CRA';
$required_group_rights = 6;

include_once 'header.php';
include_once 'menu.php';

$geny_ptr = new GenyProjectTaskRelation();
$geny_tools = new GenyTools();
date_default_timezone_set('Europe/Paris');

$data_array = array();
$data_array_filters = array( 0 => array(), 1 => array(), 2 => array(), 4 => array() );
$geny_ar = new GenyActivityReport();
$tmp_activity = new GenyActivity();
$tmp_ars = new GenyActivityReportStatus();
$tmp_task = new GenyTask();
$tmp_assignement = new GenyAssignement();
$tmp_project = new GenyProject();

foreach( $geny_ar->getActivityReportsByProfileId( $profile->id ) as $ar ){
	$tmp_activity->loadActivityById( $ar->activity_id );
	$tmp_ars->loadActivityReportStatusById( $ar->status_id );
	$tmp_task->loadTaskById( $tmp_activity->task_id );
	$tmp_assignement->loadAssignementById( $tmp_activity->assignement_id );
	$tmp_project->loadProjectById( $tmp_assignement->project_id );
	
	$status_name = "<strong style='color: red;'>error</strong>";
	if( $tmp_ars->name != "" )
		$status_name = $tmp_ars->name;
		
	$data_array[] = array( $tmp_activity->activity_date, $tmp_project->name, $tmp_task->name, $tmp_activity->load, GenyTools::getActivityReportStatusAsColoredHtml($tmp_ars) );
	
	if( ! in_array($tmp_activity->activity_date,$data_array_filters[0]) )
			$data_array_filters[0][] = $tmp_activity->activity_date;
	if( ! in_array($tmp_project->name,$data_array_filters[1]) )
			$data_array_filters[1][] = $tmp_project->name;
	if( ! in_array($tmp_task->name,$data_array_filters[2]) )
			$data_array_filters[2][] = $tmp_task->name;
	if( ! in_array($status_name,$data_array_filters[4]) )
			$data_array_filters[4][] = $status_name;
}

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/cra.png"/><p>CRA</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="cra_list">
			Liste des CRA
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de lister tous les rapports d'activité que vous avez créé.<br />
		</p>
		<script>
			var indexData = new Array();
			<?php
				$cookie = json_decode($_COOKIE["GYMActivity_cra_list_table_cra_list_php"]);
				
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
				
				var oTable = $('#cra_list_table').dataTable( {
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
					if( i==0 || i == 1 || i == 2 || i == 4){
						this.innerHTML = indexData[i];
						$('select', this).change( function () {
							oTable.fnFilter( $(this).val(), i );
						} );
					}
				} );
			
			});
			
			function onCheckBoxSelectAll(){
				$("#cra_list_table").find(':checkbox').attr('checked', $('#chkBoxSelectAll').attr('checked'));
			}
		</script>
		<form id="formID" action="#" method="post" class="table_container">
			<p>
				<table id="cra_list_table" style="color: black; width: 100%;">
					<thead>
						<th>Date</th>
						<th>Projet</th>
						<th>Tâche</th>
						<th>Charge (tâche)</th>
						<th>Status</th>
					</thead>
					<tbody>
					<?php
						foreach( $data_array as $da ){
							echo "<tr><td class='centered'>".$da[0]."</td><td class='centered'>".$da[1]."</td><td class='centered'>".$da[2]."</td><td class='centered'>".$da[3]."</td><td class='centered'>".$da[4]."</td></tr>";
						}
					?>
					</tbody>
					<tfoot>
						<th>Date</th>
						<th>Projet</th>
						<th>Tâche</th>
						<th>Charge (tâche)</th>
						<th>Status</th>
					</tfoot>
				</table>
			</p>
		</form>
	</p>
</div>

<div id="bottomdock">
	<ul>
		<?php 
			include 'backend/widgets/cra_add.dock.widget.php';
			include 'backend/widgets/cra_validation.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

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
$header_title = '%COMPANY_NAME% - Reporting status CRA';
$required_group_rights = array(1,2,4,5);

include_once 'header.php';
include_once 'menu.php';
include_once 'backend/api/ajax_toolbox.php';

$reporting_data = array( "2011-01" => array("P_USER_VALIDATION" => 0, "P_APPROVAL" => 0, "APPROVED" => 0, "BILLED" => 0, "PAID" => 0, "CLOSE" => 0, "P_REMOVAL" => 0, "REMOVED" => 0, "REFUSED" => 0), "2011-02" => array("P_USER_VALIDATION" => 0, "P_APPROVAL" => 0, "APPROVED" => 0, "BILLED" => 0, "PAID" => 0, "CLOSE" => 0, "P_REMOVAL" => 0, "REMOVED" => 0, "REFUSED" => 0), "2011-03" => array("P_USER_VALIDATION" => 0, "P_APPROVAL" => 0, "APPROVED" => 0, "BILLED" => 0, "PAID" => 0, "CLOSE" => 0, "P_REMOVAL" => 0, "REMOVED" => 0, "REFUSED" => 0), "2011-04" => array("P_USER_VALIDATION" => 0, "P_APPROVAL" => 0, "APPROVED" => 0, "BILLED" => 0, "PAID" => 0, "CLOSE" => 0, "P_REMOVAL" => 0, "REMOVED" => 0, "REFUSED" => 0), "2011-05" => array("P_USER_VALIDATION" => 0, "P_APPROVAL" => 0, "APPROVED" => 0, "BILLED" => 0, "PAID" => 0, "CLOSE" => 0, "P_REMOVAL" => 0, "REMOVED" => 0, "REFUSED" => 0), "2011-06" => array("P_USER_VALIDATION" => 0, "P_APPROVAL" => 0, "APPROVED" => 0, "BILLED" => 0, "PAID" => 0, "CLOSE" => 0, "P_REMOVAL" => 0, "REMOVED" => 0, "REFUSED" => 0), "2011-07" => array("P_USER_VALIDATION" => 0, "P_APPROVAL" => 0, "APPROVED" => 0, "BILLED" => 0, "PAID" => 0, "CLOSE" => 0, "P_REMOVAL" => 0, "REMOVED" => 0, "REFUSED" => 0), "2011-08" => array("P_USER_VALIDATION" => 0, "P_APPROVAL" => 0, "APPROVED" => 0, "BILLED" => 0, "PAID" => 0, "CLOSE" => 0, "P_REMOVAL" => 0, "REMOVED" => 0, "REFUSED" => 0), "2011-09" => array("P_USER_VALIDATION" => 0, "P_APPROVAL" => 0, "APPROVED" => 0, "BILLED" => 0, "PAID" => 0, "CLOSE" => 0, "P_REMOVAL" => 0, "REMOVED" => 0, "REFUSED" => 0), "2011-10" => array("P_USER_VALIDATION" => 0, "P_APPROVAL" => 0, "APPROVED" => 0, "BILLED" => 0, "PAID" => 0, "CLOSE" => 0, "P_REMOVAL" => 0, "REMOVED" => 0, "REFUSED" => 0), "2011-11" => array("P_USER_VALIDATION" => 0, "P_APPROVAL" => 0, "APPROVED" => 0, "BILLED" => 0, "PAID" => 0, "CLOSE" => 0, "P_REMOVAL" => 0, "REMOVED" => 0, "REFUSED" => 0), "2011-12" => array("P_USER_VALIDATION" => 0, "P_APPROVAL" => 0, "APPROVED" => 0, "BILLED" => 0, "PAID" => 0, "CLOSE" => 0, "P_REMOVAL" => 0, "REMOVED" => 0, "REFUSED" => 0) );

$gritter_notifications = array();
$year=date('Y', time());
$start_date = $year.'-01-01';
$end_date = $year.'-12-31';
$reporting_start_date = getParam('reporting_start_date',$start_date);
$reporting_end_date = getParam('reporting_end_date',$end_date);

if( isset($reporting_start_date) && $reporting_start_date != "" && isset($reporting_end_date) && $reporting_end_date != "" ){
	if( date_parse( $reporting_start_date ) !== false && date_parse( $reporting_end_date )!== false ){
		if( $reporting_end_date >= $reporting_start_date ){
			$start_date = $reporting_start_date;
			$end_date = $reporting_end_date;
		}
		else
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur fatale','msg'=>"La date de fin doit être supérieure ou égale à la date de début de la période rapportée.");
	}
	else
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur fatale','msg'=>"Au moins une des dates fournies n'est pas une date valide. Merci de respecter le format yyyy-mm-dd.");
}

$geny_ar = new GenyActivityReport();
$geny_ars = new GenyActivityReportStatus();

$debug_start_time = time();
$debug_reports_count = 0;
foreach( $geny_ar->getAllActivityReports() as $ar ){
	$geny_activity = new GenyActivity( $ar->activity_id ); // Contient la charge et la date
	$date = date( 'Y-m', strtotime( $geny_activity->activity_date ) );
	$geny_ars->loadActivityReportStatusById( $ar->status_id );
	$reporting_data[$date][$geny_ars->shortname] += round($geny_activity->load/8,1);
	$debug_reports_count++;
}
error_log("reporting_cra_status took ".(time() - $debug_start_time)." seconds to create the list with the $debug_reports_count reports.",0);

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/reporting.png"/><p>Reporting</p>
</div>


<script>
	jQuery(document).ready(function(){
		
			var oTable = $('#reporting_list').dataTable( {
				"bJQueryUI": true,
				"bStateSave": true,
				"bAutoWidth": false,
				"iDisplayLength": 25,
				"sCookiePrefix": "GYMActivity_",
				"sPaginationType": "full_numbers",
				"oLanguage": {
					"sSearch": "Recherche :",
					"sLengthMenu": "Lignes par page _MENU_",
					"sZeroRecords": "Aucun résultat",
					"sInfo": "Aff. _START_ à _END_ de _TOTAL_ lignes",
					"sInfoEmpty": "Aff. 0 à 0 de 0 lignes",
					"sInfoFiltered": "(filtré de _MAX_ lignes)",
					"oPaginate":{ 
						"sFirst":"Début",
						"sLast": "Fin",
						"sNext": "Suivant",
						"sPrevious": "Précédent"
					}
				}
			} );
		});
</script>

<!-- Création du reporting graphique -->
<!--Load the AJAX API-->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

	// Load the Visualization API and the piechart package.
	google.load('visualization', '1.0', {'packages':['corechart']});
	
	// Set a callback to run when the Google Visualization API is loaded.
	google.setOnLoadCallback(drawChart);
	
	// Callback that creates and populates a data table, 
	// instantiates the pie chart, passes in the data and
	// draws it.
	function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Date');
		data.addColumn('number', 'Val. utilisateur');
		data.addColumn('number', 'Val. management');
		data.addColumn('number', 'Validé');
		data.addColumn('number', 'Facturé');
		data.addColumn('number', 'Payé');
		data.addColumn('number', 'Fermé');
		data.addColumn('number', 'Att. suppr.');
		data.addColumn('number', 'Supprimé');
		data.addColumn('number', 'Refusé');
		data.addRows(12);
		
		<?php
		$idx=0;
		foreach ( $reporting_data as $date => $arr ){
			echo "data.setValue($idx, 0, '$date');\n";
			echo "data.setValue($idx, 1, ".$arr['P_USER_VALIDATION'].");\n";
			echo "data.setValue($idx, 2, ".$arr['P_APPROVAL'].");\n";
			echo "data.setValue($idx, 3, ".$arr['APPROVED'].");\n";
			echo "data.setValue($idx, 4, ".$arr['BILLED'].");\n";
			echo "data.setValue($idx, 5, ".$arr['PAID'].");\n";
			echo "data.setValue($idx, 6, ".$arr['CLOSE'].");\n";
			echo "data.setValue($idx, 7, ".$arr['P_REMOVAL'].");\n";
			echo "data.setValue($idx, 8, ".$arr['REMOVED'].");\n";
			echo "data.setValue($idx, 9, ".$arr['REFUSED'].");\n";
			$idx++;
		}
		?>
		
		var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
		chart.draw(data, {width: 800, height: 600, title: 'Ventilation des CRA par status',
			vAxis: {title: 'Date'}, isStacked: true
		});
	}
	
	<?php
		// Cette fonction est définie dans header.php
		displayStatusNotifications($gritter_notifications,$web_config->theme);
	?>
	
	jQuery(document).ready(function(){
		$("#formID").validationEngine('init');
		// binds form submission and fields to the validation engine
		$("#formID").validationEngine('attach');
	});
	
// 	$(function() {
// 		$( "#reporting_start_date" ).datepicker();
// 		$( "#reporting_start_date" ).datepicker( "option", "showAnim", "slideDown" );
// 		$( "#reporting_start_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
// 		$( "#reporting_start_date" ).datepicker('setDate', <?php echo "'".$start_date."'"; ?>);
// 		$( "#reporting_start_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
// 		$( "#reporting_start_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
// 		$( "#reporting_start_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
// 		$( "#reporting_start_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
// 		$( "#reporting_start_date" ).datepicker( "option", "firstDay", 1 );
// 		$( "#reporting_end_date" ).datepicker();
// 		$( "#reporting_end_date" ).datepicker( "option", "showAnim", "slideDown" );
// 		$( "#reporting_end_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
// 		$( "#reporting_end_date" ).datepicker('setDate', <?php echo "'".$end_date."'"; ?>);
// 		$( "#reporting_end_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
// 		$( "#reporting_end_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
// 		$( "#reporting_end_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
// 		$( "#reporting_end_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
// 		$( "#reporting_end_date" ).datepicker( "option", "firstDay", 1 );
// 		
// 		$( "#reporting_start_date" ).change( function(){ $( "#reporting_end_date" ).val( $( "#reporting_start_date" ).val() ) } );
// 		
// 	});
	

</script>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="reporting_monthly_view" style="width: 600px;">
			Repartition des CRA par status
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la répartition des CRA par status. Les valeurs indiquées dans le tableau sont en jours.<br/>
		Reporting entre le <strong><?php echo $start_date; ?></strong> et le <strong><?php echo $end_date; ?></strong>.<br/>
		</p>
		<style>
			@import 'styles/<?php echo $web_config->theme ?>/reporting_monthly_view.css';
		</style>
		<!--<form id="formID" action="reporting_load.php" method="post">
			<p>
				<label for="reporting_start_date">Date de début</label>
				<input name="reporting_start_date" id="reporting_start_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="reporting_end_date">Date de fin</label>
				<input name="reporting_end_date" id="reporting_end_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<input type="submit" value="Ajuster le reporting" />
		</form>-->
		<div class="table_container">
		<p>
			
			<table id="reporting_list">
			<thead>
				<th>Date</th>
				<th>Val. utilisateur</th>
				<th>Val. management</th>
				<th>Validé</th>
				<th>Facturé</th>
				<th>Payé</th>
				<th>Fermé</th>
				<th>Att. suppr.</th>
				<th>Supprimé</th>
				<th>Refusé</th>
			</thead>
			<tbody>
			<?php
				foreach ( $reporting_data as $date => $arr ){
					echo "<tr><td>$date</td><td>".$arr['P_USER_VALIDATION']."</td><td>".$arr['P_APPROVAL']."</td><td>".$arr['APPROVED']."</td><td>".$arr['BILLED']."</td><td>".$arr['PAID']."</td><td>".$arr['CLOSE']."</td><td>".$arr['P_REMOVAL']."</td><td>".$arr['REMOVED']."</td><td>".$arr['REFUSED']."</td></tr>\n";
				}
			?>
			</tbody>
			<tfoot>
				<th>Date</th>
				<th>Val. utilisateur</th>
				<th>Val. management</th>
				<th>Validé</th>
				<th>Facturé</th>
				<th>Payé</th>
				<th>Fermé</th>
				<th>Att. suppr.</th>
				<th>Supprimé</th>
				<th>Refusé</th>
			</tfoot>
			</table>
		</p>
		</div>
		<p>
			<br/>
			<div id="chart_div" style="position: relative; width: 800px; display: block;margin-left: auto;margin-right: auto;"></div>
		</p>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php 
		include 'backend/widgets/reporting_load.dock.widget.php'; 
		include 'backend/widgets/reporting_cra_completion.dock.widget.php';
		?>
	</ul>
</div>
<?php
include_once 'footer.php';
?>

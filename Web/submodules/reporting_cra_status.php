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

include_once 'backend/api/ajax_toolbox.php';

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
	$reporting_data[$date][$geny_ars->shortname] += $geny_activity->load;
	$debug_reports_count++;
}
error_log("reporting_cra_status took ".(time() - $debug_start_time)." seconds to create the list with the $debug_reports_count reports.",0);

?>
<script>
	jQuery(document).ready(function(){
		
			var oTable = $('#reporting_cra_status_table').dataTable( {
				"bDeferRender": true,
				"bJQueryUI": true,
				"bStateSave": true,
				"bAutoWidth": false,
				"iDisplayLength": 25,
				"sCookiePrefix": "GYMActivity_",
				"iCookieDuration": 60*60*24*365, // 1 year
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
		data.addRows(<?php echo count($reporting_data); ?>);
		
		<?php
		$idx=0;
		$rd_keys = array_keys($reporting_data);
		sort($rd_keys);
// 		foreach ( $reporting_data as $date => $arr ){
		foreach ( $rd_keys as $date){
			$arr = $reporting_data[$date];
			echo "data.setValue($idx, 0, '$date');\n";
			echo "data.setValue($idx, 1, ".round($arr['P_USER_VALIDATION']/8,1).");\n";
			echo "data.setValue($idx, 2, ".round($arr['P_APPROVAL']/8,1).");\n";
			echo "data.setValue($idx, 3, ".round($arr['APPROVED']/8,1).");\n";
			echo "data.setValue($idx, 4, ".round($arr['BILLED']/8,1).");\n";
			echo "data.setValue($idx, 5, ".round($arr['PAID']/8,1).");\n";
			echo "data.setValue($idx, 6, ".round($arr['CLOSE']/8,1).");\n";
			echo "data.setValue($idx, 7, ".round($arr['P_REMOVAL']/8,1).");\n";
			echo "data.setValue($idx, 8, ".round($arr['REMOVED']/8,1).");\n";
			echo "data.setValue($idx, 9, ".round($arr['REFUSED']/8,1).");\n";
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
		<img src="images/<?php echo $web_config->theme; ?>/reporting_cra.png"></img>
		<span class="reporting_monthly_view">
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
			
			<table id="reporting_cra_status_table">
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
					echo "<tr><td>$date</td><td>".round($arr['P_USER_VALIDATION']/8,1)."</td><td>".round($arr['P_APPROVAL']/8,1)."</td><td>".round($arr['APPROVED']/8,1)."</td><td>".round($arr['BILLED']/8,1)."</td><td>".round($arr['PAID']/8,1)."</td><td>".round($arr['CLOSE']/8,1)."</td><td>".round($arr['P_REMOVAL']/8,1)."</td><td>".round($arr['REMOVED']/8,1)."</td><td>".round($arr['REFUSED']/8,1)."</td></tr>\n";
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
<?php
	$bottomdock_items = array('backend/widgets/reporting_load.dock.widget.php','backend/widgets/reporting_cra_completion.dock.widget.php');
?>

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

$reporting_data = array();
$reporting_data_etp = array();
$reporting_data_etp_conges = array();
$geny_pmd = new GenyProfileManagementData();
$geny_property = new GenyProperty;
$gritter_notifications = array();

// Params of the script
$param_year = GenyTools::getParam("year",date('Y', time()));
$param_num_cp = GenyTools::getParam("num_cp",25);
$num_days_in_year = count(GenyTools::getWorkedDaysList( strtotime("$year-01-01"), strtotime("$year-12-31") ));
$num_billable_days_in_year = GenyTools::getParam("billable_days_in_year",218);
$param_num_rtt = GenyTools::getParam("num_rtt",$num_days_in_year-$param_num_cp-$num_billable_days_in_year);
$cp_by_work_day = $param_num_cp/$num_days_in_year;
$rtt_by_work_day = $param_num_rtt/$num_days_in_year;

// echo "<strong>Nombre de jours dans l'année: $num_days_in_year</strong><br/>";
// echo "<strong>Nombre de jours facturable dans l'année: $num_billable_days_in_year</strong><br/>";
// echo "<strong>Nombre de CP dans l'année: $param_num_cp</strong><br/>";
// echo "<strong>Nombre de RTT dans l'année: $param_num_rtt</strong><br/>";

// We create a table that contains the filters data (but only for required data).
$data_array_filters = array( 0 => array() );

// We load the property that holds categories and its options
$geny_property->loadPropertyByName('PROP_PROFILE_CATEGORY');
$property_options = array();
foreach( $geny_property->getPropertyOptions() as $option ){
	$property_options[$option->id]=$option;
	$data_array_filters[0][] = $option->content;
	$reporting_data[$option->id] = 0;
}

foreach( $geny_pmd->getAllProfileManagementData() as $pmd ){
	$reporting_data[$pmd->category]++;
// 	echo "Adding ".$pmd->getProfile()->login." catgory: ".$pmd->category."<br/>";
	$reporting_start_date = "$year-01-01";
	$reporting_end_date = "$year-12-31";
	if( $pmd->recruitement_date > $reporting_start_date ){
		$reporting_start_date = $pmd->recruitement_date;
	}
// 	echo " -* testing which is the lesser between ".$pmd->resignation_date." and ".$reporting_end_date."<br/>";
	if( preg_match('/^\d\d\d\d\-\d\d-\d\d/',$pmd->resignation_date) === 1 && $pmd->resignation_date < $reporting_end_date ){
		$reporting_end_date = $pmd->resignation_date;
	}
	if( ! isset($reporting_data_etp[$pmd->category]) ){
		$reporting_data_etp[$pmd->category]=0;
	}
	$tmp_worked_days = count(GenyTools::getWorkedDaysList( strtotime($reporting_start_date), strtotime($reporting_end_date) ));
	$billable_days = $tmp_worked_days - ($cp_by_work_day*$tmp_worked_days + $rtt_by_work_day*$tmp_worked_days);
// 	echo " * For ".$pmd->getProfile()->login." start_date=$reporting_start_date stop_date=$reporting_end_date wich means wrked_days=$tmp_worked_days billable_days=$billable_days (cp=".($cp_by_work_day*$tmp_worked_days)." RTT=".($rtt_by_work_day*$tmp_worked_days).")<br/>";
	$reporting_data_etp[$pmd->category] += $tmp_worked_days;
	$reporting_data_etp_conges[$pmd->category] += $billable_days;
}
// echo ">>>>>>>>>>>><br/>";
$js_data_count = "";
$tmp_array=array();
foreach ($reporting_data as $idx => $d){
// 	echo "<strong>$idx: ".$property_options[$idx]->content.": </strong>$d<br/>";
	$tmp_array[]= "['".$property_options[$idx]->content."', $d]";
}
$js_data_count = implode(",",$tmp_array);
// echo "<<<<<<<<<<<<<<br/>";
// echo ">>>>>>> ETP >>>>><br/>";
$js_data_etp = "";
$tmp_array=array();
// foreach ($reporting_data_etp as $idx => $d){
// // 	echo "<strong>$idx: ".$property_options[$idx]->content.": </strong>".($d/$num_days_in_year)."<br/>";
// 	$tmp_array[]= "['".$property_options[$idx]->content."', ".round($d/$num_days_in_year,2)."]";
// }
// echo "<<<<<<<<<<<<<<br/>";
// echo ">>>>>>> ETP Congés >>>>><br/>";
foreach ($reporting_data_etp_conges as $idx => $d){
// 	echo "<strong>$idx: ".$property_options[$idx]->content.": </strong>".round($d/$num_billable_days_in_year,2)."<br/>";
	$tmp_array[]= "['".$property_options[$idx]->content."', ".round($d/$num_billable_days_in_year,2)."]";
}
// echo "<<<<<<<<<<<<<<br/>";
$js_data_etp=implode(",",$tmp_array);
?>
<script>
	var indexData = new Array();
	<?php
		if(array_key_exists('GYMActivity_reporting_category_table_loader_php', $_COOKIE)) {
			$cookie = json_decode($_COOKIE["GYMActivity_reporting_category_table_loader_php"]);
		}
		
		$data_array_filters_html = array();
		foreach( $data_array_filters as $idx => $data ){
			//error_log("\$idx=$idx",0);
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
		
			var oTable = $('#reporting_load_table').dataTable( {
				"bDeferRender": true,
				"bJQueryUI": true,
				"bStateSave": true,
				"bAutoWidth": false,
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
			/* Add a select menu for each TH element in the table footer */
			$("tfoot th").each( function ( i ) {
				if( i < 1 ){
					this.innerHTML = indexData[i];
					$('select', this).change( function () {
						oTable.fnFilter( $(this).val(), i );
					} );
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

		// Create the data table.
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Catégorie');
		data.addColumn('number', 'Effectif');
		data.addRows([
		<?php echo $js_data_count;?>
		]);

		// Set chart options
		var options = {'title':'Effectifs pour <?php echo "$year" ?>',
				'is3D': true,
				'width':800,
				'height':300};

		// Instantiate and draw our chart, passing in some options.
		var chart = new google.visualization.PieChart(document.getElementById('chart_div1'));
		chart.draw(data, options);
		
		// Create the data table.
		var data3 = new google.visualization.DataTable();
		data3.addColumn('string', 'Catégorie');
		data3.addColumn('number', 'ETP');
		data3.addRows([
		<?php echo $js_data_etp;?>
		]);

		// Set chart options
		var options = {'title':'ETP pour <?php echo "$year" ?>',
				'is3D': true,
				'width':800,
				'height':300};

		// Instantiate and draw our chart, passing in some options.
		var chart3 = new google.visualization.PieChart(document.getElementById('chart_div3'));
		chart3.draw(data3, options);
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
</script>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/reporting_generic.png"></img>
		<span class="reporting_monthly_view">
			Reporting de charge
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des collaborateurs de <?php echo $web_config->company_name; ?> ventilés par effectifs par catégorie et par ETP. Ce reporting concerne l'année <?php echo $param_year ; ?><br/>
		</p>
		<style>
			@import 'styles/<?php echo $web_config->theme ?>/reporting_monthly_view.css';
		</style>
		<form id="formID" action="loader.php?module=reporting_category" method="post">
			<p>
				<label for="billable_days_in_year">Nombre de jours facturables</label>
				<input name="billable_days_in_year" id="billable_days_in_year" type="text" class="validate[required] text-input" value="<?php echo $num_billable_days_in_year ; ?>" />
			</p>
			<p>
				<label for="year">Année concernée</label>
				<input name="year" id="year" type="text" class="validate[required] text-input" value="<?php echo $param_year ; ?>" />
			</p>
			<!--<p>
				<label for="num_cp">Nbr jours CP</label>
				<input name="num_cp" id="num_cp" type="text" class="validate[required] text-input" value="<?php echo $param_num_cp ; ?>" />
			</p>
			<p>
				<label for="num_rtt">Nbr jours RTT</label>
				<input name="num_rtt" id="num_rtt" type="text" class="validate[required] text-input" value="<?php echo $param_num_rtt ; ?>" />
			</p>-->
			<input type="submit" value="Ajuster le reporting" />
		</form>
		<div class="table_container">
		<p>
			
			<table id="reporting_load_table">
			<thead>
				<th>Catégorie</th>
				<th>Effectif</th>
				<th>ETP</th>
			</thead>
			<tbody>
			<?php
				foreach ($reporting_data as $idx => $d){
					echo "<tr><td>".$property_options[$idx]->content."</td><td>$d</td><td>".(round($reporting_data_etp[$idx]/$num_days_in_year,2))."</td>";
				}
			?>
			</tbody>
			<tfoot>
				<th>Catégorie</th>
				<th>Effectif</th>
				<th>ETP</th>
			</tfoot>
			</table>
		</p>
		</div>
		<p>
			<ul>
				<li id="chart_div1"></li>
				<li id="chart_div3"></li>
			</ul>
		</p>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/reporting_cra_completion.dock.widget.php','backend/widgets/reporting_cra_status.dock.widget.php');
?>

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
$header_title = '%COMPANY_NAME% - Remplissage CRA';
$required_group_rights = array(1,2,4,5);

include_once 'header.php';
include_once 'menu.php';

$tmp_profile = new GenyProfile();

$month = date('m', time());
$year=date('Y', time());

$start_date = GenyTools::getCurrentMonthFirstDayDate();
$end_date = GenyTools::getCurrentMonthLastDayDate();

$geny_ar = new GenyActivityReport();
$worked_days = GenyTools::getWorkedDaysList(strtotime($start_date),strtotime($end_date));
$estimated_load=0;
foreach( $worked_days as $day ){
	$estimated_load += 8;
}

$user_completion_data = array();

foreach( $tmp_profile->getAllProfiles() as $p ){
	if( $p->rights_group_id <= 5 ){
		$user_load=0;
		foreach( $worked_days as $day ){
			$user_load += $geny_ar->getDayLoad($p->id,$day);
		}
		$user_completion_data[$p->id] = array( "profile_object" => $p, "completion" => round(($user_load*100)/$estimated_load,0) );
	}
}

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



<div id="mainarea">
	<p class="mainarea_title">
		<span class="reporting_monthly_view">
			Remplissage CRA
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici le taux de remplissage des CRA du mois courant (<?php echo "$year-$month"; ?>) par profil (<strong>externes exclus</strong>).<br/>
		<strong>Attention: la complétion est estimée en pourcentage et ne permet pas de voir les heures supplémentaires.</strong>
		</p>
		<style>
			@import 'styles/<?php echo $web_config->theme ?>/reporting_monthly_view.css';
		</style>
		<div class="table_container">
		<p>
			
			<table id="reporting_list">
			<thead>
				<th>Collab.</th>
				<th>Complétion (%)</th>
				<th>Complétion (graphique)</th>
			</thead>
			<tbody>
			<?php
				$js="";
				foreach( $user_completion_data as $item ){
					$js .= '$(function() {$( "#completion_'.$item["profile_object"]->id.'" ).progressbar({value: '.$item["completion"].'}); ;})'."\n";
					echo "<tr><td>".GenyTools::getProfileDisplayName($item["profile_object"])."</td><td>".$item["completion"]."</td><td id='completion_".$item["profile_object"]->id."'></td></tr>";
				}
			?>
			</tbody>
			<tfoot>
				<th>Collab.</th>
				<th>Complétion (%)</th>
				<th>Complétion (graphique)</th>
			</tfoot>
			</table>
			<script type='text/javascript'>
				<?php echo $js; ?>
			</script>
		</p>
		</div>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php include 'backend/widgets/reporting_monthly_view.dock.widget.php'; ?>
		<?php include 'backend/widgets/reporting_load.dock.widget.php'; ?>
		<?php include 'backend/widgets/reporting_previous_month_view.dock.widget.php'; ?>
	</ul>
</div>
<?php
include_once 'footer.php';
?>

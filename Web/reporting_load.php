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
$header_title = '%COMPANY_NAME% - Reporting de charge';
$required_group_rights = array(1,2,4,5);

include_once 'header.php';
include_once 'menu.php';
include_once 'backend/api/ajax_toolbox.php';

$reporting_data = array();
$geny_tools = new GenyTools();
$geny_project = new GenyProject();
$geny_profile = new GenyProfile();
$geny_client = new GenyClient();
$geny_assignement = new GenyAssignement();
$geny_task = new GenyTask();
$gritter_notifications = array();

foreach( $geny_client->getAllClients() as $client ){
	$clients[$client->id] = $client;
}

$geny_pt = new GenyProjectType();
foreach( $geny_pt->getAllProjectTypes() as $pt ){
	$pts[$pt->id] = $pt;
}

$geny_ps = new GenyProjectStatus();
foreach( $geny_ps->getAllProjectStatus() as $ps ){
	$pss[$ps->id] = $ps;
}

$month = date('m', time());
$year=date('Y', time());
$d_month_name = date('F', mktime(0,0,0,$month,28,$year));
$start_date="$year-$month-01";
$end_date="$year-$month-31";
$reporting_start_date = getParam('reporting_start_date');
$reporting_end_date = getParam('reporting_end_date');
$aggregation_level = getParam('reporting_aggregation_level','project');

// We create a table that contains the filters data (but only for required data).
$data_array_filters = array( 0 => array(), 1 => array(), 2 => array(), 3 => array() );

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
$geny_ars->loadActivityReportStatusByShortName('APPROVED');
foreach( $geny_ar->getActivityReportsByReportStatusId($geny_ars->id) as $ar ){
	$geny_activity = new GenyActivity( $ar->activity_id ); // Contient la charge et l'assignement_id
	// Nous ne voulons pas des absences non payé par l'entreprise dans l'aggregation par projet.
	// En revanche quand le mode d'aggrégation est par tâche nous le voulons.
	if( $aggregation_level == "tasks" || ($geny_activity->task_id != 8 && $geny_activity->task_id != 12 && $geny_activity->task_id != 19) ){ 
		if( $geny_activity->activity_date >= $start_date && $geny_activity->activity_date <= $end_date ){
			if( !isset( $reporting_data[$ar->profile_id] ) ){
				$reporting_data[$ar->profile_id] = array();
				$reporting_data_tasks[$ar->profile_id] = array();
			}
			if( !isset($reporting_data[$ar->profile_id][$geny_activity->assignement_id]) ){
				$reporting_data[$ar->profile_id][$geny_activity->assignement_id] = 0;
				$reporting_data_tasks[$ar->profile_id][$geny_activity->assignement_id] = array();
			}
			if( !isset($reporting_data_tasks[$ar->profile_id][$geny_activity->assignement_id][$geny_activity->task_id]) )
				$reporting_data_tasks[$ar->profile_id][$geny_activity->assignement_id][$geny_activity->task_id] = 0;
			$reporting_data[$ar->profile_id][$geny_activity->assignement_id] += $geny_activity->load;
			$reporting_data_tasks[$ar->profile_id][$geny_activity->assignement_id][$geny_activity->task_id] += $geny_activity->load;
			
			// Création des données de filtres par la même occasion
			$geny_profile->loadProfileById( $ar->profile_id );
			$geny_assignement->loadAssignementById($geny_activity->assignement_id);
			$geny_project->loadProjectById($geny_assignement->project_id);
			$geny_task->loadTaskById( $geny_activity->task_id );
			if( ! in_array(GenyTools::getProfileDisplayName($geny_profile),$data_array_filters[0]) )
				$data_array_filters[0][] = GenyTools::getProfileDisplayName($geny_profile);
			if( ! in_array($clients[$geny_project->client_id]->name,$data_array_filters[1]) )
				$data_array_filters[1][] = $clients[$geny_project->client_id]->name;
			if( ! in_array($geny_project->name,$data_array_filters[2]) )
				$data_array_filters[2][] = $geny_project->name;
			if( ! in_array($geny_task->name,$data_array_filters[3]) )
				$data_array_filters[3][] = $geny_task->name;
		}
	}
}

// Création des données de reporting pour la charge par client ainsi que par projet
$load_by_clients = array();
$load_by_projects = array();
foreach( $reporting_data as $profile_id => $data ){
	$geny_profile->loadProfileById($profile_id);
	foreach( $data as $assignement_id => $total_load ){
		$geny_assignement->loadAssignementById($assignement_id);
		$geny_project->loadProjectById($geny_assignement->project_id);
		if( !isset($load_by_clients[$clients[$geny_project->client_id]->name]) )
			$load_by_clients[$clients[$geny_project->client_id]->name]=0;
		if( !isset($load_by_projects[$clients[$geny_project->client_id]->name."/".$geny_project->name]) )
			$load_by_projects[$clients[$geny_project->client_id]->name."/".$geny_project->name]=0;
		$load_by_clients[$clients[$geny_project->client_id]->name] += $total_load/8;
		$load_by_projects[$clients[$geny_project->client_id]->name."/".$geny_project->name] += $total_load/8;
	}
}
$load_by_clients_js_data = "";
$tmp_array=array();
foreach( $load_by_clients as $client => $load ){
	$tmp_array[]= "['$client', $load]";
}
$load_by_clients_js_data = implode(",",$tmp_array);

$load_by_projects_js_data = "";
$tmp_array=array();
foreach( $load_by_projects as $project => $load ){
	$tmp_array[]= "['$project', $load]";
}
$load_by_projects_js_data = implode(",",$tmp_array);


// Création des données de reporting pour la charge par profile
$load_by_profiles = array();
foreach( $reporting_data as $profile_id => $data ){
	$geny_profile->loadProfileById($profile_id);
	$ptl=0;
	foreach( $data as $assignement_id => $total_load ){
		$ptl += $total_load;
	}
	$load_by_profiles[GenyTools::getProfileDisplayName($geny_profile)]=$ptl/8;
}
$load_by_profiles_js_data = "";
$tmp_array=array();
foreach( $load_by_profiles as $profile_sn => $load ){
	$tmp_array[]= "['$profile_sn', $load]";
}
$load_by_profiles_js_data = implode(",",$tmp_array);

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/reporting.png"/><p>Reporting</p>
</div>


<script>
	var indexData = new Array();
	<?php
		$cookie = json_decode($_COOKIE["GYMActivity_reporting_list_reporting_load_php"]);
		$html1 = '<select><option value=""></option>';
		foreach( $data_array_filters[0] as $d ){
			if( htmlspecialchars_decode(urldecode($cookie->aaSearchCols[0][0]),ENT_QUOTES) == htmlspecialchars_decode($d,ENT_QUOTES) )
				$html1 .= '<option selected="selected" value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';
			else
				$html1 .= '<option value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';
		}
		$html1 .= '</select>';
		$html2 = '<select><option value=""></option>';
		foreach( $data_array_filters[1] as $d ){
			if( htmlspecialchars_decode(urldecode($cookie->aaSearchCols[1][0]),ENT_QUOTES) == htmlspecialchars_decode($d,ENT_QUOTES) )
				$html2 .= '<option selected="selected" value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';
			else
				$html2 .= '<option value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';
		}
		$html2 .= '</select>';
		$html3 = '<select><option value=""></option>';
		foreach( $data_array_filters[2] as $d ){
			
			if( htmlspecialchars_decode(urldecode($cookie->aaSearchCols[2][0]),ENT_QUOTES) == htmlspecialchars_decode($d,ENT_QUOTES) )
				$html3 .= '<option selected="selected" value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';
			else
				$html3 .= '<option value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';
		}
		$html3 .= '</select>';
		$html4 = '<select><option value=""></option>';
		if( $aggregation_level == "tasks" ){
			foreach( $data_array_filters[3] as $d ){
				if( htmlspecialchars_decode(urldecode($cookie->aaSearchCols[3][0]),ENT_QUOTES) == htmlspecialchars_decode($d,ENT_QUOTES) )
					$html4 .= '<option selected="selected" value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';
				else
					$html4 .= '<option value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';;
			}
		}
		$html4 .= '</select>';
	?>
	indexData[0] = '<?php echo $html1; ?>';
	indexData[1] = '<?php echo $html2; ?>';
	indexData[2] = '<?php echo $html3; ?>';
	indexData[3] = '<?php echo $html4; ?>';
	
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
			/* Add a select menu for each TH element in the table footer */
			$("tfoot th").each( function ( i ) {
				if( i < <?php if($aggregation_level == "project"){echo "3";}else{echo "4";} ?> ){
					this.innerHTML = indexData[i];
					$('select', this).change( function () {
						oTable.fnFilter( $(this).val(), i );
					} );
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

		// Create the data table.
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Clients');
		data.addColumn('number', 'Charge');
		data.addRows([
		<?php echo $load_by_clients_js_data;?>
		]);

		// Set chart options
		var options = {'title':'Reporting de charge - charge/client - Entre <?php echo "$start_date" ?> et <?php echo "$end_date" ?>',
				'is3D': true,
				'width':500,
				'height':300};

		// Instantiate and draw our chart, passing in some options.
		var chart = new google.visualization.PieChart(document.getElementById('chart_div1'));
		chart.draw(data, options);
		
		// Create the data table.
		var data2 = new google.visualization.DataTable();
		data2.addColumn('string', 'Profiles');
		data2.addColumn('number', 'Charge');
		data2.addRows([
		<?php echo $load_by_profiles_js_data;?>
		]);

		// Set chart options
		var options = {'title':'Reporting de charge - charge/profil - Entre <?php echo "$start_date" ?> et <?php echo "$end_date" ?>',
				'is3D': true,
				'width':500,
				'height':300};

		// Instantiate and draw our chart, passing in some options.
		var chart2 = new google.visualization.PieChart(document.getElementById('chart_div2'));
		chart2.draw(data2, options);
		
		// Create the data table.
		var data3 = new google.visualization.DataTable();
		data3.addColumn('string', 'Profiles');
		data3.addColumn('number', 'Charge');
		data3.addRows([
		<?php echo $load_by_projects_js_data;?>
		]);

		// Set chart options
		var options = {'title':'Reporting de charge - charge/projet - Entre <?php echo "$start_date" ?> et <?php echo "$end_date" ?>',
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
	
	$(function() {
		$( "#reporting_start_date" ).datepicker();
		$( "#reporting_start_date" ).datepicker( "option", "showAnim", "slideDown" );
		$( "#reporting_start_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
		$( "#reporting_start_date" ).datepicker('setDate', <?php echo "'".$start_date."'"; ?>);
		$( "#reporting_start_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
		$( "#reporting_start_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
		$( "#reporting_start_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
		$( "#reporting_start_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
		$( "#reporting_start_date" ).datepicker( "option", "firstDay", 1 );
		$( "#reporting_end_date" ).datepicker();
		$( "#reporting_end_date" ).datepicker( "option", "showAnim", "slideDown" );
		$( "#reporting_end_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
		$( "#reporting_end_date" ).datepicker('setDate', <?php echo "'".$end_date."'"; ?>);
		$( "#reporting_end_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
		$( "#reporting_end_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
		$( "#reporting_end_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
		$( "#reporting_end_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
		$( "#reporting_end_date" ).datepicker( "option", "firstDay", 1 );
		
		$( "#reporting_start_date" ).change( function(){ $( "#reporting_end_date" ).val( $( "#reporting_start_date" ).val() ) } );
		
	});
	

</script>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="reporting_monthly_view">
			Reporting de charge
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des CRA ventilés par collaborateurs, par client et par projet pour la période sélectionnée (par défaut le mois en cours).<br/>
		Reporting des CRA entre le <strong><?php echo $start_date; ?></strong> et le <strong><?php echo $end_date; ?></strong>.<br/>
		<?php
			if( $aggregation_level == "project" )
				echo "<strong>Attention: Ces rapports excluent les congés non rémunérés !</strong>";
		?>
		</p>
		<style>
			@import 'styles/<?php echo $web_config->theme ?>/reporting_monthly_view.css';
		</style>
		<form id="formID" action="reporting_load.php" method="post">
			<p>
				<label for="reporting_start_date">Date de début</label>
				<input name="reporting_start_date" id="reporting_start_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="reporting_end_date">Date de fin</label>
				<input name="reporting_end_date" id="reporting_end_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<script>
					function setCookie( name, value )
					{
						document.cookie = name + "=" +escape( value );
					}
					function aggregationLevelChanged(){
						setCookie('GYMActivity_reporting_list_reporting_load_php_task_state', $('#reporting_aggregation_level').attr('checked'));
						$('#formID').submit();
					}
				</script>
				<input type="checkbox" id="reporting_aggregation_level" name="reporting_aggregation_level" value="tasks" onChange="aggregationLevelChanged()" <?php if($aggregation_level == "tasks"){echo "checked";} ?> /> <strong>Cochez</strong> la case pour ventiler la charge par <strong>tâche</strong>, <strong>décocher</strong> pour ventiler par <strong>projet</strong>.
			</p>
			<input type="submit" value="Ajuster le reporting" />
		</form>
		<div class="table_container">
		<p>
			
			<table id="reporting_list">
			<thead>
				<th>Collab.</th>
				<th>Client</th>
				<th>Projet</th>
				<?php
					if( $aggregation_level == "tasks" )
						echo "<th>Tâche</th>\n";
				?>
				<th>Nbr. <strong>jours</strong></th>
			</thead>
			<tbody>
			<?php
				if( $aggregation_level == "tasks" ){
					foreach( $reporting_data_tasks as $profile_id => $data ){
						$geny_profile->loadProfileById($profile_id);
						foreach( $data as $assignement_id => $tasks ){
							$geny_assignement->loadAssignementById($assignement_id);
							$geny_project->loadProjectById($geny_assignement->project_id);
							foreach ( $tasks as $task_id => $task_load ){
								$geny_task->loadTaskById( $task_id );
								echo "<tr><td>".GenyTools::getProfileDisplayName($geny_profile)."</td><td>".$clients[$geny_project->client_id]->name."</td><td>".$geny_project->name."</td><td>".$geny_task->name."</td><td>".($task_load/8)."</td></tr>";
							}
						}
					}
				}
				else {
					foreach( $reporting_data as $profile_id => $data ){
						$geny_profile->loadProfileById($profile_id);
						foreach( $data as $assignement_id => $total_load ){
							$geny_assignement->loadAssignementById($assignement_id);
							$geny_project->loadProjectById($geny_assignement->project_id);
							echo "<tr><td>".GenyTools::getProfileDisplayName($geny_profile)."</td><td>".$clients[$geny_project->client_id]->name."</td><td>".$geny_project->name."</td><td>".($total_load/8)."</td></tr>";
						}
					}
				}
			?>
			</tbody>
			<tfoot>
				<th>Collab.</th>
				<th>Client</th>
				<th>Projet</th>
				<?php
					if( $aggregation_level == "tasks" )
						echo "<th>Tâche</th>\n";
				?>
				<th>Nbr. <strong>jours</strong></th>
			</tfoot>
			</table>
		</p>
		</div>
		<p>
			<ul>
				<li id="chart_div1"></li>
				<li id="chart_div2"></li>
				<li id="chart_div3"></li>
			</ul>
		</p>
	</p>
</div>
<div id="bottomdock">
	<ul>
<!-- 		<?php include 'backend/widgets/project_add.dock.widget.php'; ?> -->
	</ul>
</div>
<?php
include_once 'footer.php';
?>

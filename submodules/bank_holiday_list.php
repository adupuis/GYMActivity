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


date_default_timezone_set('Europe/Paris');
$gritter_notifications = array();

$data_array = array();
$data_array_filters = array( 0 => array(), 1 => array(), 2 => array(), 5 => array() );

$bank_holiday = new GenyBankHoliday();
$country = new GenyCountry();
$project = new GenyProject();
$task = new GenyTask();

foreach( $country->getAllCountries() as $c ) {
	$countries[$c->id] = $c;
}

// Get all project of type "Congés".
// TODO: We should use a GenyProperty here.
foreach( $project->getProjectsByTypeId(5) as $p ) {
	$projects[$p->id] = $c;
}

foreach( $bank_holiday->getAllBankHolidays() as $tmp ) {
	GenyTools::Debug("Got Bank Holiday $tmp->id : $tmp->name\n");
	$tmp_country = $countries["$tmp->country_id"];

	if( $web_config->theme == "genymobile-2012" ) {
		$edit = "<a href=\"loader.php?module=bank_holiday_edit&load_bank_holiday=true&bank_holiday_id=$tmp->id\" title=\"Editer le jour férié\"><img src=\"images/$web_config->theme/holiday_summary_edit_small.png\" alt=\"Editer le jour férié\"></a>";

		$remove = "<a href=\"loader.php?module=bank_holiday_remove&bank_holiday_id=$tmp->id\" title=\"Supprimer définitivement le jour férié\"><img src=\"images/$web_config->theme/holiday_summary_remove_small.png\" alt=\"Supprimer définitivement le jour férié\"></a>";
	}
	else {
		$edit = "<a href=\"loader.php?module=bank_holiday_edit&load_bank_holiday=true&bank_holiday_id=$tmp->id\" title=\"Editer le jour férié\"><img src=\"images/$web_config->theme/project_edit_small.png\" alt=\"Editer le jour férié\"></a>";

		$remove = "<a href=\"loader.php?module=bank_holiday_remove&bank_holiday_id=$tmp->id\" title=\"Supprimer définitivement le jour férié\"><img src=\"images/$web_config->theme/project_remove_small.png\" alt=\"Supprimer définitivement le jour férié\"></a>";
	}
	$project->loadProjectById($tmp->project_id);
	$task->loadTaskById($tmp->task_id);
	$data_array[] = array( $tmp->id, $tmp->name, $project->name, $task->name, $tmp->start_date, $tmp->stop_date, $tmp_country->name, $edit, $remove );

// 	$holiday_summary_types = array( "CP"=>"CP", "RTT"=>"RTT" );
// 
	if( !in_array($tmp->name, $data_array_filters[0]) )
		$data_array_filters[0][] = $tmp->name;
	if( !in_array( $project->name, $data_array_filters[1] ) )
		$data_array_filters[1][] = $project->name;
    if( !in_array( $task->name, $data_array_filters[2] ) )
		$data_array_filters[2][] = $task->name;
    if( !in_array( $tmp_country->name, $data_array_filters[5] ) )
		$data_array_filters[5][] = $tmp_country->name;
}

?>
<div id="mainarea">
	<p class="mainarea_title">
	<img src="images/<?php echo $web_config->theme; ?>/holiday_summary_generic.png"></img>
		<span class="bank_holiday_list">
			Liste des jours fériés
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des jours fériés.
		</p>
		<script>
			var indexData = new Array();
			<?php
				if(array_key_exists("GYMActivity_bank_holiday_list_table_loader_php", $_COOKIE)) {
					$cookie = json_decode($_COOKIE["GYMActivity_bank_holiday_list_table_loader_php"]);
				}
				
				$data_array_filters_html = array();
				foreach( $data_array_filters as $idx => $data ) {
					$data_array_filters_html[$idx] = '<select><option value=""></option>';
					foreach( $data as $d ) {
						if( isset( $cookie ) && htmlspecialchars_decode( urldecode( $cookie->aaSearchCols[$idx][0]), ENT_QUOTES ) == htmlspecialchars_decode( $d, ENT_QUOTES ) ) {
							$data_array_filters_html[$idx] .= '<option selected="selected" value="'.htmlentities( $d, ENT_QUOTES, 'UTF-8' ).'">'.htmlentities( $d, ENT_QUOTES, 'UTF-8' ).'</option>';
						}
						else {
							$data_array_filters_html[$idx] .= '<option value="'.htmlentities( $d, ENT_QUOTES, 'UTF-8' ).'">'.htmlentities( $d, ENT_QUOTES, 'UTF-8' ).'</option>';
						}
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
				
				var oTable = $('#bank_holiday_list_table').dataTable( {
					"bDeferRender": true,
					"bJQueryUI": true,
					"bStateSave": true,
					"bAutoWidth": false,
					"sCookiePrefix": "GYMActivity_",
					"sPaginationType": "full_numbers",
					"oLanguage": {
						"sSearch": "Recherche :",
						"sLengthMenu": "Jours fériés _MENU_",
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
					},
// 					"aaSorting": [[ 5, "desc" ]]
				} );
				/* Add a select menu for each TH element in the table footer */
				/* i+1 is to avoid the first row wich contains a <input> tag without any informations */
				$("tfoot th").each( function ( i ) {
					if( i == 0 || i == 1 || i == 2 || i == 5 ) {
						this.innerHTML = indexData[i];
						$('select', this).change( function () {
							oTable.fnFilter( $(this).val(), i );
						} );
					}
				} );
			
			});
			
		</script>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
		</script>
		<form id="formID" action="loader.php?module=bank_holiday_list_apply" method="post" class="table_container">
			<style>
				@import 'styles/<?php echo $web_config->theme ?>/holiday_summary_list.css';
			</style>
			<input type="hidden" name="bank_holiday_apply_list" value="true" />
			<p>
				<table id="bank_holiday_list_table" style="color: black; width: 100%;">
					<thead>
						<th>Nom</th>
						<th>Projet (congés)</th>
						<th>Tâche (congés)</th>
						<th>Date de début</th>
						<th>Date de fin</th>
						<th>Pays concerné</th>
						<th>Editer</th>
						<th>Supprimer</th>
					</thead>
					<tbody>
					<?php
// 						foreach( $data_array as $da ){
// 							echo "<tr><td>".$da[1]."</td><td><center>".$da[2]."</center></td><td><center>".$da[3]."<center></td><td><center>".$da[4]."<center></td><td><center>".$da[5]."</center></td><td><center>".$da[6]."</center></td><td><center>".$da[7]."</center></td><td><center>".$da[8]."</center></td><td><center>".$da[9]."</center></td></tr>";
// 						}
                        foreach( $data_array as $da ){
                            echo "<tr> <td> <center>".$da[1]."</center> </td> <td> <center>".$da[2]."</center> </td> <td> <center>".$da[3]."</center> </td> <td> <center>".$da[4]."</center> </td> <td> <center>".$da[5]."</center> </td> <td> <center>".$da[6]."</center> </td> <td> <center>".$da[7]."</center> </td> <td> <center>".$da[8]."</center> </td>  </tr>";
                        }
					?>
					</tbody>
					<tfoot>
                        <th class="filtered">Nom</th>
						<th class="filtered">Projet (congés)</th>
						<th class="filtered">Tâche (congés)</th>
						<th class="filtered">Date de début</th>
						<th class="filtered">Date de fin</th>
						<th class="filtered">Pays concerné</th>
						<th class="filtered">Editer</th>
						<th class="filtered">Supprimer</th>
					</tfoot>
				</table>
			</p>
			<p>
                <input type="submit" value="Appliquer les jours fériés" />
			</p>
		</form>
	</p>
</div>

<?php
	$bottomdock_items = array('backend/widgets/bank_holiday_add.dock.widget.php');
?>

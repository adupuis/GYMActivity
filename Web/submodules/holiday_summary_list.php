<?php
//  Copyright (C) 2011 by GENYMOBILE & Quentin Désert
//  qdesert@genymobile.com
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
$data_array_filters = array( 0 => array(), 1 => array() );


$geny_holiday_summary = new GenyHolidaySummary();

$geny_profile = new GenyProfile();
foreach( $geny_profile->getAllProfiles() as $prof ) {
	$profiles[$prof->id] = $prof;
}

foreach( $geny_holiday_summary->getAllHolidaySummaries() as $tmp ) {
	
	$tmp_profile = $profiles["$tmp->profile_id"];
	if( $tmp_profile->firstname && $tmp_profile->lastname ) {
		$screen_name = $tmp_profile->firstname." ".$tmp_profile->lastname;
	}
	else {
		$screen_name = $tmp_profile->login;
	}

	if( $web_config->theme == "genymobile-2012" ) {
		$edit = "<a href=\"loader.php?module=holiday_summary_edit&load_holiday_summary=true&holiday_summary_id=$tmp->id\" title=\"Editer le solde de congés\"><img src=\"images/$web_config->theme/conges_admin_edit_small.png\" alt=\"Editer le solde de congés\"></a>";

		$remove = "<a href=\"loader.php?module=holiday_summary_remove&holiday_summary_id=$tmp->id\" title=\"Supprimer définitivement le solde de congés\"><img src=\"images/$web_config->theme/conges_admin_remove_small.png\" alt=\"Supprimer définitiement le solde de congés\"></a>";
	}
	else {
		$edit = "<a href=\"loader.php?module=holiday_summary_edit&load_holiday_summary=true&holiday_summary_id=$tmp->id\" title=\"Editer le solde de congés\"><img src=\"images/$web_config->theme/project_edit_small.png\" alt=\"Editer le solde de congés\"></a>";

		$remove = "<a href=\"loader.php?module=holiday_summary_remove&holiday_summary_id=$tmp->id\" title=\"Supprimer définitivement le solde de congés\"><img src=\"images/$web_config->theme/project_remove_small.png\" alt=\"Supprimer définitiement le solde de congés\"></a>";
	}
	
	$data_array[] = array( $tmp->id, $screen_name, $tmp->type, $tmp->period_start, $tmp->period_end, $tmp->count_acquired, $tmp->count_taken, $tmp->count_remaining, $edit, $remove );

	$holiday_summary_types = array( "CP"=>"CP", "RTT"=>"RTT" );

	if( !in_array($screen_name, $data_array_filters[0]) )
		$data_array_filters[0][] = $screen_name;
	if( !in_array( $holiday_summary_types["$tmp->type"], $data_array_filters[1] ) )
		$data_array_filters[1][] = $holiday_summary_types["$tmp->type"];
}

?>
<div id="mainarea">
	<p class="mainarea_title">
		<span class="holiday_summary_list">
			Soldes de congés
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des soldes de congés.
		</p>
		<script>
			var indexData = new Array();
			<?php
				if(array_key_exists("GYMActivity_holiday_summary_list_php", $_COOKIE)) {
					$cookie = json_decode($_COOKIE["GYMActivity_holiday_summary_list_php"]);
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
				
				var oTable = $('#holiday_summary_list_table').dataTable( {
					"bJQueryUI": true,
					"bStateSave": true,
					"bAutoWidth": false,
					"sCookiePrefix": "GYMActivity_",
					"sPaginationType": "full_numbers",
					"oLanguage": {
						"sSearch": "Recherche :",
						"sLengthMenu": "Soldes de congés par page _MENU_",
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
					if( i == 0 || i == 1 ) {
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
		<form id="formID" action="loader.php?module=holiday_summary_list" method="post" class="table_container">
			<style>
				@import 'styles/<?php echo $web_config->theme ?>/holiday_summary_list.css';
			</style>
			<p>
				<table id="holiday_summary_list_table" style="color: black; width: 100%;">
					<thead>
						<th>Profil</th>
						<th>Type</th>
						<th>Début</th>
						<th>Fin</th>
						<th>Acquis</th>
						<th>Pris</th>
						<th>Restant</th>
						<th>Editer</th>
						<th>Supprimer</th>
					</thead>
					<tbody>
					<?php
						foreach( $data_array as $da ){
							echo "<tr><td>".$da[1]."</td><td><center>".$da[2]."</center></td><td><center>".$da[3]."<center></td><td><center>".$da[4]."<center></td><td><center>".$da[5]."</center></td><td><center>".$da[6]."</center></td><td><center>".$da[7]."</center></td><td><center>".$da[8]."</center></td><td><center>".$da[9]."</center></td></tr>";
						}
					?>
					</tbody>
					<tfoot>
						<th class="filtered">Profil</th>
						<th class="filtered">Type</th>
						<th class="filtered">Début</th>
						<th class="filtered">Fin</th>
						<th class="filtered">Acquis</th>
						<th class="filtered">Pris</th>
						<th class="filtered">Restant</th>
						<th class="filtered">Editer</th>
						<th class="filtered">Supprimer</th>
					</tfoot>
				</table>
			</p>
		</form>
	</p>
</div>

<?php
	$bottomdock_items = array('backend/widgets/holiday_summary_add.dock.widget.php');
?>

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
$data_array_filters = array( 1 => array(), 2 => array(), 3 => array(), 4 => array() );


$geny_intranet_page = new GenyIntranetPage();

$geny_intranet_category = new GenyIntranetCategory();
foreach( $geny_intranet_category->getAllIntranetCategories() as $cat ) {
	$intranet_categories[$cat->id] = $cat;
}

$geny_intranet_type = new GenyIntranetType();
foreach( $geny_intranet_type->getAllIntranetTypes() as $type ) {
	$intranet_types[$type->id] = $type;
}

$geny_intranet_page_status = new GenyIntranetPageStatus();
foreach( $geny_intranet_page_status->getAllIntranetPageStatus() as $page_status ) {
	$intranet_page_statuses[$page_status->id] = $page_status;
}

$geny_profile = new GenyProfile();
foreach( $geny_profile->getAllProfiles() as $profile ) {
	$profiles[$profile->id] = $profile;
}

foreach( $geny_intranet_page->getAllIntranetPages() as $tmp ) {
	
	$intranet_category_name = $intranet_categories["$tmp->category_id"]->name;
	$intranet_type_name = $intranet_types["$tmp->type_id"]->name." (".$intranet_category_name.")";
	$intranet_page_status_name = $intranet_page_statuses["$tmp->page_status_id"]->name;
	$profile_screen_name = $profiles["$tmp->profile_id"]->firstname." ".$profiles["$tmp->profile_id"]->lastname;
	if( $profile_screen_name == '' ) {
		$profile_screen_name = $profiles["$tmp->profile_id"]->login;
	}

	$remove = "<a href=\"loader.php?module=intranet_page_remove&intranet_page_id=$tmp->id\" title=\"Supprimer définitivement la page Intranet\"><img src=\"images/$web_config->theme/intranet_page_remove_small.png\" alt=\"Supprimer définitivement la page Intranet\"></a>";
	
	$data_array[] = array( $tmp->id, $tmp->title, $intranet_category_name, $intranet_type_name, $intranet_page_status_name, $profile_screen_name, $remove );
	
	if( !in_array( $intranet_category_name, $data_array_filters[1]) )
		$data_array_filters[1][] = $intranet_category_name;
	if( !in_array( $intranet_type_name, $data_array_filters[2]) )
		$data_array_filters[2][] = $intranet_type_name;
	if( !in_array( $intranet_page_status_name, $data_array_filters[3]) )
		$data_array_filters[3][] = $intranet_page_status_name;
	if( !in_array( $profile_screen_name, $data_array_filters[4]) )
		$data_array_filters[4][] = $profile_screen_name;
}

?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/intranet_page_list.png"></img>
		<span class="intranet_page_list">
			Liste des pages Intranet
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des pages de l'Intranet.
		</p>
		<script>
			var indexData = new Array();
			<?php
				if( array_key_exists( "GYMActivity_intranet_page_list_php", $_COOKIE ) ) {
					$cookie = json_decode( $_COOKIE["GYMActivity_intranet_page_list_php"] );
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
				
				var oTable = $('#intranet_page_list_table').dataTable( {
					"bJQueryUI": true,
					"bStateSave": true,
					"bAutoWidth": false,
					"sCookiePrefix": "GYMActivity_",
					"sPaginationPage": "full_numbers",
					"oLanguage": {
						"sSearch": "Recherche :",
						"sLengthMenu": "Pages par page _MENU_",
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
					if( i == 1 || i == 2 || i == 3 || i == 4 ) {
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
				displayStatusNotifications( $gritter_notifications, $web_config->theme );
			?>
		</script>
		<form id="formID" action="loader.php?module=intranet_page_list" method="post" class="table_container">
			<style>
				@import 'styles/<?php echo $web_config->theme ?>/intranet_page_list.css';
			</style>
			<p>
				<table id="intranet_page_list_table" style="color: black; width: 100%;">
					<thead>
						<th>Titre</th>
						<th>Catégorie</th>
						<th>Type</th>
						<th>Statut</th>
						<th>Créateur</th>
						<th>Supprimer</th>
					</thead>
					<tbody>
					<?php
						foreach( $data_array as $da ){
							echo "<tr><td>".$da[1]."</td><td><center>".$da[2]."</center></td><td><center>".$da[3]."</center></td><td><center>".$da[4]."</center></td><td><center>".$da[5]."</center></td><td><center>".$da[6]."</center></td></tr>";
						}
					?>
					</tbody>
					<tfoot>
						<th class="filtered">Titre</th>
						<th class="filtered">Catégorie</th>
						<th class="filtered">Type</th>
						<th class="filtered">Statut</th>
						<th class="filtered">Créateur</th>
						<th class="filtered">Supprimer</th>
					</tfoot>
				</table>
			</p>
		</form>
	</p>
</div>

<?php
	$bottomdock_items = array();
?>

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
foreach( $geny_intranet_page_status->getAllIntranetPageStatus() as $status ) {
	$intranet_page_statuses[$status->id] = $status;
}

$geny_profile = new GenyProfile();
foreach( $geny_profile->getAllProfiles() as $tmp_profile ) {
	$profiles[$tmp_profile->id] = $tmp_profile;
}

foreach( $geny_intranet_page->getAllIntranetPages() as $tmp ) {
	
	$intranet_category_name = $intranet_categories["$tmp->intranet_category_id"]->name;
	$intranet_type_name = $intranet_types["$tmp->intranet_type_id"]->name;
	$intranet_page_status_name = $intranet_page_statuses["$tmp->status_id"]->name;
	$profile_screen_name = $profiles["$tmp->profile_id"]->firstname." ".$profiles["$tmp->profile_id"]->lastname;
	if( $profile_screen_name == '' ) {
		$profile_screen_name = $profiles["$tmp->profile_id"]->login;
	}
	
	$intranet_page_profile = new GenyProfile( $tmp->profile_id );
	
	$profile_authorized_to_view = false;
	$intranet_page_status_id = $tmp->status_id;
	if( $intranet_page_status_id == 1 ) { // brouillon
		if( $profile->rights_group_id == 1  || /* admin */
		    $profile->rights_group_id == 2  ||   /* superuser */
		    $profile->id == $tmp->profile_id ) {
			$profile_authorized_to_view = true;
		}
	}
	else if( $intranet_page_status_id == 2 ) { // visible par le groupe
		if( $profile->rights_group_id == 1  || /* admin */
		    $profile->rights_group_id == 2  ||   /* superuser */
		    $profile->rights_group_id == $intranet_page_profile->rights_group_id ) {
			$profile_authorized_to_view = true;
		}
	}
	else { // publié
		$profile_authorized_to_view = true;
	}
	
	if( $profile_authorized_to_view ) {
		$view = "<a href=\"loader.php?module=intranet_page_view&load_intranet_page=true&intranet_page_id=$tmp->id\" title=\"Visualiser la page Intranet\"><img src=\"images/$web_config->theme/intranet_page_view_small.png\" alt=\"Visualiser la page Intranet\"></a>";
	}
	else {
		$view = "";
	}

	$profile_authorized_to_edit = false;
	$intranet_page_acl_modification_type = $tmp->acl_modification_type;
	if( $intranet_page_acl_modification_type == "owner" ) {
		if( $profile->rights_group_id == 1  || /* admin */
		    $profile->rights_group_id == 2  ||   /* superuser */
		    $profile->id == $tmp->profile_id ) {
			$profile_authorized_to_edit = true;
		}
	}
	else if( $intranet_page_acl_modification_type == "group" ) {
		if( $profile->rights_group_id == 1  || /* admin */
		    $profile->rights_group_id == 2  ||   /* superuser */
		    $profile->rights_group_id == $intranet_page_profile->rights_group_id ) {
			$profile_authorized_to_edit = true;
		}
	}
	else {
		$profile_authorized_to_edit = true;
	}
	
	if( $profile_authorized_to_edit ) {
		$edit = "<a href=\"loader.php?module=intranet_page_edit&load_intranet_page=true&intranet_page_id=$tmp->id\" title=\"Editer la page Intranet\"><img src=\"images/$web_config->theme/intranet_page_edit_small.png\" alt=\"Editer la page Intranet\"></a>";
	}
	else {
		$edit = "";
	}
	
	if( $profile->rights_group_id == 1  || /* admin */
	    $profile->rights_group_id == 2  || /* superuser */
	    $profile->id == $tmp->profile_id ) /* créateur de la page */ {
		$remove = "<a href=\"loader.php?module=intranet_page_remove&intranet_page_id=$tmp->id\" title=\"Supprimer définitivement la page Intranet\"><img src=\"images/$web_config->theme/intranet_page_remove_small.png\" alt=\"Supprimer définitivement la page Intranet\"></a>";
	}
	else {
		$remove = "";
	}
	
	$data_array[] = array( $tmp->id, $tmp->title, $intranet_category_name, $intranet_type_name, $intranet_page_status_name, $profile_screen_name, $view, $edit, $remove );
	
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
					"iCookieDuration": 60*60*24*365, // 1 year
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
						<th>Sous-catégorie</th>
						<th>Statut</th>
						<th>Créateur</th>
						<th>Visualiser</th>
						<th>Editer</th>
						<th>Supprimer</th>
					</thead>
					<tbody>
					<?php
						foreach( $data_array as $da ){
							echo "<tr><td>".$da[1]."</td><td><center>".$da[2]."</center></td><td><center>".$da[3]."</center></td><td><center>".$da[4]."</center></td><td><center>".$da[5]."</center></td><td><center>".$da[6]."</center></td><td><center>".$da[7]."</center></td><td><center>".$da[8]."</center></td></tr>";
						}
					?>
					</tbody>
					<tfoot>
						<th class="filtered">Titre</th>
						<th class="filtered">Catégorie</th>
						<th class="filtered">Sous-catégorie</th>
						<th class="filtered">Statut</th>
						<th class="filtered">Créateur</th>
						<th class="filtered">Visualiser</th>
						<th class="filtered">Editer</th>
						<th class="filtered">Supprimer</th>
					</tfoot>
				</table>
			</p>
		</form>
	</p>
</div>

<?php
	$bottomdock_items = array('backend/widgets/intranet_page_add.dock.widget.php');
?>

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
// $data_array_filters = array( 0 => array(), 1 => array(), 2 => array() );


$geny_intranet_category = new GenyIntranetCategory();

// $geny_project = new GenyProject();
// foreach( $geny_project->getAllProjects() as $proj ) {
// 	$projects[$proj->id] = $proj;
// }
// 
// $geny_task = new GenyTask();
// foreach( $geny_task->getAllTasks() as $tsk ) {
// 	$tasks[$tsk->id] = $tsk;
// }
// 
// $geny_profile = new GenyProfile();
// foreach( $geny_profile->getAllProfiles() as $prof ) {
// 	$profiles[$prof->id] = $prof;
// }

foreach( $geny_intranet_category->getAllIntranetCategories() as $tmp ) {
	
// 	$project_name = $projects["$tmp->project_id"]->name;

// 	$task_name = $tasks["$tmp->task_id"]->name;

// 	$profile_name = '';
// 	if( isset( $tmp->profile_id ) ) {
// 		$tmp_profile = $profiles["$tmp->profile_id"];
// 		if( $tmp_profile->firstname && $tmp_profile->lastname ) {
// 			$profile_name = $tmp_profile->firstname." ".$tmp_profile->lastname;
// 		}
// 		else {
// 			$profile_name = $tmp_profile->login;
// 		}
// 	}

	$edit = "<a href=\"loader.php?module=intranet_category_edit&load_intranet_category=true&intranet_category_id=$tmp->id\" title=\"Editer la catégorie Intranet\"><img src=\"images/$web_config->theme/intranet_category_edit_small.png\" alt=\"Editer la catégorie Intranet\"></a>";

	$remove = "<a href=\"loader.php?module=intranet_category_remove&intranet_category_id=$tmp->id\" title=\"Supprimer définitivement la catégorie Intranet\"><img src=\"images/$web_config->theme/intranet_category_remove_small.png\" alt=\"Supprimer définitivement la catégorie Intranet\"></a>";
	
	$data_array[] = array( $tmp->id, $tmp->name, $tmp->description, $edit, $remove );
}

?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/intranet_category_list.png"></img>
		<span class="intranet_category_list">
			Liste des catégories Intranet
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des catégories de l'Intranet.
		</p>
		<script>
			var indexData = new Array();
			<?php
				if( array_key_exists( "GYMActivity_intranet_category_list_php", $_COOKIE ) ) {
					$cookie = json_decode( $_COOKIE["GYMActivity_intranet_category_list_php"] );
				}
				
// 				$data_array_filters_html = array();
// 				foreach( $data_array_filters as $idx => $data ) {
// 					$data_array_filters_html[$idx] = '<select><option value=""></option>';
// 					foreach( $data as $d ) {
// 						if( isset( $cookie ) && htmlspecialchars_decode( urldecode( $cookie->aaSearchCols[$idx][0]), ENT_QUOTES ) == htmlspecialchars_decode( $d, ENT_QUOTES ) ) {
// 							$data_array_filters_html[$idx] .= '<option selected="selected" value="'.htmlentities( $d, ENT_QUOTES, 'UTF-8' ).'">'.htmlentities( $d, ENT_QUOTES, 'UTF-8' ).'</option>';
// 						}
// 						else {
// 							$data_array_filters_html[$idx] .= '<option value="'.htmlentities( $d, ENT_QUOTES, 'UTF-8' ).'">'.htmlentities( $d, ENT_QUOTES, 'UTF-8' ).'</option>';
// 						}
// 					}
// 					$data_array_filters_html[$idx] .= '</select>';
// 				}
// 				foreach( $data_array_filters_html as $idx => $html ){
// 					echo "indexData[$idx] = '$html';\n";
// 				}
			?>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
				
				var oTable = $('#intranet_category_list_table').dataTable( {
					"bJQueryUI": true,
					"bStateSave": true,
					"bAutoWidth": false,
					"sCookiePrefix": "GYMActivity_",
					"sPaginationType": "full_numbers",
					"oLanguage": {
						"sSearch": "Recherche :",
						"sLengthMenu": "Catégorie Intranet par page _MENU_",
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
// 				/* Add a select menu for each TH element in the table footer */
// 				/* i+1 is to avoid the first row wich contains a <input> tag without any informations */
// 				$("tfoot th").each( function ( i ) {
// 					if( i == 0 || i == 1 || i == 2 ) {
// 						this.innerHTML = indexData[i];
// 						$('select', this).change( function () {
// 							oTable.fnFilter( $(this).val(), i );
// 						} );
// 					}
// 				} );
			
			});
			
		</script>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications( $gritter_notifications, $web_config->theme );
			?>
		</script>
		<form id="formID" action="loader.php?module=intranet_category_list" method="post" class="table_container">
			<style>
				@import 'styles/<?php echo $web_config->theme ?>/intranet_category_list.css';
			</style>
			<p>
				<table id="intranet_category_list_table" style="color: black; width: 100%;">
					<thead>
						<th>Nom</th>
						<th>Description</th>
						<th>Editer</th>
						<th>Supprimer</th>
					</thead>
					<tbody>
					<?php
						foreach( $data_array as $da ){
							echo "<tr><td>".$da[1]."</td><td>".$da[2]."</td><td><center>".$da[3]."</center></td><td><center>".$da[4]."</center></td></tr>";
						}
					?>
					</tbody>
					<tfoot>
						<th class="filtered">Nom</th>
						<th class="filtered">Description</th>
						<th class="filtered">Editer</th>
						<th class="filtered">Supprimer</th>
					</tfoot>
				</table>
			</p>
		</form>
	</p>
</div>

<?php
	$bottomdock_items = array('backend/widgets/intranet_category_add.dock.widget.php','backend/widgets/intranet_type_list.dock.widget.php','backend/widgets/intranet_type_add.dock.widget.php');
?>

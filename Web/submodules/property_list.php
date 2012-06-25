<?php
//  Copyright (C) 2012 by GENYMOBILE & Jean-Charles Leneveu
//  jcleneveu@genymobile.com
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

$geny_property = new GenyProperty();
$geny_property_type = new GenyPropertyType();
$geny_property_option = new GenyPropertyOption();
$geny_property_value = new GenyPropertyValue();
$tmp_property_value_string = "";
$geny_property_values = array();

?>
<script>
	jQuery(document).ready(function(){
	
		var oTable = $('#propriety_list').dataTable( {
			"bDeferRender": true,
			"bJQueryUI": true,
			"bStateSave": true,
			"bAutoWidth": false,
			"sCookiePrefix": "GYMActivity_",
			"iCookieDuration": 60*60*24*365, // 1 year
			"sPaginationType": "full_numbers",
			"oLanguage": {
				"sSearch": "Recherche :",
				"sLengthMenu": "Proprieté par page _MENU_",
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
			}
		} );
	});
</script>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/property_list.png"></img>
		<span class="property_list">
			Liste des propriétés d'administration
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des propriétés d'administration.
		</p>
		<style>
			@import 'styles/<?php echo $web_config->theme ?>/property_list.css';
		</style>
		<div class="table_container">
		<p>
			<table id="propriety_list">
				<thead>
				<tr><th>Nom</th><th>Valeur actuelle</th><th>Type</th><th>Description</th><th>Éditer</th><th>Supprimer</th></tr>
				</thead>
				<tbody>
				<?php
					foreach( $geny_property->getAllProperties() as $tmp_geny_property ) {
						$geny_property_type->loadPropertyTypeById( $tmp_geny_property->type_id );
						$geny_property_values = $tmp_geny_property->getPropertyValues();
						switch ( $geny_property_type->shortname ) {
						
							case "PROP_TYPE_BOOL" :
							case "PROP_TYPE_DATE" :
							case "PROP_TYPE_LONG_TEXT" :
							case "PROP_TYPE_SHORT_TEXT" :
								
								if( isset( $geny_property_values[0] ) ) {
									$tmp_property_value_string = $geny_property_values[0]->content;
								}
								else {
									$tmp_property_value_string = "not defined";
								}
								break;
							
							case "PROP_TYPE_LIST_SELECT" :
							case "PROP_TYPE_MULTI_SELECT" :
							
								$tmp_property_value_string = "";
								$tmp_string_formating_cpt = 0;
								foreach( $geny_property_values as $geny_property_value ) {
									if( $tmp_string_formating_cpt > 0 ) {
										$tmp_property_value_string .= " + ";
									}
									$geny_property_option->loadPropertyOptionById( intval( $geny_property_value->content ) );
									$tmp_property_value_string .= $geny_property_option->content;
									$tmp_string_formating_cpt++;
								}
							break;
							
							default:
							
							$tmp_property_value_string = "type not correctly defined";
							break;
						}
						
						echo "<tr class='centered'><td>$tmp_geny_property->name</td>";
						echo "<td>$tmp_property_value_string</td>";
						echo "<td>$geny_property_type->shortname</td>";
						echo "<td>$tmp_geny_property->label</td>";
						echo "<td><a href='loader.php?module=property_edit&load_property=true&property_id=$tmp_geny_property->id'><img src='images/$web_config->theme/property_edit_small.png'></a></td>";
						echo "<td><a href='loader.php?module=property_remove&property_id=$tmp_geny_property->id'><img src='images/$web_config->theme/property_remove_small.png'></a></td>";
						echo "</tr>";
					}
				?>
				</tbody>
			</table>
		</p>
		</div>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/property_add.dock.widget.php');
?>

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

$property = new GenyProperty();
$propertyType = new GenyPropertyType();

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
					foreach( $property->getPropertiesList() as $prop ){
						$propertyType->loadPropertyTypeById($prop->type_id);
						$vals = $prop->getPropertyValues();
						switch ($propertyType->shortname) {
						
						case "PROP_TYPE_BOOL" :
						case "PROP_TYPE_DATE" :
						case "PROP_TYPE_LONG_TEXT" :
						case "PROP_TYPE_SHORT_TEXT" :
							
							if(isset($vals[0])) $val = $vals[0]->content;
							else $val = "not defined";
							break;
						
						case "PROP_TYPE_LIST_SELECT" :
						case "PROP_TYPE_MULTI_SELECT" :
						
							$opt = new GenyPropertyOption;
							$val = "";
							$cpt=0;
						    foreach($vals as $v) {
							if($cpt > 0) $val .= " + ";
							$opt->loadPropertyOptionById(intval($v->content));
							$val .= $opt->content;
							$cpt++;
						    }
						    break;
						    
						default:
						
						    $val = "type not correctly defined";
						    break;
						}
						
						echo "<tr class='centered'><td>$prop->name</td><td>$val</td><td>$propertyType->shortname</td><td>$prop->label</td><td><a href='loader.php?module=property_edit&load_property=true&property_id=$prop->id'><img src='images/$web_config->theme/property_edit_small.png'></a></td><td><a href='loader.php?module=property_remove&property_id=$prop->id'><img src='images/$web_config->theme/property_remove_small.png'></a></td></tr>\n\t\t\t\t";
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

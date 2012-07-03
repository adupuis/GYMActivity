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

// déclaration des variables générales
$loaded_geny_property_id = -1; // identifiant de la propriété qui va être chargée dans le formulaire html
$geny_property = new GenyProperty();
$geny_property_value = new GenyPropertyValue();
$geny_property_type = new GenyPropertyType();
$geny_properties = array();
$geny_property_values = array();
$gritter_notifications = array();

// chargement des actions à effectuer sur la propriété
$param_action_create_property = GenyTools::getParam( 'create_property', 'false' );
$param_action_load_property = GenyTools::getParam( 'load_property', 'false' );
$param_action_edit_property = GenyTools::getParam( 'edit_property', 'false' );

// chargement des informations de la propriété à créer/éditer/charger
$param_property_name = GenyTools::getParam( 'property_name', '' );
$param_property_value = GenyTools::getParam( 'property_value', '' );
$param_property_label = GenyTools::getParam( 'property_label', '' );
$param_property_type = GenyTools::getParam( 'property_type', -1 );
$param_property_id = GenyTools::getParam( 'property_id', -1 );

// si la propriété a de multiples valeurs, on homogénéise le nom de variable associé
if( intval( $param_property_type ) == 2 ) {
	$param_property_values = $param_property_value;
	unset( $param_property_value );
}
// si à l'inverse, des valeurs multiples sont données en paramètre alors que
// la propriété ne peut prendre qu'une valeur, on supprime l'ambiguité
else if( is_array( $param_property_value ) ) {
	$param_property_value = "";
}


// Cas n°1 : création d'une nouvelle propriété (et de sa valeur associée)
if( $param_action_create_property == "true" ) {
	if( $param_property_name != "" && $param_property_label != "" && $param_property_type != -1 && is_numeric( $param_property_type ) ) {
		$loaded_geny_property_id = intval( $geny_property->insertNewProperty( $param_property_name, $param_property_label, $param_property_type ) );
		if( $loaded_geny_property_id != GENYMOBILE_FALSE ) {
			if( $geny_property_value->insertNewPropertyValue( "", $loaded_geny_property_id ) != GENYMOBILE_FALSE ) {
				$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Propriété créée avec succès.','msg'=>"La propriété a été correctement créée." );
			}
			else {
				$gritter_notifications[] = array( 'status'=>'warning', 'title' => 'Erreur lors de la création de la valeur de la propriété','msg'=>"La propriété a été correctement créé mais la valeur rattachée à cette propriété n'a pas pu être crée." );
			}
		}
		else{
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur.','msg'=>"Erreur lors de la création de la propriété." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur.','msg'=>"Certains champs obligatoires sont manquant ou mal renseignés. Merci de les remplir." );
	}
}


// Cas n°2 : chargement d'une propriété
else if( $param_action_load_property == "true" ) {
	if( $param_property_id != -1 && is_numeric( $param_property_id ) ) {
		$loaded_geny_property_id = intval( $param_property_id );
	}
	else  {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Chargement impossible','msg'=>"Impossible de charger la propriété : id non spécifié ou non correctement typé." );
	}
}


// Cas n°3 : édition d'une propriété
else if( $param_action_edit_property == "true" ) {
	
	if( $param_property_id != -1 && is_numeric( $param_property_id ) ) {
		
		// chargement de la propriété
		$loaded_geny_property_id = intval( $param_property_id );
		$geny_property->loadPropertyById( $loaded_geny_property_id );
		
		// par défaut, on considère qu'on n'a pas touché aux valeurs de la propriété
		$are_geny_property_values_edited = false;
		$are_geny_property_values_successfully_updated = false;
		
		// édition du nom
		if( $param_property_name != "" && $geny_property->name != $param_property_name ) {
			$geny_property->updateString( 'property_name', $param_property_name );
		}
		
		// édition du label
		if( $param_property_label != "" && $geny_property->label != $param_property_label ) {
			$geny_property->updateString( 'property_label', $param_property_label );
		}
		
		// édition du type (WARNING : le type est un int)
		if( $param_property_type != -1 && $geny_property->type_id != $param_property_type && is_numeric( $param_property_type ) ) {
			$geny_property->updateInt( 'property_type_id', intval( $param_property_type ) );
		}
		
		// édition de la ou les valeur(s) de la propriété
		
		// récupération des valeurs et du type de valeurs
		$geny_property_values = $geny_property->getPropertyValues();
		if( $param_property_type != -1 && is_numeric( $param_property_type ) ) {
			$geny_property_type_id = intval( $param_property_type );
		}
		else {
			$geny_property_type_id = intval( $geny_property->type_id );
		}
		
		// les valeurs sont gérées différemment en fonction du type
		switch ( $geny_property_type_id ) {
		
			case 1: // BOOL
				
				$geny_property->setNumberOfPropertyValues( 1 );
				$geny_property_values = $geny_property->getPropertyValues();
				$geny_property_value = $geny_property_values[0];
				
				// on détermine la nouvelle valeur (true ou false) de la propriété
				if( $param_property_value == "0" || $param_property_value == "false" ) {
					$new_geny_property_value = "false";
				}
				else {
					$new_geny_property_value = "true";
				}
				
				// si la nouvelle valeur est différente de l'ancienne, on la met à jour
				if( $geny_property_value->content != $new_geny_property_value ) {
					$are_geny_property_values_edited = true;
					$geny_property_value->updateString( 'property_value_content', $new_geny_property_value );
					if( $geny_property_value->commitUpdates() ) {
						$are_geny_property_values_successfully_updated = true;
						$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Valeur éditée','msg'=>"Valeur éditée avec succès" );
					}
					else {
						$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Modification impossible','msg'=>"Erreur lors de la modification d'une valeur" );
					}
				}
				break;
				
			case 2: // SELECT MULTIPLE
			
				// on règle le nombre de valeurs en fonction du tableau transmis en paramètre
				$geny_property->setNumberOfPropertyValues( count( $param_property_values ) );
				$geny_property_values = $geny_property->getPropertyValues();
				
				// par défaut, on considère que tout est bien
				$are_geny_property_values_successfully_updated = true;
				$are_geny_property_values_edited = true;
				
				$tmp_property_value_id_cpt = 0;
					
				if( is_array( $param_property_values ) ) {
					foreach( $param_property_values as $tmp_property_value ) {
						if( is_numeric( $tmp_property_value ) &&  $tmp_property_value != "") {
							$geny_property_values[$tmp_property_value_id_cpt]->updateString( 'property_value_content', $tmp_property_value );
							if( ! $geny_property_values[$tmp_property_value_id_cpt]->commitUpdates() ) {
								$are_geny_property_values_successfully_updated = false;
							}
						}
						else {
							$param_property_values[$tmp_property_value_id_cpt]->deletePropertyValue();
						}
						$tmp_property_value_id_cpt++;
					}
				}
				
				if( $are_geny_property_values_successfully_updated ) {
					$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Valeur(s) éditée(s)','msg'=>"Valeur(s) éditée(s) avec succès" );
				}
				else {
					$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Modification impossible','msg'=>"Erreur lors de la modification d'au moins une valeur" );
				}				
				break;
			
			case 3: // SELECT
			
				$geny_property->setNumberOfPropertyValues( 1 );
				$geny_property_values = $geny_property->getPropertyValues();
				$geny_property_value = $geny_property_values[0];
				
				if( is_numeric( $param_property_value ) && $param_property_value != $geny_property_value->content && $param_property_value != "" ) {
					$are_geny_property_values_edited = true;
					$geny_property_value->updateString( 'property_value_content', $param_property_value );
					if( $geny_property_value->commitUpdates() ) {
						$are_geny_property_values_successfully_updated = true;
						$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Valeur éditée','msg'=>"Valeur éditée avec succès" );
					}
					else {
						$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Modification impossible','msg'=>"Erreur lors de la modification d'une valeur" );
					}
				}
				break;
			
			case 4; // SHORT_TEXT
			case 5; // TEXTAREA
				
				$geny_property->setNumberOfPropertyValues( 1 );
				$geny_property_values = $geny_property->getPropertyValues();
				$geny_property_value = $geny_property_values[0];
				
				if( $param_property_value != $geny_property_value->content ) {
					$are_geny_property_values_edited = true;
					$geny_property_value->updateString( 'property_value_content', $param_property_value );
					if( $geny_property_value->commitUpdates() ) {
						$are_geny_property_values_successfully_updated = true;
						$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Valeur éditée','msg'=>"Valeur éditée avec succès" );
					}
					else {
						$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Modification impossible','msg'=>"Erreur lors de la modification d'une valeur" );
					}
				}
				break;
			
			case 6; // DATE
				
				$geny_property->setNumberOfPropertyValues( 1 );
				$geny_property_values = $geny_property->getPropertyValues();
				$geny_property_value = $geny_property_values[0];
				
				if( checkdate( substr( $param_property_value, 5, 2 ), substr( $param_property_value, 8, 2 ), substr( $param_property_value, 0, 4 ) ) ) {
					if( $param_property_value != $geny_property_value->content ) {
						$are_geny_property_values_edited = true;
						$geny_property_value->updateString( 'property_value_content', $_POST['property_value'] );
						if( $geny_property_value->commitUpdates() ) {
							$are_geny_property_values_successfully_updated = true;
							$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Valeur éditée','msg'=>"Valeur éditée avec succès" );
						}
						else {
							$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Modification impossible','msg'=>"Erreur lors de la modification d'une valeur" );
						}
					}
				}
				else {
					$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Modification impossible','msg'=>"La valeur insérée ne respecte pas le format d'une date valide" );
				}
				break;
			
			default: // problème : le type spécifié ne correspond à aucun type connu
				$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur : mauvais type de propriété" );
				break;
			
		}
		
		if( $geny_property->commitUpdates() || ( $are_geny_property_values_edited && $are_geny_property_values_successfully_updated ) ) {
			$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"Propriété mise à jour avec succès." );
		}
		elseif ( !$are_geny_property_values_edited ) {
			$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Rien à mettre à jour','msg'=>"Aucun champ n'a été modifié" );
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour de la propriété." );
		}
	}
	else  {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Modification impossible','msg'=>"Impossible de modifier la propriété : id non spécifié." );
	}
}

// chargement de la propriété dont on veut afficher les options d'édition (+ valeurs et options rattachées)
if( $loaded_geny_property_id < 0 ) {
	$geny_properties = $geny_property->getAllProperties();
	if( count( $geny_properties > 0 ) ) {
		$loaded_geny_property_id = $geny_properties[0]->id;
	}
}
$geny_property->loadPropertyById( $loaded_geny_property_id );
$geny_property_type->loadPropertyTypeById( $geny_property->type_id );
$geny_property_values = $geny_property->getPropertyValues();
if( is_array( $geny_property_values ) && isset( $geny_property_values[0] ) ) {
	$geny_property_value = $geny_property_values[0];
}
else {
	$geny_property_value = new GenyPropertyValue();
}

?>

<div id="mainarea">
	<style>
		@import 'styles/<?php echo $web_config->theme ?>/property_edit.css';
	</style>
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/property_edit.png">
		<span class="property_edit">
			Modifier une Propriété
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de modifier une propriété d'administration.
		</p>
		
		<form id="select_property_form" action="loader.php?module=property_edit" method="post">
			<input type="hidden" name="load_property" value="true" />
			<p>
				<label for="property_id">Sélection propriété</label>

				<select name="property_id" id="property_id" onChange="submit()" class="chzn-select">
					<?php	// on affiche toutes les propriétés et on sélectionne celui qui a été passé en paramètre
						foreach( $geny_property->getAllProperties() as $tmp_geny_property ){
							if( $loaded_geny_property_id == $tmp_geny_property->id )
								echo "<option value=\"".$tmp_geny_property->id."\" selected>".$tmp_geny_property->name."</option>\n";
							else
								echo "<option value=\"".$tmp_geny_property->id."\">".$tmp_geny_property->name."</option>\n";
						}
					?>
				</select>
			</p>
		</form>

		<form id="edit_property_form" action="loader.php?module=property_edit" method="post" name="edit_property_form">
			<input type="hidden" name="edit_property" value="true" />
			<input type="hidden" name="property_id" value="<?php echo $loaded_geny_property_id ?>" />
			<p>
				<label for="property_name">Name</label>
				<input name="property_name" id="property_name" type="text" class="validate[required] text-input" value="<?php echo $geny_property->name ?>"/>
			</p>
			
			<p>
				<label for="property_label">Label</label>
				<input name="property_label" id="property_label" type="text" class="validate[required] text-input" value="<?php echo $geny_property->label ?>"/>
			</p>
			
			<p>
				<label for="property_type">Type</label>
				<select name="property_type" class="chzn-select" id="property_type" class="validate[required] select-input">
					<?php	// on affiche tous les types possibles
						foreach( $geny_property_type->getAllPropertyTypes() as $tmp_property_type ) {
							echo '<option value="' . $tmp_property_type->id . '"';
							if( $tmp_property_type->id == $geny_property->type_id ) {
								echo ' selected ';
							}
							echo '>' . $tmp_property_type->name . '</option>';
						}
					?>
				</select>
			</p>

			<p>
				<label for="property_value">Valeur</label>
				<?php	// en fonction du type de propriété, on affiche les inputs différemments
					switch( $geny_property_type->shortname )
					{
						case "PROP_TYPE_BOOL":
							if( $geny_property_value->content == 'true' ) {
								$is_true_option_selected = "selected";
								$is_false_option_selected = "";
							}
							else {
								$is_true_option_selected = "";
								$is_false_option_selected = "selected";
							}
							echo '<select name="property_value" class="chzn-select" id="property_value" class="validate[required] select-input">';
							echo '<option value="1" ' . $is_true_option_selected . ' >true</option>';
							echo '<option value="0" ' . $is_false_option_selected . ' >false</option>';
							echo "</select>";
							break;
						
						case "PROP_TYPE_SHORT_TEXT":
							echo '<input name="property_value" id="property_value" type="text" class="validate[required] text-input" value="' . $geny_property_value->content . '"/>';
							break;
						
						case "PROP_TYPE_DATE":
							echo '<input name="property_value" id="property_value" type="text" class="validate[required,custom[date]] text-input" value="' . $geny_property_value->content . '"/>';
							break;
						
						case "PROP_TYPE_LONG_TEXT":
							echo '<textarea name="property_value" id="property_value" type="text" class="validate[required] text-input">' . $geny_property_value->content . '</textarea>';
							break;

						case "PROP_TYPE_MULTI_SELECT":
							$is_multiple_select = array( 'multiple="multiple"', '[]' );
						
						case "PROP_TYPE_LIST_SELECT":
							if( !isset( $is_multiple_select ) ) {
								$is_multiple_select = array( '', '' );
							}
							
							echo '<select ' . $is_multiple_select[0] . ' name="property_value'. $is_multiple_select[1] . '" class="chzn-select" id="property_value" class="validate[required] select-input">';
							foreach( $geny_property->getPropertyOptions() as $tmp_property_option ) {
								$is_option_selected = "";
								foreach( $geny_property_values as $geny_property_value ) {
									if( $geny_property_value->content == $tmp_property_option->id ) {
										$is_option_selected = "selected";
									}
								}
								echo '<option value="' . $tmp_property_option->id . '"' . $is_option_selected . '>' . $tmp_property_option->content . '</option>';
							}
							echo "</select>";
							echo '<input type="button" id="delete_property_option_button" value="Supprimer" onClick="deletePropertyOption(\''.$web_config->theme.'\')">';
							echo "</p>";
							
							
							echo "<p>";
							echo '<label for="property_option_add">Ajout d\'option</label>';
							echo '<input type="text" id="add_property_option_label" name="add_property_option_label" id="add_property_option_label">';
							echo '<input id="add_property_option_button" class="button" type="button" value="Ajouter" onClick="addPropertyOption(\''.$web_config->theme.'\')">';
							echo "</p>";
							
							break;
						
						default:
							$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Type inconnu','msg'=>"Type de valeur non reconnu." );
							break;
					}
				?>
			</p>
			
			<p>
				<input type="submit" value="Modifier"/> ou <a href="loader.php?module=property_list">annuler</a>
			</p>
			
		</form>
	</p>
	
	<script type="text/javascript">
	
		<?php
			// si on a une date, on utilise datepicker
			if( $geny_property_type->id == 6 ) {
		?>
				$(function () {
					$("#property_value").datepicker();
					$("#property_value").datepicker( "option", "showAnim", "slideDown" );
					$("#property_value").datepicker( "option", "dateFormat", "yy-mm-dd" );
					$("#property_value").datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$("#property_value").datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$("#property_value").datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$("#property_value").datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$("#property_value").show();
					$("#property_value").val("<?php echo $geny_property_value->content; ?>");
				});
		<?php
			}
			displayStatusNotifications( $gritter_notifications, $web_config->theme );
		?>
	</script>
		<?php
			// si on est en présence d'un input de type "select", on rajoute les fonctions js
			// pour le rajout où la suppression d'option à la volée
			if( $geny_property_type->id == 2 || $geny_property_type->id == 3 ) {
				echo '<script type="text/javascript" src="js/manage_property_option.js"></script>';
			}
		?>

	
	
	
	
</div>
<?php
	$bottomdock_items = array( 'backend/widgets/notifications.dock.widget.php','backend/widgets/property_list.dock.widget.php','backend/widgets/property_add.dock.widget.php' );
?>

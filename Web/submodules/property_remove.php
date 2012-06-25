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
$geny_property = new GenyProperty();
$geny_property_value = new GenyPropertyValue();
$gritter_notifications = array();

// chargement des paramètres
$param_action_force_remove = GenyTools::getParam('force_remove', "false");
$param_action_remove_property = GenyTools::getParam('remove_property', "false");
$param_geny_property_id = GenyTools::getParam('property_id', -1);

if( $param_action_remove_property == "true" ) {
	if( $param_geny_property_id != -1 && is_numeric( $param_geny_property_id ) ) {
		if( $param_action_force_remove == "true" ) {
		
			$geny_property->loadPropertyById( $param_geny_property_id );
		
			if( $geny_property->deleteProperty() ) {
				$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Propriété supprimée avec succès.");
			}
			else {
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression de la propriété de la table Properties.");
			}
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours.");
		}
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de supprimer la propriété ','msg'=>"id non spécifié.");
	}
}

?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/property_remove.png"></img>
		<span class="property_remove">
			Supprimer une propriété
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de <strong>supprimer définitivement</strong> une propriété d'administration de la base de donnée.
		</p>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications( $gritter_notifications, $web_config->theme );
			?>
		</script>
		<script>
			jQuery(document).ready(function(){
				$("#select_login_form").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#select_login_form").validationEngine('attach');
			});
			
		</script>
		<form id="select_login_form" action="loader.php?module=property_remove" method="post">
			<input type="hidden" name="remove_property" value="true" />
			<p>
				<label for="property_id">Sélection propriété</label>

				<select name="property_id" id="property_id" class="chzn-select">
					<?php
						foreach( $geny_property->getAllProperties() as $geny_property ) {
							if( $param_geny_property_id == $geny_property->id ) {
								echo '<option value="' . $geny_property->id . '" selected>' . $geny_property->name . '</option>\n';
							}
							else {
								echo '<option value="' . $geny_property->id . '">' . $geny_property->name . '</option>\n';
							}
						}
					?>
				</select>
			</p>
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression de la propriété. <strong>La suppression est définitive et ne pourra pas être annulée. La suppression d'une propriété entraîne la suppression de toutes les valeurs et de toutes les options rattachées à cette propriété !</strong>
			</p>
			<p>
				<input type="submit" value="Supprimer" /> ou <a href="loader.php?module=property_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/property_list.dock.widget.php','backend/widgets/property_add.dock.widget.php');
?>

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


$gritter_notifications = array();
$properties = new GenyProperty();

$handle = mysql_connect($web_config->db_host,$web_config->db_user,$web_config->db_password);
mysql_select_db($web_config->db_name);
mysql_query("SET NAMES 'utf8'");

if( isset($_POST['remove_property']) && $_POST['remove_property'] == "true" ){
	if(isset($_POST['property_id'])){
		if( isset($_POST['force_remove']) && $_POST['force_remove'] == "true" ){
		
			$properties->loadPropertyById($id);
		
			// si on a des listes (multiple ou non), on supprime les options rattachées
			if($properties->type_id == 2 || $properties->type_id == 3)
			{
				 $opt = new GenyPropertyOption;
				foreach($properties->getPropertyValues() as $v) {
					$opt->deletePropertyOption(intval($v->content));
				}
			}
			
			// on supprime la ou les valeurs rattachées à la propriétée
			$vals = $properties->getPropertyValues();
			foreach($vals as $val) {
				$val->deletePropertyValue(0);
			}
		
			// enfin, on suprime la propriétée en tant que telle
			$id = mysql_real_escape_string($_POST['property_id']);
			$query = "DELETE FROM Properties WHERE property_id=$id";
			if(! mysql_query($query)){
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression de la propriété de la table Properties.");
			}
			else
				$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Propriété supprimée avec succès.");
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours.");
		}
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de supprimer la propriété ','msg'=>"id non spécifié.");
	}
}
else{
// 	$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Aucune action spécifiée.");
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
				displayStatusNotifications($gritter_notifications,$web_config->theme);
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
						foreach( $properties->getPropertiesList() as $prop ){
							if( (isset($_POST['property_id']) && $_POST['property_id'] == $prop->id) || (isset($_GET['property_id']) && $_GET['property_id'] == $prop->id) )
								echo "<option value=\"".$prop->id."\" selected>".$prop->name."</option>\n";
							else if( isset($_POST['property_name']) && $_POST['property_name'] == $prop->name )
								echo "<option value=\"".$prop->id."\" selected>".$prop->name."</option>\n";
							else
								echo "<option value=\"".$prop->id."\">".$prop->name."</option>\n";
						}
					?>
				</select>
			</p>
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression de la propriété. <strong>La suppression est définitive et ne pourra pas être annulée.</strong>
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

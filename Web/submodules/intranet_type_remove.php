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

$gritter_notifications = array();

$geny_intranet_type = new GenyIntranetType();
$geny_intranet_category = new GenyIntranetCategory();

$handle = mysql_connect( $web_config->db_host, $web_config->db_user, $web_config->db_password );
mysql_select_db( $web_config->db_name );
mysql_query( "SET NAMES 'utf8'" );

$remove_intranet_type = GenyTools::getParam( 'remove_intranet_type', 'NULL' );
if( $remove_intranet_type == "true" ) {
	$intranet_type_id = GenyTools::getParam( 'intranet_type_id', 'NULL' );
	if( $intranet_type_id != 'NULL' ) {
		$force_remove_intranet_type = GenyTools::getParam( 'force_remove', 'NULL' );
		if( $force_remove_intranet_type == "true" ) {
			$id = GenyTools::getParam( 'intranet_type_id', 'NULL' );
			$geny_intranet_type->loadIntranetTypeById( $id );
			if( $profile->rights_group_id == 1 /* admin */       ||
			    $profile->rights_group_id == 2 /* superuser */)  {
				$query = "DELETE FROM IntranetTypes WHERE intranet_type_id=$id";
				if( !mysql_query( $query ) ) {
					$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression de la sous-catégorie Intranet de la table IntranetTypes." );
				}
				else
					$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"Sous-catégorie Intranet supprimée avec succès." );
			}
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => "Impossible de supprimer la sous-catégorie Intranet",'msg'=>"id non spécifié." );
	}
}
else {
// 	$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Aucune action spécifiée.");
}


?>

<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/intranet_type_remove.png"></img>
		<span class="intranet_type_remove">
			Supprimer sous-catégorie Intranet
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de <strong>supprimer définitivement</strong> une sous-catégorie Intranet.
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
		<form id="select_login_form" action="loader.php?module=intranet_type_remove" method="post">
			<input type="hidden" name="remove_intranet_type" value="true" />
			<p>
				<label for="intranet_type_id">Sélection sous-catégorie</label>

				<select name="intranet_type_id" id="intranet_type_id" class="chzn-select">
					<?php
					$intranet_types = $geny_intranet_type->getAllIntranetTypes();
					$intranet_type_id = GenyTools::getParam( 'intranet_type_id', 'NULL' );

					foreach( $intranet_types as $intranet_type ) {
						
						foreach( $geny_intranet_category->getAllIntranetCategories() as $cat ) {
							if( $intranet_type->intranet_category_id == $cat->id ) {
								$intranet_category_name = $cat->name;
							}
						}
						
						if( $intranet_type_id == $intranet_type->id ) {
							echo "<option value=\"".$intranet_type->id."\" selected>".$intranet_type->name." (".$intranet_category_name.")"."</option>\n";
						}
						else {
							echo "<option value=\"".$intranet_type->id."\">".$intranet_type->name." (".$intranet_category_name.")"."</option>\n";
						}
					}
					if( $geny_intranet_type->id < 0 ) {
						$geny_intranet_type->loadIntranetTypeById( $intranet_types[0]->id );
					}
					?>
				</select>
			</p>
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression de la sous-catégorie Intranet. <strong>La suppression est définitive et ne pourra pas être annulée.</strong>
			</p>
			<p>
				<input type="submit" value="Supprimer" /> ou <a href="loader.php?module=intranet_type_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/intranet_type_list.dock.widget.php','backend/widgets/intranet_type_add.dock.widget.php');
?>

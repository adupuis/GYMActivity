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

$geny_intranet_category = new GenyIntranetCategory();

$handle = mysql_connect( $web_config->db_host, $web_config->db_user, $web_config->db_password );
mysql_select_db( $web_config->db_name );
mysql_query( "SET NAMES 'utf8'" );

$remove_intranet_category = GenyTools::getParam( 'remove_intranet_category', 'NULL' );
if( $remove_intranet_category == "true" ) {
	$intranet_category_id = GenyTools::getParam( 'intranet_category_id', 'NULL' );
	if( $intranet_category_id != 'NULL' ) {
		$force_remove_intranet_category = GenyTools::getParam( 'force_remove', 'NULL' );
		if( $force_remove_intranet_category == "true" ) {
			$id = GenyTools::getParam( 'intranet_category_id', 'NULL' );
			$geny_intranet_category->loadIntranetCategoryById( $id );
			if( $profile->rights_group_id == 1 /* admin */       ||
			    $profile->rights_group_id == 2 /* superuser */)  {
				$query = "DELETE FROM IntranetCategories WHERE intranet_category_id=$id";
				if( !mysql_query( $query ) ) {
					$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression de la catégorie Intranet de la table IntranetCategories." );
				}
				else
					$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"catégorie Intranet supprimée avec succès." );
			}
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => "Impossible de supprimer la catégorie Intranet",'msg'=>"id non spécifié." );
	}
}
else {
// 	$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Aucune action spécifiée.");
}


?>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="intranet_category_remove">
			Supprimer une catégorie Intranet
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de <strong>supprimer définitivement</strong> une catégorie Intranet.
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
		<form id="select_login_form" action="loader.php?module=intranet_category_remove" method="post">
			<input type="hidden" name="remove_intranet_category" value="true" />
			<p>
				<label for="intranet_category_id">Sélection catégorie Intranet</label>

				<select name="intranet_category_id" id="intranet_category_id" class="chzn-select">
					<?php
					$intranet_categories = $geny_intranet_category->getAllIntranetCategories();
					$intranet_category_id = GenyTools::getParam( 'intranet_category_id', 'NULL' );

					foreach( $intranet_categories as $intranet_category ) {
						if( $intranet_category_id == $intranet_category->id ) {
							echo "<option value=\"".$intranet_category->id."\" selected>".$intranet_category->name."</option>\n";
						}
						else {
							echo "<option value=\"".$intranet_category->id."\">".$intranet_category->name."</option>\n";
						}
					}
					if( $geny_intranet_category->id < 0 ) {
						$geny_intranet_category->loadIntranetCategoryById( $intranet_categories[0]->id );
					}
					?>
				</select>
			</p>
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression de la catégorie Intranet. <strong>La suppression est définitive et ne pourra pas être annulée.</strong>
			</p>
			<p>
				<input type="submit" value="Supprimer" /> ou <a href="loader.php?module=intranet_category_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/intranet_category_list.dock.widget.php','backend/widgets/intranet_category_add.dock.widget.php');
?>

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

$create_intranet_category = GenyTools::getParam( 'create_intranet_category', 'NULL' );
$load_intranet_category = GenyTools::getParam( 'load_intranet_category', 'NULL' );
$edit_intranet_category = GenyTools::getParam( 'edit_intranet_category', 'NULL' );

if( $create_intranet_category == "true" ) {
	$intranet_category_name = GenyTools::getParam( 'intranet_category_name', 'NULL' );
	$intranet_category_description = GenyTools::getParam( 'intranet_category_description', 'NULL' );

	if( $intranet_category_name != 'NULL' && $intranet_category_description != 'NULL' ) {
		$insert_id = $geny_intranet_category->insertNewIntranetCategory( 'NULL', $intranet_category_name, $intranet_category_description );
		if( $insert_id != -1 ) {
			$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"Catégorie Intranet ajoutée avec succès." );
			$geny_intranet_category->loadIntranetCategoryById( $insert_id );
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout de la catégorie Intranet." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir." );
	}
}
else if( $load_intranet_category == 'true' ) {
	$intranet_category_id = GenyTools::getParam( 'intranet_category_id', 'NULL' );
	if( $intranet_category_id != 'NULL' ) {
// 		$tmp_geny_intranet_category = new GenyIntranetCategory();
// 		$tmp_geny_intranet_category->loadIntranetCategoryById( $intranet_category_id );
		if( $profile->rights_group_id == 1  || /* admin */
		    $profile->rights_group_id == 2     /* superuser */ ) {
			$geny_intranet_category->loadIntranetCategoryById( $intranet_category_id );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger la catégorie Intranet",'msg'=>"Vous n'êtes pas autorisé.");
			header( 'Location: error.php?category=intranet_category&backlinks=intranet_category_list,intranet_category_add' );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Impossible de charger la catégorie Intranet','msg'=>"id non spécifié." );
	}
}
else if( $edit_intranet_category == 'true' ) {
	$intranet_category_id = GenyTools::getParam( 'intranet_category_id', 'NULL' );
	if( $intranet_category_id != 'NULL' ) {
		$geny_intranet_category->loadIntranetCategoryById( $intranet_category_id );
		
		if( $profile->rights_group_id == 1 /* admin */       ||
		    $profile->rights_group_id == 2 /* superuser */ ) {

			$intranet_category_name = GenyTools::getParam( 'intranet_category_name', 'NULL' );
			$intranet_category_description = GenyTools::getParam( 'intranet_category_description', 'NULL' );

			if( $intranet_category_name != 'NULL' && $geny_intranet_category->name != $intranet_category_name ) {
				$geny_intranet_category->updateString( 'intranet_category_name', $intranet_category_name );
			}
			if( $intranet_category_description != 'NULL' && $geny_intranet_category->description != $intranet_category_description ) {
				$geny_intranet_category->updateString( 'intranet_category_description', $intranet_category_description );
			}
		}
		if( $geny_intranet_category->commitUpdates() ) {
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Catégorie Intranet mise à jour avec succès.");
			$geny_intranet_category->loadIntranetCategoryById( $intranet_category_id );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour de la catégorie Intranet.");
		}
	}
}

?>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="intranet_category_edit">
			Modifier catégorie Intranet
		</span>
	</p>
	<p class="mainarea_content">

		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications( $gritter_notifications, $web_config->theme );
			?>
		</script>

		<p class="mainarea_content_intro">
		Ce formulaire permet d'éditer une catégorie Intranet existante. Tous les champs doivent être remplis.
		</p>
		
		<form id="select_intranet_category_form" action="loader.php?module=intranet_category_edit" method="post">
			<input type="hidden" name="load_intranet_category" value="true" />
			<p>
				<label for="intranet_category_id">Sélection catégorie Intranet</label>

				<select name="intranet_category_id" id="intranet_category_id" class="chzn-select" onChange="submit()">
					<?php
						$intranet_categories = $geny_intranet_category->getAllIntranetCategories();

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
		</form>
		<form id="formID" action="loader.php?module=intranet_category_edit" method="post">
			<input type="hidden" name="edit_intranet_category" value="true" />
			<input type="hidden" name="intranet_category_id" value="<?php echo $geny_intranet_category->id ?>" />
			
			<p>
				<label for="intranet_category_name">Nom</label>
				<input name="intranet_category_name" id="intranet_category_name" type="text" value="<?php echo $geny_intranet_category->name ?>" class="validate[required,length[2,25]] text-input" maxlength="25"/>
			</p>
			<p>
				<label for="intranet_category_description">Description</label>
				<textarea name="intranet_category_description" id="intranet_category_description" class="validate[required,length[2,140]] text-input" maxlength="140"><?php echo $geny_intranet_category->description ?></textarea>
			</p>

			<p>
				<input type="submit" value="Modifier" /> ou <a href="loader.php?module=intranet_category_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/intranet_category_list.dock.widget.php','backend/widgets/intranet_category_add.dock.widget.php');
?>

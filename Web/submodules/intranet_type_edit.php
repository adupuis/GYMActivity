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

$create_intranet_type = GenyTools::getParam( 'create_intranet_type', 'NULL' );
$load_intranet_type = GenyTools::getParam( 'load_intranet_type', 'NULL' );
$edit_intranet_type = GenyTools::getParam( 'edit_intranet_type', 'NULL' );

if( $create_intranet_type == "true" ) {
	$intranet_type_name = GenyTools::getParam( 'intranet_type_name', 'NULL' );
	$intranet_type_description = GenyTools::getParam( 'intranet_type_description', 'NULL' );
	$intranet_category_id = GenyTools::getParam( 'intranet_category_id', 'NULL' );

	if( $intranet_type_name != 'NULL' && $intranet_type_description != 'NULL' && $intranet_category_id != 'NULL' ) {
		$insert_id = $geny_intranet_type->insertNewIntranetType( 'NULL', $intranet_type_name, $intranet_type_description, $intranet_category_id );
		if( $insert_id != -1 ) {
			$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"Type de catégorie Intranet ajoutée avec succès." );
			$geny_intranet_type->loadIntranetTypeById( $insert_id );
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout du type de catégorie Intranet." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir." );
	}
}
else if( $load_intranet_type == 'true' ) {
	$intranet_type_id = GenyTools::getParam( 'intranet_type_id', 'NULL' );
	if( $intranet_type_id != 'NULL' ) {
		if( $profile->rights_group_id == 1  || /* admin */
		    $profile->rights_group_id == 2     /* superuser */ ) {
			$geny_intranet_type->loadIntranetTypeById( $intranet_type_id );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger le type de catégorie Intranet",'msg'=>"Vous n'êtes pas autorisé.");
			header( 'Location: error.php?category=intranet_type&backlinks=intranet_type_list,intranet_type_add' );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Impossible de charger le type de catégorie Intranet','msg'=>"id non spécifié." );
	}
}
else if( $edit_intranet_type == 'true' ) {
	$intranet_type_id = GenyTools::getParam( 'intranet_type_id', 'NULL' );
	if( $intranet_type_id != 'NULL' ) {
		$geny_intranet_type->loadIntranetTypeById( $intranet_type_id );
		
		if( $profile->rights_group_id == 1 /* admin */       ||
		    $profile->rights_group_id == 2 /* superuser */ ) {

			$intranet_type_name = GenyTools::getParam( 'intranet_type_name', 'NULL' );
			$intranet_type_description = GenyTools::getParam( 'intranet_type_description', 'NULL' );
			$intranet_category_id = GenyTools::getParam( 'intranet_category_id', 'NULL' );

			if( $intranet_type_name != 'NULL' && $geny_intranet_type->name != $intranet_type_name ) {
				$geny_intranet_type->updateString( 'intranet_type_name', $intranet_type_name );
			}
			if( $intranet_type_description != 'NULL' && $geny_intranet_type->description != $intranet_type_description ) {
				$geny_intranet_type->updateString( 'intranet_type_description', $intranet_type_description );
			}
			if( $intranet_category_id != 'NULL' && $geny_intranet_type->category_id != $intranet_category_id ) {
				$geny_intranet_type->updateInt( 'intranet_category_id', $intranet_category_id );
			}
		}
		if( $geny_intranet_type->commitUpdates() ) {
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Type de catégorie Intranet mis à jour avec succès.");
			$geny_intranet_type->loadIntranetTypeById( $intranet_type_id );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour du type de catégorie Intranet.");
		}
	}
}

?>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="intranet_type_edit">
			Modifier type de catégorie Intranet
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
		Ce formulaire permet d'éditer un type de catégorie Intranet existant. Tous les champs doivent être remplis.
		</p>
		
		<form id="select_intranet_type_form" action="loader.php?module=intranet_type_edit" method="post">
			<input type="hidden" name="load_intranet_type" value="true" />
			<p>
				<label for="intranet_type_id">Sélection type</label>

				<select name="intranet_type_id" id="intranet_type_id" class="chzn-select" onChange="submit()">
					<?php
						$intranet_types = $geny_intranet_type->getAllIntranetTypes();
						
						foreach( $intranet_types as $intranet_type ) {
							
							foreach( $geny_intranet_category->getAllIntranetCategories() as $cat ) {
								if( $intranet_type->category_id == $cat->id ) {
									$intranet_category_name = $cat->name;
								}
							}
							
							if( $geny_intranet_type->id == $intranet_type->id ) {
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
		</form>
		<form id="formID" action="loader.php?module=intranet_type_edit" method="post">
			<input type="hidden" name="edit_intranet_type" value="true" />
			<input type="hidden" name="intranet_type_id" value="<?php echo $geny_intranet_type->id ?>" />
			
			<p>
				<label for="intranet_category_id">Catégorie</label>
				<select name="intranet_category_id" id="intranet_category_id" class="chzn-select">
					<?php
						foreach( $geny_intranet_category->getAllIntranetCategories() as $intranet_category ) {
							if( $geny_intranet_type->category_id == $intranet_category->id ) {
								echo "<option value=\"".$intranet_category->id."\" selected>".$intranet_category->name."</option>\n";
							}
							else {
								echo "<option value=\"".$intranet_category->id."\">".$intranet_category->name."</option>\n";
							}
						}
					?>
				</select>
			</p>
			<p>
				<label for="intranet_type_name">Nom</label>
				<input name="intranet_type_name" id="intranet_type_name" type="text" value="<?php echo $geny_intranet_type->name ?>" class="validate[required,length[2,25]] text-input" maxlength="25"/>
			</p>
			<p>
				<label for="intranet_type_description">Description</label>
				<textarea name="intranet_type_description" id="intranet_type_description" class="validate[required,length[2,140]] text-input" maxlength="140"><?php echo $geny_intranet_type->description ?></textarea>
			</p>

			<p>
				<input type="submit" value="Modifier" /> ou <a href="loader.php?module=intranet_type_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/intranet_type_list.dock.widget.php','backend/widgets/intranet_type_add.dock.widget.php');
?>

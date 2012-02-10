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

$geny_intranet_tag = new GenyIntranetTag();

$create_intranet_tag = GenyTools::getParam( 'create_intranet_tag', 'NULL' );
$load_intranet_tag = GenyTools::getParam( 'load_intranet_tag', 'NULL' );
$edit_intranet_tag = GenyTools::getParam( 'edit_intranet_tag', 'NULL' );

if( $create_intranet_tag == "true" ) {
	$intranet_tag_name = GenyTools::getParam( 'intranet_tag_name', 'NULL' );
	
	if( $intranet_tag_name != 'NULL' ) {
		$insert_id = $geny_intranet_tag->insertNewIntranetTag( 'NULL', $intranet_tag_name );
		if( $insert_id != -1 ) {
			$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"Tag Intranet créé avec succès." );
			$geny_intranet_tag->loadIntranetTagById( $insert_id );
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de la création du tag Intranet." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir." );
	}
}
else if( $load_intranet_tag == 'true' ) {
	$intranet_tag_id = GenyTools::getParam( 'intranet_tag_id', 'NULL' );
	if( $intranet_tag_id != 'NULL' ) {
		if( $profile->rights_group_id == 1  || /* admin */
		    $profile->rights_group_id == 2     /* superuser */ ) {
			$geny_intranet_tag->loadIntranetTagById( $intranet_tag_id );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger le tag Intranet",'msg'=>"Vous n'êtes pas autorisé.");
			header( 'Location: error.php?category=intranet_tag&backlinks=intranet_tag_list' );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Impossible de charger le tag Intranet','msg'=>"id non spécifié." );
	}
}
else if( $edit_intranet_tag == 'true' ) {
	$intranet_tag_id = GenyTools::getParam( 'intranet_tag_id', 'NULL' );
	if( $intranet_tag_id != 'NULL' ) {
		$geny_intranet_tag->loadIntranetTagById( $intranet_tag_id );
		
		if( $profile->rights_group_id == 1 /* admin */       ||
		    $profile->rights_group_id == 2 /* superuser */ ) {

			$intranet_tag_name = GenyTools::getParam( 'intranet_tag_name', 'NULL' );

			if( $intranet_tag_name != 'NULL' && $geny_intranet_tag->name != $intranet_tag_name ) {
				$geny_intranet_tag->updateString( 'intranet_tag_name', $intranet_tag_name );
			}
		}
		if( $geny_intranet_tag->commitUpdates() ) {
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Tag Intranet mis à jour avec succès.");
			$geny_intranet_tag->loadIntranetTagById( $intranet_tag_id );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour du tag Intranet.");
		}
	}
}

?>

<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/intranet_tag_edit.png"></img>
		<span class="intranet_tag_edit">
			Modifier tag Intranet
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
		Ce formulaire permet d'éditer un tag Intranet existant. Tous les champs doivent être remplis.
		</p>
		
		<form id="select_intranet_tag_form" action="loader.php?module=intranet_tag_edit" method="post">
			<input type="hidden" name="load_intranet_tag" value="true" />
			<p>
				<label for="intranet_tag_id">Sélection tag</label>

				<select name="intranet_tag_id" id="intranet_tag_id" class="chzn-select" onChange="submit()">
					<?php
						$intranet_tags = $geny_intranet_tag->getAllIntranetTagsOrderByName();
						
						foreach( $intranet_tags as $intranet_tag ) {
							
							if( $geny_intranet_tag->id == $intranet_tag->id ) {
								echo "<option value=\"".$intranet_tag->id."\" selected>".$intranet_tag->name."</option>\n";
							}
							else {
								echo "<option value=\"".$intranet_tag->id."\">".$intranet_tag->name."</option>\n";
							}
						}
						if( $geny_intranet_tag->id < 0 ) {
							$geny_intranet_tag->loadIntranetTagById( $intranet_tags[0]->id );
						}
					?>
				</select>
			</p>
		</form>
		<form id="formID" action="loader.php?module=intranet_tag_edit" method="post">
			<input type="hidden" name="edit_intranet_tag" value="true" />
			<input type="hidden" name="intranet_tag_id" value="<?php echo $geny_intranet_tag->id ?>" />
			
			<p>
				<label for="intranet_tag_name">Nom</label>
				<input name="intranet_tag_name" id="intranet_tag_name" type="text" value="<?php echo $geny_intranet_tag->name ?>" class="validate[required,length[2,25]] text-input" maxlength="25"/>
			</p>
			
			<p>
				<input type="submit" value="Modifier" /> ou <a href="loader.php?module=intranet_tag_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/intranet_tag_list.dock.widget.php','backend/widgets/intranet_tag_add.dock.widget.php');
?>

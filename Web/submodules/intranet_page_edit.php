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

$geny_intranet_page = new GenyIntranetPage();
$geny_intranet_category = new GenyIntranetCategory();

$create_intranet_page = GenyTools::getParam( 'create_intranet_page', 'NULL' );
$load_intranet_page = GenyTools::getParam( 'load_intranet_page', 'NULL' );
$edit_intranet_page = GenyTools::getParam( 'edit_intranet_page', 'NULL' );

if( $create_intranet_page == "true" ) {
	$intranet_page_title = GenyTools::getParam( 'intranet_page_title', 'NULL' );
	$intranet_category_id = GenyTools::getParam( 'intranet_category_id', 'NULL' );
	$intranet_type_id = GenyTools::getParam( 'intranet_type_id', 'NULL' );
	$intranet_page_description = GenyTools::getParam( 'intranet_page_description', 'NULL' );
	$intranet_page_content = GenyTools::getParam( 'intranet_page_content_editor', 'NULL' );

	if( $intranet_page_title != 'NULL' && $intranet_category_id && $intranet_type_id && $intranet_page_description != 'NULL' && $intranet_page_content != 'NULL' ) {
		$insert_id = $geny_intranet_page->insertNewIntranetPage( 'NULL', $intranet_page_title, $intranet_category_id, $intranet_type_id, $intranet_page_description, $intranet_page_content );
		if( $insert_id != -1 ) {
			$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"Page Intranet créée avec succès." );
			$geny_intranet_page->loadIntranetPageById( $insert_id );
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de la création de la page Intranet." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir." );
	}
}
else if( $load_intranet_page == 'true' ) {
	$intranet_page_id = GenyTools::getParam( 'intranet_page_id', 'NULL' );
	if( $intranet_page_id != 'NULL' ) {
// 		if( $profile->rights_group_id == 1  || /* admin */
// 		    $profile->rights_group_id == 2     /* superuser */ ) {
			$geny_intranet_page->loadIntranetPageById( $intranet_page_id );
// 		}
// 		else {
// 			$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger la page Intranet",'msg'=>"Vous n'êtes pas autorisé.");
// 			header( 'Location: error.php?category=intranet_page' );
// 		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Impossible de charger la page Intranet','msg'=>"id non spécifié." );
	}
}
else if( $edit_intranet_page == 'true' ) {
	$intranet_page_id = GenyTools::getParam( 'intranet_page_id', 'NULL' );
	if( $intranet_page_id != 'NULL' ) {
		$geny_intranet_page->loadIntranetPageById( $intranet_page_id );
		
// 		if( $profile->rights_group_id == 1 /* admin */       ||
// 		    $profile->rights_group_id == 2 /* superuser */ ) {

			$intranet_page_title = GenyTools::getParam( 'intranet_page_title', 'NULL' );
			$intranet_category_id = GenyTools::getParam( 'intranet_category_id', 'NULL' );
			$intranet_type_id = GenyTools::getParam( 'intranet_type_id', 'NULL' );
			$intranet_page_description = GenyTools::getParam( 'intranet_page_description', 'NULL' );
			$intranet_page_content = GenyTools::getParam( 'intranet_page_content_editor', 'NULL' );

			if( $intranet_page_title != 'NULL' && $geny_intranet_page->title != $intranet_page_title ) {
				$geny_intranet_page->updateString( 'intranet_page_title', $intranet_page_title );
			}
			if( $intranet_category_id != 'NULL' && $geny_intranet_page->category_id != $intranet_category_id ) {
				$geny_intranet_page->updateInt( 'intranet_category_id', $intranet_category_id );
			}
			if( $intranet_type_id != 'NULL' && $geny_intranet_page->type_id != $intranet_type_id ) {
				$geny_intranet_page->updateInt( 'intranet_type_id', $intranet_type_id );
			}
			if( $intranet_page_description != 'NULL' && $geny_intranet_page->description != $intranet_page_description ) {
				$geny_intranet_page->updateString( 'intranet_page_description', $intranet_page_description );
			}
			if( $intranet_page_content != 'NULL' && $geny_intranet_page->content != $intranet_page_content ) {
				$geny_intranet_page->updateString( 'intranet_page_content', gzcompress( $intranet_page_content ) );
			}
// 		}
		if( $geny_intranet_page->commitUpdates() ) {
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Page Intranet mise à jour avec succès.");
			$geny_intranet_page->loadIntranetPageById( $intranet_page_id );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour de la page Intranet.");
		}
	}
}

?>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="intranet_page_view">
			Page Intranet
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
		Ce formulaire permet d'éditer une page Intranet existante. Tous les champs doivent être remplis.
		</p>
		
		<form id="select_intranet_page_form" action="loader.php?module=intranet_page_edit" method="post">
			<input type="hidden" name="load_intranet_page" value="true" />
			<p>
				<label for="intranet_page_id">Sélection page Intranet</label>

				<select name="intranet_page_id" id="intranet_page_id" class="chzn-select" onChange="submit()">
					<?php
						$intranet_pages = $geny_intranet_page->getAllIntranetPages();
						
						foreach( $intranet_pages as $intranet_page ) {
							if( $geny_intranet_page->id == $intranet_page->id ) {
								echo "<option value=\"".$intranet_page->id."\" selected>".$intranet_page->title."</option>\n";
							}
							else {
								echo "<option value=\"".$intranet_page->id."\">".$intranet_page->title."</option>\n";
							}
						}
						if( $geny_intranet_page->id < 0 ) {
							$geny_intranet_page->loadIntranetPageById( $intranet_pages[0]->id );
						}
					?>
				</select>
			</p>
		</form>
		<form id="formID" action="loader.php?module=intranet_page_edit" method="post">
			<input type="hidden" name="edit_intranet_page" value="true" />
			<input type="hidden" name="intranet_page_id" value="<?php echo $geny_intranet_page->id ?>" />
			
			<p>
				<label for="intranet_category_id">Catégorie</label>
				<select name="intranet_category_id" id="intranet_category_id" class="chzn-select">
					<?php
						foreach( $geny_intranet_category->getAllIntranetCategories() as $intranet_category ) {
							if( $geny_intranet_page->category_id == $intranet_category->id ) {
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
				<label for="intranet_type_id">Type</label>
				<select name="intranet_type_id" id="intranet_type_id" class="chzn-select" data-placeholder="Choisissez d'abord une catégorie...">
					<option value=""></option>
				</select>
			</p>
			
			<script type="text/javascript">

				function getIntranetTypes(){
					var intranet_category_id = $("#intranet_category_id").val();
					if( intranet_category_id > 0 ) {
						var intranet_type_id = <?php echo $geny_intranet_page->type_id ?>;
						
						$.get('backend/api/get_intranet_type_list.php?category_id='+intranet_category_id, function( data ) {
							$('.intranet_types_options').remove();
							$.each( data, function( key, val ) {
								if( val["id"] == intranet_type_id ) {
									$("#intranet_type_id").append('<option class="intranet_types_options" value="' + val["id"] + '" title="' + val["id"] + '" selected>' + val["name"] + '</option>');
								}
								else {
									$("#intranet_type_id").append('<option class="intranet_types_options" value="' + val["id"] + '" title="' + val["id"] + '">' + val["name"] + '</option>');
								}
							});
							$("#intranet_type_id").attr('data-placeholder','Choisissez un type...');
							$("#intranet_type_id").trigger("liszt:updated");
							$("span:contains('Choisissez d'abord une catégorie...')").text('Choisissez un type...');

						},'json');
					}
				}
				$("#intranet_category_id").change( getIntranetTypes );
				getIntranetTypes();
				
			</script>
			
			<p>
				<label for="intranet_page_title">Titre</label>
				<input name="intranet_page_title" id="intranet_page_title" type="text" value="<?php echo $geny_intranet_page->title ?>" class="validate[required,length[2,25]] text-input" maxlength="25"/>
			</p>
			<p>
				<label for="intranet_page_description">Description courte</label>
				<textarea name="intranet_page_description" id="intranet_page_description" class="validate[required,length[2,140]] text-input" maxlength="140"><?php echo $geny_intranet_page->description ?></textarea>
			</p>
			
			<p>
				<textarea id="intranet_page_content_editor" name="intranet_page_content_editor"><?php echo $geny_intranet_page->content ?></textarea>
			</p>
			<script type="text/javascript">
				CKEDITOR.replace( 'intranet_page_content_editor' );
			</script>
			
			<p>
				<input type="submit" value="Sauvegarder" /> ou <a href="loader.php?module=intranet_page_edit">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/intranet_page_add.dock.widget.php');
?>

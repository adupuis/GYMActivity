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

$geny_intranet_category = new GenyIntranetCategory();
$geny_intranet_type = new GenyIntranetType();
$geny_intranet_page_status = new GenyIntranetPageStatus();

?>

<style>
	@import "styles/genymobile-2012/chosen_override.css";
</style>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="intranet_page_add">
			Ajouter page Intranet
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter une page Intranet. Tous les champs doivent être remplis.
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			$(document).ready(function(){
				$(".categorieslistselect").listselect({listTitle: "Catégories disponibles",selectedTitle: "Catégories sélectionnées"});
			});
		</script>
		<form id="formID" action="loader.php?module=intranet_page_edit" method="post">
			<input type="hidden" name="create_intranet_page" value="true" />
			
			<p>
				<label for="intranet_category_id">Catégorie</label>
				<select name="intranet_category_id" id="intranet_category_id" class="chzn-select" data-placeholder="Choisissez une catégorie...">
					<option value=""></option>
					<?php
						foreach( $geny_intranet_category->getAllIntranetCategories() as $intranet_category ) {
							echo "<option value=\"".$intranet_category->id."\">".$intranet_category->name."</option>\n";
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
			<p>
				<label for="intranet_tag_id">Tags</label>
				<select name="intranet_tag_id[]" id="intranet_tag_id" multiple class="chzn-select" data-placeholder="Choisissez un ou plusieurs tags...">
				</select>
			</p>
			<p>
				<label for="intranet_page_status_id">Statut</label>
				<select name="intranet_page_status_id" id="intranet_page_status_id" class="chzn-select" data-placeholder="Choisissez un statut...">
					<option value=""></option>
					<?php
						foreach( $geny_intranet_page_status->getAllIntranetPageStatus() as $intranet_page_status ) {
							echo "<option value=\"".$intranet_page_status->id."\">".$intranet_page_status->name." - ".$intranet_page_status->description."</option>\n";
						}
					?>
				</select>
			</p>
			
			<script type="text/javascript">

				function getIntranetTypes(){
					var intranet_category_id = $("#intranet_category_id").val();
					if( intranet_category_id > 0 ) {
						$.get('backend/api/get_intranet_type_list.php?intranet_category_id='+intranet_category_id, function( data ) {
							$('.intranet_types_options').remove();
							$.each( data, function( key, val ) {
								$("#intranet_type_id").append('<option class="intranet_types_options" value="' + val["id"] + '" title="' + val["id"] + '">' + val["name"] + '</option>');
							});
							$("#intranet_type_id").attr('data-placeholder','Choisissez un type...');
							$("#intranet_type_id").trigger("liszt:updated");
							$("span:contains('Choisissez d'abord une catégorie...')").text('Choisissez un type...');

						},'json');
					}
				}
				$("#intranet_category_id").change( getIntranetTypes );
				getIntranetTypes();
				
				function getIntranetTags(){
					$.get('backend/api/get_intranet_tag_list.php', function( data ) {
						$('.intranet_tags_options').remove();
						$.each( data, function( key, val ) {
							$("#intranet_tag_id").append('<option class="intranet_tags_options" value="' + val["id"] + '" title="' + val["id"] + '">' + val["name"] + '</option>');
						});
						$("#intranet_tag_id").attr('data-placeholder','Choisissez un ou plusieurs tags...');
						$("#intranet_tag_id").trigger("liszt:updated");

					},'json');
				}
				getIntranetTags();
				
			</script>
			
			<p>
				<label for="intranet_page_title">Titre</label>
				<input name="intranet_page_title" id="intranet_page_title" type="text" class="validate[required,length[2,25]] text-input" maxlength="25"/>
			</p>
			<p>
				<label for="intranet_page_description">Description courte</label>
				<textarea name="intranet_page_description" id="intranet_page_description" class="validate[required,length[2,140]] text-input" maxlength="140"></textarea>
			</p>
			
			<p>
				<textarea id="intranet_page_content_editor" name="intranet_page_content_editor"></textarea>
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
	$bottomdock_items = array();
?>

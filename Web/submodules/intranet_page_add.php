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
		<img src="images/<?php echo $web_config->theme; ?>/intranet_page_add.png"></img>
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
				<label for="intranet_type_id">Sous-catégorie</label>
				<select name="intranet_type_id" id="intranet_type_id" class="chzn-select" data-placeholder="Choisissez d'abord une catégorie...">
					<option value=""></option>
				</select>
			</p>
			<p style="width:550px">
				<label for="intranet_tag_id">Tags</label>
				<select name="intranet_tag_id[]" id="intranet_tag_id" multiple class="chzn-select" data-placeholder="Choisissez/ajoutez des tags..." style="width:360px">
				</select>
				<a href='#create_intranet_tag' rel='prettyPhoto[create_intranet_tag]' class="submit" style="margin:0;float:right">+</a>
			</p>
			<p>
				<label for="intranet_page_acl_modification_type">Modification Page</label>
				<select name="intranet_page_acl_modification_type" id="intranet_page_acl_modification_type" class="chzn-select" data-placeholder="Choisissez qui peut modifier la page...">
					<option value=""></option>
					<option value="owner">Créateur de la page</option>
					<option value="group">Membres du groupe du créateur de la page</option>
					<option value="all">Tout le monde</option>
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
							$("#intranet_type_id").attr('data-placeholder','Choisissez une sous-catégorie...');
							$("#intranet_type_id").trigger("liszt:updated");
							$("span:contains('Choisissez d'abord une catégorie...')").text('Choisissez une sous-catégorie...');

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
						$("#intranet_tag_id").attr('data-placeholder','Choisissez/ajoutez des tags...');
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

<!-- Formulaire de création d'un tag -->
<div id='create_intranet_tag' style="display:none">
	<script>
		$(function() {
			var availableTags = [
				<?php
					$tags = '';
					$intranet_tag = new GenyIntranetTag();
					foreach( $intranet_tag->getAllIntranetTagsOrderByName() as $tag ) {
						$tags .= '"'.$tag->name.'",';
					}
					echo rtrim( $tags, "," );
				?>
			];
			console.log( availableTags );
			$("#intranet_tag_name").autocomplete({
				source: availableTags
			});
		});
	</script>
	<form id="form_intranet_tag_add" class="popup" style="margin:0">
		<p>
			<label for="intranet_tag_name">Nom</label>
			<input name="intranet_tag_name" id="intranet_tag_name" type="text" class="text-input" maxlength="25" style="text-transform:lowercase"/>
		</p>
		<p>
			<a href="#" id="submit_tag" class="submit">Créer</a> <a href="#" id="close_popup" onclick="$.prettyPhoto.close()" class="submit" >Annuler</a>
		</p>
	</form>
</div>

<script>
$("a[rel='prettyPhoto[create_intranet_tag]']").prettyPhoto({modal: 'true',animation_speed:'fast',slideshow:false, hideflash: true, social_tools: '<div class="pp_social" id="status_message_display"></div>', theme: 'pp_default', default_width: 700, keyboard_shortcuts: false});

$(document).on("click", "div#pp_full_res #submit_tag", function(){
	var intranet_tag_name = $("div#pp_full_res #intranet_tag_name").val();
	if( intranet_tag_name == "" ) {
		alert( "Vous devez saisir un nom." );
	}
	else {
		console.log("About to send AJAX request");
		jQuery.get("backend/api/create_intranet_tag.php?name="+encodeURIComponent(intranet_tag_name), function(data){
			console.log("Back from AJAX, processing");
			console.log("status="+data.status);
			console.log("status_message="+data.status_message);
			$(".pp_social #status_message_display").empty();
			if( data.status == "success" ) {
				$("div#pp_full_res #intranet_tag_name").val("");
				var intranet_tag_id = $("#intranet_tag_id").attr('value');
				console.log("intranet_tag_id="+intranet_tag_id);
				$("#intranet_tag_id").append('<option class="tasks_options" value="' + data.id + '">' + data.name + '</option>');
				$("#intranet_tag_id").trigger("liszt:updated");
				$(".pp_social #status_message_display").append("<strong style='color: green;'>"+data.status_message+"</strong>");
				$("div#pp_full_res #close_popup").empty();
				$("div#pp_full_res #close_popup").append("Fermer");
			}
			else {
				$(".pp_social #status_message_display").append("<strong style='color: red;'>"+data.status_message+"</strong>");
			}
		},"json");
	}
} );

$("#pp_full_res #form_intranet_tag_add").validationEngine('init');
// binds form submission and fields to the validation engine
$("##pp_full_res #form_intranet_tag_add").validationEngine('attach');

</script>

<?php
	$bottomdock_items = array('backend/widgets/intranet_page_list.dock.widget.php');
?>

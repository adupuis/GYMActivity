<?php

$geny_intranet_category = new GenyIntranetCategory();
$geny_intranet_type = new GenyIntranetType();
$geny_intranet_tag = new GenyIntranetTag();
$geny_intranet_page = new GenyIntranetPage();

?>

<style>
	@import "styles/genymobile-2012/home_intranet.css";
</style>

<div id="home_intranet_dock">

	<p id="home_intranet_tag_list">
		<label for="intranet_tag_select">Tags</label>
		<select name="intranet_tag_select[]" id="intranet_tag_select" multiple class="tag_list chzn-select" data-placeholder="Choisissez un ou plusieurs tags...">
		</select>
	</p>
	<p id="home_intranet_category_list" style="display:none">
		<label for="intranet_category_select">Catégories</label>
		<select name="intranet_category_select" id="intranet_category_select" class="category_type_list chzn-select">
			<option value="0"></option>
			<?php
				foreach( $geny_intranet_category->getAllIntranetCategories() as $intranet_category ) {
					echo "<option value=\"".$intranet_category->id."\">".$intranet_category->name."</option>\n";
				}
			?>
		</select>
	</p>
	<p id="home_intranet_type_list" style="display:none">
		<label for="intranet_type_select">Sous-catégories</label>
		<select name="intranet_type_select" id="intranet_type_select" class="category_type_list chzn-select">
			<option value="0"></option>
		</select>
	</p>

	<div id="home_intranet_categories">
		<div class="home_intranet_separator">Catégories</div>
		<ul>
			<?php
				$geny_intranet_category = new GenyIntranetCategory();
				foreach( $geny_intranet_category->getAllIntranetCategories() as $intranet_category ) {
					$filename = "images/genymobile-2012/".html_entity_decode( $intranet_category->image_name ).".png";
					if( !file_exists( $filename ) ) {
						$filename = "images/genymobile-2012/intranet_category_generic.png";
					}
									
					echo "<li class=\"widget\">";
					echo "<a class=\"widget_link\" href=# style=\"background-image: url(".$filename.")\" onclick=\"displayIntranetTypeWidgets( ".$intranet_category->id.");updateIntranetCategoryList( ".$intranet_category->id.");\">";
					echo "<span class=\"dock_item_title\">".$intranet_category->name."</span><br/>";
					echo "<span class=\"dock_item_content\">".$intranet_category->description."</span>";
					echo "</a>";
					echo "</li>\n";
				}
			?>
		</ul>
	</div>
	
	<div id="home_intranet_types" style="display:none">
		<div class="home_intranet_separator">Sous-catégories</div>
		<ul>
			<?php
				foreach( $geny_intranet_type->getAllIntranetTypes() as $intranet_type ) {
					$intranet_category = new GenyIntranetCategory( $intranet_type->intranet_category_id );
					$filename = "images/genymobile-2012/".html_entity_decode( $intranet_category->image_name ).".png";
					if( !file_exists( $filename ) ) {
						$filename = "images/genymobile-2012/intranet_category_generic.png";
					}
					
					echo "<li class=\"widget intranet_type type_intranet_category_".$intranet_category->id."\">";
					echo "<a class=\"widget_link\" href=# style=\"background-image: url(".$filename.")\" onclick=\"displayIntranetPageWidgets( ".$intranet_type->id.");updateIntranetTypeList( ".$intranet_type->id.");\">";
					echo "<span class=\"dock_item_title\">".$intranet_type->name."</span><br/>";
					echo "<span class=\"dock_item_content\">".$intranet_type->description."</span>";
					echo "</a>";
					echo "</li>\n";
				}
			?>
		</ul>
	</div>
	
	<div id="home_intranet_pages" style="display:none">
		<div class="home_intranet_separator">Pages</div>
		<ul>
			<?php
				foreach( $geny_intranet_page->getAllIntranetPages() as $intranet_page ) {
					$intranet_category = new GenyIntranetCategory( $intranet_page->intranet_category_id );
					$intranet_type = new GenyIntranetType( $intranet_page->intranet_type_id );
					$filename = "images/genymobile-2012/".html_entity_decode( $intranet_category->image_name ).".png";
					if( !file_exists( $filename ) ) {
						$filename = "images/genymobile-2012/intranet_category_generic.png";
					}
					
					$intranet_tag_list = $geny_intranet_tag->getIntranetTagsByPage( $intranet_page->id );
					$page_intranet_tag_classes = "";
					foreach( $intranet_tag_list as $tag ) {
						$page_intranet_tag_classes .= " page_intranet_tag_".$tag->id;
					}
					$page_intranet_tag_names = "<br/>";
					foreach( $intranet_tag_list as $tag ) {
						$page_intranet_tag_names .= " ".$tag->name;
					}
					
					echo "<li class=\"widget intranet_page page_intranet_category_".$intranet_category->id." page_intranet_type_".$intranet_type->id.$page_intranet_tag_classes."\">";
					echo "<a class=\"widget_link\" href=\"loader.php?module=intranet_page_view&load_intranet_page=true&intranet_page_id=".$intranet_page->id."\" style=\"background-image: url(".$filename.")\">";
					echo "<span class=\"dock_item_title\">".$intranet_page->title."</span><br/>";
					echo "<span class=\"dock_item_content\">".$intranet_page->description."</span>";
					echo "<span class=\"dock_item_content\">".$page_intranet_tag_names."</span>";
					echo "</a>";
					echo "</li>\n";
				}
			?>
		</ul>
	</div>
</div>

<script type="text/javascript">
	
	getIntranetTags();
	$("#intranet_tag_select").change( intranetTagChanged );
	$("#intranet_category_select").change( intranetCategoryChanged );
	$("#intranet_type_select").change( intranetTypeChanged );
	
	function getIntranetTags() {
		console.log( 'function getIntranetTags' );
		$.get('backend/api/get_intranet_tag_list.php', function( data ) {
			$('.intranet_tags_options').remove();
			$.each( data, function( key, val ) {
				$("#intranet_tag_select").append('<option class="intranet_tags_options" value="' + val["id"] + '" title="' + val["id"] + '">' + val["name"] + '</option>');
			});
			$("#intranet_tag_select").attr('data-placeholder','Choisissez un ou plusieurs tags...');
			$("#intranet_tag_select").trigger("liszt:updated");

		},'json');
	}
	
	function getIntranetTypes( selected_intranet_type_id ) {
		console.log( 'function getIntranetTypes' );
		var intranet_category_id = $("#intranet_category_select").val();
		console.log( '[getIntranetTypes] intranet_category_id: '+intranet_category_id );
		console.log( '[getIntranetTypes] selected_intranet_type_id: '+selected_intranet_type_id );
		if( intranet_category_id > 0 ) {
			$.get('backend/api/get_intranet_type_list.php?intranet_category_id='+intranet_category_id, function( data ) {
				$('.intranet_types_options').remove();
				$.each( data, function( key, val ) {
					if( val["id"] == selected_intranet_type_id ) {
						$("#intranet_type_select").append('<option class="intranet_types_options" value="' + val["id"] + '" title="' + val["id"] + '" selected>' + val["name"] + '</option>');
					}
					else {
						$("#intranet_type_select").append('<option class="intranet_types_options" value="' + val["id"] + '" title="' + val["id"] + '">' + val["name"] + '</option>');
					}
				});
				$("#intranet_type_select").trigger("liszt:updated");

			},'json');
		}
	}
	
	function updateIntranetCategoryList( intranet_category_id ) {
		console.log( 'function updateIntranetCategoryList' );
		$("#intranet_category_select").val( intranet_category_id );
		$("#intranet_category_select").trigger("liszt:updated");
		$('#home_intranet_category_list').show();
	}
	
	function updateIntranetTypeList( intranet_type_id ) {
		console.log( 'function updateIntranetTypeList' );
		getIntranetTypes( intranet_type_id );
		console.log( '[updateIntranetTypeList] intranet_type_id: '+intranet_type_id );
		$('#home_intranet_type_list').show();
	}
	
	function intranetCategoryChanged() {
		console.log( 'function intranetCategoryChanged' );
		$('#intranet_type_select').val(0);
		var intranet_type_id = $("#intranet_type_select").val();
		console.log( '--intranet_type_id: '+intranet_type_id );
		$('#home_intranet_type_list').hide();
		var intranet_category_id = $("#intranet_category_select").val();
		if( intranet_category_id > 0 ) {
			$("#intranet_category_select").change( displayIntranetTypeWidgets( intranet_category_id ) );
		}
	}
	
	function intranetTypeChanged() {
		console.log( 'function intranetTypeChanged' );
		var intranet_type_id = $("#intranet_type_select").val();
		console.log( 'intranet_type_id: '+intranet_type_id );
		if( intranet_type_id > 0 ) {
			$("#intranet_type_select").change( displayIntranetPageWidgets( intranet_type_id ) );
		}
		else {
			$('#home_intranet_pages').hide();
			if( $('#home_intranet_category_list').is(':hidden') ) {
				$('#home_intranet_categories').show();
			}
			else {
				$('#home_intranet_types').show();
			}
		}
	}
	
	function displayIntranetTypeWidgets( intranet_category_id ) {
		console.log( 'function displayIntranetTypeWidgets' );
		console.log( 'intranet_category_id: '+intranet_category_id );
		$('.intranet_type').hide();
		if( intranet_category_id > 0 ) {
			$('.type_intranet_category_'+intranet_category_id).show();
		}
		$('#home_intranet_categories').hide();
		$('#home_intranet_tags').hide();
		$('#home_intranet_pages').hide();
		$('#home_intranet_types').show();
	}
	
	function processPageFilterByTagsAndTypeOrTagsAndCategory( intranet_tags, intranet_type_id, intranet_category_id ) {
		console.log( 'processPageFilterByTagsAndTypeOrTagsAndCategory' );
		var intranet_tags_split = intranet_tags.toString().split(',');
		var tag_classes = "";
		$.each( intranet_tags_split, function( data ) {
			tag_classes += ".page_intranet_tag_"+intranet_tags_split[data];
		});
		console.log( 'tag_classes: '+tag_classes );
		console.log( 'intranet_type_id: '+intranet_type_id );
		console.log( 'intranet_category_id: '+intranet_category_id );
		if( intranet_type_id > 0 ) {
			$(tag_classes).filter('.page_intranet_type_'+intranet_type_id).show();
		}
		else if( intranet_category_id > 0 ) {
			$(tag_classes).filter('.page_intranet_category_'+intranet_category_id).show();
		}
		else {
			$(tag_classes).show();
		}
	}
	
	function intranetTagChanged() {
		console.log( 'function intranetTagChanged' );
		$('.intranet_page').hide();
		var intranet_tags = $("#intranet_tag_select").val();
		var intranet_type_id = $("#intranet_type_select").val();
		var intranet_category_id = $("#intranet_category_select").val();
		console.log( 'intranet_tags: '+intranet_tags );
		console.log( 'intranet_type_id: '+intranet_type_id );
		console.log( 'intranet_category_id: '+intranet_category_id );
		if( intranet_tags != null ) {
			processPageFilterByTagsAndTypeOrTagsAndCategory( intranet_tags, intranet_type_id, intranet_category_id );
			$('#home_intranet_categories').hide();
			$('#home_intranet_types').hide();
			$('#home_intranet_pages').show();
		}
		else {
			intranetTypeChanged();
		}
	}

	function displayIntranetPageWidgets( intranet_type_id ) {
		console.log( 'function displayIntranetPageWidgets' );
		$('.intranet_page').hide();
		var intranet_tags = $("#intranet_tag_select").val();
		var intranet_category_id = $("#intranet_category_select").val();
		console.log( 'intranet_tags: '+intranet_tags );
		console.log( 'intranet_type_id: '+intranet_type_id );
		console.log( 'intranet_category_id: '+intranet_category_id );
		if( intranet_tags != null ) {
			processPageFilterByTagsAndTypeOrTagsAndCategory( intranet_tags, intranet_type_id, intranet_category_id );
		}
		else {
			if( intranet_type_id > 0 ) {
				$('.page_intranet_type_'+intranet_type_id).show();
			}
		}
		$('#home_intranet_categories').hide();
		$('#home_intranet_types').hide();
		$('#home_intranet_pages').show();
	}

</script>

<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/intranet_page_add.dock.widget.php');
?>
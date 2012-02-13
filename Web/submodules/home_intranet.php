<div id="maindock">
	<ul>
		<?php
			$geny_intranet_category = new GenyIntranetCategory();
			foreach( $geny_intranet_category->getAllIntranetCategories() as $intranet_category ) {
				$filename = "images/genymobile-2012/".html_entity_decode( $intranet_category->image_name ).".png";
				if( !file_exists( $filename ) ) {
					$filename = "images/genymobile-2012/intranet_category_generic.png";
				}
				
				echo "<li class=\"intranet_category_".$intranet_category->id."\">";
				echo "<a href=# style=\"background-image: url(".$filename.")\">";
				echo "<span class=\"dock_item_title\">".$intranet_category->name."</span><br/>";
				echo "<span class=\"dock_item_content\">".$intranet_category->description."</span>";
				echo "</a>";
				echo "</li>";
			}
		?>
	</ul>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php');
?>
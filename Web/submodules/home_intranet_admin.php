<div id="maindock">
	<ul>
		<?php
			if( $profile->rights_group_id == 1 || $profile->rights_group_id == 2 ) {
				include 'backend/widgets/intranet_category_add.dock.widget.php';
				include 'backend/widgets/intranet_category_list.dock.widget.php';
				include 'backend/widgets/intranet_type_add.dock.widget.php';
				include 'backend/widgets/intranet_type_list.dock.widget.php';
				include 'backend/widgets/intranet_tag_add.dock.widget.php';
				include 'backend/widgets/intranet_tag_list.dock.widget.php';
				include 'backend/widgets/intranet_page_add.dock.widget.php';
				include 'backend/widgets/intranet_page_list.dock.widget.php';
			}
		?>
	</ul>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php');
?>
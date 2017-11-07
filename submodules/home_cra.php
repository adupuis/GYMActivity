<div id="maindock">
	<ul>
		<?php
			include 'backend/widgets/cra_add.dock.widget.php';
			include 'backend/widgets/cra_validation.dock.widget.php';
			include 'backend/widgets/cra_list.dock.widget.php';
			include 'backend/widgets/cra_deletion.dock.widget.php';
			if( $profile->rights_group_id == 1 || $profile->rights_group_id == 2 ){
				include 'backend/widgets/cra_validation_admin.dock.widget.php'; 
				include 'backend/widgets/cra_post_validation_workflow.dock.widget.php';
				include 'backend/widgets/cra_post_validation_workflow_all.dock.widget.php';
			}
		?>
	</ul>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php');
?>

<div id="maindock">
	<ul>
		<?php
			if( $profile->rights_group_id == 1 || $profile->rights_group_id == 2 ){
				include 'backend/widgets/task_add.dock.widget.php'; 
				include 'backend/widgets/task_list.dock.widget.php'; 
				include 'backend/widgets/project_add.dock.widget.php'; 
				include 'backend/widgets/project_list.dock.widget.php';
				include 'backend/widgets/daily_rate_add.dock.widget.php';
				include 'backend/widgets/daily_rate_list.dock.widget.php';
			}
		?>
	</ul>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php');
?>
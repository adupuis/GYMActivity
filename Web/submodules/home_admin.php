<div id="maindock">
	<ul>
		<?php 
			include 'backend/widgets/user_admin_password_change.dock.widget.php';
			if( $web_config->theme != "tablet" )
				include 'backend/widgets/api_key.dock.widget.php';
			if( $profile->rights_group_id == 1 ){
				include 'backend/widgets/profile_add.dock.widget.php'; 
				include 'backend/widgets/profile_list.dock.widget.php'; 
			}
			if( $profile->rights_group_id == 1 || $profile->rights_group_id == 2 ){
				include 'backend/widgets/client_add.dock.widget.php'; 
				include 'backend/widgets/client_list.dock.widget.php'; 
				include 'backend/widgets/task_add.dock.widget.php'; 
				include 'backend/widgets/task_list.dock.widget.php'; 
				include 'backend/widgets/project_add.dock.widget.php'; 
				include 'backend/widgets/project_list.dock.widget.php';
				include 'backend/widgets/holiday_summary_add.dock.widget.php';
				include 'backend/widgets/holiday_summary_list.dock.widget.php';
			}
			include 'backend/widgets/idea_add.dock.widget.php';
			include 'backend/widgets/idea_list.dock.widget.php';
			if( $profile->rights_group_id == 1 || $profile->rights_group_id == 2 ){
				include 'backend/widgets/daily_rate_add.dock.widget.php';
				include 'backend/widgets/daily_rate_list.dock.widget.php';
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
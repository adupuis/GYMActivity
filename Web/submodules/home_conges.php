<div id="maindock">
	<ul>
		<?php
			include 'backend/widgets/conges_add.dock.widget.php';
			include 'backend/widgets/conges_validation.dock.widget.php';
			include 'backend/widgets/conges_list.dock.widget.php';
			include 'backend/widgets/conges_deletion.dock.widget.php';
			if( $profile->rights_group_id == 1 || $profile->rights_group_id == 2 ){
				include 'backend/widgets/holiday_summary_add.dock.widget.php';
				include 'backend/widgets/holiday_summary_list.dock.widget.php';
				include 'backend/widgets/conges_generate_report.dock.widget.php';
			}
		?>
	</ul>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php');
?>

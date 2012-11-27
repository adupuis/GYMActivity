<div id="maindock">
	<ul>
		<?php
			include 'backend/widgets/reporting_cra_fulfilment.dock.widget.php';
			include 'backend/widgets/reporting_personal_load.dock.widget.php';
			if( in_array($profile->rights_group_id, array(1,2,4,5)) ){
				include 'backend/widgets/reporting_monthly_view.dock.widget.php';
				include 'backend/widgets/reporting_previous_month_view.dock.widget.php';
				include 'backend/widgets/reporting_load.dock.widget.php';
				include 'backend/widgets/reporting_ressources_view.dock.widget.php';
				include 'backend/widgets/reporting_cra_completion.dock.widget.php';
				include 'backend/widgets/reporting_cra_status.dock.widget.php';
				include 'backend/widgets/reporting_category.dock.widget.php';
			}
		?>
	</ul>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php');
?>

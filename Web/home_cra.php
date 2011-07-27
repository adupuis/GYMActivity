<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - CRA';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/default/cra.png"/><p>CRA</p>
</div>

<div id="maindock">
	<ul>
		<?php
			include 'backend/widgets/cra_add.dock.widget.php';
			include 'backend/widgets/cra_validation.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

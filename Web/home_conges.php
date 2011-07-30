<?php
// Variable to configure global behaviour
$header_title = 'GENYMOBILE - Congés';
$required_group_rights = 6;

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/default/conges.png"/><p>Congés</p>
</div>


<div id="maindock">
	<ul>
		<?php
			include 'backend/widgets/conges_add.dock.widget.php';
			include 'backend/widgets/conges_validation.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

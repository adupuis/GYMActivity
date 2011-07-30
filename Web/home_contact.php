<?php
// Variable to configure global behaviour
$header_title = 'GENYMOBILE - Contact';
$required_group_rights = 6;

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/default/contact.png"/><p>Contact</p>
</div>

<div id="maindock">
	<ul>
		<li class="">
			<?php
				include 'backend/widgets/send_notifications.dock.widget.php';
			?>
		</li>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

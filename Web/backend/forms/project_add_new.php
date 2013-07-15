<?php

include '../../rights_groups.php';

// Variable to configure global behaviour
$header_title = 'GenY Mobile - Projects Admin - Add new';
$required_group_rights = array(Admins, TopManagers, Users, TechnologyLeaders, Reporters, GroupLeaders);

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/default/edit_new.png"/><p>Add new project</p>
</div>

<div class="ajax_content">
</div>

<?php
include_once 'footer.php';
?>
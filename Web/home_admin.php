<?php
//  Copyright (C) 2011 by GENYMOBILE & Arnaud Dupuis
//  adupuis@genymobile.com
//  http://www.genymobile.com
// 
//  This program is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 3 of the License, or
//  (at your option) any later version.
// 
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
// 
//  You should have received a copy of the GNU General Public License
//  along with this program; if not, write to the
//  Free Software Foundation, Inc.,
//  59 Temple Place - Suite 330, Boston, MA  02111-1307, USA

// Variable to configure global behaviour
$header_title = 'GENYMOBILE - Admin';
$required_group_rights = 6;

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/default/admin.png"/><p>Admin</p>
</div>

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
				include 'backend/widgets/cra_validation_admin.dock.widget.php'; 
				include 'backend/widgets/client_add.dock.widget.php'; 
				include 'backend/widgets/client_list.dock.widget.php'; 
				include 'backend/widgets/task_add.dock.widget.php'; 
				include 'backend/widgets/task_list.dock.widget.php'; 
				include 'backend/widgets/project_add.dock.widget.php'; 
				include 'backend/widgets/project_list.dock.widget.php';
			}
			include 'backend/widgets/idea_add.dock.widget.php'; 
			include 'backend/widgets/idea_list.dock.widget.php'; 
		?>
	</ul>
</div>


<?php
include_once 'footer.php';
?>

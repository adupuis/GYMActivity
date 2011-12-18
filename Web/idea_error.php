<?php
//  Copyright (C) 2011 by GENYMOBILE & Quentin Désert
//  qdesert@genymobile.com
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
$header_title = '%COMPANY_NAME% - Idées - Erreur';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';

$gritter_notifications = array();

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/idea.png"/><p>Idée</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="idea_remove">
			Erreur !
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Vous n'êtes pas autorisé à effectuer cette action.
		</p>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php 
			include 'backend/widgets/idea_list.dock.widget.php';
			include 'backend/widgets/idea_add.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

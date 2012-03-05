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
$header_title = '%COMPANY_NAME% - Erreur';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';
include_once 'backend/api/ajax_toolbox.php';

$gritter_notifications = array();
$error_msg = getParam( "error_msg", "Vous n'êtes pas autorisé à effectuer cette action." );

?>

<div class="page_title">
	<?php
	foreach( explode( ",", getParam( "category" ) ) as $category ) {
		if( file_exists( "images/".$web_config->theme."/$category.png" ) ) {
			$category_name = "";
			if( $category == "idea" ) {
				$category_name = "Idée";
			}
			echo "<img src=\"images/".$web_config->theme."/$category.png\"/><p>$category_name</p>";
		}
	}
	?>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<?php
		foreach( explode( ",", getParam( "category" ) ) as $category ) {
			// TODO: replace class remove by a class error (with appropriate icon for each category)
			echo "<span class=\"".$category."_remove\">";
		}
		?>
			Erreur !
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		<?php echo $error_msg; ?>
		</p>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php
		foreach( explode( ",", getParam( "backlinks" ) ) as $file ) {
			if( file_exists( "backend/widgets/$file.dock.widget.php" ) ) {
				include "backend/widgets/$file.dock.widget.php";
			}
		}
		?>
    </ul>
</div>

<?php
include_once 'footer.php';
?>

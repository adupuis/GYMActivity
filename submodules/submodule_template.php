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



?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/IMAGES.png"></img>
		<span class="CLASS">
			TITLE
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		INTRODUCTION_TEXT
		</p>
		 <script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			
		</script>
		<form id="formID" action="loader.php?module=SUBMISSION_TARGET" method="post">
			<input type="hidden" name="create_TARGET" value="true" />
			<p>
				<label for="MODULE_name">Nom</label>
				<input name="MODULE_name" id="MODULE_name" type="text" class="validate[required] text-input" />
			</p>
			
			
			<p>
				<input type="submit" value="Créer" /> ou <a href="MODULE_list.php">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php');
?>

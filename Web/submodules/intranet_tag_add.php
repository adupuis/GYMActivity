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

?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/intranet_tag_add.png"></img>
		<span class="intranet_tag_add">
			Ajouter Tag Intranet
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter un tag Intranet. Tous les champs doivent être remplis.
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
		</script>
		<form id="formID" action="loader.php?module=intranet_tag_edit" method="post">
			<input type="hidden" name="create_intranet_tag" value="true" />
			<p>
				<label for="intranet_tag_name">Nom</label>
				<input name="intranet_tag_name" id="intranet_tag_name" type="text" class="validate[required,length[2,25]] text-input" maxlength="25" style="text-transform:lowercase"/>
			</p>
			
			<p>
				<input type="submit" value="Ajouter" /> ou <a href="loader.php?module=intranet_tag_edit">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/intranet_tag_list.dock.widget.php');
?>

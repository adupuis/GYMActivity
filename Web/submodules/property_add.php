<?php
//  Copyright (C) 2011 by GENYMOBILE & Jean-Charles Leneveu
//  jcleneveu@genymobile.com
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

?>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/property_add.png"></img>
		<span class="property_add">
			Ajouter une propriété
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter une propriété afin d'administrer le système. Tous les champs doivent être remplis.
		</p>
		 <script>
			jQuery(document).ready(function(){
				$("#property_add_form").validationEngine('init');
				$("#property_add_form").validationEngine('attach');
			});
		</script>
		<form id="property_add_form" action="loader.php?module=property_edit" method="post">
			<input type="hidden" name="create_property" value="true" />
			<p>
				<label for="property_name">Nom</label>
				<input name="property_name" id="property_name" type="text" class="validate[required] text-input" />
			</p>
			
			<p>
				<label for="property_label">Label</label>
				<input name="property_label" id="property_label" type="text" class="validate[required] text-input" />
			</p>
			
			<p>
				<label for="property_type">Type</label>
				<select name="property_type" class="chzn-select" id="property_type" class="validate[required] select-input">
				<?php
					$geny_property_type = new GenyPropertyType();
					foreach($geny_property_type->getAllPropertyTypes() as $tmp_property_type ) {
						echo '<option value="' . $tmp_property_type->id . '">' . $tmp_property_type->name . '</option>';
					}
				?>
				</select>
			</p>
			
			<p>
				<input type="submit" value="Créer" /> ou <a href="loader.php?module=property_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/property_list.dock.widget.php');
?>

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
$header_title = '%COMPANY_NAME% - Ajout client';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/client_generic.png"/><p>Client</p>
</div>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="client_add">
			Ajouter un client
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter un client dans la base des clients. Tous les champs doivent être remplis.
		</p>
		 <script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			
		</script>
		<form id="formID" action="client_edit.php" method="post">
			<input type="hidden" name="create_client" value="true" />
			<p>
				<label for="client_name">Name</label>
				<input name="client_name" id="client_name" type="text" class="validate[required] text-input" />
			</p>
			
			
			<p>
				<input type="submit" value="Créer" /> ou <a href="#form">annuler</a>
			</p>
		</form>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php 
			include 'backend/widgets/client_list.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

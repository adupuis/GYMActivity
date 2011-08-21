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
$header_title = '%COMPANY_NAME% - Ajout idée';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';

$geny_client = new GenyClient();

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/idea.png"/><p>Idée</p>
</div>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="idea_add">
			Ajouter une idée
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter une idée dans la boîte à idées. Tous les champs doivent être remplis.
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			$(document).ready(function(){
				$(".profileslistselect").listselect({listTitle: "Profils disponibles",selectedTitle: "Profils sélectionnés"});
			});
		</script>
		<form id="formID" action="idea_view.php" method="post">
			<input type="hidden" name="create_idea" value="true" />
			<p>
				<label for="idea_title">Titre</label>
				<input name="idea_title" id="idea_title" type="text" class="validate[required,length[2,100]] text-input" />
			</p>
			<p>
				<label for="idea_description">Description</label>
				<textarea name="idea_description" id="idea_description" class="validate[required] text-input"></textarea>
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
			include 'backend/widgets/idea_list.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

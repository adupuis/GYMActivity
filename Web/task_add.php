<?php
// Variable to configure global behaviour
$header_title = 'GENYMOBILE - Ajout tâche';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/task_generic.png"/><p>Tâche</p>
</div>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="task_add">
			Ajouter une tâche
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter une tâche dans la base des tâches. Tous les champs doivent être remplis.
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			
		</script>
		<form id="formID" action="task_edit.php" method="post">
			<input type="hidden" name="create_task" value="true" />
			<p>
				<label for="task_name">Nom</label>
				<input name="task_name" id="task_name" type="text" class="validate[required] text-input" />
			</p>
			<p>
				<label for="task_description">Description</label>
				<textarea name="task_description" id="task_description" class="validate[required] text-input"></textarea>
			</p>
			
			<p>
				<input type="submit" value="Créer" /> ou <a href="#formID">annuler</a>
			</p>
		</form>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php 
			include 'backend/widgets/task_list.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

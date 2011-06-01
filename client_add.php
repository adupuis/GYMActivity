<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Ajout client';
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

<?php
include_once 'footer.php';
?>

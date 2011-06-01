<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Mise à jour du mot de passe';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/profile_admin.png"/><p>Admin</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="profile_admin">
			Modifier le mot de passe.
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de modifier votre mot de passe.<br />
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
		</script>
		<form id="formID" action="user_admin_password_change.php" method="post">
			<input type="hidden" name="update_password" value="true" />
			<p>
				<label for="password_first">Mot de passe</label>
				<input name="password_first" id="password_first" class="validate[required,length[6,40]] text-input" type="password" />
			</p>
			<p>
				<label for="password_second">Confirmez le mot de passe</label>
				<input name="password_second" id="password_second" class="validate[required,confirm[password_first]] text-input" type="password" />
			</p>
			<p>
				<input type="submit" value="Créer" /> ou <a href="#formID">annuler</a>
			</p>
		</form>
	</p>
</div>

<?php
include_once 'footer.php';
?>

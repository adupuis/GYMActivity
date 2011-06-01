<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Mise à jour du mot de passe';
$required_group_rights = 5;

include_once 'header.php';

if(isset($_POST['update_password']) && $_POST['update_password'] == "yes" && isset($_POST['password_first']) && isset($_POST['password_second']) && $_POST['password_first'] == $_POST['password_second'] ){
	$profile->updateString('profile_password',md5($_POST['password_first']));
	if( $profile->commit() )
		$db_status .= "<li class=\"status_message_success\">Mot de passe mis à jour avec succès.</li>\n";
	else
		$db_status .= "<li class=\"status_message_error\">Erreur lors de la mise à jour du mot de passe.</li>\n";
}

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/profile_admin.png"/><p>Password</p>
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

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GENYMOBILE - Apps</title>
<link rel="stylesheet" type="text/css" href="styles/default/login-page.css" media="screen" title="bbxcss" />
<style type="text/css">
</style>
</head>
<body>
<p style="margin:10px auto 0;text-align:center;display:block;"><img id='main_logo' src='images/default/logo_genymobile.jpg' /></p>
<form id="start" action="check_login.php" method="post">
	<h1>Login GENYMOBILE - Apps</h1>

	<p>
		<label for="geny_username">Login</label>
		<input name="geny_username" id="geny_username" type="text" />
	</p>
	<p>
		<label for="geny_password">Password</label>
		<input name="geny_password" id="geny_password" type="password" />
	</p>
	<p>
		<label for="geny_theme">Password</label>
		<select name="geny_theme" id="geny_theme">
			<option value='default'>Thème par défaut</option>
			<option value='tablet'>Tablettes</option>
		</select>
	</p>
	<!-- Bouton de soumission habituel. Je l'ai commenté pour pouvoir afficher ma div "finish" avec la pseudo-classe :target -->
	<p>
		<input type="submit" value="Login" /> ou <a href="#form">annuler</a>
	</p>
	<!--<p>
		<a class="submit" href="#finish">Envoyer</a> ou <a href="#start">annuler</a>
	</p>-->
<?php
	if(isset($_GET['reason'])){
		if($_GET['reason'] == 'goodcredentials')
			echo '<div id="status_success"><p>Login réussi!</p></div>';
		else if($_GET['reason'] == 'badcredentials')
			echo '<div id="status_error"><p>Login échoué.</p></div>';
		else if($_GET['reason'] == 'authrequired')
			echo '<div id="status_error"><p>Authentification requise.</p></div>';
		else if($_GET['reason'] == 'forbidden')
			echo '<div id="status_error"><p>Accès refusé. Cette tentative d\'accès a été enregistrée et rapportée.</p></div>';
	}
?>
</form>
<p id="credits">&copy; 2011-2012 <strong>GENYMOBILE</strong>.</p>

</body>
</html>
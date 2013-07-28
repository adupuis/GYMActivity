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

include_once "classes/GenyWebConfig.php";
include_once 'classes/GenyPropertyValue.php';
include_once 'classes/GenyPropertyOption.php';
include_once 'classes/GenyProperty.php';
include_once 'classes/GenyTools.php';


$web_config = new GenyWebConfig();

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GENYMOBILE - Apps</title>
<link rel="stylesheet" type="text/css" href="styles/genymobile-2012/login-page.css" media="screen" title="bbxcss" />
<link rel="stylesheet" href="js/chosen/chosen.css" />
<style type="text/css">
</style>
</head>
<body>
<p style="margin:60px auto 0;text-align:center;display:block;"><img id='main_logo' src='images/default/<?php echo $web_config->company_index_logo ?>' /></p>
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
		<label for="geny_theme">Theme</label>
		<select name="geny_theme" id="geny_theme" class="chzn-select">
			<!--<option value='default'>Thème par défaut</option>
			<?php
// 				$selected='';
// 				if( stripos($_SERVER['HTTP_USER_AGENT'],"Android 3") !== false || stripos($_SERVER['HTTP_USER_AGENT'],"SCH-I") !== false || stripos($_SERVER['HTTP_USER_AGENT'],"iPad") !== false )
// 					$selected = "selected='selected'";
			?>
			<option value='tablet' <?php //echo $selected; ?>>Tablettes</option>-->
			<option value='genymobile-2012' selected>Genymobile 2012</option>
		</select>
	</p>
	<!-- Bouton de soumission habituel. Je l'ai commenté pour pouvoir afficher ma div "finish" avec la pseudo-classe :target -->
	<p>
		<input type="submit" value="Login" />
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
	$prop = new GenyProperty();
	$prop->loadPropertyByName("PROP_APP_STATE");
	$pvs = $prop->getPropertyValues();
	$pv = new GenyPropertyValue();
	$state_pv = $pv->getPropertyValuesByPropertyId(3);
	if( count($pvs) == 1 ){
		$state_pv = $pvs;
	}
	$s = array_shift($state_pv);
	$popt = new GenyPropertyOption($s->content);
	GenyTools::debug("\$s->content: $s->content");
	GenyTools::debug("\$popt->content: $popt->content");
	if( $popt->content == 'Active - Issues' ){
		echo '<div id="app_state" class="app_state_warning"><p>Des problèmes ont été rapportés sur cette version de GYMActivity.</p></div>';
	}
	else if($popt->content == 'Inactive - Upgrade'){
		echo '<div id="app_state" class="app_state_critical"><p>GYMActivity est en cours de mise à jour. Connexion impossible.</p></div>';
	}
	else if($popt->content == 'Inactive - Maintenance' ){
		echo '<div id="app_state" class="app_state_critical"><p>GYMActivity est en cours de maintenance. Connexion impossible.</p></div>';
	}
	else if($popt->content == 'Inactive' ){
		echo '<div id="app_state" class="app_state_critical"><p>GYMActivity est inaccessible pour le moment. Connexion impossible.</p></div>';
	}
?>
</form>
<p id="credits">&copy; 2011-2012 <strong>Genymobile</strong>.</p>
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen();</script>
</body>
</html>
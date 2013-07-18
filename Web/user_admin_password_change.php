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


include_once 'rights_groups.php';

// Variable to configure global behaviour
$header_title = '%COMPANY_NAME% - Mise à jour du mot de passe';
$required_group_rights = array(ADM, TM, USR, TL, REP, EXT, GL);
$disable_password_reset_redirection = true;

include_once 'header.php';

$gritter_notifications = array();

if(isset($_POST['update_password']) && $_POST['update_password'] == "true" && isset($_POST['password_first']) && isset($_POST['password_second']) && $_POST['password_first'] == $_POST['password_second'] ){
	$profile->updateString('profile_password',md5($_POST['password_first']));
	$profile->updateBool('profile_needs_password_reset','false');
	if( $profile->commitUpdates() ){
		$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Mot de passe mis à jour avec succès.");
		include_once 'menu.php';
	}
	else
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de la mise à jour du mot de passe.");
}
?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/profile_admin.png"/><p>Password</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="profile_admin">
			Modifier mot de passe.
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
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
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
				<input type="submit" value="Modifier" />
			</p>
		</form>
	</p>
</div>

<?php
include_once 'footer.php';
?>

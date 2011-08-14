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
$header_title = '%COMPANY_NAME% - Edition profil';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$gritter_notifications = array();
$profile_firstname = "";
$profile_lastname = "";
$profile_email = "";
$profile_password = "";
$profile_is_active = "true";
$profile_needs_password_reset = "true";
$rights_group_id = 3;
$geny_profile = new GenyProfile();

if( isset($_POST['create_profile']) && $_POST['create_profile'] == "true" ){
	if( isset($_POST['profile_login']) && isset($_POST['profile_firstname']) && isset($_POST['profile_lastname']) && isset($_POST['profile_password']) && isset($_POST['profile_email']) && isset($_POST['rights_group_id']) ){
		$profile_login = $_POST['profile_login'];
		$profile_firstname = $_POST['profile_firstname'];
		$profile_lastname = $_POST['profile_lastname'];
		$profile_email = $_POST['profile_email'];
		$profile_password = $_POST['profile_password'];
		$profile_is_active = $_POST['profile_is_active'];
		$profile_needs_password_reset = $_POST['profile_needs_password_reset'];
		$rights_group_id = $_POST['rights_group_id'];
		if( $geny_profile->insertNewProfile('NULL',$profile_login,$profile_firstname,$profile_lastname,$profile_password,$profile_email,$profile_is_active,$profile_needs_password_reset,$rights_group_id) ){
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Profil créé avec succès.");
			$geny_profile->loadProfileByLogin($profile_login);
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de la création du profil.");
		}
	}
	else {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir.");
	}
}
else if( isset($_POST['load_profile']) && $_POST['load_profile'] == "true" ){
	if(isset($_POST['profile_id'])){
		$geny_profile->loadProfileById($_POST['profile_id']);
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de charger le profil utilisateur ','msg'=>"id non spécifié.");
	}
}
else if( isset($_GET['load_profile']) && $_GET['load_profile'] == "true" ){
	if(isset($_GET['profile_id'])){
		$geny_profile->loadProfileById($_GET['profile_id']);
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de charger le profil utilisateur ','msg'=>"id non spécifié.");
	}
}
else if( isset($_POST['edit_profile']) && $_POST['edit_profile'] == "true" ){
	if(isset($_POST['profile_id'])){
		$geny_profile->loadProfileById($_POST['profile_id']);
		if( isset($_POST['profile_login']) && $_POST['profile_login'] != "" && $geny_profile->login != $_POST['profile_login'] ){
			$geny_profile->updateString('profile_login',$_POST['profile_login']);
		}
		if( isset($_POST['profile_firstname']) && $_POST['profile_firstname'] != "" && $geny_profile->firstname != $_POST['profile_firstname'] ){
			$geny_profile->updateString('profile_firstname',$_POST['profile_firstname']);
		}
		if( isset($_POST['profile_lastname']) && $_POST['profile_lastname'] != "" && $geny_profile->lastname != $_POST['profile_lastname'] ){
			$geny_profile->updateString('profile_lastname',$_POST['profile_lastname']);
		}
		if( isset($_POST['profile_password']) && $_POST['profile_password'] != "" ){
			$geny_profile->updateString('profile_password',md5($_POST['profile_password']));
		}
		if( isset($_POST['profile_email']) && $_POST['profile_email'] != "" && $geny_profile->email != $_POST['profile_email'] ){
			$geny_profile->updateString('profile_email',$_POST['profile_email']);
		}
		if( isset($_POST['profile_is_active']) && $_POST['profile_is_active'] != "" && $geny_profile->is_active != $_POST['profile_is_active'] ){
			$geny_profile->updateBool('profile_is_active',$_POST['profile_is_active']);
		}
		if( isset($_POST['profile_needs_password_reset']) && $_POST['profile_needs_password_reset'] != "" && $geny_profile->needs_password_reset != $_POST['profile_needs_password_reset'] ){
			$geny_profile->updateBool('profile_needs_password_reset',$_POST['profile_needs_password_reset']);
		}
		if( isset($_POST['rights_group_id']) && $_POST['rights_group_id'] != "" && $geny_profile->rights_group_id != $_POST['rights_group_id'] ){
			$geny_profile->updateInt('rights_group_id',$_POST['rights_group_id']);
		}
		if($geny_profile->commitUpdates()){
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Profil mis à jour avec succès.");
			$geny_profile->loadProfileById($_POST['profile_id']);
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour du profil.");
		}
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de modifier le profil utilisateur ','msg'=>"id non spécifié.");
	}
}
else{
// 	$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Aucune action spécifiée.");
}


?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/profile_generic.png"/><p>Profil</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="profile_edit">
			Modifier un profil
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de modifier un profil dans la base des utilisateurs.
		</p>
		<?php
			if( isset($db_status) && $db_status != "" ){
				echo "<ul class=\"status_message\">\n$db_status\n</ul>";
			}
		?>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
		</script>
		
		<form id="select_login_form" action="profile_edit.php" method="post">
			<input type="hidden" name="load_profile" value="true" />
			<p>
				<label for="profile_id">Séléction profil</label>

				<select name="profile_id" id="profile_id" onChange="submit()">
					<?php
						$profiles = $geny_profile->getAllProfiles();
						foreach( $profiles as $profile ){
							if( (isset($_POST['profile_id']) && $_POST['profile_id'] == $profile->id) || (isset($_GET['profile_id']) && $_GET['profile_id'] == $profile->id) )
								echo "<option value=\"".$profile->id."\" selected>".$profile->login."</option>\n";
							else if( isset($_POST['profile_login']) && $_POST['profile_login'] == $profile->login)
								echo "<option value=\"".$profile->id."\" selected>".$profile->login."</option>\n";
							else
								echo "<option value=\"".$profile->id."\">".$profile->login."</option>\n";
						}
						if( $geny_profile->id < 0 )
							$geny_profile->loadProfileById( $profiles[0]->id );
					?>
				</select>
			</p>
		</form>

		<form id="start" action="profile_edit.php" method="post">
			<input type="hidden" name="edit_profile" value="true" />
			<input type="hidden" name="profile_id" value="<?php echo $geny_profile->id ?>" />
			<p>
				<label for="profile_login">Login</label>
				<input name="profile_login" id="profile_login" value="<?php echo $geny_profile->login ?>" type="text" class="validate[optional,custom[onlyLetter],length[2,100]] text-input" />
			</p>
			<p>
				<label for="profile_firstname">Prénom</label>
				<input name="profile_firstname" id="profile_firstname" value="<?php echo $geny_profile->firstname ?>" type="text" class="validate[optional,length[2,100]] text-input" />
			</p>
			<p>
				<label for="profile_lastname">Nom de famille</label>
				<input name="profile_lastname" id="profile_lastname" value="<?php echo $geny_profile->lastname ?>" type="text" class="validate[optional,length[2,100]] text-input" />
			</p>
			<p>
				<label for="profile_password">Mot de passe</label>
				<input name="profile_password" id="profile_password" type="password" class="validate[optional,length[8,100]] text-input" />
			</p>
			<p>
				<label for="profile_email">E-Mail</label>
				<input name="profile_email" id="profile_email" value="<?php echo $geny_profile->email ?>" type="text" class="validate[optional,custom[email]] text-input" />
			</p>

			<p>
				<label for="profile_is_active">Profil actif</label>
				<select name="profile_is_active" id="profile_is_active" >
					<?php
						if( $geny_profile->is_active ){
							echo "<option value=\"true\" selected>Oui</option>\n<option value=\"false\">Non</option>\n";
						}
						else{
							echo "<option value=\"true\">Oui</option>\n<option value=\"false\" selected>Non</option>\n";
						}
					?>
				</select>
			</p>
			<p>
				<label for="profile_needs_password_reset">R-à-Z password</label>
				<select name="profile_needs_password_reset" id="profile_needs_password_reset">
					<?php
						if( $geny_profile->needs_password_reset ){
							echo "<option value=\"true\" selected>Oui</option>\n<option value=\"false\">Non</option>\n";
						}
						else{
							echo "<option value=\"true\">Oui</option>\n<option value=\"false\" selected>Non</option>\n";
						}
					?>
				</select>
			</p>
			<p>
				<label for="rights_group_id">Groupe</label>
				<select name="rights_group_id" id="rights_group_id">
					<?php
						$geny_rg = new GenyRightsGroup();
						foreach( $geny_rg->getAllRightsGroups() as $group ){
							if($geny_profile->rights_group_id == $group->id)
								echo "<option value=\"".$group->id."\" title=\"".$group->description."\" selected>".$group->name."</option>\n";
							else
								echo "<option value=\"".$group->id."\" title=\"".$group->description."\">".$group->name."</option>\n";
						}
// 						$query = "SELECT rights_group_id,rights_group_name FROM Rights_Groups";
// 						$result = mysql_query($query, $handle);
// 						while ($row = mysql_fetch_row($result)){
// 							if($geny_profile->rights_group_id == $row[0])
// 								echo "<option value=\"".$row[0]."\" selected>".$row[1]."</option>\n";
// 							else
// 								echo "<option value=\"".$row[0]."\">".$row[1]."</option>\n";
// 						}
					?>
				</select>
			</p>
			
			<p>
				<input type="submit" value="Modifier" /> ou <a href="#form">annuler</a>
			</p>
		</form>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php 
			include 'backend/widgets/profile_list.dock.widget.php';
			include 'backend/widgets/profile_add.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

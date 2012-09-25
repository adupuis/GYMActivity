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


$gritter_notifications = array();
$profile_firstname = "";
$profile_lastname = "";
$profile_email = "";
$profile_password = "";
$profile_is_active = "true";
$profile_needs_password_reset = "true";
$rights_group_id = 3;
$geny_profile = new GenyProfile();
$geny_pmd = new GenyProfileManagementData();
$geny_pmd->setDebug(true);

$param_load_profile = GenyTools::getParam("load_profile","false");
$param_profile_id   = GenyTools::getParam("profile_id",null);

if( isset($_POST['create_profile']) && $_POST['create_profile'] == "true" ){
	if( isset($_POST['profile_login']) && isset($_POST['profile_firstname']) && isset($_POST['profile_lastname']) && isset($_POST['profile_password']) && isset($_POST['profile_email']) && isset($_POST['rights_group_id']) && isset($_POST['pmd_availability_date']) && isset($_POST['pmd_is_billable']) && isset($_POST['pmd_recruitement_date']) && isset($_POST['pmd_salary']) && isset($_POST['pmd_variable_salary']) && isset($_POST['pmd_objectived_salary']) && isset($_POST['technology_leader_id']) && isset($_POST['group_leader_id']) && isset($_POST['pmd_category']) ){
		$profile_login = $_POST['profile_login'];
		$profile_firstname = $_POST['profile_firstname'];
		$profile_lastname = $_POST['profile_lastname'];
		$profile_email = $_POST['profile_email'];
		$profile_password = $_POST['profile_password'];
		$profile_is_active = $_POST['profile_is_active'];
		$profile_needs_password_reset = $_POST['profile_needs_password_reset'];
		$rights_group_id = $_POST['rights_group_id'];
		$pmd_availability_date = $_POST['pmd_availability_date'];
		$pmd_is_billable = $_POST['pmd_is_billable'];
		$pmd_recruitement_date = $_POST['pmd_recruitement_date'];
		$pmd_salary = $_POST['pmd_salary'];
		$pmd_variable_salary = $_POST['pmd_variable_salary'];
		$pmd_objectived_salary = $_POST['pmd_objectived_salary'];
		$pmd_group_leader_id = $_POST['group_leader_id'];
		$pmd_technology_leader_id = $_POST['technology_leader_id'];
		$pmd_category = $_POST['pmd_category'];
		if( $geny_profile->insertNewProfile('NULL',$profile_login,$profile_firstname,$profile_lastname,$profile_password,$profile_email,$profile_is_active,$profile_needs_password_reset,$rights_group_id) ){
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Profil créé avec succès.");
			$geny_profile->loadProfileByLogin($profile_login);
			if($geny_profile->id > 0){
				$pmd_id = $geny_pmd->insertNewProfileManagementData($geny_profile->id,$pmd_salary,$pmd_variable_salary,$pmd_objectived_salary,$pmd_recruitement_date,$pmd_is_billable,$pmd_availability_date,$pmd_group_leader_id,$pmd_technology_leader_id,$pmd_category);
				GenyTools::debug("profile_edit.php pmd_id=$pmd_id after a call to insertNewProfileManagementData.");
				if( $pmd_id <= 0)
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur chargement','msg'=>"Erreur lors du chargement des données de management du profil.");
				else
					$geny_pmd->loadProfileManagementDataById($pmd_id);
			} else
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur chargement','msg'=>"Erreur lors du chargement des données du profil.");
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de la création du profil.");
		}
	}
	else {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir.");
	}
}

else if( $param_load_profile == "true" ){
	if(isset($param_profile_id)){
		$geny_profile->loadProfileById($param_profile_id);
		GenyTools::debug("profile_edit.php: \$_POST['profile_id']=".$param_profile_id." \$geny_profile->id=".$geny_profile->id);
		$geny_pmd->loadProfileManagementDataByProfileId( $geny_profile->id );
		if( $geny_profile->id > 0 && $geny_pmd->id <= 0 ){
			// Dans ce cas nous avons un profil mais pas de profilemanagementdata, il faut donc les créer
			$geny_pmd_new_id = $geny_pmd->insertNewProfileManagementData($geny_profile->id,12345,123,1000,"1979-01-01","true","1979-01-01");
			// Comme les données sont des données par défaut il faut notifier les groupes adéquates.
			$grg = new GenyRightsGroup();
			$gn = new GenyNotification();
			$grg->loadRightsGroupByName('Admins');
			$gn->insertNewGroupNotification($grg->id,"Un nouveau profil management a été créé pour ".GenyTools::getProfileDisplayName($geny_profile).". Merci de compléter les informations." );
			$grg->loadRightsGroupByName('SuperUsers');
			$gn->insertNewGroupNotification($grg->id,"Un nouveau profil management a été créé pour ".GenyTools::getProfileDisplayName($geny_profile).". Merci de compléter les informations." );
			// Enfin il faut recharger les données de management avec le groupe qui vient d'être créé
			$geny_pmd->loadProfileManagementDataById( $geny_pmd_new_id );
		}
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de charger le profil utilisateur ','msg'=>"id non spécifié.");
	}
}
// else if( isset($_GET['load_profile']) && $_GET['load_profile'] == "true" ){
// 	if(isset($_GET['profile_id'])){
// 		$geny_profile->loadProfileById($_GET['profile_id']);
// 		GenyTools::debug("profile_edit.php: \$_GET['profile_id']=".$_GET['profile_id']." \$geny_profile->id=".$geny_profile->id);
// 		$geny_pmd->loadProfileManagementDataByProfileId( $geny_profile->id );
// 		if( $geny_profile->id > 0 && $geny_pmd->id <= 0 ){
// 			// Dans ce cas nous avons un profil mais pas de profilemanagementdata, il faut donc les créer
// 			$geny_pmd_new_id = $geny_pmd->insertNewProfileManagementData($geny_profile->id,12345,123,1000,"1979-01-01","true","1979-01-01");
// 			// Comme les données sont des données par défaut il faut notifier les groupes adéquates.
// 			$grg = new GenyRightsGroup();
// 			$gn = new GenyNotification();
// 			$grg->loadRightsGroupByName('Admins');
// 			$gn->insertNewGroupNotification($grg->id,"Un nouveau profil management a été créé pour ".GenyTools::getProfileDisplayName($geny_profile).". Merci de compléter les informations." );
// 			$grg->loadRightsGroupByName('SuperUsers');
// 			$gn->insertNewGroupNotification($grg->id,"Un nouveau profil management a été créé pour ".GenyTools::getProfileDisplayName($geny_profile).". Merci de compléter les informations." );
// 			// Enfin il faut recharger les données de management avec le groupe qui vient d'être créé
// 			$geny_pmd->loadProfileManagementDataById( $geny_pmd_new_id );
// 		}
// 	}
// 	else  {
// 		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de charger le profil utilisateur ','msg'=>"id non spécifié.");
// 	}
// }
else if( isset($_POST['edit_profile']) && $_POST['edit_profile'] == "true" ){
	if(isset($_POST['profile_id'])){
		$geny_profile->loadProfileById($_POST['profile_id']);
		$geny_pmd->loadProfileManagementDataByProfileId( $geny_profile->id );
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
			$geny_pmd->loadProfileManagementDataByProfileId( $geny_profile->id );
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour du profil.");
		}
		// isset($_POST['pmd_availability_date']) && isset($_POST['pmd_is_billable']) && isset($_POST['pmd_recruitement_date']) && isset($_POST['pmd_salary'])
		if( isset($_POST['pmd_availability_date']) && $_POST['pmd_availability_date'] != "" && $geny_pmd->availability_date != $_POST['pmd_availability_date'] ){
			$geny_pmd->updateString('profile_management_data_availability_date',$_POST['pmd_availability_date']);
		}
		if( isset($_POST['pmd_is_billable']) && $_POST['pmd_is_billable'] != "" && $geny_pmd->is_billable != $_POST['pmd_is_billable'] ){
			$geny_pmd->updateBool('profile_management_data_is_billable',$_POST['pmd_is_billable']);
		}
		if( isset($_POST['pmd_recruitement_date']) && $_POST['pmd_recruitement_date'] != "" && $geny_pmd->recruitement_date != $_POST['pmd_recruitement_date'] ){
			$geny_pmd->updateString('profile_management_data_recruitement_date',$_POST['pmd_recruitement_date']);
		}
		if( isset($_POST['pmd_salary']) && $_POST['pmd_salary'] != "" && $geny_pmd->salary != $_POST['pmd_salary'] ){
			$geny_pmd->updateInt('profile_management_data_salary',$_POST['pmd_salary']);
		}
		if( isset($_POST['pmd_variable_salary']) && $_POST['pmd_variable_salary'] != "" && $geny_pmd->salary != $_POST['pmd_variable_salary'] ){
			$geny_pmd->updateInt('profile_management_data_variable_salary',$_POST['pmd_variable_salary']);
		}
		if( isset($_POST['pmd_objectived_salary']) && $_POST['pmd_objectived_salary'] != "" && $geny_pmd->salary != $_POST['pmd_objectived_salary'] ){
			$geny_pmd->updateInt('profile_management_data_objectived_salary',$_POST['pmd_objectived_salary']);
		}
		if( isset($_POST['group_leader_id']) && $_POST['group_leader_id'] != "" && $geny_pmd->group_leader_id != $_POST['group_leader_id'] ){
			$geny_pmd->updateInt('profile_management_data_group_leader_id',$_POST['group_leader_id']);
		}
		if( isset($_POST['technology_leader_id']) && $_POST['technology_leader_id'] != "" && $geny_pmd->technology_leader_id != $_POST['technology_leader_id'] ){
			$geny_pmd->updateInt('profile_management_data_technology_leader_id',$_POST['technology_leader_id']);
		}
		if( isset($_POST['pmd_category']) && $_POST['pmd_category'] != "" && $geny_pmd->category != $_POST['pmd_category'] ){
			$geny_pmd->updateInt('profile_management_data_category',$_POST['pmd_category']);
		}
		if($geny_pmd->commitUpdates()){
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Données de management du profil mis à jour avec succès.");
			$geny_pmd->loadProfileManagementDataByProfileId( $geny_profile->id );
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour des données de management du profil.");
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
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/profile_edit.png"></img>
		<span class="profile_edit">
			Modifier un profil
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de modifier un profil dans la base des utilisateurs.
		</p>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
		</script>
		
		<form id="select_login_form" action="loader.php?module=profile_edit" method="post">
			<input type="hidden" name="load_profile" value="true" />
			<p>
				<label for="profile_id">Sélection profil</label>

				<select name="profile_id" id="profile_id" onChange="submit()" class="chzn-select">
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

		<form id="start" action="loader.php?module=profile_edit" method="post">
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
				<select name="rights_group_id" id="rights_group_id" class="chzn-select">
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
				<label for="group_leader_id">Group Leader</label>
				<select name="group_leader_id" id="group_leader_id" class="chzn-select">
				<?php
					foreach( $geny_profile->getProfilesListWithRestrictions( array("rights_group_id=".$geny_rg->getIdByShortname('ADM'), "rights_group_id=".$geny_rg->getIdByShortname('TM'), "rights_group_id=".$geny_rg->getIdByShortname('GL') ), "OR" ) as $pfl ){
						if( $geny_pmd->group_leader_id == $pfl->id ) {
							echo "<option value=\"".$pfl->id."\" selected>".GenyTools::getProfileDisplayName($pfl)."</option>\n";
						}
						else {
							echo "<option value=\"".$pfl->id."\" >".GenyTools::getProfileDisplayName($pfl)."</option>\n";
						}
						
					}
				?>
				</select>
			</p>
			<p>
				<label for="technology_leader_id">Technology Leader</label>
				<select name="technology_leader_id" id="technology_leader_id" class="chzn-select">
				<?php
					foreach( $geny_profile->getProfilesListWithRestrictions( array("rights_group_id=".$geny_rg->getIdByShortname('ADM'), "rights_group_id=".$geny_rg->getIdByShortname('TM'), "rights_group_id=".$geny_rg->getIdByShortname('TL') ), "OR" ) as $pfl ){
						if( $geny_pmd->technology_leader_id == $pfl->id ) {
							echo "<option value=\"".$pfl->id."\" selected>".GenyTools::getProfileDisplayName($pfl)."</option>\n";
						}
						else {
							echo "<option value=\"".$pfl->id."\">".GenyTools::getProfileDisplayName($pfl)."</option>\n";
						}
					}
				?>
				</select>
			</p>
			<strong>CATEGORY ---------</strong><br/>
			<p>
				<label for="pmd_category">Catégorie</label>
				<select name="pmd_category" id="pmd_category" class="chzn-select">
					<?php
					$geny_property = new GenyProperty();
					$geny_property->loadPropertyByName('PROP_PROFILE_CATEGORY');
					foreach( $geny_property->getPropertyOptions() as $option ){
						if($option->content == $geny_pmd->category){
							echo "<option value='".$option->id."' selected>".$option->content."</option>";
						}
						else{
							echo "<option value='".$option->id."'>".$option->content."</option>";
						}
					}
					?>
				</select>
			</p>
			<strong>--------- CATEGORY</strong><br/>
			<p>
				<label for="pmd_is_billable">Profil facturable</label>
				<select name="pmd_is_billable" id="pmd_is_billable" >
					<?php
						if( $geny_pmd->is_billable ){
							echo "<option value=\"true\" selected>Oui</option>\n<option value=\"false\">Non</option>\n";
						}
						else{
							echo "<option value=\"true\">Oui</option>\n<option value=\"false\" selected>Non</option>\n";
						}
					?>
				</select>
			</p>
			<p>
				<label for="pmd_salary">Salaire fixe (€ brut/an)</label>
				<input name="pmd_salary" id="pmd_salary" value="<?php echo $geny_pmd->salary ?>" type="text" class="validate[required,custom[reallyOnlyNumber]] text-input" />
			</p>
			<p>
				<label for="pmd_variable_salary">Salaire Var. (€ brut/an)</label>
				<input name="pmd_variable_salary" id="pmd_variable_salary" value="<?php echo $geny_pmd->variable_salary ?>" type="text" class="validate[required,custom[reallyOnlyNumber]] text-input" />
			</p>
			<p>
				<label for="pmd_objectived_salary">Prime / Obj. (€ brut/an)</label>
				<input name="pmd_objectived_salary" id="pmd_objectived_salary" value="<?php echo $geny_pmd->objectived_salary ?>" type="text" class="validate[required,custom[reallyOnlyNumber]] text-input" />
			</p>
			 
			<script type="text/javascript">
				$(function() {
					$( "#pmd_recruitement_date" ).datepicker();
					$( "#pmd_recruitement_date" ).datepicker('setDate', new Date());
					$( "#pmd_recruitement_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#pmd_recruitement_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#pmd_recruitement_date" ).datepicker( "option", "defaultDate", "<?php echo $geny_pmd->recruitement_date ?>" );
					$( "#pmd_recruitement_date" ).datepicker( "setDate", "<?php echo $geny_pmd->recruitement_date ?>" );
					$( "#pmd_recruitement_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#pmd_recruitement_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#pmd_recruitement_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#pmd_recruitement_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#pmd_recruitement_date" ).datepicker( "option", "firstDay", 1 );
					
					$( "#pmd_availability_date" ).datepicker();
					$( "#pmd_availability_date" ).datepicker('setDate', new Date());
					$( "#pmd_availability_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#pmd_availability_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#pmd_availability_date" ).datepicker( "option", "defaultDate", "<?php echo $geny_pmd->availability_date ?>" );
					$( "#pmd_availability_date" ).datepicker( "setDate", "<?php echo $geny_pmd->availability_date ?>" );
					$( "#pmd_availability_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
					$( "#pmd_availability_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
					$( "#pmd_availability_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
					$( "#pmd_availability_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
					$( "#pmd_availability_date" ).datepicker( "option", "firstDay", 1 );
				});
			</script>
			<p>
				<label for="pmd_recruitement_date">Date d'embauche</label>
				<input name="pmd_recruitement_date" id="pmd_recruitement_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="pmd_availability_date">Date de disponibilité</label>
				<input name="pmd_availability_date" id="pmd_availability_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<input type="submit" value="Modifier" /> ou <a href="loader.php?module=profile_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/profile_list.dock.widget.php','backend/widgets/profile_add.dock.widget.php');
?>

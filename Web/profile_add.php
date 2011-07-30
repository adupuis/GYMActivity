<?php
// Variable to configure global behaviour
$header_title = 'GENYMOBILE - Ajout profil';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$geny_profile = new GenyProfile();

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/profile_generic.png"/><p>Profil</p>
</div>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="profile_add">
			Ajouter un profil
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter un profil dans la base des utilisateurs. Tous les champs doivent être remplis.
		</p>
		 <script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			
		</script>
		<form id="formID" action="profile_edit.php" method="post">
			<input type="hidden" name="create_profile" value="true" />
			<p>
				<label for="profile_login">Login</label>
				<input name="profile_login" id="profile_login" type="text" class="validate[required,custom[onlyLetter],length[2,100]] text-input" />
			</p>
			<script>
				function updateVals() {
					var text = $("#profile_login").val();
					$("#profile_email").val("");
					$("#profile_email").val(text+"@genymobile.com");
				}

				$("#profile_login").change(updateVals);
				updateVals();

			</script>
			<p>
				<label for="profile_firstname">Prénom</label>
				<input name="profile_firstname" id="profile_firstname" type="text" class="validate[required,length[2,100]] text-input" />
			</p>
			<p>
				<label for="profile_lastname">Nom de famille</label>
				<input name="profile_lastname" id="profile_lastname" type="text" class="validate[required,length[2,100]] text-input" />
			</p>
			<p>
				<label for="profile_password">Mot de passe</label>
				<input name="profile_password" id="profile_password" type="password" class="validate[required,length[8,100]] text-input" />
			</p>
			<p>
				<label for="profile_email">E-Mail</label>
				<input name="profile_email" class="validate[required,custom[email]] text-input" id="profile_email" type="text" />
			</p>
			<p>
				<label for="profile_is_active">Profil actif</label>
				<select name="profile_is_active" id="profile_is_active" />
					<option value="true">Oui</option>
					<option value="false">Non</option>
				</select>
			</p>
			<p>
				<label for="profile_needs_password_reset">R-à-Z password</label>
				<select name="profile_needs_password_reset" id="profile_needs_password_reset"/>
					<option value="true">Oui</option>
					<option value="false">Non</option>
				</select>
			</p>
			<p>
				<label for="rights_group_id">Groupe</label>
				<select name="rights_group_id" id="rights_group_id"/>
					<?php
						$geny_rg = new GenyRightsGroup();
						foreach( $geny_rg->getAllRightsGroups() as $group ){
							if($geny_profile->rights_group_id == $group->id)
								echo "<option value=\"".$group->id."\" title=\"".$group->description."\" selected>".$group->name."</option>\n";
							else
								echo "<option value=\"".$group->id."\" title=\"".$group->description."\">".$group->name."</option>\n";
						}
					?>
				</select>
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
			include 'backend/widgets/profile_list.dock.widget.php'; 
		?>
	</ul>
</div>
<?php
include_once 'footer.php';
?>

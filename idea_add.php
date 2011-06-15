<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Ajout idée';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$geny_client = new GenyClient();
$geny_profile = new GenyProfile();
$geny_idea_status = new GenyIdeaStatus();

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/project_generic.png"/><p>Idée</p>
</div>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="idea_add">
			Ajouter une idée
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'ajouter une idée dans la boîte à idées. Tous les champs doivent être remplis.
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			$(document).ready(function(){
				$(".profileslistselect").listselect({listTitle: "Profils disponibles",selectedTitle: "Profils sélectionnés"});
			});
		</script>
		<form id="formID" action="idea_edit.php" method="post">
			<input type="hidden" name="create_idea" value="true" />
			<p>
				<label for="idea_title">Titre de l'idée</label>
				<input name="idea_title" id="idea_title" type="text" class="validate[required,custom[onlyLetter],length[2,100]] text-input" />
			</p>
			<p>
				<label for="idea_description">Description</label>
				<textarea name="idea_description" id="idea_description" class="validate[required] text-input"></textarea>
			</p>
			<p>
				<label for="idea_status">Statut</label>
				<select name="idea_status" id="idea_status">
				<?php
					foreach( $geny_idea_status->getAllIdeaStatus() as $idea_status ) {
						echo "<option value=\"".$idea_status->id."\">".$idea_status->name."</option>\n";
					}
				?>
				</select>
			</p>
			<p>
				<label for="idea_submitter">Soumetteur</label>
				<select name="idea_submitter" id="idea_submitter">
				<?php
					foreach( $geny_profile->getAllProfiles() as $profile ) {
						echo "<option value=\"".$profile->id."\">".$profile->firstname." ".$profile->lastname."</option>\n";
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

<?php
include_once 'footer.php';
?>

<?php
// Variable to configure global behaviour
$header_title = 'GENYMOBILE - Edition idée';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';

$db_status = "";

$geny_idea = new GenyIdea();
$geny_idea_status = new GenyIdeaStatus();

$logged_in_profile = new GenyProfile();
$logged_in_profile->loadProfileByUsername( $_SESSION['USERID'] );



if( isset( $_POST['load_idea'] ) && $_POST['load_idea'] == "true" ) {
	if( isset( $_POST['idea_id'] ) ) {
		$geny_idea->loadIdeaById( $_POST['idea_id'] );
	}
	else {
		$db_status .= "<li class=\"status_message_error\">Impossible de charger l'idée : id non spécifié.</li>\n";
	}
}
else if( isset( $_GET['load_idea'] ) && $_GET['load_idea'] == "true" ) {
	if( isset( $_GET['idea_id'] ) ) {
		$geny_idea->loadIdeaById( $_GET['idea_id'] );
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de charger l'idée : id non spécifié.</li>\n";
	}
}
else if( isset( $_POST['edit_idea'] ) && $_POST['edit_idea'] == "true" ) {
	if( isset( $_POST['idea_id'] ) ) {
		$geny_idea->loadIdeaById( $_POST['idea_id'] );
		if( isset( $_POST['idea_title'] ) && $_POST['idea_title'] != "" && $geny_idea->title != $_POST['idea_title'] ) {
			$geny_idea->updateString( 'idea_title', $_POST['idea_title'] );
		}
		if( isset( $_POST['idea_description'] ) && $_POST['idea_description'] != "" && $geny_idea->description != $_POST['idea_description'] ) {
			$geny_idea->updateString( 'idea_description', $_POST['idea_description'] );
		}
		if( isset( $_POST['idea_votes'] ) && $_POST['idea_votes'] != "" ) {
			$geny_idea->updateInt( 'idea_votes', $_POST['idea_votes'] );
		}
		if( isset( $_POST['idea_status'] ) && $_POST['idea_status'] != "" ) {
			$geny_idea->updateInt( 'idea_status_id', $_POST['idea_status'] );
		}
		if( isset( $logged_in_profile->id ) && $logged_in_profile->id != "" ) {
			$geny_idea->updateInt( 'idea_submitter', $logged_in_profile->id );
		}
		if( $geny_idea->commitUpdates() ) {
			$db_status .= "<li class=\"status_message_success\">Idée mise à jour avec succès.</li>\n";
			$geny_idea->loadIdeaById( $_POST['idea_id'] );
		}
		else {
			$db_status .= "<li class=\"status_message_error\">Erreur durant la mise à jour de l'idée.</li>\n";
		}
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de modifier l'idée : id non spécifié.</li>\n";
	}
}

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/project_generic.png"/><p>Idée</p>
</div>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="idea_edit">
			Modifier une idée
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d'éditer une idée existante. Tous les champs doivent être remplis.
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
		</script>
		<?php
			if( isset($db_status) && $db_status != "" ){
				echo "<ul class=\"status_message\">\n$db_status\n</ul>";
			}
		?>
		<form id="select_idea_form" action="idea_edit.php" method="post">
			<input type="hidden" name="load_idea" value="true" />
			<p>
				<label for="idea_id">Sélection idée</label>

				<select name="idea_id" id="idea_id" onChange="submit()">
					<?php
					$ideas = $geny_idea->getIdeasListBySubmitter( $logged_in_profile->id );
					foreach( $ideas as $idea ) {
						if( ( isset( $_POST['idea_id'] ) && $_POST['idea_id'] == $idea->id ) || ( isset( $_GET['idea_id'] ) && $_GET['idea_id'] == $idea->id ) ) {
							echo "<option value=\"".$idea->id."\" selected>".$idea->name."</option>\n";
						}
						else if( isset($_POST['idea_name']) && $_POST['idea_name'] == $idea->name) {
							echo "<option value=\"".$idea->id."\" selected>".$idea->name."</option>\n";
						}
						else {
							echo "<option value=\"".$idea->id."\">".$idea->title."</option>\n";
						}
					}
					if( $geny_idea->id < 0 ) {
						$geny_idea->loadIdeaById( $ideas[0]->id );
					}
					?>
				</select>
			</p>
		</form>
		<form id="formID" action="idea_edit.php" method="post">
			<input type="hidden" name="edit_idea" value="true" />
			<input type="hidden" name="idea_id" value="<?php echo $geny_idea->id ?>" />
			 <p>
				<label for="idea_title">Titre</label>
				<input name="idea_title" id="idea_title" type="text" value="<?php echo $geny_idea->title ?>" class="validate[required,length[2,100]] text-input" />
			</p>
			<p>
				<label for="idea_description">Description</label>
				<textarea name="idea_description" id="idea_description" class="validate[required] text-input"><?php echo $geny_idea->description ?></textarea>
			</p>
			<p>
				<label for="idea_status">Statut</label>
				<select name="idea_status" id="idea_status">
					<?php
					foreach( $geny_idea_status->getAllIdeaStatus() as $idea_status ) {
						if( $geny_idea->status_id == $idea_status->id ) {
							echo "<option value=\"".$idea_status->id."\" selected>".$idea_status->name."</option>\n";
						}
						else {
							echo "<option value=\"".$idea_status->id."\">".$idea_status->name."</option>\n";
						}
					}
					?>
				</select>
			</p>
			<p>
				<input type="submit" value="Modifier" /> ou <a href="#formID">annuler</a>
			</p>
		</form>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php 
			include 'backend/widgets/idea_list.dock.widget.php';
			include 'backend/widgets/idea_add.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

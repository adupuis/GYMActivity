<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Visualisation idée';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$db_status = "";

$geny_idea = new GenyIdea();
$geny_idea_status = new GenyIdeaStatus();
$geny_idea_message = new GenyIdeaMessage();
$geny_profile = new GenyProfile();

if( isset( $_POST['load_idea'] ) && $_POST['load_idea'] == "true" ) {
	if( isset( $_POST['idea_id'] ) ) {
		$geny_idea->loadIdeaById( $_POST['idea_id'] );
	}
	else {
		$db_status .= "<li class=\"status_message_error\">Impossible de charger l'idée : id non spécifié.</li>\n";
	}
}

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/project_generic.png"/><p>Idée</p>
</div>

<div id="mainarea">
	<form id="select_idea_form" action="idea_view.php" method="post">
		<input type="hidden" name="load_idea" value="true" />
		<p>
			<label for="idea_id">Sélection idée</label>

			<select name="idea_id" id="idea_id" onChange="submit()">
			<?php
			$ideas = $geny_idea->getAllIdeas();
			foreach( $ideas as $idea ) {
				if( ( isset( $_POST['idea_id'] ) && $_POST['idea_id'] == $idea->id ) || ( isset( $_GET['idea_id'] ) && $_GET['idea_id'] == $idea->id ) ) {
					echo "<option value=\"".$idea->id."\" selected>".$idea->name."</option>\n";
				}
				else if( isset( $_POST['idea_name'] ) && $_POST['idea_name'] == $idea->name ) {
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
	<p class="mainarea_title">
		<span class="idea_edit">
		<?php echo $geny_idea->title ?>
		</span>
	</p>
	<p class="mainarea_content">
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
		</script>
		<?php
			if( isset($db_status) && $db_status != "" ) {
				echo "<ul class=\"status_message\">\n$db_status\n</ul>";
			}
		?>

		<center><strong>Statut: </strong><span>
		<?php
		foreach( $geny_idea_status->getAllIdeaStatus() as $idea_status ) {
			if( $geny_idea->status_id == $idea_status->id ) {
				echo $idea_status->name;
				break;
			}
		}
		?>
		</span></center><br><br>
		
		<table cellspacing="0" cellpadding="4" class="idea_table">
		<tbody>
			<tr>
				<th id="idea_table_header_author" class="idea_table_header">Auteur</th>
				<th id="idea_table_header_content" class="idea_table_header">Contenu</th>
			</tr>
			<tr id="idea_table_idea">
				<td>
				<?php
				foreach( $geny_profile->getAllProfiles() as $profile ) {
					if( $geny_idea->submitter == $profile->id ) {
						echo $profile->firstname." ".$profile->lastname;
						break;
					}
				}
				?>
				</td>
				<td>
				<?php echo $geny_idea->description ?>
				</td>
			</tr>
		</tbody>
		</table>

		<br>
		
		<table cellspacing="0" cellpadding="4" class="messages_table">
		<tbody>
			<tr>
			<th id="messages_table_header_author" class="messages_table_header">Auteurs</th>
			<th id="messages_table_header_content" class="messages_table_header">Commentaires</th>
			</tr>
			<?php
			$geny_idea_messages = $geny_idea_message->getIdeaMessagesListByIdeaId( $geny_idea->id );
			foreach( $geny_idea_messages as $idea_message ) {
				foreach( $geny_profile->getAllProfiles() as $profile ) {
				if( $idea_message->profile_id == $profile->id ) {
					$message_author = $profile->firstname." ".$profile->lastname;
					break;
				}
				}
				echo "<tr><td>".$message_author."</td><td>".$idea_message->content."</td></tr>";
			}
			if( $geny_idea->id < 0 ) {
				$geny_idea->loadIdeaById( $ideas[0]->id );
			}
			?>
		</tbody>
		</table>
	</p>
</div>

<?php
include_once 'footer.php';
?>

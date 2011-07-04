<?php

ini_set( 'error_reporting', E_ERROR );
ini_set( 'display_errors', 1 );

// Variable to configure global behaviour
$header_title = 'GenY Mobile - Visualisation idée';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';

$db_status = "";

$geny_idea = new GenyIdea();
$geny_idea_status = new GenyIdeaStatus();
$geny_idea_message = new GenyIdeaMessage();
$geny_idea_vote = new GenyIdeaVote();
$geny_profile = new GenyProfile();

$logged_in_profile = new GenyProfile();
$logged_in_profile->loadProfileByUsername( $_SESSION['USERID'] );

if( isset( $_POST['create_idea'] ) && $_POST['create_idea'] == "true" ) {
	if( isset( $_POST['idea_title'] ) ) {
		if( $geny_idea->insertNewIdea( 'NULL', $_POST['idea_title'], $_POST['idea_description'], $_POST['idea_votes'], 1, $logged_in_profile->id ) ) {
			$db_status .= "<li class=\"status_message_success\">Idée créée avec succès.</li>\n";
			$geny_idea->loadIdeaByTitle( $_POST['idea_title'] );
		}
		else {
			$db_status .= "<li class=\"status_message_error\">Erreur lors de la création de l'idée.</li>\n";
		}
	}
	else {
		$db_status .= "<li class=\"status_message_error\">Certains champs obligatoires sont manquant. Merci de les remplir.</li>\n";
	}
}
else if( isset( $_POST['load_idea'] ) && $_POST['load_idea'] == "true" ) {
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
else if( isset( $_GET['idea_vote'] ) && $_GET['idea_vote'] == "true" ) {
	if( isset( $_GET['idea_vote_idea_id'] ) ) {
		$geny_idea->loadIdeaById( $_GET['idea_vote_idea_id'] );
		if( isset( $_GET['idea_vote_positive'] ) && $_GET['idea_vote_positive'] == "true" ) {
			$my_votes_for_this_idea = $geny_idea_vote->getIdeaVotesListByProfileAndIdeaId( $logged_in_profile->id, $_GET['idea_vote_idea_id'] );
			if( count( $my_votes_for_this_idea ) > 0 ) {
				$geny_idea_vote = $my_votes_for_this_idea[0];
				$geny_idea_vote->updateInt( 'idea_negative_vote', 0 );
				$geny_idea_vote->updateInt( 'idea_positive_vote', 1 );
				if( $geny_idea_vote->commitUpdates() ) {
					$db_status .= "<li class=\"status_message_success\">Finalement vous êtes pour!</li>\n";
				}
				else {
					$db_status .= "<li class=\"status_message_error\">Erreur durant la mise à jour du vote.</li>\n";
				}
			}
			else {
				if( $geny_idea_vote->insertNewIdeaVote( 'NULL', 1, 0, $logged_in_profile->id, $_GET['idea_vote_idea_id'] ) ) {
					$db_status .= "<li class=\"status_message_success\">Vous avez voté pour !</li>\n";
				}
				else {
					$db_status .= "<li class=\"status_message_error\">Erreur lors de l'ajout du vote positif.</li>\n";
				}
			}

		}
		else if( isset( $_GET['idea_vote_negative'] ) && $_GET['idea_vote_negative'] == "true" ) {
			$my_votes_for_this_idea = $geny_idea_vote->getIdeaVotesListByProfileAndIdeaId( $logged_in_profile->id, $_GET['idea_vote_idea_id'] );
			if( count( $my_votes_for_this_idea ) > 0 ) {
				$geny_idea_vote = $my_votes_for_this_idea[0];
				$geny_idea_vote->updateInt( 'idea_negative_vote', 1 );
				$geny_idea_vote->updateInt( 'idea_positive_vote', 0 );
				if( $geny_idea_vote->commitUpdates() ) {
					$db_status .= "<li class=\"status_message_success\">Finalement vous êtes contre...</li>\n";
				}
				else {
					$db_status .= "<li class=\"status_message_error\">Erreur durant la mise à jour du vote.</li>\n";
				}
			}
			else {
				if( $geny_idea_vote->insertNewIdeaVote( 'NULL', 0, 1, $logged_in_profile->id, $_GET['idea_vote_idea_id'] ) ) {
					$db_status .= "<li class=\"status_message_success\">Vous avez voté contre...</li>\n";
				}
				else {
					$db_status .= "<li class=\"status_message_error\">Erreur lors de l'ajout du vote négatif.</li>\n";
				}
			}
		}
		else if( isset( $_GET['idea_vote_neutral'] ) && $_GET['idea_vote_neutral'] == "true" ) {
			$my_votes_for_this_idea = $geny_idea_vote->getIdeaVotesListByProfileAndIdeaId( $logged_in_profile->id, $_GET['idea_vote_idea_id'] );
			if( count( $my_votes_for_this_idea ) > 0 ) {
				$geny_idea_vote = $my_votes_for_this_idea[0];
				if( $geny_idea_vote->removeIdeaVote( $geny_idea_vote->id ) ) {
					$db_status .= "<li class=\"status_message_success\">Finalement vous n'avez pas d'avis sur la question...</li>\n";
				}
				else {
					$db_status .= "<li class=\"status_message_error\">Erreur lors de la suppression du vote.</li>\n";
				}
			}
		}
	}
	else {
		$db_status .= "<li class=\"status_message_error\">Impossible de charger l'idée : id non spécifié.</li>\n";
	}
}
else if( isset( $_POST['idea_message_create'] ) && $_POST['idea_message_create'] == "true" ) {
	if( isset( $_POST['idea_message_idea_id'] ) ) {
		$geny_idea->loadIdeaById( $_POST['idea_message_idea_id'] );
		if( isset( $_POST['idea_message_content'] ) ) {
			if( $geny_idea_message->insertNewIdeaMessage( 'NULL', $_POST['idea_message_content'], $logged_in_profile->id, $_POST['idea_message_idea_id'] ) ) {
				$db_status .= "<li class=\"status_message_success\">Commentaire ajouté avec succès.</li>\n";
			}
			else {
				$db_status .= "<li class=\"status_message_error\">Erreur lors de l'ajout du commentaire.</li>\n";
			}
		}
		else {
			$db_status .= "<li class=\"status_message_error\">Certains champs obligatoires sont manquant. Merci de les remplir.</li>\n";
		}
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
				if( $geny_idea->id == $idea->id ) {
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
		</span></center>

		<br>

		<center>
		<?php
		$bProfileHasVoted = false;
		foreach( $geny_idea_vote->getIdeaVotesListByIdeaId( $geny_idea->id ) as $idea_vote ) {
			if( $idea_vote->profile_id == $logged_in_profile->id ) {
				if( $idea_vote->idea_positive_vote == 1 ) {
					$bProfileHasVoted = true;
					echo "<a href=\"idea_view.php?idea_vote=true&idea_vote_negative=true&idea_vote_idea_id=".$geny_idea->id."\" title=\"Voter contre cette idée\"><img src=\"images/".$web_config->theme."/smiley_down.png\" alt=\"Voter contre cette idée\"></a>";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<a href=\"idea_view.php?idea_vote=true&idea_vote_neutral=true&idea_vote_idea_id=".$geny_idea->id."\" title=\"Pas d'avis pour cette idée\"><img src=\"images/".$web_config->theme."/smiley_neutral.png\" alt=\"Pas d'avis pour cette idée\"></a>";
				}
				else if( $idea_vote->idea_negative_vote == 1 ) {
					$bProfileHasVoted = true;
					echo "<a href=\"idea_view.php?idea_vote=true&idea_vote_positive=true&idea_vote_idea_id=".$geny_idea->id."\" title=\"Voter pour cette idée\"><img src=\"images/".$web_config->theme."/smiley_up.png\" alt=\"Voter pour cette idée\"></a>";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<a href=\"idea_view.php?idea_vote=true&idea_vote_neutral=true&idea_vote_idea_id=".$geny_idea->id."\" title=\"Pas d'avis pour cette idée\"><img src=\"images/".$web_config->theme."/smiley_neutral.png\" alt=\"Pas d'avis pour cette idée\"></a>";
				}
				break;
			}
		}
		if( !$bProfileHasVoted ) {
			echo "<a href=\"idea_view.php?idea_vote=true&idea_vote_positive=true&idea_vote_idea_id=".$geny_idea->id."\" title=\"Voter pour cette idée\"><img src=\"images/".$web_config->theme."/smiley_up.png\" alt=\"Voter pour cette idée\"></a>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<a href=\"idea_view.php?idea_vote=true&idea_vote_negative=true&idea_vote_idea_id=".$geny_idea->id."\" title=\"Voter contre cette idée\"><img src=\"images/".$web_config->theme."/smiley_down.png\" alt=\"Voter contre cette idée\"></a>";
		}
		?>
		</center>

		<br><br>
		
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
						if( $profile->firstname && $profile->lastname ) {
							echo $profile->firstname." ".$profile->lastname;
						}
						else {
							echo $profile->login;
						}
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
		
		<table cellspacing="0" cellpadding="4" class="idea_message_table">
		<tbody>
			<tr>
			<th id="idea_message_table_header_author" class="idea_message_table_header">Auteurs</th>
			<th id="idea_message_table_header_content" class="idea_message_table_header">Commentaires</th>
			</tr>
			<?php
			$geny_idea_messages = $geny_idea_message->getIdeaMessagesListByIdeaId( $geny_idea->id );
			foreach( $geny_idea_messages as $idea_message ) {
				foreach( $geny_profile->getAllProfiles() as $profile ) {
				if( $idea_message->profile_id == $profile->id ) {
					if( $profile->firstname && $profile->lastname ) {
						$message_author = $profile->firstname." ".$profile->lastname;
					}
					else {
						$message_author = $profile->login;
					}
					break;
				}
				}
				echo "<tr id=\"idea_message_table_idea\"><td>".$message_author."</td><td>".$idea_message->content."</td></tr>";
			}
			if( $geny_idea->id < 0 ) {
				$geny_idea->loadIdeaById( $ideas[0]->id );
			}
			?>
		</tbody>
		</table>

		<br>

		<form id="idea_message_create_form" action="idea_view.php" method="post">
 			<input type="hidden" name="idea_message_create" value="true" />
 			<input type="hidden" name="idea_message_idea_id" value="<?php echo $geny_idea->id ?>" />
 			<label for="idea_message_content">Poster un commentaire</label>
			<textarea name="idea_message_content" id="idea_message_content" class="validate[required] text-input"></textarea>
			<input type="submit" id="idea_message_create_form_submit" value="Poster" />
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

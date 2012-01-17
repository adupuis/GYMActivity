<?php

//  Copyright (C) 2011 by GENYMOBILE & Quentin Désert
//  qdesert@genymobile.com
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

ini_set( 'error_reporting', E_ERROR );
ini_set( 'display_errors', 1 );

// Variable to configure global behaviour

$gritter_notifications = array();

$geny_idea = new GenyIdea();
$geny_idea_status = new GenyIdeaStatus();
$geny_idea_message = new GenyIdeaMessage();
$geny_idea_vote = new GenyIdeaVote();
$geny_profile = new GenyProfile();

$current_datetime = date("Y-m-d H:i:s");

$create_idea = GenyTools::getParam( 'create_idea', 'NULL' );
$load_idea = GenyTools::getParam( 'load_idea', 'NULL' );
$edit_idea = GenyTools::getParam( 'edit_idea', 'NULL' );
$idea_vote = GenyTools::getParam( 'idea_vote', 'NULL' );
$idea_message_create = GenyTools::getParam( 'idea_message_create', 'NULL' );

if( $create_idea == "true" ) {
	$idea_title = GenyTools::getParam( 'idea_title', 'NULL' );
	$idea_description = GenyTools::getParam( 'idea_description', 'NULL' );
	$idea_status = GenyTools::getParam( 'idea_status', 'NULL' );

	if( $idea_title != 'NULL' && $idea_description != 'NULL' ) {
		$insert_id = $geny_idea->insertNewIdea( 'NULL', $idea_title, $idea_description, $idea_vote, 1, $profile->id, $current_datetime );
		if( $insert_id ) {
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Idée créée avec succès.");
			$geny_idea->loadIdeaById( $insert_id );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de la création de l'idée.");
		}
	}
	else {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir.");
	}
}
else if( $load_idea == "true" ) {
	$idea_id = GenyTools::getParam( 'idea_id', 'NULL' );
	if( $idea_id != 'NULL' ) {
		$geny_idea->loadIdeaById( $idea_id );
	}
	else {
		$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger l'idée ",'msg'=>"id non spécifié.");
	}
}
else if( $idea_vote == "true" ) {
	$negative = 0;
	$positive = 0;
	$idea_vote_idea_id = GenyTools::getParam( 'idea_vote_idea_id', 'NULL' );
	if( $idea_vote_idea_id != 'NULL' ) {
		$geny_idea->loadIdeaById( $idea_vote_idea_id );
		$idea_vote_positive = GenyTools::getParam( 'idea_vote_positive', 'NULL' );
		$idea_vote_negative = GenyTools::getParam( 'idea_vote_negative', 'NULL' );
		$idea_vote_neutral = GenyTools::getParam( 'idea_vote_neutral', 'NULL' );
		
		if( $idea_vote_positive == "true" ) {
			// Positive vote
			$my_votes_for_this_idea = $geny_idea_vote->getIdeaVotesListByProfileAndIdeaId( $profile->id, $idea_vote_idea_id );
			if( count( $my_votes_for_this_idea ) > 0 ) {
				// I already voted for this idea
				$geny_idea_vote = $my_votes_for_this_idea[0];
				$negative = $geny_idea_vote->idea_negative_vote;
				$geny_idea_vote->updateInt( 'idea_negative_vote', 0 );
				$geny_idea_vote->updateInt( 'idea_positive_vote', 1 );
				if( $geny_idea_vote->commitUpdates() >= 1 ) {
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Finalement vous êtes pour!");
				}
				else {
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour du vote.");
				}
			}
			else {
				// I didn't vote for this idea before (or was neutral)
				if( $geny_idea_vote->insertNewIdeaVote( 'NULL', 1, 0, $profile->id, $idea_vote_idea_id ) ) {
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Vous avez voté pour !");
				}
				else {
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout du vote positif.");
				}
			}
			// Updating idea
			if( $negative == 1 ) {
				$geny_idea->updateInt( 'idea_votes', $geny_idea->votes + 2 );
			}
			else {
				$geny_idea->updateInt( 'idea_votes', $geny_idea->votes + 1 );
			}
		}
		else if( $idea_vote_negative == "true" ) {
			// Negative vote
			$my_votes_for_this_idea = $geny_idea_vote->getIdeaVotesListByProfileAndIdeaId( $profile->id, $idea_vote_idea_id );
			if( count( $my_votes_for_this_idea ) > 0 ) {
				// I already voted for this idea
				$geny_idea_vote = $my_votes_for_this_idea[0];
				$positive = $geny_idea_vote->idea_positive_vote;
				$geny_idea_vote->updateInt( 'idea_negative_vote', 1 );
				$geny_idea_vote->updateInt( 'idea_positive_vote', 0 );
				if( $geny_idea_vote->commitUpdates() >= 1 ) {
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Finalement vous êtes contre...");
				}
				else {
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour du vote.");
				}
			}
			else {
				// I didn't vote for this idea before (or was neutral)
				if( $geny_idea_vote->insertNewIdeaVote( 'NULL', 0, 1, $profile->id, $idea_vote_idea_id ) ) {
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Vous avez voté contre...");
				}
				else {
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout du vote négatif.");
				}
			}
			// Updating idea
			if( $positive == 1 ) {
				$geny_idea->updateInt( 'idea_votes', $geny_idea->votes - 2 );
			}
			else {
				$geny_idea->updateInt( 'idea_votes', $geny_idea->votes - 1 );
			}
		}
		else if( $idea_vote_neutral == "true" ) {
			// Neutral vote
			$my_votes_for_this_idea = $geny_idea_vote->getIdeaVotesListByProfileAndIdeaId( $profile->id, $idea_vote_idea_id );
			if( count( $my_votes_for_this_idea ) > 0 ) {
				// I already voted for this idea
				$geny_idea_vote = $my_votes_for_this_idea[0];
				$positive = $geny_idea_vote->idea_positive_vote;
				if( $geny_idea_vote->removeIdeaVote( $geny_idea_vote->id ) ) {
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Finalement vous n'avez pas d'avis sur la question...");
				}
				else {
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de la suppression du vote.");
				}
			}
			// Updating idea
			if( $positive == 1 ) {
				$geny_idea->updateInt( 'idea_votes', $geny_idea->votes - 1 );
			}
			else {
				$geny_idea->updateInt( 'idea_votes', $geny_idea->votes + 1 );
			}
		}
		// Committing idea update
		if( $geny_idea->commitUpdates() >= 1 ) {
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Idée mise à jour avec succès.");
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour de l'idée.");
		}
	}
	else {
		$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger l'idée ",'msg'=>"id non spécifié.");
	}
}
else if( $idea_message_create == "true" ) {
	$idea_message_idea_id = GenyTools::getParam( 'idea_message_idea_id', 'NULL' );
	if( $idea_message_idea_id != 'NULL' ) {
		$geny_idea->loadIdeaById( $idea_message_idea_id );
		$idea_message_content = GenyTools::getParam( 'idea_message_content', 'NULL' );
		if( $idea_message_content != 'NULL' ) {
			if( $geny_idea_message->insertNewIdeaMessage( 'NULL', $idea_message_content, $current_datetime, $profile->id, $idea_message_idea_id ) ) {
				$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Commentaire ajouté avec succès.");
// 				$geny_idea_message->sendMailForNewMessage( $profile->id, $_POST['idea_message_content'], $_POST['idea_message_idea_id'] );
			}
			else {
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout du commentaire.");
			}
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir.");
		}
	}
	else {
		$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger l'idée ",'msg'=>"id non spécifié.");
	}
}

?>
<div id="mainarea">
	<form id="select_idea_form" action="idea_view.php" method="post">
		<input type="hidden" name="load_idea" value="true" />
		<p>
			<label for="idea_id">Sélection idée</label>

			<select name="idea_id" id="idea_id" onChange="submit()" class="chzn-select">
			<?php
			$ideas = $geny_idea->getAllIdeas();
			foreach( $ideas as $idea ) {
				if( $geny_idea->id == $idea->id ) {
					echo "<option value=\"".$idea->id."\" selected>".$idea->title."</option>\n";
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
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
		</script>

		<br><br>
		

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
			if( $idea_vote->profile_id == $profile->id ) {
				if( $idea_vote->idea_positive_vote == 1 ) {
					$bProfileHasVoted = true;
					echo "<a href=\"loader.php?module=idea_view&idea_vote=true&idea_vote_negative=true&idea_vote_idea_id=".$geny_idea->id."\" title=\"Voter contre cette idée\"><img src=\"images/".$web_config->theme."/idea_vote_down_small.png\" alt=\"Voter contre cette idée\"></a>";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<a href=\"loader.php?module=idea_view&idea_vote=true&idea_vote_neutral=true&idea_vote_idea_id=".$geny_idea->id."\" title=\"Pas d'avis pour cette idée\"><img src=\"images/".$web_config->theme."/idea_vote_neutral_small.png\" alt=\"Pas d'avis pour cette idée\"></a>";
				}
				else if( $idea_vote->idea_negative_vote == 1 ) {
					$bProfileHasVoted = true;
					echo "<a href=\"loader.php?module=idea_view&idea_vote=true&idea_vote_positive=true&idea_vote_idea_id=".$geny_idea->id."\" title=\"Voter pour cette idée\"><img src=\"images/".$web_config->theme."/idea_vote_up_small.png\" alt=\"Voter pour cette idée\"></a>";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<a href=\"loader.php?module=idea_view&idea_vote=true&idea_vote_neutral=true&idea_vote_idea_id=".$geny_idea->id."\" title=\"Pas d'avis pour cette idée\"><img src=\"images/".$web_config->theme."/idea_vote_neutral_small.png\" alt=\"Pas d'avis pour cette idée\"></a>";
				}
				break;
			}
		}
		if( !$bProfileHasVoted ) {
			echo "<a href=\"loader.php?module=idea_view&idea_vote=true&idea_vote_positive=true&idea_vote_idea_id=".$geny_idea->id."\" title=\"Voter pour cette idée\"><img src=\"images/".$web_config->theme."/idea_vote_up_small.png\" alt=\"Voter pour cette idée\"></a>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<a href=\"loader.php?module=idea_view&idea_vote=true&idea_vote_negative=true&idea_vote_idea_id=".$geny_idea->id."\" title=\"Voter contre cette idée\"><img src=\"images/".$web_config->theme."/idea_vote_down_small.png\" alt=\"Voter contre cette idée\"></a>";
		}
		?>
		</center>

		<br><br>

		<div class='idea'>
			<div class="idea_submitter">
			Idée de 
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
			</div>
			<div class="idea_submission_date">
			<?php
 				echo date("j-m-Y G:i", strtotime( $geny_idea->submission_date ) );
			?>
			</div>
			<div class='idea_description'>
				<?php echo nl2br( $geny_idea->description ) ?>
				<br />
			</div>
		</div>

		<div id='idea_messages'>

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
			echo "<div class=\"idea_message\">";
			echo "<div class=\"idea_message_author\">";
			echo $message_author;
			echo "</div>";
			echo "<div class=\"idea_message_submission_date\">";
			echo date("j-m-Y G:i", strtotime( $idea_message->submission_date ) );
			echo "</div>";
			echo "<div class=\"idea_message_description\">";
			echo nl2br( $idea_message->content );
 			echo "</div></div>";
		}
		if( $geny_idea->id < 0 ) {
			$geny_idea->loadIdeaById( $ideas[0]->id );
		}
		?>

		</div>
		
		<br><br><br>

		<form id="idea_message_create_form" action="loader.php?module=idea_view" method="post">
 			<input type="hidden" name="idea_message_create" value="true" />
 			<input type="hidden" name="idea_message_idea_id" id="idea_message_idea_id" value="<?php echo $geny_idea->id ?>" />
 			<label for="idea_message_content">Poster un commentaire</label>
			<textarea name="idea_message_content" id="idea_message_content" class="validate[required] text-input"></textarea>
			<input type="submit" id="idea_message_create_form_submit" value="Poster" onclick="send_idea_message_mail()" />
		</form>
	</p>
</div>
<script>

	console.log( "javascript from idea_view." );

	function send_idea_message_mail() {

		var idea_message_idea_id = $('#idea_message_idea_id').val();
		var idea_message_profile_id = <?php echo $profile->id; ?>;
		var idea_message_content = $('#idea_message_content').val();
		$.get( "backend/api/send_idea_message_mail.php", { idea_id: idea_message_idea_id,
								   idea_message_profile_id: idea_message_profile_id,
								   idea_message_content: idea_message_content }
		);

	}

</script>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/idea_list.dock.widget.php','backend/widgets/idea_add.dock.widget.php');
?>

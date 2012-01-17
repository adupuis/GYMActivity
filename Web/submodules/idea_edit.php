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

// Variable to configure global behaviour


$gritter_notifications = array();

$geny_idea = new GenyIdea();
$geny_idea_status = new GenyIdeaStatus();

// $create_idea = GenyTools::getParam( 'create_idea', 'NULL' );
$load_idea = GenyTools::getParam( 'load_idea', 'NULL' );
$edit_idea = GenyTools::getParam( 'edit_idea', 'NULL' );

if( $load_idea == "true" ) {
	$idea_id = GenyTools::getParam( 'idea_id', 'NULL' );
	if( $idea_id != 'NULL' ) {
		$tmp_geny_idea = new GenyIdea();
		$tmp_geny_idea->loadIdeaById( $idea_id );
		if( $tmp_geny_idea->submitter == $profile->id ||
		    $profile->rights_group_id == 1  || /* admin */
		    $profile->rights_group_id == 2     /* superuser */ ) {
			$geny_idea->loadIdeaById( $idea_id );
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => "Impossible de charger l'idée ",'msg'=>"Vous n'êtes pas autorisé." );
			header( 'Location: error.php?category=idea&backlinks=idea_list,idea_add' );
		}
	}
	else {
		$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger l'idée ",'msg'=>"id non spécifié.");
	}
}
else if( $edit_idea == "true" ) {
	$idea_id = GenyTools::getParam( 'idea_id', 'NULL' );
	if( $idea_id != 'NULL' ) {
		$geny_idea->loadIdeaById( $idea_id );
		if($geny_idea->submitter == $profile->id            ||
		   $profile->rights_group_id == 1 /* admin */       ||
		   $profile->rights_group_id == 2 /* superuser */)  {
			$idea_title = GenyTools::getParam( 'idea_title', 'NULL' );
			$idea_description = GenyTools::getParam( 'idea_description', 'NULL' );
// 			$idea_vote = GenyTools::getParam( 'idea_vote', 'NULL' );
			$idea_status = GenyTools::getParam( 'idea_status', 'NULL' );

			if( $idea_title != 'NULL' && $geny_idea->title != $idea_title ) {
				$geny_idea->updateString( 'idea_title', $idea_title );
			}
			if( $idea_description != 'NULL' && $geny_idea->description != $idea_description ) {
				$geny_idea->updateString( 'idea_description', $idea_description );
			}
// 			if( $idea_vote != 'NULL' && $geny_idea->votes != $idea_vote ) {
// 				$geny_idea->updateInt( 'idea_vote', $idea_vote );
// 			}
			if( $idea_status != 'NULL' && $geny_idea->status != $idea_status ) {
				$geny_idea->updateInt( 'idea_status', $idea_status );
			}
		}
		if( $geny_idea->commitUpdates() ) {
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Idée mise à jour avec succès.");
			$geny_idea->loadIdeaById( $idea_id );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour de l'idée.");
		}
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de modifier l'idée ",'msg'=>"id non spécifié.");
	}
}

?>
<div id="mainarea">
	<p class="mainarea_title">
		<span class="idea_edit">
			Modifier une idée
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet d''éditer une idée existante. Tous les champs doivent être remplis.
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
		<form id="select_idea_form" action="loader.php?module=idea_edit" method="post">
			<input type="hidden" name="load_idea" value="true" />
			<p>
				<label for="idea_id">Sélection idée</label>

				<select name="idea_id" id="idea_id" onChange="submit()" class="chzn-select">
					<?php
					if( $profile->rights_group_id == 1 /* admin */ ||
					    $profile->rights_group_id == 2 /* superuser */ ) {
						$ideas = $geny_idea->getAllIdeas();
					}
					else {
						$ideas = $geny_idea->getIdeasListBySubmitter( $profile->id );
					}
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
		<form id="formID" action="loader.php?module=idea_edit" method="post">
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
<?php if( $profile->rights_group_id == 1 ): ?>
			<p>
				<label for="idea_status">Statut</label>
				<select name="idea_status" id="idea_status" class="chzn-select">
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
<?php endif; ?>
			<p>
				<input type="submit" value="Modifier" /> ou <a href="loader.php?module=idea_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/idea_list.dock.widget.php','backend/widgets/idea_add.dock.widget.php');
?>

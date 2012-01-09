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
$header_title = '%COMPANY_NAME% - Edition idée';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';

$gritter_notifications = array();

$geny_idea = new GenyIdea();
$geny_idea_status = new GenyIdeaStatus();

if( isset( $_POST['load_idea'] ) && $_POST['load_idea'] == "true" ) {
	if( isset( $_POST['idea_id'] ) ) {
		$geny_idea->loadIdeaById( $_POST['idea_id'] );
	}
	else {
		$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger l'idée ",'msg'=>"id non spécifié.");
	}
}
else if( isset( $_GET['load_idea'] ) && $_GET['load_idea'] == "true" ) {
	if( isset( $_GET['idea_id'] ) ) {
		$tmp_geny_idea = new GenyIdea();
		$tmp_geny_idea->loadIdeaById( $_GET['idea_id'] );
		if( $tmp_geny_idea->submitter == $profile->id ||
		    $profile->rights_group_id == 1  || /* admin */
		    $profile->rights_group_id == 2     /* superuser */ ) {
			$geny_idea->loadIdeaById( $_GET['idea_id'] );
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger l'idée ",'msg'=>"Vous n'êtes pas autorisé.");
			header( 'Location: error.php?category=idea&backlinks=idea_list,idea_add' );
		}
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger l'idée ",'msg'=>"id non spécifié.");
	}
}
else if( isset( $_POST['edit_idea'] ) && $_POST['edit_idea'] == "true" ) {
	if( isset( $_POST['idea_id'] ) ) {
		$geny_idea->loadIdeaById( $_POST['idea_id'] );
		if($geny_idea->submitter == $profile->id            ||
		   $profile->rights_group_id == 1 /* admin */       ||
		   $profile->rights_group_id == 2 /* superuser */)  {
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
		}
		if( $geny_idea->commitUpdates() ) {
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Idée mise à jour avec succès.");
			$geny_idea->loadIdeaById( $_POST['idea_id'] );
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

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/idea.png"/><p>Idée</p>
</div>

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
		<form id="select_idea_form" action="idea_edit.php" method="post">
			<input type="hidden" name="load_idea" value="true" />
			<p>
				<label for="idea_id">Sélection idée</label>

				<select name="idea_id" id="idea_id" onChange="submit()">
					<?php
					if( $profile->rights_group_id == 1 /* admin */ ||
					    $profile->rights_group_id == 2 /* superuser */ ) {
						$ideas = $geny_idea->getAllIdeas();
					}
					else {
						$ideas = $geny_idea->getIdeasListBySubmitter( $profile->id );
					}
					foreach( $ideas as $idea ) {
						if( ( isset( $_POST['idea_id'] ) && $_POST['idea_id'] == $idea->id ) || ( isset( $_GET['idea_id'] ) && $_GET['idea_id'] == $idea->id ) ) {
							echo "<option value=\"".$idea->id."\" selected>".$idea->title."</option>\n";
						}
						else if( isset($_POST['idea_name']) && $_POST['idea_name'] == $idea->title ) {
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
<?php if( $profile->rights_group_id == 1 ): ?>
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
<?php endif; ?>
			<p>
				<input type="submit" value="Modifier" /> ou <a href="idea_list.php">annuler</a>
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

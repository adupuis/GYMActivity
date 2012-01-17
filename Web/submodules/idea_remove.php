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

$handle = mysql_connect($web_config->db_host,$web_config->db_user,$web_config->db_password);
mysql_select_db($web_config->db_name);
mysql_query("SET NAMES 'utf8'");

$remove_idea = GenyTools::getParam( 'remove_idea', 'NULL' );

if( $remove_idea == "true" ) {
	$idea_id = GenyTools::getParam( 'idea_id', 'NULL' );
	if( $idea_id != 'NULL' ) {
		$force_remove_idea = GenyTools::getParam( 'force_remove', 'NULL' );
		if( $force_remove_idea == "true" ) {
			$id = GenyTools::getParam( 'idea_id', 'NULL' );
			$geny_idea->loadIdeaById( $id );
			error_log("[GYMActivity::DEBUG] after load geny_idea id : ".$geny_idea->id, 0 );
			if( $geny_idea->submitter == $profile->id		||
			    $profile->rights_group_id == 1 /* admin */		||
			    $profile->rights_group_id == 2 /* superuser */ ) {
				$query = "DELETE FROM Ideas WHERE idea_id=$id";
				if(! mysql_query($query)) {
					$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression de l'idée de la table Ideas.");
				}
				else
					$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Idée supprimée avec succès.");
			}
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours.");
		}
	}
	else {
		$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de supprimer l'idée ",'msg'=>"id non spécifié.");
	}
}
else {
// 	$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Aucune action spécifiée.");
}


?>
<div id="mainarea">
	<p class="mainarea_title">
		<span class="idea_remove">
			Supprimer une idée
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de <strong>supprimer définitivement</strong> une idée de la boîte à idées.
		</p>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
		</script>
		<script>
			jQuery(document).ready(function(){
				$("#select_login_form").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#select_login_form").validationEngine('attach');
			});
			
		</script>
		<form id="select_login_form" action="loader.php?module=idea_remove" method="post">
			<input type="hidden" name="remove_idea" value="true" />
			<p>
				<label for="idea_id">Séléction idée</label>

				<select name="idea_id" id="idea_id" class="chzn-select">
					<?php
					if( $profile->rights_group_id == 1 /* admin */ ||
					    $profile->rights_group_id == 2 /* superuser */ ) {
						$ideas = $geny_idea->getAllIdeas();
					}
					else {
						$ideas = $geny_idea->getIdeasListBySubmitter( $profile->id );
					}
					$idea_id = GenyTools::getParam( 'idea_id', 'NULL' );
					foreach( $ideas as $idea ) {
						if( $idea_id == $idea->id ) {
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
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression de l'idée. <strong>La suppression est définitive et ne pourra pas être annulée.</strong>
			</p>
			<p>
				<input type="submit" value="Supprimer" /> ou <a href="loader.php?module=idea_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/idea_list.dock.widget.php','backend/widgets/idea_add.dock.widget.php');
?>

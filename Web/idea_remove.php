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
$header_title = 'GENYMOBILE - Suppression idée';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';

$geny_idea = new GenyIdea();
$logged_in_profile = new GenyProfile();
$logged_in_profile->loadProfileByUsername( $_SESSION['USERID'] );

$handle = mysql_connect($web_config->db_host,$web_config->db_user,$web_config->db_password);
mysql_select_db($web_config->db_name);
mysql_query("SET NAMES 'utf8'");

if( isset($_POST['remove_idea']) && $_POST['remove_idea'] == "true" ) {
	if(isset($_POST['idea_id'])) {
		if( isset($_POST['force_remove']) && $_POST['force_remove'] == "true" ) {
			$id = $_POST['idea_id'];
			$query = "DELETE FROM Ideas WHERE idea_id=$id";
			if(! mysql_query($query)) {
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression de l'idée de la table Ideas.</li>\n";
			}
			else
				$db_status .= "<li class=\"status_message_success\">Idée supprimée avec succès.</li>\n";
		}
		else {
			$db_status .= "<li class=\"status_message_error\">Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours.</li>\n";
		}
	}
	else {
		$db_status .= "<li class=\"status_message_error\">Impossible de supprimer l'idée : id non spécifié.</li>\n";
	}
}
else {
// 	$db_status .= "<li class=\"status_message_error\">Aucune action spécifiée.</li>\n";
}


?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/project_generic.png"/><p>Idée</p>
</div>


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
			<?php
			if( isset($db_status) && $db_status != "" ){
				echo "<ul class=\"status_message\">\n$db_status\n</ul>";
			}
			?>
		<script>
			$(".status_message").click(function () {
			$(".status_message").fadeOut("slow");
			});
		</script>
		<script>
			jQuery(document).ready(function(){
				$("#select_login_form").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#select_login_form").validationEngine('attach');
			});
			
		</script>
		<form id="select_login_form" action="idea_remove.php" method="post">
			<input type="hidden" name="remove_idea" value="true" />
			<p>
				<label for="idea_id">Séléction idée</label>

				<select name="idea_id" id="idea_id">
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
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression de l'idée. <strong>La suppression est définitive et ne pourra pas être annulée.</strong>
			</p>
			<p>
				<input type="submit" value="Supprimer" /> ou <a href="#form">annuler</a>
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

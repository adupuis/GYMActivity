<?php
//  Copyright (C) 2011 by GENYMOBILE & Arnaud Dupuis
//  adupuis@genymobile.com
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
$geny_project = new GenyProject();

$handle = mysql_connect($web_config->db_host,$web_config->db_user,$web_config->db_password);
mysql_select_db($web_config->db_name);
mysql_query("SET NAMES 'utf8'");

if( isset($_POST['remove_project']) && $_POST['remove_project'] == "true" ){
	if(isset($_POST['project_id'])){
		if( isset($_POST['force_remove']) && $_POST['force_remove'] == "true" ){
			$id = mysql_real_escape_string($_POST['project_id']);
			$query = "DELETE FROM Assignements WHERE project_id=$id";
			if(! mysql_query($query)){
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression du projet de la table Assignements.");
			}
			$query = "DELETE FROM ProjectTaskRelations WHERE project_id=$id";
			if(! mysql_query($query)){
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression du projet de la table ProjectTaskRelations.");
			}
			$query = "DELETE FROM Projects WHERE project_id=$id";
			if(! mysql_query($query)){
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression du project de la table Projects.");
			}
			else
				$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Projet supprimé avec succès.");
		}
		else{
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours.");
		}
	}
	else  {
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Impossible de supprimer le profil utilisateur ','msg'=>"id non spécifié.");
	}
}
else{
// 	$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Aucune action spécifiée.");
}


?>
<div id="mainarea">
	<p class="mainarea_title">
		<span class="project_remove">
			Supprimer un profil
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de <strong>supprimer définitivement</strong> un profil dans la base des utilisateurs.
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
		<form id="select_login_form" action="loader.php?module=project_remove" method="post">
			<input type="hidden" name="remove_project" value="true" />
			<p>
				<label for="project_id">Séléction profil</label>

				<select name="project_id" id="project_id">
					<?php
						$query = "SELECT project_id,project_name FROM Projects";
						$result = mysql_query($query, $handle);
						
						while ($row = mysql_fetch_row($result)){
							if( (isset($_POST['project_id']) && $_POST['project_id'] == $row[0]) || (isset($_GET['project_id']) && $_GET['project_id'] == $row[0]) )
								echo "<option value=\"".$row[0]."\" selected>".$row[1]."</option>\n";
							else if( isset($_POST['project_name']) && $_POST['project_name'] == $row[1] )
								echo "<option value=\"".$row[0]."\" selected>".$row[1]."</option>\n";
							else
								echo "<option value=\"".$row[0]."\">".$row[1]."</option>\n";
						}
					?>
				</select>
			</p>
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression du projet. <strong>La suppression est définitive et ne pourra pas être annulée. La suppression d'un projet entraîne la suppression de toutes les associations avec des tâches ainsi que de tous les CRAs.</strong>
			</p>
			<p>
				<input type="submit" value="Supprimer" /> ou <a href="loader.php?module=project_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/project_list.dock.widget.php','backend/widgets/project_add.dock.widget.php');
?>

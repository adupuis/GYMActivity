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
$profile_firstname = "";
$profile_lastname = "";
$profile_email = "";
$profile_password = "";
$profile_is_active = "true";
$profile_needs_password_reset = "true";
$rights_group_id = 3;
$geny_profile = new GenyProfile();

$handle = mysql_connect($web_config->db_host,$web_config->db_user,$web_config->db_password);
mysql_select_db($web_config->db_name);
mysql_query("SET NAMES 'utf8'");

if( isset($_POST['remove_profile']) && $_POST['remove_profile'] == "true" ){
	if(isset($_POST['profile_id'])){
		if( isset($_POST['force_remove']) && $_POST['force_remove'] == "true" ){
			$id = mysql_real_escape_string($_POST['profile_id']);
			$query = "DELETE FROM IdeaMessages WHERE profile_id=$id";
			if(! mysql_query($query)){
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression du profil de la table IdeaMessages.");
			}
			$query = "DELETE FROM ActivityReports WHERE profile_id=$id";
			if(! mysql_query($query)){
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression du profil de la table ActivityReports.");
			}
			$query = "DELETE FROM Assignements WHERE profile_id=$id";
			if(! mysql_query($query)){
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression du profil de la table Assignements.");
			}
			$query = "DELETE FROM Activities WHERE profile_id=$id";
			if(! mysql_query($query)){
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression du profil de la table Activities.");
			}
			$query = "DELETE FROM Profiles WHERE profile_id=$id";
			if(! mysql_query($query)){
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la suppression du profil de la table Profiles.");
			}
			else
				$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Profil utilisateur supprimé avec succès.");
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
		<span class="profile_remove">
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
		<form id="select_login_form" action="loader.php?module=profile_remove" method="post">
			<input type="hidden" name="remove_profile" value="true" />
			<p>
				<label for="profile_id">Séléction profil</label>

				<select name="profile_id" id="profile_id" class="chzn-select">
					<?php
						$query = "SELECT profile_id,profile_login FROM Profiles";
						$result = mysql_query($query, $handle);
						
						while ($row = mysql_fetch_row($result)){
							if( (isset($_POST['profile_id']) && $_POST['profile_id'] == $row[0]) || (isset($_GET['profile_id']) && $_GET['profile_id'] == $row[0]) )
								echo "<option value=\"".$row[0]."\" selected>".$row[1]."</option>\n";
							else if( isset($_POST['profile_login']) && $_POST['profile_login'] == $row[1] )
								echo "<option value=\"".$row[0]."\" selected>".$row[1]."</option>\n";
							else
								echo "<option value=\"".$row[0]."\">".$row[1]."</option>\n";
						}
					?>
				</select>
			</p>
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression du profil. <strong>La suppression est définitive et ne pourra pas être annulée. La suppression d'un profil entraîne la suppression de tous ses messages, CRAs, reporting, etc.</strong>
			</p>
			<p>
				<input type="submit" value="Supprimer" /> ou <a href="loader.php?module=profile_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/profile_list.dock.widget.php','backend/widgets/profile_add.dock.widget.php');
?>

<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Suppression profil';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$db_status = "";
$profile_firstname = "";
$profile_lastname = "";
$profile_email = "";
$profile_password = "";
$profile_is_active = "true";
$profile_needs_password_reset = "true";
$rights_group_id = 3;
$geny_profile = new GenyProfile();

$handle = mysql_connect($web_config->db_host,$web_config->db_user,$web_config->db_password);
mysql_select_db("GYMActivity");
mysql_query("SET NAMES 'utf8'");

if( isset($_POST['remove_profile']) && $_POST['remove_profile'] == "true" ){
	if(isset($_POST['profile_id'])){
		if( isset($_POST['force_remove']) && $_POST['force_remove'] == "true" ){
			$id = mysql_real_escape_string($_POST['profile_id']);
			$query = "DELETE FROM IdeaMessages WHERE profile_id=$id";
			if(! mysql_query($query)){
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression du profil de la table IdeaMessages.</li>\n";
			}
			$query = "DELETE FROM ActivityReports WHERE profile_id=$id";
			if(! mysql_query($query)){
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression du profil de la table ActivityReports.</li>\n";
			}
			$query = "DELETE FROM Assignements WHERE profile_id=$id";
			if(! mysql_query($query)){
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression du profil de la table Assignements.</li>\n";
			}
			$query = "DELETE FROM Activities WHERE profile_id=$id";
			if(! mysql_query($query)){
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression du profil de la table Activities.</li>\n";
			}
			$query = "DELETE FROM Profiles WHERE profile_id=$id";
			if(! mysql_query($query)){
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression du profil de la table Profiles.</li>\n";
			}
			else
				$db_status .= "<li class=\"status_message_success\">Profil utilisateur supprimé avec succès.</li>\n";
		}
		else{
			$db_status .= "<li class=\"status_message_error\">Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours.</li>\n";
		}
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de supprimer le profil utilisateur : id non spécifié.</li>\n";
	}
}
else{
// 	$db_status .= "<li class=\"status_message_error\">Aucune action spécifiée.</li>\n";
}


?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/profile_generic.png"/><p>Profil</p>
</div>


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
		<form id="select_login_form" action="profile_remove.php" method="post">
			<input type="hidden" name="remove_profile" value="true" />
			<p>
				<label for="profile_id">Séléction profil</label>

				<select name="profile_id" id="profile_id">
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
				<input type="submit" value="Supprimer" /> ou <a href="#form">annuler</a>
			</p>
		</form>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php 
			include 'backend/widgets/profile_list.dock.widget.php';
			include 'backend/widgets/profile_add.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

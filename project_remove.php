<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Suppression projet';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$geny_project = new GenyProject();

$handle = mysql_connect($web_config->db_host,$web_config->db_user,$web_config->db_password);
mysql_select_db("GYMActivity");
mysql_query("SET NAMES 'utf8'");

if( isset($_POST['remove_project']) && $_POST['remove_project'] == "true" ){
	if(isset($_POST['project_id'])){
		if( isset($_POST['force_remove']) && $_POST['force_remove'] == "true" ){
			$id = mysql_real_escape_string($_POST['project_id']);
			$query = "DELETE FROM Assignements WHERE project_id=$id";
			if(! mysql_query($query)){
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression du projet de la table Assignements.</li>\n";
			}
			$query = "DELETE FROM Project_Task_Relations WHERE project_id=$id";
			if(! mysql_query($query)){
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression du projet de la table Project_Task_Relations.</li>\n";
			}
			$query = "DELETE FROM Projects WHERE project_id=$id";
			if(! mysql_query($query)){
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression du project de la table Projects.</li>\n";
			}
			else
				$db_status .= "<li class=\"status_message_success\">Projet supprimé avec succès.</li>\n";
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
	<img src="images/<?php echo $web_config->theme ?>/project_generic.png"/><p>Profil</p>
</div>


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
		<form id="select_login_form" action="project_remove.php" method="post">
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
				<input type="submit" value="Supprimer" /> ou <a href="#form">annuler</a>
			</p>
		</form>
	</p>
</div>

<?php
include_once 'footer.php';
?>

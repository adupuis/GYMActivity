<?php
// Variable to configure global behaviour
$header_title = 'GENYMOBILE - Suppression client';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$db_status = "";
$geny_client = new GenyClient();

$handle = mysql_connect($web_config->db_host,$web_config->db_user,$web_config->db_password);
mysql_select_db($web_config->db_name);
mysql_query("SET NAMES 'utf8'");

if( isset($_POST['remove_client']) && $_POST['remove_client'] == "true" ){
	if(isset($_POST['client_id'])){
		if( isset($_POST['force_remove']) && $_POST['force_remove'] == "true" ){
			$id = mysql_real_escape_string($_POST['client_id']);
			$query = "DELETE FROM Projects WHERE client_id=$id";
			if(! mysql_query($query)){
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression du client de la table Projects.</li>\n";
			}
			$query = "DELETE FROM Clients WHERE client_id=$id";
			if(! mysql_query($query)){
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression du client de la table Clients.</li>\n";
			}
			else
				$db_status .= "<li class=\"status_message_success\">Client supprimé avec succès.</li>\n";
		}
		else{
			$db_status .= "<li class=\"status_message_error\">Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours.</li>\n";
		}
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de supprimer le client : id non spécifié.</li>\n";
	}
}
else{
// 	$db_status .= "<li class=\"status_message_error\">Aucune action spécifiée.</li>\n";
}


?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/client_generic.png"/><p>Client</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="client_remove">
			Supprimer un client
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de <strong>supprimer définitivement</strong> un client dans la base des clients.
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
		<form id="select_login_form" action="client_remove.php" method="post">
			<input type="hidden" name="remove_client" value="true" />
			<p>
				<label for="client_id">Séléction client</label>

				<select name="client_id" id="client_id">
					<?php
						foreach( $geny_client->getAllClients() as $client ){
							if( (isset($_POST['client_id']) && $_POST['client_id'] == $client->id) || (isset($_GET['client_id']) && $_GET['client_id'] == $client->id) )
								echo "<option value=\"".$client->id."\" selected>".$client->name."</option>\n";
							else if( isset($_POST['client_name']) && $_POST['client_name'] == $client->name )
								echo "<option value=\"".$client->id."\" selected>".$client->name."</option>\n";
							else
								echo "<option value=\"".$client->id."\">".$client->name."</option>\n";
						}
					?>
				</select>
			</p>
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression du client. <strong>La suppression est définitive et ne pourra pas être annulée. La suppression d'un client entraîne la suppression de tous les projets rattachés à ce client !</strong>
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
			include 'backend/widgets/client_list.dock.widget.php';
			include 'backend/widgets/client_add.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

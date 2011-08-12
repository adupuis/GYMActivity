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
$header_title = '%COMPANY_NAME% - Edition client';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$geny_client = new GenyClient();
$db_status = '';

if( isset($_POST['create_client']) && $_POST['create_client'] == "true" ){
	if( isset($_POST['client_name']) && $_POST['client_name'] != "" ){
		if( $geny_client->insertNewClient('NULL',$_POST['client_name']) ){
			$db_status .= "<li class=\"status_message_success\">Client créé avec succès.</li>\n";
			$geny_client->loadClientByName($_POST['client_name']);
		}
		else{
			$db_status .= "<li class=\"status_message_error\">Erreur lors de la création du client.</li>\n";
		}
	}
	else {
		$db_status .= "<li class=\"status_message_error\">Certains champs obligatoires sont manquant. Merci de les remplir.</li>\n";
	}
}
else if( isset($_POST['load_client']) && $_POST['load_client'] == "true" ){
	if(isset($_POST['client_id'])){
		$geny_client->loadClientById($_POST['client_id']);
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de charger le client client : id non spécifié.</li>\n";
	}
}
else if( isset($_GET['load_client']) && $_GET['load_client'] == "true" ){
	if(isset($_GET['client_id'])){
		$geny_client->loadClientById($_GET['client_id']);
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de charger le client client : id non spécifié.</li>\n";
	}
}
else if( isset($_POST['edit_client']) && $_POST['edit_client'] == "true" ){
	if(isset($_POST['client_id'])){
		$geny_client->loadClientById($_POST['client_id']);
		if( isset($_POST['client_name']) && $_POST['client_name'] != "" && $geny_client->login != $_POST['client_name'] ){
			$geny_client->updateString('client_name',$_POST['client_name']);
		}
		
		if($geny_client->commitUpdates()){
			$db_status .= "<li class=\"status_message_success\">Client mis à jour avec succès.</li>\n";
			$geny_client->loadClientById($_POST['client_id']);
		}
		else{
			$db_status .= "<li class=\"status_message_error\">Erreur durant la mise à jour du client.</li>\n";
		}
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de modifier le client utilisateur : id non spécifié.</li>\n";
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
		<span class="client_edit">
			Modifier un client
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de modifier un client dans la base des utilisateurs.
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
		
		<form id="select_login_form" action="client_edit.php" method="post">
			<input type="hidden" name="load_client" value="true" />
			<p>
				<label for="client_id">Séléction client</label>

				<select name="client_id" id="client_id" onChange="submit()">
					<?php
						$clients = $geny_client->getAllClients();
						foreach( $clients as $client ){
							if( (isset($_POST['client_id']) && $_POST['client_id'] == $client->id) || (isset($_GET['client_id']) && $_GET['client_id'] == $client->id) )
								echo "<option value=\"".$client->id."\" selected>".$client->name."</option>\n";
							else if( isset($_POST['client_name']) && $_POST['client_name'] == $client->name )
								echo "<option value=\"".$client->id."\" selected>".$client->name."</option>\n";
							else
								echo "<option value=\"".$client->id."\">".$client->name."</option>\n";
						}
						if( $geny_client->id < 0 )
							$geny_client->loadClientById( $clients[0]->id );
					?>
				</select>
			</p>
		</form>

		<form id="start" action="client_edit.php" method="post">
			<input type="hidden" name="edit_client" value="true" />
			<input type="hidden" name="client_id" value="<?php echo $geny_client->id ?>" />
			<p>
				<label for="client_name">Name</label>
				<input name="client_name" id="client_name" type="text" class="validate[required] text-input" value="<?php echo $geny_client->name ?>"/>
			</p>
			
			<p>
				<input type="submit" value="Modifier" /> ou <a href="#form">annuler</a>
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

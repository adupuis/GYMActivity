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
$header_title = '%COMPANY_NAME% - Suppression tâche';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$db_status = "";
$geny_task = new GenyTask();

$handle = mysql_connect($web_config->db_host,$web_config->db_user,$web_config->db_password);
mysql_select_db($web_config->db_name);
mysql_query("SET NAMES 'utf8'");

if( isset($_POST['remove_task']) && $_POST['remove_task'] == "true" ){
	if(isset($_POST['task_id'])){
		if( isset($_POST['force_remove']) && $_POST['force_remove'] == "true" ){
			$id = mysql_real_escape_string($_POST['task_id']);
			$query = "DELETE FROM Activities WHERE task_id=$id";
			if(! mysql_query($query)){
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression de la tâche de la table Activities.</li>\n";
			}
			$query = "DELETE FROM ProjectTaskRelations WHERE task_id=$id";
			if(! mysql_query($query)){
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression de la tâche de la table ProjectTaskRelations.</li>\n";
			}
			$query = "DELETE FROM Tasks WHERE task_id=$id";
			if(! mysql_query($query)){
				$db_status .= "<li class=\"status_message_error\">Erreur durant la suppression de la tâche de la table Tasks.</li>\n";
			}
			else
				$db_status .= "<li class=\"status_message_success\">Tâche supprimée avec succès.</li>\n";
		}
		else{
			$db_status .= "<li class=\"status_message_error\">Veuillez cochez la case acquittant votre compréhension de la portée de l'opération en cours.</li>\n";
		}
	}
	else  {
		$db_status .= "<li class=\"status_message_error\">Impossible de supprimer le tâche : id non spécifié.</li>\n";
	}
}
else{
// 	$db_status .= "<li class=\"status_message_error\">Aucune action spécifiée.</li>\n";
}


?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/task_generic.png"/><p>Tâche</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="task_remove">
			Supprimer un tâche
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de <strong>supprimer définitivement</strong> un tâche dans la base des tâches.
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
		<form id="select_login_form" action="task_remove.php" method="post">
			<input type="hidden" name="remove_task" value="true" />
			<p>
				<label for="task_id">Séléction tâche</label>

				<select name="task_id" id="task_id">
					<?php
						foreach( $geny_task->getAllTasks() as $client ){
							if( (isset($_POST['task_id']) && $_POST['task_id'] == $client->id) || (isset($_GET['task_id']) && $_GET['task_id'] == $client->id) )
								echo "<option value=\"".$client->id."\" selected>".$client->name."</option>\n";
							else if( isset($_POST['task_name']) && $_POST['task_name'] == $client->name )
								echo "<option value=\"".$client->id."\" selected>".$client->name."</option>\n";
							else
								echo "<option value=\"".$client->id."\">".$client->name."</option>\n";
						}
					?>
				</select>
			</p>
			<p>
			<input type="checkbox" name="force_remove" value="true" class="validate[required] checkbox" /> Veuillez cocher cette case pour confirmer la suppression de la tâche. <strong>La suppression est définitive et ne pourra pas être annulée. La suppression d'un tâche entraîne la suppression de toutes les affectations aux projets ainsi que tous les rapports d'activités !</strong>
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
			include 'backend/widgets/task_list.dock.widget.php';
			include 'backend/widgets/task_add.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

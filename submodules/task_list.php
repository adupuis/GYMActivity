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


$geny_task = new GenyTask();

?>
<script>
	jQuery(document).ready(function(){
	
		var oTable = $('#task_list').dataTable( {
			"bDeferRender": true,
			"bJQueryUI": true,
			"bStateSave": true,
			"bAutoWidth": false,
			"sCookiePrefix": "GYMActivity_",
			"iCookieDuration": 60*60*24*365, // 1 year
			"sPaginationType": "full_numbers",
			"oLanguage": {
				"sSearch": "Recherche :",
				"sLengthMenu": "Tâche par page _MENU_",
				"sZeroRecords": "Aucun résultat",
				"sInfo": "Aff. _START_ à _END_ de _TOTAL_ enregistrements",
				"sInfoEmpty": "Aff. 0 à 0 de 0 enregistrements",
				"sInfoFiltered": "(filtré de _MAX_ enregistrements)",
				"oPaginate":{ 
					"sFirst":"Début",
					"sLast": "Fin",
					"sNext": "Suivant",
					"sPrevious": "Précédent"
				}
			}
		} );
	});
</script>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/task_list.png"></img>
		<span class="task_list">
			Liste des tâches
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des tâches dans la base des tâches.
		</p>
		<style>
			@import 'styles/<?php echo $web_config->theme ?>/task_list.css';
		</style>
		<div class="table_container">
		<p>
			<table id="task_list">
			<thead>
				<tr>
					<th>Nom</th>
					<th>Description</th>
					<th>Éditer</th>
					<th>Supprimer</th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach( $geny_task->getAllTasks() as $task ){
					echo "<tr><td>$task->name</td><td>$task->description</td><td><a href='loader.php?module=task_edit&load_task=true&task_id=$task->id' title='Éditer la tâche'><img src='images/$web_config->theme/task_edit_small.png' alt='Éditer la tâche' ></a></td><td><a href='loader.php?module=task_remove&task_id=$task->id' title='Supprimer définitivement la tâche'><img src='images/$web_config->theme/task_remove_small.png' alt='Supprimer définitivement la tâche'></a></td></tr>";
				}
			?>
			</tbody>
			</table>
		</p>
		</div>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/task_add.dock.widget.php');
?>

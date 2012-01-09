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


$geny_client = new GenyClient();

?>
<script>
	jQuery(document).ready(function(){
	
		var oTable = $('#client_list').dataTable( {
			"bJQueryUI": true,
			"bStateSave": true,
			"bAutoWidth": false,
// 			"sScrollY": 400,
// 			"bScrollCollapse": true,
			"sCookiePrefix": "GYMActivity_",
			"sPaginationType": "full_numbers",
			"oLanguage": {
				"sSearch": "Recherche :",
				"sLengthMenu": "Clients par page _MENU_",
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
		<span class="client_list">
			Liste des clients
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des clients dans la base des clients.
		</p>
		<style>
			@import 'styles/<?php echo $web_config->theme ?>/client_list.css';
		</style>
		<div class="table_container">
		<p>
			<table id="client_list">
				<thead>
				<tr><th>Nom</th><th>Éditer</th><th>Supprimer</th></tr>
				</thead>
				<tbody>
				<?php
					foreach( $geny_client->getAllClients() as $client ){
						echo "<tr class='centered'><td>$client->name</td><td><a href='loader.php?module=client_edit&load_client=true&client_id=$client->id'><img src='images/$web_config->theme/client_edit_small.png'></a></td><td><a href='loader.php?module=client_remove&client_id=$client->id'><img src='images/$web_config->theme/client_remove_small.png'></a></td></tr>";
					}
				?>
				</tbody>
			</table>
		</p>
		</div>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/notifications.dock.widget.php','backend/widgets/client_add.dock.widget.php');
?>

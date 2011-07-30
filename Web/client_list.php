<?php
// Variable to configure global behaviour
$header_title = 'GENYMOBILE - Liste clients';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$geny_client = new GenyClient();

?>

<script>
	jQuery(document).ready(function(){
	
		var oTable = $('#client_list').dataTable( {
			"bJQueryUI": true,
			"bAutoWidth": false,
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

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/client_generic.png"/><p>Client</p>
</div>


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
			@import 'styles/default/client_list.css';
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
						echo "<tr class='centered'><td>$client->name</td><td><a href='client_edit.php?load_client=true&client_id=$client->id'><img src='images/$web_config->theme/client_edit_small.png'></a></td><td><a href='client_remove.php?client_id=$client->id'><img src='images/$web_config->theme/client_remove_small.png'></a></td></tr>";
					}
				?>
				</tbody>
			</table>
		</p>
		</div>
	</p>
</div>
<div id="bottomdock">
	<ul>
		<?php
			include 'backend/widgets/client_add.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

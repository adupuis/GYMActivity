<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Liste clients';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

// $handle = mysql_connect($db_host,$db_user,$db_password);
// mysql_select_db("GYMActivity");
// mysql_query("SET NAMES 'utf8'");

$geny_client = new GenyClient();

?>

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
		<p>
		<!--
		client_id int auto_increment,
		client_name
		-->
			<table class="object_list">
			<tr><th>Nom</th><th>Éditer</th><th>Supprimer</th></tr>
			<?php
				foreach( $geny_client->getAllClients() as $client ){
					echo "<tr><td>$client->name</td><td><a href='client_edit.php?load_client=true&client_id=$client->id'><img src='images/$web_config->theme/client_edit_small.png'></a></td><td><a href='client_remove.php?client_id=$client->id'><img src='images/$web_config->theme/client_remove_small.png'></a></td></tr>";
				}
			?>
			</table>
		</p>
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

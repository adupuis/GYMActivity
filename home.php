<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Home';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/default/home.png"/><p>Home</p>
</div>

<div id="maindock">
	<ul>
		<li class="cra_add">
			<a href="cra_add.php">
				<span class="dock_item_title">Ajouter un CRA</span><br/>
				<span class="dock_item_content">Ajouter un rapport d'activité pour une période donnée.</span>
			</a>
		</li>
		<li class="cra_edit">
			<a href="cra_validation.php">
				<span class="dock_item_title">Valider des CRA</span><br/>
				<span class="dock_item_content">Voir la liste des CRA non validés afin de les valider.</span>
			</a>
		</li>
		<li class="conges_add">
			<a href="conges_add.php">
				<span class="dock_item_title">Poser un congé</span><br/>
				<span class="dock_item_content">Faire une demande de congés pour une période donnée.</span>
			</a>
		</li>
		<li class="conges_edit">
			<a href="conges_edit.php">
				<span class="dock_item_title">Modifier un congé</span><br/>
				<span class="dock_item_content">Editer/modifier une demande de congés qui a déjà été faite.</span>
			</a>
		</li>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

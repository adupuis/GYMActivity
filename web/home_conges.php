<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Congés';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/default/conges.png"/><p>Congés</p>
</div>


<div id="maindock">
	<ul>
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
		<li class="conges_remove">
			<a href="conges_remove.php">
				<span class="dock_item_title">Supprimer un congé</span><br/>
				<span class="dock_item_content">Supprimer une demande de congés qui a déjà été faite.</span>
			</a>
		</li>
		<li class="conges_list">
			<a href="conges_list.php">
				<span class="dock_item_title">Lister mes congés</span><br/>
				<span class="dock_item_content">Afficher la liste de tous les congés que vous avez déjà posé ainsi que votre solde de congés.</span>
			</a>
		</li>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

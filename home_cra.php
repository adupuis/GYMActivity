<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - CRA';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/default/cra.png"/><p>CRA</p>
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
			<a href="cra_edit.php">
				<span class="dock_item_title">Editer un CRA</span><br/>
				<span class="dock_item_content">Modifier un rapport d'activité précédemment saisi.</span>
			</a>
		</li>
		<li class="cra_remove">
			<a href="cra_remove.php">
				<span class="dock_item_title">Supprimer un CRA</span><br/>
				<span class="dock_item_content">Supprimer un rapport d'activité précédemment saisi.</span>
			</a>
		</li>
		<li class="cra_list">
			<a href="cra_list.php">
				<span class="dock_item_title">Lister mes CRA</span><br/>
				<span class="dock_item_content">Voir la liste de mes CRAs.</span>
			</a>
		</li>
	</ul>
</div>

<?php
include_once 'footer.php';
?>

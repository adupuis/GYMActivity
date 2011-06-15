<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Admin';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/default/admin.png"/><p>Admin</p>
</div>

<div id="maindock">
	<ul>
		<li class="cra_admin_generic">
			<a href="cra_validation.php">
				<span class="dock_item_title">Validation CRA</span><br/>
				<span class="dock_item_content">Validation des soumissions et des demandes de suppressions de CRAs.</span>
			</a>
		</li>
		<li class="conges_admin_generic">
			<a href="conges_validation.php">
				<span class="dock_item_title">Validation congés</span><br/>
				<span class="dock_item_content">Validation des soumissions et des demandes de suppressions de congés.</span>
			</a>
		</li>
		<li class="project_add">
			<a href="project_add.php">
				<span class="dock_item_title">Ajouter un projet</span><br/>
				<span class="dock_item_content">Ajouter un projet à la base de projets.</span>
			</a>
		</li>
		<li class="project_edit">
			<a href="project_edit.php">
				<span class="dock_item_title">Modifier un projet</span><br/>
				<span class="dock_item_content">Modifier un projet existant.</span>
			</a>
		</li>
		<li class="project_remove">
			<a href="project_remove.php">
				<span class="dock_item_title">Supprimer un projet</span><br/>
				<span class="dock_item_content">Supprimer un projet existant.</span>
			</a>
		</li>
		<li class="project_list">
			<a href="project_list.php">
				<span class="dock_item_title">Liste des projets</span><br/>
				<span class="dock_item_content">Lister tous les projets de GenY Mobile.</span>
			</a>
		</li>
		<li class="task_add">
			<a href="task_add.php">
				<span class="dock_item_title">Ajouter une tâche</span><br/>
				<span class="dock_item_content">Ajouter une tâche à la base de tâche.</span>
			</a>
		</li>
		<li class="task_edit">
			<a href="task_edit.php">
				<span class="dock_item_title">Modifier une tâche</span><br/>
				<span class="dock_item_content">Modifier une tâche existante.</span>
			</a>
		</li>
		<li class="task_remove">
			<a href="task_remove.php">
				<span class="dock_item_title">Supprimer une tâche</span><br/>
				<span class="dock_item_content">Supprimer une tâche existante.</span>
			</a>
		</li>
		<li class="task_list">
			<a href="task_list.php">
				<span class="dock_item_title">Liste des tâches</span><br/>
				<span class="dock_item_content">Lister toutes les tâches.</span>
			</a>
		</li>
		<li class="profile_add">
			<a href="profile_add.php">
				<span class="dock_item_title">Ajouter un profil</span><br/>
				<span class="dock_item_content">Ajouter un profil à la base de profils.</span>
			</a>
		</li>
		<li class="profile_edit">
			<a href="profile_edit.php">
				<span class="dock_item_title">Modifier un profil</span><br/>
				<span class="dock_item_content">Modifier un profil existant.</span>
			</a>
		</li>
		<li class="profile_remove">
			<a href="profile_remove.php">
				<span class="dock_item_title">Supprimer un profil</span><br/>
				<span class="dock_item_content">Supprimer un profil existant.</span>
			</a>
		</li>
		<li class="profile_list">
			<a href="profile_list.php">
				<span class="dock_item_title">Liste des profils</span><br/>
				<span class="dock_item_content">Lister tous les profils de GenY Mobile.</span>
			</a>
		</li>
		<li class="client_add">
			<a href="client_add.php">
				<span class="dock_item_title">Ajouter un client</span><br/>
				<span class="dock_item_content">Ajouter un client à la base de clients.</span>
			</a>
		</li>
		<li class="client_edit">
			<a href="client_edit.php">
				<span class="dock_item_title">Modifier un client</span><br/>
				<span class="dock_item_content">Modifier un client existant.</span>
			</a>
		</li>
		<li class="client_remove">
			<a href="client_remove.php">
				<span class="dock_item_title">Supprimer un client</span><br/>
				<span class="dock_item_content">Supprimer un client existant.</span>
			</a>
		</li>
		<li class="client_list">
			<a href="client_list.php">
				<span class="dock_item_title">Liste des clients</span><br/>
				<span class="dock_item_content">Lister tous les clients de GenY Mobile.</span>
			</a>
		</li>
		<li class="idea_add">
			<a href="idea_add.php">
				<span class="dock_item_title">Ajouter une idée</span><br/>
				<span class="dock_item_content">Ajouter une idée dans la boîte à idées.</span>
			</a>
		</li>
		<li class="idea_edit">
			<a href="idea_edit.php">
				<span class="dock_item_title">Modifier une idée</span><br/>
				<span class="dock_item_content">Modifier une idée existante.</span>
			</a>
		</li>
		<li class="idea_remove">
			<a href="idea_remove.php">
				<span class="dock_item_title">Supprimer une idée</span><br/>
				<span class="dock_item_content">Supprimer une idée existante.</span>
			</a>
		</li>
		<li class="idea_list">
			<a href="idea_list.php">
				<span class="dock_item_title">Liste des idées</span><br/>
				<span class="dock_item_content">Lister toutes les idées de la boîte à idée Geny Mobile.</span>
			</a>
		</li>
	</ul>
</div>


<?php
include_once 'footer.php';
?>

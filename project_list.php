<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Liste projets';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$geny_project = new GenyProject();
$geny_client = new GenyClient();
foreach( $geny_client->getAllClients() as $client ){
	$clients[$client->id] = $client;
}

$geny_pt = new GenyProjectType();
foreach( $geny_pt->getAllProjectTypes() as $pt ){
	$pts[$pt->id] = $pt;
}

$geny_ps = new GenyProjectStatus();
foreach( $geny_ps->getAllProjectStatus() as $ps ){
	$pss[$ps->id] = $ps;
}

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/project_generic.png"/><p>Projet</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="project_list">
			Liste des projets
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des projets dans la base des projets.
		</p>
		<p>
		<!--
		project_id int auto_increment,
		project_name varchar(200) not null default 'Undefined',
		project_description text,
		client_id int not null default 1,
		project_location varchar(200),
		project_start_date date not null,
		project_end_date date,
		project_type_id int,
		project_status_id int,
		-->
			<table class="object_list">
			<tr><th>Nom</th><th>Client</th><th>Localisation</th><th>Date début</th><th>Date fin</th><th>Type</th><th>Status</th><th>Description</th><th>Éditer</th><th>Supprimer</th></tr>
			<?php
				function getImage($bool){
					if($bool == 1)
						return 'images/'.$web_config->theme.'/button_success_small.png';
					else
						return 'images/'.$web_config->theme.'/button_error_small.png';
				}
				foreach( $geny_project->getAllProjects() as $tmp ){
					echo "<tr><td>$tmp->name</td><td>".$clients["$tmp->client_id"]->name."</td><td>$tmp->location</td><td>$tmp->start_date</td><td>$tmp->end_date</td><td>".$pts["$tmp->type_id"]->name."</td><td>".$pss["$tmp->status_id"]->name."</td><td>$tmp->description</td><td><a href='project_edit.php?load_project=true&project_id=$tmp->id' title='Editer le project'><img src='images/$web_config->theme/project_edit_small.png' alt='Editer le projet'></a></td><td><a href='project_remove.php?project_id=$tmp->id' title='Supprimer définitivement le project'><img src='images/$web_config->theme/project_remove_small.png' alt='Supprimer définitivement le projet'></a></td></tr>";
				}
			?>
			</table>
		</p>
	</p>
</div>

<?php
include_once 'footer.php';
?>

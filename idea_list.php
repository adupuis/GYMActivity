<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Liste idées';
$required_group_rights = 2;

include_once 'header.php';
include_once 'menu.php';

$geny_idea = new GenyIdea();

$geny_idea_status = new GenyIdeaStatus();
foreach( $geny_idea_status->getAllIdeaStatus() as $idea_status ) {
	$idea_statuses[$idea_status->id] = $idea_status;
}

$geny_profile = new GenyProfile();
foreach( $geny_profile->getAllProfiles() as $profile ) {
	$profiles[$profile->id] = $profile;
}

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/project_generic.png"/><p>Idées</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="idea_list">
			Boîte à idées
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des idées de la boîte à idées Geny Mobile.
		</p>
		<p>
			<table class="object_list">
			<tr><th>Titre</th><th>Votes</th><th>Statut</th><th>Soumetteur</th><th>Voir</th><th>Éditer</th><th>Supprimer</th></tr>
			<?php
				function getImage( $bool ) {
					if( $bool == 1 )
						return 'images/'.$web_config->theme.'/button_success_small.png';
					else
						return 'images/'.$web_config->theme.'/button_error_small.png';
				}

				foreach( $geny_idea->getAllIdeas() as $tmp ) {
					$profile = $profiles["$tmp->submitter"];
					if( $profile->firstname && $profile->lastname ) {
						$screen_name = $profile->firstname." ".$profile->lastname;
					}
					else {
						$screen_name = $profile->login;
					}
					echo "<tr><td>$tmp->title</td><td>".$tmp->votes."</td><td>".$idea_statuses["$tmp->status_id"]->name."</td><td>".$screen_name."</td><td><a href='idea_view.php?load_idea=true&idea_id=$tmp->id' title='Voir l'idée'><img src='images/$web_config->theme/project_edit_small.png' alt='Voir l'idée'></a></td><td><a href='idea_edit.php?load_idea=true&idea_id=$tmp->id' title='Editer l'idée'><img src='images/$web_config->theme/project_edit_small.png' alt='Editer l'idée'></a></td><td><a href='idea_remove.php?idea_id=$tmp->id' title='Supprimer définitivement l'idée'><img src='images/$web_config->theme/project_remove_small.png' alt='Supprimer définitivement l'idée'></a></td></tr>";
				}
			?>
			</table>
		</p>
	</p>
</div>

<?php
include_once 'footer.php';
?>

<?php
//  Copyright (C) 2011 by GENYMOBILE & Quentin Désert
//  qdesert@genymobile.com
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
$header_title = '%COMPANY_NAME% - Liste des Idées';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';

date_default_timezone_set('Europe/Paris');
$gritter_notifications = array();

$data_array = array();
$data_array_filters = array( 2 => array(), 3 => array() );


$geny_idea = new GenyIdea();

$geny_idea_status = new GenyIdeaStatus();
foreach( $geny_idea_status->getAllIdeaStatus() as $idea_status ) {
	$idea_statuses[$idea_status->id] = $idea_status;
}

$geny_profile = new GenyProfile();
foreach( $geny_profile->getAllProfiles() as $profile ) {
	$profiles[$profile->id] = $profile;
}

$logged_in_profile = new GenyProfile();
$logged_in_profile->loadProfileByUsername( $_SESSION['USERID'] );

$geny_idea_vote = new GenyIdeaVote();

foreach( $geny_idea->getAllIdeasSortedByVotes() as $tmp ) {

	
	$profile = $profiles["$tmp->submitter"];
	if( $profile->firstname && $profile->lastname ) {
		$screen_name = $profile->firstname." ".$profile->lastname;
	}
	else {
		$screen_name = $profile->login;
	}

	$view = "<a href=\"idea_view.php?load_idea=true&idea_id=$tmp->id\" title=\"Voir l'idée\"><img src=\"images/$web_config->theme/idea_view_small.png\" alt=\"Voir l'idée\"></a>";

	if( $tmp->submitter == $logged_in_profile->id ) {
		$edit = "<a href=\"idea_edit.php?load_idea=true&idea_id=$tmp->id\" title=\"Editer l'idée\"><img src=\"images/$web_config->theme/idea_edit_small.png\" alt=\"Editer l'idée\"></a>";
		$remove = "<a href=\"idea_remove.php?idea_id=$tmp->id\" title=\"Supprimer définitivement l'idée\"><img src=\"images/$web_config->theme/idea_remove_small.png\" alt=\"Supprimer définitiement l'idée\"></a>";
	}
	else {
		$edit = $edit = "<img src=\"images/$web_config->theme/idea_edit_small_disable.png\" alt=\"Editer l'idée\">";;
		$remove = "<img src=\"images/$web_config->theme/idea_remove_small_disable.png\" alt=\"Supprimer définitivement l'idée\">";
	}

	$data_array[] = array( $tmp->id, $tmp->title, $tmp->votes, $idea_statuses["$tmp->status_id"]->name, $screen_name, $view, $edit, $remove );

	if( ! in_array($idea_statuses["$tmp->status_id"]->name, $data_array_filters[2]) )
		$data_array_filters[2][] = $idea_statuses["$tmp->status_id"]->name;
	if( ! in_array($screen_name, $data_array_filters[3]) )
		$data_array_filters[3][] = $screen_name;
}

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/idea.png"/><p>Idées</p>
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
		<script>
			var indexData = new Array();
			<?php
				$cookie = json_decode($_COOKIE["GYMActivity_idea_list_php"]);
				
				$data_array_filters_html = array();
				foreach( $data_array_filters as $idx => $data ){
					$data_array_filters_html[$idx] = '<select><option value=""></option>';
					foreach( $data as $d ){
						if( isset($cookie) && htmlspecialchars_decode(urldecode($cookie->aaSearchCols[$idx][0]),ENT_QUOTES) == htmlspecialchars_decode($d,ENT_QUOTES) )
							$data_array_filters_html[$idx] .= '<option selected="selected" value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';
						else
							$data_array_filters_html[$idx] .= '<option value="'.htmlentities($d,ENT_QUOTES,'UTF-8').'">'.htmlentities($d,ENT_QUOTES,'UTF-8').'</option>';
					}
					$data_array_filters_html[$idx] .= '</select>';
				}
				foreach( $data_array_filters_html as $idx => $html ){
					echo "indexData[$idx] = '$html';\n";
				}
			?>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
				
				var oTable = $('#idea_list_table').dataTable( {
					"bJQueryUI": true,
					"bStateSave": true,
					"bAutoWidth": false,
					"sCookiePrefix": "GYMActivity_",
					"sPaginationType": "full_numbers",
					"oLanguage": {
						"sSearch": "Recherche :",
						"sLengthMenu": "Idées par page _MENU_",
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
				/* Add a select menu for each TH element in the table footer */
				/* i+1 is to avoid the first row wich contains a <input> tag without any informations */
				$("tfoot th").each( function ( i ) {
					if(i == 2 || i == 3){
						this.innerHTML = indexData[i];
						$('select', this).change( function () {
							oTable.fnFilter( $(this).val(), i );
						} );
					}
				} );
			
			});
			
		</script>
		<script>
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications($gritter_notifications,$web_config->theme);
			?>
		</script>
		<form id="formID" action="idea_list.php" method="post" class="table_container">
			<style>
				@import 'styles/<?php echo $web_config->theme ?>/idea_list.css';
			</style>
			<p>
				<table id="idea_list_table" style="color: black; width: 100%;">
					<thead>
						<th>Titre</th>
						<th>Votes</th>
						<th>Statut</th>
						<th>Soumetteur</th>
						<th>Voir</th>
						<th>Editer</th>
						<th>Supprimer</th>
					</thead>
					<tbody>
					<?php
						foreach( $data_array as $da ){
							echo "<tr><td>".$da[1]."</td><td>".$da[2]."</td><td>".$da[3]."</td><td>".$da[4]."</td><td><center>".$da[5]."</center></td><td><center>".$da[6]."</center></td><td><center>".$da[7]."</center></td></tr>";
						}
					?>
					</tbody>
					<tfoot>
						<th class="filtered">Titre</th>
						<th class="filtered">Votes</th>
						<th class="filtered">Statut</th>
						<th class="filtered">Soumetteur</th>
						<th class="filtered">Voir</th>
						<th class="filtered">Editer</th>
						<th class="filtered">Supprimer</th>
					</tfoot>
				</table>
			</p>
		</form>
	</p>
</div>

<div id="bottomdock">
	<ul>
		<?php 
			include 'backend/widgets/idea_add.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>
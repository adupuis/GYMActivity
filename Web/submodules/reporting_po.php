<?php
//  Copyright (C) 2011 by GENYMOBILE Arnaud Dupuis & Jean-Charles Leneveu
//  adupuis@genymobile.com & jcleneveu@genymobile.com
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

// déclaration de variables générales
$reporting_data = array();
$geny_project = new GenyProject();
$geny_profile = new GenyProfile();
$geny_client = new GenyClient();
$geny_daily_rate = new GenyDailyRate();
$geny_activity = new GenyActivity();
$geny_assignement = new GenyAssignement();
$geny_task = new GenyTask();
$geny_daily_rate = new GenyDailyRate();
$gritter_notifications = array();

// on récupère les dates fournies par l'utilisateur
$start_date = GenyTools::getCurrentMonthFirstDayDate();
$end_date = GenyTools::getCurrentMonthLastDayDate();
$reporting_start_date = GenyTools::getParam( 'reporting_start_date', $start_date );
$reporting_end_date = GenyTools::getParam( 'reporting_end_date', $end_date );
if( isset( $reporting_start_date ) && $reporting_start_date != "" && isset( $reporting_end_date ) && $reporting_end_date != "" ){
	if( date_parse( $reporting_start_date ) !== false && date_parse( $reporting_end_date )!== false ){
		if( $reporting_end_date >= $reporting_start_date ){
			$start_date = $reporting_start_date;
			$end_date = $reporting_end_date;
		}
		else
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur fatale','msg'=>"La date de fin doit être supérieure ou égale à la date de début de la période rapportée.");
	}
	else
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur fatale','msg'=>"Au moins une des dates fournies n'est pas une date valide. Merci de respecter le format yyyy-mm-dd.");
}

// création de 3 tableaux statiques contenant : les clients, les types de projet et les status
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

function getPoRateForActivityReport($ar_id){
	// Pseudo-code :
	// get activity for $ar_id
	// get project
	// get assignement 
	// get profile (not sure)
	// Try to get a daily rate with profile, project, task and corresponding dates
	// if no results : try to get a daily rate with profile, project and corresponding dates
	// if no results : try to get a daily rate with project, tasks and corresponding dates
	// if no results : try to get a daily rate with project and corresponding dates (not sure this case is allowed)
}

// les aggregations déterminent l'affichage et le tri des données
$aggregations = array( "project", "task", "profile");

// on se sert du cookie pour savoir par quoi il faut ventiler
if(array_key_exists('GYMActivity_reporting_po_table_reporting_po_php_aggregation_state', $_COOKIE)) {
	$ts_cookie = get_object_vars(json_decode( $_COOKIE['GYMActivity_reporting_po_table_reporting_po_php_aggregation_state'] ));
}

// par défault, on considère que l'on affiche le strict minimum => aucune aggregation n'est définie
$nb_of_enabled_aggregations = 0;

foreach( $aggregations as $aggregation ) {
	$aggregation_level["$aggregation"] = (bool) GenyTools::getParam( 'reporting_aggregation_level_'.$aggregation, false );
	if( isset( $ts_cookie ) ) {
		if( isset( $ts_cookie["$aggregation"] ) && $ts_cookie["$aggregation"] == true ) {
			$aggregation_level["$aggregation"] = true;
		}
	}
	if( $aggregation_level["$aggregation"] ) {
		$nb_of_enabled_aggregations++;
	}
}

// création du tableau contenant les filtres 
$data_array_filters = range( 0, $nb_of_enabled_aggregations );
foreach( $data_array_filters as $key => $data ) {
	$data_array_filters[$key] = array();
}

// on prend tous les dailyrate dans l'intervalle donné
foreach( $geny_daily_rate->getDailyRatesListWithRestrictions( array( "daily_rate_start_date >= \"$start_date\"", "daily_rate_end_date <= \"$end_date\"" ) ) as $geny_daily_rate ) {
	
	// on crée le reporting de data
	$reporting_data[] = array( "po_number" => $geny_daily_rate->po_number ,
				   "project_id" => $geny_daily_rate->project_id ,
				   "task_id" => $geny_daily_rate->task_id ,
				   "profile_id" => $geny_daily_rate->profile_id ,
				   "nb_consumed_days" => "unknow",
				   "nb_remaining_days" => "unknow",
				   "total_nb_day_po" => $geny_daily_rate->po_days );
		
	// on crée les données de filtres par la même occasion
	$geny_profile->loadProfileById( $geny_daily_rate->profile_id );
	$geny_project->loadProjectById( $geny_daily_rate->project_id );
	$geny_task->loadTaskById( $geny_daily_rate->task_id );
	
	if( ! in_array( $geny_daily_rate->po_number, $data_array_filters[0] ) )
		$data_array_filters[0][] = $geny_daily_rate->po_number;
	
	if( $aggregation_level["profile"] ) {
		if( ! in_array( GenyTools::getProfileDisplayName( $geny_profile ), $data_array_filters[$nb_of_enabled_aggregations] ) )
			$data_array_filters[$nb_of_enabled_aggregations][] = GenyTools::getProfileDisplayName( $geny_profile );
	}
	if( $aggregation_level["project"] ) {
		if( ! in_array( $geny_project->name, $data_array_filters[1] ) )
			$data_array_filters[1][] = $geny_project->name;
	}
	if( $aggregation_level["task"] ) {
		if( ! in_array( $geny_task->name,$data_array_filters[intval( 1 || $aggregation_level["project"] ) + 1] ) )
			$data_array_filters[intval( 1 || $aggregation_level["project"] ) + 1][] = $geny_task->name;
	}
}

// foreach( $geny_ar->getActivityReportsListWithRestrictions( array( "activity_report_status_id != $ars_p_user_approval_id", "activity_report_status_id != $ars_refused_id", "activity_report_status_id != $ars_removed_id" ) ) as $ar ){
// 	$geny_activity->loadActivityById( $ar->activity_id ); // Contient la charge et l'assignement_id
// 	// Nous ne voulons pas des absences non payé par l'entreprise dans l'aggregation par projet.
// 	// En revanche quand le mode d'aggrégation est par tâche nous le voulons.
// 	if( $aggregation_level["task"] || ($geny_activity->task_id != 8 && $geny_activity->task_id != 12 && $geny_activity->task_id != 19) ){ 
// 		if( $geny_activity->activity_date >= $start_date && $geny_activity->activity_date <= $end_date ){
// 			if( !isset( $reporting_data[$ar->profile_id] ) ){
// 				$reporting_data[$ar->profile_id] = array();
// 				$reporting_data_tasks[$ar->profile_id] = array();
// 			}
// 			if( !isset($reporting_data[$ar->profile_id][$geny_activity->assignement_id]) ){
// 				$reporting_data[$ar->profile_id][$geny_activity->assignement_id] = 0;
// 				$reporting_data_tasks[$ar->profile_id][$geny_activity->assignement_id] = array();
// 			}
// 			if( !isset($reporting_data_tasks[$ar->profile_id][$geny_activity->assignement_id][$geny_activity->task_id]) )
// 				$reporting_data_tasks[$ar->profile_id][$geny_activity->assignement_id][$geny_activity->task_id] = 0;
// 			$reporting_data[$ar->profile_id][$geny_activity->assignement_id] += $geny_activity->load;
// 			$reporting_data_tasks[$ar->profile_id][$geny_activity->assignement_id][$geny_activity->task_id] += $geny_activity->load;
// 			
// 			// Création des données de filtres par la même occasion
// 			$geny_profile->loadProfileById( $ar->profile_id );
// 			$geny_assignement->loadAssignementById($geny_activity->assignement_id);
// 			$geny_project->loadProjectById($geny_assignement->project_id);
// 			$geny_task->loadTaskById( $geny_activity->task_id );
// 			if( ! in_array(GenyTools::getProfileDisplayName($geny_profile),$data_array_filters[0]) )
// 				$data_array_filters[0][] = GenyTools::getProfileDisplayName($geny_profile);
// 			if( ! in_array($clients[$geny_project->client_id]->name,$data_array_filters[1]) )
// 				$data_array_filters[1][] = $clients[$geny_project->client_id]->name;
// 			if( ! in_array($geny_project->name,$data_array_filters[2]) )
// 				$data_array_filters[2][] = $geny_project->name;
// 			if( ! in_array($geny_task->name,$data_array_filters[3]) )
// 				$data_array_filters[3][] = $geny_task->name;
// 		}
// 	}
// }

// Création des données de reporting pour la charge par client ainsi que par projet
// WARNING : on sait pas à quoi ça sert => EST-CE NORMAL ?
// $load_by_clients = array();
// $load_by_projects = array();
// foreach( $reporting_data as $profile_id => $data ){
// 	$geny_profile->loadProfileById($profile_id);
// 	foreach( $data as $assignement_id => $total_load ){
// 		$geny_assignement->loadAssignementById($assignement_id);
// 		$geny_project->loadProjectById($geny_assignement->project_id);
// 		if( !isset($load_by_clients[$clients[$geny_project->client_id]->name]) )
// 			$load_by_clients[$clients[$geny_project->client_id]->name]=0;
// 		if( !isset($load_by_projects[$clients[$geny_project->client_id]->name."/".$geny_project->name]) )
// 			$load_by_projects[$clients[$geny_project->client_id]->name."/".$geny_project->name]=0;
// 		$load_by_clients[$clients[$geny_project->client_id]->name] += $total_load/8;
// 		$load_by_projects[$clients[$geny_project->client_id]->name."/".$geny_project->name] += $total_load/8;
// 	}
// }

// $load_by_projects_js_data = "";
// $tmp_array=array();
// foreach( $load_by_projects as $project => $load ){
// 	$tmp_array[]= "['$project', $load]";
// }
// $load_by_projects_js_data = implode(",",$tmp_array);

?>
<script>
	var indexData = new Array();
	<?php
		// on charge le cookie de filtrage de datafilter
		if(array_key_exists('GYMActivity_reporting_po_table_loader_php', $_COOKIE)) {
			$cookie = json_decode($_COOKIE["GYMActivity_reporting_po_table_loader_php"]);
		}
		
		// on génère les options de filtrage de datafilter en dynamique
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
		
			var oTable = $('#reporting_po_table').dataTable( {
				"bJQueryUI": true,
				"bStateSave": true,
				"bAutoWidth": false,
				"sCookiePrefix": "GYMActivity_",
				"iCookieDuration": 60*60*24*365, // 1 year
				"sPaginationType": "full_numbers",
				"oLanguage": {
					"sSearch": "Recherche :",
					"sLengthMenu": "Lignes par page _MENU_",
					"sZeroRecords": "Aucun résultat",
					"sInfo": "Aff. _START_ à _END_ de _TOTAL_ lignes",
					"sInfoEmpty": "Aff. 0 à 0 de 0 lignes",
					"sInfoFiltered": "(filtré de _MAX_ lignes)",
					"oPaginate":{ 
						"sFirst":"Début",
						"sLast": "Fin",
						"sNext": "Suivant",
						"sPrevious": "Précédent"
					}
				}
			} );
			/* Add a select menu for each TH element in the table footer */
			$("tfoot th").each( function ( i ) {
				if( i < <?php echo $nb_of_enabled_aggregations+1 ?> ){
					this.innerHTML = indexData[i];
					$('select', this).change( function () {
						oTable.fnFilter( $(this).val(), i );
					} );
				}
			} );
		});
</script>
<script type="text/javascript">
	
	<?php
		// on affiche les notifications
		displayStatusNotifications($gritter_notifications,$web_config->theme);
	?>
	
	jQuery(document).ready(function(){
		$("#formID").validationEngine('init');
		$("#formID").validationEngine('attach');
	});
	
	$(function() {
		$( "#reporting_start_date" ).datepicker();
		$( "#reporting_start_date" ).datepicker( "option", "showAnim", "slideDown" );
		$( "#reporting_start_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
		$( "#reporting_start_date" ).datepicker('setDate', <?php echo "'".$start_date."'"; ?>);
		$( "#reporting_start_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
		$( "#reporting_start_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
		$( "#reporting_start_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
		$( "#reporting_start_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
		$( "#reporting_start_date" ).datepicker( "option", "firstDay", 1 );
		$( "#reporting_end_date" ).datepicker();
		$( "#reporting_end_date" ).datepicker( "option", "showAnim", "slideDown" );
		$( "#reporting_end_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
		$( "#reporting_end_date" ).datepicker('setDate', <?php echo "'".$end_date."'"; ?>);
		$( "#reporting_end_date" ).datepicker( "option", "dayNames", ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] );
		$( "#reporting_end_date" ).datepicker( "option", "dayNamesShort", ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'] );
		$( "#reporting_end_date" ).datepicker( "option", "dayNamesMin", ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'] );
		$( "#reporting_end_date" ).datepicker( "option", "monthNames", ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre'] );
		$( "#reporting_end_date" ).datepicker( "option", "firstDay", 1 );
		
		$( "#reporting_start_date" ).change( function(){ $( "#reporting_end_date" ).val( $( "#reporting_start_date" ).val() ) } );
		
	});
	
</script>
<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/reporting_monthly_view.png"></img>
		<span class="reporting_monthly_view">
			Reporting bon de commande
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des PO ventilés par bon de commande pour la période sélectionnée (par défaut le mois en cours).<br/>
		Reporting des PO entre le <strong><?php echo $start_date; ?></strong> et le <strong><?php echo $end_date; ?></strong>.<br/>
		<?php
			// TODO : regarder si on a des conditions de restriction à afficher à l'utilisateur en fonction de l'aggregation_level
// 			if( $aggregation_level["truc"] )
// 				echo "<strong>Attention: BLABLA</strong>";
		?>
		</p>
		<style>
			@import 'styles/<?php echo $web_config->theme ?>/reporting_monthly_view.css';
		</style>
		<form id="formID" action="loader.php?module=reporting_po" method="post">
			<p>
				<label for="reporting_start_date">Date de début</label>
				<input name="reporting_start_date" id="reporting_start_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="reporting_end_date">Date de fin</label>
				<input name="reporting_end_date" id="reporting_end_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<script>
					function setCookie( name, value )
					{
						var date = new Date();
						date.setTime(date.getTime()+(24*60*60*365));
						var expires = "; expires="+date.toGMTString()+";";
						document.cookie = name + "=" + escape( value ) + expires;
					}
					function aggregationLevelChanged(){
						var aggregation_level = new Object();
						aggregation_level["task"] = Boolean($('#reporting_aggregation_level_task').attr('checked')) ;
						aggregation_level["project"] = Boolean($('#reporting_aggregation_level_project').attr('checked')) ;
						aggregation_level["profile"] = Boolean($('#reporting_aggregation_level_profile').attr('checked')) ;
						console.debug( aggregation_level );
						setCookie('GYMActivity_reporting_po_table_reporting_po_php_task_state', JSON.stringify(aggregation_level));
						$('#formID').submit();
					}
				</script>
				<input type="checkbox" id="reporting_aggregation_level_task" name="reporting_aggregation_level_task" onChange="aggregationLevelChanged()"
				<?php if( $aggregation_level["task"] ){ echo "checked"; } ?> /> <strong>Cochez</strong> la case pour ventiler la charge par <strong>tâche</strong>,<br>
				
				<input type="checkbox" id="reporting_aggregation_level_project" name="reporting_aggregation_level_project" onChange="aggregationLevelChanged()"
				<?php if( $aggregation_level["project"] ){ echo "checked"; } ?> /> <strong>Cochez</strong> la case pour ventiler la charge par <strong>projet</strong>,<br>
				
				<input type="checkbox" id="reporting_aggregation_level_profile" name="reporting_aggregation_level_profile" onChange="aggregationLevelChanged()"
				<?php if( $aggregation_level["profile"] ){ echo "checked"; } ?> /> <strong>Cochez</strong> la case pour ventiler la charge par <strong>profil</strong>.
			</p>
			<input type="submit" value="Ajuster le reporting" />
		</form>
		<div class="table_container">
		<p>
			
			<table id="reporting_po_table">
			<thead>
				<th>Po</th>
				<?php
					if( $aggregation_level["project"] ) {
						echo "<th>Projet</th>\n";
					}
					
					if( $aggregation_level["task"] ) {
						echo "<th>Tâche</th>\n";
					}
					
					if( $aggregation_level["profile"] ) {
						echo "<th>Profil</th>\n";
					}
				?>
				<th>Nbr. jours <strong>total</strong></th>
				<th>Nbr. jours <strong>restants</strong></th>
				<th>Nbr. jours <strong>consommés</strong></th>
			</thead>
			<tbody>
			<?php

				foreach( $reporting_data as $data ){
					$geny_profile->loadProfileById( $data["profile_id"] );
					$geny_project->loadProjectById( $data["project_id"] );
					$geny_task->loadTaskById( $data["task_id"] );
					echo "<tr><td>" . $data["po_number"]
						. ( ( $aggregation_level["project"] == true ) ? "</td><td>" . $geny_project->name : "" )
						. ( ( $aggregation_level["task"] == true ) ? "</td><td>" . $geny_task->name : "" )
						. ( ( $aggregation_level["profile"] == true ) ? "</td><td>" . GenyTools::getProfileDisplayName( $geny_profile ) : "" )
						. "</td><td>" . $data["total_nb_day_po"]
						. "</td><td>" . $data["nb_consumed_days"]
						. "</td><td>" . $data["nb_remaining_days"]
						. "</td></tr>";
				}
			?>
			</tbody>
			<tfoot>
				<th>Po.</th>
				<?php
					if( $aggregation_level["project"] ) {
						echo "<th>Projet</th>\n";
					}

					if( $aggregation_level["task"] ) {
						echo "<th>Tâche</th>\n";
					}

					if( $aggregation_level["profile"] ) {
						echo "<th>Profil</th>\n";
					}
				?>
				<th>Nb. jours <strong>total</strong></th>
				<th>Nb. jours <strong>restant</strong></th>
				<th>Nb. jours <strong>consommé</strong></th>
			</tfoot>
			</table>
		</p>
		</div>
		<p>
			<ul>
				<li id="chart_div1"></li>
				<li id="chart_div3"></li>
			</ul>
		</p>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/reporting_cra_completion.dock.widget.php','backend/widgets/reporting_cra_status.dock.widget.php');
?>

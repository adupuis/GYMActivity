<?php
//  Copyright (C) 2011 by GENYMOBILE & Arnaud Dupuis
//  adupuis@genymobile.com
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

include_once 'backend/api/ajax_toolbox.php';


function display_span_infos($id, $o, $predicted) {
	$geny_project = new GenyProject();
	$geny_client = new GenyClient();
	$geny_property = new GenyProperty();
	if($predicted) $message = "~ ";
	
	$geny_project->loadProjectById($id);
	$geny_client->loadClientById($geny_project->client_id);
	$param = "color_project_type_". $geny_project->type_id;
	$props = $geny_property->searchProperties($param);
	$geny_property = $props[0];
	$vals = $geny_property->getPropertyValues();
	echo '<div style="background-color:'.$vals[0]->content.'">&nbsp;&nbsp;' . $message . $geny_client->name . " - " . $geny_project->name . " : ${o}h</div>";
}

function display_project($geny_project, $projects, $final_id, $predictionTotale, $partial_id, $predictionPartielle) {
	$geny_property = new GenyProperty();
	$param = "color_project_type_". $geny_project->type_id;
	$props = $geny_property->searchProperties($param);
	$geny_property = $props[0];
	$vals = $geny_property->getPropertyValues();

	echo '<td style="background-color:'.$vals[0]->content.'" class="'.$geny_project->id.'">';
	echo '<a href="#" class="bulle"><div id="case">'.$final_id.' | '.$predictionTotale.'</div><span>';
	if($predictionTotale) echo '<div style="background-color:black">&nbsp;&nbsp;Prédiction</div>';
	foreach($projects as $id => $o) {
		display_span_infos($id, $o, 0);
	}

	if($predictionPartielle) display_span_infos($partial_id, $predictionPartielle, 1);
	echo '</span></a></td>';
}


$reporting_data = array();
$geny_project = new GenyProject();
$geny_profile = new GenyProfile();
$geny_client = new GenyClient();
$geny_assignement = new GenyAssignement();
$geny_task = new GenyTask();
$gritter_notifications = array();

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

$year = getParam('year',date("Y"));
$month = getParam('month',date("m"));
if($month < 10) $month = "0" . $month ; 
$lastday = date('t',mktime(0,0,0,$month+1,0,$year));
$ressources_start_date = "$year-$month-01" ;
$ressources_end_date = "$year-$month-$lastday";
$nbday = date('d',mktime(0,0,0,$month+1,0,$year));

if( isset($ressources_start_date) && $ressources_start_date != "" && isset($ressources_end_date) && $ressources_end_date != "" ){
	if( date_parse( $ressources_start_date ) !== false && date_parse( $ressources_end_date )!== false ){
		if( $ressources_end_date >= $ressources_start_date ){
			$start_date = $ressources_start_date;
			$end_date = $ressources_end_date;
		}
		else
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur fatale','msg'=>"Erreur interne : La date de fin doit être supérieure ou égale à la date de début de la période rapportée.");
	}
	else
		$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur fatale','msg'=>"Au moins une des dates fournies n'est pas une date valide.");
}

$geny_ar = new GenyActivityReport();

// récupération des ids de statut que l'on ne désire pas voir
$geny_ars = new GenyActivityReportStatus();
$geny_ars->loadActivityReportStatusByShortName('P_USER_VALIDATION');
$ars_p_user_approval_id = $geny_ars->id;
$geny_ars->loadActivityReportStatusByShortName('REMOVED');
$ars_removed_id = $geny_ars->id;
$geny_ars->loadActivityReportStatusByShortName('REFUSED');
$ars_refused_id = $geny_ars->id;

foreach( $geny_ar->getActivityReportsListWithRestrictions( array( "activity_report_status_id != $ars_p_user_approval_id", "activity_report_status_id != $ars_refused_id", "activity_report_status_id != $ars_removed_id" ) ) as $ar ){
	$geny_activity = new GenyActivity( $ar->activity_id ); // Contient la charge et l'assignement_id
	$geny_profile->loadProfileById( $ar->profile_id );
	$geny_assignement = new GenyAssignement($geny_activity->assignement_id);
	$geny_profil_management = new GenyProfileManagementData();
	$geny_profil_management->loadProfileManagementDataByProfileId( $ar->profile_id );

	// restriction par rapport à la date
	if( $geny_activity->activity_date >= $start_date && $geny_activity->activity_date <= $end_date ){
		
		if($geny_profile->is_active && $geny_profil_management->availability_date <= $end_date ){
			
			if( !isset( $reporting_data[$geny_profile->id] ) )
				$reporting_data[$geny_profile->id] = array();
			if( !isset( $reporting_data[$geny_profile->id][0] ) )
				$reporting_data[$geny_profile->id][0] = array();
			if( !isset( $reporting_data[$geny_profile->id][1] ) )
				$reporting_data[$geny_profile->id][1] = array();
			
			for($k=0; $k<2; $k++) {
				for($i=1; $i<=$nbday; $i++) {
					if( !isset( $reporting_data[$geny_profile->id][$k][$i] ) )
						$reporting_data[$geny_profile->id][$k][$i] = array();
									
					for($j=0; $j<4; $j++) {
						if( !isset( $reporting_data[$geny_profile->id][$k][$i][$j] ) )
							$reporting_data[$geny_profile->id][$k][$i][$j] = -1 ;
					}
				}
			}
			
			$load = intval($geny_activity->load);
			$day_act = intval(substr($geny_activity->activity_date,8,2));
			
			for($k=0; $k<2; $k++) {
				for($j=0; $j<4; $j++) {
					if($reporting_data[$geny_profile->id][$k][$day_act][$j] == -1 && $load > 0) {
						$reporting_data[$geny_profile->id][$k][$day_act][$j] = $geny_assignement->project_id;
						$load--;
					}
				}
			}
		}
	}
}
?>

<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/reporting_generic.png"></img>
		<span class="reporting_monthly_view">
			Tableau d'utilisation des ressources
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici le tableau d'utilisation des ressources du mois sélectionné (par défaut le mois en cours).<br/>
		Reporting d'utilisation des ressources entre le <strong><?php echo $start_date; ?></strong> et le <strong><?php echo $end_date; ?></strong>.<br/>
		</p>
		<style>
			@import 'styles/<?php echo $web_config->theme ?>/reporting_ressources_view.css';
		</style>
		<form id="formID" action="loader.php?module=reporting_ressources" method="post">
			<p>
				<label for="month">Mois</label>
				<select name="month" id="month" type="text" class="chzn-select"/>
				<option value="1" <?php if($month != NULL) { if($month == "01") echo "selected"; } else if(date("m") == 1) echo "selected"; ?>>Janvier</option>
				<option value="2" <?php if($month != NULL) { if($month == "02") echo "selected"; } else if(date("m") == 2) echo "selected"; ?>>Février</option>
				<option value="3" <?php if($month != NULL) { if($month == "03") echo "selected"; } else if(date("m") == 3) echo "selected"; ?>>Mars</option>
				<option value="4" <?php if($month != NULL) { if($month == "04") echo "selected"; } else if(date("m") == 4) echo "selected"; ?>>Avril</option>
				<option value="5" <?php if($month != NULL) { if($month == "05") echo "selected"; } else if(date("m") == 5) echo "selected"; ?>>Mai</option>
				<option value="6" <?php if($month != NULL) { if($month == "06") echo "selected"; } else if(date("m") == 6) echo "selected"; ?>>Juin</option>
				<option value="7" <?php if($month != NULL) { if($month == "07") echo "selected"; } else if(date("m") == 7) echo "selected"; ?>>Juillet</option>
				<option value="8" <?php if($month != NULL) { if($month == "08") echo "selected"; } else if(date("m") == 8) echo "selected"; ?>>Aout</option>
				<option value="9" <?php if($month != NULL) { if($month == "09") echo "selected"; } else if(date("m") == 9) echo "selected"; ?>>Septembre</option>
				<option value="10" <?php if($month != NULL) { if($month == "10") echo "selected"; } else if(date("m") == 10) echo "selected"; ?>>Octobre</option>
				<option value="11" <?php if($month != NULL) { if($month == "11") echo "selected"; } else if(date("m") == 11) echo "selected"; ?>>Novembre</option>
				<option value="12" <?php if($month != NULL) { if($month == "12") echo "selected"; } else if(date("m") == 12) echo "selected"; ?>>Décembre</option>
				</select>
			</p>
			<p>
				<label for="year">Année</label>
				<input name="year" id="year" type="text" style="padding:4px 0 4px 0;" value="<?php if($year != NULL) { echo $year; } else echo date("Y"); ?>" />
			</p>
			<input type="submit" value="Ajuster le reporting" />
		</form>
		<div class="table_container">
		<p>
			
			<table id="reporting_load_table">
			<tr><th><div id="names">Nom Prénom</div></th><?php for($i=1; $i<=$nbday; $i++) echo '<th><div id="case">'.($i)."</div></th>"; ?></tr>
			
			
			<?php
			
			$last_predictions = array();
			
			foreach( $reporting_data as $profile_id => $period_data ){
				
				$geny_profile->loadProfileById($profile_id);
				
				if(!isset($last_predictions[$profile_id])) $last_predictions[$profile_id] = -1;
				
				$name = substr(GenyTools::getProfileDisplayName($geny_profile),0,10);
				if($name != GenyTools::getProfileDisplayName($geny_profile)) $name = $name . "...";
				echo '<tr><th rowspan="2"><div id="names">'.$name.'</div></th>';
				
					foreach( $period_data as $period => $days_data ){
					
						if($period == 1) echo "<tr>";
					
						foreach($days_data as $day => $hours) {
						
							if(isset($projects)) unset($projects);
							$final_id = -1;
							$projects = array();
							$temp_top = 0;
							$temp_h = 0;
						
							foreach($hours as $hour) {
								if(isset($projects["$hour"]) && $hour != -1) $projects["$hour"]++;
								else if($hour != -1) $projects["$hour"] = 1;
							}
							
							foreach($projects as $id => $o) {
								if($o > $temp_top) {
									$final_id = $id;
									$temp_top = $o;
								}
								$temp_h += $o ;
							}
							
							$predictionTotale = 0;
							$predictionPartielle = 0;
							$partial_id = -1;
							
							if(date("N", mktime(0,0,0,$month, $day, $year)) != 6 && date("N", mktime(0,0,0,$month, $day, $year)) != 7) {
							
								$predictionPartielle = 4 - $temp_h;
								
								if($predictionPartielle > 2) $predictionTotale = 1;
								
								if($predictionTotale || $predictionPartielle) {
									$assignements = $geny_assignement->getActiveAssignementsListByProfileId($profile_id);
									if(sizeof($assignements) == 1) $temp_id = $assignements[0]->project_id;
									elseif(sizeof($assignements) >= 2) {
										if($last_predictions[$profile_id] == -1) {
											$temp_id = $assignements[0]->project_id;
											$last_predictions[$profile_id] = $assignements[0]->project_id;
										}
										else {
											$found_last_pred=false;
											for($i=0; $i<=sizeof($assignements); $i++)
											{
												if($found_last_pred) break;
												if($last_predictions[$profile_id] == $assignements[$i]->project_id) $found_last_pred = true;
											}
											if($i == sizeof($assignements)) $i = 0;
											$temp_id = $assignements[$i]->project_id;
											$last_predictions[$profile_id] = $assignements[$i]->project_id;
										}
									}
									else $temp_id = -1 ;
								}
								if($predictionPartielle) $partial_id = $temp_id;
								if($predictionTotale) $final_id = $temp_id;
							}
							
							$geny_project = new GenyProject();
							$geny_project->loadProjectById($final_id);
							
							if($geny_project->id > 0)
							{
								display_project($geny_project, $projects, $final_id, $predictionTotale, $partial_id, $predictionPartielle);
							}
							else echo '<td style="background-color:#D8D8D8;" class="empty"><div id="case"></div></td>';
						}
						echo "</tr>";
					}
				}
			?>
			</table>
		</p>
		</div>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/reporting_cra_completion.dock.widget.php','backend/widgets/reporting_cra_status.dock.widget.php');
?>
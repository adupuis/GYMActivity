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
			
			if( !isset( $reporting_data[$ar->profile_id] ) )
				$reporting_data[$ar->profile_id] = array();
			
			$nbday = date('d',mktime(0,0,0,$month+1,0,$year));
				
			for($i=0; $i<$nbday; $i++) {
				if( !isset( $reporting_data[$ar->profile_id][$i] ) )
					$reporting_data[$ar->profile_id][$i] = array();
				if( !isset( $reporting_data[$ar->profile_id][$i]["matin"] ) )
					$reporting_data[$ar->profile_id][$i]["matin"] = array();
				if( !isset( $reporting_data[$ar->profile_id][$i]["aprem"] ) )
					$reporting_data[$ar->profile_id][$i]["aprem"] = array();
								
				for($j=0; $j<2; $j++) {
					for($k=0; $k<4; $k++) {
						if( !isset( $reporting_data[$ar->profile_id][$i][$j][$k] ) )
							$reporting_data[$ar->profile_id][$i][$j][$k] = -1 ;
					}
				}
			}
			
			$load = $geny_activity->load;
			$day_act = substr($geny_activity->activity_date,8,2);
						
			for($j=0; $j<2; $j++) {
				for($k=0; $k<4; $k++) {
					if($reporting_data[$ar->profile_id][$day_act][$j][$k] == -1 && $load > 0) { // PROBLEME ICI
						$reporting_data[$ar->profile_id][$day_act][$j][$k] = $geny_assignement->project_id;
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
				<option value="1" <?php if(date("m") == 1) echo "selected"; ?>>Janvier</option>
				<option value="2" <?php if(date("m") == 2) echo "selected"; ?>>Février</option>
				<option value="3" <?php if(date("m") == 3) echo "selected"; ?>>Mars</option>
				<option value="4" <?php if(date("m") == 4) echo "selected"; ?>>Avril</option>
				<option value="5" <?php if(date("m") == 5) echo "selected"; ?>>Mai</option>
				<option value="6" <?php if(date("m") == 6) echo "selected"; ?>>Juin</option>
				<option value="7" <?php if(date("m") == 7) echo "selected"; ?>>Juillet</option>
				<option value="8" <?php if(date("m") == 8) echo "selected"; ?>>Aout</option>
				<option value="9" <?php if(date("m") == 9) echo "selected"; ?>>Septembre</option>
				<option value="10" <?php if(date("m") == 10) echo "selected"; ?>>Octobre</option>
				<option value="11" <?php if(date("m") == 11) echo "selected"; ?>>Novembre</option>
				<option value="12" <?php if(date("m") == 12) echo "selected"; ?>>Décembre</option>
				</select>
			</p>
			<p>
				<label for="year">Année</label>
				<input name="year" id="year" type="text" style="padding:4px 0 4px 0;" value="<?php echo date("Y"); ?>" />
			</p>
			<input type="submit" value="Ajuster le reporting" />
		</form>
		<div class="table_container">
		<p>
			
			<table id="reporting_load_table">
			<thead>
			</thead>
			<tbody>
			<tr><td>nom prénom</td><?php for($i=0; $i<$nbday; $i++) echo "<td>$i</td>"; ?></tr>
			<?php
				foreach( $reporting_data as $profile_id => $days_data ){
				
				$geny_profile->loadProfileById($profile_id);
				
				echo "<tr>";
				
				echo "<td>".GenyTools::getProfileDisplayName($geny_profile)."</td>";
				
					foreach( $days_data as $data ){
					
						foreach($data as $period => $hours) {
							
							foreach($hours as $hour) {
								if($hour > 0) $geny_project->loadProjectById($hour); // TO_EDIT
								echo "<td>". $geny_project->name ."</td>";
							}
						}
					}
					
				echo "</tr>";
				}
			?>
			</tbody>
			</table>
		</p>
		</div>
	</p>
</div>
<?php
	$bottomdock_items = array('backend/widgets/reporting_cra_completion.dock.widget.php','backend/widgets/reporting_cra_status.dock.widget.php');
?>
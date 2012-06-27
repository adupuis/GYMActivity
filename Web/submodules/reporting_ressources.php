<?php
//  Copyright (C) 2012 by GENYMOBILE & Jean-Charles Leneveu
//  jcleneveu@genymobile.com
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

setlocale( LC_TIME, 'fr_FR.utf8', 'fra' ); 

// déclaration des variables globales
$reporting_data = array();
$geny_activity = new GenyActivity();
$geny_project = new GenyProject();
$geny_project_type = new GenyProjectType();
$geny_profile = new GenyProfile();
$geny_client = new GenyClient();
$geny_activity_report = new GenyActivityReport();
$geny_assignement = new GenyAssignement();
$geny_assignements = array();
$geny_activity_report_status = new GenyActivityReportStatus();
$geny_profil_management = new GenyProfileManagementData();
$gritter_notifications = array();

// récupération des paramètres rentrés par l'utilisateur
$param_year = getParam( 'year', date( "Y" ) );
$param_month = getParam( 'month', date( "m" ) );

// on récupère un int qu'il faut tranformer en chaine de 2 caractères
if( intval( $param_month ) < 10 ) {
	$param_month = "0" . intval( $param_month );
}
$last_day_of_month = date( 't', mktime( 0, 0, 0, intval( $param_month ) + 1, 0, intval( $param_year ) ) );
$ressources_start_date = $param_year . '-' . $param_month . '-01' ;
$ressources_end_date = "$param_year-$param_month-$last_day_of_month";
$nb_day_in_month = date( 'd', mktime( 0, 0, 0, intval( $param_month ) + 1, 0, intval( $param_year ) ) );

if( $param_year != "" && $param_month != "" && is_numeric( $param_month ) && is_numeric( $param_year ) ) {
	if( date_parse( $ressources_start_date ) && date_parse( $ressources_end_date ) ) {
		if( $ressources_end_date >= $ressources_start_date ){
			$start_date = $ressources_start_date;
			$end_date = $ressources_end_date;
		}
		else {
			$start_date = date( "Y-m-01" );
			$end_date = date( "Y-m-d", mktime( 0, 0, 0, date( "m" )+ 1, 0, date( "Y" ) ) );
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur fatale','msg'=>"Erreur interne : La date de fin doit être supérieure ou égale à la date de début de la période rapportée." );
		}
	}
	else {
		$start_date = date( "Y-m-01" );
		$end_date = date( "Y-m-d", mktime( 0, 0, 0, date( "m" )+ 1, 0, date( "Y" ) ) );
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur fatale','msg'=>"Au moins une des dates fournies n'est pas une date valide." );
	}
}

// récupération des ids de status que l'on ne désire pas voir
$geny_activity_report_status->loadActivityReportStatusByShortName( 'P_USER_VALIDATION' );
$ars_p_user_approval_id = $geny_activity_report_status->id;
$geny_activity_report_status->loadActivityReportStatusByShortName( 'REMOVED' );
$ars_removed_id = $geny_activity_report_status->id;
$geny_activity_report_status->loadActivityReportStatusByShortName( 'REFUSED' );
$ars_refused_id = $geny_activity_report_status->id;

foreach( $geny_activity_report->getActivityReportsListWithRestrictions( array( "activity_report_status_id != $ars_p_user_approval_id", "activity_report_status_id != $ars_refused_id", "activity_report_status_id != $ars_removed_id" ) ) as $tmp_activity_report ){
	
	// on charge l'activité associée 
	$geny_activity->loadActivityById( $tmp_activity_report->activity_id );
	
	// restriction par rapport à la date
	if( $geny_activity->activity_date >= $start_date && $geny_activity->activity_date <= $end_date ) {
	
		// on charge toutes les informations rattachées à cet activity_report
		$geny_profile->loadProfileById( $tmp_activity_report->profile_id );
		$geny_assignement->loadAssignementById( $geny_activity->assignement_id );
		$geny_profil_management->loadProfileManagementDataByProfileId( $tmp_activity_report->profile_id );
		$tmp_numeric_activity_load = intval( $geny_activity->load );
		$day_act = intval( substr( $geny_activity->activity_date, 8, 2 ) );
		
		// restriction par rapport au profil
		if( $geny_profile->is_active && $geny_profil_management->availability_date <= $end_date ) {
			
			// initialisations du tableau de profile
			if( !isset( $reporting_data[$geny_profile->id] ) ) {
				$reporting_data[$geny_profile->id] = array();
			}
			// 0 = matin, 1 = aprem
			for( $period = 0; $period < 2; $period ++ ) {
				// initialisations du tableau du matin/aprem
				if( !isset( $reporting_data[$geny_profile->id][$period] ) ) {
					$reporting_data[$geny_profile->id][$period] = array();
				}
			
				// initialisation du tableau suivant les jours
				for( $day = 1; $day <= $nb_day_in_month; $day ++ ) {
					if( !isset( $reporting_data[$geny_profile->id][$period][$day] ) )
						$reporting_data[$geny_profile->id][$period][$day] = array();
					
					// initialisation du tableau suivant les heures
					for( $hour = 0; $hour < 4; $hour ++ ) {
						if( !isset( $reporting_data[$geny_profile->id][$period][$day][$hour] ) )
							$reporting_data[$geny_profile->id][$period][$day][$hour] = -1 ;
					}
				}
				// construction des données
				for( $hour = 0; $hour < 4; $hour ++ ) {
					if( $reporting_data[$geny_profile->id][$period][$day_act][$hour] == -1 && $tmp_numeric_activity_load > 0 ) {
						$reporting_data[$geny_profile->id][$period][$day_act][$hour] = $geny_assignement->project_id;
						$tmp_numeric_activity_load--;
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
		<form id="select_ressources_date" action="loader.php?module=reporting_ressources" method="post">
			<p>
				<label for="month">Mois</label>
				<select name="month" id="month" type="text" class="chzn-select"/>
				<?php
					for( $tmp_month_id = 1; $tmp_month_id <= 12; $tmp_month_id ++ ) {
						$is_month_option_selected = "";
						if( intval( $tmp_month_id ) == intval( $param_month ) ) {
							echo $is_month_option_selected = " selected ";
						}
						echo '<option value="' . $tmp_month_id . '"' . $is_month_option_selected . '>' . strftime( "%B", mktime(1, 1, 1, $tmp_month_id, 1, 1 ) ) . '</option>';
					}
				?>
				</select>
			</p>
			<p>
				<label for="year">Année</label>
				<input name="year" id="year" type="text" value="<?php
				
					if( $param_year != "" && is_numeric($param_year) ) {
						echo $param_year;
					}
					else {
						echo date( "Y" );
					}
				?>"/>
			</p>
			<input type="submit" value="Ajuster le reporting" />
		</form>
		<div class="table_container">
		<p>
			
			<table id="reporting_load_table">
			<tr><th><div id="names">Nom Prénom</div></th>
			<?php
				for( $tmp_day_id = 1; $tmp_day_id <= $nb_day_in_month; $tmp_day_id ++ ) {
					echo '<th><div id="case">' . ( $tmp_day_id ) . "</div></th>";
				}
			?>
			</tr>
			<?php
			
				$last_predictions = array();
				
				// on parcourt les données par profil
				foreach( $reporting_data as $tmp_profile_id => $tmp_period_data ) {
					
					// chargement du profil
					$geny_profile->loadProfileById( $tmp_profile_id );
					if( ! isset( $last_predictions[$tmp_profile_id] ) ) {
						$last_predictions[$tmp_profile_id] = -1;
					}
					
					// affichage du nom
					$displayed_profile_name = substr( GenyTools::getProfileDisplayName( $geny_profile ), 0, 10 );
					if( $displayed_profile_name != GenyTools::getProfileDisplayName( $geny_profile ) ) {
						$displayed_profile_name = $displayed_profile_name . "...";
					}
					echo '<tr><th rowspan="2"><div id="names">' . $displayed_profile_name . '</div></th>';
					
					// on parcourt les données par période (matin/aprem)
					foreach( $tmp_period_data as $tmp_period => $tmp_days_data ) {
					
						// si c'est l'aprem on commence une nouvelle ligne (sinon la ligne a déjà été commencée par le nom du profil)
						if( $tmp_period == 1 ) {
							echo "<tr>";
						}
					
						// on parcourt les données par jour
						foreach( $tmp_days_data as $tmp_day => $tmp_hours_data ) {
						
							$majority_project_id = -1; // id du projet donnant la couleur à la case
							$projects_list = array(); // listes des projets
							$total_prediction = 0;
							$partial_prediction = 0;
							$predicted_project_id = -1;
						
							// on fait un tableau des projets avec le nb d'heures associées
							foreach( $tmp_hours_data as $tmp_project_id ) {
								if( isset( $projects_list["$tmp_project_id"] ) && $tmp_project_id != -1 ) {
									$projects_list["$tmp_project_id"]++;
								}
								else if( $tmp_project_id != -1 ) {
									$projects_list["$tmp_project_id"] = 1;
								}
							}
							
							// on détermine le projet qui a eu le plus d'heure
							$tmp_top_nb_hour = 0;
							$tmp_total_nb_hour = 0;
							foreach( $projects_list as $tmp_project_id => $tmp_nb_hour ) {
								if( $tmp_nb_hour > $tmp_top_nb_hour ) {
									$majority_project_id = $tmp_project_id;
									$tmp_top_nb_hour = $tmp_nb_hour;
								}
								$tmp_total_nb_hour += $tmp_nb_hour ;
							}
								
							// on exclut le week-end de la prédiction : il est normal de ne pas avoir de cra le week-end...
							if( date( "N", mktime( 0, 0, 0, intval( $param_month ), intval( $tmp_day ), intval( $param_year ) ) ) != "6" && date( "N", mktime( 0, 0, 0, intval( $param_month ), intval( $tmp_day ), intval( $param_year ) ) ) != "7") {
							
								// la prediction partielle est le nombre d'heures qu'il manque à la période pour être pleine
								$partial_prediction = 4 - $tmp_total_nb_hour;
								
								// si jamais la prédiction fait plus de la moitié de la période, la prédiction sera totale (cad qu'elle déterminera la couleur de la case)
								if( $partial_prediction > 2 ) {
									$total_prediction = 1;
								}
								
								// on détermine le projet à prendre pour les prédictions
								if( $partial_prediction ) {
									$geny_assignements = $geny_assignement->getActiveAssignementsListByProfileId( $tmp_profile_id );
									// cas simple : l'utilisateur n'est rattaché qu'à un seul projet => on prend celui-là
									if( sizeof( $geny_assignements ) == 1 ) {
										$temp_project_id = $geny_assignements[0]->project_id;
									}
									// cas plus tordu : on détermine le projet à considérer en fonction des prédictions précédentes
									else if( sizeof( $geny_assignements ) >= 2 ) {
										// si il n'y a pas de précédentes prédiction, on prend le premier projet de l'utilisateur
										if( $last_predictions[$tmp_profile_id] == -1 ) {
											$temp_project_id = $geny_assignements[0]->project_id;
											$last_predictions[$tmp_profile_id] = $geny_assignements[0]->project_id;
										}
										// sinon, on cherche le projet qui suit la précédente prédiction
										else {
											// on cherche la prédiction précédente
											for( $tmp_cpt = 0; $tmp_cpt <= sizeof( $geny_assignements ); $tmp_cpt ++ ) {
												if( $last_predictions[$tmp_profile_id] == $geny_assignements[$tmp_cpt]->project_id ) {
													break;
												}
											}
											// on prend le projet suivant
											$tmp_cpt++;
											// si on a atteint la fin de la liste de projet, on prend le premier projet
											if( $tmp_cpt == sizeof( $geny_assignements ) ) {
												$tmp_cpt = 0;
											}
											$temp_project_id = $geny_assignements[$tmp_cpt]->project_id;
											$last_predictions[$tmp_profile_id] = $geny_assignements[$tmp_cpt]->project_id;
										}
									}
									else {
										$temp_project_id = -1 ;
									}
								}
								if( $partial_prediction ) {
									$predicted_project_id = $temp_project_id;
								}
								if( $total_prediction ) {
									$majority_project_id = $temp_project_id;
								}
							}
							
							$geny_project->loadProjectById( $majority_project_id );
								
							// on affiche une case colorée en fonction du type de projet si l'id > 0
							if( $majority_project_id > 0 ) {
							
								// on récupère la couleur associée au type de projet
								$project_type_background_color = $geny_project_type->getProjectTypeColor( $geny_project->type_id );
								
								// on affiche la case
								echo '<td style="background-color:' . $project_type_background_color . '" class="' . $geny_project->id . '">';
								echo '<a href="#" class="bulle"><div id="case">'.$majority_project_id.'</div><span>';
								
								// si la prédiction a détérminé la couleur de la case, on affiche qu'il s'agit d'une prédiction
								if( $total_prediction ) {
									echo '<div id="predicted-span-info">Prédiction</div>';
								}
								
								// quoiqu'il arrive, on affiche les projets des cra déclarés par l'utilisateur
								foreach( $projects_list as $tmp_project_id => $tmp_nb_hour ) {
									// chargement des données
									$geny_project->loadProjectById( $tmp_project_id );
									$geny_client->loadClientById( $geny_project->client_id );
									
									// récupération de la couleur du span en fonction de type de projet
									$project_type_background_color = $geny_project_type->getProjectTypeColor( $geny_project->type_id );
									
									// affichage du div
									echo '<div id="span-info" style="background-color:' . $project_type_background_color . '">' . $geny_client->name . " - " . $geny_project->name . " : ${tmp_nb_hour}h</div>";
								}
								
								// si on a fait des prédictions partielles, on affiche les cra que l'on a prédit
								if( $partial_prediction ) {
									// chargement des données
									$geny_project->loadProjectById( $predicted_project_id );
									$geny_client->loadClientById( $geny_project->client_id );
									
									// récupération de la couleur du span en fonction de type de projet
									$project_type_background_color = $geny_project_type->getProjectTypeColor( $geny_project->type_id );
									
									// affichage du div
									echo '<div id="span-info" style="background-color:' . $project_type_background_color . '">~ ' . $geny_client->name . " - " . $geny_project->name . " : ${partial_prediction}h</div>";
								}
								
								echo '</span></a></td>';
							}
							//sinon si l'id < 0, on affiche une case grise
							else {
								echo '<td class="empty"><div id="case"></div></td>';
							}
							unset( $projects_list );
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
	$bottomdock_items = array( 'backend/widgets/reporting_cra_completion.dock.widget.php','backend/widgets/reporting_cra_status.dock.widget.php' );
?>
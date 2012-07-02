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

include_once 'backend/api/ajax_toolbox.php';

setlocale( LC_TIME, 'fr_FR.utf8', 'fra' ); 

// déclaration des variables globales
$reporting_data = array();
$last_prediction = array();
$geny_project = new GenyProject();
$geny_project_type = new GenyProjectType();
$geny_profile = new GenyProfile();
$geny_client = new GenyClient();
$geny_assignement = new GenyAssignement();
$activity_report_ressources = new GenyActivityReportRessources();
$geny_assignements = array();
$geny_profil_management = new GenyProfileManagementData();
$gritter_notifications = array();

// récupération des paramètres rentrés par l'utilisateur
$param_year = getParam( 'year', date( "Y" ) );
$param_month = getParam( 'month', date( "m" ) );

// on récupère un int qu'il faut tranformer en chaine de 2 caractères
if( intval( $param_month ) < 10 ) {
	$param_month = "0" . intval( $param_month );
}

// si le mois et l'année données par l'utilisateur sont correctes, on crée une chaine "YYYY-MM-" qui va servir à créer facilement des dates à partir de cette chaine
if( is_numeric( $param_year ) && is_numeric( $param_month ) && strlen( $param_year ) == 4 && strlen( $param_month ) == 2 ) {
	$month = intval( $param_month );
	$year = intval( $param_year );
	$nb_day_in_month = date( 'd', mktime( 0, 0, 0, intval( $param_month ) + 1, 0, intval( $param_year ) ) );
}
// sinon par défaut on met le mois et l'année courante
else {
	$month = intval( date( "m" ) );
	$year = intval( date( "Y" ) );
	$nb_day_in_month = date( 'd', mktime( 0, 0, 0, intval( date( "m" ) ) + 1, 0, intval( date( "Y" ) ) ) );
}

// on initialise le tableau de données par profil
foreach( $geny_profile->getAllProfiles() as $tmp_profile ) {

	// on charge les informations annexes associées au profil
	$geny_profil_management->loadProfileManagementDataByProfileId( $tmp_profile->id );
	$geny_assignements = $geny_assignement->getActiveAssignementsListByProfileId( $tmp_profile->id );
	
	// restriction de profil => on ne prend que les gens qui ont des projets en cours et qui sont disponibles
	if( $tmp_profile->is_active && $geny_profil_management->availability_date <= ( date( "Y-m-d", mktime( 0, 0, 0, $month, $nb_day_in_month, $year ) ) ) && sizeof( $geny_assignements ) > 0 ) {
		if( !isset( $reporting_data[$tmp_profile->id] ) ) {
		
			// si le tableau contenant l'identifiant du dernier projet prédit 
			// de cet utilisateur n'existe pas, on l'initialise
			if( !isset( $last_prediction[$tmp_profile->id] ) )
				$last_prediction[$tmp_profile->id] = -1;
			
			// on initialise aussi le tableau contenant les données de reporting
			$reporting_data[$tmp_profile->id] = array();
			
			// on ajoute le profil à la liste des profils actifs
			$active_profile_ids[] = $tmp_profile->id;
			
			// on continue d'initialiser le tableau de reporting
			// (par demi-journées, puis par journées)
			// 0 = matin, 1 = aprem
			for( $half_day = 0; $half_day < 2; $half_day ++ ) {
			
				// initialisations du tableau du matin/aprem
				if( !isset( $reporting_data[$tmp_profile->id][$half_day] ) ) {
					$reporting_data[$tmp_profile->id][$half_day] = array();
				}
			
				// initialisation du tableau suivant les jours
				for( $day = 1; $day <= $nb_day_in_month; $day ++ ) {
					if( !isset( $reporting_data[$tmp_profile->id][$half_day][$day] ) )
						$reporting_data[$tmp_profile->id][$half_day][$day] = array();
					
					if( !isset( $reporting_data[$tmp_profile->id][$half_day][$day]["majority_project_id"] ) )
						$reporting_data[$tmp_profile->id][$half_day][$day]["majority_project_id"] = -1 ;
					if( !isset( $reporting_data[$tmp_profile->id][$half_day][$day]["total_prediction"] ) )
						$reporting_data[$tmp_profile->id][$half_day][$day]["total_prediction"] = false ;
					if( !isset( $reporting_data[$tmp_profile->id][$half_day][$day]["cras"] ) )
						$reporting_data[$tmp_profile->id][$half_day][$day]["cras"] = array() ;
				}
			}
		}
	}
}

// on parcourt tous les profils actifs
foreach( $active_profile_ids as $tmp_profile_id ) {
	// et tous les jours du mois
	for( $day = 1; $day <= $nb_day_in_month; $day ++ ) {
	
		// on concatène la date
		$tmp_date = date( "Y-m-d", mktime( 0, 0, 0, $month, $day, $year ) );
		
		// on parcourt tous les cras déclarés
		foreach( $activity_report_ressources->getActivityReportsRessourcesFromDateAndProfileId( $tmp_date, $tmp_profile_id ) as $tmp_ressources ) {
			// la charge
			$tmp_numeric_activity_load = intval( $tmp_ressources->activity_load );
			
			// pour chaque demi-journée
			for( $half_day = 0; $half_day < 2; $half_day ++ ) {
				$tmp_total_h[$half_day] = 0;
				// on cherche combien on a d'heures pour cette période pour cet utilisateur en mémoire
				foreach( $reporting_data[$tmp_profile_id][$half_day][$day]["cras"] as $cra ) {
					$tmp_total_h[$half_day] += $cra["nb_h"];
				}
				// si la période n'est pas remplie
				if( $tmp_total_h[$half_day] < 4 && $tmp_numeric_activity_load > 0) {
					// si jamais la charge du cra en cours rentre ENTIEREMENT dans la période
					if( $tmp_total_h[$half_day] + $tmp_numeric_activity_load < 4 ) {
						$reporting_data[$tmp_profile_id][$half_day][$day]["cras"][] = array( "project_id" => $tmp_ressources->project_id ,
														"nb_h" => $tmp_numeric_activity_load ,
														"predicted" => false ) ;
						$tmp_total_h[$half_day] += $tmp_numeric_activity_load;
						$tmp_numeric_activity_load = 0;
					}
					else {
						$reporting_data[$tmp_profile_id][$half_day][$day]["cras"][] = array( "project_id" => $tmp_ressources->project_id ,
														"nb_h" => 4 - $tmp_total_h[$half_day] ,
														"predicted" => false ) ;
						$tmp_numeric_activity_load -= 4 - $tmp_total_h[$half_day] ;
						$tmp_total_h[$half_day] = 4;

					}
				}
			}
			// éventuellement, si $tmp_numeric_activity_load != 0, on a des heures sup'
		}
		
		for( $half_day = 0; $half_day < 2; $half_day ++ ) {
			if( date( "N", mktime( 0, 0, 0, intval( $param_month ), intval( $day ), intval( $param_year ) ) ) != "6" && date( "N", mktime( 0, 0, 0, intval( $param_month ), intval( $day ), intval( $param_year ) ) ) != "7") {
				// si les cras ne sont pas complets, on prédit les cras manquants.
				$tmp_total_h[$half_day] = 0;
				// on cherche combien on a d'heures pour cette période pour cet utilisateur en mémoire
				foreach( $reporting_data[$tmp_profile_id][$half_day][$day]["cras"] as $cra ) {
					$tmp_total_h[$half_day] += $cra["nb_h"];
				}
				if( $tmp_total_h[$half_day] < 4 ) {
					$predicted_project_id = -1; // TODO
					
					$geny_assignements = $geny_assignement->getActiveAssignementsListByProfileId( $tmp_profile_id );
					// cas simple : l'utilisateur n'est rattaché qu'à un seul projet => on prend celui-là
					if( sizeof( $geny_assignements ) == 1 ) {
						$predicted_project_id = $geny_assignements[0]->project_id;
					}
					// cas plus tordu : on détermine le projet à considérer en fonction des prédictions précédentes
					else if( sizeof( $geny_assignements ) >= 2 ) {
						// si il n'y a pas de précédentes prédiction, on prend le premier projet de l'utilisateur
						if( $last_prediction[$tmp_profile_id] == -1 ) {
							$predicted_project_id = $geny_assignements[0]->project_id;
							$last_prediction[$tmp_profile_id] = $geny_assignements[0]->project_id;
						}
						// sinon, on cherche le projet qui suit la précédente prédiction
						else {
							// on cherche la prédiction précédente
							for( $tmp_cpt = 0; $tmp_cpt <= sizeof( $geny_assignements ); $tmp_cpt ++ ) {
								if( $last_prediction[$tmp_profile_id] == $geny_assignements[$tmp_cpt]->project_id ) {
									break;
								}
							}
							// on prend le projet suivant
							$tmp_cpt++;
							// si on a atteint la fin de la liste de projet, on prend le premier projet
							if( $tmp_cpt == sizeof( $geny_assignements ) ) {
								$tmp_cpt = 0;
							}
							$predicted_project_id = $geny_assignements[$tmp_cpt]->project_id;
							$last_prediction[$tmp_profile_id] = $geny_assignements[$tmp_cpt]->project_id;
						}
					}
					// si il n'y a pas de projets associés, l'id est négative
					else {
						$predicted_project_id = -1 ;
					}
					
					// ajout du cra "prédit"
					$reporting_data[$tmp_profile_id][$half_day][$day]["cras"][] = array( "project_id" => $predicted_project_id ,
														"nb_h" => 4 - $tmp_total_h[$half_day] ,
														"predicted" => true ) ;
				}
			}
			
			// initialisation du projet majoritaire en nb d'heures
			$majority_cra = array( "project_id" => -1,
				"nb_h" => 0,
				"predicted" => false );
			
			// on trouve le projet majoritaire en nb d'heures
			foreach( $reporting_data[$tmp_profile_id][$half_day][$day]["cras"] as $cra ) {
				if( $cra["nb_h"] > $majority_cra["nb_h"] ) {
					$majority_cra = $cra;
				}
			}
			
			// mise à jour de l'id de projet majoritaire en nb d'heures
			$reporting_data[$tmp_profile_id][$half_day][$day]["majority_project_id"] = $majority_cra["project_id"] ;
			
			// si le projet majoritaire a été prédit, on le précise lors de l'affichage
			if( $majority_cra["predicted"] == true ) {
				$reporting_data[$tmp_profile_id][$half_day][$day]["total_prediction"] = true;
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
		Reporting d'utilisation des ressources entre le <strong><?php echo date( "Y-m-d", mktime( 0, 0, 0, $month, 1, $year ) ); ?></strong> et le <strong><?php echo date( "Y-m-d", mktime( 0, 0, 0, $month, $nb_day_in_month, $year ) ); ?></strong>.<br/>
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
						if( intval( $tmp_month_id ) == intval( $month ) ) {
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
							
				// on parcourt les données par profil
				foreach( $reporting_data as $tmp_profile_id => $tmp_period_data ) {
					
					// chargement du profil
					$geny_profile->loadProfileById( $tmp_profile_id );
					
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
						foreach( $tmp_days_data as $tmp_day => $tmp_data ) {
							
							$geny_project->loadProjectById( $tmp_data["majority_project_id"] );
								
							// on affiche une case colorée en fonction du type de projet si l'id > 0
							if( $tmp_data["majority_project_id"] > 0 ) {
							
								// on récupère la couleur associée au type de projet
								$project_type_background_color = $geny_project_type->getProjectTypeColor( $geny_project->type_id );
								
								$class_of_td = $geny_project->id;
								if( $tmp_data["total_prediction"] ) {
									$class_of_td = "predicted_td";
								}
								
								// on affiche la case
								echo '<td style="background-color:' . $project_type_background_color . '" class="' . $class_of_td . '">';
								echo '<a href="#" class="bulle"><div id="case">' . $tmp_data["majority_project_id"] . '</div><span>';
								
								// si la prédiction a détérminé la couleur de la case, on affiche qu'il s'agit d'une prédiction
								if( $tmp_data["total_prediction"] ) {
									echo '<div id="predicted-span-info">Prédiction</div>';
								}
								
								// quoiqu'il arrive, on affiche les projets des cra déclarés par l'utilisateur
								foreach( $tmp_data["cras"] as $cra ) {
									// chargement des données
									$geny_project->loadProjectById( $cra["project_id"] );
									$geny_client->loadClientById( $geny_project->client_id );
									
									// récupération de la couleur du span en fonction de type de projet
									$project_type_background_color = $geny_project_type->getProjectTypeColor( $geny_project->type_id );
									
									// affichage du div
									echo '<div id="span-info" style="background-color:' . $project_type_background_color . '">' . $geny_client->name . " - " . $geny_project->name . " : " . $cra["nb_h"] . "h</div>";
								}
								
								echo '</span></a></td>';
							}
							//sinon si l'id < 0, on affiche une case grise
							else {
								echo '<td class="empty"><div id="case"></div></td>';
							}
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
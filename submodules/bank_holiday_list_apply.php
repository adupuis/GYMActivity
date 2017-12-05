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


date_default_timezone_set('Europe/Paris');
$gritter_notifications = array();

$data_array = array();
$data_array_filters = array( 0 => array(), 1 => array(), 2 => array(), 3 => array() );

$bank_holiday = new GenyBankHoliday();
$country = new GenyCountry();
$project = new GenyProject();
$task = new GenyTask();
$bank_holidays = $bank_holiday->getAllBankHolidays();
$activity_report_addition_status = array();

if( GenyTools::getParam("bank_holiday_apply_list","") == "true" ){
	$profile = new GenyProfile();
	$geny_assignement = new GenyAssignement();
	$geny_activity = new GenyActivity();
	$geny_ar = new GenyActivityReport();
	$pmd = new GenyProfileManagementData();
	$geny_project = new GenyProject();
	foreach($profile->getProfileByActivation(true) as $p){
		$pmd->loadProfileManagementDataByProfileId($p->id);
		$profile_name = $p->login;
		if( $p->firstname != '' && $p->lastname  != '') {
			$profile_name = $p->firstname." ".$p->lastname;
		}
		if( !in_array($profile_name, $data_array_filters[0]) )
			$data_array_filters[0][] = $profile_name;
		foreach ( $bank_holidays as $bh ){
			GenyTools::debug("\n\nPROCESSING: '$bh->name' to profile $p->login as he is from country $pmd->country_id from $bh->start_date to $bh->stop_date\n\n");
			if( !in_array($bh->name, $data_array_filters[1]) )
				$data_array_filters[1][] = $bh->name;
			if($bh->country_id == $pmd->country_id){
				GenyTools::debug("Adding banking holiday '$bh->name' to profile $p->login as he is from country $pmd->country_id from $bh->start_date to $bh->stop_date");
				$bh_days_list = GenyTools::getWorkedDaysList(strtotime($bh->start_date), strtotime($bh->stop_date) );
				foreach ($bh_days_list as $d){
					if($bh->project_id != $geny_project->id)
						$geny_project->loadProjectById($bh->project_id);
					if( $d < $geny_project->start_date || $d > $geny_project->end_date ){
						$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout du jour férié $bh->name pour le $d : la date du jour férié est en dehors des bornes temporelle du projet $geny_project->name (du $geny_project->start_date au $geny_project->end_date).");
						break;
					}
					GenyTools::debug("Adding an ActivityReport for date $d");
					$geny_country = new GenyCountry($pmd->country_id);
					if( !in_array($geny_country->name, $data_array_filters[2]) )
						$data_array_filters[2][] = $geny_country->name;
					// On récupère la charge pour le jour et on ajoute les 8h (1j) du jour de congés que l'on va rajouter.
					$day_load = $geny_ar->getDayLoad($p->id,$d)+8;
					if($day_load <= 8){
						$assignements = $geny_assignement->getAssignementsListByProjectIdAndProfileId($bh->project_id,$p->id);
						if(count($assignements) == 1 && $assignements[0]->id > -1){
							$geny_activity_id = $geny_activity->insertNewActivity('NULL',$d,8,date('Y-m-j'),$assignements[0]->id,$bh->task_id);
							if( $geny_activity_id > -1 ){
								$geny_ars = new GenyActivityReportStatus();
								$geny_ars->loadActivityReportStatusByShortName('APPROVED');
								$geny_ar_id = $geny_ar->insertNewActivityReport('NULL',-1,$geny_activity_id,$p->id,$geny_ars->id );
								if( $geny_ar_id > -1 ){
									GenyTools::debug("Adding banking holiday '$bh->name' to profile $p->login as he is from country $pmd->country_id => OK");
									$activity_report_addition_status[] = array( 'profile_name' => $profile_name, 'bank_holiday'=>$bh->name, 'country' => $geny_country->name,'status' => 'Ok' );
									if( !in_array('Ok', $data_array_filters[3]) )
										$data_array_filters[3][] = 'Ok';
								}
								else{
									$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout du jour férié $bh->name pour le $d pour $profile_name (Erreur d'insertion dans la base de données).");
									$activity_report_addition_status[] = array( 'profile_name' => $profile_name, 'bank_holiday'=>$bh->name, 'country' => $geny_country->name,'status' => 'KO (Erreur d\'insertion dans la base de données).' );
									if( !in_array('KO (Erreur d\'insertion dans la base de données).', $data_array_filters[3]) )
										$data_array_filters[3][] = 'KO (Erreur d\'insertion dans la base de données).';
								}
							}
							else {
								$geny_activity->deleteActivity($geny_activity_id);
								$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout du jour férié $bh->name pour le $d pour $profile_name (Erreur d'insertion dans la base de données).");
								$activity_report_addition_status[] = array( 'profile_name' => $profile_name, 'bank_holiday'=>$bh->name, 'country' => $geny_country->name,'status' => 'KO (Erreur d\'insertion dans la base de données).' );
								if( !in_array('KO (Erreur d\'insertion dans la base de données).', $data_array_filters[3]) )
									$data_array_filters[3][] = 'KO (Erreur d\'insertion dans la base de données).';
							}
						}
						else{
							$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout du jour férié $bh->name pour le $d pour $profile_name (profile non assigné au projet $geny_project->name).");
							GenyTools::debug("Adding banking holiday '$bh->name' to profile $p->login as he is from country $pmd->country_id => KO (NO ASSIGNEMENT)");
							$activity_report_addition_status[] = array( 'profile_name' => $profile_name, 'bank_holiday'=>$bh->name, 'country' => $geny_country->name, 'status' => "KO (profile non assigné au projet $geny_project->name)" );
							if( !in_array('KO (more than 8h entered for this day)', $data_array_filters[3]) )
								$data_array_filters[3][] = "KO (profile non assigné au projet $geny_project->name)";
						}
					}
					else{
						GenyTools::debug("Adding banking holiday '$bh->name' to profile $p->login as he is from country $pmd->country_id => KO (TOO MANY HOURS)");
						$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout du jour férié $bh->name pour le $d pour $profile_name.");
						$activity_report_addition_status[] = array( 'profile_name' => $profile_name, 'bank_holiday'=>$bh->name, 'country' => $geny_country->name, 'status' => 'KO (more than 8h entered for this day)' );
						if( !in_array('KO (more than 8h entered for this day)', $data_array_filters[3]) )
							$data_array_filters[3][] = 'KO (more than 8h entered for this day)';

					}
				}
			}
		}
	}
	
}

?>
<div id="mainarea">
	<p class="mainarea_title">
	<img src="images/<?php echo $web_config->theme; ?>/holiday_summary_generic.png"></img>
		<span class="bank_holiday_list_apply">
			Liste des jours fériés
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des jours fériés. <br/><strong style="color:red">Attention: si le jour férié tombe un weekend il n'est pas affiché dans cette liste.</strong>
		</p>
		<script>
			var indexData = new Array();
			<?php
				if(array_key_exists("GYMActivity_bank_holiday_list_apply_php", $_COOKIE)) {
					$cookie = json_decode($_COOKIE["GYMActivity_bank_holiday_list_apply_php"]);
				}
				
				$data_array_filters_html = array();
				foreach( $data_array_filters as $idx => $data ) {
					$data_array_filters_html[$idx] = '<select><option value=""></option>';
					foreach( $data as $d ) {
						if( isset( $cookie ) && htmlspecialchars_decode( urldecode( $cookie->aaSearchCols[$idx][0]), ENT_QUOTES ) == htmlspecialchars_decode( $d, ENT_QUOTES ) ) {
							$data_array_filters_html[$idx] .= '<option selected="selected" value="'.htmlentities( $d, ENT_QUOTES, 'UTF-8' ).'">'.htmlentities( $d, ENT_QUOTES, 'UTF-8' ).'</option>';
						}
						else {
							$data_array_filters_html[$idx] .= '<option value="'.htmlentities( $d, ENT_QUOTES, 'UTF-8' ).'">'.htmlentities( $d, ENT_QUOTES, 'UTF-8' ).'</option>';
						}
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
				
				var oTable = $('#bank_holiday_list_apply_table').dataTable( {
					"bDeferRender": true,
					"bJQueryUI": true,
					"bStateSave": true,
					"bAutoWidth": false,
					"sCookiePrefix": "GYMActivity_",
					"sPaginationType": "full_numbers",
					"oLanguage": {
						"sSearch": "Recherche :",
						"sLengthMenu": "Jours fériés _MENU_",
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
					},
// 					"aaSorting": [[ 5, "desc" ]]
				} );
				/* Add a select menu for each TH element in the table footer */
				/* i+1 is to avoid the first row wich contains a <input> tag without any informations */
				$("tfoot th").each( function ( i ) {
					if( i == 0 || i == 1 || i == 2 || i == 3 ) {
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
		<form id="formID" action="loader.php?module=bank_holiday_list_apply" method="post" class="table_container">
			<style>
				@import 'styles/<?php echo $web_config->theme ?>/bank_holiday_list_apply.css';
			</style>
			<input type="hidden" name="bank_holiday_apply_list" value="true" />
			<p>
				<table id="bank_holiday_list_apply_table" style="color: black; width: 100%;">
					<thead>
						<th>Profile</th>
						<th>Jour férié</th>
						<th>Pays</th>
						<th>Status</th>
					</thead>
					<tbody>
					<?php
						foreach( $activity_report_addition_status as $aras ){
							GenyTools::debug("Displaying: ".$aras['profile_name']." - ".$aras['bank_holiday']." - ".$aras['country']." - ".$aras['status']);
							echo "<tr> <td> <center>".$aras['profile_name']."</center> </td> <td> <center>".$aras['bank_holiday']."</center> </td> <td> <center>".$aras['country']."</center> </td> <td> <center>".$aras['status']."</center> </td> </tr>";
						}
					?>
					</tbody>
					<tfoot>
						<th class="filtered">Profile</th>
						<th class="filtered">Jour férié</th>
						<th class="filtered">Pays</th>
						<th class="filtered">Status</th>
					</tfoot>
				</table>
			</p>
		</form>
	</p>
</div>

<?php
	$bottomdock_items = array('backend/widgets/bank_holiday_add.dock.widget.php','backend/widgets/bank_holiday_list.dock.widget.php');
?>
